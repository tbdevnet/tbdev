<?php

function local_user()
{
  return $_SERVER["SERVER_ADDR"] == $_SERVER["REMOTE_ADDR"];
}
//$FUNDS = "$2,610.31";

$SITE_ONLINE = true;
//$SITE_ONLINE = local_user();
//$SITE_ONLINE = false;

$max_torrent_size = 1000000;
$announce_interval = 60 * 30;
$signup_timeout = 86400 * 3;
$minvotes = 1;
$max_dead_torrent_time = 6 * 3600;

// Max users on site
$maxusers = 75000; // LoL Who we kiddin' here?

// Max users on site
$maxusers = 5000;

// ONLY USE ONE OF THE FOLLOWING DEPENDING ON YOUR O/S!!!
//$torrent_dir = "/var/tb/torrents";    # FOR UNIX ONLY - must be writable for httpd user
$torrent_dir = "F:/web/xampp/htdocs/tb/torrents";    # FOR WINDOWS ONLY - must be writable for httpd user

# the first one will be displayed on the pages
$announce_urls = array();
$announce_urls[] = "http://localhost/TBDEV/announce.php";
$announce_urls[] = "http://domain.com:82/announce.php";
$announce_urls[] = "http://domain.com:83/announce.php";

if ($_SERVER["HTTP_HOST"] == "")
  $_SERVER["HTTP_HOST"] = $_SERVER["SERVER_NAME"];
$BASEURL = "http://" . $_SERVER["HTTP_HOST"]."/TB";

// Set this to your site URL... No ending slash!
$DEFAULTBASEURL = "http://localhost/TB";

//set this to true to make this a tracker that only registered users may use
$MEMBERSONLY = true;

//maximum number of peers (seeders+leechers) allowed before torrents starts to be deleted to make room...
//set this to something high if you don't require this feature
$PEERLIMIT = 50000;

// Email for sender/return path.
$SITEEMAIL = "noreply@domain.com";

$SITENAME = "TBDEV.NET";

$autoclean_interval = 900;
$pic_base_url = "./pic/";

// Set this to the line break character sequence of your system
$linebreak = "\r\n";

define ('UC_USER', 0);
define ('UC_POWER_USER', 1);
define ('UC_VIP', 2);
define ('UC_UPLOADER', 3);
define ('UC_MODERATOR', 4);
define ('UC_ADMINISTRATOR', 5);
define ('UC_SYSOP', 6);

require_once("secrets.php");
require_once("cleanup.php");

//Do not modify -- versioning system
//This will help identify code for support issues at tbdev.net
define ('TBVERSION','TBDEV.NET-12-09-05');

/**** validip/getip courtesy of manolete <manolete@myway.com> ****/

// IP Validation
function validip($ip)
{
	if (!empty($ip) && $ip == long2ip(ip2long($ip)))
	{
		// reserved IANA IPv4 addresses
		// http://www.iana.org/assignments/ipv4-address-space
		$reserved_ips = array (
				array('0.0.0.0','2.255.255.255'),
				array('10.0.0.0','10.255.255.255'),
				array('127.0.0.0','127.255.255.255'),
				array('169.254.0.0','169.254.255.255'),
				array('172.16.0.0','172.31.255.255'),
				array('192.0.2.0','192.0.2.255'),
				array('192.168.0.0','192.168.255.255'),
				array('255.255.255.0','255.255.255.255')
		);

		foreach ($reserved_ips as $r)
		{
				$min = ip2long($r[0]);
				$max = ip2long($r[1]);
				if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
		}
		return true;
	}
	else return false;
}

