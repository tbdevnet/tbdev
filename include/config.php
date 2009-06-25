<?php

error_reporting(E_ALL);

define('SQL_DEBUG', 1);

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

if ( strtoupper( substr(PHP_OS, 0, 3) ) == 'WIN' )
  {
    $file_path = str_replace( "\\", "/", dirname(__FILE__) );
    $file_path = str_replace( "/include", "", $file_path );
  }
  else
  {
    $file_path = dirname(__FILE__);
  }
  
define('ROOT_PATH', $file_path);
$torrent_dir = ROOT_PATH . '/torrents';
//$torrent_dir = "F:/web/xampp/htdocs/tb/torrents";    # FOR WINDOWS ONLY - must be writable for httpd user

# the first one will be displayed on the pages
$announce_urls = array();
$announce_urls[] = "http://localhost/TB/announce.php";
$announce_urls[] = "http://localhost:2710/announce";
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
$SITEEMAIL = "coldfusion@localhost";

$SITENAME = "TBDEV.NET";

$autoclean_interval = 900;
$sql_error_log = './logs/sql_err_'.date("M_D_Y").'.log';
$pic_base_url = "./pic/";
$stylesheet = "./1.css";
$READPOST_EXPIRY = 14*86400; // 14 days
// Set this to the line break character sequence of your system
$linebreak = "\r\n";

define ('UC_USER', 0);
define ('UC_POWER_USER', 1);
define ('UC_VIP', 2);
define ('UC_UPLOADER', 3);
define ('UC_MODERATOR', 4);
define ('UC_ADMINISTRATOR', 5);
define ('UC_SYSOP', 6);

//Do not modify -- versioning system
//This will help identify code for support issues at tbdev.net
define ('TBVERSION','TBDev_ALPHA_1_16.06.09');

?>