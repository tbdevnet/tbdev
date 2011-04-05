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
if ( ! defined( 'IN_TBDEV_FORUM' ) )
{
	print "{$lang['forum_view_access']}";
	exit();
}

  //$lang = array_merge( $lang, load_language('forums') );

  //-------- Action: View forum

    $HTMLOUT = '';
    
    $forumid = isset($_GET["forumid"]) ? intval($_GET["forumid"]) : 0;

    if (!is_valid_id($forumid))
      header("Location: {$TBDEV['baseurl']}/forum.php");

    $page = isset($_GET["page"]) ? intval($_GET["page"]) : 0;

    $userid = $CURUSER["id"];

    //------ Get forum name

    $res = @mysql_query("SELECT name, minclassread, minclasswrite, minclasscreate FROM forums WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);

    if( false == mysql_num_rows($res) )
    {
      header("Location: {$TBDEV['baseurl']}/forums.php");
    }
    
    $thisforum = mysql_fetch_assoc($res);

    $forumname = htmlsafechars($thisforum["name"]);

    if (get_user_class() < $thisforum["minclassread"])
      header("Location: {$TBDEV['baseurl']}/forums.php");
      //die("Not permitted");

    //------ Page links

/////////////////// Get topic count & Do Pager thang! ////////////////////////////


    $perpage = $CURUSER["topicsperpage"];
    
    if (!$perpage) 
      $perpage = 20;

    $res = @mysql_query("SELECT COUNT(*) FROM topics WHERE forumid=$forumid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_row($res);

    $hits = $arr[0];

    require_once "include/pager.php";
                  $menu = pager( 
                  array( 
                  'count'  => $hits,
                  'perpage'    => $perpage,
                  'start_value'  => $page,
                  'url'    => "forums.php?action=viewforum&amp;forumid=$forumid")
                  );



/////////////////// Get topic count & Do Pager thang end! ////////////////////////////

    //------ Get topics data

    $topicsres = @mysql_query("SELECT * FROM topics WHERE forumid=$forumid ORDER BY sticky, lastpost DESC LIMIT $page,$perpage") or sqlerr(__FILE__, __LINE__);

    $numtopics = mysql_num_rows($topicsres);
    
    $maypost = get_user_class() >= $thisforum["minclasscreate"];
    
    $newtopic = $maypost ? "<span class='fbtn'><a href='{$TBDEV['baseurl']}/forums.php?action=newtopic&amp;forumid=$forumid'>{$lang['forum_view_new_topic']}</a></span>" : "<span class='fbtn nocreate'>{$lang['forum_view_no_new_topic']}</span>";
    
    $jump = insert_quick_jump_menu($forumid);

    $buttons = "<div style='text-align:right;margin:10px 0px 10px 0px;'>
    <span class='fbtn'><a href='{$TBDEV['baseurl']}/forums.php?action=search'>{$lang['forum_view_search']}</a></span>
    <span class='fbtn'><a href='{$TBDEV['baseurl']}/forums.php?action=viewunread&amp;forumid=$forumid'>{$lang['forum_view_unread']}</a></span>
    {$newtopic}
    </div>";
    
    $fnav = "<div class='fnav'><a href='{$TBDEV['baseurl']}/forums.php'>{$lang['forums_title']}</a> -&gt; $forumname</div>\n";
    
    if( !$numtopics )
    {
      $HTMLOUT .=  "<div class='tb_table_outer_wrap'>{$fnav}$buttons
      <div class='tb_table_inner_wrap'>
      <span style='color:#ffffff;'>$forumname</span>
      </div>
      <table class='tb_table no_topics'>
      <tr class='row2'>
        <td class='noborder'>{$lang['forum_view_no_topics']}</td>
      </tr>
      </table>
      <div class='right'>{$jump}</div>
      </div>\n";

      //$HTMLOUT .= $jump;

      print stdhead("{$lang['forum_view_forum_title']}") . $HTMLOUT . stdfoot();
      exit();
    }

    //$buttons = "<div style='text-align:right;margin:5px 0px 5px 0px;'>blah</div>";
    //$HTMLOUT .=  $menu;

    $HTMLOUT .=  "<div class='tb_table_outer_wrap'>{$fnav}{$menu}{$buttons}
    <div class='tb_table_inner_wrap'>
    <span style='color:#ffffff;'>$forumname</span>
    </div>
    <table class='tb_table'>";

    $HTMLOUT .=  "
    <tr class='header'>
    <th class='col_c_icon'>&nbsp;</th>
    <th class='col_c_forum left'>{$lang['forum_view_topic']}</th>
    <th class='col_c_stats right'>{$lang['forum_view_replies']}</th>
    <th class='col_c_stats right'>{$lang['forum_view_views']}</th>
    <th class='col_c_post center'>{$lang['forum_view_author']}</th>
    <th class='col_c_post left'>{$lang['forum_view_lastpost']}</th>
    </tr>\n";

    while ($topicarr = mysql_fetch_assoc($topicsres))
    {
    $topicid = $topicarr["id"];

    $topic_userid = $topicarr["userid"];

    $topic_views = $topicarr["views"];

    $views = number_format($topic_views);

    $locked = $topicarr["locked"] == "yes";

    $sticky = $topicarr["sticky"] == "yes";

    //---- Get reply count

    $res = mysql_query("SELECT COUNT(*) FROM posts WHERE topicid=$topicid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_row($res);

    $posts = $arr[0];

    $replies = max(0, $posts - 1);

    $tpages = floor($posts / $postsperpage);

    if ($tpages * $postsperpage != $posts)
      ++$tpages;

    if ($tpages > 1)
    {
      $minimenu = pager( 
                  array( 
                  'count'  => $posts,
                  'perpage'    => $postsperpage,
                  'start_value'  => 0,
                  'mini' => 1,
                  'url'    => "forums.php?action=viewtopic&amp;topicid=$topicid")
                  );
    }
    else
      $minimenu = "";

    //---- Get userID and date of last post

    $res = mysql_query("SELECT * FROM posts WHERE topicid=$topicid ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_assoc($res);

    $lppostid = 0 + $arr["id"];

    //..rp..
    $lppostadd = $arr["added"];
    // ..rp..

    $lpuserid = 0 + $arr["userid"];

    $lpadded = "<span style='white-space: nowrap;'>" . get_date( $arr['added'],'') . "</span>";

    //------ Get name of last poster

    $res = mysql_query("SELECT * FROM users WHERE id=$lpuserid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) == 1)
    {
      $arr = mysql_fetch_assoc($res);

      $lpusername = "<a href='userdetails.php?id=$lpuserid'>{$arr['username']}</a>";
    }
    else
      $lpusername = sprintf($lang['forum_view_unknown'], $topic_userid);

    //------ Get author

    $res = mysql_query("SELECT username FROM users WHERE id=$topic_userid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) == 1)
    {
      $arr = mysql_fetch_assoc($res);

      $lpauthor = "<a href='userdetails.php?id=$topic_userid'>{$arr['username']}</a>";
    }
    else
      $lpauthor = sprintf($lang['forum_view_unknown'], $topic_userid);

    //---- Print row

    $r = mysql_query("SELECT lastpostread FROM readposts WHERE userid=$userid AND topicid=$topicid") or sqlerr(__FILE__, __LINE__);

    $a = mysql_fetch_row($r);

    $new = !$a || $lppostid > $a[0];

    // ..rp..
    $new = ($lppostadd > (TIME_NOW - $TBDEV['readpost_expiry'])) ? (!$a || $lppostid > $a[0]) : 0;
    //..rp..

    $topicpic = ($locked ? ($new ? "lockednew" : "locked") : ($new ? "unlockednew" : "unlocked"));

    $subject = ($sticky ? "{$lang['forum_view_sticky']}" : "") . "<a href='forums.php?action=viewtopic&amp;topicid=$topicid'><b>" .
    htmlsafechars($topicarr["subject"]) . "</b></a>$minimenu";

    $HTMLOUT .=  "
    <tr class='row1'>
      <td class='short altrow'>
      <img src='{$forum_pic_url}{$topicpic}.gif' alt='' title='' /></td>
      <td class='noborder'>$subject</td>
      <td class='altrow stats'>$replies</td>
      <td class='altrow stats'>$views</td>
      <td class='last_post center noborder'>$lpauthor</td>
      <td class='last_post noborder'>$lpadded<br />
      <strong>by</strong>&nbsp;$lpusername<br />
      <a href='forums.php?action=viewtopic&amp;topicid=$topicid&amp;page=p$lppostid#$lppostid'>Last Post</a></td>
    </tr>\n";
    } // while

    $HTMLOUT .=  "</table>
    {$menu}{$buttons}
    <div class='right'>{$jump}</div>
    </div>\n";

    //$HTMLOUT .=  $menu;

    $HTMLOUT .=  "<div>
    <img src=\"{$forum_pic_url}unlockednew.gif\" style='margin-right: 5px' alt='' title='' />{$lang['forum_view_new_posts']}
    <img src=\"{$forum_pic_url}locked.gif\" style='margin-left: 10px; margin-right: 5px' alt='' title='' />{$lang['forum_view_locked_topic']}
    </div>\n";

    
    print stdhead("{$lang['forum_view_forum_title']}") . $HTMLOUT . stdfoot();

    die;

?>