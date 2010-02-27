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
require_once("include/bittorrent.php");

dbconn();
loggedinorreturn();
$lang = array_merge( load_language('global'), load_language('takerate') );
function bark($msg) {
	genbark($msg, "{$lang['rate_fail']}");
}

if (!isset($CURUSER))
	bark("{$lang['rate_login']}");

if (!mkglobal("rating:id"))
	bark("{$lang['rate_miss_form_data']}");

$id = 0 + $id;
if (!$id)
	bark("{$lang['rate_invalid_id']}");

$rating = 0 + $rating;
if ($rating <= 0 || $rating > 5)
	bark("{$lang['rate_invalid']}");

$res = mysql_query("SELECT owner FROM torrents WHERE id = $id");
$row = mysql_fetch_assoc($res);
if (!$row)
	bark("{$lang['rate_torrent_not_found']}");

//if ($row["owner"] == $CURUSER["id"])
//	bark("{$lang['rate_not_vote_own_torrent']}");
$time_now = time();
$res = mysql_query("INSERT INTO ratings (torrent, user, rating, added) VALUES ($id, " . $CURUSER["id"] . ", $rating, $time_now)");
if (!$res) {
	if (mysql_errno() == 1062)
		bark("{$lang['rate_already_voted']}");
	else
		bark(mysql_error());
}

mysql_query("UPDATE torrents SET numratings = numratings + 1, ratingsum = ratingsum + $rating WHERE id = $id");

header("Refresh: 0; url=details.php?id=$id&rated=1");

?>