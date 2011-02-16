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


function commenttable($rows)
{
	global $CURUSER, $TBDEV;

	
	$lang = load_language( 'torrenttable_functions' );
	
	$htmlout = '';
	$count = 0;
	
	$htmlout .= begin_main_frame();
	$htmlout .= begin_frame();
	
	foreach ($rows as $row)
	{
		$htmlout .= "<p class='sub'>#{$row["id"]} {$lang["commenttable_by"]} ";
    if (isset($row["username"]))
		{
			$title = $row["title"];
			if ($title == "")
				$title = get_user_class_name($row["class"]);
			else
				$title = htmlsafechars($title);
        $htmlout .= "<a name='comm{$row["id"]}' href='userdetails.php?id={$row["user"]}'><b>" .
        	htmlsafechars($row["username"]) . "</b></a>" . ($row["donor"] == "yes" ? "<img src='{$TBDEV['pic_base_url']}star.gif' alt='".$lang["commenttable_donor_alt"]."' />" : "") . ($row["warned"] == "yes" ? "<img src=".
    			"'{$TBDEV['pic_base_url']}warned.gif' alt='".$lang["commenttable_warned_alt"]."' />" : "") . " ($title)\n";
		}
		else
   		$htmlout .= "<a name='comm{$row["id"]}'><i>(".$lang["commenttable_orphaned"].")</i></a>\n";

		$htmlout .= get_date( $row['added'],'');
		$htmlout .= ($row["user"] == $CURUSER["id"] || get_user_class() >= UC_MODERATOR ? "- [<a href='comment.php?action=edit&amp;cid={$row['id']}'>".$lang["commenttable_edit"]."</a>]" : "") .
			(get_user_class() >= UC_MODERATOR ? "- [<a href='comment.php?action=delete&amp;cid={$row['id']}'>".$lang["commenttable_delete"]."</a>]" : "") .
			($row["editedby"] && get_user_class() >= UC_MODERATOR ? "- [<a href='comment.php?action=vieworiginal&amp;cid={$row['id']}'>".$lang["commenttable_view_original"]."</a>]" : "") . "</p>\n";
		$avatar = ($CURUSER["avatars"] == "yes" ? htmlsafechars($row["avatar"]) : "");
		
		if (!$avatar)
			$avatar = "{$TBDEV['pic_base_url']}default_avatar.gif";
		$text = format_comment($row["text"]);
    if ($row["editedby"])
    	$text .= "<p style='font-size:1px;' class='small'>".$lang["commenttable_last_edited_by"]." <a href='userdetails.php?id={$row['editedby']}'><b>{$row['username']}</b></a> ".$lang["commenttable_last_edited_at"]." ".get_date($row['editedat'],'DATE')."</p>\n";
		$htmlout .= begin_table(true);
		$htmlout .= "<tr valign='top'>\n";
		$htmlout .= "<td style='width:150px; text-align:center; padding: 0px'><img width='{$row['av_w']}' height='{$row['av_h']}' src='{$avatar}' alt='' /></td>\n";
		$htmlout .= "<td class='text'>$text</td>\n";
		$htmlout .= "</tr>\n";
     $htmlout .= end_table();
  }
	$htmlout .= end_frame();
	$htmlout .= end_main_frame();
	
	return $htmlout;
}


?>