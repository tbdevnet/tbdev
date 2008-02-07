<?php
require_once "include/bittorrent.php";
require_once "include/user_functions.php";
require_once "include/bbcode_functions.php";

dbconn(false);
loggedinorreturn();

	$out = isset($_GET['out']) ? $_GET['out'] : false;
	
  if ($out)		// Sentbox
  {
	  stdhead("Sentbox", false);
		print("<table class=main width=750 border=0 cellspacing=0 cellpadding=10><tr><td class=embedded>\n");
  	print("<h1 align=center>Sentbox</h1>\n");
   	print("<div align=center>(<a href=" . $_SERVER['PHP_SELF'] . ">Inbox</a>)</div>\n");
	  $res = mysql_query("SELECT u.username, m. *
                        FROM messages m
                        LEFT JOIN users u ON m.receiver = u.id
                        WHERE m.sender = ".$CURUSER['id']." 
                        AND m.location
                        IN ('out', 'both')
                        ORDER BY m.added DESC ") or sqlerr(__FILE__,__LINE__);
	  if (mysql_num_rows($res) == 0)
      stdmsg("Information","Your Sentbox is empty!");
	  else
	  while ($arr = mysql_fetch_assoc($res))
	  {
 	  	//$res2 = mysql_query("SELECT username FROM users WHERE id=" . $arr["receiver"]) or sqlerr();
	    //$arr2 = mysql_fetch_assoc($res2);
	    $receiver = "<a href=userdetails.php?id=" . $arr["receiver"] . ">" . $arr["username"] . "</a>";
	  	$elapsed = get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]));
	    print("<p><table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text>\n");
	    print("To <b>$receiver</b> at\n" . $arr["added"] . " ($elapsed ago) GMT\n");
      if (get_user_class() >= UC_MODERATOR && $arr["unread"] == "yes")
	    	print("<b>(<font color=red>Unread!</font>)</b>");
	    print("<p><table class=main width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text>\n");
	    print(format_comment($arr["msg"]));
	    print("</td></tr></table></p>\n<p>");
	    print("<table width=100%  border=0><tr><td class=embedded>\n");
			print("<a href=deletemessage.php?id=" . $arr["id"] . "&type=out><b>Delete</b></a></td>\n");
	    print("</tr></table></tr></table></p>\n");
	  }
  }
  else		// Inbox
  {
	  stdhead("Inbox", false);
		print("<table class=main width=750 border=0 cellspacing=0 cellpadding=10><tr><td class=embedded>\n");
  	print("<h1 align=center>Inbox</h1>\n");
   	print("<div align=center>(<a href=" . $_SERVER['PHP_SELF'] . "?out=1>Sentbox</a>)</div>\n");
  	$res = mysql_query("SELECT u.username, m. *
                        FROM messages m
                        LEFT JOIN users u ON m.sender = u.id
                        WHERE m.receiver = ".$CURUSER['id']." 
                        AND m.location
                        IN ('in', 'both')
                        ORDER BY m.added DESC ") or sqlerr(__FILE__,__LINE__);
	  if (mysql_num_rows($res) == 0)
      stdmsg("Information","Your Inbox is empty!");
	  else
    	while ($arr = mysql_fetch_assoc($res))
	    {
	        $sender = ($arr['sender'] == 0) ? 'system' : ($arr['username'] ? "<a href=userdetails.php?id={$arr["sender"]}>{$arr["username"]}</a>" : "[Deleted]");
	       
	        
	    $elapsed = get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]));
	      print("<p><table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text>\n");
	      print("From <b>$sender</b> at\n" . $arr["added"] . " ($elapsed ago) GMT\n");
	      if ($arr["unread"] == "yes")
	      {
	        print("<b>(<font color=red>NEW!</font>)</b>");
	        $update_buffer[] = $arr['id'];
	        //mysql_query("UPDATE messages SET unread='false' WHERE id=" . $arr["id"]) or die("arghh");
	      }
	      print("<p><table class=main width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text>\n");
	      print(format_comment($arr["msg"]));
	      print("</td></tr></table></p>\n<p>");
	      print("<table width=100%  border=0><tr><td class=embedded>\n");
	      print(($arr["sender"] && $arr['username']? "<a href=sendmessage.php?receiver=" . $arr["sender"] . "&replyto=" . $arr["id"] .
	        "><b>Reply</b></a>" : "<font class=gray><b>Reply</b></font>") .
	        " | <a href=deletemessage.php?id=" . $arr["id"] . "&type=in><b>Delete</b></a></td>\n");
				/*
	      if (get_user_class() >= UC_MODERATOR)
	      {
	        print("<td class=embedded><div align=right>Templates: &nbsp; ".
	          ($arr["sender"] ? "<a href=sendmessage.php?receiver=" .
	          $arr["sender"] . "&replyto=" . $arr["id"] . "&auto=1" .
	          "><b>FAQ</b></a>" : "<font class=gray><b>FAQ</b></font>").
	          " | What  else?".
	          "</div></td>\n");
	      }
        */
	      print("</tr></table></tr></table></p>\n");
	    }
	    @mysql_query("UPDATE messages SET unread='false' WHERE id IN (".join(',', $update_buffer).")");
  }
	print("</td></tr></table>\n");
	print("<p align=center>Do you need to <a href=users.php>find</a> someone?</p>\n");
  stdfoot();
?>