SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


INSERT INTO cleanup (clean_id, clean_title, clean_file, clean_time, clean_increment, clean_cron_key, clean_log, clean_desc, clean_on) VALUES
(1, 'Normalize Torrents', 'normalize_torrents.php', 1294437635, 50, 'd6704d582b136ea1ed13635bb9059f57', 1, 'bla blah blah', 0),
(2, 'Frontpage Stats Build', 'frontpage_stats.php', 1294440400, 10, '6272317b90846180504dcf7c28902cb2', 0, 'Thsi rebuilds the stats on index page or wherever else you decide to use them!', 0),
(3, 'Delete Old Torrents', 'delete_old_torrents.php', 0, 86400, '560f666531f0b409a577236a5ec571dc', 0, 'Deletes torrents older than 28 days', 0),
(4, 'Expire Old Peers', 'expire_peers.php', 0, 900, '3a784b3948266707895dfbc31e19c6b0', 0, 'Cleans out old peers from the peers table based on ''clean interva'' x 1.3', 0),
(5, 'Expire Readposts', 'expire_readposts.php', 0, 86400, 'c254efb9042f1d1a32d123b8e50d9012', 0, 'Cleans all readposts from topics/posts etc', 0),
(6, 'Expire Old User Accounts', 'expire_user_accounts.php', 0, 86400, '2b2786a667e6234e107c4873cb5126a9', 0, 'Expires old accounts every so often.', 0),
(7, 'Updates Forum & Topic Counts', 'forum_topic_stats.php', 0, 900, '08eefec1e363ab5309589cb8f3214a69', 0, 'Updates forum and topic counts etc.', 0),
(8, 'Expires Old Inactive User Accounts', 'incative_user_accounts.php', 0, 86400, 'd4654a8a66e26bbbaff130eab0d5f3d6', 0, 'Deletes old inactive accounts older than 42 days', 0),
(9, 'Make Dead Torrents Invisible', 'invisibilize_torrents.php', 0, 86400, 'b5514005c7e4735e1f636ba59bd47cc8', 0, 'Hides dead torrents from the browse list but does not delete them.', 0),
(10, 'AutoOptimize MySQL Database', 'mysql_optimise_clean.php', 0, 2592000, '7d00f70b99ed8c0ee8056c03fd5e6c75', 0, 'Optimize all mysql tables with overhead.', 0),
(11, 'Kill MySQL Processes', 'mysql_process_kill.php', 0, 86400, 'a03a77aceadd17109542effae0d6f41a', 0, 'Kills all mysql processes over 60 seconds', 0),
(12, 'Normalizes Old Torrents', 'normalize_torrents.php', 0, 900, '575513659ee6a52a358630c943518c52', 0, 'Deletes torrents not in the DB and deletes torrent ID''s not in the torrents directory.', 0),
(13, 'Updates Torrent Comments', 'toorent_comment_update.php', 0, 86400, 'c273ee4cb03c85b3fa14f1e8e70c0f71', 0, 'Updates torrent comment counts etc etc.', 0),
(14, 'Promote & Demote Users', 'update_power_users.php', 0, 86400, '36706f9087d357c3bab312d316462375', 0, 'Promotes users to power users and demotes users to erm, users!', 0),
(15, 'Update User Warnings', 'update_user_warnings.php', 0, 17800, '9f6dec80c3870d5fdd5e1e5e48d57c33', 0, 'Removes and updates old warnings, and also instigates new warnings etc etc.', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
