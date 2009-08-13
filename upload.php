<?php
/*
+------------------------------------------------
|   TBDev.net BitTorrent Tracker PHP
|   =============================================
|   by CoLdFuSiOn
|   (c) 2003 - 2009 TBDev.Net
|   http://www.tbdev.net
|   =============================================
|   svn: http://sourceforge.net/projects/tbdevnet/
|   Licence Info: GPL
+------------------------------------------------
|   $Date$
|   $Revision$
|   $Author$
|   $URL$
+------------------------------------------------
*/
require_once "include/bittorrent.php";
require_once "include/user_functions.php";
require_once "include/html_functions.php";

dbconn(false);

loggedinorreturn();

stdhead("Upload");

if ($CURUSER['class'] < UC_UPLOADER)
{
  stdmsg("Sorry...", "You are not authorized to upload torrents.  (See <a href=\"faq.php#up\">Uploading</a> in the FAQ.)");
  stdfoot();
  exit;
}

?>
<div align='center'>
<form enctype="multipart/form-data" action="takeupload.php" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_torrent_size?>" />
<p>The tracker's announce url is <b><?php echo  $announce_urls[0] ?></b></p>
<table border="1" cellspacing="0" cellpadding="10">
<?php

tr("Torrent file", "<input type='file' name='file' size='80' />\n", 1);
tr("Torrent name", "<input type=\"text\" name=\"name\" size=\"80\" /><br />(Taken from filename if not specified. <b>Please use descriptive names.</b>)\n", 1);
tr("NFO file", "<input type='file' name='nfo' size='80' /><br />(<b>Optional.</b> Can only be viewed by power users.)\n", 1);
tr("Description", "<textarea name=\"descr\" rows=\"10\" cols=\"80\"></textarea>" .
  "<br />(HTML/BB code is <b>not</b> allowed.)", 1);

$s = "<select name=\"type\">\n<option value=\"0\">(choose one)</option>\n";

$cats = genrelist();
foreach ($cats as $row)
	$s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";

$s .= "</select>\n";
tr("Type", $s, 1);

?>
<tr><td align="center" colspan="2"><input type="submit" class='btn' value="Do it!" /></td></tr>
</table>
</form>
</div>
<?php

stdfoot();

?>