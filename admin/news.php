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

if ( ! defined( 'IN_TBDEV_ADMIN' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly.";
	exit();
}

//require_once "include/bittorrent.php";
require_once "include/user_functions.php";
require_once "include/bbcode_functions.php";
require_once "include/html_functions.php";

$input = array_merge( $_GET, $_POST);

$mode = isset($input['mode']) ? $input['mode'] : '';

$warning = '';
//   Delete News Item    //////////////////////////////////////////////////////

if ($mode == 'delete')
{
	$newsid = isset($input['newsid']) ? (int)$input["newsid"] : 0;
	
  if (!is_valid_id($newsid))
  	stderr("Error","Invalid news item ID - Code 1.");

  $returnto = htmlentities($input["returnto"]);

  $sure = isset($input["sure"]) ? (int)$input['sure'] : 0;
  if (!$sure)
    stderr("Delete news item","Do you really want to delete a news item? Click\n" .
    	"<a href='admin.php?action=news&amp;mode=delete&amp;newsid=$newsid&amp;returnto=news&amp;sure=1'>here</a> if you are sure.");

  mysql_query("DELETE FROM news WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

	if ($returnto != "")
		header("Location: $BASEURL/admin.php?action=news");
	else
		$warning = "News item was deleted successfully.";
}

//   Add News Item    /////////////////////////////////////////////////////////

if ($mode == 'add')
{

	$body = isset($input["body"]) ? (string)$input["body"] : 0;
	if ( !$body OR strlen($body) < 4 )
		stderr("Error","The news item cannot be empty!");

	$added = isset($input["added"]) ? $input['added'] : 0;
	
	if (!$added)
		$added = time();

  @mysql_query("INSERT INTO news (userid, added, body) VALUES (".
  	$CURUSER['id'] . ", $added, " . sqlesc($body) . ")") or sqlerr(__FILE__, __LINE__);
  	
	if (mysql_affected_rows() == 1)
		$warning = "News item was added successfully.";
	else
		stderr("Error","Something weird just happened.");
}

//   Edit News Item    ////////////////////////////////////////////////////////

if ($mode == 'edit')
{

	$newsid = isset($input["newsid"]) ? (int)$input["newsid"] : 0;

  if (!is_valid_id($newsid))
  	stderr("Error","Invalid news item ID - Code 2.");

  $res = @mysql_query("SELECT * FROM news WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) != 1)
	  stderr("Error", "No news item with ID.");

	$arr = mysql_fetch_assoc($res);

  if ($_SERVER['REQUEST_METHOD'] == 'POST')
  {
  	$body = isset($_POST['body']) ? $_POST['body'] : '';

    if ($body == "" OR strlen($_POST['body']) < 4)
    	stderr("Error", "Body cannot be empty!");

    $body = sqlesc($body);

    $editedat = time();

    mysql_query("UPDATE news SET body=$body WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

    $returnto = isset($_POST['returnto']) ? htmlentities($_POST['returnto']) : '';

		if ($returnto != "")
			header("Location: $BASEURL/admin.php?action=news");
		else
			$warning = "News item was edited successfully.";
  }
  else
  {
 	 	//$returnto = isset($_POST['returnto']) ? htmlentities($_POST['returnto']) : $BASEURL.'/news.php';
	  $htmlout = "<h1>Edit News Item</h1>\n";
	  
	  $htmlout .= "<form method='post' action='admin.php?action=news'>\n";
	  
	  $htmlout .= "<input type='hidden' name='newsid' value='$newsid' />\n";
	  
	  $htmlout .= "<input type='hidden' name='action' value='edit' />\n";
	  
	  $htmlout .= "<table border='1' cellspacing='0' cellpadding='5'>\n";
	  
	  $htmlout .= "<tr><td style='padding: 0px'><textarea name='body' cols='145' rows='5'>" . htmlentities($arr['body'], ENT_QUOTES) . "</textarea></td></tr>\n";
	  
	  $htmlout .= "<tr><td align='center'><input type='submit' value='Okay' class='btn' /></td></tr>\n";
	  
	  $htmlout .= "</table>\n";
	  
	  $htmlout .= "</form>\n";
	  
	  stdhead();
	  
	  print $htmlout;
	  
	  stdfoot();
	  exit();
  }
}

//   Other Actions and followup    ////////////////////////////////////////////

stdhead("Site news");
print("<h1>Submit News Item</h1>\n");
if (!empty($warning))
	print("<p><font size='-3'>($warning)</font></p>");
print("<form method='post' action='admin.php?action=news'>\n");
print("<input type='hidden' name='mode' value='add' />\n");
print("<table border='1' cellspacing='0' cellpadding='5'>\n");
print("<tr><td style='padding: 10px'><textarea name='body' cols='141' rows='5' style='border: 0px'></textarea>\n");
print("<br /><br /><div align='center'><input type='submit' value='Okay' class='btn' /></div></td></tr>\n");
print("</table></form><br /><br />\n");

$res = @mysql_query("SELECT * FROM news ORDER BY added DESC") or sqlerr(__FILE__, __LINE__);

if (mysql_num_rows($res) > 0)
{


 	begin_main_frame();
	begin_frame();

	while ($arr = mysql_fetch_assoc($res))
	{
		$newsid = $arr["id"];
		$body = format_comment($arr["body"]);
	  $userid = $arr["userid"];
	  $added = get_date( $arr['added'],'');

    $res2 = mysql_query("SELECT username, donor FROM users WHERE id = $userid") or sqlerr(__FILE__, __LINE__);
    $arr2 = mysql_fetch_assoc($res2);

    $postername = $arr2["username"];

    if ($postername == "")
    	$by = "unknown[$userid]";
    else
    	$by = "<a href='userdetails.php?id=$userid'><b>$postername</b></a>" .
    		($arr2["donor"] == "yes" ? "<img src=\"{$pic_base_url}star.gif\" alt='Donor' />" : "");
    		
    begin_table(true);
	  print("<tr><td class='embedded'>");
    print("$added&nbsp;&nbsp;by&nbsp$by");
    print(" <div style='float:right;'>[<a href='admin.php?action=news&amp;mode=edit&amp;newsid=$newsid'><b>Edit</b></a>]");
    print(" - [<a href='admin.php?action=news&amp;mode=delete&amp;newsid=$newsid'><b>Delete</b></a>]</div>");
    print("</td></tr>\n");

	  
	  print("<tr valign='top'><td class='comment'>$body</td></tr>\n");
	  end_table();
	  print '<br />';
	}
	end_frame();
	end_main_frame();
}
else
  stdmsg("Sorry", "No news available!");
stdfoot();
die;
?>