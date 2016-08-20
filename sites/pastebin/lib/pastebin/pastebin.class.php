<?php
/**
 * $Project: homework $
 * $Id: pastebin.class.php,v 1.2 2016/08/20 $
 * 
 * Pastebin alike Collaboration Tool
 *
 * This file copyright (C) 2016 Mizanur Rahman (mrahmanjewel@gmail.com)
 */

/**
* Pastebin main class
*/
class Pastebin
{
    var $conf = null;
    var $db = null;
    
    /**
    * Constructor expects a configuration array which should contain
    * the elements documented in config/default.conf.php
    */
    function Pastebin(&$conf)
    {
        $this->conf = &$conf;
        $this->db = new DB();    
    }
    
    /**
    * Has a 5% probability of cleaning old posts from the database
    */
    function doGarbageCollection()
    {
        if(rand()%100 < 1)
        {
            $this->db->gc();
        }
    }
    
    /**
    * Function for validating a user-submitted username
    */
    function _validateUsername($name)
    {
        return trim(substr(preg_replace('/[^A-Za-z0-9_ \-]/', '',$name),0,24));    
    }
    
    /**
    * Function for validating a user-submitted format code
    */
    function _validateFormat($format)
    {
        if (!array_key_exists($format, $this->conf['all_syntax']))
            $format = 'text';
            
        return $format;    
    }
    
    /**
    * Function for validating a user-submitted expiry code
    */
    function _validateExpiry($expiry)
    {
        if (!preg_match('/^[dmf]$/', $expiry))
            $expiry = 'd';
            
        return $expiry;
    }
    
    /**
    * returns array of cookie info if present, false otherwise
    */
    function extractCookie()
    {
        $data = false;
        if (isset($_COOKIE['persistName']))
        {
            $data = array();
            
            //blow apart the cookie
            list($poster, $last_format, $last_expiry) = explode('#', $_COOKIE['persistName']);
            
            //clean and validate the cookie inputs
            $data['poster'] = $this->_validateUsername($poster);
            $data['last_format'] = $this->_validateFormat($last_format);
            $data['last_expiry'] = $this->_validateExpiry($last_expiry);
        }
        
        return $data;
    }
    
    /**
     * Post registration
     */
    function doPost(&$post)
    {
        $id=0;
        
        $this->errors=array();
        
        //validate some inputs
        $post['poster'] = $this->_validateUsername($post['poster']);
        $post['format'] = $this->_validateFormat($post['format']);
        $post['expiry'] = $this->_validateExpiry($post['expiry']);
        
        //set/clear the persistName cookie
        if (isset($post['remember']))
        {
            $value = $post['poster'] . '#' . $post['format'] . '#' . $post['expiry'];
            
            //set cookie if not set
            if (!isset($_COOKIE['persistName']) || 
                ($value!= $_COOKIE['persistName']))
                setcookie ('persistName', $value, time()+3600*24*365);  
        }
        else
        {
            //clear cookie if set
            if (isset($_COOKIE['persistName']))
                setcookie ('persistName', '', 0);
        }
        
        if (strlen($post['code2']))
        {
            if (strlen($post['poster'])==0)
                $post['poster'] = 'Anonymous';
            
            $format = $post['format'];
            if (!array_key_exists($format, $this->conf['all_syntax']))
                $format= '';
            
            $code = $post['code2'];
            
            //is it spam?
            require_once('pastebin/spamfilter.class.php');
            $filter = new SpamFilter();
            
            if ($filter->canPost($post))
            {
                //prepare for insert..
		// Parent pid is added as by-design for future new feature (reply) add purpose
                $parent_pid = 0;

                $id = $this->db->addPost($post['poster'], $format, $code,
                    $parent_pid, $post['expiry']);
            }
            else
            {
                $this->errors[] = 'Sorry, your post tripped our spam/abuse filter - let us know if you think this could be improved';
            }
            
        }
        else
        {
            $this->errors[] = 'No code specified';
        }
        
        return $id;
    }    
    
    function validatePostId($raw)
    {
        return $this->db->validatePostId($raw);    
    }
    
