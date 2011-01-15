<?php

$INSERT[] = "INSERT INTO avps (arg, value_s, value_i, value_u) VALUES
('lastcleantime', '', 0, 1247059621),
('seeders', '', 0, 1),
('leechers', '', 0, 0),
('loadlimit', '12.5-1246045258', 0, 0)";



$INSERT[] = "INSERT INTO categories (id, name, image, cat_desc) VALUES
(1, 'Appz/PC ISO', 'cat_apps.gif', 'No Description'),
(2, 'Games/PC ISO', 'cat_games.gif', 'No Description'),
(3, 'Movies/SVCD', 'cat_movies.gif', 'No Description'),
(4, 'Music', 'cat_music.gif', 'No Description'),
(5, 'Episodes', 'cat_episodes.gif', 'No Description'),
(6, 'XXX', 'cat_xxx.gif', 'No Description'),
(7, 'Games/GBA', 'cat_games.gif', 'No Description'),
(8, 'Games/PS2', 'cat_games.gif', 'No Description'),
(9, 'Anime', 'cat_anime.gif', 'No Description'),
(10, 'Movies/XviD', 'cat_movies.gif', 'No Description'),
(11, 'Movies/DVD-R', 'cat_movies.gif', 'No Description'),
(12, 'Games/PC Rips', 'cat_games.gif', 'No Description'),
(13, 'Appz/misc', 'cat_apps.gif', 'No Description')";

$INSERT[] = "INSERT INTO cleanup (clean_id, clean_title, clean_file, clean_time, clean_increment, clean_cron_key, clean_log, clean_desc, clean_on) VALUES
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
(15, 'Update User Warnings', 'update_user_warnings.php', 0, 17800, '9f6dec80c3870d5fdd5e1e5e48d57c33', 0, 'Removes and updates old warnings, and also instigates new warnings etc etc.', 0)";


