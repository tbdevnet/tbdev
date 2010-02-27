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
require_once "include/html_functions.php";
require_once "include/user_functions.php";
require_once ROOT_PATH."/cache/timezones.php";

dbconn(false);

loggedinorreturn();

    $lang = array_merge( load_language('global'), load_language('my') );
/*
$res = mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND location IN ('in', 'both')") or print(mysql_error());
$arr = mysql_fetch_row($res);
$messages = $arr[0];
$res = mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND location IN ('in', 'both') AND unread='yes'") or print(mysql_error());
$arr = mysql_fetch_row($res);
$unread = $arr[0];
$res = mysql_query("SELECT COUNT(*) FROM messages WHERE sender=" . $CURUSER["id"] . " AND location IN ('out', 'both')") or print(mysql_error());
$arr = mysql_fetch_row($res);
$outmessages = $arr[0];
*/


    $HTMLOUT = '';
    
    if (isset($_GET["edited"])) 
    {
      $HTMLOUT .= "<h1>{$lang['my_updated']}!</h1>\n";
      if (isset($_GET["mailsent"]))
        $HTMLOUT .= "<h2>{$lang['my_mail_sent']}!</h2>\n";
    }
    elseif (isset($_GET["emailch"]))
    {
      $HTMLOUT .= "<h1>{$lang['my_emailch']}!</h1>\n";
    }
    //else
      //print("<h1>Welcome, <a href=userdetails.php?id=$CURUSER[id]>$CURUSER[username]</a>!</h1>\n");
    $user_header = "<span style='font-size: 20px;'><a href='userdetails.php?id={$CURUSER['id']}'>{$CURUSER['username']}</a></span>";
    
    if(!empty($CURUSER['avatar']) && $CURUSER['av_w'] > 5 && $CURUSER['av_h'] > 5)
    {
      $avatar = "<img src='{$CURUSER['avatar']}' width='{$CURUSER['av_w']}' height='{$CURUSER['av_h']}' alt='' />";
    }
    else
    {
      $avatar = "<img src='{$TBDEV['pic_base_url']}forumicons/default_avatar.gif' alt='' />";
    }

    $HTMLOUT .= "<script type='text/javascript'>

    function daylight_show()
    {
      if ( document.getElementById( 'tz-checkdst' ).checked )
      {
        document.getElementById( 'tz-checkmanual' ).style.display = 'none';
      }
      else
      {
        document.getElementById( 'tz-checkmanual' ).style.display = 'block';
      }
    }

    </script>


    <table border='1' cellspacing='0' cellpadding='10' align='center'>
    <!--<tr>
    <td align='center' width='33%'><a href='logout.php'><b>{$lang['my_logout']}</b></a></td>
    <td align='center' width='33%'><a href='mytorrents.php'><b>{$lang['my_torrents']}</b></a></td>
    <td align='center' width='33%'><a href='friends.php'><b>{$lang['my_users_lists']}</b></a></td>
    </tr>-->
    <tr>
      <td valign='top'>
      $user_header<br />
      $avatar<br />
      <a href='mytorrents.php'>{$lang['my_edit_torrents']}</a><br />
      <a href='friends.php'>{$lang['my_edit_friends']}</a><br />
      <a href='users.php'>{$lang['my_search']}</a>
      </td>
    <td>
      <form method='post' action='takeprofedit.php'>
      <table border='1' cellspacing='0' cellpadding='5' width='100%'>";


    /***********************

    $res = mysql_query("SELECT COUNT(*) FROM ratings WHERE user=" . $CURUSER["id"]);
    $row = mysql_fetch_array($res,MYSQL_NUM);
    tr("Ratings submitted", $row[0]);

    $res = mysql_query("SELECT COUNT(*) FROM comments WHERE user=" . $CURUSER["id"]);
    $row = mysql_fetch_array($res,MYSQL_NUM);
    tr("Written comments", $row[0]);

    ****************/
    $stylesheets ='';
    $ss_r = mysql_query("SELECT * from stylesheets") or die;
    $ss_sa = array();
    while ($ss_a = mysql_fetch_assoc($ss_r))
    {
      $ss_id = $ss_a["id"];
      $ss_name = $ss_a["name"];
      $ss_sa[$ss_name] = $ss_id;
    }
    ksort($ss_sa);
    reset($ss_sa);
    while (list($ss_name, $ss_id) = each($ss_sa))
    {
      if ($ss_id == $CURUSER["stylesheet"])
      { 
        $ss = " selected='selected'";
      }
      else
      {
        $ss = "";
      }
      $stylesheets .= "<option value='$ss_id'$ss>$ss_name</option>\n";
    }

    $countries = "<option value='0'>---- {$lang['my_none']} ----</option>\n";
    $ct_r = mysql_query("SELECT id,name FROM countries ORDER BY name") or sqlerr(__FILE__,__LINE__);
    
    while ($ct_a = mysql_fetch_assoc($ct_r))
    {
      $countries .= "<option value='{$ct_a['id']}'" . ($CURUSER["country"] == $ct_a['id'] ? " selected='selected'" : "") . ">{$ct_a['name']}</option>\n";
    }
        //-----------------------------------------
        // Work out the timezone selection
        //-----------------------------------------
        $offset = ($CURUSER['time_offset'] != "") ? (string)$CURUSER['time_offset'] : (string)$TBDEV['time_offset'];
        
        $time_select = "<select name='user_timezone'>";
        
        //-----------------------------------------
        // Loop through the langauge time offsets and names to build our
        // HTML jump box.
        //-----------------------------------------
        
        foreach( $TZ as $off => $words )
        {
          if ( preg_match("/^time_(-?[\d\.]+)$/", $off, $match))
          {
            $time_select .= $match[1] == $offset ? "<option value='{$match[1]}' selected='selected'>$words</option>\n" : "<option value='{$match[1]}'>$words</option>\n";
          }
        }
        
        $time_select .= "</select>";
     
        //-----------------------------------------
        // DST IN USE?
        //-----------------------------------------
        
        if ($CURUSER['dst_in_use'])
        {
          $dst_check = 'checked="checked"';
        }
        else
        {
          $dst_check = '';
        }
        
        //-----------------------------------------
        // DST CORRECTION IN USE?
        //-----------------------------------------
        
        if ($CURUSER['auto_correct_dst'])
        {
          $dst_correction = 'checked="checked"';
        }
        else
        {
          $dst_correction = '';
        }
        
        
    $HTMLOUT .= tr($lang['my_accept_pm'],
    "<input type='radio' name='acceptpms'" . ($CURUSER["acceptpms"] == "yes" ? " checked='checked'" : "") . " value='yes' />{$lang['my_except_blocks']}
    <input type='radio' name='acceptpms'" .  ($CURUSER["acceptpms"] == "friends" ? " checked='checked'" : "") . " value='friends' />{$lang['my_only_friends']}
    <input type='radio' name='acceptpms'" .  ($CURUSER["acceptpms"] == "no" ? " checked='checked'" : "") . " value='no' />{$lang['my_only_staff']}"
    ,1);



    $HTMLOUT .= tr($lang['my_delete_pms'], "<input type='checkbox' name='deletepms'" . ($CURUSER["deletepms"] == "yes" ? " checked='checked'" : "") . " /> {$lang['my_default_delete']}",1);
    $HTMLOUT .= tr($lang['my_save_pms'], "<input type='checkbox' name='savepms'" . ($CURUSER["savepms"] == "yes" ? " checked='checked'" : "") . " /> {$lang['my_default_save']}",1);
    
    $categories = '';
    
    $r = mysql_query("SELECT id,name FROM categories ORDER BY name") or sqlerr();
    //$categories = "Default browsing categories:<br>\n";
    if (mysql_num_rows($r) > 0)
    {
      $categories .= "<table><tr>\n";
      $i = 0;
      while ($a = mysql_fetch_assoc($r))
      {
        $categories .=  ($i && $i % 2 == 0) ? "</tr><tr>" : "";
        $categories .= "<td class='bottom' style='padding-right: 5px'><input name='cat{$a['id']}' type='checkbox' " . (strpos($CURUSER['notifs'], "[cat{$a['id']}]") !== false ? " checked='checked'" : "") . " value='yes' />&nbsp;" . htmlspecialchars($a["name"]) . "</td>\n";
        ++$i;
      }
      $categories .= "</tr></table>\n";
    }

    $HTMLOUT .= tr($lang['my_email_notif'], "<input type='checkbox' name='pmnotif'" . (strpos($CURUSER['notifs'], "[pm]") !== false ? " checked='checked'" : "") . " value='yes' /> {$lang['my_notify_pm']}<br />\n" .
       "<input type='checkbox' name='emailnotif'" . (strpos($CURUSER['notifs'], "[email]") !== false ? " checked='checked'" : "") . " value='yes' /> {$lang['my_notify_torrent']}\n"
       , 1);
    $HTMLOUT .= tr($lang['my_browse'],$categories,1);
    $HTMLOUT .= tr($lang['my_stylesheet'], "<select name='stylesheet'>\n$stylesheets\n</select>",1);
    $HTMLOUT .= tr($lang['my_language'], "Engrish",1);
    $HTMLOUT .= tr($lang['my_country'], "<select name='country'>\n$countries\n</select>",1);

    // Timezone stuff //
    $HTMLOUT .= tr($lang['my_tz'], $time_select ,1);
    $HTMLOUT .= tr($lang['my_checkdst'], "<input type='checkbox' name='checkdst' id='tz-checkdst' onclick='daylight_show()' value='1' $dst_correction />&nbsp;{$lang['my_auto_dst']}<br />
    <div id='tz-checkmanual' style='display: none;'><input type='checkbox' name='manualdst' value='1' $dst_check />&nbsp;{$lang['my_is_dst']}</div>",1);
    // Timezone stuff end //

    $HTMLOUT .= tr($lang['my_avatar'], "<input name='avatar' size='50' value='" . htmlspecialchars($CURUSER["avatar"]) .
      "' /><br />\n{$lang['my_avatar_info']}",1);
    $HTMLOUT .= tr($lang['my_tor_perpage'], "<input type='text' size='10' name='torrentsperpage' value='$CURUSER[torrentsperpage]' /> {$lang['my_default']}",1);
    $HTMLOUT .= tr($lang['my_top_perpage'], "<input type='text' size='10' name='topicsperpage' value='$CURUSER[topicsperpage]' /> {$lang['my_default']}",1);
    $HTMLOUT .= tr($lang['my_post_perpage'], "<input type='text' size='10' name='postsperpage' value='$CURUSER[postsperpage]' /> {$lang['my_default']}",1);
    $HTMLOUT .= tr($lang['my_view_avatars'], "<input type='checkbox' name='avatars'" . ($CURUSER["avatars"] == "yes" ? " checked='checked'" : "") . " /> {$lang['my_low_bw']}",1);
    $HTMLOUT .= tr($lang['my_info'], "<textarea name='info' cols='50' rows='4'>" . htmlentities($CURUSER["info"], ENT_QUOTES) . "</textarea><br />{$lang['my_tags']}", 1);
    $HTMLOUT .= tr($lang['my_email'], "<input type='text' name='email' size='50' value='" . htmlspecialchars($CURUSER["email"]) . "' /><br />{$lang['my_email_pass']}<br /><input type='password' name='chmailpass' size='50' />", 1);
    $HTMLOUT .= "<tr><td colspan='2' align='left'>{$lang['my_note']}</td></tr>\n";
    $HTMLOUT .= tr($lang['my_chpass'], "<input type='password' name='chpassword' size='50' />", 1);
    $HTMLOUT .= tr($lang['my_pass_again'], "<input type='password' name='passagain' size='50' />", 1);

    function priv($name, $descr) {
      global $CURUSER;
      if ($CURUSER["privacy"] == $name)
        return "<input type='radio' name='privacy' value='$name' checked='checked' /> $descr";
      return "<input type='radio' name='privacy' value='$name' /> $descr";
    }

    /* tr("Privacy level",  priv("normal", "Normal") . " " . priv("low", "Low (email address will be shown)") . " " . priv("strong", "Strong (no info will be made available)"), 1); */


    $HTMLOUT .= "<tr><td colspan='2' align='center'>
      <input type='submit' value='{$lang['my_submit']}' class='btn' /> 
      <input type='reset' value='{$lang['my_revert']}' class='btn' />
      </td></tr>
      </table>
      </form>
    </td>
    </tr>
    </table>";

    /*
    if ($messages){
      print("<p>You have $messages message" . ($messages != 1 ? "s" : "") . " ($unread new) in your <a href='inbox.php'><b>inbox</b></a>,<br />\n");
      if ($outmessages)
        print("and $outmessages message" . ($outmessages != 1 ? "s" : "") . " in your <a href='inbox.php?out=1'><b>sentbox</b></a>.\n</p>");
      else
        print("and your <a href='inbox.php?out=1'>sentbox</a> is empty.</p>");
    }
    else
    {
      print("<p>Your <a href='inbox.php'>inbox</a> is empty, <br />\n");
      if ($outmessages)
        print("and you have $outmessages message" . ($outmessages != 1 ? "s" : "") . " in your <a href='inbox.php?out=1'><b>sentbox</b></a>.\n</p>");
      else
        print("and so is your <a href='inbox.php?out=1'>sentbox</a>.</p>");
    }
    */
    //print("<p><a href='users.php'><b>Find User/Browse User List</b></a></p>");
    
    
    print stdhead(htmlentities($CURUSER["username"], ENT_QUOTES) . "{$lang['my_stdhead']}", false) . $HTMLOUT . stdfoot();

?>