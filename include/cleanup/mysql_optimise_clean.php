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

	$sql = @mysql_query( "SHOW TABLE STATUS FROM {$TBDEV['mysql_db']}" );
  
  $oht = '';
  
  while( $row = mysql_fetch_assoc($sql) )
  {
    if( $row['Data_free'] > 100 )
    {
      $oht .= $row['Data_free'].',';
    }
  }
  
  $oht = rtrim( $oht, ',');
  
  if( $oht != '' )
  {
    $sql = @mysql_query( "OPTIMIZE TABLE {$oht}" );
  }
  
	if( $oht != '' )
  {
    $data['clean_desc'] = "MySQLCleanup optimized {$oht} table(s)";
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