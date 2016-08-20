<?php
/**
 * $Project: homework $
 * $Id: util.inc.php,v 0.0.1 2016/08/20 $
 * 
 * Pastebin alike Collaboration Tool
 *
 * This file copyright (C) 2016 Mizanur Rahman (mrahmanjewel@gmail.com)
 * 
 */

function t($str)
{
    // By design for localization
    return $str;
}

//html helpers using above localization system

function h1($str)
{
    echo '<h1>'.t($str).'</h1>';
}
function p($str)
{
    echo '<p>'.t($str).'</p>';
}
function li($str)
{
    echo '<li>'.t($str).'</li>';
}