// Patched function to detect REAL IP address if it's valid
function getip() {
   if (isset($_SERVER)) {
     if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && validip($_SERVER['HTTP_X_FORWARDED_FOR'])) {
       $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
     } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && validip($_SERVER['HTTP_CLIENT_IP'])) {
       $ip = $_SERVER['HTTP_CLIENT_IP'];
     } else {
       $ip = $_SERVER['REMOTE_ADDR'];
     }
   } else {
     if (getenv('HTTP_X_FORWARDED_FOR') && validip(getenv('HTTP_X_FORWARDED_FOR'))) {
       $ip = getenv('HTTP_X_FORWARDED_FOR');
     } elseif (getenv('HTTP_CLIENT_IP') && validip(getenv('HTTP_CLIENT_IP'))) {
       $ip = getenv('HTTP_CLIENT_IP');
     } else {
       $ip = getenv('REMOTE_ADDR');
     }
   }

   return $ip;
 }

function dbconn($autoclean = false)
{
    global $mysql_host, $mysql_user, $mysql_pass, $mysql_db;

    if (!@mysql_connect($mysql_host, $mysql_user, $mysql_pass))
    {
	  switch (mysql_errno())
	  {
		case 1040:
		case 2002:
			if ($_SERVER[REQUEST_METHOD] == "GET")
				die("<html><head><meta http-equiv=refresh content=\"5 $_SERVER[REQUEST_URI]\"></head><body><table border=0 width=100% height=100%><tr><td><h3 align=center>The server load is very high at the moment. Retrying, please wait...</h3></td></tr></table></body></html>");
			else
				die("Too many users. Please press the Refresh button in your browser to retry.");
        default:
    	    die("[" . mysql_errno() . "] dbconn: mysql_connect: " . mysql_error());
      }
    }
    mysql_select_db($mysql_db)
        or die('dbconn: mysql_select_db: ' + mysql_error());

    userlogin();

    if ($autoclean)
        register_shutdown_function("autoclean");
}


function userlogin() {
    global $SITE_ONLINE;
    unset($GLOBALS["CURUSER"]);

    $ip = getip();
	$nip = ip2long($ip);
    $res = mysql_query("SELECT * FROM bans WHERE $nip >= first AND $nip <= last") or sqlerr(__FILE__, __LINE__);
    if (mysql_num_rows($res) > 0)
    {
      header("HTTP/1.0 403 Forbidden");
      print("<html><body><h1>403 Forbidden</h1>Unauthorized IP address.</body></html>\n");
      die;
    }

    if (!$SITE_ONLINE || empty($_COOKIE["uid"]) || empty($_COOKIE["pass"]))
        return;
    $id = 0 + $_COOKIE["uid"];
    if (!$id || strlen($_COOKIE["pass"]) != 32)
        return;
    $res = mysql_query("SELECT * FROM users WHERE id = $id AND enabled='yes' AND status = 'confirmed'");// or die(mysql_error());
    $row = mysql_fetch_array($res);
    if (!$row)
        return;
    $sec = hash_pad($row["secret"]);
    if ($_COOKIE["pass"] !== $row["passhash"])
        return;
    mysql_query("UPDATE users SET last_access='" . get_date_time() . "', ip=".sqlesc($ip)." WHERE id=" . $row["id"]);// or die(mysql_error());
    $row['ip'] = $ip;
    $GLOBALS["CURUSER"] = $row;
}

function autoclean() {
    global $autoclean_interval;

    $now = time();
    $docleanup = 0;

    $res = mysql_query("SELECT value_u FROM avps WHERE arg = 'lastcleantime'");
    $row = mysql_fetch_array($res);
    if (!$row) {
        mysql_query("INSERT INTO avps (arg, value_u) VALUES ('lastcleantime',$now)");
        return;
    }
    $ts = $row[0];
    if ($ts + $autoclean_interval > $now)
        return;
    mysql_query("UPDATE avps SET value_u=$now WHERE arg='lastcleantime' AND value_u = $ts");
    if (!mysql_affected_rows())
        return;

    docleanup();
}

function unesc($x) {
    if (get_magic_quotes_gpc())
        return stripslashes($x);
    return $x;
}

