<?php
header("Content-type: image/svg+xml");
//header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
//header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
echo '<?xml version="1.0" encoding="iso-8859-1" standalone="no"?>'; 
?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN" "http://www.w3.org/TR/SVG/DTD/svg10.dtd">

<?php
// settings for the application

$_bg = "ff0000";
$_default = "878787";
$_booked = "343434";
$_mouseover = "008800";
$_selected = "aaaa00";
$_selectover = "cccc00";
$_line = "0, 128, 0";

$layout = array(5, 4, 4, 3, 3, 3, 2, 2, 1, 1, 1);

include "connection.php";

$show = mysql_real_escape_string(str_pad($_GET["show"], 4, '0', STR_PAD_LEFT));
$seats = array();
$tseats = 0;

if ($show == '0') {
    $query = mysql_query("SELECT SUBSTR(`cid`, 5, 1) AS `row`, RIGHT(`cid`, 2) AS `seat`, LEFT(`cid`, 4) AS `show` FROM `cs_seats` WHERE LEFT(`cid`, 4)>='$show'");
    $tseats = $sseats = mysql_num_rows(mysql_query("SELECT SUBSTR(`cid`, 5, 1) AS `row`, RIGHT(`cid`, 2) AS `seat`, LEFT(`cid`, 4) AS `show` FROM `cs_seats` WHERE LEFT(`cid`, 4)='$show'"));
}
else {
    $query = mysql_query("SELECT SUBSTR(`cid`, 5, 1) AS `row`, RIGHT(`cid`, 2) AS `seat`, LEFT(`cid`, 4) AS `show` FROM `cs_seats` WHERE LEFT(`cid`, 4)='$show' OR LEFT(`cid`, 4)='0000'");
    $tseats = mysql_num_rows($query);
    $sseats = mysql_num_rows(mysql_query("SELECT SUBSTR(`cid`, 5, 1) AS `row`, RIGHT(`cid`, 2) AS `seat`, LEFT(`cid`, 4) AS `show` FROM `cs_seats` WHERE LEFT(`cid`, 4)='$show'"));
}

while ($row = mysql_fetch_assoc($query)) {
    if (!array_key_exists($row["row"], $seats)) $seats[$row["row"]] = array();
    if (!is_numeric($row["seat"])) $row["seat"] = substr($row["seat"], 1);
    $seats[$row["row"]][$row["seat"]] = $row["show"];
}

?>

<svg width="600" height="400" viewBox="0 0 600 400" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" version="1.1"  baseProfile="full">
<script type="text/ecmascript">
    <![CDATA[
    function highlight(evt, start, sid) {
        document.getElementById('curid').firstChild.nodeValue = sid;
        if (evt.target.getAttribute("selected") == "true") {
            if (start) evt.target.setAttribute("fill", "#<?php echo $_selectover; ?>");
            else evt.target.setAttribute("fill", "#<?php echo $_selected; ?>");
        } else {
            if (start) evt.target.setAttribute("fill", "#<?php echo $_mouseover; ?>");
            else evt.target.setAttribute("fill", "#<?php echo $_default; ?>");
        }
    }
    function selection(evt, sid) {
        if (evt.target.getAttribute("selected") == "true") {
            evt.target.setAttribute("selected", "false");
            evt.target.setAttribute("fill", "#<?php echo $_mouseover; ?>");
            top.removeseat(sid);
        } else {
            evt.target.setAttribute("selected", "true");
            evt.target.setAttribute("fill", "#<?php echo $_selected; ?>");
            top.addseat(sid);
        }
    }
    ]]>
</script>
<defs>
    <style type="text/css">
        <![CDATA[
        .seri { font-weight: bold; font-family: serif; text-decoration:none; fill: #fff; }
        .label { font-size: 12px; font-family: sans-serif; fill: #fff; }
        .line { stroke: rgb(<?php echo $_line; ?>); stroke-width:1; fill: #<?php echo $_booked; ?>; }
        ]]>
    </style>
</defs>

<g id="container"> 
    <rect x="0" y="0" width="600" height="400" fill="#<?php echo $_bg; ?>" />
    <text x="95%" y="25" class="label" id="curid"> </text>
    <text x="85%" y="37" class="label" id="tseats">Total Seats: <?php echo $tseats; ?></text>
    <text x="85.5%" y="49" class="label" id="sseats">Sold Seats: <?php echo $sseats; ?></text>
    <g id="seats">
<?php
for ($y = 0; $y < 20; $y++) {
    $row = chr(ord("A") + $y + ($y > 7 ? 1 : 0));
    printf('<text x="10" y="%d" class="label">%s</text>'."\r\n", 56 + $y * 14, $row);
    for ($x = 1; $x <= 38; $x++) {
        $xx = 590 - (25 + ($x - 1) * 14 + ($x > 11 ? ($x >= 28 ? 16 : 8) : 0));
        $yy = 45 + $y * 14;

        if (($y < count($layout) && !($x - 1 < $layout[$y] || 38 - $x < $layout[$y])) || $y >= count($layout))
            if (!isset($seats[$row]) || !isset($seats[$row][$x]))
                printf('    <rect x="%1$d" y="%2$d" width="14" height="14" fill="#%3$s" stroke="#000000" onmouseover="highlight(evt, true, \'%4$s\');" ' .
                    'onmouseout="highlight(evt, false, \'%4$s\');" onclick="selection(evt, \'%4$s\');" />'."\r\n", $xx, $yy, $_default, $row . $x);
            else if ($seats[$row][$x] == 0) {
                printf('    <rect x="%d" y="%d" width="14" height="14" fill="#%s" stroke="#000000" />'."\r\n", $xx, $yy, $_booked);
                printf('        <line x1="%d" x2="%d" y1="%d" y2="%d" class="line" />'."\r\n", $xx + 1, $xx + 13, $yy + 1, $yy + 13);
                printf('        <line x1="%d" x2="%d" y1="%d" y2="%d" class="line" />'."\r\n", $xx + 13, $xx + 1, $yy + 1, $yy + 13);
            }
            else {
                printf('    <rect x="%d" y="%d" width="14" height="14" fill="#%s" stroke="#000000" />'."\r\n", $xx, $yy, $_booked);
                printf('        <circle cx="%d" cy="%d" r="5" class="line" />'."\r\n", $xx + 7, $yy + 7);
            }
    }
}

for ($x = 1; $x <= 38; $x += 2)
    printf('<text x="%d" y="%d" class="label">%s</text>'."\r\n", 590 - (25 + ($x - 1) * 14 + ($x > 11 ? ($x >= 28 ? 16 : 8) : 0)), $yy + 28, ($x < 10 ? " $x" : "$x"));

?>
    </g>
</g>

</svg>
