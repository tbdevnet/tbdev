<?php
ob_start("ob_gzhandler");

require_once "include/bittorrent.php";
require_once "include/user_functions.php";

dbconn(true);

loggedinorreturn();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $choice = isset($_POST["choice"]) ? $_POST["choice"] : 257;
     if ($CURUSER && ctype_digit($choice) && $choice < 256 && $choice == floor($choice)) {
    $pollid = (int)$_POST['pollid'];
    $userid = (int)$CURUSER["id"];
    $res = mysql_query("SELECT pa.id ".
                     "FROM polls AS p ".
                     "LEFT JOIN pollanswers AS pa ON pa.pollid = p.id AND pa.userid = ".sqlesc($userid)." ".
                     "WHERE p.id = ".sqlesc($pollid)) or sqlerr(__FILE__, __LINE__);
    $arr = mysql_fetch_assoc($res) or stderr('Sorry', 'Inexistent poll!');

    if (is_valid_id($arr['id']))
    stderr("Error...", "Dupe vote");

    mysql_query("INSERT INTO pollanswers VALUES(0, $pollid, $userid, $choice)") or sqlerr(__FILE__, __LINE__);
    if (mysql_affected_rows() != 1)
      stderr("Error", "An error occured. Your vote has not been counted.");
        header("Location: $BASEURL/");
    die;
  }

else
    stderr("Error", "Please select an option.");
}

/*
$a = @mysql_fetch_assoc(@mysql_query("SELECT id,username FROM users WHERE status='confirmed' ORDER BY id DESC LIMIT 1")) or die(mysql_error());
if ($CURUSER)
  $latestuser = "<a href='userdetails.php?id=" . $a["id"] . "'>" . $a["username"] . "</a>";
else
  $latestuser = $a['username'];
*/

$registered = number_format(get_row_count("users"));
//$unverified = number_format(get_row_count("users", "WHERE status='pending'"));
$torrents = number_format(get_row_count("torrents"));
//$dead = number_format(get_row_count("torrents", "WHERE visible='no'"));

$r = mysql_query("SELECT value_u FROM avps WHERE arg='seeders'") or sqlerr(__FILE__, __LINE__);
$a = mysql_fetch_row($r);
$seeders = 0 + $a[0];
$r = mysql_query("SELECT value_u FROM avps WHERE arg='leechers'") or sqlerr(__FILE__, __LINE__);
$a = mysql_fetch_row($r);
$leechers = 0 + $a[0];
if ($leechers == 0)
  $ratio = 0;
else
  $ratio = round($seeders / $leechers * 100);
$peers = number_format($seeders + $leechers);
$seeders = number_format($seeders);
$leechers = number_format($leechers);

/*
$dt = gmtime() - 180;
$dt = sqlesc(get_date_time($dt));
$res = mysql_query("SELECT id, username, class, donated FROM users WHERE last_access >= $dt ORDER BY username") or print(mysql_error());
while ($arr = mysql_fetch_assoc($res))
{
  if ($activeusers) $activeusers .= ",\n";
  switch ($arr["class"])
  {
    case UC_SYSOP:
    case UC_ADMINISTRATOR:
    case UC_MODERATOR:
      $arr["username"] = "<font color='#A83838'>" . $arr["username"] . "</font>";
      break;
     case UC_UPLOADER:
      $arr["username"] = "<font color='#4040C0'>" . $arr["username"] . "</font>";
      break;
  }
  $donator = $arr["donated"] > 0;
  if ($donator)
    $activeusers .= "<span style=\"white-space: nowrap;\">";
  if ($CURUSER)
    $activeusers .= "<a href='userdetails.php?id=" . $arr["id"] . "'><b>" . $arr["username"] . "</b></a>";
  else
    $activeusers .= "<b>$arr[username]</b>";
  if ($donator)
    $activeusers .= "<img src=\"{$pic_base_url}star.gif\" alt='Donated $arr[donated]'></span>";
}
if (!$activeusers)
  $activeusers = "There have been no active users in the last 15 minutes.";
*/
stdhead();
//echo "<font class='small''>Welcome to our newest member, <b>$latestuser</b>!</font>\n";

print("<table width='737' class='main' border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>");
print("<h2>Recent news");
if (get_user_class() >= UC_ADMINISTRATOR)
	print(" - <font class='small'>[<a class='altlink' href='news.php'><b>News page</b></a>]</font>");
