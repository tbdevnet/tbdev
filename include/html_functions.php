<?php

  //-------- Begins a main frame

  function begin_main_frame()
  {
    print("<table class='main' width='750' border='0' cellspacing='0' cellpadding='0'>" .
      "<tr><td class='embedded'>\n");
  }

  //-------- Ends a main frame

  function end_main_frame()
  {
    print("</td></tr></table>\n");
  }

  function begin_frame($caption = "", $center = false, $padding = 10)
  {
    $tdextra = "";
    
    if ($caption)
      print("<h2>$caption</h2>\n");

    if ($center)
      $tdextra .= " align='center'";

    print("<table width='100%' border='1' cellspacing='0' cellpadding='$padding'><tr><td$tdextra>\n");

  }

  function attach_frame($padding = 10)
  {
    print("</td></tr><tr><td style='border-top: 0px'>\n");
  }

  function end_frame()
  {
    print("</td></tr></table>\n");
  }

  function begin_table($fullwidth = false, $padding = 5)
  {
    $width = "";
    
    if ($fullwidth)
      $width .= " width='100%'";
    print("<table class='main'$width border='1' cellspacing='0' cellpadding='$padding'>\n");
  }

  function end_table()
  {
    print("</td></tr></table>\n");
  }
  
	function tr($x,$y,$noesc=0) {
		if ($noesc)
			$a = $y;
		else {
			$a = htmlspecialchars($y);
			$a = str_replace("\n", "<br />\n", $a);
		}
		print("<tr><td class=\"heading\" valign=\"top\" align=\"right\">$x</td><td valign=\"top\" align='left'>$a</td></tr>\n");
	}

?>