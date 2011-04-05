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
// I really really can't see any point to this section
// because all can be deleted from the dead torent routine
// this should only be run once a month at most. Complete waste of time!
function docleanup( $data ) {
	global $TBDEV;

	set_time_limit(0);
	ignore_user_abort(1);

	do {
		$res = mysql_query("SELECT id FROM torrents");
		$ar = array();
		while ($row = mysql_fetch_array($res,MYSQL_NUM)) {
			$id = $row[0];
			$ar[$id] = 1;
		}

		if (!count($ar))
			break;

		$dp = @opendir($TBDEV['torrent_dir']);
		if (!$dp)
			break;

		$ar2 = array();
		while (($file = readdir($dp)) !== false) {
			if (!preg_match('/^(\d+)\.torrent$/', $file, $m))
				continue;
			$id = $m[1];
			$ar2[$id] = 1;
			if (isset($ar[$id]) && $ar[$id])
				continue;
			$ff = $TBDEV['torrent_dir'] . "/$file";
			unlink($ff);
		}
		closedir($dp);

		if (!count($ar2))
			break;

		$delids = array();
		foreach (array_keys($ar) as $k) 
		{
			if (isset($ar2[$k]) && $ar2[$k])
				continue;
			$delids[] = $k;
			unset($ar[$k]);
		}
		
		if (count($delids))
		{
			$ids = join(",", $delids);
			mysql_query("DELETE torrents t, peers p, files f FROM torrents t
                  left join files f on f.torrent=t.id
                  left join peers p on p.torrent=t.id
                  WHERE f.torrent IN ($ids) 
                  OR p.torrent IN ($ids) 
                  OR t.id IN ($ids)");
    }
		
		if( false !== mysql_affected_rows() )
		{
      $data['clean_desc'] = count($delids) . " items deleted";
		}
	
	} while (0);

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