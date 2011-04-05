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
require_once "include/bittorrent.php";
require_once "include/user_functions.php";
require_once "include/html_functions.php";

$action = $_GET["action"];

dbconn(false);


loggedinorreturn();

    $lang = array_merge( load_language('global'), load_language('comment') );
    
    if ($action == "add")
    {
      if ($_SERVER["REQUEST_METHOD"] == "POST")
      {
        $torrentid = 0 + $_POST["tid"];
        if (!is_valid_id($torrentid))
          stderr("{$lang['comment_error']}", "{$lang['comment_invalid_id']}");

        $res = @mysql_query("SELECT name FROM torrents WHERE id = $torrentid") or sqlerr(__FILE__,__LINE__);
        $arr = mysql_fetch_array($res,MYSQL_NUM);
        if (!$arr)
          stderr("{$lang['comment_error']}", "{$lang['comment_invalid_torrent']}");

        $text = trim($_POST["body"]);
        if (!$text)
          stderr("{$lang['comment_error']}", "{$lang['comment_body']}");

        @mysql_query("INSERT INTO comments (user, torrent, added, text, ori_text) VALUES (" .
            $CURUSER["id"] . ",$torrentid, " . TIME_NOW . ", " . sqlesc($text) .
             "," . sqlesc($text) . ")");

        $newid = mysql_insert_id();

        @mysql_query("UPDATE torrents SET comments = comments + 1 WHERE id = $torrentid");

        header("Refresh: 0; url=details.php?id=$torrentid&viewcomm=$newid#comm$newid");
        die;
      }

      $torrentid = 0 + $_GET["tid"];
      if (!is_valid_id($torrentid))
        stderr("{$lang['comment_error']}", "{$lang['comment_invalid_id']}");

      $res = mysql_query("SELECT name FROM torrents WHERE id = $torrentid") or sqlerr(__FILE__,__LINE__);
      $arr = mysql_fetch_assoc($res);
      if (!$arr)
        stderr("{$lang['comment_error']}", "{$lang['comment_invalid_torrent']}");
      
      $HTMLOUT = '';
      $js = "<script type='text/javascript' src='scripts/bbcode2text.js'></script>";
      
      $HTMLOUT .= "<div class='cblock'>
                    <div class='cblock-header'>{$lang['comment_add']}\"" . htmlsafechars($arr["name"]) . "\"</div>
                    <div class='cblock-content'>
                    <form name='bbcode2text' method='post' action='comment.php?action=add'>
                    <input type='hidden' name='tid' value='{$torrentid}'/>";
      $HTMLOUT .=   bbcode2textarea(  );
      $HTMLOUT .= " <div align='center'>
                    <input type='submit' name='comment' value='{$lang['comment_doit']}' class='' />
                    </div>
                    </form>
                    </div>
                    </div>";




      $res = mysql_query("SELECT comments.id, text, comments.added, comments.editedby, comments.editedat, username, users.id as user, users.title, users.avatar, users.av_w, users.av_h, users.class, users.donor, users.warned FROM comments LEFT JOIN users ON comments.user = users.id WHERE torrent = $torrentid ORDER BY comments.id DESC LIMIT 5");

      $allrows = array();
      while ($row = mysql_fetch_assoc($res))
        $allrows[] = $row;

      if (count($allrows)) {
              require_once "include/comment_functions.php";
              //require_once "include/html_functions.php";
              require_once "include/bbcode_functions.php";
          $HTMLOUT .= "<h2>{$lang['comment_recent']}</h2>\n";
          $HTMLOUT .= commenttable($allrows);
        }

      print stdhead("{$lang['comment_add']}\"{$arr["name"]}\"", $js) . $HTMLOUT . stdfoot();
      die;
    }
    elseif ($action == "edit")
    {
      $commentid = 0 + $_GET["cid"];
      if (!is_valid_id($commentid))
        stderr("{$lang['comment_error']}", "{$lang['comment_invalid_id']}");

      $res = mysql_query("SELECT c.*, t.name FROM comments AS c LEFT JOIN torrents AS t ON c.torrent = t.id WHERE c.id=$commentid") or sqlerr(__FILE__,__LINE__);
      $arr = mysql_fetch_assoc($res);
      if (!$arr)
        stderr("{$lang['comment_error']}", "{$lang['comment_invalid_id']}.");

      if ($arr["user"] != $CURUSER["id"] && get_user_class() < UC_MODERATOR)
        stderr("{$lang['comment_error']}", "{$lang['comment_denied']}");

      if ($_SERVER["REQUEST_METHOD"] == "POST")
      {
        $text = $_POST['body'];
        $returnto = htmlsafechars($_POST["returnto"]);

        if ($text == "")
          stderr("{$lang['comment_error']}", "{$lang['comment_body']}");

        $text = sqlesc($text);

        $editedat = TIME_NOW;

        mysql_query("UPDATE comments SET text=$text, editedat=$editedat, editedby={$CURUSER['id']} WHERE id=$commentid") or sqlerr(__FILE__, __LINE__);

        if ($returnto)
          header("Location: $returnto");
        else
          header("Location: {$TBDEV['baseurl']}/");      // change later ----------------------
        die;
      }
      
      $returnto = htmlsafechars($_SERVER["HTTP_REFERER"]);
      $js = "<script type='text/javascript' src='scripts/bbcode2text.js'></script>";
      $HTMLOUT = '';

      $HTMLOUT .= "
                     <div class='cblock'>
                         <div class='cblock-header'>{$lang['comment_edit']}\"" . htmlsafechars($arr["name"]) . "\"</div>
                         <div class='cblock-content'>
                             <form name='bbcode2text' method='post' action='comment.php?action=edit&amp;cid=$commentid'>
                                  <input type='hidden' name='returnto' value='{$returnto}' />
                                  <input type='hidden' name='cid' value='$commentid' />";
      $HTMLOUT .=                 bbcode2textarea( 'body', htmlsafechars($arr["text"]) );
      $HTMLOUT .= "       <div align='center'>
                          <input type='submit' name='comment' value='{$lang['comment_doit']}' class='' />
                          </div>
                          </form>
                         </div>
                     </div>";

      print stdhead("{$lang['comment_edit']}\"{$arr["name"]}\"", $js) . $HTMLOUT . stdfoot();
      die;
    }
    elseif ($action == "delete")
    {
      if (get_user_class() < UC_MODERATOR)
        stderr("{$lang['comment_error']}", "{$lang['comment_denied']}");

      $commentid = 0 + $_GET["cid"];

      if (!is_valid_id($commentid))
        stderr("{$lang['comment_error']}", "{$lang['comment_invalid_id']}");

      $sure = isset($_GET["sure"]) ? (int)$_GET["sure"] : false;

      if (!$sure)
      {
        $referer = $_SERVER["HTTP_REFERER"];
        stderr("{$lang['comment_delete']}", "{$lang['comment_about_delete']}\n" .
          "<a href='comment.php?action=delete&amp;cid=$commentid&amp;sure=1" .
          ($referer ? "&amp;returnto=" . urlencode($referer) : "") .
          "'>here</a> {$lang['comment_delete_sure']}");
      }


      $res = mysql_query("SELECT torrent FROM comments WHERE id=$commentid")  or sqlerr(__FILE__,__LINE__);
      $arr = mysql_fetch_assoc($res);
      if ($arr)
        $torrentid = $arr["torrent"];

      @mysql_query("DELETE FROM comments WHERE id=$commentid") or sqlerr(__FILE__,__LINE__);
      if ($torrentid && mysql_affected_rows() > 0)
        mysql_query("UPDATE torrents SET comments = comments - 1 WHERE id = $torrentid");

      $returnto = $_GET["returnto"];

      if ($returnto)
        header("Location: $returnto");
      else
        header("Location: {$TBDEV['baseurl']}/");      // change later ----------------------
      die;
    }
    elseif ($action == "vieworiginal")
    {
      if (get_user_class() < UC_MODERATOR)
        stderr("{$lang['comment_error']}", "{$lang['comment_denied']}");

      $commentid = 0 + $_GET["cid"];

      if (!is_valid_id($commentid))
        stderr("{$lang['comment_error']}", "{$lang['comment_invalid_id']}");

      $res = mysql_query("SELECT c.*, t.name FROM comments AS c LEFT JOIN torrents AS t ON c.torrent = t.id WHERE c.id=$commentid") or sqlerr(__FILE__,__LINE__);
      $arr = mysql_fetch_assoc($res);
      if (!$arr)
        stderr("{$lang['comment_error']}", "{$lang['comment_invalid_id']} $commentid.");

      
      $HTMLOUT = '';

      $HTMLOUT .= "
                     <div class='cblock'>
                         <div class='cblock-header'>{$lang['comment_original_contents']}#$commentid</div>
                         <div class='cblock-content'>
                             <table width='500' border='1' cellspacing='0' cellpadding='5'>
                                   <tr>
                                      <td class='comment'>".htmlsafechars($arr["ori_text"])."</td>
                                   </tr>
                             </table><br />
                         </div>
                     </div>";

      $returnto = htmlsafechars(filter_var($_SERVER['HTTP_REFERER'], FILTER_SANITIZE_STRING));

    //	$returnto = "details.php?id=$torrentid&amp;viewcomm=$commentid#$commentid";

      if ($returnto)
        $HTMLOUT .= "<span class='btn'><a href='$returnto'>{$lang['comment_back']}</a></span>\n";

      print stdhead("{$lang['comment_original']}") . $HTMLOUT . stdfoot();
      die;
    }
    else
      stderr("{$lang['comment_error']}", "{$lang['comment_unknown']}");

    die;
?>