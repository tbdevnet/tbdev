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

	$sql = @mysql_query("SHOW PROCESSLIST");
  
  $cnt = 0;
  
  while ($arr = mysql_fetch_assoc($sql))
  {
    if( $arr['db'] == $TBDEV['mysql_db'] AND $arr['Command'] == 'Sleep'  AND $arr['Time'] > 60 )
    {
      @mysql_query( "KILL {$arr['Id']}" );
      $cnt ++;
    }
  }


	if( $cnt != 0 )
  {
    $data['clean_desc'] = "MySQLCleanup killed {$cnt} processes";
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