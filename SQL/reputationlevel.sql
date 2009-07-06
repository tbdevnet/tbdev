-- 
-- Host: localhost
-- Generation Time: Jul 06, 2009 at 11:46 PM
-- Server version: 5.0.33
-- PHP Version: 5.2.1
-- 
-- Database: `mytbdev`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `reputationlevel`
-- 

CREATE TABLE `reputationlevel` (
  `reputationlevelid` int(11) unsigned NOT NULL auto_increment,
  `minimumreputation` int(10) NOT NULL default '0',
  `level` varchar(250) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`reputationlevelid`),
  KEY `reputationlevel` (`minimumreputation`)
) ENGINE=MyISAM;

-- 
-- Dumping data for table `reputationlevel`
-- 

INSERT INTO `reputationlevel` VALUES (1, -999999, 'is infamous around these parts');
INSERT INTO `reputationlevel` VALUES (2, -50, 'can only hope to improve');
INSERT INTO `reputationlevel` VALUES (3, -10, 'has a little shameless behaviour in the past');
INSERT INTO `reputationlevel` VALUES (4, 0, 'is an unknown quantity at this point');
INSERT INTO `reputationlevel` VALUES (5, 10, 'is on a distinguished road');
INSERT INTO `reputationlevel` VALUES (6, 50, 'will become famous soon enough');
INSERT INTO `reputationlevel` VALUES (7, 150, 'has a spectacular aura about');
INSERT INTO `reputationlevel` VALUES (8, 250, 'is a jewel in the rough');
INSERT INTO `reputationlevel` VALUES (9, 350, 'is just really nice');
INSERT INTO `reputationlevel` VALUES (10, 450, 'is a glorious beacon of light');
INSERT INTO `reputationlevel` VALUES (11, 550, 'is a name known to all');
INSERT INTO `reputationlevel` VALUES (12, 650, 'is a splendid one to behold');
INSERT INTO `reputationlevel` VALUES (13, 1000, 'has much to be proud of');
INSERT INTO `reputationlevel` VALUES (14, 1500, 'has a brilliant future');
INSERT INTO `reputationlevel` VALUES (15, 2000, 'has a reputation beyond repute');