function mksize($bytes)
{
	if ($bytes < 1000 * 1024)
		return number_format($bytes / 1024, 2) . " kB";
	elseif ($bytes < 1000 * 1048576)
		return number_format($bytes / 1048576, 2) . " MB";
	elseif ($bytes < 1000 * 1073741824)
		return number_format($bytes / 1073741824, 2) . " GB";
	else
		return number_format($bytes / 1099511627776, 2) . " TB";
}

function mksizeint($bytes)
{
	$bytes = max(0, $bytes);
	if ($bytes < 1000)
		return floor($bytes) . " B";
	elseif ($bytes < 1000 * 1024)
		return floor($bytes / 1024) . " kB";
	elseif ($bytes < 1000 * 1048576)
		return floor($bytes / 1048576) . " MB";
	elseif ($bytes < 1000 * 1073741824)
		return floor($bytes / 1073741824) . " GB";
	else
		return floor($bytes / 1099511627776) . " TB";
}

function deadtime() {
    global $announce_interval;
    return time() - floor($announce_interval * 1.3);
}

function mkprettytime($s) {
    if ($s < 0)
        $s = 0;
    $t = array();
    foreach (array("60:sec","60:min","24:hour","0:day") as $x) {
        $y = explode(":", $x);
        if ($y[0] > 1) {
            $v = $s % $y[0];
            $s = floor($s / $y[0]);
        }
        else
            $v = $s;
        $t[$y[1]] = $v;
    }

    if ($t["day"])
        return $t["day"] . "d " . sprintf("%02d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
    if ($t["hour"])
        return sprintf("%d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
//    if ($t["min"])
        return sprintf("%d:%02d", $t["min"], $t["sec"]);
//    return $t["sec"] . " secs";
}

function mkglobal($vars) {
    if (!is_array($vars))
        $vars = explode(":", $vars);
    foreach ($vars as $v) {
        if (isset($_GET[$v]))
            $GLOBALS[$v] = unesc($_GET[$v]);
        elseif (isset($_POST[$v]))
            $GLOBALS[$v] = unesc($_POST[$v]);
        else
            return 0;
    }
    return 1;
}

function tr($x,$y,$noesc=0) {
    if ($noesc)
        $a = $y;
    else {
        $a = htmlspecialchars($y);
        $a = str_replace("\n", "<br />\n", $a);
    }
    print("<tr><td class=\"heading\" valign=\"top\" align=\"right\">$x</td><td valign=\"top\" align=left>$a</td></tr>\n");
}

function validfilename($name) {
    return preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $name);
}

function validemail($email) {
    return preg_match('/^[\w.-]+@([\w.-]+\.)+[a-z]{2,6}$/is', $email);
}

function sqlesc($x) {
    return "'".mysql_real_escape_string($x)."'";
}

function sqlwildcardesc($x) {
    return str_replace(array("%","_"), array("\\%","\\_"), mysql_real_escape_string($x));
}

function urlparse($m) {
    $t = $m[0];
    if (preg_match(',^\w+://,', $t))
        return "<a href=\"$t\">$t</a>";
    return "<a href=\"http://$t\">$t</a>";
}

function parsedescr($d, $html) {
    if (!$html)
    {
      $d = htmlspecialchars($d);
      $d = str_replace("\n", "\n<br>", $d);
    }
    return $d;
}

function stdhead($title = "", $msgalert = true) {
    global $CURUSER, $SITE_ONLINE, $FUNDS, $SITENAME, $pic_base_url;

  if (!$SITE_ONLINE)
    die("Site is down for maintenance, please check back again later... thanks<br>");

    header("Content-Type: text/html; charset=iso-8859-1");
    //header("Pragma: No-cache");
    if ($title == "")
        $title = $SITENAME .(isset($_GET['tbv'])?" (".TBVERSION.")":'');
    else
        $title = $SITENAME .(isset($_GET['tbv'])?" (".TBVERSION.")":''). " :: " . htmlspecialchars($title);
  if ($CURUSER)
  {
    $ss_a = @mysql_fetch_array(@mysql_query("select uri from stylesheets where id=" . $CURUSER["stylesheet"]));
    if ($ss_a) $ss_uri = $ss_a["uri"];
  }
  if (!$ss_uri)
  {
    ($r = mysql_query("SELECT uri FROM stylesheets WHERE id=1")) or die(mysql_error());
    ($a = mysql_fetch_array($r)) or die(mysql_error());
    $ss_uri = $a["uri"];
  }
  if ($msgalert && $CURUSER)
  {
    $res = mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " && unread='yes'") or die("OopppsY!");
    $arr = mysql_fetch_row($res);
    $unread = $arr[0];
  }
?>
<html><head>
<title><?= $title ?></title>
<link rel="stylesheet" href="./<?=$ss_uri?>" type="text/css">
</head>
<body>

<table width=100% cellspacing=0 cellpadding=0 style='background: transparent'>
<tr>
<td class=clear width=49%>
<!--
<table border=0 cellspacing=0 cellpadding=0 style='background: transparent'>
<tr>

<td class=clear>
<img src="<?=$pic_base_url?>star20.gif" style='margin-right: 10px'>
</td>
<td class=clear>
<font color=white><b>Current funds: <?=$FUNDS?></b></font>
</td>
</tr>
</table>
-->

</td>
<td class=clear>
<div align=center>
<img src="<?=$pic_base_url?>logo.jpg" align=center>
</div>
</td>
<td class=clear width=49% align=right>
<a href=donate.php><img src="https://www.paypal.com/en_US/i/btn/x-click-but04.gif" border="0" alt="Make a donation" style='margin-top: 5px'></a>
</td>
</tr></table>
<?php

$w = "width=100%";
//if ($_SERVER["REMOTE_ADDR"] == $_SERVER["SERVER_ADDR"]) $w = "width=984";

?>
<table class=mainouter <?=$w; ?> border="1" cellspacing="0" cellpadding="10">

<!------------- MENU ------------------------------------------------------------------------>

<? $fn = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], "/") + 1); ?>
<tr><td class=outer align=center>
<table class=main width=700 cellspacing="0" cellpadding="5" border="0">
<tr>

