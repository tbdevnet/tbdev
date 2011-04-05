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
                                             <div>
                                                 {$lang['videoformats_cam_body']}
                                             </div>

<div class='inner_header'></div>
<div class='inner_header'></div>
<div class='inner_header'></div>
<div class='inner_header'></div>
<div class='inner_header'></div>
<div class='inner_header'></div>
<div class='inner_header'></div>
<div class='inner_header'></div>
<div class='inner_header'></div>
<div class='inner_header'></div>




                                      </td>
                                   </tr>
                             </table>";

    $HTMLOUT .= "        </div>
                     </div>";

    print stdhead("{$lang['videoformats_header']}") . $HTMLOUT . stdfoot();
?>