<?php

include "authenticate.php";
include "connection.php";

$global = array();
$shows = array();
$showdata = array();

$result = mysql_query("SELECT `name`, `sid` FROM `cs_shows` ORDER BY `date` ASC");
while ($show = mysql_fetch_assoc($result))
    $showdata[str_pad($show["sid"], 4, '0', STR_PAD_LET)] = $show["name"];

$result = mysql_query("SELECT SUBSTR(`cid`, 5, 1) AS `row`, RIGHT(`cid`, 2) AS `seat` FROM `cs_seats` WHERE LEFT(`cid`, 4) = '0000'") or die(mysql_error());
while ($seat = mysql_fetch_assoc($result)) {
    if (!is_numeric($seat["seat"])) $seat["seat"] = substr($seat["seat"], 1);
    $global[] = $seat["row"] . $seat["seat"];
}

$result = mysql_query("SELECT SUBSTR(`cid`, 5, 1) AS `row`, RIGHT(`cid`, 2) AS `seat`, LEFT(`cid`, 4) AS `show` FROM `cs_seats` WHERE LEFT(`cid`, 4) != '0000'") or die(mysql_error());
while ($seat = mysql_fetch_assoc($result)) {
    if (!key_exists($seat["show"], $shows)) $shows[$seat["show"]] = array();
    if (!is_numeric($seat["seat"])) $seat["seat"] = substr($seat["seat"], 1);
    $shows[$seat["show"]][] = $seat["row"] . $seat["seat"];
}

echo "<pre>";
foreach($global as $gs)
    foreach ($shows as $show => $seats)
        foreach ($seats as $seat)
            if ($seat == $gs) {
                $seatdata = mysql_fetch_assoc(mysql_query("SELECT * FROM `cs_seats` WHERE `cid`='{$show}{$seat}'"));
                echo "Seat {$seat} for $showdata[$show]: {$seatdata['first']} {$seatdata['last']} ({$seatdata['phone']}) - conflicts with <br />";
                $seatdata = mysql_fetch_assoc(mysql_query("SELECT * FROM `cs_seats` WHERE `cid`='0000{$seat}'"));
                echo "    Season's Ticket holder: {$seatdata['first']} {$seatdata['last']} ({$seatdata['phone']})<br />&nbsp;<br />";
            }

?>
