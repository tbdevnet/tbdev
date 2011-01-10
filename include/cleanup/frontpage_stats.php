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
	//global $TBDEV;

	set_time_limit(0);
	ignore_user_abort(1);
  
	$stats = array();
	
  // Update stats 
	$sql = @mysql_query( "SELECT seeder, COUNT(*) as cnt FROM peers GROUP BY seeder" );
	
  while( $row = mysql_fetch_assoc($sql) )
  {
    if($row['seeder'] == 'yes')
    {
      $stats['seeders'] = $row['cnt'];
    }
    else
    {
      $stats['leechers'] = $row['cnt'];
    }
  }
  
  $sql = @mysql_query( "SELECT COUNT(*) as cnt FROM users" );
	$row = mysql_fetch_assoc($sql);
	$stats['usercnt'] = $row['cnt'];
	
	$sql = @mysql_query( "SELECT COUNT(*) as cnt FROM torrents" );
	$row = mysql_fetch_assoc($sql);
	$stats['torrentcnt'] = $row['cnt'];
	
  $stats['peers'] = ($stats['seeders'] + $stats['leechers']);
  $stats['perc'] = number_format( $stats['leechers'] != 0 ?($stats['seeders']/$stats['leechers']) : 0, 2 );
  
  // temporary cache mechanism until the real deal
  $fh = @fopen( ROOT_PATH.'/cache/stats.php', 'wb' );
		
  if( !$fh )
  {
    return FALSE;
  }
  
  if( is_array( $stats ) )
  {
    $stats = serialize($stats);
  }
  
  $value = '"'.addslashes( $stats ).'"';
  
  $file_content = "<?"."php\n\n".'$stats = '.$stats.";\n\n?".'>';
  
  flock( $fh, LOCK_EX );
  fwrite( $fh, $file_content );
  flock( $fh, LOCK_UN );
  fclose( $fh );
  
  @chmod( ROOT_PATH.'/cache/stats.php', 0777 );
  // cache end temporary
  /*
  $stats = unserialize( stripslashes($stats) );
  
  foreach($stats as $k => $v)
  {
    print $v .'----'.$k.'<br />';
  }
  exit;
  */
	if( !$data['clean_log'] )
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