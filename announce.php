<?php
/*
+------------------------------------------------
|   TBDev.net BitTorrent Tracker PHP
|   =============================================
|   by CoLdFuSiOn
|   (c) 2003 - 2010 TBDev.Net
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
*/error_reporting(0);
////////////////// GLOBAL VARIABLES ////////////////////////////	
$TBDEV['baseurl'] = 'http://localhost/tb_new/';
$TBDEV['announce_interval'] = 60 * 30;
$TBDEV['min_interval'] = 60 * 15;
$TBDEV['user_ratios'] = 0;
$TBDEV['connectable_check'] = 0;
define ('UC_VIP', 2);
// DB setup
$TBDEV['mysql_host'] = "localhost";
$TBDEV['mysql_user'] = "root";
$TBDEV['mysql_pass'] = "blank";
$TBDEV['mysql_db']   = "test";
////////////////// GLOBAL VARIABLES ////////////////////////////

// DO NOT EDIT BELOW UNLESS YOU KNOW WHAT YOU'RE DOING!!

define( 'TIME_NOW', time() );

$agent = $_SERVER["HTTP_USER_AGENT"];

// Deny access made with a browser...
if (
    preg_match('%^Mozilla/|^Opera/|^Links |^Lynx/%i', $agent) || 
    isset($_SERVER['HTTP_COOKIE']) || 
    isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) || 
    isset($_SERVER['HTTP_ACCEPT_CHARSET'])
    )
    err("torrent not registered with this tracker CODE 1");

if( !$_GET['compact'] )
  {
    err('Sorry, this tracker no longer supports non-compact clients!');
  }
/////////////////////// FUNCTION DEFS ///////////////////////////////////
function dbconn()
{
    global $TBDEV;

    if (!@mysql_connect($TBDEV['mysql_host'], $TBDEV['mysql_user'], $TBDEV['mysql_pass']))
    {
	  err('Please call back later');
    }
    mysql_select_db($TBDEV['mysql_db']) or err('Please call back later');
}

function err($x)
{
	exit('d14:failure reason' . strlen($x) . ":{$x}e");
}

function warn($x)
{
	exit('d15:warning message' . strlen($x) . ":{$x}e");
}

function benc_resp_raw($x)
{
    header( "Content-Type: text/plain" );
    header( "Pragma: no-cache" );

    if ( $_SERVER['HTTP_ACCEPT_ENCODING'] == 'gzip' )
    {
        header( "Content-Encoding: gzip" );
        echo gzencode( $x, 9, FORCE_GZIP );
    }
    else
        echo $x ;
}

function hash_where($name, $hash) {
    $shhash = preg_replace('/ *$/s', "", $hash);
    return "($name = " . sqlesc($hash) . " OR $name = " . sqlesc($shhash) . ")";
}

function sqlesc($x) {
    return "'".mysql_real_escape_string($x)."'";
}

function portblacklisted($port)
{
	// direct connect
	if ($port >= 411 && $port <= 413) return true;

	// bittorrent
	if ($port >= 6881 && $port <= 6889) return true;

	// kazaa
	if ($port == 1214) return true;

	// gnutella
	if ($port >= 6346 && $port <= 6347) return true;

	// emule
	if ($port == 4662) return true;

	// winmx
	if ($port == 6699) return true;

	return false;
}
/////////////////////// FUNCTION DEFS END ///////////////////////////////

$parts = array();
if( !isset($_GET['passkey']) OR !preg_match('/^[0-9a-fA-F]{32}$/i', $_GET['passkey'], $parts) ) 
		err("Invalid Passkey");
	else
		$GLOBALS['passkey'] = $parts[0];
		
foreach (array("info_hash","peer_id","event","ip","localip") as $x) 
{
if(isset($_GET["$x"]))
$GLOBALS[$x] = "" . $_GET[$x];
}

foreach (array("port","downloaded","uploaded","left") as $x)
{
$GLOBALS[$x] = 0 + $_GET[$x];
}


foreach (array("passkey","info_hash","peer_id","port","downloaded","uploaded","left") as $x)

if (!isset($x)) err("Missing key: $x");



foreach (array("info_hash","peer_id") as $x)

