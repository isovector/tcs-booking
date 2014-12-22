<?php

include "authenticate.php";
include "connection.php";

if (isset($_GET["cid"])) {
    mysql_query("DELETE FROM `cs_seats` WHERE `cid`='{$_GET['cid']}'");
}


$showdata = array();

$result = mysql_query("SELECT `name`, `sid` FROM `cs_shows` ORDER BY `date` ASC");
while ($show = mysql_fetch_assoc($result))
    $showdata[str_pad($show["sid"], 4, '0', STR_PAD_LEFT)] = $show["name"];

$result = mysql_query("SELECT LEFT(`cid`, 4) AS `show`, SUBSTR(`cid`, 5, 1) AS `row`, RIGHT(`cid`, 2) AS `seat`, `cid`, `first`, `last`, `booktime` FROM `cs_seats` ORDER BY `booktime` DESC LIMIT 50") or die(mysql_error());

?>
<table border="1" cellpadding="5">
<tr style=text-align: center;><td><b>Name</b></td><td><b>Seat</b></td><td><b>Show</b></td><td><b>Booked Time</b></td><td><b>Unbook</b></td></tr>
<?php
while ($seat = mysql_fetch_assoc($result)) {
    if (!is_numeric($seat["seat"])) $seat["seat"] = substr($seat["seat"], 1);
    echo "<tr><td>{$seat['first']} {$seat['last']}</td><td>{$seat['row']}{$seat['seat']}</td><td>{$showdata[$seat['show']]}</td><td>{$seat['booktime']}</td><td><a href=\"unbook.php?cid={$seat['show']}{$seat['row']}{$seat['seat']}\">Unbook</a></td></tr>";
}
echo "</table>";

?>
