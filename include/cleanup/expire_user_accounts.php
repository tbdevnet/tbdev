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

	set_time_limit(0);
	ignore_user_abort(1);

	$deadtime = TIME_NOW - $TBDEV['signup_timeout'];
	@mysql_query("DELETE FROM users WHERE status = 'pending' AND added < $deadtime AND last_login < $deadtime AND last_access < $deadtime");

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