print("</h2>\n");
$res = mysql_query("SELECT * FROM news WHERE ADDDATE(added, INTERVAL 45 DAY) > NOW() ORDER BY added DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0)
{
	require_once "include/bbcode_functions.php";

	print("<table width='100%' border='1' cellspacing='0' cellpadding='10'><tr><td class='text'>\n<ul>");
	while($array = mysql_fetch_assoc($res))
	{
	  print("<li>" . gmdate("Y-m-d",strtotime($array['added'])) . "<br />" . format_comment($array['body']));
    if (get_user_class() >= UC_ADMINISTRATOR)
    {
    	print(" <br /><font size=\"-2\">[<a class='altlink' href='news.php?action=edit&amp;]newsid=" . $array['id'] . "&amp;returnto=index.php'><b>E</b></a>]</font>");
    	print(" <font size=\"-2\">[<a class='altlink' href='news.php?action=delete&amp;newsid=" . $array['id'] . "&amp;returnto=index.php'><b>D</b></a>]</font>");
    }
    print("</li>");
  }
  print("</ul></td></tr></table>\n");
}

/*
<h2>Active users</h2>
<table width='100%' border='1' cellspacing='0' cellpadding='1'0><tr><td class='text'>
<?php echo $activeusers?>
</td></tr></table>
*/

if($CURUSER) {

// Get current poll
$res = mysql_query("SELECT p.*, pa.id AS pa_id, pa.selection FROM polls AS p ".
"LEFT JOIN pollanswers AS pa ON pa.pollid = p.id AND pa.userid = ".$CURUSER['id']." ".
"ORDER BY p.added DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
if($pollok = (mysql_num_rows($res) > 0))
{
$arr = mysql_fetch_assoc($res);
$pollid = $arr["id"];
$userid = $CURUSER["id"];
$question = $arr["question"];
$o = array($arr["option0"], $arr["option1"], $arr["option2"], $arr["option3"], $arr["option4"],
$arr["option5"], $arr["option6"], $arr["option7"], $arr["option8"], $arr["option9"],
$arr["option10"], $arr["option11"], $arr["option12"], $arr["option13"], $arr["option14"],
$arr["option15"], $arr["option16"], $arr["option17"], $arr["option18"], $arr["option19"]);
}

echo '<h2>Poll';
if ($CURUSER['class'] >= UC_MODERATOR)
{

?>
<font class="small"> - [<a class="altlink" href="makepoll.php?returnto=main"><b>New</b></a>
<?php
if($pollok) {
?>
- [<a class="altlink" href="makepoll.php?action=edit&amp;pollid=<?php echo $arr['id'];?>&amp;returnto=main"><b>Edit</b></a>]
- [<a class="altlink" href="polls.php?action=delete&amp;pollid=<?php echo $arr['id'];?>&amp;returnto=main"><b>Delete</b></a>]
<?php
}
echo '</font>';
}
echo '</h2>';
if($pollok) {
?>
<table width='100%' border='1' cellspacing='0' cellpadding='10'>
<tr><td align='center'>
<table class='main' border='1' cellspacing='0' cellpadding='0'>
<tr><td class='text'>
<p align='center'>
<b><?php echo $question;?></b>
</p>
<?php
// $voted = $arr2;
$voted = (is_valid_id($arr['pa_id']) ? true : false);
if ($voted)
{
// display results // old
// if ($arr2["selection"])
// $uservote = $arr2["selection"];
// else
// $uservote = -1;
$uservote = ($arr["selection"] != '' ? (int)$arr["selection"] : -1);

// we reserve 255 for blank vote.
$res = mysql_query("SELECT selection FROM pollanswers WHERE pollid=$pollid AND selection < 20") or sqlerr(__FILE__, __LINE__);
$tvotes = mysql_num_rows($res);

// $vs = array(); // array of
for($i=0;$i<20;$i++) $vs[$i]=0;
$os = array();

// Count votes
while ($arr2 = mysql_fetch_row($res))
$vs[$arr2[0]] += 1;

reset($o);
for ($i = 0; $i < count($o); ++$i)
if ($o[$i])
$os[$i] = array($vs[$i], $o[$i]);

function srt($a,$b)
{
if ($a[0] > $b[0]) return -1;
if ($a[0] < $b[0]) return 1;
return 0;
}

// now os is an array like this: array(array(123, "Option 1"), array(45, "Option 2"))
if($uservote!=-1)
$os[$uservote]['casted'] = true

if ($arr["sort"] == "yes")
usort($os, "srt");
?>
<table class='main' width='100%' border='0' cellspacing='0' cellpadding='0'>
<?php
$i = 0;
// while ($a = $os[$i])
foreach($os as $a)
{
if (isset($a['casted']))
$a[1] .= "&nbsp;*";
$p = ($tvotes == 0 ? 0 : round($a[0] / $tvotes * 100));
$c = ($i % 2 ? '' : ' bgcolor="#ECE9D8"');
print("<tr><td width='1%' class='embednowrap$c'>" . $a[1] . "&nbsp;&nbsp;</td><td width='99%' class='embedded$c'>" .
"<img src=\"{$pic_base_url}bar_left.gif\" alt='' /><img src=\"{$pic_base_url}bar.gif\" alt=\"\" height=\"9\" width=\"" . ($p * 3) .
"\" /><img src=\"{$pic_base_url}bar_right.gif\" alt=\"\" /> $p%</td></tr>\n");
++$i;
}
print("</table>\n");
$tvotes = number_format($tvotes);
print("<p align='center'>Votes: $tvotes</p>\n");
}else {
print("<form method='post' action='index.php'>\n");
print("<input type='hidden' name='pollid' value='$pollid' />\n");
for ($i=0; $a = $o[$i]; ++$i)
print("<input type='radio' name='choice' value='$i' />$a<br />\n");
print("<br />");
print("<input type='radio' name='choice' value='255' />Blank vote (a.k.a. \"I just want to see the results!\")<br />\n");
print("<p align='center'><input type='submit' value='Vote!' class='btn' /></p></form>");
}
?>
</td></tr>
</table>
<?php
if ($voted)
echo "<p align='center'><a href='polls.php'>Previous polls</a></p>";
?>
</td></tr>
</table>
<?php
} else {
?>
<table width='100%' border='1' cellspacing='0' cellpadding='10'>
<tr><td align='center'>
<table class='main' border='1' cellspacing='0' cellpadding='0'>
<tr><td class='text'>
<p align='center'>
<h3>No Active Polls</h3>
</p>
</td></tr>
</table>
</td></tr>
</table>
<p><br /></p>
<?php
}

}
?>

<h2>Stats</h2>
<table width='100%' border='1' cellspacing='0' cellpadding='10'><tr><td align='center'>
<table class='main' border='1' cellspacing='0' cellpadding='5'>
<tr><td class='rowhead'>Registered users</td><td align='right'><?php echo $registered?></td></tr>
<!-- <tr><td class='rowhead'>Unconfirmed users</td><td align=right><?php echo $unverified?></td></tr> -->
<tr><td class='rowhead'>Torrents</td><td align='right'><?php echo $torrents?></td></tr>
<?php if (isset($peers)) { ?>
<tr><td class='rowhead'>Peers</td><td align='right'><?php echo $peers?></td></tr>
<tr><td class='rowhead'>Seeders</td><td align='right'><?php echo $seeders?></td></tr>
<tr><td class='rowhead'>Leechers</td><td align='right'><?php echo $leechers?></td></tr>
<tr><td class='rowhead'>Seeder/leecher ratio (%)</td><td align='right'><?=$ratio?></td></tr>
<?php } ?>
</table>
</td></tr></table>

<?php /*
<h2>Server load</h2>
<table width='100%' border='1' cellspacing='0' cellpadding='1'0><tr><td align=center>
<table class=main border='0' width=402><tr><td style='padding: 0px; background-image: url("<?php echo $pic_base_url?>loadbarbg.gif"); background-repeat: repeat-x'>
<?php $percent = min(100, round(exec('ps ax | grep -c apache') / 256 * 100));
if ($percent <= 70) $pic = "loadbargreen.gif";
elseif ($percent <= 90) $pic = "loadbaryellow.gif";
else $pic = "loadbarred.gif";
$width = $percent * 4;
print("<img height='1'5 width=$width src=\"{$pic_base_url}{$pic}\" alt='$percent%'>"); ?>
</td></tr></table>
</td></tr></table>
*/ ?>

<p><font class='small'>Disclaimer: None of the files shown here are actually hosted on this server. The links are provided solely by this site's users.
The administrator of this site (www.tbdev.net) cannot be held responsible for what its users post, or any other actions of its users.
You may not use this site to distribute or download any material when you do not have the legal rights to do so.
It is your own responsibility to adhere to these terms.</font></p>

<p align='center'>
<a href="http://www.tbdev.net"><img src="<?=$pic_base_url?>tbdev_btn_red.png" border='0' alt="P2P Legal Defense Fund" /></a>
</p>


</td></tr></table>

<?php

stdfoot();
?>