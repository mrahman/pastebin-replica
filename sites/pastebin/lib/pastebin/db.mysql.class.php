<?php
/**
 * $Project: homework $
 * $Id: db.mysql.class.php,v 0.0.1 2016/08/20 $
 * 
 * Pastebin alike Collaboration Tool
 *
 * This file copyright (C) 2016 Mizanur Rahman (mrahmanjewel@gmail.com)
 */
 
/**
* Database handler
* All of the SQL used by the rest of the code is contained in here
*/

require_once('pastebin/mysql.class.php');

class DB extends MySQL
{
    var $dblink=null;
    var $dbresult;
    
    /**
    * Constructor - establishes DB connection
    */
    function DB()
    {
        $this->MySQL();
    }
    
    function gc()
    {
        //delete expired posts
        $this->_deleteExpiredPosts();
    }
    
    /**
    * Delete all expired posts
    */
    function _deleteExpiredPosts()
    {
        $this->query("delete from pastebin where expires is not null and now() > expires");    
    }
    
    /**
    * given user specified post id, return a validated version
    */
    function validatePostId($raw)
    {
        return intval($raw);    
    }
    
    /**
    * erase a post
    */
    function deletePost($pid)
    {
        $this->query('delete from pastebin where pid=?', $pid);    
        return true;
    }
    
    /**
     * Add post and return id
     */
    function addPost($poster, $format, $code, $parent_pid, $expiry_flag)
    {
        //figure out expiry time
        switch ($expiry_flag)
        {
            case 'd';
                $expires="DATE_ADD(NOW(), INTERVAL 1 DAY)";
                break;
            case 'f';
                $expires="NULL";
            default:
            case 'm';
                $expires="DATE_ADD(NOW(), INTERVAL 1 MONTH)";
                break;
        }
        
        $this->query('insert into pastebin (poster, posted, format, code, parent_pid, expires, expiry_flag) '.
                "values (?, now(), ?, ?, ?, $expires, ?)",
                $poster, $format, $code, $parent_pid, $expiry_flag);    
        $id = $this->get_insert_id();    
        
	// Above query intentionally kept outside following transaction
        // The following need to do refactoring
        $this->query('start transaction');
        $this->query('update recent set seq_no=seq_no+1 order by seq_no desc');
        $this->query('insert into recent (seq_no, pid) values (1,?)', $id);
        $this->query('delete from recent where seq_no > 10');
        $this->query('commit');
        
        //flush recent list
        $this->_flushCache('recent');        
        
        return $id;
    }

    /**
     * Return entire pastebin row for given id
     */
    function getPost($id)
    {
        $this->query('select *,date_format(posted, \'%a %D %b %H:%i\') as postdate '.
            'from pastebin where pid=?', $id);
        if ($this->next_record())
            return $this->row;
        else
            return false;
        
    }

    /**
     * Return summaries for $count posts ($count=0 means all)
     */
    function getRecentPostSummary($count)
    {
        $limit = $count? "limit $count" : '';
        $posts=array();
        
        $cacheid = 'recent';
        
        $posts=$this->_cachedquery($cacheid, 'select p.pid,p.poster,unix_timestamp()-unix_timestamp(p.posted) as age, '.
            "date_format(p.posted, '%a %D %b %H:%i') as postdate ".
            'from pastebin as p '.
            'inner join recent as r on p.pid=r.pid '.
            "order by p.posted desc, p.pid desc $limit");
        
        return $posts;
    }

    function _flushCache($cacheid)
    {
	// TODO: Expire cache on upsert
    }
    
    function _cachedquery($cacheid, $sql)
    {
        // TODO: Implement cache read technique
        //cache miss, so proceed to db

        if (is_null($this->dblink))
            $this->_connect();
        
        if (func_num_args() > 2)
        {
            $q = md5(uniqid(rand(), true));
            $sql = str_replace('?', $q, $sql);
            
            $args = func_get_args();
            for ($i=2; $i <= count($args); $i++)
            {
                $sql=preg_replace("/$q/", "'".preg_quote(mysql_real_escape_string($args[$i]))."'", $sql,1);
            }
        
            //we shouldn't have any $q left, but it will help debugging if we change them back!
            $sql=str_replace($q, '?', $sql);
        }    
        
        $result=array();
            
        $this->dbresult=mysql_query($sql, $this->dblink);
        if ($this->dbresult)
        {
            while($row=mysql_fetch_array($this->dbresult,MYSQL_ASSOC))
            {
                $result[]=$row;    
            }
        }
        
        //we have our result
        // TODO: save to cache for future usage

        return $result;
    }
}
