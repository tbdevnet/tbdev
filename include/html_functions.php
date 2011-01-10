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

  //-------- Begins a main frame

  function begin_main_frame()
  {
    return "<table class='main' width='750px' border='0' cellspacing='0' cellpadding='0'>" .
      "<tr><td class='embedded'>\n";
  }

  //-------- Ends a main frame

  function end_main_frame()
  {
    return "</td></tr></table>\n";
  }

  function begin_frame($caption = "", $center = false, $padding = 10)
  {
    $tdextra = "";
    $htmlout = '';
    if ($caption)
      $htmlout .= "<h2>$caption</h2>\n";

    if ($center)
      $tdextra .= " align='center'";

    $htmlout .= "<table width='100%' border='1' cellspacing='0' cellpadding='$padding'><tr><td$tdextra>\n";
    
    return $htmlout;
  }

  function attach_frame($padding = 10)
  {
    print("</td></tr><tr><td style='border-top: 0px'>\n");
  }

  function end_frame()
  {
    return "</td></tr></table>\n";
  }

  function begin_table($fullwidth = false, $padding = 5)
  {
    $width = "";
    $htmlout = '';
    
    if ($fullwidth)
      $width .= " width='100%'";
    $htmlout .= "<table class='main'$width border='1' cellspacing='0' cellpadding='$padding'>\n";
    
    return $htmlout;
  }

  function end_table()
  {
    return "</table>\n";
  }
  
  //  function end_table()
//  {
//    print("</td></tr></table>\n");
//  }
  
	function tr($x,$y,$noesc=0) {
		if ($noesc)
			$a = $y;
		else {
			$a = htmlsafechars($y);
			$a = str_replace("\n", "<br />\n", $a);
		}
		
		return "<tr><td class='heading' valign='top' align='right'>$x</td><td valign='top' align='left'>$a</td></tr>\n";
	}


  //-------- Inserts a smilies frame

function insert_smilies_frame()
  {
    global $smilies, $TBDEV;
    
    $htmlout = '';
    
    $htmlout .= begin_frame("Smilies", true);

    $htmlout .= begin_table(false, 5);

    $htmlout .= "<tr><td class='colhead'>Type...</td><td class='colhead'>To make a...</td></tr>\n";

    foreach($smilies as $code => $url)
    {
      $htmlout .= "<tr><td>$code</td><td><img src=\"{$TBDEV['pic_base_url']}smilies/{$url}\" alt='' /></td></tr>\n";
    }
    
    $htmlout .= end_table();

    $htmlout .= end_frame();
    
    return $htmlout;
}


