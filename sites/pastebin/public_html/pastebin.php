<?php
/**
 * $Project: homework $
 * $Id: pastebin.php,v 0.0.1 2016/08/20 $
 * 
 * Pastebin alike Collaboration Tool
 *
 * This file copyright (C) 2016 Mizanur Rahman (mrahmanjewel@gmail.com)
 * 
 */
 
// include bootstrap
//
require_once('pastebin/bootstrap.inc.php');

// create our pastebin object
//
$pastebin = new Pastebin($CONF);

// process new posting 
//
if (isset($_POST['paste']))
{
    //process posting and redirect
    $id = $pastebin->doPost($_POST);
    if ($id)
    {
        $pastebin->redirectToPost($id);
        exit;
    }
}

// process download
//
if (isset($_GET['dl'])) 
{
    $pid = $pastebin->validatePostId($_GET['dl']);
    if (empty($pid) || (count($_GET)>1))
    {
        header('HTTP/1.0 400 Bad Request');
        echo 'Bad request';
        exit;
    }
    elseif (!$pastebin->doDownload($pid))
    {
        //not found
        echo "Pastebin entry $pid is not available";
    }
    exit;
}

$page = array();

//figure out some nice defaults
$page['current_format'] = $CONF['default_highlighter'];
$page['expiry'] = $CONF['default_expiry'];
$page['remember'] = '';    

//are we remembering the user?
$cookie= $pastebin->extractCookie();
if ($cookie)
{
    //initialise bits of page with cookie data
    $page['remember'] = 'checked="checked"';
    $page['current_format'] = $cookie['last_format'];
    $page['poster'] = $cookie['poster'];
    $page['expiry'] = $cookie['last_expiry'];
}

// erase a post
//
if (isset($_REQUEST['erase']))
{
    $pid = $pastebin->validatePostId($_REQUEST['erase']);
    $post = $pastebin->getPost($pid);
    $can_erase = (!empty($post['poster']) && !empty($cookie['poster']) && $post['poster'] == $cookie['poster']);
        
    if ($can_erase)
    {
        $pastebin->deletePost($pid);
        $page['delete_message'] = t('Your post has been deleted');
    }
    else
    {
        $page['delete_message'] = t('You cannot delete this post - contact us if you need further assistance');
        $_REQUEST['show'] = $pid;
    }
}

//add list of recent posts
$list = isset($_REQUEST['list']) ? intval($_REQUEST['list']) : 10;
$page['recent'] = $pastebin->getRecentPosts($list);

// show a post
//
if (isset($_REQUEST['show']))
{
    $pid= $pastebin->validatePostId($_REQUEST['show']);
    
    //get the post
    $page['post'] = $pastebin->getPost($pid);
    
    if (!isset($page['post']['pid']))
    {
        //post could not be loaded - return a 410 code
        header('HTTP/1.0 410 Gone');

        //bluffing non human access (example)
        $is_bot = preg_match('/bot|slurp/i',$_SERVER['HTTP_USER_AGENT']);
        if ($is_bot)
        {
            echo 'Pastebin post expired or deleted - <a href="' . $CONF['this_script'] . '">click here to make a new post<a/>';
            exit;
        }
    }

    $pastebin->outputExpiryHeaders($page['post']);

    //see if we can be quick about this...
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && isset($page['post']['modified']))
    {
        $since=strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
        if ($page['post']['modified'] <= $since)
        {
            header('HTTP/1.1 304 Not Modified');
            exit;
        }
    }
    
    //can we erase?
    $page['can_erase'] = (isset($page['post']['poster']) && isset($page['poster']) && ($page['poster'] == $page['post']['poster']));
    
    //ensure corrent format is selected
    $page['current_format'] = isset($page['post']['format'])? $page['post']['format'] : '';
}
else
{
     $page['posttitle'] = 'New Posting';
}

if (($page['current_format'] != 'text') && isset($CONF['all_syntax'][$page['current_format']]))
{
    //give the page a title which features the syntax used..
    $page['title'] = $CONF['all_syntax'][$page['current_format']] . ' ' .$page['title'];
} else {
    //use configured title
    $page['title'] = $CONF['title'];
}

header('Content-Type: text/html; charset=' . $CONF['http_charset']);

// HTML page output
//
include('../include/layout.inc.php');

// clean up older posts 
$pastebin->doGarbageCollection();
