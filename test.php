<?php

$http_moz = @getenv('HTTP_X_MOZ');

        if ( isset($http_moz) AND strstr( strtolower($http_moz), 'prefetch' ) )
		{
			if ( PHP_SAPI == 'cgi-fcgi' OR PHP_SAPI == 'cgi' )
			{
				@header('Status: 403 Forbidden');
			}
			else
			{
				@header('HTTP/1.1 403 Forbidden');
			}
			
			print "Prefetching or precaching is not allowed";
			exit();
		}
		
date_default_timezone_set('Europe/London');

if ( strtoupper( substr(PHP_OS, 0, 3) ) == 'WIN' )
				{
					//$file_path = str_replace( ROOT_PATH, "",  $_SERVER['SCRIPT_FILENAME'] );
					$file_path = str_replace( "\\", "/", dirname(__FILE__) );
					
					//$data['file_path'] = str_replace( "/\\", "\\", $_SERVER['SCRIPT_FILENAME'] );
				}
				else
				{
          $file_path = dirname(__FILE__);
				}
define('ROOT_PATH', $file_path);
echo '<pre>';
echo ROOT_PATH.'<br />';
echo $file_path.'<br />';
echo $data['file_path'].'<br />';
echo $_SERVER['SCRIPT_FILENAME'].'<br />';
echo '</pre>';
print_r(localtime(time(), TRUE));

if(mail('chrispyzone@gmail.com', 'football stinks!', 'hi, how the heck are you?', "From: admin@localhost")) {
echo 'mail sent';
}else{
echo 'not sent';
}
echo '<br />';

$size = getimagesize("http://www.tbdev.net/style_images/microskin/wwwskin.jpg");
print_r($size);
?>