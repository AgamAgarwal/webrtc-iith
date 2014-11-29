<?php
session_start();
if(!isset($_SESSION['uid'])) {
	echo json_encode(array("call" => 0));
	exit();
}

require_once("connect.php");

//remove old entries
$query="DELETE FROM sessions WHERE CURRENT_TIMESTAMP-time>120";
$result=$mysqli->query($query);

//get incoming calls
$query="SELECT sessions.id as sessionid, users.firstname as firstname, users.lastname as lastname FROM sessions";
$query.=" INNER JOIN users ON sessions.";
if($_SESSION['usertype']=="doctor")
	$query.="studentid";
else
	$query.="doctorid";
$query.="=users.id";
$query.=" WHERE sessions.";
$query.=$_SESSION['usertype']."id=";
$query.=$_SESSION['uid'];
$query.=";";
$result=$mysqli->query($query);

if($result && $obj=$result->fetch_object()) {
	echo json_encode(array("call" => 1, "sessionID" => $obj->sessionid, "name" => $obj->firstname." ".$obj->lastname));
	exit();
}
echo json_encode(array("call" => 0));
?>