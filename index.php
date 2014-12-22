<?php include "authenticate.php"; ?>
<!DOCTYPE HTML>
<html>
<head>
    <style type="text/css">
body {
    background-color: #f00;
}
#applet {
    position: absolute;
    left: 15px;
    top: 30px;
}
#controlpanel {
    position: absolute;
    background-color: rgba(255, 255, 255, 0.5);
    left: 650px;
    top: 50px;
    width: 320px;
    min-height: 320px;
    padding: 4px;
}

#controlpanel2 {
    position: absolute;
    background-color: rgba(255, 255, 255, 0.5);
    left: 15px;
    top: 15px;
    width: 955px;
    height: 20px;
    padding: 4px;
}

input[type=radio] {
    margin-left: 0px;
    margin-right: 0px;
}

    </style>
    <title>Terrace Concert Society - Ticket Booking</title>
    <meta http-equiv="X-UA-Compatible" content="chrome=1" />
</head>
<body>
    <script type="text/ecmascript">
//    <![CDATA[
function update(element) {
    document.getElementById("largemaplink").href = "big.php?force=" + element.options[element.selectedIndex].value;
    document.getElementById("excellink").href = "export.php?sid=" + element.options[element.selectedIndex].value;
    document.getElementById("theatre").src = "theatre.php?show=" + element.options[element.selectedIndex].value;
    document.getElementById("showid").value = element.options[element.selectedIndex].value;
    document.getElementById("curshow").innerHTML = element.options[element.selectedIndex].text;
    if (navigator.vendor == "Google Inc.") { // this is a fix for chrome frame
        document.location = "?force=" + element.options[element.selectedIndex].value;
        return;
    }
    var container = document.getElementById('formdata');
    while (container.childNodes.length != 0)
        container.removeChild(container.childNodes[0]);
}
function addseat(cid) {
    var container = document.getElementById('formdata');
    var newone = document.createElement('div');
    newone.id = "seatdata" + cid;
    newone.innerHTML = "<div id=\"seat" + cid + "\"><input type=\"hidden\" name=\"seatdata[]\" value=\"" + cid + "\" /><b>Seat " + cid + ":</b><br /><table><tr><td>First:</td><td><input type=\"text\" name=\"fname[]\" /></td></tr><tr><td>Last:</td><td><input type=\"text\" name=\"lname[]\" /></td></tr><tr><td>Phone:</td><td><input type=\"text\" name=\"phone[]\" /></td></tr><tr><td>Email:</td><td><input type=\"text\" name=\"email[]\" /></td></tr><tr><td>Address:</td><td><input type=\"text\" name=\"address[]\" /></td></tr><tr><td>City:</td><td><input type=\"text\" name=\"city[]\" /></td></tr><tr><td>Postal:</td><td><input type=\"text\" name=\"postal[]\" /></td></tr><tr><td>Age:</td><td>Youth <input type=\"radio\" name=\"age[]\" value=\"0\" /> Adult<input type=\"radio\" name=\"age[]\" value=\"1\" selected=\"selected\" /> Sen.<input type=\"radio\" name=\"age[]\" value=\"2\" /></td></tr></table><br />&nbsp;";
    container.insertBefore(newone, container.firstChild);
}
function removeseat(cid) {
    document.getElementById('formdata').removeChild(document.getElementById('seatdata' + cid));
}
//    ]]>
    </script>
    <div id="applet">
<?php
include "connection.php";

if (!isset($_GET["force"]) || !is_numeric($_GET["force"]))
    $_GET["force"] = 0;
?>
        <embed width="600" height="400" src="theatre.php?show=<?php echo $_GET["force"]; ?>" name="theatre" id="theatre" type="image/svg+xml">
<?php
$query = mysql_query("SELECT `sid`, `name` FROM `cs_shows` ORDER BY `date` ASC");
$shows = array();
$out = array();
while ($row = mysql_fetch_assoc($query)) {
    $out[] = sprintf('            <option value="%d"%s>%s</option>', $row["sid"], ($row["sid"] == $_GET["force"] ? ' selected="selected"' : ""), $row["name"]);
    $shows[$row["sid"]] = $row["name"];
}

?>
    </div>
    <div id="controlpanel">
        <form action="process.php" method="post">
            <input type="hidden" id="showid" name="show" value="0" />
            <b>Show:</b> <span id="curshow"><?php echo $shows[$_GET["force"]]; ?></span><input type="submit" value="Book Tickets" style="float: right;" /><br />            
            <div id="formdata"></div>
        </form>
    </div>
    <div id="controlpanel2">
        <label for="showselect">Jump to Show:</label>
        <select onchange="update(this)" id="showselect" name="showselect">
<?php
foreach ($out as $line)
    echo $line;

?>
        </select>
        <span style="float: right;">
            <a href="big.php?force=<?php echo $_GET["force"]; ?>" id="largemaplink">View Large Map</a> |
            <a href="export.php?sid=<?php echo $_GET["force"]; ?>" id="excellink">Excel</a> |
            <a href="fullexport.php?sid=<?php echo $_GET["force"]; ?>" id="excellink">Full Data</a> |
            <a href="unbook.php">Unbook Recent Tickets</a>
        </span>
    </div>
</body>
</html>
