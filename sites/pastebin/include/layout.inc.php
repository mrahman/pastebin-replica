<?php
/**
 * $Project: homework $
 * $Id: layout.php,v 0.0.1 2016/08/20 $
 * 
 * Pastebin alike Collaboration Tool
 *
 * This file copyright (C) 2016 Mizanur Rahman (mrahmanjewel@gmail.com)
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html data-language="en_US"  lang="en">
<head>
<title><?php echo $page['title'] ?></title>
<meta name="ROBOTS" content="NOARCHIVE"/>
<link rel="stylesheet" type="text/css" media="screen" href="./pastebin.css?ver=6" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.6.0/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.6.0/highlight.min.js"></script>
<script type="text/javascript" src="./pastebin.js?ver=7"></script>
</head>

<body onload="initPastebin()">
<div style="display:none;">
<h1 style="display: none;">pastebin - collaboration tools</h1>
<p style="display: none;">pastebin is a collaboration tool allowing you to share
 code snippets while chatting on chatwork, hipchat, skype or message board.</p>
<p style="display: none;">This site is developed to XHTML and CSS2 W3C standards.  
If you see this paragraph, your browser does not support those standards and you 
need to upgrade.  Visit <a href="http://www.webstandards.org/upgrade/" target="_blank">WaSP</a>
for a variety of options.</p>
</div>

<div id="titlebar"><?php 
	echo $page['title'];
	echo " <a href=\"{$CONF['this_script']}?help=1\">View Help</a>";
?>
</div>

<div id="menu">

<?php
echo '<h1>'.t('Recent Posts').'</h1>';
?>

<ul>
<?php  
foreach($page['recent'] as $idx => $entry)
{
	if ($entry['pid'] == $pid)
		$cls = " class=\"highlight\"";
	else
		$cls = '';
		
	echo "<li{$cls}><a href=\"{$entry['url']}\">";
	echo $entry['poster'];
	echo "</a><br/>{$entry['agefmt']}</li>\n";
}

echo "<li><a rel=\"nofollow\" href=\"{$CONF['this_script']}\">".t('Make new post').'</a></li>';
?>
</ul>

<?php
echo '<h1>'.t('About').'</h1><p>';

echo t('Pastebin is a tool for collaboration code snippets,');
echo " <a href=\"{$CONF['this_script']}?help=1\">".t('See help for details').
		'</a>. ';	

echo '<h1>'.t('Credits').'</h1><p>';
	
echo t('Software developed by <a href="https://github.com/mrahman">Mizanur Rahman</a>');
?>

</div>

<div id="content">

<?php

// show processing errors
//
if (!empty($pastebin->errors))
{
	echo '<h1>' . t('Errors') . '</h1><ul>';
	foreach($pastebin->errors as $err)
	{
		echo "<li>$err</li>";
	}
	echo "</ul>";
	echo "<hr />";
}

if (!empty($page['delete_message']))
{
	echo "<h1>{$page['delete_message']}</h1><br/>";
}

// show a post
//
if (isset($_GET['help']))
	$page['posttitle'] = '';
	
if (!empty($page['post']['posttitle']))
{
	echo "<h1>{$page['post']['posttitle']}";
	echo "<br/>";
	
	if ($page['can_erase'])
	{
		echo "<a href=\"{$page['post']['deleteurl']}\" title=\"".t('delete post')."\">".t('delete post')."</a> | ";
	}
	
	echo "<a href=\"{$page['post']['downloadurl']}\" title=\"".t('download file')."\">".t('download')."</a> | ";
	echo "<a href=\"{$CONF['this_script']}\" title=\"".t('make new post')."\">".t('new post')."</a>";
	echo "</h1>";

}

if (isset($page['post']['pid']))
{
	echo '<pre><code class="' . $page['post']['format'] . '">';
	echo htmlentities($page['post']['code']);
	echo '</code></pre>';
}	

if (isset($_GET['help']))
{
	include('../include/help.inc.php');
}
else if (!isset($page['post']['pid']))
{
	include('../include/new.post.inc.php');
} 
?>

</div>
</body>
<script>hljs.initHighlightingOnLoad();</script>
</html>
