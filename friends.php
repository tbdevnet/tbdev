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
require_once "include/bittorrent.php";
require_once "include/user_functions.php";

dbconn(false);
loggedinorreturn();

    $lang = array_merge( load_language('global'), load_language('friends') );
    
    $userid = isset($_GET['id']) ? (int)$_GET['id'] : $CURUSER['id'];
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    //if (!$userid)
    //	$userid = $CURUSER['id'];

    if (!is_valid_id($userid))
      stderr($lang['friends_error'], $lang['friends_invalid_id']);

    if ($userid != $CURUSER["id"])
    stderr($lang['friends_error'], $lang['friends_no_access']);


    // action: add -------------------------------------------------------------

    if ($action == 'add')
    {
      $targetid = 0+$_GET['targetid'];
      $type = $_GET['type'];

      if (!is_valid_id($targetid))
        stderr($lang['friends_error'], $lang['friends_invalid_id']);

      if ($type == 'friend')
      {
        $table_is = $frag = 'friends';
        $field_is = 'friendid';
      }
      elseif ($type == 'block')
      {
        $table_is = $frag = 'blocks';
        $field_is = 'blockid';
      }
      else
       stderr($lang['friends_error'], $lang['friends_unknown']);

      $r = mysql_query("SELECT id FROM $table_is WHERE userid=$userid AND $field_is=$targetid") or sqlerr(__FILE__, __LINE__);
      if (mysql_num_rows($r) == 1)
       stderr($lang['friends_error'], sprintf($lang['friends_already'], htmlsafechars($table_is)));
       

      mysql_query("INSERT INTO $table_is VALUES (0,$userid, $targetid)") or sqlerr(__FILE__, __LINE__);
      header("Location: {$TBDEV['baseurl']}/friends.php?id=$userid#$frag");
      die;
    }

    // action: delete ----------------------------------------------------------

    if ($action == 'delete')
    {
      $targetid = (int)$_GET['targetid'];
      $sure = isset($_GET['sure']) ? htmlsafechars($_GET['sure']) : false;
      $type = isset($_GET['type']) ? ($_GET['type'] == 'friend' ? 'friend' : 'block') : stderr($lang['friends_error'], 'LoL');

      if (!is_valid_id($targetid))
      stderr($lang['friends_error'], $lang['friends_invalid_id']);

      if (!$sure)
        stderr("{$lang['friends_delete']} $type", sprintf($lang['friends_sure'], $type, $userid, $type, $targetid) );

      if ($type == 'friend')
      {
        mysql_query("DELETE FROM friends WHERE userid=$userid AND friendid=$targetid") or sqlerr(__FILE__, __LINE__);
        if (mysql_affected_rows() == 0)
         stderr($lang['friends_error'], $lang['friends_no_friend']);
        $frag = "friends";
      }
      elseif ($type == 'block')
      {
        mysql_query("DELETE FROM blocks WHERE userid=$userid AND blockid=$targetid") or sqlerr(__FILE__, __LINE__);
        if (mysql_affected_rows() == 0)
        stderr($lang['friends_error'], $lang['friends_no_block']);
        $frag = "blocks";
      }
      else
      stderr($lang['friends_error'], $lang['friends_unknown']);

      header("Location: {$TBDEV['baseurl']}/friends.php?id=$userid#$frag");
      die;
    }

    // main body  -----------------------------------------------------------------

    $res = mysql_query("SELECT * FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
    $user = mysql_fetch_assoc($res) or stderr($lang['friends_error'], $lang['friends_no_user']);
    //stderr("Error", "No user with ID.");
    
    $HTMLOUT = '';
    
    $donor = ($user["donor"] == "yes") ? "<img src='{$TBDEV['pic_base_url']}starbig.gif' alt='{$lang['friends_donor']}' style='margin-left: 4pt' />" : '';
    $warned = ($user["warned"] == "yes") ? "<img src='{$TBDEV['pic_base_url']}warnedbig.gif' alt='{$lang['friends_warned']}' style='margin-left: 4pt' />" : '';


    
/////////////////////// FRIENDS BLOCK ///////////////////////////////////////
    
    $res = mysql_query("SELECT f.friendid as id, u.username AS name, u.class, u.avatar, u.title, u.donor, u.warned, u.enabled, u.last_access FROM friends AS f LEFT JOIN users as u ON f.friendid = u.id WHERE userid=$userid ORDER BY name") or sqlerr(__FILE__, __LINE__);
    
    $count = mysql_num_rows($res);
    $friends = '';
    
    if( !$count)
    {
      $friends = "<em>{$lang['friends_friends_empty']}.</em>";
    }
    else
    {
      
      while ($friend = mysql_fetch_assoc($res))
      {
        $title = $friend["title"];
        if (!$title)
          $title = get_user_class_name($friend["class"]);
        
        $userlink = "<a href='userdetails.php?id={$friend['id']}'><b>".htmlsafechars($friend['name'])."</b></a>";
        $userlink .= get_user_icons($friend) . " ($title)<br />{$lang['friends_last_seen']} " . get_date( $friend['last_access'],'');
        
        $delete = "<span class='btn'><a href='friends.php?id=$userid&amp;action=delete&amp;type=friend&amp;targetid={$friend['id']}'>{$lang['friends_remove']}</a></span>";
          
        $pm = "&nbsp;<span class='btn'><a href='sendmessage.php?receiver={$friend['id']}'>{$lang['friends_pm']}</a></span>";
          
        $avatar = ($CURUSER["avatars"] == "yes" ? htmlsafechars($friend["avatar"]) : "");
        if (!$avatar)
          $avatar = "{$TBDEV['pic_base_url']}default_avatar.gif";
          
        $friends .= "<div style='border: 1px solid black;padding:5px;'>".($avatar ? "<img width='50px' src='$avatar' style='float:right;' alt='' />" : ""). "<p >{$userlink}<br /><br />{$delete}{$pm}</p></div><br />";
        
      }
      
    }
    
    //if ($i % 2 == 1)
      //$HTMLOUT .= "<td class='bottom' width='50%'>&nbsp;</td></tr></table>\n";
    //print($friends);
   // $HTMLOUT .= "</td></tr></table>\n";

    
/////////////////////// FRIENDS BLOCK END///////////////////////////////////////
    
 
       
//////////////////// ENEMIES BLOCK ////////////////////////////

    $res = mysql_query("SELECT b.blockid as id, u.username AS name, u.donor, u.warned, u.enabled, u.last_access FROM blocks AS b LEFT JOIN users as u ON b.blockid = u.id WHERE userid=$userid ORDER BY name") or sqlerr(__FILE__, __LINE__);
    
    $blocks = '';
    
    if(mysql_num_rows($res) == 0)
    {
      $blocks = "{$lang['friends_blocks_empty']}<em>.</em>";
    }
    else
    {
      //$i = 0;
      //$blocks = "<table width='100%' cellspacing='0' cellpadding='0'>";
      while ($block = mysql_fetch_assoc($res))
      {
        $blocks .= "<div style='border: 1px solid black;padding:5px;'>";
        $blocks .= "<span class='btn' style='float:right;'><a href='friends.php?id=$userid&amp;action=delete&amp;type=block&amp;targetid={$block['id']}'>{$lang['friends_delete']}</a></span><br />";
        $blocks .= "<p><a href='userdetails.php?id={$block['id']}'><b>" . htmlsafechars($block['name']) . "</b></a>";
        $blocks .= get_user_icons($block) . "</p></div><br />";
        
      }
      
    }
//////////////////// ENEMIES BLOCK END ////////////////////////////  

    $HTMLOUT .= "
                     <div class='cblock'>
                         <div class='cblock-header'>{$lang['friends_personal']} ".htmlsafechars($user['username'])."$donor$warned</div>
                         <div class='cblock-content'>";

    $HTMLOUT .= "<table class='main' width='739' border='0' cellspacing='0' cellpadding='0'>
    <tr>
      <td class='colhead'><h2  style='width:50%; text-align:left;'><a name='friends'>{$lang['friends_friends_list']}</a></h2></td>
      <td class='colhead'><h2  style='width:50%; text-align:left; vertical-align:top;'><a name='blocks'>{$lang['friends_blocks_list']}</a></h2></td>
    </tr>
    <tr>
      <td style='padding:10px;background:#ECE9D8;width:50%;'>$friends</td>
      <td style='padding:10px;background:#ECE9D8 vertical-align:top;'>$blocks</td>
    </tr>
    </table>";
    
    $HTMLOUT .= " <p><a href='users.php'><b>{$lang['friends_user_list']}</b></a></p>";

    $HTMLOUT .= "      </div>
                     </div>";
    
    print stdhead("{$lang['friends_stdhead']} {$user['username']}") . $HTMLOUT . stdfoot();
?>