    function getPostURL($id)
    {
        global $CONF;
        return sprintf("http://{$_SERVER['HTTP_HOST']}".$this->conf['url_format'], $id);
    }

    function redirectToPost($id)
    {
        header('Location:' . $this->getPostURL($id));    
    }
    
    function doDownload($pid)
    {
        $ok=false;
        $post= $this->db->getPost($pid);
        if ($post)
        {
            //figure out extension
            $ext= "txt";
            switch($post['format'])
            {
                case 'bash':
                    $ext= 'sh';
                    break;
                case 'actionscript':
                    $ext= 'html';
                    break;
                case 'html4strict':
                    $ext= 'html';
                    break;
                case 'javascript':
                    $ext= 'js';
                    break;
                case 'perl':
                    $ext= 'pl';
                    break;
                case 'php':
                case 'c':
                case 'cpp':
                case 'css':
                case 'xml':
                    $ext= $post['format'];
                    break;
            }
            
            // dl code
            header('Content-type: text/plain');
            header('Content-Disposition: attachment; filename= "'. $pid . '.' . $ext . '"');
            echo $post['code'];
            $ok=true;
        }
        else
        {
            //not found
            header('HTTP/1.0 404 Not Found');
        }
    
        return $ok;
    }

    function outputExpiryHeaders($post)
    {
        if (!isset($post['expires'])) {
            //most probably a non-existent post
            return;
        }

        $expires= $post['expires'];
        $updated=isset($post['modified'])?$post['modified']:$post['posted'];
                     

        $last_modified = gmdate('D, d M Y H:i:s', $updated) . ' GMT';
        header("Last-Modified: $last_modified");
        
        if (!$expires)
        {
            #cache it for a year
            $expires=time()+86400*365;
        }

        if ($expires)
        {
            $date = gmdate('D, d M Y H:i:s', $expires) . ' GMT';
            header("Expires: $date"); 

            $maxage= $expires-time();
            header("Cache-Control: max-age= $maxage, must-revalidate");

        }
    }
    
    /**
    * returns list of recently post summaries
    *
    * parameter is a count or 0 for all
    */
    function getRecentPosts($list=10)
    {
        //get raw db info
        $posts = $this->db->getRecentPostSummary($list);
        
        //augment with some formatting
        foreach($posts as $idx=>$post)
        {
            $age= $post['age'];
            $days=floor($age/(3600*24));
            $hours=floor($age/3600);
            $minutes=floor($age/60);
            $seconds= $age;
            
            if ($days>1)
                $age=sprintf(t('%d days ago'), $days);
            elseif ($hours>0)
                $age=($hours>1)? sprintf(t('%d hours ago'), $hours) : t('1 hour ago');
            elseif ($minutes>0)
                $age=($minutes>1)? sprintf(t('%d mins ago'), $minutes) : t('1 min ago');
            else
                $age=($seconds>1)? sprintf(t('%d secs ago'), $seconds) : t('1 sec ago');
            
            $url= $this->getPostURL($post['pid']);
            
            $posts[$idx]['agefmt'] = $age;
            $posts[$idx]['url'] = $this->getPostURL($post['pid']);
            
        }
        
        return $posts;        
    }

    function deletePost($pid)
    {
        return $this->db->deletePost($pid);    
    }

    /**
    * Get particular paste data, prepare for displaying on a page
    * Returns an list of useful information
    */
    function getPost($pid)
    {
        $post= $this->db->getPost($pid);
        if ($post)
        {
            //show a quick reference url, poster and posted datetime
            $post['posttitle'] = "Posted by {$post['poster']} on {$post['postdate']}";
            
            $post['downloadurl'] = $this->conf['this_script'] . "?dl=$pid";
            $post['deleteurl'] = $this->conf['this_script'] . "?erase=$pid";
            $post['pid'] = $pid;
        }
        else
        {
            $post['code'] = '<b>Unknown post id, it may have been deleted</b><br />';
            $this->errors[] = 'Unknown post id, it may have expired or been deleted';
        }    
        
        return $post;
    }
    
}
