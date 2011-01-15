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
	define('IN_TBDEV_FORUM', TRUE);

  require_once "include/bittorrent.php";
  require_once "include/user_functions.php";
  require_once "include/html_functions.php";
  //require_once "include/bbcode_functions.php";
  require_once "forums/forum_functions.php";
  

  dbconn(false);

  loggedinorreturn();
  
  $lang = array_merge( load_language('global'), load_language('forums') );
  
  $action = isset($_GET["action"]) ? $_GET["action"] : '';
  $forum_pic_url = $TBDEV['pic_base_url'] . 'forumicons/';
    //-------- Global variables

  $maxsubjectlength = 40;
  $postsperpage = $CURUSER["postsperpage"];
	if (!$postsperpage) $postsperpage = 25;

  switch($action) {
  
    case 'viewforum':
      require_once "forums/forum_view.php";
      exit();
      break;
      
    case 'viewtopic':
      require_once "include/bbcode_functions.php";
      require_once "forums/forum_topicview.php";
      exit();
      break;
      
    case 'reply':
    case 'quotepost':
      require_once "include/bbcode_functions.php";
      require_once "forums/forum_reply.php";
      exit();
      break;
      
    case 'post':
      require_once "forums/forum_post.php";
      exit();
      break;
      
    case 'newtopic':
      require_once "forums/forum_new_topic.php";
      exit();
      break;
    
    case 'deletepost':
    case 'editpost':
      require_once "forums/forum_user_options.php";
      exit();
      break;
      
    case 'locktopic':
    case 'unlocktopic':
    case 'setlocked':
    case 'renametopic':
    case 'setsticky':
    case 'deletetopic':
    case 'movetopic':
      require_once "forums/forum_mod_options.php";
      exit();
      break;
      
    case 'viewunread':
      require_once "forums/forum_view_unread.php";
      exit();
      break;
      
    case 'search':
      require_once "forums/forum_search.php";
      exit();
      break;
      
    case 'catchup':
      catch_up();
      std_view();
      exit();
      break;
    
    default:
      std_view();
      break;
  }


function std_view() {

  global $TBDEV, $CURUSER, $lang, $forum_pic_url;
  
  //$lang = array_merge( $lang, load_language('forums') );
  
  $forums_res = mysql_query("SELECT * FROM forums ORDER BY sort, name") or sqlerr(__FILE__, __LINE__);
  
  $htmlout = '';
  
  $fnav = "<div class='fnav'>{$lang['forums_title']}</div>\n";
  
  $buttons = "<div style='text-align:right;margin:10px 0px 10px 0px;'>
  <span class='fbtn'><a href='forums.php?action=search'>{$lang['forums_search']}</a></span>
  &nbsp;<span class='fbtn'><a href='forums.php?action=viewunread'>{$lang['forums_view_unread']}</a></span>
  &nbsp;<span class='fbtn'><a href='forums.php?action=catchup'>{$lang['forums_catchup']}</a></span>
  </div>";
  
  $htmlout .="<div class='tb_table_outer_wrap'>{$fnav}$buttons
  <div class='tb_table_inner_wrap'>
  <span style='color:#ffffff;'>{$lang['forums_title']}</span>
  </div>
  <table class='tb_table'>\n";

  $htmlout .= "
  <tr class='header'>
    <th class='col_c_icon'>&nbsp;</th>
    <th class='col_c_forum left'>{$lang['forums_forum_heading']}</th>
    <th class='col_c_stats right'>{$lang['forums_topic_heading']}</th>
    <th class='col_c_stats right'>{$lang['forums_posts_heading']}</th>
    <th class='col_c_post left'>{$lang['forums_lastpost_heading']}</th>
    </tr>\n";

  while ($forums_arr = mysql_fetch_assoc($forums_res))
  {
    if (get_user_class() < $forums_arr["minclassread"])
      continue;

    $forumid = $forums_arr["id"];

    $forumname = htmlsafechars($forums_arr["name"]);

    $forumdescription = htmlsafechars($forums_arr["description"]);

    $topiccount = number_format($forums_arr["topiccount"]);

    $postcount = number_format($forums_arr["postcount"]);

    $lastpostid = get_forum_last_post($forumid);

    // Get last post info

    $post_res = mysql_query("SELECT p.added, p.topicid, p.userid, u.username, t.subject
							FROM posts p
							LEFT JOIN users u ON p.userid = u.id
							LEFT JOIN topics t ON p.topicid = t.id
							WHERE p.id = $lastpostid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($post_res) == 1)
    {
      $post_arr = mysql_fetch_assoc($post_res) or die("{$lang['forums_bad_post']}");

      $lastposterid = $post_arr["userid"];

      $lastpostdate = get_date( $post_arr["added"],'' );

      $lasttopicid = $post_arr["topicid"];

      //$user_res = mysql_query("SELECT username FROM users WHERE id=$lastposterid") or sqlerr(__FILE__, __LINE__);

      //$user_arr = mysql_fetch_assoc($user_res);

      $lastposter = htmlsafechars($post_arr['username']);

      //$topic_res = mysql_query("SELECT subject FROM topics WHERE id=$lasttopicid") or sqlerr(__FILE__, __LINE__);

      //$topic_arr = mysql_fetch_assoc($topic_res);

      $lasttopic = htmlsafechars($post_arr['subject']);

      $lastpost = "<span style='white-space: nowrap;'>$lastpostdate</span><br />
      <strong>by</strong> <a href='userdetails.php?id=$lastposterid'>$lastposter</a><br /><strong>in</strong> <a href='forums.php?action=viewtopic&amp;topicid=$lasttopicid&amp;page=p$lastpostid#$lastpostid'>$lasttopic</a>";

      $r = mysql_query("SELECT lastpostread FROM readposts WHERE userid={$CURUSER['id']} AND topicid=$lasttopicid") or sqlerr(__FILE__, __LINE__);

      $a = mysql_fetch_row($r);

	//..rp..
	$npostcheck = ($post_arr['added'] > (TIME_NOW - $TBDEV['readpost_expiry'])) ? (!$a OR $lastpostid > $a[0]) : 0;
	
	/* if ($a && $a[0] >= $lastpostid)
	$img = "unlocked";
	else
	$img = "unlockednew";
	*/
	
	if ($npostcheck)
	$img = "unlockednew";
	else
	$img = "unlocked";
	
	// ..rp..
    }
    else
    {
      $lastpost = "N/A";
      $img = "unlocked";
    }
    $htmlout .= "
    <tr class='row1'>
      <td class='altrow'>
      <img src=\"{$forum_pic_url}$img.gif\" alt='' title='' /></td>
      <td class='noborder'>
      <a href='forums.php?action=viewforum&amp;forumid=$forumid'><strong>$forumname</strong></a><p class='desc'>$forumdescription</p></td>
      <td class='altrow stats'>$topiccount</td>
      <td class='altrow stats'>$postcount</td>
      <td class='last_post noborder'>$lastpost</td>
    </tr>\n";
  }

  $htmlout .= "</table>$buttons</div><br />\n";

  //$buttons = "<div style='width:80%'><p style='text-align:right;'><span class='btn'><a href='forums.php?action=search'>{$lang['forums_search']}</a></span>&nbsp;<span class='btn'><a href='forums.php?action=viewunread'>{$lang['forums_view_unread']}</a></span>&nbsp;<span class='btn'><a href='forums.php?action=catchup'>{$lang['forums_catchup']}</a></span></p></div>";


  print stdhead("{$lang['forums_title']}") . $htmlout . stdfoot();
  exit();
}
?>