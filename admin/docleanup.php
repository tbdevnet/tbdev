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
|   $Date: 2009-08-13 16:42:39 +0100 (Thu, 13 Aug 2009) $
|   $Revision: 185 $
|   $Author: tbdevnet $
|   $URL: https://tbdevnet.svn.sourceforge.net/svnroot/tbdevnet/trunk/TB/docleanup.php $
+------------------------------------------------
*/
require_once("include/bittorrent.php");

dbconn();
loggedinorreturn();

if( get_user_class() != UC_SYSOP )
	exit();
	
docleanup();

print("Done");

?>
