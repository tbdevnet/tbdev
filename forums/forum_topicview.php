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
	print "{$lang['forum_topic_view_access']}";
	exit();
}


    //$lang = array_merge( $lang, load_language('forums') );

    $topicid = (int)$_GET["topicid"];

    $page = isset($_GET["page"]) ? (int)$_GET["page"] : 0;

    if (!is_valid_id($topicid))
      stderr("{$lang['forum_topic_view_user_error']}", "{$lang['forum_topic_view_incorrect']}");

    $userid = $CURUSER["id"];

    //------ Get topic info

    $res = mysql_query("SELECT * FROM topics t 
                        LEFT JOIN forums f ON t.forumid = f.id
                        WHERE t.id = $topicid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_assoc($res) or stderr("{$lang['forum_topic_view_forum_error']}", "{$lang['forum_topic_view_notfound']}");

    
    if ($CURUSER["class"] < $arr["minclassread"])
      stderr("{$lang['forum_topic_view_error']}", "{$lang['forum_topic_view_not_permitted']}");

    
    $locked = ($arr["locked"] == 'yes');
    $subject = htmlsafechars($arr["subject"]);
    $sticky = $arr["sticky"] == "yes";
    $forumid = intval($arr["forumid"]);
    $maypost = false;
    $forum = htmlsafechars($arr["name"]);
    $buttons = '';
    $fastrepbtn = '';
    $fastreply = '';
    
    //------ Update hits column

    @mysql_query("UPDATE topics SET views = views + 1 WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

    if ($locked && get_user_class() < UC_MODERATOR)
    {
      $buttons .= "<span class='fbtn nocreate'>{$lang['forum_topic_view_locked']}</span>\n";
    }
    else
    {
      if (get_user_class() < $arr["minclasswrite"])
      {
        $buttons .= "<span class='fbtn nocreate'>{$lang['forum_topic_view_permission']}</span>\n";
      }
      else
      {
        $maypost = true;
      }
    }

      //------ "View unread" / "Add reply" buttons

    $buttons .= "<span class='fbtn'><a href='{$TBDEV['baseurl']}/forums.php?action=viewunread&amp;forumid=$forumid'>{$lang['forum_topic_view_unread']}</a> </span>\n";

    if ($maypost)
    {
      $buttons .= "<span class='fbtn'><a href='{$TBDEV['baseurl']}/forums.php?action=reply&amp;topicid={$topicid}'>Add Reply</a></span>\n";
    
    $fastrepbtn = "<span class='fbtn'><a href='#' onclick=\"showhide('fastreply'); return(false);\">Fast Reply</a></span>\n";
    
    $fastreply = insert_fastreply(array('forumid' => 0, 'topicid' => $topicid));
    }
    //------ Forum quick jump drop-down
    $jump = insert_quick_jump_menu($forumid);
    
    //------ Get post count
////////////////////////// pager section ////////////////////////////////////
    $res = @mysql_query("SELECT COUNT(*) FROM posts WHERE topicid=$topicid") or sqlerr(__FILE__, __LINE__);

    $cnt = mysql_fetch_row($res);

    $postcount = $cnt[0];

    $perpage = $postsperpage;

    $pages = ceil($postcount / $perpage);

    if ($page[0] == "p")
  	{
	    $findpost = substr($page, 1);
	    $res = @mysql_query("SELECT id FROM posts WHERE topicid=$topicid ORDER BY added") or sqlerr(__FILE__, __LINE__);
	    $i = 1;
	    while ($arr = mysql_fetch_row($res))
	    {
	      if ($arr[0] == $findpost)
	        break;
	      ++$i;
	    }
	    $page = ceil($i / $perpage);
	  }
/*
    if ($page == "last")
      $page = $pages;
    else
    {
      if($page < 1)
        $page = 1;
      elseif ($page > $pages)
        $page = $pages;
    }
*/
    require_once "include/pager.php";
                  $menu = pager( 
                  array( 
                  'count'  => $postcount,
                  'perpage'    => $perpage,
                  'start_value'  => $page,
                  'url'    => "forums.php?action=viewtopic&amp;topicid=$topicid")
                  );
/////////////////////////// END PAGER SECTION UGLY ///////////////////////////////

    //------ Get posts

    $res = @mysql_query("SELECT p. * , u.username, u.ip, u.class, u.avatar, u.av_w, u.av_h, u.donor, u.title, u.enabled, u.warned, u.reputation, ue.username AS uname_edit, ue.id AS uname_editid FROM posts p LEFT JOIN users u ON u.id = p.userid LEFT JOIN users ue ON p.editedby = ue.id WHERE topicid = $topicid ORDER BY p.id LIMIT $page,$perpage") or sqlerr(__FILE__, __LINE__);

    
    
    $HTMLOUT = '';
    //$fnav = "<div class='fnav'><a href='{$TBDEV['baseurl']}/forums.php'>{$lang['forums_title']}</a> -&gt; $forumname</div>\n";
	
    $fnav = "<div class='fnav'>
    <a name='top'></a>
    <a href='{$TBDEV['baseurl']}/forums.php'>{$lang['forums_title']}</a> -&gt;
    <a href='forums.php?action=viewforum&amp;forumid=$forumid'>$forum</a> -&gt; $subject</div>\n";

    //$HTMLOUT .= $pagemenu;

    //------ Print table

    //$HTMLOUT .= begin_main_frame();

    //begin_frame();

    $pc = mysql_num_rows($res);

    $pn = 0;

    $r = @mysql_query("SELECT lastpostread FROM readposts WHERE userid=" . $CURUSER["id"] . " AND topicid=$topicid") or sqlerr(__FILE__, __LINE__);

    $a = mysql_fetch_row($r);

    $lpr = $a[0];

    $HTMLOUT .= "<div class='tb_table_outer_wrap'>{$fnav}{$menu}
        <div style='text-align:right;margin:10px 0px 10px 0px;'>$buttons</div>
        <div class='tb_table_inner_wrap'>
        <span style='color:#ffffff;'>$subject</span>
        </div>";

    while ($arr = mysql_fetch_assoc($res))
    {
        ++$pn;

        $postid = $arr["id"];

        $posterid = $arr["userid"];

        $added = get_date( $arr['added'],'');

        //---- Get poster details

        //$res2 = mysql_query("SELECT username, class, avatar, av_w, av_h, donor, title, enabled, warned FROM users WHERE id=$posterid") or sqlerr(__FILE__, __LINE__);

        //$arr2 = mysql_fetch_assoc($res2);

        $postername = htmlsafechars( $arr["username"] );

        if ($postername == "")
        {
          $by = sprintf($lang['forum_topic_view_unknown'], $posterid);

          //$avatar = "";
        }
        else
        {
  //		if ($arr2["enabled"] == "yes")
            //$avatar = ($CURUSER["avatars"] == "yes" ? htmlsafechars($arr2["avatar"]) : "");
  //	    else
  //			$avatar = "{$TBDEV['pic_base_url']}disabled_avatar.gif";

          $title = htmlsafechars( $arr["title"] );

          if (!$title)
            $title = get_user_class_name($arr["class"]);

          $by = "<a href='userdetails.php?id=$posterid'><strong>$postername</strong></a>" 
          . ($arr["donor"] == "yes" ? "<img src='{$TBDEV['pic_base_url']}star.gif' alt='{$lang['forum_topic_view_donor']}' />" : "") 
          . ($arr["enabled"] == "no" ? "<img src='{$TBDEV['pic_base_url']}disabled.gif' alt='{$lang['forum_topic_view_disabled']}' style='margin-left: 2px' />" : ($arr["warned"] == "yes" ? "<a href='rules.php#warning' class='altlink'><img src='{$TBDEV['pic_base_url']}warned.gif' alt='{$lang['forum_topic_view_warned']}' border='0' /></a>" : "")) . "&nbsp;".( $CURUSER['class'] >= UC_MODERATOR ? htmlsafechars($arr['ip']) : '' );
        }

        if ( ($CURUSER["avatars"] == "yes") AND !empty($arr['avatar']) )
        {
          $avatar = "<img width='{$arr['av_w']}' height='{$arr['av_h']}' src='".htmlsafechars($arr['avatar'])."' alt='' />";
        }
        else
        {
          $avatar = "<img width='100' src='{$forum_pic_url}default_avatar.gif' alt='' />";
        }
        
        $HTMLOUT .= "<a name='$postid'></a>\n";

        if ($pn == $pc)
        {
          $HTMLOUT .= "<a name='last'></a>\n";
          //..rp..
  /* if ($postid > $lpr)
  mysql_query("UPDATE readposts SET lastpostread=$postid WHERE userid=$userid AND topicid=$topicid") or sqlerr(__FILE__, __LINE__);
  */
  //..rp..
        }

        $HTMLOUT .= "
        <div class='post_wrap'>
        <div class='post_head'>
        <span style='float:left;'>Posted by $by</span>
        <span>Post&nbsp;#$postid<a href='#top'><img src='{$forum_pic_url}top.gif' border='0' alt='{$lang['forum_topic_view_top']}' /></a></span>
        </div>\n";

        $quotebtn = '';
        $editbtn = '';
        $deletebtn = '';
        
        if (!$locked || get_user_class() >= UC_MODERATOR)
          $quotebtn = "<span class='user_control_fbtn'><a href='forums.php?action=quotepost&amp;topicid=$topicid&amp;postid=$postid&amp;forumid=$forumid'><b>{$lang['forum_topic_view_quote']}</b></a></span>";

        if (($CURUSER["id"] == $posterid && !$locked) || get_user_class() >= UC_MODERATOR)
          $editbtn = "<span class='user_control_fbtn'><a href='forums.php?action=editpost&amp;postid=$postid&amp;forumid=$forumid'><b>{$lang['forum_topic_view_edit']}</b></a></span>";

        if (get_user_class() >= UC_MODERATOR)
          $deletebtn = "<span class='user_control_fbtn'><a href='forums.php?action=deletepost&amp;postid=$postid&amp;forumid=$forumid'><b>{$lang['forum_topic_view_delete']}</b></a></span>";
          
        $body = wordwrap( format_comment($arr["body"]), 80, "\n", true);
//////////// not needed, editedby pulled from top query ////////
/*
        if (is_valid_id($arr['editedby']))
        {
          $res2 = mysql_query("SELECT username FROM users WHERE id={$arr['editedby']}");
          if (mysql_num_rows($res2) == 1)
          {
            $arr2 = mysql_fetch_assoc($res2);
            $body .= "<br /><span class='fedited_by'>{$lang['forum_topic_view_edit_by']}<a href='userdetails.php?id={$arr['editedby']}'><strong>{$arr2['username']}</strong></a> on ".get_date( $arr['editedat'],'')."</span>\n";
          }
        }
*/
        if (is_valid_id($arr['uname_editid']))
        {
          $body .= "<div class='fedited_by'>{$lang['forum_topic_view_edit_by']}<a href='userdetails.php?id={$arr['uname_editid']}'><strong>{$arr['uname_edit']}</strong></a> on ".get_date( $arr['editedat'],'')."</div>\n";
        }
        
        $member_reputation = $arr['username'] != '' ? get_reputation($arr) : '';
        
        $HTMLOUT .= "<div class='author_info'>
        <ul>
          <li class='avatar'>$avatar</li>
          <li class='title'>$title</li>
          <li class='info_rep'>$member_reputation</li>
        </ul>
        </div>
        <div class='post_body'>
        <div class='post_time'>Posted $added</div>
        {$body}
        </div>\n";

        //$HTMLOUT .= end_table();
      
        $postadd = $arr['added'];
      //..rp..
      if (($postid > $lpr) AND ($postadd > (TIME_NOW - $TBDEV['readpost_expiry']))) 
      {
        if ($lpr)
        {
          @mysql_query("UPDATE readposts SET lastpostread=$postid WHERE userid=$userid AND topicid=$topicid") or sqlerr(__FILE__, __LINE__);
        }
        else
        {
          @mysql_query("INSERT INTO readposts (userid, topicid, lastpostread) VALUES($userid, $topicid, $postid)") or sqlerr(__FILE__, __LINE__);
        }
      
      }
      $HTMLOUT .= "<div class='post_footer'><span>{$quotebtn}{$editbtn}{$deletebtn}</span></div>
      </div>";
    }


      $HTMLOUT .= "{$menu}<div style='text-align:right;margin:10px 0px 10px 0px;'>{$buttons}{$fastrepbtn}</div>{$fastreply}</div>";

      //$HTMLOUT .= $pagemenu;

      
      //$HTMLOUT .= "</tr></table>\n";
      
      //------ Mod options

      if (get_user_class() >= UC_MODERATOR)
      {
        require_once ROOT_PATH.'/forums/forum_mod_functions.php';
        $HTMLOUT .= forum_mod_panel( $forumid, $topicid, $subject, $sticky, $locked );
      }

    
    $js = "<script type='text/javascript' src='./scripts/popup.js'></script>
    <script type='text/javascript' src='{$TBDEV['baseurl']}/scripts/show_hide.js'></script>";
    
    print stdhead($lang['forum_topic_view_view_topic'], $js) . $HTMLOUT . stdfoot();

    die;
?>