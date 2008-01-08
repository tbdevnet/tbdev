<?php

require_once("include/bittorrent.php");

dbconn();

loggedinorreturn();



if (!preg_match(':^/(\d{1,10})/(.+)\.torrent$:', $_SERVER["PATH_INFO"], $matches))
	httperr();

$id = 0 + $matches[1];
if (!$id)
	httperr();




$res = mysql_query("SELECT name, filename FROM torrents WHERE id = $id") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_assoc($res);

$fn = "$torrent_dir/$id.torrent";

if (!$row || !is_file($fn) || !is_readable($fn))
	httperr();


mysql_query("UPDATE torrents SET hits = hits + 1 WHERE id = $id");

require_once "include/benc.php";



if (!isset($CURUSER['passkey']) || strlen($CURUSER['passkey']) != 32) {

$CURUSER['passkey'] = md5($CURUSER['username'].get_date_time().$CURUSER['passhash']);

mysql_query("UPDATE users SET passkey='{$CURUSER['passkey']}' WHERE id={$CURUSER['id']}");

}



$dict = bdec_file($fn, (1024*1024));

//$dict['value']['announce']['value'] = "$BASEURL/announce.php/passkey=$CURUSER[passkey]";
$dict['value']['announce']['value'] = "{$announce_urls[0]}/passkey={$CURUSER['passkey']}";

$dict['value']['announce']['string'] = strlen($dict['value']['announce']['value']).":".$dict['value']['announce']['value'];

$dict['value']['announce']['strlen'] = strlen($dict['value']['announce']['string']);



header('Content-Disposition: attachment; filename="[TBDev]'.$row['filename'].'"');

header("Content-Type: application/x-bittorrent");



print(benc($dict));



?>