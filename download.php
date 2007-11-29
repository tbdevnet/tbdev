<?php

require_once("include/bittorrent.php");

dbconn();

if (!preg_match(':^/(\d{1,10})/(.+)\.torrent$:', $_SERVER["PATH_INFO"], $matches))
	httperr();

$id = 0 + $matches[1];
if (!$id)
	httperr();

$res = mysql_query("SELECT name FROM torrents WHERE id = $id") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_assoc($res);

$fn = "$torrent_dir/$id.torrent";

if (!$row || !is_file($fn) || !is_readable($fn))
	httperr();


mysql_query("UPDATE torrents SET hits = hits + 1 WHERE id = $id");

header("Content-Type: application/x-bittorrent");

readfile($fn);

?>