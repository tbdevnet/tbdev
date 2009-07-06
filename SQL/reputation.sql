-- phpMyAdmin SQL Dump
-- version 2.9.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jul 06, 2009 at 11:44 PM
-- Server version: 5.0.33
-- PHP Version: 5.2.1
-- 
-- Database: `mytbdev`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `reputation`
-- 

CREATE TABLE `reputation` (
  `reputationid` int(11) unsigned NOT NULL auto_increment,
  `reputation` int(10) NOT NULL default '0',
  `whoadded` int(10) NOT NULL default '0',
  `reason` varchar(250) collate utf8_unicode_ci default NULL,
  `dateadd` int(10) NOT NULL default '0',
  `postid` int(10) NOT NULL default '0',
  `userid` mediumint(8) NOT NULL default '0',
  PRIMARY KEY  (`reputationid`),
  KEY `userid` (`userid`),
  KEY `whoadded` (`whoadded`),
  KEY `multi` (`postid`,`userid`),
  KEY `dateadd` (`dateadd`)
) ENGINE=MyISAM;
