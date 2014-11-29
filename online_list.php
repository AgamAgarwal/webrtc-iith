<?php
session_start();
if(!isset($_SESSION['uid'])) {
	echo json_encode("error");
	exit();
}

require_once("connect.php");

//Refresh online peers based on heartbeat times
$query="UPDATE users SET online=0 WHERE online=1 AND CURRENT_TIMESTAMP-last_heartbeat>10;";

$result=$mysqli->query($query);

//get list of online peers based on usertype
$query="SELECT id, username, firstname, lastname, online FROM users WHERE usertype=\"";
$query.=$_SESSION['usertype']=="doctor"?"student":"doctor";
$query.="\" ORDER BY online DESC;";

$result=$mysqli->query($query);

$list=array();

if($result)
	while($obj=$result->fetch_object()) {
		array_push($list, $obj);
	}

echo json_encode($list);
?>