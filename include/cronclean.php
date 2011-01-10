<?php

if ( ! defined( 'IN_TBDEV_CRON' ) )
{
	print "Go away!!";
	exit();
}

    require_once "config.php";
    
    if (!@mysql_connect($TBDEV['mysql_host'], $TBDEV['mysql_user'], $TBDEV['mysql_pass']))
    {
      sqlerr(__FILE__,__LINE__);
    }

    mysql_select_db($TBDEV['mysql_db'])
        or sqlerr(__FILE__,'dbconn: mysql_select_db: ' . mysql_error());
        
    mysql_set_charset('utf8');
    
    $now = TIME_NOW;

    $sql = @mysql_query( "SELECT * FROM cleanup WHERE clean_cron_key = '{$argv[1]}' LIMIT 0,1" );
    
    $row = mysql_fetch_assoc( $sql );
    
    if ( $row['clean_id'] )
		{
			$next_clean = intval( $now + ($row['clean_increment'] ? $row['clean_increment'] : 15*60) );
			// don't really need to update if its cron. no point as yet.
			@mysql_query( "UPDATE cleanup SET clean_time = $next_clean WHERE clean_id = {$row['clean_id']}" );
			
			if ( file_exists( ROOT_PATH.'/include/cleanup/'.$row['clean_file'] ) )
			{
				require_once( ROOT_PATH.'/include/cleanup/'.$row['clean_file'] );
			
        register_shutdown_function( 'docleanup', $row );
			}
		
      
		}

function sqlesc($x) {
    return "'".mysql_real_escape_string($x)."'";
}
?>