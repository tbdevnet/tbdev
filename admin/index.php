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

require_once "include/html_functions.php";
require_once "include/user_functions.php";


    $lang = array_merge( $lang, load_language('ad_index') );

    $HTMLOUT = '';

    $HTMLOUT .= "
                 <div class='cblock'>
                     <div class='cblock-header'>Staff Tools</div>
                     <div class='cblock-lb'>Sysop, Admin and Moderator tools!</div>
                     <div class='cblock-content'>
                     <div class='base-layer'>
                         <!-- row 1 -->
                         <div class='table-row'>
                             <div class='left-layer'><p class='text'><a href='admin.php?action=bans'>{$lang['index_bans']}</a></p></div>
                             <div class='left-layer'><p class='text'><a href='admin.php?action=adduser'>{$lang['index_new_user']}</a></p></div>
                             <div class='left-layer'><p class='text'><a href='admin.php?action=log'>{$lang['index_log']}</a></p></div>
                             <div class='left-layer'><p class='text'><a href='admin.php?action=docleanup'>{$lang['index_mcleanup']}</a></p></div>
                             <div class='left-layer'><p class='text'><a href='users.php'>{$lang['index_user_list']}</a></p></div>
                             <div class='space-line'></div>
                         </div>
        			     <!-- row 2 -->
                         <div class='table-row'>
                             <div class='left-layer'><p class='text'><a href='tags.php'>{$lang['index_tags']}</a></p></div>
                             <div class='left-layer'><p class='text'><a href='smilies.php'>{$lang['index_emoticons']}</a></p></div>
                             <div class='left-layer'><p class='text'><a href='admin.php?action=delacct'>{$lang['index_delacct']}</a></p></div>
                             <div class='left-layer'><p class='text'><a href='admin.php?action=stats'>{$lang['index_stats']}</a></p></div>
                             <div class='space-line'></div>
                         </div>
			             <!-- row 3 -->
                         <div class='table-row'>
                             <div class='left-layer'><p class='text'><a href='admin.php?action=testip'>{$lang['index_testip']}</a></p></div>
                             <div class='left-layer'><p class='text'><a href='admin.php?action=usersearch'>{$lang['index_user_search']}</a></p></div>
                             <div class='left-layer'><p class='text'><a href='admin.php?action=mysql_overview'>{$lang['index_mysql_overview']}</a></p></div>
                             <div class='left-layer'><p class='text'><a href='admin.php?action=mysql_stats'>{$lang['index_mysql_stats']}</a></p></div>
                             <div class='space-line'></div>
                         </div>
			             <!-- row 4 -->
                         <div class='table-row'>
                             <div class='left-layer'><p class='text'><a href='admin.php?action=forummanage'>{$lang['index_forummanage']}</a></p></div>
                             <div class='left-layer'><p class='text'><a href='admin.php?action=categories'>{$lang['index_categories']}</a></p></div>
                             <div class='space-line'></div>
                         </div>
			             <!-- row 5 -->
                         <div class='table-row'>
                             <div class='left-layer'><p class='text'><a href='reputation_ad.php'>{$lang['index_rep_system']}</a></p></div>
                             <div class='left-layer'><p class='text'><a href='reputation_settings.php'>{$lang['index_rep_settings']}</a></p></div>
                             <div class='left-layer'><p class='text'><a href='admin.php?action=news'>{$lang['index_news']}</a></p></div>
                             <div class='left-layer'><p class='text'><a href='admin.php?action=rules'>{$lang['index_rules']}</a></p></div>
                             <div class='left-layer'><p class='text'><a href='admin.php?action=cleanup_manager'>Cleanup Manager</a></p></div>
                             <div class='space-line'></div>
                         </div>
                     </div>";
    $HTMLOUT .= "
      </div></div>";
 
    print stdhead("Staff") . $HTMLOUT . stdfoot();

?>