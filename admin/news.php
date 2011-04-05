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

if ( ! defined( 'IN_TBDEV_ADMIN' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly.";
	exit();
}

//require_once "include/bittorrent.php";
require_once "include/user_functions.php";
require_once "include/bbcode_functions.php";
require_once "include/html_functions.php";

    $lang = array_merge( $lang, load_language('ad_news') );
    
    $input = array_merge( $_GET, $_POST);

    $mode = isset($input['mode']) ? $input['mode'] : '';

    $warning = '';
    
    $HTMLOUT = '';
    
        // Update NEws dates to rejuvenate /////////////////////////////

    if('update' == $mode)
    {
      if(isset($input['news_update']) && count($input['news_update']))
      {
        foreach($input['news_update'] as $v)
        {
          if(!is_valid_id($v)) stderr("Error", "No news ID");
          $newsIDS[] = $v;
        }
      }
      else
      {
        stderr("Error", "No data!");
      }
      
      $news = join(',', $newsIDS);
      
      @mysql_query("UPDATE news set added = ".TIME_NOW." WHERE id IN ($news)");
      
      if(-1 == mysql_affected_rows())
        stderr("Error", "Update failed");
      
      header("Location: {$TBDEV['baseurl']}/admin.php?action=news");
      
    }
	
    
    //   Delete News Item    //////////////////////////////////////////////////////
    if ($mode == 'delete')
    {
      $newsid = isset($input['newsid']) ? (int)$input["newsid"] : 0;
      
      if (!is_valid_id($newsid))
        stderr($lang['news_error'],sprintf($lang['news_gen_error'],1));

      $returnto = isset($input['returno']) ? htmlsafechars($input["returnto"]) : '';

      $sure = isset($input["sure"]) ? (int)$input['sure'] : 0;
      
      if (!$sure)
      {
        stderr($lang['news_delete_notice'],sprintf($lang['news_delete_text'],$newsid));
      }
      
      @mysql_query("DELETE FROM news WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

      if ($returnto != "")
        header("Location: {$TBDEV['baseurl']}/admin.php?action=news");
      else
        $warning = $lang['news_delete_ok'];
    }


    //   Add News Item    /////////////////////////////////////////////////////////
    if ($mode == 'add')
    {

      $body = isset($input["body"]) ? (string)$input["body"] : 0;
      
      if ( !$body OR strlen($body) < 4 )
        stderr($lang['news_error'],$lang['news_add_body']);
      
      $body = sqlesc($body);
      
      $added = isset($input['added']) ? $input['added'] : TIME_NOW;
      
      $headline = (isset($input['subject']) AND !empty($input['subject'])) ? sqlesc($input['subject']) : sqlesc('TBDev.net News');
      
      @mysql_query("INSERT INTO news (userid, added, body, headline) VALUES ({$CURUSER['id']}, $added, $body, $headline)") or sqlerr(__FILE__, __LINE__);
        
      if (mysql_affected_rows() == 1)
        $warning = $lang['news_add_ok'];
      else
        stderr($lang['news_error'],$lang['news_add_err']);
    }

    
    //   Edit News Item    ////////////////////////////////////////////////////////
    if ($mode == 'edit')
    {

      $newsid = isset($input["newsid"]) ? (int)$input["newsid"] : 0;

      if (!is_valid_id($newsid))
        stderr($lang['news_error'], sprintf($lang['news_gen_error'],2));

      $res = @mysql_query("SELECT * FROM news WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

      if (mysql_num_rows($res) != 1)
        stderr($lang['news_error'], $lang['news_edit_nonewsid']);

      $arr = mysql_fetch_assoc($res);

      if ($_SERVER['REQUEST_METHOD'] == 'POST')
      {
        $body = isset($_POST['body']) ? $_POST['body'] : '';

        if ($body == "" OR strlen($_POST['body']) < 4)
          stderr($lang['news_error'], $lang['news_add_body']);

        $headline = (isset($input['subject']) AND !empty($input['subject'])) ? sqlesc($input['subject']) : sqlesc('TBDev.net News');
        
        $body = sqlesc($body);

        $editedat = TIME_NOW;

        @mysql_query("UPDATE news SET body=$body, headline=$headline WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

        $returnto = isset($_POST['returnto']) ? htmlsafechars($_POST['returnto']) : '';

        if ($returnto != "")
          header("Location: {$TBDEV['baseurl']}/index.php");
        else
          $warning = $lang['news_edit_ok'];;
      }
      else
      {
        //$returnto = isset($_POST['returnto']) ? htmlsafechars($_POST['returnto']) : $TBDEV['baseurl'].'/news.php';
        $js = "<script type='text/javascript' src='scripts/bbcode2text.js'></script>";
        $title = htmlsafechars($arr['headline']);
    $HTMLOUT .= "
                 <div class='cblock'>
                 <div class='cblock-header'>{$lang['news_edit_title']}</div>
                 <div class='cblock-lb'></div>
                 <div class='cblock-content'>

        <form id='bbcode2text' method='post' action='admin.php?action=news'>
        
        <input type='hidden' name='newsid' value='$newsid' />
        
        <input type='hidden' name='mode' value='edit' />
        <div align='center'>
           <input style='width:615px;' type='text' name='subject' size='50' value='{$title}' />
        </div>";
        
        $HTMLOUT .= bbcode2textarea( 'body', $arr['body'] );
          
        
        $HTMLOUT .= "<div align='center'>
                <input type='submit' name='newsedit' value='Edit' class='' />
             </div></form>
      </div></div>";

      print  stdhead($lang['news_edit_title'], $js) . $HTMLOUT . stdfoot();
        exit();
      }
    }

    
    
    //   Other Actions and followup    ////////////////////////////////////////////
    $HTMLOUT .= "
                 <div class='cblock'>
                     <div class='cblock-header'>{$lang['news_submit_title']}</div>";
    $HTMLOUT .= "    <div class='cblock-lb'>";
    if (!empty($warning))
    {
      $HTMLOUT .= "<p style='font-size:-3px;'>($warning)</p>";
    }
    $HTMLOUT .= "    </div>
                     <div class='cblock-content'>";

    $HTMLOUT .= "<form id='bbcode2text' method='post' action='admin.php?action=news'>
                      <input type='hidden' name='mode' value='add' />";
    
    $js = "<script type='text/javascript' src='scripts/bbcode2text.js'></script>";
        
    $HTMLOUT .= "<div align='center'>
                <input style='width:615px;' type='text' name='subject' size='50' value='' />
             </div>";
    $HTMLOUT .= bbcode2textarea( 'body' );
    $HTMLOUT .= "<div align='center'>
                <input type='submit' name='postquickreply' value='Add' class='' />
             </div>
    </form><br /><br />";

    $res = @mysql_query("SELECT * FROM news ORDER BY added DESC") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) > 0)
    {
      $HTMLOUT .= begin_main_frame();
      $HTMLOUT .= "<form method='post' action='admin.php?action=news'>
      <input type='hidden' name='mode' value='update' />";

      while ($arr = mysql_fetch_assoc($res))
      {
        $newsid = $arr["id"];
        $body = format_comment($arr["body"]);
        $headline = htmlsafechars($arr['headline']);
        $userid = $arr["userid"];
        $added = get_date( $arr['added'],'');

        $res2 = @mysql_query("SELECT username, donor FROM users WHERE id = $userid") or sqlerr(__FILE__, __LINE__);
        $arr2 = mysql_fetch_assoc($res2);

        $postername = $arr2["username"];

        if ($postername == "")
          $by = "unknown[$userid]";
        else
          $by = "<a href='userdetails.php?id=$userid'><b>$postername</b></a>" .
            ($arr2["donor"] == "yes" ? "<img src=\"{$TBDEV['pic_base_url']}star.gif\" alt='Donor' />" : "");
            
        $HTMLOUT .= begin_frame();    
        $HTMLOUT .= begin_table(true);
        $HTMLOUT .= "
        <tr>
          <td class='colhead'>$headline<span style='float:right;'><input type='checkbox' name='news_update[]' value='$newsid' /></span></td>
        </tr>
        <tr>
          <td>{$added}&nbsp;&nbsp;by&nbsp;$by
            <div style='float:right;'><a href='admin.php?action=news&amp;mode=edit&amp;newsid=$newsid'><span class='btn'>{$lang['news_act_edit']}</span></a>&nbsp;<a href='admin.php?action=news&amp;mode=delete&amp;newsid=$newsid'><span class='btn'>{$lang['news_act_delete']}</span></a>
            </div>
          </td>
        </tr>
        <tr valign='top'>
          <td class='comment'>$body</td>
        </tr>\n";
        
        $HTMLOUT .= end_table();
        $HTMLOUT .= end_frame();
        $HTMLOUT .= "<div class='clear'>&nbsp;</div>";
      }
      
       $HTMLOUT .= "<div style='text-align:right;'><input name='submit' type='submit' value='Update' class='btn' /></div></form>";
       $HTMLOUT .= end_main_frame();


    }
    else
      stdmsg($lang['news_sorry'], $lang['news_nonews']);

      $HTMLOUT .= "
      </div></div>";

    print stdhead($lang['news_window_title'], $js) . $HTMLOUT . stdfoot();
    die;
?>