if (strlen($GLOBALS[$x]) != 20) err("Invalid $x (" . strlen($GLOBALS[$x]) . " - " . urlencode($GLOBALS[$x]) . ")");

unset($x);

$info_hash = bin2hex($info_hash);

$ip = $_SERVER['REMOTE_ADDR'];

$port = 0 + $port;
$downloaded = 0 + $downloaded;
$uploaded = 0 + $uploaded;
$left = 0 + $left;

$rsize = 30;
foreach(array("num want", "numwant", "num_want") as $k)
{
	if (isset($_GET[$k]))
	{
		$rsize = (int)$_GET[$k];
		break;
	}
}


if (!$port || $port > 0xffff)
	err("invalid port");

if (!isset($event))
	$event = "";

$seeder = ($left == 0) ? "yes" : "no";

dbconn();


$user_query = mysql_query("SELECT id, uploaded, downloaded, class, enabled FROM users WHERE passkey=".sqlesc($passkey)) or err("Tracker error 2");

if ( mysql_num_rows($user_query) != 1 )

 err("Unknown passkey. Please redownload the torrent from {$TBDEV['baseurl']}.");
 
	$user = mysql_fetch_assoc($user_query);
	if( $user['enabled'] == 'no' ) err('Permission denied, you\'re not enabled');
	
	
$res = mysql_query("SELECT id, banned, seeders + leechers AS numpeers, added AS ts FROM torrents WHERE info_hash = " .sqlesc($info_hash));//" . hash_where("info_hash", $info_hash));

$torrent = mysql_fetch_assoc($res);
if (!$torrent)
	err("torrent not registered with this tracker CODE 2");

$torrentid = $torrent["id"];

$fields = "seeder, peer_id, compact, ip, port, uploaded, downloaded, userid";

//$numpeers = $torrent["numpeers"];
  $limit = "";

  if ($torrent['numpeers'] > $rsize)
    $limit = "ORDER BY RAND() LIMIT $rsize";
    
  $wherep ='';
  
  if ($seeder == 'yes')
    $whereap = "AND seeder = 'no'";
    
  $res = mysql_query("SELECT $fields FROM peers WHERE torrent = $torrentid AND connectable = 'yes' {$wherep} {$limit}");
  
  unset($wherep);
  
//////////////////// START NEW COMPACT MODE/////////////////////////////

  //$resp = "d" . benc_str("interval") . "i" . $TBDEV['announce_interval'] ."e" . benc_str("min interval") . "i" . 300 ."e5:"."peers" ;
  $resp = "d8:intervali{$TBDEV['announce_interval']}e12:min intervali{$TBDEV['min_interval']}e5:peers";
  
  $peers = '';

  $peer_num = 0;
  
  while ($row = mysql_fetch_assoc($res))
  {
    $peers .= $row['compact']; //pack('Nn', ip2long($row['ip']), $row['port']);

    $peer_num++;
  }



$resp .= strlen($peers) . ':' . $peers . 'e';



$selfwhere = "torrent = $torrentid AND " . hash_where("peer_id", $peer_id);

///////////////////////////// END NEW COMPACT MODE////////////////////////////////



if (!isset($self))
{
	$res = mysql_query("SELECT $fields FROM peers WHERE $selfwhere");
	$row = mysql_fetch_assoc($res);
	if ($row)
	{
		$userid = $row["userid"];
		$self = $row;
	}
}

//// Up/down stats ////////////////////////////////////////////////////////////



if (!isset($self))

