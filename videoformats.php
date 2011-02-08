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
require "include/bittorrent.php";
require "include/user_functions.php";

dbconn(false);

    $lang = array_merge( load_language('global'), load_language('videoformats') );
    
    $HTMLOUT = '';

    $HTMLOUT .= "
                     <div class='cblock'>
                         <div class='cblock-header'>{$lang['videoformats_title']}</div>
                         <div class='cblock-content'>
                             <table class='main' width='595' border='0' cellspacing='0' cellpadding='0'>
                                   <tr>
                                      <td class='embedded'>
                                         <div class='inner_header'>{$lang['videoformats_cam']}</div>
                                         <div>{$lang['videoformats_cam_body']}</div>

                                         <div class='inner_header'>{$lang['videoformats_telesync']}</div>
                                         <div>{$lang['videoformats_telesync_body']}</div>

                                         <div class='inner_header'>{$lang['videoformats_telecine']}</div>
                                         <div>{$lang['videoformats_telecine_body']}</div>

                                         <div class='inner_header'>{$lang['videoformats_screener']}</div>
                                         <div>{$lang['videoformats_screener_body']}</div>

                                         <div class='inner_header'>{$lang['videoformats_dvdscreener']}</div>
                                         <div>{$lang['videoformats_dvdscreener_body']}</div>

                                         <div class='inner_header'>{$lang['videoformats_dvdrip']}</div>
                                         <div>{$lang['videoformats_dvdrip_body']}</div>

                                         <div class='inner_header'>{$lang['videoformats_vhsrip']}</div>
                                         <div>{$lang['videoformats_vhsrip_body']}</div>

                                         <div class='inner_header'>{$lang['videoformats_tvrip']}</div>
                                         <div>{$lang['videoformats_tvrip_body']}</div>

                                         <div class='inner_header'>{$lang['videoformats_workprint']}</div>
                                         <div>{$lang['videoformats_workprint_body']}</div>

                                         <div class='inner_header'>{$lang['videoformats_divx']}</div>
                                         <div>{$lang['videoformats_divx_body']}</div>

                                         <div class='inner_header'>{$lang['videoformats_watermarks']}</div>
                                         <div>{$lang['videoformats_watermarks_body']}</div>

                                         <div class='inner_header'>{$lang['videoformats_asian']}</div>
                                         <div>{$lang['videoformats_asian_body']}</div>

                                         <div class='inner_header'>{$lang['videoformats_scenetag']}</div>

                                         <div class='inner_header'>{$lang['videoformats_proper']}</div>
                                         <div>{$lang['videoformats_proper_body']}</div>

                                         <div class='inner_header'>{$lang['videoformats_limited']}</div>
                                         <div>{$lang['videoformats_limited_body']}</div>

                                         <div class='inner_header'>{$lang['videoformats_internal']}</div>
                                         <div>{$lang['videoformats_internal_body']}</div>

                                         <div class='inner_header'>{$lang['videoformats_stv']}</div>
                                         <div>{$lang['videoformats_stv_body']}</div>

                                         <div class='inner_header'>{$lang['videoformats_aspect']}</div>
                                         <div>{$lang['videoformats_aspect_body']}</div>

                                         <div class='inner_header'>{$lang['videoformats_repack']}</div>
                                         <div>{$lang['videoformats_repack_body']}</div>

                                         <div class='inner_header'>{$lang['videoformats_nuked']}</div>
                                         <div>{$lang['videoformats_nuked_body']}</div>

                                         <div class='inner_header'>{$lang['videoformats_dupe']}</div>
                                         <div>{$lang['videoformats_dupe_body']}</div>

                                      </td>
                                   </tr>
                             </table>";

    $HTMLOUT .= "        </div>
                     </div>";

    print stdhead("{$lang['videoformats_header']}") . $HTMLOUT . stdfoot();
?>