$INSERT[] = "INSERT INTO countries (id, name, flagpic) VALUES
(1, 'Sweden', 'sweden.gif'),
(2, 'United States of America', 'usa.gif'),
(3, 'Russia', 'russia.gif'),
(4, 'Finland', 'finland.gif'),
(5, 'Canada', 'canada.gif'),
(6, 'France', 'france.gif'),
(7, 'Germany', 'germany.gif'),
(8, 'China', 'china.gif'),
(9, 'Italy', 'italy.gif'),
(10, 'Denmark', 'denmark.gif'),
(11, 'Norway', 'norway.gif'),
(12, 'United Kingdom', 'uk.gif'),
(13, 'Ireland', 'ireland.gif'),
(14, 'Poland', 'poland.gif'),
(15, 'Netherlands', 'netherlands.gif'),
(16, 'Belgium', 'belgium.gif'),
(17, 'Japan', 'japan.gif'),
(18, 'Brazil', 'brazil.gif'),
(19, 'Argentina', 'argentina.gif'),
(20, 'Australia', 'australia.gif'),
(21, 'New Zealand', 'newzealand.gif'),
(22, 'Spain', 'spain.gif'),
(23, 'Portugal', 'portugal.gif'),
(24, 'Mexico', 'mexico.gif'),
(25, 'Singapore', 'singapore.gif'),
(67, 'India', 'india.gif'),
(62, 'Albania', 'albania.gif'),
(26, 'South Africa', 'southafrica.gif'),
(27, 'South Korea', 'southkorea.gif'),
(28, 'Jamaica', 'jamaica.gif'),
(29, 'Luxembourg', 'luxembourg.gif'),
(30, 'Hong Kong', 'hongkong.gif'),
(31, 'Belize', 'belize.gif'),
(32, 'Algeria', 'algeria.gif'),
(33, 'Angola', 'angola.gif'),
(34, 'Austria', 'austria.gif'),
(35, 'Yugoslavia', 'yugoslavia.gif'),
(36, 'Western Samoa', 'westernsamoa.gif'),
(37, 'Malaysia', 'malaysia.gif'),
(38, 'Dominican Republic', 'dominicanrep.gif'),
(39, 'Greece', 'greece.gif'),
(40, 'Guatemala', 'guatemala.gif'),
(41, 'Israel', 'israel.gif'),
(42, 'Pakistan', 'pakistan.gif'),
(43, 'Czech Republic', 'czechrep.gif'),
(44, 'Serbia', 'serbia.gif'),
(45, 'Seychelles', 'seychelles.gif'),
(46, 'Taiwan', 'taiwan.gif'),
(47, 'Puerto Rico', 'puertorico.gif'),
(48, 'Chile', 'chile.gif'),
(49, 'Cuba', 'cuba.gif'),
(50, 'Congo', 'congo.gif'),
(51, 'Afghanistan', 'afghanistan.gif'),
(52, 'Turkey', 'turkey.gif'),
(53, 'Uzbekistan', 'uzbekistan.gif'),
(54, 'Switzerland', 'switzerland.gif'),
(55, 'Kiribati', 'kiribati.gif'),
(56, 'Philippines', 'philippines.gif'),
(57, 'Burkina Faso', 'burkinafaso.gif'),
(58, 'Nigeria', 'nigeria.gif'),
(59, 'Iceland', 'iceland.gif'),
(60, 'Nauru', 'nauru.gif'),
(61, 'Slovenia', 'slovenia.gif'),
(63, 'Turkmenistan', 'turkmenistan.gif'),
(64, 'Bosnia Herzegovina', 'bosniaherzegovina.gif'),
(65, 'Andorra', 'andorra.gif'),
(66, 'Lithuania', 'lithuania.gif'),
(68, 'Netherlands Antilles', 'nethantilles.gif'),
(69, 'Ukraine', 'ukraine.gif'),
(70, 'Venezuela', 'venezuela.gif'),
(71, 'Hungary', 'hungary.gif'),
(72, 'Romania', 'romania.gif'),
(73, 'Vanuatu', 'vanuatu.gif'),
(74, 'Vietnam', 'vietnam.gif'),
(75, 'Trinidad & Tobago', 'trinidadandtobago.gif'),
(76, 'Honduras', 'honduras.gif'),
(77, 'Kyrgyzstan', 'kyrgyzstan.gif'),
(78, 'Ecuador', 'ecuador.gif'),
(79, 'Bahamas', 'bahamas.gif'),
(80, 'Peru', 'peru.gif'),
(81, 'Cambodia', 'cambodia.gif'),
(82, 'Barbados', 'barbados.gif'),
(83, 'Bangladesh', 'bangladesh.gif'),
(84, 'Laos', 'laos.gif'),
(85, 'Uruguay', 'uruguay.gif'),
(86, 'Antigua Barbuda', 'antiguabarbuda.gif'),
(87, 'Paraguay', 'paraguay.gif'),
(89, 'Thailand', 'thailand.gif'),
(88, 'Union of Soviet Socialist Republics', 'ussr.gif'),
(90, 'Senegal', 'senegal.gif'),
(91, 'Togo', 'togo.gif'),
(92, 'North Korea', 'northkorea.gif'),
(93, 'Croatia', 'croatia.gif'),
(94, 'Estonia', 'estonia.gif'),
(95, 'Colombia', 'colombia.gif'),
(96, 'Lebanon', 'lebanon.gif'),
(97, 'Latvia', 'latvia.gif'),
(98, 'Costa Rica', 'costarica.gif'),
(99, 'Egypt', 'egypt.gif'),
(100, 'Bulgaria', 'bulgaria.gif')";



$INSERT[] = "INSERT INTO forums (sort, id, name, description, minclassread, minclasswrite, postcount, topiccount, minclasscreate) VALUES
(0, 1, 'VIP Lounge', 'Your area for discussion of topics, ideas, code & anything else you don''t want made public just yet.', 6, 6, 0, 0, 6)";







$INSERT[] = "INSERT INTO reputationlevel (reputationlevelid, minimumreputation, level) VALUES
(1, -999999, 'is infamous around these parts'),
(2, -50, 'can only hope to improve'),
(3, -10, 'has a little shameless behaviour in the past'),
(4, 0, 'is an unknown quantity at this point'),
(5, 10, 'is on a distinguished road'),
(6, 50, 'will become famous soon enough'),
(7, 150, 'has a spectacular aura about'),
(8, 250, 'is a jewel in the rough'),
(9, 350, 'is just really nice'),
(10, 450, 'is a glorious beacon of light'),
(11, 550, 'is a name known to all'),
(12, 650, 'is a splendid one to behold'),
(13, 1000, 'has much to be proud of'),
(14, 1500, 'has a brilliant future'),
(15, 2000, 'has a reputation beyond repute')";

$INSERT[] = "INSERT INTO searchcloud (id, searchedfor, howmuch) VALUES
(1, 'bob', 1),
(2, 'testing', 4),
(3, 'blackadder', 1),
(4, '24', 2)";


$INSERT[] = "INSERT INTO stylesheets (id, uri, name) VALUES
(1, '1.css', '(default)'),
(2, '2.css', 'Large text')";


?>
