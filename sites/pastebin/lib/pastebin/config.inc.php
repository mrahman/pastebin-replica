<?php
/**
 * $Project: homework $
 * $Id: config.inc.php,v 0.0.1 2016/08/20 $
 * 
 * Configuration file, override with domain specific configs
 *
 * Copyright (C) 2016 Mizanur Rahman (mrahmanjewel@gmail.com)
 */
 
$CONF = array();

//include a default
require_once('config/default.conf.php');

//get domain parts 
$domain_parts = explode('.', preg_replace('/[^A-Za-z0-9-\.]/', '', $_SERVER['HTTP_HOST']));
foreach($domain_parts as $idx => $domain_part)
{
    if (strlen($domain_part) == 0)
    {
        $domain_part = 'bad';
    }
    $domain[$idx] = $domain_part;
}

//now pull in overides for each level of domain
$config = '';
$sep = '';
for ($i = count($domain_parts) - 1; $i >= 0; $i--)
{
    $config = $domain_parts[$i] . $sep . $config;
    $sep='.';
    
    @include_once("config/$config.conf.php");
}

//pull in required database class
require_once('pastebin/db.'.$CONF['dbsystem'].'.class.php');
