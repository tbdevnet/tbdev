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
if ( ! defined( 'IN_TBDEV_FORUM' ) )
{
	print "{$lang['forum_mod_options_access']}";
	exit();
}


function forum_mod_panel( $forumid, $topicid, $subject, $sticky, $locked ) {

  global $lang;
  
  //attach_frame();
  $req_uri = htmlsafechars($_SERVER['PHP_SELF']);

  $res = mysql_query("SELECT id,name,minclasswrite FROM forums ORDER BY name") or sqlerr(__FILE__, __LINE__);

  $htmlout = "<div class='mod_option_box'>
  <table class='mod_option'>
    <tr>
      <td align='right'>{$lang['forum_topic_view_sticky']}</td>
      <td>
        <form method='post' action='forums.php?action=setsticky'>
        <input type='hidden' name='topicid' value='$topicid' />
        <input type='hidden' name='returnto' value='{$req_uri}' />
        <input type='radio' name='sticky' value='yes' " . ($sticky ? " checked='checked'" : "") . " /> {$lang['forum_topic_view_yes']} <input type='radio' name='sticky' value='no' " . (!$sticky ? " checked='checked'" : "") . " /> {$lang['forum_topic_view_no']}
        <input type='submit' value='{$lang['forum_topic_view_set']}' />
        </form>
      </td>
    </tr>
    <tr>
      <td align='right'>{$lang['forum_topic_view_set_locked']}</td>
      <td >
        <form method='post' action='forums.php?action=setlocked'>
        <input type='hidden' name='topicid' value='$topicid' />
        <input type='hidden' name='returnto' value='{$req_uri}' />
        <input type='radio' name='locked' value='yes' " . ($locked ? " checked='checked'" : "") . " /> {$lang['forum_topic_view_yes']} <input type='radio' name='locked' value='no' " . (!$locked ? " checked='checked'" : "") . " /> {$lang['forum_topic_view_no']}
        <input type='submit' value='{$lang['forum_topic_view_set']}' /></form>
      </td>
    </tr>
    <tr>
      <td align='right'>{$lang['forum_topic_view_rename']}</td>
      <td >
        <form method='post' action='forums.php?action=renametopic'>
        <input type='hidden' name='topicid' value='$topicid' />
        <input type='hidden' name='returnto' value='{$req_uri}' />
        <input type='text' name='subject' size='60' value='" . htmlsafechars($subject) . "' />
        <input type='submit' value='{$lang['forum_topic_view_okay']}' />
        </form>
      </td>
    </tr>
    <tr>
      <td align='right'>Move this thread to:</td>
      <td>
        <form method='post' action='forums.php?action=movetopic'>
        <input type='hidden' name='topicid' value='$topicid' />
        &nbsp;<select name='forumid'>
        <option value='0'>-- Move Topic --</option>\n";

  while ($arr = mysql_fetch_assoc($res))
    if ($arr["id"] != $forumid && get_user_class() >= $arr["minclasswrite"])
      $htmlout .= "<option value='{$arr["id"]}'>{$arr["name"]}</option>\n";

  $htmlout .= "</select> 
        <input type='submit' value='{$lang['forum_topic_view_okay']}' />
        </form>
      </td>
    </tr>
    <tr>
      <td align='right'>{$lang['forum_topic_view_delete_topic']}</td>
      <td>
        <form method='post' action='forums.php?action=deletetopic'>
        <input type='hidden' name='action' value='deletetopic' />
        <input type='hidden' name='topicid' value='$topicid' />
        <input type='hidden' name='forumid' value='$forumid' />
        <input type='checkbox' name='sure' value='1' />I'm sure
        <input type='submit' value='{$lang['forum_topic_view_okay']}' />
        </form>
      </td>
    </tr>
  </table>
  </div>\n";
  
  return $htmlout;
}
?>