function bbcode2textarea( $submit='', $body='', $title='' ) {

  global $TBDEV;
  
  $htmlout = '';
  $emot_dir = $TBDEV['pic_base_url'].'smilies/';
  $htmlout = "
  <table width='800px'border='1' cellspacing='0' cellpadding='10px'>";
  
  if( $title != '' )
  {
    $htmlout .= "<tr>
    <td align='center'>
    <input style='width:650px;' type='text' name='title' size='50' onfocus=\"if(this.value == '{$title}') { this.value = ''; }\" value='{$title}' />
    </td>
    </tr>";
  }
  
  $htmlout .= "
  <tr>
  <td align='center'>
  <textarea style='width:650px;' name='body' cols='55' rows='15'>{$body}</textarea>
  </td>
  </tr>
  
  <tr>
  <td align='center'>
  <input type='button' value='b' style='font-weight:bold;width:25px;' onclick=\"addText('body', '[b]', '[/b]');\" />
  <input type='button' value='i' style='font-style:italic;width:25px;' onclick=\"addText('body', '[i]', '[/i]');\" />
  <input type='button' value='u' style='text-decoration:underline;width:25px;' onclick=\"addText('body', '[u]', '[/u]');\" />
  <input type='button' value='s' style='text-decoration:underline;width:25px;' onclick=\"addText('body', '[s]', '[/s]');\" />
  <input type='button' value='http' name='url' style='width:30px;' onclick=\"tag_url();\" />
  <input type='button' value='mail' style='width:35px;' onclick=\"addText('body', '[mail]', '[/mail]');\" />
  <input type='button' value='img' style='width:30px;' onclick=\"tag_image();\" />
  <input type='button' value='left' style='width:45px;' onclick=\"addText('body', '[left]', '[/left]');\" />
  <input type='button' value='center' style='width:45px;' onclick=\"addText('body', '[center]', '[/center]');\" />
  <input type='button' value='right' style='width:45px;' onclick=\"addText('body', '[right]', '[/right]');\" />
  <input type='button' value='list' style='width:40px;' onclick=\"tag_list();\" />
  <input type='button' value='code' style='width:40px;' onclick=\"addText('body', '[code]', '[/code]');\" />
  <input type='button' value='quote' style='width:45px;' onclick=\"addText('body', '[quote]', '[/quote]');\" />
  <br />
  
  <select name='ffont' style='font-size:1em;height:2em;line-height:100%' onchange=\"alterfont(this.options[this.selectedIndex].value, 'font');\">
       <option value='0'>Font</option>
       <option value='Arial' style='font-family: Arial;'>Arial</option>
       <option value='Times' style='font-family: Times;'>Times</option>

       <option value='Courier' style='font-family: Courier;'>Courier</option>
       <option value='Impact' style='font-family: Impact;'>Impact</option>
       <option value='Geneva' style='font-family: Geneva;'>Geneva</option>
       <option value='Optima' style='font-family: Optima;'>Optima</option>
       </select>

  <select name='fsize' style='font-size:1em;height:2em;line-height:100%' onchange=\"alterfont(this.options[this.selectedIndex].value, 'size');\">
      <option value='0'>Size</option>
      <option style='font-size:1em;line-height:100%' value='1'>Small</option>
      <option style='font-size:2em;line-height:100%' value='2'>Large</option>
      <option style='font-size:3em;line-height:100%' value='3'>Largest</option>
      <option style='font-size:4em;line-height:100%' value='4'>Largest</option>
      </select>

  <select name='fcolor' style='font-size:1em;height:2em;line-height:100%' onchange=\"alterfont(this.options[this.selectedIndex].value, 'color');\">
       <option value='0'>Color</option>
       <option value='blue' style='color: blue;'>Blue</option>
       <option value='red' style='color: red;'>Red</option>
       <option value='purple' style='color: purple;'>Purple</option>
       <option value='orange' style='color: orange;'>Orange</option>
       <option value='yellow' style='color: yellow;'>Yellow</option>
       <option value='gray' style='color: gray;'>Gray</option>
       <option value='green' style='color: green;'>Green</option>

       </select>
  </td>
  </tr>
  
  
  <tr>
  <td align='center'>
  <img style='vertical-align:bottom;' src='{$emot_dir}smile1.gif' alt='smiley' onclick=\"insertText('body', ' :-)');\" />
  <img style='vertical-align:bottom;' src='{$emot_dir}wink.gif' alt='smiley' onclick=\"insertText('body', ' :wink:');\" />
  <img style='vertical-align:bottom;' src='{$emot_dir}noexpression.gif' alt='smiley' onclick=\"insertText('body', ' :-|');\" />
  <img style='vertical-align:bottom;' src='{$emot_dir}sad.gif' alt='smiley' onclick=\"insertText('body', ' :-(');\" />
  <img style='vertical-align:bottom;' src='{$emot_dir}ohmy.gif' alt='smiley' onclick=\"insertText('body', ' :-O');\" />
  <img style='vertical-align:bottom;' src='{$emot_dir}tongue.gif' alt='smiley' onclick=\"insertText('body', ' :-P');\" />
  <img style='vertical-align:bottom;' src='{$emot_dir}cool2.gif' alt='smiley' onclick=\"insertText('body', ' :cool:');\" />
  <img style='vertical-align:bottom;' src='{$emot_dir}grin.gif' alt='smiley' onclick=\"insertText('body', ' :-D');\" />
  <img style='vertical-align:bottom;' src='{$emot_dir}angry.gif' alt='smiley' onclick=\"insertText('body', ' :angry:');\" />
  <img style='vertical-align:bottom;' src='{$emot_dir}wub.gif' alt='smiley' onclick=\"insertText('body', ' :wub:');\" />

  &nbsp;<span class='btn'><a href='javascript: more_emoticons();'>More Smilies</a></span>
  <br /><br />
  
  </td>
  </tr>";
  if( $submit != '' )
  {
  $htmlout .= "<tr>
  <td align='center'>
  <input type='submit' name='postquickreply' value='{$submit}' class='' />
  </td>
  </tr>";
  }
  $htmlout .="</table>
  <br />";
  
  return $htmlout;
}

?>