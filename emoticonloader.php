<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		
		<html xmlns='http://www.w3.org/1999/xhtml'>

		<head>

			<meta name='generator' content='TBDev.net' />
			<meta http-equiv='Content-Language' content='en-us' />
			<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
			
			<title>Emoticons Extra</title>
			<link rel='stylesheet' href='1.css' type='text/css' />
		</head>
    
    <body>
    <script type="text/javascript">
<!--
	function add_smilie(code)
	{
		opener.document.getElementById('bbcode2text').body.value += ' ' + code + ' ';
		//return true;
	}
//-->
</script>

    <table>
    
<?php
require_once "include/emoticons.php";   

    foreach ($smilies as $k => $v ) {
    
    print "<tr>
	  <td align='center' class='row1' valign='middle'><a href=\"javascript:add_smilie('$k')\">$k</a></td>
	  <td align='center' class='row2' valign='middle'><a href=\"javascript:add_smilie('$k')\"><img src='./pic/smilies/$v' border='0' style='vertical-align:middle;' alt='$v' title='$v' /></a></td>
   </tr>";
    }

?>
    </table>
    </body>
    </html>