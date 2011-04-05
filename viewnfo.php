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
require "include/bittorrent.php";
require "include/user_functions.php";
require "include/bbcode_functions.php";
dbconn(false);
loggedinorreturn();

    $lang = array_merge( load_language('global'), load_language('viewnfo') );
    
    $id = 0 + $_GET["id"];
    if ($CURUSER['class'] < UC_POWER_USER || !is_valid_id($id))
      die;

    $r = mysql_query("SELECT name,nfo FROM torrents WHERE id=$id") or sqlerr();
    $a = mysql_fetch_assoc($r) or die("{$lang['text_puke']}");
    //$nfo = htmlsafechars($a["nfo"]);
    $HTMLOUT = '';
    

    $HTMLOUT .= "
                     <div class='cblock'>
                         <div class='cblock-header'>{$lang['text_nfofor']}<a href='details.php?id=$id'>".htmlsafechars($a['name'])."</a></div>
                         <div class='cblock-lb'>{$lang['text_forbest']}<a href='ftp://{$_SERVER['HTTP_HOST']}/misc/linedraw.ttf'>{$lang['text_linedraw']}</a>{$lang['text_font']}</div>
                         <div class='cblock-content'>
                             <table border='1' cellspacing='0' cellpadding='5'>
                                   <tr>
                                      <td class='text'>\n";
    $HTMLOUT .= "                        <pre>" . format_urls(htmlsafechars($a['nfo'])) . "</pre>\n";
    $HTMLOUT .= "                     </td>
                                   </tr>
                             </table>\n";
    $HTMLOUT .= "       </div>
                     </div>";


    print stdhead() . $HTMLOUT . stdfoot();
?>