<td align="center" class="navigation"><a href=index.php>Home</a></td>
<td align="center" class="navigation"><a href=browse.php>Browse</a></td>
<td align="center" class="navigation"><a href=search.php>Search</a></td>
<td align="center" class="navigation"><a href=upload.php>Upload</a></td>
<? if (!$CURUSER) { ?>
<td align="center" class="navigation">
<a href=login.php>Login</a> / <a href=signup.php>Signup</a>
</td>
<? } else { ?>
<td align="center" class="navigation"><a href=my.php>Profile</a></td>
<? } ?>
<td align="center" class="navigation"><a href=chat.php>Chat</a></td>
<td align="center" class="navigation"><a href=forums.php>Forums</a></td>
<td align="center" class="navigation"><a href=misc/dox.php>DOX</a></td>
<td align="center" class="navigation"><a href=topten.php>Top 10</a></td>
<td align="center" class="navigation"><a href=log.php>Log</a></td>
<td align="center" class="navigation"><a href=rules.php>Rules</a></td>
<td align="center" class="navigation"><a href=faq.php>FAQ</a></td>
<td align="center" class="navigation"><a href=links.php>Links</a></td>
<td align="center" class="navigation"><a href=staff.php>Staff</a></td>
</tr>
</table>
</td>
</tr>
<tr><td align=center class=outer style="padding-top: 20px; padding-bottom: 20px">
<?

if ($unread)
{
  print("<p><table border=0 cellspacing=0 cellpadding=10 bgcolor=red><tr><td style='padding: 10px; background: red'>\n");
  print("<b><a href=inbox.php><font color=white>You have $unread new message" . ($unread > 1 ? "s" : "") . "!</font></a></b>");
  print("</td></tr></table></p>\n");
}

} // stdhead

