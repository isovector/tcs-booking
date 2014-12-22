<?php
include "authenticate.php";
include 'connection.php';

if (!isset($_GET["sid"]) || !is_numeric($_GET["sid"])) die("This is no longer the correct means of getting show details - please use the booking form now.");

$sid = $_GET["sid"];
$show = mysql_fetch_assoc(mysql_query("SELECT * FROM `cs_shows` WHERE `sid`='{$sid}'"));
$sid = str_pad($sid, 4, '0', STR_PAD_LEFT);

$result = mysql_query("SELECT CASE LEFT(`cid`, 4) WHEN '0000' THEN 'YES' ELSE 'NO' END AS `season`, RIGHT(`cid`, 3) AS `seat`, CONCAT(CONCAT(`first`, ' '), `last`) AS `name`, `phone`, `email` FROM cs_seats WHERE LEFT(`cid`, 4)='0000' OR LEFT(`cid`, 4)='{$sid}'") or die(mysql_error());

$tsv  = array();
$tsv[] = implode("\t", array("SEASON TICKET", "SEAT", "NAME", "PHONE", "EMAIL"));
while($row = mysql_fetch_array($result, MYSQL_NUM))
   $tsv[]  = implode("\t", $row);

$tsv = implode("\r\n", $tsv);

$fileName = str_replace(array(' ', '\'', "&amp;"), array('-', '', "&"), $show["name"]) . "-seating.xls";
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$fileName");

echo $tsv;

?>
