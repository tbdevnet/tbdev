<?php

require_once("include/bittorrent.php");

dbconn(false);
stdhead("Links");


function add_link($url, $title, $description = "")
{
  $text = "<a class=altlink href=$url>$title</a>";
  if ($description)
    $text = "$text - $description";
  print("<li>$text</li>\n");
}

?>
<? if ($CURUSER) { ?>
<p><a href=sendmessage.php?receiver=2>Please report dead links!</a></p>
<? } ?>
<table width=750 class=main border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>

<h2>Other pages on this site</h2>
<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text><ul>
<li><a class=altlink href=rss.xml>RSS feed</a> -
  For use with RSS-enabled software. An alternative to torrent email notifications.
<li><a class=altlink href=rssdd.xml>RSS feed (direct download)</a> -
  Links directly to the torrent file.
<li><a class=altlink href=bitbucket-upload>Bitbucket</a> -
  If you need a place to host your avatar or other pictures.
</ul></td></tr></table>

<h2>BitTorrent Information</h2>
<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text><ul>
<li><a class=altlink href=http://dessent.net/btfaq/>Brian's BitTorrent FAQ and Guide</a> -
  Everything you need to know about BitTorrent. Required reading for all n00bs.</font>
<li><a class=altlink href=http://10mbit.com/faq/bt/>The Ultimate BitTorrent FAQ</a> -
  Another nice BitTorrent FAQ, by Evil Timmy.
</ul></td></tr></table>

<h2>BitTorrent Software</h2>
<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text><ul>
<li><a class=altlink href=http://pingpong-abc.sourceforge.net/>ABC</a> -
  "ABC is an improved client for the Bittorrent peer-to-peer file distribution solution."</li>
<li><a class=altlink href=http://azureus.sourceforge.net/>Azureus</a> -
  "Azureus is a java bittorrent client. It provides a quite full bittorrent protocol implementation using java language."</li>
<li><a class=altlink href=http://bnbt.go-dedicated.com/>BNBT</a> -
  Nice BitTorrent tracker written in C++.</li>
<li><a class=altlink href=http://bittornado.com/>BitTornado</a> -
  a.k.a "TheSHAD0W's Experimental BitTorrent Client".</li>
<li><a class=altlink href=http://www.bitconjurer.org/BitTorrent>BitTorrent</a> -
  Bram Cohen's official BitTorrent client.</li>
<li><a class=altlink href=http://ei.kefro.st/projects/btclient/>BitTorrent EXPERIMENTAL</a> -
  "This is an unsupported, unofficial, and, most importantly, experimental build of the BitTorrent GUI for Windows."</li>
<li><a class=altlink href=http://krypt.dyndns.org:81/torrent/>Burst!</a> -
  Alternative Win32 BitTorrent client.</li>
<li><a class=altlink href=http://g3torrent.sourceforge.net/>G3 Torrent</a> -
  "A feature rich and graphically empowered bittorrent client written in python."</li>
<li><a class=altlink href=http://krypt.dyndns.org:81/torrent/maketorrent/>MakeTorrent</a> -
  A tool for creating torrents.</li>
<li><a class=altlink href=http://ptc.sourceforge.net/>Personal Torrent Collector</a> -
  BitTorrent client.</li>
<li><a class=altlink href=http://www.shareaza.com/>Shareaza</a> -
  Gnutella, eDonkey and BitTorrent client.</li>
</ul></td></tr></table>

<h2>Download sites</h2>
<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text><ul>
<li><a class=altlink href=http://www.suprnova.org/>SuprNova</a> -
  Apps, games, movies, TV and other stuff. [popups]</li>
<li><a class=altlink href=http://empornium.us:6969/>Empornium</a> -
  Pr0n, and then some!
</ul></td></tr></table>

<h2>Forum communities</h2>
<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text><ul>
<li><a class=altlink href=http://www.filesoup.com/>Filesoup</a> -
  BitTorrent community.</li>
<li><a class=altlink href=http://www.torrent-addiction.com/forums/index.php>Torrent Addiction</a> -
  Another BitTorrent community. [popups]</li>
<li><a class=altlink href=http://www.terabits.net/>TeraBits</a> -
Games, movies, apps both unix and win, tracker support, music, xxx.</li>
<li><a class=altlink href=http://www.ftpdreams.com/new/forum/sitenews.asp>FTP Dreams</a> - "Where Dreams Become a Reality".</li>
</ul></td></tr></table>

<h2>Other sites</h2>
<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text><ul>
<li><a class=altlink href=http://www.nforce.nl/>NFOrce</a> -
  Game and movie release tracker / forums.</li>
<li><a class=altlink href=http://www.grokmusiq.com/>grokMusiQ</a> -
  Music release tracker.</li>
<li><a class=altlink href=http://www.izonews.com/>iSONEWS</a> -
  Release tracker and forums.</li>
<li><a class=altlink href=http://www.btsites.tk>BTSITES.TK</a> -
  BitTorrent link site. [popups]</li>
<li><a class=altlink href=http://www.litezone.com/>Link2U</a> -
  BitTorrent link site.</li>
</ul></td></tr></table>


<h2>Link to TBDev.net</h2>
<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text>
Do you want a link to torrentbits on your homepage?<br>
Copy the following and paste it into your homepage code.<br>
<br>
<font color=#004E98>
&lt;!-- TBDev Link --&gt;<br>
<br>
&lt;a href="http://www.tbdev.net"&gt;<br>
&lt;img src="http://www.tbdev.net/pic/tbani22.gif" border="0" alt="TBDev - The best!"&gt;&lt;/a&gt;<br>
<br>
&lt;!-- End of TBDev Link --&gt;</font><br>
<br>
<br>
It will look like this:<br>
<br>
<a href="http://www.tbdev.net/">
<img src="./pic/tbdev_btn_red.png" border="0" alt="TBDev - The best!"></a>
<br>
</td></tr></table>

</td></tr></table>

<?php

stdfoot();

?>