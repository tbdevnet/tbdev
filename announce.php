<?php



require_once "include/bittorrent.php";
require_once "include/benc.php";

if(!isset($_SERVER['PATH_INFO']) || empty($_SERVER['PATH_INFO']))
	err('PATH_INFO Failure');
	
	
function err($msg)
{
	benc_resp(array("failure reason" => array('type' => "string", 'value' => $msg)));
	
	exit();
}

function benc_resp($d)
{
	benc_resp_raw(benc(array('type' => "dictionary", 'value' => $d)));
}

function benc_resp_raw($x)
{
	header("Content-Type: text/plain");
	header("Pragma: no-cache");
	print($x);
}
/*
$pathchop = trim($_SERVER["PATH_INFO"], "/");
$pathtokey = explode("=", $pathchop);
//echo "key = " . $pathtokey[0] . " AND value = " . $pathtokey[1] . "<br>";
$GLOBALS["passkey"] = "$pathtokey[1]";
//echo $passkey;
*/
$parts = array();
$pattern = '[0-9a-fA-F]{32}';
if(!ereg($pattern, $_SERVER['PATH_INFO'], $parts)) 
		err("Invalid Passkey");
	else
		$GLOBALS['passkey'] = $parts[0];
//if(!isset($_GET["event"])) $_GET["event"] = "vapourware";
//$ipcheck = isset( $_GET['ip'] ) ? "ip" : "localip";
foreach (array("info_hash","peer_id","event","ip","localip") as $x) 
{
if(isset($_GET["$x"]))
$GLOBALS[$x] = "" . $_GET[$x];
//echo $GLOBALS[$x] . "---------" . $_GET[$x] . "<br>";
}

foreach (array("port","downloaded","uploaded","left") as $x)
{
$GLOBALS[$x] = 0 + $_GET[$x];
//echo $GLOBALS[$x] . "---------" . $_GET[$x] . "<br>";
}


foreach (array("passkey","info_hash","peer_id","port","downloaded","uploaded","left") as $x)

if (!isset($x)) err("Missing key: $x");



foreach (array("info_hash","peer_id") as $x)

if (strlen($GLOBALS[$x]) != 20) err("Invalid $x (" . strlen($GLOBALS[$x]) . " - " . urlencode($GLOBALS[$x]) . ")");

unset($x);

//if (strlen($GLOBALS['passkey']) != 32) err("Invalid passkey (" . strlen($passkey) . " - $passkey)");



//if (empty($ip) || !preg_match('/^(d{1,3}.){3}d{1,3}$/s', $ip))

$ip = getip();

$port = 0 + $port;
$downloaded = 0 + $downloaded;
$uploaded = 0 + $uploaded;
$left = 0 + $left;

$rsize = 50;
foreach(array("num want", "numwant", "num_want") as $k)
{
	if (isset($_GET[$k]))
	{
		$rsize = 0 + $_GET[$k];
		break;
	}
}

$agent = $_SERVER["HTTP_USER_AGENT"];

// Deny access made with a browser...
if (ereg("^Mozilla\\/", $agent) || ereg("^Opera\\/", $agent) || ereg("^Links ", $agent) || ereg("^Lynx\\/", $agent))
	err("torrent not registered with this tracker CODE 1");

if (!$port || $port > 0xffff)
	err("invalid port");

if (!isset($event))
	$event = "";

$seeder = ($left == 0) ? "yes" : "no";

dbconn(false);

$valid = @mysql_fetch_row(@mysql_query("SELECT COUNT(*) FROM users WHERE passkey=" . sqlesc($passkey)));

if ($valid[0] != 1) err("Invalid passkey! Re-download the .torrent from $BASEURL");

$res = mysql_query("SELECT id, banned, seeders + leechers AS numpeers, UNIX_TIMESTAMP(added) AS ts FROM torrents WHERE " . hash_where("info_hash", $info_hash));

$torrent = mysql_fetch_assoc($res);
if (!$torrent)
	err("torrent not registered with this tracker CODE 2");

$torrentid = $torrent["id"];

$fields = "seeder, peer_id, ip, port, uploaded, downloaded, userid";

$numpeers = $torrent["numpeers"];
$limit = "";
if ($numpeers > $rsize)
	$limit = "ORDER BY RAND() LIMIT $rsize";
$res = mysql_query("SELECT $fields FROM peers WHERE torrent = $torrentid AND connectable = 'yes' $limit");

//////////////////// START NEW COMPACT MODE/////////////////////////////

if($_GET['compact'] != 1)

{

$resp = "d" . benc_str("interval") . "i" . $announce_interval . "e" . benc_str("peers") . "l";

}