function stdfoot() {
  //global $pic_base_url;
  print("</td></tr></table>\n");
  print("</body></html>\n");
}

function genbark($x,$y) {
    stdhead($y);
    print("<h2>" . htmlspecialchars($y) . "</h2>\n");
    print("<p>" . htmlspecialchars($x) . "</p>\n");
    stdfoot();
    exit();
}

function mksecret($len = 20) {
    $ret = "";
    for ($i = 0; $i < $len; $i++)
        $ret .= chr(mt_rand(0, 255));
    return $ret;
}

function httperr($code = 404) {
    header("HTTP/1.0 404 Not found");
    print("<h1>Not Found</h1>\n");
    print("<p>Sorry pal :(</p>\n");
    exit();
}

function gmtime()
{
    return strtotime(get_date_time());
}

/*
function logincookie($id, $password, $secret, $updatedb = 1, $expires = 0x7fffffff) {
    $md5 = md5($secret . $password . $secret);
    setcookie("uid", $id, $expires, "/");
    setcookie("pass", $md5, $expires, "/");

    if ($updatedb)
        mysql_query("UPDATE users SET last_login = NOW() WHERE id = $id");
}
*/

function logincookie($id, $passhash, $updatedb = 1, $expires = 0x7fffffff)
{
	setcookie("uid", $id, $expires, "/");
	setcookie("pass", $passhash, $expires, "/");

  if ($updatedb)
  	mysql_query("UPDATE users SET last_login = NOW() WHERE id = $id");
}


function logoutcookie() {
    setcookie("uid", "", 0x7fffffff, "/");
    setcookie("pass", "", 0x7fffffff, "/");
}

