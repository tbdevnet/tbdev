<?php
require_once "include/bittorrent.php";
require_once "include/html_functions.php";


dbconn();
stdhead("Staff");
begin_main_frame();
?>
<? begin_frame("Staff"); ?>
<? end_frame(); ?>

<? begin_frame("Current uploaders"); ?>
<? end_frame(); ?>

<? begin_frame("Moderators"); ?>
<? end_frame(); ?>

<?
end_main_frame();
stdfoot();
?>