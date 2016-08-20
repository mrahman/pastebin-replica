<?php
/**
 * $Project: homework $
 * $Id: bootstrap.inc.php,v 0.0.1 2016/08/20 $
 * 
 * Pastebin alike Collaboration Tool
 *
 * This file copyright (C) 2016 Mizanur Rahman (mrahmanjewel@gmail.com)
 * 
 */

// includes
//
require_once('pastebin/config.inc.php');
require_once('pastebin/pastebin.class.php');
require_once('pastebin/util.inc.php');

/**
* Characters code for http and htmlentities
*/
$charset_code = array(
    'unicode' => array('http'=>'UTF-8', 'htmlentities'=>'UTF-8'),
);
/**
* Which character set to use?
*/
$charset= 'unicode';

/**
* configure character set
*/
$CONF['htmlentity_encoding'] = $charset_code[$charset]['htmlentities'];
$CONF['http_charset'] = $charset_code[$charset]['http'];

set_time_limit(180);

// handle magic quotes 
//
if (get_magic_quotes_gpc())
{
    function callback_stripslashes(&$val, $name)
    {
        if (get_magic_quotes_gpc())
            $val=stripslashes($val);
    }

    if (count($_GET))
        array_walk ($_GET, 'callback_stripslashes');
    if (count($_POST))
        array_walk ($_POST, 'callback_stripslashes');
    if (count($_COOKIE))
        array_walk ($_COOKIE, 'callback_stripslashes');
}
