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
ob_start("ob_gzhandler");

require_once "include/bittorrent.php";
require_once "include/user_functions.php";
require_once "include/cache_functions.php";

dbconn(true);

loggedinorreturn();

    $lang = array_merge( load_language('global'), load_language('index') );
    //$lang = ;
    
    $HTMLOUT = '';
/*
$a = @mysql_fetch_assoc(@mysql_query("SELECT id,username FROM users WHERE status='confirmed' ORDER BY id DESC LIMIT 1")) or die(mysql_error());
if ($CURUSER)
  $latestuser = "<a href='userdetails.php?id=" . $a["id"] . "'>" . $a["username"] . "</a>";
else
  $latestuser = $a['username'];
*/
$TBDEV['memcache_server'] = 'localhost';
$TBDEV['memcache_port'] = 11211;
$TBDEV['memcache'] = 1;
$TBDEVCACHE = array();

  if( tbdev_cache_connect() )
  {
      print( 'The cache is not working as intended' );
      exit();
  }
  
  if( !$TBDEVCACHE['stats']= getCache( 'frontpagestats' ) )
  {
    $sql = @mysql_query( "SELECT seeder, COUNT(*) as cnt FROM peers GROUP BY seeder" );
    
    $TBDEVCACHE['stats'] = array('seeders'=>0, 'leechers'=>0);
    
    while( $row = mysql_fetch_assoc($sql) )
    {
      if($row['seeder'] == 'yes')
      {
        $TBDEVCACHE['stats']['seeders'] = $row['cnt'];
      }
      else
      {
        $TBDEVCACHE['stats']['leechers'] = $row['cnt'];
      }
    }
    
    $TBDEVCACHE['stats']['registered'] = number_format(get_row_count("users"));
    //$unverified = number_format(get_row_count("users", "WHERE status='pending'"));
    $TBDEVCACHE['stats']['torrents'] = number_format(get_row_count("torrents"));
    //$dead = number_format(get_row_count("torrents", "WHERE visible='no'"));

    if ($TBDEVCACHE['stats']['leechers'] == 0)
    {
      $TBDEVCACHE['stats']['ratio'] = 0;
    }
    else
    {
      $TBDEVCACHE['stats']['ratio'] = round($TBDEVCACHE['stats']['seeders'] / $TBDEVCACHE['stats']['leechers'] * 100);
    }
    $TBDEVCACHE['stats']['peers'] = number_format($TBDEVCACHE['stats']['seeders'] + $TBDEVCACHE['stats']['leechers']);
    $TBDEVCACHE['stats']['seeders'] = number_format($TBDEVCACHE['stats']['seeders']);
    $TBDEVCACHE['stats']['leechers'] = number_format($TBDEVCACHE['stats']['leechers']);
    
    
    setCache( 'frontpagestats', $TBDEVCACHE['stats'], 10 );
  }
  //do_put( '12121212', $TBDEVCACHE['stats'], $ttl=60 );
  //print_r( do_get( '12121212' ) );
  //print_r( $TBDEVCACHE['stats'] );exit;
  
  //do_remove( '12121212' );
  
  
  
  if( !$TBDEVCACHE['news']= getCache( 'news' ) )
  {
    $sql = @mysql_query( "SELECT * FROM news WHERE added + ( 3600 *24 *45 ) >
					".TIME_NOW." ORDER BY added DESC LIMIT 10" );
	
    while( $row = mysql_fetch_assoc($sql) )
    {
      $TBDEVCACHE['news'][ $row['id'] ] = $row;
    }
    
    setCache( 'news', $TBDEVCACHE['news'], 30 );
  }
  
  
  //print_r(memcache_get_stats($memcache));exit;
  $adminbutton = '';
    
    if (get_user_class() >= UC_ADMINISTRATOR)
          $adminbutton = "&nbsp;<span style='float:right;'><a href='admin.php?action=news'>News page</a></span>\n";
          
    $HTMLOUT .= "<div style='text-align:left;width:80%;border:1px solid blue;padding:5px;'>
    <div style='background:lightgrey;height:25px;'><span style='font-weight:bold;font-size:12pt;'>{$lang['news_title']}</span>{$adminbutton}</div><br />";
      
					
    if( count($TBDEVCACHE['news']) > 0 )
    {
      require_once "include/bbcode_functions.php";

      $button = "";
      
      foreach( $TBDEVCACHE['news'] as $array )
      {
        if (get_user_class() >= UC_ADMINISTRATOR)
        {
          $button = "<div style='float:right;'><a href='admin.php?action=news&amp;mode=edit&amp;newsid={$array['id']}'>{$lang['news_edit']}</a>&nbsp;<a href='admin.php?action=news&amp;mode=delete&amp;newsid={$array['id']}'>{$lang['news_delete']}</a></div>";
        }
        
        $HTMLOUT .= "<div style='background:lightgrey;height:20px;'><span style='font-weight:bold;font-size:10pt;'>{$array['headline']}</span></div>\n";
        
        $HTMLOUT .= "<span style='color:grey;font-weight:bold;text-decoration:underline;'>".get_date( $array['added'],'DATE') . "</span>{$button}\n";
        
        $HTMLOUT .= "<div style='margin-top:10px;padding:5px;'>".format_comment($array['body'])."</div><hr />\n";
        
      
      }
     
    }

    $HTMLOUT .= "</div><br />\n";


    $HTMLOUT .= "<div style='text-align:left;width:80%;border:1px solid blue;padding:5px;'>
    <div style='background:lightgrey;height:25px;'><span style='font-weight:bold;font-size:12pt;'>{$lang['stats_title']}</span></div><br />
    
      <table align='center' class='main' border='1' cellspacing='0' cellpadding='5'>
      <tr>
      <td class='rowhead'>{$lang['stats_regusers']}</td><td align='right'>{$TBDEVCACHE['stats']['registered']}</td>
      </tr>
      <!-- <tr><td class='rowhead'>{$lang['stats_unverified']}</td><td align=right>{unverified}</td></tr> -->
      <tr>
      <td class='rowhead'>{$lang['stats_torrents']}</td><td align='right'>{$TBDEVCACHE['stats']['torrents']}</td>
      </tr>";
      
    if (isset($TBDEVCACHE['stats']['peers'])) 
    { 
      $HTMLOUT .= "<tr><td class='rowhead'>{$lang['stats_peers']}</td><td align='right'>{$TBDEVCACHE['stats']['peers']}</td></tr>
      <tr><td class='rowhead'>{$lang['stats_seed']}</td><td align='right'>{$TBDEVCACHE['stats']['seeders']}</td></tr>
      <tr><td class='rowhead'>{$lang['stats_leech']}</td><td align='right'>{$TBDEVCACHE['stats']['leechers']}</td></tr>
      <tr><td class='rowhead'>{$lang['stats_sl_ratio']}</td><td align='right'>{$TBDEVCACHE['stats']['ratio']}</td></tr>";
    } 
    
      $HTMLOUT .= "</table>
      </div>";

/*
<h2>Server load</h2>
<table width='100%' border='1' cellspacing='0' cellpadding='1'0><tr><td align=center>
<table class=main border='0' width=402><tr><td style='padding: 0px; background-image: url("<?php echo $TBDEV['pic_base_url']?>loadbarbg.gif"); background-repeat: repeat-x'>
<?php $percent = min(100, round(exec('ps ax | grep -c apache') / 256 * 100));
if ($percent <= 70) $pic = "loadbargreen.gif";
elseif ($percent <= 90) $pic = "loadbaryellow.gif";
else $pic = "loadbarred.gif";
$width = $percent * 4;
print("<img height='1'5 width=$width src=\"{$TBDEV['pic_base_url']}{$pic}\" alt='$percent%'>"); ?>
</td></tr></table>
</td></tr></table>
*/

    $HTMLOUT .= sprintf("<p><font class='small'>{$lang['foot_disclaimer']}</font></p>", $TBDEV['site_name']);
    
    $HTMLOUT .= "";

///////////////////////////// FINAL OUTPUT //////////////////////

    print stdhead('Home') . $HTMLOUT . stdfoot();
?>