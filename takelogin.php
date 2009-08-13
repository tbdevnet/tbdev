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
|   $Date: $
|   $Revision: $
|   $Author: $
|   $URL: $
+------------------------------------------------
*/
require_once("include/bittorrent.php");

if (!mkglobal("username:password:captcha"))
	die();
	
session_start();
  if(empty($captcha) || $_SESSION['captcha_id'] != strtoupper($captcha)){
      header('Location: login.php');
      exit();
}

dbconn();

function bark($text = "Username or password incorrect")
{
  stderr("Login failed!", $text);
}

$res = mysql_query("SELECT id, passhash, secret, enabled FROM users WHERE username = " . sqlesc($username) . " AND status = 'confirmed'");
$row = mysql_fetch_assoc($res);

if (!$row)
	bark();

if ($row["passhash"] != md5($row["secret"] . $password . $row["secret"]))
	bark();

if ($row["enabled"] == "no")
	bark("This account has been disabled.");

logincookie($row["id"], $row["passhash"]);

if (!empty($_POST["returnto"]))
	header("Location: $_POST[returnto]");
else
	header("Location: my.php");

?>