function loggedinorreturn() {
    global $CURUSER, $BASEURL;
    if (!$CURUSER) {
        header("Location: $BASEURL/login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]));
        exit();
    }
}

function deletetorrent($id) {
    global $torrent_dir;
    mysql_query("DELETE FROM torrents WHERE id = $id");
    foreach(explode(".","peers.files.comments.ratings") as $x)
        mysql_query("DELETE FROM $x WHERE torrent = $id");
    unlink("$torrent_dir/$id.torrent");
}



function downloaderdata($res) {
    $rows = array();
    $ids = array();
    $peerdata = array();
    while ($row = mysql_fetch_assoc($res)) {
        $rows[] = $row;
        $id = $row["id"];
        $ids[] = $id;
        $peerdata[$id] = array(downloaders => 0, seeders => 0, comments => 0);
    }

    if (count($ids)) {
        $allids = implode(",", $ids);
        $res = mysql_query("SELECT COUNT(*) AS c, torrent, seeder FROM peers WHERE torrent IN ($allids) GROUP BY torrent, seeder");
        while ($row = mysql_fetch_assoc($res)) {
            if ($row["seeder"] == "yes")
                $key = "seeders";
            else
                $key = "downloaders";
            $peerdata[$row["torrent"]][$key] = $row["c"];
        }
        $res = mysql_query("SELECT COUNT(*) AS c, torrent FROM comments WHERE torrent IN ($allids) GROUP BY torrent");
        while ($row = mysql_fetch_assoc($res)) {
            $peerdata[$row["torrent"]]["comments"] = $row["c"];
        }
    }

    return array($rows, $peerdata);
}



function searchfield($s) {
    return preg_replace(array('/[^a-z0-9]/si', '/^\s*/s', '/\s*$/s', '/\s+/s'), array(" ", "", "", " "), $s);
}

function genrelist() {
    $ret = array();
    $res = mysql_query("SELECT id, name FROM categories ORDER BY name");
    while ($row = mysql_fetch_array($res))
        $ret[] = $row;
    return $ret;
}

function linkcolor($num) {
    if (!$num)
        return "red";
//    if ($num == 1)
//        return "yellow";
    return "green";
}

function ratingpic($num) {
    global $pic_base_url;
    $r = round($num * 2) / 2;
    if ($r < 1 || $r > 5)
        return;
    return "<img src=\"{$pic_base_url}{$r}.gif\" border=\"0\" alt=\"rating: $num / 5\" />";
}

function get_row_count($table, $suffix = "")
{
  if ($suffix)
    $suffix = " $suffix";
  ($r = mysql_query("SELECT COUNT(*) FROM $table$suffix")) or die(mysql_error());
  ($a = mysql_fetch_row($r)) or die(mysql_error());
  return $a[0];
}

function stdmsg($heading, $text)
{
  print("<table class=main width=750 border=0 cellpadding=0 cellspacing=0><tr><td class=embedded>\n");
  if ($heading)
    print("<h2>$heading</h2>\n");
  print("<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text>\n");
  print($text . "</td></tr></table></td></tr></table>\n");
}


function stderr($heading, $text)
{
  stdhead();
  stdmsg($heading, $text);
  stdfoot();
  die;
}

function sqlerr($file = '', $line = '')
{
  print("<table border=0 bgcolor=blue align=left cellspacing=0 cellpadding=10 style='background: blue'>" .
    "<tr><td class=embedded><font color=white><h1>SQL Error</h1>\n" .
  "<b>" . mysql_error() . ($file != '' && $line != '' ? "<p>in $file, line $line</p>" : "") . "</b></font></td></tr></table>");
  die;
}

// Returns the current time in GMT in MySQL compatible format.
function get_date_time($timestamp = 0)
{
  if ($timestamp)
    return date("Y-m-d H:i:s", $timestamp);
  else
    return gmdate("Y-m-d H:i:s");
}

function encodehtml($s, $linebreaks = true)
{
  $s = str_replace("<", "&lt;", str_replace("&", "&amp;", $s));
  if ($linebreaks)
    $s = nl2br($s);
  return $s;
}

function get_dt_num()
{
  return gmdate("YmdHis");
}


//Finds last occurrence of needle in haystack
//in PHP5 use strripos() instead of this
function _strlastpos ($haystack, $needle, $offset = 0)
{
	$addLen = strlen ($needle);
	$endPos = $offset - $addLen;
	while (true)
	{
		if (($newPos = strpos ($haystack, $needle, $endPos + $addLen)) === false) break;
		$endPos = $newPos;
	}
	return ($endPos >= 0) ? $endPos : false;
}





function write_log($text)
{
  $text = sqlesc($text);
  $added = sqlesc(get_date_time());
  mysql_query("INSERT INTO sitelog (added, txt) VALUES($added, $text)") or sqlerr(__FILE__, __LINE__);
}


function sql_timestamp_to_unix_timestamp($s)
{
  return mktime(substr($s, 11, 2), substr($s, 14, 2), substr($s, 17, 2), substr($s, 5, 2), substr($s, 8, 2), substr($s, 0, 4));
}

function get_elapsed_time($ts)
{
  $mins = floor((gmtime() - $ts) / 60);
  $hours = floor($mins / 60);
  $mins -= $hours * 60;
  $days = floor($hours / 24);
  $hours -= $days * 24;
  $weeks = floor($days / 7);
  $days -= $weeks * 7;
  $t = "";
  if ($weeks > 0)
    return "$weeks week" . ($weeks > 1 ? "s" : "");
  if ($days > 0)
    return "$days day" . ($days > 1 ? "s" : "");
  if ($hours > 0)
    return "$hours hour" . ($hours > 1 ? "s" : "");
  if ($mins > 0)
    return "$mins min" . ($mins > 1 ? "s" : "");
  return "< 1 min";
}


function hash_pad($hash) {
    return str_pad($hash, 20);
}

function hash_where($name, $hash) {
    $shhash = preg_replace('/ *$/s', "", $hash);
    return "($name = " . sqlesc($hash) . " OR $name = " . sqlesc($shhash) . ")";
}




?>