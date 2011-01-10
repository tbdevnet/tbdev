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


function docleanup( $data ) {
	global $TBDEV;

	set_time_limit(1200);
	ignore_user_abort(1);

	$torrents = array();
	$res = @mysql_query("SELECT torrent, seeder, COUNT(*) AS c FROM peers GROUP BY torrent, seeder");
	while ($row = mysql_fetch_assoc($res)) 
	{
		if ($row["seeder"] == "yes")
			$key = "seeders";
		else
			$key = "leechers";
		$torrents[$row["torrent"]][$key] = $row["c"];
	}

	$res = @mysql_query("SELECT torrent, COUNT(*) AS c FROM comments GROUP BY torrent");
	while ($row = mysql_fetch_assoc($res)) 
	{
		$torrents[$row["torrent"]]["comments"] = $row["c"];
	}
	
	
	$fields = explode(":", "comments:leechers:seeders");
	$res = @mysql_query("SELECT id, seeders, leechers, comments FROM torrents");
	while ($row = mysql_fetch_assoc($res)) 
	{
		$id = $row["id"];
		if(isset($torrents[$id]))
		$torr = $torrents[$id];
		foreach ($fields as $field) 
		{
			if (!isset($torr[$field]))
				$torr[$field] = 0;
		}
		$update = array();
		foreach ($fields as $field) {
			if ($torr[$field] != $row[$field])
				$update[] = "$field = " . $torr[$field];
		}
		if (count($update))
			@mysql_query("UPDATE torrents SET " . implode(",", $update) . " WHERE id = $id");
	}

	if( false !== mysql_affected_rows() )
  {
    $data['clean_desc'] = mysql_affected_rows() . " items deleted";
  }
	
	if( $data['clean_log'] )
	{
    cleanup_log( $data );
	}

}

function cleanup_log( $data )
{
  $text = sqlesc($data['clean_title']);
  $added = TIME_NOW;
  $ip = sqlesc($_SERVER['REMOTE_ADDR']);
  $desc = sqlesc($data['clean_desc']);
  
  mysql_query( "INSERT INTO cleanup_log (clog_event, clog_time, clog_ip, clog_desc) VALUES ($text, $added, $ip, {$desc})" ) or sqlerr(__FILE__, __LINE__);
}
?>