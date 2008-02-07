<?php

require_once "include/bittorrent.php";
dbconn();

loggedinorreturn();

$nick = ($CURUSER ? $CURUSER["username"] : ("Guest" . rand(1000, 9999)));
$irc_url = 'apollo.wyldryde.org';
$irc_channel = '#TBDEVNET';


stdhead();


?>
<p>The official IRC channel is <a <?php echo $irc_url; ?>><?php echo $irc_channel; ?></a> on the <a href=http://www.gigadactyl.com>gigadactyl</a> network.</p>
<div class="borderwrap" width="1%" align='center'>
	<div class="maintitle"><?=$SITENAME?></div>
	<div class="row1" align='center'><applet code=IRCApplet.class codebase="./javairc/" archive="irc.jar,pixx.jar" width=640 height=400>
<param name="CABINETS" value="irc.cab,securedirc.cab,pixx.cab">
<param name="nick" value="<?=$nick?>">
<param name="alternatenick" value="<?=$nick?>???">
<param name="fullname" value="Java User">
<param name="host" value="<?=$irc_url?>">
<param name="gui" value="pixx">
<param name="quitmessage" value="<?=$SITENAME?> forever!">
<param name="asl" value="true">
<param name="command1" value="/join <?=$irc_channel?>">
<param name="style:bitmapsmileys" value="true">
<param name="style:floatingasl" value="true">
<param name="pixx:highlight" value="true">
<param name="pixx:highlightnick" value="true">
<param name="pixx:nickfield" value="true">
<param name="style:smiley1" value="~:) img/sleep.gif">
</applet></div>
</div>

<?

stdfoot();

?>