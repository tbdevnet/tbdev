<?php
/*
+------------------------------------------------
|   TBDev.net BitTorrent Tracker PHP
|   =============================================
|   by CoLdFuSiOn
|   (c) 2003 - 2011 TBDev.Net
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
error_reporting  (E_ERROR | E_WARNING | E_PARSE);
set_magic_quotes_runtime(0);

define( 'INSTALLER_ROOT_PATH', './' );

define( 'TBDEV_ROOT_PATH', '../' );

define( 'CACHE_PATH' , TBDEV_ROOT_PATH );

define( 'REQ_PHP_VER' , '5.2.1' );

define( 'TBDEV_REV' , 'TBDev 2009.rev.295' );

$installer = new installer;

class installer
{
	var $htmlout = "";

  var $VARS = array();


function installer() {
    
    $this->VARS = array_merge( $_GET, $_POST);
    
    if ( file_exists( INSTALLER_ROOT_PATH.'install.lock') )
    {
      $this->install_error("This installer is locked!
      <br />You cannot install unless you delete the 'install/install.lock' file");
      exit();
    }


  switch($this->VARS['progress'])
  {
    case '1':
      $this->do_step_one();
      break;
      
    case '2':
      $this->do_step_two();
      break;
      
    case '3':
      $this->do_step_three();
      break;
        
    case '4':
      $this->do_step_four();
      break;
         
    case '5':
      $this->do_step_five();
      break;
           
    case '6':
      $this->do_step_six();
      break;
      
    case 'end':
      $this->do_end();
      break;
      
    default:
      $this->do_start();
      break;
  }

}



function do_start() {
	
	
	$this->stdhead('Welcome');
	
	$this->htmlout .= "<div class='box_content'>

							<h2>Welcome to the TBDev Tracker Installer</h2><br /><br />
							
							<p>Before we go any further, please ensure that all the files have been uploaded, and that the file 'config.php' has suitable permissions to allow this script to write to it ( 0666 should be sufficient ).</p>
							   <br /><br />
							   <h3>".TBDEV_REV." requires PHP ".REQ_PHP_VER." or better and an MYSQL database.</h3>
							   <br /><br />
							   You will also need the following information:
							   <ul>
							   <li>Your mySQL database name</li>
							   <li>Your mySQL username</li>
							   <li>Your mySQL password</li>
							   <li>Your mySQL host address (usually localhost)</li>
							   </ul>
							   <br />
							   Once you have clicked on proceed, you will be taken to a form to enter information the installer needs to set up your tracker.
							   <br /><br />
							   <strong>PLEASE NOTE: USING THIS INSTALLER WILL DELETE ANY CURRENT TBDEV DATABASE AND OVERWRITE ANY CONFIG.PHP FILE</strong>
							   ";
	
	$warnings   = array();
	
	$checkfiles = array( 
              INSTALLER_ROOT_PATH."sql",
              TBDEV_ROOT_PATH ."include/config.php"
              );
					  
	$writeable  = array( 
              TBDEV_ROOT_PATH."include/config.php",
              TBDEV_ROOT_PATH."torrents"
              );
	
	foreach ( $checkfiles as $cf )
	{
		if ( ! file_exists($cf) )
		{
			$warnings[] = "Cannot locate the file '$cf'.";
		}
	}
	
	foreach ( $writeable as $cf )
	{
		if ( ! is_writeable($cf) )
		{
			$warnings[] = "Cannot write to the file '$cf'. Please CHMOD to 0777.";
		}
	}
	
	$phpversion = phpversion();
	
	if ($phpversion < REQ_PHP_VER)
	{
		$warnings[] = "<strong>TBDev Tracker requires PHP Version ".REQ_PHP_VER." or better.</strong>";
	}
	
	if ( ! function_exists('get_cfg_var') )
	{
		$warnings[] = "<strong>Your PHP installation isn't sufficient to run TBDev Tracker.</strong>";
	}
	
	if ( function_exists('ini_get') AND @ini_get("safe_mode") )
	{
		$warnings[] = "<strong>TBDev Tracker won't run when safe_mode is on.</strong>";
	}
	
	if( function_exists( 'gd_info' ) )
  {
    $gd	= gd_info();
    $fail	= true;
    
    if( $gd["GD Version"] )
    {
      preg_match( "/.*?([\d\.]+).*?/", $gd["GD Version"], $matches );
      
      if( $matches[1] )
      {
        $gdversions	= version_compare( '2.0', $matches[1], '<=' );
        
        if( !$gdversions )
        {
          $fail = false;
        }
      }
    }

    !$fail ? $warnings[] = "TBDev.net requires GD library version 2. The version on your server is'{$gd['GD Version']}'.  Find the upgrade here <a href='http://us.php.net/manual/en/image.setup.php'>libgd library</a>." : false;
  }
	
	$ext = get_loaded_extensions();
	
	if( ! in_array('mysql', $ext) )
	{
    $warnings[] = "<strong>Your server doesn't appear to have a MySQL library, you will need this before you can continue.</strong>";
	}
	
	if( get_magic_quotes_gpc() ) 
	{
    $warnings[] = "<strong>This feature has been DEPRECATED as of PHP 5.3.0. Relying on this feature is highly discouraged.</strong> <a href='http://php.net/manual/en/security.magicquotes.php'>About Magic Quotes</a>";
  }
	
	
	
	
	if ( count($warnings) > 0 )
	{
	
		$err_string = implode( "<br /><br />", $warnings );
	
		$this->htmlout .= "<br /><br />
							    <div class='error-box' style='width: 500px;'>
							     <strong>Warning!
							     The following errors must be rectified before continuing!</strong>
								 <br /><br />
								 $err_string
							    </div>";
	}
	else
	{
		$this->htmlout .= "<br /><br /><div class='proceed-btn-div'><a href='index.php?progress=1'><span class='btn'>PROCEED</span></a></div>";
	}
	
	$this->htmlout .= "</div>";
	
	$this->htmlout();
}




function do_step_one() {
	
	$this->stdhead('Set Up form');
	
	$this->htmlout .= "
	<div class='box_content'>
	
	<form action='index.php' method='post'>
	<div>
	<input type='hidden' name='progress' value='2' />
	</div>
	
	<h2>Your Server Environment</h2>";
	
	$this->htmlout .= "
	<p>This section requires you to enter your SQL information. If in doubt, please check with your webhost before asking for support. You may choose to enter an existing database name,if not - you must create a new database before continuing.</p>
	
	<fieldset>
    <legend><strong>MySQL Host</strong></legend>
      <input type='text' name='mysql_host' value='localhost' />
      (localhost is usually sufficient)
  </fieldset>
	
	<fieldset>
	  <legend><strong>MySQL Database Name</strong></legend>
	  <input type='text' name='mysql_db' value='' />
	</fieldset>
	
	<fieldset>
	  <legend><strong>SQL Username</strong></legend>
	  <input type='text' name='mysql_user' value='' />
	</fieldset>
	
	<fieldset>
	  <legend><strong>SQL Password</strong></legend>
	  <input type='text' name='mysql_pass' value='' />
	</fieldset>
	
	<div class='proceed-btn-div'>
	<input class='btn' type='submit' value='PROCEED' /></div>
	
	</form>
	</div>";

						 
	$this->htmlout();
						 
}



function do_step_two() {
	
	$in = array('mysql_host','mysql_db','mysql_user', 'mysql_pass');
	
	foreach($in as $out)
	{
		if ($this->VARS[ $out ] == "")
		{
			$this->install_error("You must complete all of the form");
		}
	}
	
	if (!@mysql_connect($this->VARS['mysql_host'], $this->VARS['mysql_user'], $this->VARS['mysql_pass']))
    {
      $this->install_error( "Connection error:<br /><br />[" . mysql_errno() . "] dbconn: mysql_connect: " . mysql_error());
    }
    //mysql_select_db($TBDEV['mysql_db']) or die('dbconn: mysql_select_db: ' . mysql_error());
    //mysql_set_charset('utf8');
    
	if(!mysql_select_db($this->VARS['mysql_db']))
  {
    if(!mysql_query("CREATE DATABASE {$this->VARS['mysql_db']} DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci"))
    {
      $this->install_error( "Unable to create database" );
      exit();
    }
    
    mysql_select_db($this->VARS['mysql_db']);
    
  }
  else
  {
    mysql_select_db($this->VARS['mysql_db']);
  }
	
	
	require_once( INSTALLER_ROOT_PATH.'sql/mysql_tables.php' );
	require_once( INSTALLER_ROOT_PATH.'sql/mysql_inserts.php' );
	
	foreach( $TABLE as $q )
	{
	   preg_match("/CREATE TABLE (\S+) \(/", $q, $match);
	   
	   if ($match[1])
	   {
		   mysql_query( "DROP TABLE {$match[1]}" );
	   }
   
	   if ( ! mysql_query($q) )
	   {
		   $this->install_error($q."<br /><br />".mysql_error());
	   }
  }

	foreach( $INSERT as $q )
	{
		if ( ! mysql_query($q) )
    {
      $this->install_error($q."<br /><br />".mysql_error());
    }
	}

	
	$this->stdhead('Database Success!');
	
	$this->htmlout .= "
	<div class='box_content'>
	
	<h2>Database Success</h2>
	
	<strong>Your database has been installed!</strong>
	<br /><br />
	The installation process is almost complete.
	<br />
	The next step will configure the tracker settings.
	If you already have a config.php file, you should go no further.
	Any existing config.php will be overwritten!
	<br /><br />
	
	<form action='index.php' method='post'>
	<div>
	<input type='hidden' name='progress' value='3' />
	<input type='hidden' name='mysql_host' value='{$this->VARS['mysql_host']}' />
	<input type='hidden' name='mysql_db' value='{$this->VARS['mysql_db']}' />
	<input type='hidden' name='mysql_user' value='{$this->VARS['mysql_user']}' />
	<input type='hidden' name='mysql_pass' value='{$this->VARS['mysql_pass']}' />
	</div>
	<div class='proceed-btn-div'>
	<input class='btn' type='submit' value='PROCEED' /></div>
	</form>
	</div>";
						 
	$this->htmlout();
}



function do_step_three() {
	
	$base_url = str_replace( "/install/index.php", "", $_SERVER['HTTP_REFERER']);
	$base_url = str_replace( "/install/" , "", $base_url);
	$base_url = str_replace( "/install"  , "", $base_url);
	$base_url = str_replace( "index.php" , "", $base_url);
	
  if ( ! $base_url )
  {
    $base_url = substr($_SERVER['SCRIPT_NAME'],0, -17);
    
      if ($base_url == '')
      {
        $base_url == '/';
      }
      
      $base_url = 'http://'.$_SERVER['SERVER_NAME'].$base_url; 
  }
	
	$base_url = preg_replace( "#/$#", "", $base_url );
	$ann_url = $base_url.'/announce.php';
	
	$this->stdhead('Config Set Up form');
	
	$this->htmlout .= "
	<div class='box_content'>
	
	<form action='index.php' method='post'>
	<div>
	<input type='hidden' name='progress' value='4' />
	</div>
	
	<h2>Setting up your Config file</h2>";
	
	$this->htmlout .= "
	<p>This section requires you to enter your all information. If in doubt, please check with your webhost before asking for support. Please note: Any settings you enter here will overwrite any settings in your config.php file!</p>
	
	<fieldset>
    <legend><strong>MySQL Settings</strong></legend>
    
    <div class='form-field'>  
    <label>MySQL Host</label>
    <input type='text' name='mysql_host' value='{$this->VARS['mysql_host']}' /><br />
    </div>
    
    <div class='form-field'>
    <label>MySQL Database Name</label>
	  <input type='text' name='mysql_db' value='{$this->VARS['mysql_db']}' /><br />
    </div>

	  <div class='form-field'>  
    <label>SQL Username</label>
	  <input type='text' name='mysql_user' value='{$this->VARS['mysql_user']}' /><br />
    </div>
	
    <div class='form-field'>  
    <label>SQL Password</label>
	  <input type='text' name='mysql_pass' value='{$this->VARS['mysql_pass']}' /><br />
	  </div>
	</fieldset>
	
	<fieldset>
	  <legend><strong>General Settings</strong></legend>
	  
	  <div class='form-field'>  
    <label>Base URL</label>
	  <input type='text' name='base_url' value='{$base_url}' />
	  <br /><span class='form-field-info'>Check that this setting is correct, as it was automagic!</span>
	  </div>
	  
	  <div class='form-field'>  
    <label>Announce URL</label>
	  <input type='text' name='ann_url' value='{$ann_url}' />
	  <br /><span class='form-field-info'>Check that this setting is correct, as it was automagic!</span>
	  </div>
	  
	  <div class='form-field'>  
    <label>Cookie Prefix</label>
	  <input type='text' name='cookie_prefix' value='_tbdev' />
	  </div>
	  
	  <div class='form-field'>  
    <label>Cookie Path</label>
	  <input type='text' name='cookie_path' value='' />
	  </div>
	  
	  <div class='form-field'>  
    <label>Cookie Domain</label>
	  <input type='text' name='cookie_domain' value='' />
	  </div>
	  
	  <div class='form-field'>  
    <label>Site Email</label>
	  <input type='text' name='site_email' value='' />
	  </div>
	  
	  <div class='form-field'>  
    <label>Site Name</label>
	  <input type='text' name='site_name' value='' />
	  </div>
	  
	  <div class='form-field'>  
    <label>Language</label>
	  <input type='text' name='language' value='en' />
	  </div>
	  
	  <div class='form-field'>  
    <label>Character Set</label>
	  <input type='text' name='char_set' value='UTF-8' />
	  </div>
	  
	</fieldset>
	
	<div class='proceed-btn-div'>
	<input class='btn' type='submit' value='PROCEED' /></div>
	
	</form>
	</div>";

						 
	$this->htmlout();
						 
}


function do_step_four() {
	
	$DB = "";
	
	$NEW_INFO = array();
	
	$in = array('base_url','mysql_host','mysql_db','mysql_user', 'mysql_pass', 'ann_url','site_email', 'site_name', 'language', 'char_set');
	//print_r($this->VARS); exit;
	foreach($in as $out)
	{
		if ($this->VARS[ $out ] == "")
		{
			$this->install_error("You must complete all of the form.");
		}
	}
	
	// open config_dist.txt
	$conf_string = file_get_contents('./config_dist.php');
	
  $placeholders = array('<#mysql_host#>', '<#mysql_db#>', '<#mysql_user#>', '<#mysql_pass#>', '<#ann_url#>', '<#base_url#>', '<#cookie_prefix#>','<#cookie_path#>','<#cookie_domain#>', '<#site_email#>', '<#site_name#>', '<#language#>', '<#char_set#>');
  
  $replacements = array($this->VARS['mysql_host'], $this->VARS['mysql_db'], $this->VARS['mysql_user'], $this->VARS['mysql_pass'], $this->VARS['ann_url'], $this->VARS['base_url'], $this->VARS['cookie_prefix'], $this->VARS['cookie_path'], $this->VARS['cookie_domain'], $this->VARS['site_email'], $this->VARS['site_name'], $this->VARS['language'], $this->VARS['char_set']);

	$conf_string = str_replace($placeholders, $replacements, $conf_string);
	
	if ( $fh = fopen( TBDEV_ROOT_PATH.'include/config.php', 'w' ) )
	{
		fputs($fh, $conf_string, strlen($conf_string) );
		fclose($fh);
	}
	else
	{
		$this->install_error("Could not write to 'config.php'");
	}
	
	// announce now
	$ann_string = file_get_contents('./announce_dist.php');
	$ann_string = str_replace($placeholders, $replacements, $ann_string);
	
	if ( $fh = fopen( TBDEV_ROOT_PATH.'announce.php', 'w' ) )
	{
		fputs($fh, $ann_string, strlen($ann_string) );
		fclose($fh);
	}
	else
	{
		$this->install_error("Could not write to 'announce.php'");
	}
	
	$this->stdhead('Wrote Config Success!');
	
	$this->htmlout .= "
	<div class='box_content'>
	<h2>Success! Your configuration file and Announce file was written successfully!</h2>
	<br /><br />
	The next step will create your Sysop account.
	<br /><br />
	<div class='proceed-btn-div'><a href='index.php?progress=5'><span class='btn'>CREATE ACCOUNT</span></a></div>
	</div>";
						 
	$this->htmlout();
}




function do_step_five() {
	
	$this->stdhead('Config Set Up form');
	
	$this->htmlout .= "
	<div class='box_content'>
	
	<form action='index.php' method='post'>
	<div>
	<input type='hidden' name='progress' value='6' />
	</div>
	
	<h2>Creating your sysop account</h2>";
	
	$this->htmlout .= "
	<p>This section requires you to enter all your information.</p>
	
	<fieldset>
    <legend><strong>Sysop Account Details</strong></legend>
    
    <div class='form-field'>  
    <label>User Name</label>
    <input type='text' name='username' value='' /><br />
    </div>
    
    <div class='form-field'>
    <label>Password One</label>
	  <input type='text' name='pass' value='' /><br />
    </div>

	  <div class='form-field'>  
    <label>Password Two</label>
	  <input type='text' name='pass2' value='' /><br />
    </div>
	
    <div class='form-field'>  
    <label>Email Address</label>
	  <input type='text' name='email' value='' /><br />
	  </div>
	</fieldset>
	
	<div class='proceed-btn-div'>
	<input class='btn' type='submit' value='PROCEED' /></div>
	
	</form>
	</div>";

						 
	$this->htmlout();
}



function do_step_six() {
	
	$in = array('username','pass','pass2','email');
	
	foreach($in as $out)
	{
		if ($this->VARS[ $out ] == "")
		{
			$this->install_error("You must complete all of the form fields!");
		}
	}
	
  if ($this->VARS['pass2'] != $this->VARS['pass'])
	{
		$this->install_error("Your passwords did not match");
	}
	
	
	require_once(TBDEV_ROOT_PATH.'include/config.php');
	
	if (!@mysql_connect($TBDEV['mysql_host'], $TBDEV['mysql_user'], $TBDEV['mysql_pass']))
  {
    $this->install_error( "Connection error:<br /><br />[" . mysql_errno() . "] dbconn: mysql_connect: " . mysql_error());
  }

  
    
	if(!mysql_select_db($TBDEV['mysql_db']))
  {
    $this->install_error( "Unable to select database" );
  }
    
	@mysql_set_charset('utf8');
	
	$secret = $this->mksecret();
  $wantpasshash = $this->make_passhash( $secret, md5($this->VARS['pass']) );
	
	$user = array (	
                'id'				=>	1,
								'username'				=>	"{$this->VARS['username']}",
								'passhash'			=>	"$wantpasshash",
								'secret'				=>	"$secret",
								'email'		=>	"{$this->VARS['email']}",
								'status'				=>	"confirmed",
								'class'				=>	6,
								'added'		=>	time(),
								'time_offset'		=>	0,
								'dst_in_use'	=>	1
							);
	
	foreach( $user as $k => $v )
	{
    $user[ $k ] = "'".mysql_real_escape_string($v)."'";
	}
	
	$query = "INSERT INTO users (" .implode(', ', array_keys($user)). ") VALUES (". implode(', ', array_values($user)) .")";

	//print $query; exit;     
	if ( ! mysql_query($query) )
	{
		$this->install_error($query."<br /><br />".mysql_error());
	}

	
	$this->stdhead('Account Success!');
	
	$this->htmlout .= "
	<div class='box_content'>
	<h2>Success! Your sysop account was successfully created!</h2>
	<br /><br />
	The installation process is almost complete.
  The next step will do some investigation into your system state and try to chmod the correct directories.
	<br /><br />
	You may however, have to manually chmod directories that the installer cannot!
	<div class='proceed-btn-div'><a href='index.php?progress=end'><span class='btn'>FINISH INSTALL</span></a></div>
	</div>";
						 
	$this->htmlout();
}



function do_end() {

	if ($FH = @fopen( INSTALLER_ROOT_PATH.'install.lock', 'w' ) )
	{
		@fwrite( $FH, date(DATE_RFC822), 40 );
		@fclose($FH);
		
		@chmod( INSTALLER_ROOT_PATH.'install.lock', 0666 );
		
		$this->stdhead('Install Complete!');
	
		$txt = "Although the installer is now locked (to re-install, remove the file 'install.lock'), for added security, please remove the index.php file before continuing.
			 <br /><br />
			 <div style='text-align: center;'><a href='../login.php'>Log into tracker</a></div>";
	}
	else
	{
		$this->stdhead('Install Complete!');
		
		$txt = "PLEASE REMOVE THE INSTALLER ('index.php') BEFORE CONTINUING!<br />
		Not doing this will open you up to a situation where anyone could delete your tracker &amp; data!
				<br /><br />
				<div style='text-align: center;'><a href='../login.php'>Log into tracker</a></div>";
	}
	
	$warn = '';
	
	if( !@chmod( TBDEV_ROOT_PATH.'include/config.php', 0644) )
	{
    $warn .= "<br />Warning, please chmod include/config.php to 0644 via ftp or shell.";
	}
	
	if( !@chmod( TBDEV_ROOT_PATH.'announce.php', 0644) )
	{
    $warn .= "<br />Warning, please chmod announce.php to 0644 via ftp or shell.";
	}
	
	$this->htmlout .= "
	<div class='box_content'>
	<h2>Installation Successfully Completed!</h2>
	<br />
	<strong>The installation is now complete!</strong>
	{$warn}
	<br /><br />
	{$txt}
	</div>";
						 
	$this->htmlout();
	
	
	
}
////////////////////////////////////////////////////////////
/////////////    WORKER FUNCTIONS //////////////////////////
////////////////////////////////////////////////////////////

function install_error($msg="") {

	
	$this->stdhead('Warning!');
	
	$this->htmlout .= "<div class='error-box'>
						     <h2>Warning!</h2>
						     <br /><br />
						     <h3>The following errors must be rectified before continuing!</h3>
						     <br />Please <a href='javascript:history.back()'><span class='btn'>go back</span></a> and try again!
						     <br /><br />
						     $msg
						    </div>";
	
	
	
	$this->htmlout();
}


	
function htmlout() {

		echo $this->htmlout;
		echo "</div>
		<div id='siteInfo'><p class='center'>
    <a href='http://www.tbdev.net'><img src='./img/tbdev_btn_red.png' alt='Powered By TBDev &copy;2010' title='Powered By TBDev &copy;2010' /></a></p>
    </div>

    </body></html>";
		exit();
}
	


function stdhead($title="") {
	
		$this->htmlout = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"
        \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
    <html xmlns=\"http://www.w3.org/1999/xhtml\">

		<head>

			<meta name='generator' content='TBDev.net' />
			<meta http-equiv='Content-Language' content='en-us' />
			<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
			
			<title>TBDEV.NET :: {$title}</title>
			<link rel='stylesheet' href='1.css' type='text/css' />

		</head>
    
    <body>

				 
			<div id='masthead'>
      <div id='logostrip'>
      <div class='text-header'>TBDev.net Installer</div>
      </div>
      </div>
			<div>";
				  	   
}



function mksecret($len=5) {
		$salt = '';
		
		for ( $i = 0; $i < $len; $i++ )
		{
			$num   = rand(33, 126);
			
			if ( $num == '92' )
			{
				$num = 93;
			}
			
			$salt .= chr( $num );
		}
		
		return $salt;
}
	


function make_passhash($salt, $md5_once_password) {
		return md5( md5( $salt ) . $md5_once_password );
}


} //end class
?>