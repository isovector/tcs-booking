<?php include "authenticate.php"; ?>
<!DOCTYPE HTML>
<html>
<head>
    <style type="text/css">
body {
    background-color: #f00;
}
#showselect {
    position: absolute;
    left: 500px;
    top: 30px;
}
#controlpanel {
    position: absolute;
    background-color: rgba(255, 255, 255, 0.5);
    left: 650px;
    top: 15px;
    width: 230px;
    height: 320px;
    padding: 4px;
}
    </style>
    <title>Terrace Concert Society - Ticket Booking</title>
    <meta http-equiv="X-UA-Compatible" content="chrome=1" />
</head>
<body>
    <script type="text/ecmascript">
//    <![CDATA[
function update(element) {
    document.getElementById("theatre").src = "theatre.php?show=" + element.options[element.selectedIndex].value;
    document.getElementById("showid").value = element.options[element.selectedIndex].value;
    document.getElementById("curshow").innerHTML = element.options[element.selectedIndex].text;
    if (navigator.vendor == "Google Inc.") { // this is a fix for chrome frame
        document.location = "?force=" + element.options[element.selectedIndex].value;
        return;
    }
    var container = document.getElementById('seatlist');
    while (container.childNodes.length != 0)
        container.removeChild(container.childNodes[0]);
    container = document.getElementById('formdata');
    while (container.childNodes.length != 0)
        container.removeChild(container.childNodes[0]);
}
function addseat(cid) {
    var container = document.getElementById('seatlist');
    var newone = document.createElement('span');
    newone.id = "seatid" + cid;
    newone.innerHTML = cid + ", ";
    container.insertBefore(newone, container.lastChild);
    newone = document.createElement('input');
    newone.type = "hidden";
    newone.id = "seatdata" + cid;
    newone.name = "seatdata[]";
    newone.value = cid;
    document.getElementById('formdata').insertBefore(newone, document.getElementById('formdata').lastChild);
}
function removeseat(cid) {
    document.getElementById('seatlist').removeChild(document.getElementById('seatid' + cid));
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
        <embed width="1200" height="800" src="theatre.php?show=<?php echo $_GET["force"]; ?>" name="theatre" id="theatre" type="image/svg+xml">
        <select onchange="update(this)" id="showselect">
<?php
$query = mysql_query("SELECT `sid`, `name` FROM `cs_shows` ORDER BY `date` ASC");
$shows = array();
while ($row = mysql_fetch_assoc($query)) {
    printf('            <option value="%d"%s>%s</option>', $row["sid"], ($row["sid"] == $_GET["force"] ? ' selected="selected"' : ""), $row["name"]);
    $shows[$row["sid"]] = $row["name"];
}

?>
        </select>
    </div>
</body>
</html>
