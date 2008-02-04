<?php
//print_r($_POST);exit();
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