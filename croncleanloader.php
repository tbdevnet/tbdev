<?php

define('IN_TBDEV_CRON', true);

  if( !isset($argv) OR !is_array($argv) OR (count($argv) != 2) OR !preg_match('/^[0-9a-fA-F]{32}$/i', $argv[1]) )
  {
    exit('Go away!');
  }

  require_once "include/cronclean.php";


?>