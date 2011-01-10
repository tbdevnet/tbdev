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

	// delete old torrents
	$days = 28;
	$dt = (TIME_NOW - ($days * 86400));
	$res = mysql_query("SELECT id, name FROM torrents WHERE added < $dt");
	while ($arr = mysql_fetch_assoc($res))
	{
		@unlink("{$TBDEV['torrent_dir']}/{$arr['id']}.torrent");
		@mysql_query("DELETE FROM torrents WHERE id={$arr['id']}");
		@mysql_query("DELETE FROM peers WHERE torrent={$arr['id']}");
		@mysql_query("DELETE FROM comments WHERE torrent={$arr['id']}");
		@mysql_query("DELETE FROM files WHERE torrent={$arr['id']}");
		write_log("Torrent {$arr['id']} ({$arr['name']}) was deleted by system (older than $days days)");
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