else

{

$resp = "d" . benc_str("interval") . "i" . $announce_interval ."e" . benc_str("min interval") . "i" . 300 ."e5:"."peers" ;

}

$peer = array();

$peer_num = 0;
while ($row = mysql_fetch_assoc($res))

{

    if($_GET['compact'] != 1)

{



$row["peer_id"] = hash_pad($row["peer_id"]);



if ($row["peer_id"] === $peer_id)

{

 $self = $row;

 continue;

}



$resp .= "d" .

 benc_str("ip") . benc_str($row["ip"]);

       if (!$_GET['no_peer_id']) {

  $resp .= benc_str("peer id") . benc_str($row["peer_id"]);

 }

 $resp .= benc_str("port") . "i" . $row["port"] . "e" .

 "e";

      }

      else

      {

         $peer_ip = explode('.', $row["ip"]);

$peer_ip = pack("C*", $peer_ip[0], $peer_ip[1], $peer_ip[2], $peer_ip[3]);

$peer_port = pack("n*", (int)$row["port"]);

$time = intval((time() % 7680) / 60);

if($_GET['left'] == 0)

{

$time += 128;

}

$time = pack("C", $time);



   $peer[] = $time . $peer_ip . $peer_port;

$peer_num++;


      }

}



if ($_GET['compact']!=1)

$resp .= "ee";

else

{
$o = "";
for($i=0;$i<$peer_num;$i++)

 {

  $o .= substr($peer[$i], 1, 6);

 }

$resp .= strlen($o) . ':' . $o . 'e';

}

$selfwhere = "torrent = $torrentid AND " . hash_where("peer_id", $peer_id);

///////////////////////////// END NEW COMPACT MODE////////////////////////////////

/************ OLD MODE
$resp = "d" . benc_str("interval") . "i" . $announce_interval . "e" . benc_str("peers") . "l";
unset($self);
while ($row = mysql_fetch_assoc($res))
{
	$row["peer_id"] = hash_pad($row["peer_id"]);

	if ($row["peer_id"] === $peer_id)
	{
		$userid = $row["userid"];
		$self = $row;
		continue;
	}

	$resp .= "d" .
		benc_str("ip") . benc_str($row["ip"]) .
		benc_str("peer id") . benc_str($row["peer_id"]) .
		benc_str("port") . "i" . $row["port"] . "e" .
		"e";
}

$resp .= "ee";

$selfwhere = "torrent = $torrentid AND " . hash_where("peer_id", $peer_id);
/*********************END OLD MODE */


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



$rz = mysql_query("SELECT id, uploaded, downloaded, class FROM users WHERE passkey=".sqlesc($passkey)." AND enabled = 'yes' ORDER BY last_access DESC LIMIT 1") or err("Tracker error 2");

if ($MEMBERSONLY && mysql_num_rows($rz) == 0)

 err("Unknown passkey. Please redownload the torrent from $BASEURL.");
 
	$az = mysql_fetch_assoc($rz);
	$userid = $az["id"];

	if ($left > 0 && $az["class"] < UC_VIP)
//	if ($az["class"] < UC_VIP)
	{
		$gigs = $az["uploaded"] / (1024*1024*1024);
		$elapsed = floor((gmtime() - $torrent["ts"]) / 3600);
		$ratio = (($az["downloaded"] > 0) ? ($az["uploaded"] / $az["downloaded"]) : 1);
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
		mysql_query("UPDATE users SET uploaded = uploaded + $upthis, downloaded = downloaded + $downthis WHERE id=$userid") or err("Tracker error 3");
}

///////////////////////////////////////////////////////////////////////////////

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
		mysql_query("UPDATE peers SET uploaded = $uploaded, downloaded = $downloaded, to_go = $left, last_action = NOW(), seeder = '$seeder'"
			. ($seeder == "yes" && $self["seeder"] != $seeder ? ", finishedat = " . time() : "") . " WHERE $selfwhere");
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
			err("Port $port is blacklisted.");
		else
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

		$ret = mysql_query("INSERT INTO peers (connectable, torrent, peer_id, ip, port, uploaded, downloaded, to_go, started, last_action, seeder, userid, agent, passkey) VALUES ('$connectable', $torrentid, " . sqlesc($peer_id) . ", " . sqlesc($ip) . ", $port, $uploaded, $downloaded, $left, NOW(), NOW(), '$seeder', $userid, " . sqlesc($agent) . "," . sqlesc($passkey) . ")");
		
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
	$updateset[] = "last_action = NOW()";
}

if (count($updateset))
	mysql_query("UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $torrentid");

benc_resp_raw($resp);



?>