<?php

require_once("include/bittorrent.php");
dbconn();

ini_set('session.use_trans_sid', '0');

// Begin the session
session_start();
(time() - $_SESSION['captcha_time'] < 10) ? exit('NO SPAM!') : NULL;

$res = mysql_query("SELECT COUNT(*) FROM users") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_row($res);
if ($arr[0] >= $maxusers)
	stderr("Sorry", "The current user account limit (" . number_format($maxusers) . ") has been reached. Inactive accounts are pruned all the time, please check back again later...");


stdhead("Signup");

?>
<!--
<table width=500 border=1 cellspacing=0 cellpadding=10><tr><td align=left>
<h2 align=center>Proxy check</h2>
<b><font color=red>Important - please read:</font></b> We do not accept users connecting through public proxies. When you
submit the form below we will check whether any commonly used proxy ports on your computer is open. If you have a firewall it may alert of you of port
scanning activity originating from <b>69.10.142.42</b> (torrentbits.org). This is only our proxy-detector in action.
<b>The check takes up to 30 seconds to complete, please be patient.</b> The IP address we will test is <b><?= $_SERVER["REMOTE_ADDR"]; ?></b>.
By proceeding with submitting the form below you grant us permission to scan certain ports on this computer.
</td></tr></table>
<p>
-->
<script type="text/javascript" src="captcha/captcha.js"></script>

Note: You need cookies enabled to sign up or log in.
<p>
<form method="post" action="takesignup.php">
<table border="1" cellspacing=0 cellpadding="10">
<tr><td align="right" class="heading">Desired username:</td><td align=left><input type="text" size="40" name="wantusername" /></td></tr>
<tr><td align="right" class="heading">Pick a password:</td><td align=left><input type="password" size="40" name="wantpassword" /></td></tr>
<tr><td align="right" class="heading">Enter password again:</td><td align=left><input type="password" size="40" name="passagain" /></td></tr>
<tr valign=top><td align="right" class="heading">Email address:</td><td align=left><input type="text" size="40" name="email" />
<table width=250 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded><font class=small>The email address must be valid.
You will receive a confirmation email which you need to respond to. The email address won't be publicly shown anywhere.</font></td></tr>
</td></tr></table>
</td></tr>
  <tr>
    <td>&nbsp;</td>
    <td>
      <div id="captchaimage">
      <a href="<?php echo $_SERVER['PHP_SELF']; ?>" onclick="refreshimg(); return false;" title="Click to refresh image">
      <img class="cimage" src="captcha/GD_Security_image.php?<?php echo time(); ?>" alt="Captcha image" />
      </a>
      </div>
     </td>
  </tr>
  <tr>
      <td class="rowhead">PIN:</td>
      <td>
        <input type="text" maxlength="6" name="captcha" id="captcha" onBlur="check(); return false;"/>
      </td>
  </tr>
<tr><td align="right" class="heading"></td><td align=left><input type=checkbox name=rulesverify value=yes> I have read the site rules page.<br>
<input type=checkbox name=faqverify value=yes> I agree to read the FAQ before asking questions.<br>
<input type=checkbox name=ageverify value=yes> I am at least 13 years old.</td></tr>
<tr><td colspan="2" align="center"><input type=submit value="Sign up! (PRESS ONLY ONCE)" style='height: 25px'></td></tr>
</table>
</form>
<?

stdfoot();

?>