{

$valid = @mysql_fetch_row(@mysql_query("SELECT COUNT(*) FROM peers WHERE torrent=$torrentid AND passkey=" . sqlesc($passkey)));

if ($valid[0] >= 1 && $seeder == 'no') err("Connection limit exceeded! You may only leech from one location at a time.");

if ($valid[0] >= 3 && $seeder == 'yes') err("Connection limit exceeded!");


	if ($left > 0 && $user['class'] < UC_VIP && $TBDEV['user_ratios'])
	{
		$gigs = $user["uploaded"] / (1024*1024*1024);
		$elapsed = floor((TIME_NOW - $torrent["ts"]) / 3600);
		$ratio = (($user["downloaded"] > 0) ? ($user["uploaded"] / $user["downloaded"]) : 1);
		if ($ratio < 0.5 || $gigs < 5) $wait = 48;
		elseif ($ratio < 0.65 || $gigs < 6.5) $wait = 24;
		elseif ($ratio < 0.8 || $gigs < 8) $wait = 12;
		elseif ($ratio < 0.95 || $gigs < 9.5) $wait = 6;
		else $wait = 0;
		if ($elapsed < $wait)
				err("Not authorized (" . ($wait - $elapsed) . "h) - READ THE FAQ!");
	}
}
else
{
	$upthis = max(0, $uploaded - $self["uploaded"]);
	$downthis = max(0, $downloaded - $self["downloaded"]);

	if ($upthis > 0 || $downthis > 0)
		mysql_query("UPDATE users SET uploaded = uploaded + $upthis, downloaded = downloaded + $downthis WHERE id=".$user['id']) or err("Tracker error 3");
}

///////////////////////////////////////////////////////////////////////////////


$updateset = array();

if ($event == "stopped")
{
	if (isset($self))
	{
		mysql_query("DELETE FROM peers WHERE $selfwhere");
		if (mysql_affected_rows())
		{
			if ($self["seeder"] == "yes")
				$updateset[] = "seeders = seeders - 1";
			else
				$updateset[] = "leechers = leechers - 1";
		}
	}
}
else
{
	if ($event == "completed")
		$updateset[] = "times_completed = times_completed + 1";

	if (isset($self))
	{
		$compact = '';
		// only update compact if ip or port has changed
    if( $self['ip'] != $ip || ($self['port']+0) != $port )
    {
      $compact = "compact = ".sqlesc(pack('Nn', ip2long($ip), $port)).',';
    }
		
		mysql_query("UPDATE peers SET uploaded = $uploaded, downloaded = $downloaded, 
		to_go = $left, last_action = ".TIME_NOW.", $compact
		seeder = '$seeder'"
			. ($seeder == "yes" && $self["seeder"] != $seeder ? ", 
			finishedat = " . TIME_NOW : "") . " WHERE $selfwhere");
			
		if (mysql_affected_rows() && $self["seeder"] != $seeder)
		{
			if ($seeder == "yes")
			{
				$updateset[] = "seeders = seeders + 1";
				$updateset[] = "leechers = leechers - 1";
			}
			else
			{
				$updateset[] = "seeders = seeders - 1";
				$updateset[] = "leechers = leechers + 1";
			}
		}
	}
	else
	{
		if ($event != "started")
			err("Peer not found. ".$passkey." Restart the torrent.");

		if (portblacklisted($port))
		{
			err("Port $port is blacklisted.");
		}
		elseif ( $TBDEV['connectable_check'] )
		{
			$sockres = @fsockopen($ip, $port, $errno, $errstr, 5);
			if (!$sockres)
				$connectable = "no";
			else
			{
				$connectable = "yes";
				@fclose($sockres);
			}
		}
		else
		{
      $connectable = 'yes';
		}
    
    $compact = sqlesc(pack('Nn', ip2long($ip), $port));
    
		$ret = mysql_query("INSERT INTO peers (connectable, torrent, peer_id, compact, ip, port, uploaded, downloaded, to_go, started, last_action, seeder, userid, agent, passkey) VALUES ('$connectable', $torrentid, " . sqlesc($peer_id) . ", $compact, " . sqlesc($ip) . ", $port, $uploaded, $downloaded, $left, ".TIME_NOW.", ".TIME_NOW.", '$seeder', {$user['id']}, " . sqlesc($agent) . "," . sqlesc($passkey) . ")");
		
		if ($ret)
		{
			if ($seeder == "yes")
				$updateset[] = "seeders = seeders + 1";
			else
				$updateset[] = "leechers = leechers + 1";
		}
	}
}

if ($seeder == "yes")
{
	if ($torrent["banned"] != "yes")
		$updateset[] = "visible = 'yes'";
	
	$updateset[] = "last_action = ".TIME_NOW;
}

if (count($updateset))
	mysql_query("UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $torrentid");

benc_resp_raw($resp);



?>