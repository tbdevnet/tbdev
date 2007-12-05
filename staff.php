<?php
require_once "include/bittorrent.php";
require_once "include/html_functions.php";
require_once "include/user_functions.php";


dbconn();

loggedinorreturn();
stdhead("Staff");

$query = mysql_query("SELECT users.id, username, email, last_access, class, title, country, status, countries.flagpic, countries.name FROM users LEFT  JOIN countries ON countries.id = users.country WHERE class >=4 AND status='confirmed' ORDER BY username") or sqlerr();

while($arr2 = mysql_fetch_assoc($query)) {
	
/*	if($arr2["class"] == UC_VIP)
		$vips[] =  $arr2;
*/	
	if($arr2["class"] == UC_MODERATOR)
		$mods[] =  $arr2;
		
	if($arr2["class"] == UC_ADMINISTRATOR)
		$admins[] =  $arr2;
		
	if($arr2["class"] == UC_SYSOP)
		$sysops[] =  $arr2;
	}
/*
print_r($sysops);
print("<BR>");
print_r($admins);
print("<BR>");
print_r($mods);
print("<BR>");
print(count($mods));
*/
function DoStaff($staff, $staffclass, $cols = 2) {
	global $pic_base_url;
	
	$dt = time() - 180;
	
	if($staff===false) {
		print("<br><table width='75%' border=1 cellpadding=3>");
		print("<tr><td class='colhead'><h2>{$staffclass}</h2></td></tr>");
		print("<tr><td>None defined yet!</td></tr></table>");
		return;
		}
	$counter = count($staff);
		
	$rows = ceil($counter/$cols);
	$cols = ($counter < $cols) ? $counter : $cols;
	//echo "<br>" . $cols . "   " . $rows;
	$r = 0;
	print("<br><table width='75%' border=1 cellpadding=3>");
	print("<tr><td class='colhead' colspan={$counter}><h2>{$staffclass}</h2></td></tr>");
	
	for($ia = 0; $ia < $rows; $ia++){

        echo "<tr>";
        for($i = 0; $i < $cols; $i++){
			if( isset($staff[$r]) )  {
			echo "<td><a href=userdetails.php?id={$staff[$r]['id']}>".$staff[$r]["username"]."</a>".
			"   <img style='vertical-align: middle;' src={$pic_base_url}staff".
			(time($staff[$r]['last_access'])>$dt?"/online.gif":"/offline.gif" )." border=0>".
			"<a href=sendmessage.php?receiver={$staff[$r]['id']}>".
			"   <img style='vertical-align: middle;' src={$pic_base_url}staff/users.png border=0 title=\"Personal Message\"></a>".
			"<a href=email-gateway.php?id={$staff[$r]['id']}>".
			"   <img style='vertical-align: middle;' src={$pic_base_url}staff/mail.png border=0 alt={$staff[$r]['username']} title=\"Send Mail\"></a>".
			"   <img style='vertical-align: middle;' src={$pic_base_url}flag/{$staff[$r]['flagpic']} border=0 alt='{$staff[$r]['name']}'></td>";
			$r++;
        }else{
			echo "<td>&nbsp;</td>";
			}
        }
        echo "</tr>";
		
        }
	print("</table>");
/*
print("</table>");
print("<br><table border=1><tr>");
for ($i = 0; $i <= count($staff)-1; $i++) {
		print("<td>{$staff[$i]["username"]}</td>");
		}
		print("</tr></table>");
*/
}

