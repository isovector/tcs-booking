<?php

include "connection.php";
include "authenticate.php";


function xore($InputString, $KeyPhrase) {
    $KeyPhraseLength = strlen($KeyPhrase);
    for ($i = 0; $i < strlen($InputString); $i++){
        $rPos = $i % $KeyPhraseLength;
        $r = ord($InputString[$i]) ^ ord($KeyPhrase[$rPos]);
        $InputString[$i] = chr($r);
    }
    return $InputString;
}

foreach ($_POST as &$var)
    if (!is_array($var))
        $var = mysql_real_escape_string($var);

$show = str_pad($_POST["show"], 4, '0', STR_PAD_LEFT);
$details = mysql_fetch_assoc(mysql_query("SELECT `name`, `date` FROM `cs_shows` WHERE `sid`='{$_POST['show']}'"));

echo "<pre>";
for ($i = 0; $i < count($_POST["seatdata"]); $i++) {
    mysql_query(sprintf("INSERT INTO `cs_seats` (`cid`, `first`, `last`, `phone`, `email`, `address`, `city`, `province`, `postal`, `adult`, `booktime`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', NOW());",
        $show . $_POST["seatdata"][$i], $_POST["fname"][$i], $_POST["lname"][$i], $_POST["phone"][$i], $_POST["email"][$i], $_POST["address"][$i], $_POST["city"][$i], "BC", $_POST["postal"][$i], $_POST["age"][$i])) or die(mysql_error());

    echo "<h3>Terrace Concert Society - Ticket Confirmation</h3>\r\n";
    printf("<b>Show: </b> <u>%s</u>\r\n<b>Date: </b> %s\r\n\r\n", $details["name"], date("l, F j, Y \a\\t h:i A", strtotime($details["date"])));
    printf("<b>Name: </b> %s\r\n<b>Phone:</b> %s\r\n<b>Email:</b> %s\r\n<b>Seats:</b> <i>%s</i>\r\n\r\n<b>Digital Signature:</b>\r\n\r\n", $_POST["fname"][$i] . " " . $_POST["lname"][$i], $_POST["phone"][$i], $_POST["email"][$i], $_POST["seatdata"][$i]);

    echo "----------------BEGIN TICKET SIGNATURE----------------\r\n";
    $d = base64_encode(xore(serialize($_POST), "syzygy"));
    $md5 = md5($d);
    $d = $md5 . ":" . strlen($d) . "//" . $d;
    $o = wordwrap($d, 54, "\r\n", true);
    echo $o . substr(str_repeat($md5, 12), 0, 54 - (strlen($d) % 54));
    echo "\r\n-----------------END TICKET SIGNATURE-----------------\r\n\r\n";

    echo "<br style=\"page-break-after: always;\" />\r\n";
}

?>
