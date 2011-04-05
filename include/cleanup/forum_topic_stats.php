<?php
/*
+------------------------------------------------
|   TBDev.net BitTorrent Tracker PHP
|   =============================================
|   by CoLdFuSiOn
|   (c) 2003 - 2011 TBDev.Net
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

	$forums = @mysql_query("SELECT f.id, count( DISTINCT t.id ) AS topics, count( * ) AS posts
                          FROM forums f
                          LEFT JOIN topics t ON f.id = t.forumid
                          LEFT JOIN posts p ON t.id = p.topicid
                          GROUP BY f.id");
	while ($forum = mysql_fetch_assoc($forums))
	{
		$forum['posts'] = $forum['topics'] > 0 ? $forum['posts'] : 0;
		@mysql_query("update forums set postcount={$forum['posts']}, topiccount={$forum['topics']} where id={$forum['id']}");
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