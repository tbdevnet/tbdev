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

	// promote power users
	$limit = 25*1024*1024*1024;
	$minratio = 1.05;
	$maxdt = (TIME_NOW - 86400*28);
	$res = @mysql_query("SELECT id FROM users WHERE class = 0 AND uploaded >= $limit AND uploaded / downloaded >= $minratio AND added < $maxdt") or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($res) > 0)
	{
		$dt = TIME_NOW;
		$msg = sqlesc("Congratulations, you have been auto-promoted to [b]Power User[/b]. :)\nYou can now download dox over 1 meg and view torrent NFOs.\n");
		while ($arr = mysql_fetch_assoc($res))
		{
			@mysql_query("UPDATE users SET class = 1 WHERE id = {$arr['id']}") or sqlerr(__FILE__, __LINE__);
			@mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, {$arr['id']}, $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);
		}
	}

	// demote power users
	$minratio = 0.95;
	$res = mysql_query("SELECT id FROM users WHERE class = 1 AND uploaded / downloaded < $minratio") or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($res) > 0)
	{
		$dt = TIME_NOW;
		$msg = sqlesc("You have been auto-demoted from [b]Power User[/b] to [b]User[/b] because your share ratio has dropped below $minratio.\n");
		while ($arr = mysql_fetch_assoc($res))
		{
			@mysql_query("UPDATE users SET class = 0 WHERE id = {$arr['id']}") or sqlerr(__FILE__, __LINE__);
			@mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, {$arr['id']}, $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);
		}
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