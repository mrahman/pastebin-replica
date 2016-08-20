<?php
/**
 * $Project: homework $
 * $Id: default.conf.php,v 0.0.1 2016/08/20 $
 * 
 * Pastebin Alike Collaboration Tool
 *
 * This file copyright (C) 2016 Mizanur Rahman (mrahmanjewel@gmail.com)
 * 
 */
 
/**
* This is the main configuration file containing typical defaults. 
* 
* For ease of upgrading, DO NOT MODIFY THIS FILE!
* 
* Create an override file with a name matching your domain or element of
* of it. For example for the domain 'override.yourdomain.com', the code will
* attempt to include these config files in order
* 
* default.conf.php
* overrider.yourdomain.com.conf.php 
*
* The purpose of this to allow you to specific global options lower down,
* say in com.conf.php, but domain-specific overrides in higher up files like
* overrider.yourdomain.com.conf.php
*/

/**
* Site title
*/
$CONF['title'] = 'pastebin alike - sharing tool';

/**
* Email address feedback should be sent to
*/
$CONF['feedback_to'] = 'mrahmanjewel@gmail.com';

/**
* Apparent sender address for feedback email
*/
$CONF['feedback_sender'] = 'mrahman <mrahmanjewel@gmail.com>';

/**
* database type - can be file or mysql
*/
$CONF['dbsystem'] = 'mysql';

/**
* db credentials
*/
$CONF['dbhost'] = 'localhost';
$CONF['dbname'] = 'pastebin';
$CONF['dbuser'] = 'pasteuser';
$CONF['dbpass'] = 'pastepass';

/**
 * format of urls to pastebin entries - %d is the placeholder for
 * the entry id. 
 */
$CONF['url_format'] = '/pastebin/pastebin.php?show=%s';

/**
* default expiry time d (day) m (month) or f (forever)
*/
$CONF['default_expiry'] = 'm';

/**
* this is the path to the script 
*/
$CONF['this_script'] = './pastebin.php';

/**
* default syntax highlighter
*/
$CONF['default_highlighter'] = 'text';

/**
* available formats
*/
$CONF['all_syntax'] =array(
	'text'=>'None',
	'bash'=>'Bash',
	'c'=>'C',
	'cpp'=>'C++',
	'csharp'=>'C#',
	'css'=>'CSS',
	'java'=>'Java',
	'javascript'=>'Javascript',
	'mysql'=>'MySQL',
	'pascal'=>'Pascal',
	'perl'=>'Perl',
	'php'=>'PHP',
	'python'=>'Python',
	'ruby'=>'Ruby',
);

/**
* popular formats, listed first
*/
$CONF['popular_syntax'] = array(
	'text','bash', 'c', 'cpp',
	'java','javascript','php',
	'perl', 'python', 'ruby');