print("<h1>Staff Page</h1>");
DoStaff($sysops, "Sysops");
isset($admins) ? DoStaff($admins, "Administrators") : DoStaff($admins=false, "Administrators");
isset($mods) ? DoStaff($mods, "Moderators") : DoStaff($mods=false, "Moderators");
//isset($vips) ? DoStaff($vips, "VIP's") : DoStaff($vips=false, "VIP's");



 if (get_user_class() >= UC_MODERATOR) { 
?>

<br />

<br />
		<table>
		<!-- row 1 -->
		<tr><td>
		<div id="cpanel">
			<div style="float:left;">
			<div class="icon">

			<a href="bans.php">
				<div>
					<img src="<?=$pic_base_url?>staff/module.png" alt="Bad User" align="middle" name="image" border="0" />
				</div>
				Bans</a>
			</div>
			</div>
			<div style="float:left;">
			<div class="icon">

			<a href="adduser.php">
				<div class="iconimage">
					<img src="<?=$pic_base_url?>staff/addusers.png" alt="Add New User" align="middle" name="image" border="0" />
				</div>
				Add New User</a>
			</div>
			</div>
			<div style="float:left;">
			<div class="icon">

			<a href="recover.php">
				<div class="iconimage">
					<img src="<?=$pic_base_url?>staff/user.png" alt="Recover Account" align="middle" name="image" border="0" />
				</div>
				Recover Account</a>
			</div>
			</div>
			<div style="float:left;">
			<div class="icon">

			<a href="users.php">
				<div class="iconimage">
					<img src="<?=$pic_base_url?>staff/cancel_f2.png" alt="Users List" align="middle" name="image" border="0" />
				</div>
				Users List</a>
			</div>
			</div>
			</div>
			
			</td></tr>
			<!-- row 2 -->
			<tr><td>
			
			<div id="cpanel">
			<div style="float:left;">
			<div class="icon">

			<a href="tags.php">
				<div class="iconimage">
					<img src="<?=$pic_base_url?>staff/module.png" alt="BBCode Tags" align="middle" name="image" border="0" />
				</div>
				BBCode Tags</a>
			</div>
			</div>
			<div style="float:left;">
			<div class="icon">

			<a href="smilies.php">
				<div class="iconimage">
					<img src="<?=$pic_base_url?>staff/addusers.png" alt="Smilies" align="middle" name="image" border="0" />
				</div>
				Smilies/Emoticons</a>
			</div>
			</div>
			<div style="float:left;">
			<div class="icon">

			<a href="delacct.php">
				<div class="iconimage">
					<img src="<?=$pic_base_url?>staff/user.png" alt="Delete Account" align="middle" name="image" border="0" />
				</div>
				Delete Account</a>
			</div>
			</div>
			<div style="float:left;">
			<div class="icon">

			<a href="stats.php">
				<div class="iconimage">
					<img src="<?=$pic_base_url?>staff/cancel_f2.png" alt="Tracker Stats" align="middle" name="image" border="0" />
				</div>
				Tracker Statistics</a>
			</div>
			</div>
			</div>
			
			</td></tr>
			<!-- roow 3 -->
			<tr><td>
			
			<div id="cpanel">
			<div style="float:left;">
			<div class="icon">

			<a href="testip.php">
				<div class="iconimage">
					<img src="<?=$pic_base_url?>staff/module.png" alt="Test IP" align="middle" name="image" border="0" />
				</div>
				Test IP</a>
			</div>
			</div>
			<div style="float:left;">
			<div class="icon">

			<a href="usersearch.php">
				<div class="iconimage">
					<img src="<?=$pic_base_url?>staff/addusers.png" alt="User Search" align="middle" name="image" border="0" />
				</div>
				User Search</a>
			</div>
			</div>
			<div style="float:left;">
			<div class="icon">

			<a href="mysql_overview.php">
				<div class="iconimage">
					<img src="<?=$pic_base_url?>staff/user.png" alt="Mysql Overview" align="middle" name="image" border="0" />
				</div>
				MySQL Overview</a>
			</div>
			</div>
			<div style="float:left;">
			<div class="icon">

			<a href="mysql_stats.php">
				<div class="iconimage">
					<img src="<?=$pic_base_url?>staff/cancel_f2.png" alt="MySQL Stats" align="middle" name="image" border="0" />
				</div>
				MySQL Statistics</a>
			</div>
			</div>
			</div>
			
			</td></tr>
			<!-- row 4 -->
			<tr><td>
			
			<div id="cpanel">
			<div style="float:left;">
			<div class="icon">

			<a href="admin_email_search.php">
				<div class="iconimage">
					<img src="<?=$pic_base_url?>staff/module.png" alt="Email Search" align="middle" name="image" border="0" />
				</div>
				Email Search</a>
			</div>
			</div>
			<div style="float:left;">
			<div class="icon">

			<a href="categories.php">
				<div class="iconimage">
					<img src="<?=$pic_base_url?>staff/addusers.png" alt="Add New User" align="middle" name="image" border="0" />
				</div>
				Categories</a>
			</div>
			</div>
			<div style="float:left;">
			<div class="icon">

			<a href="newusers.php">
				<div class="iconimage">
					<img src="<?=$pic_base_url?>staff/user.png" alt="User Search" align="middle" name="image" border="0" />
				</div>
				Newest Users</a>
			</div>
			</div>
			<div style="float:left;">
			<div class="icon">

			<a href="resetpassword.php">
				<div class="iconimage">
					<img src="<?=$pic_base_url?>staff/cancel_f2.png" alt="Ban User" align="middle" name="image" border="0" />
				</div>
			  Reset Password </a>
			</div>
			</div>
			

		</div>
		</td></tr></table>
<? }

stdfoot();

?>