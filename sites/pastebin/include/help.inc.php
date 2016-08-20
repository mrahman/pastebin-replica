<?php
/**
 * $Project: homework $
 * $Id: help.inc.php,v 0.0.1 2016/08/20 $
 * 
 * Pastebin alike Collaboration Tool
 *
 * This file copyright (C) 2016 Mizanur Rahman (mrahmanjewel@gmail.com)
 */

h1('What is pastebin?');
p('pastebin is here to help you collaborate on debugging code snippets. '.
	'If you\'re not familiar with the idea, most people use it like this:');

echo '<ul>';

li('<a href="' . $CONF['this_script'] . '">submit</a> a code (text) fragment to pastebin, getting a url like http://localhost:8080/pastebin/1234');
li('paste the url into an chatwork, hipchat, skype conversation');
li('someone responds by reading');

echo '</ul>';

h1('How can I delete a post?');
p('If you clicked the "remember me" checkbox when posting, you will be able to delete '.
'post from the same computer you posted from - simply view the post and click the "delete post" link.');
p('In other cases, contact us and we will delete it for you');

h1('And this is all free?');
p('It is free.');

h1('Acceptable Use Policy');
p('In particular, please do not post email lists, password lists or personal information.'); 
