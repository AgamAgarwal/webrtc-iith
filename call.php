<?php
session_start();
if(!isset($_SESSION['uid']) || !isset($_POST['id'])) {
	echo json_encode(array("allowed" => 0));
	exit();
}

require_once("connect.php");

//checking if allowed to call
$query="SELECT id FROM users WHERE id=".($_POST['id'])." AND usertype=\""
	.($_SESSION['usertype']=="doctor"?"student":"doctor")
	."\" AND online=1 AND busy=0;";
$result=$mysqli->query($query);

if($result && $obj=$result->fetch_object()) {
	//add a request session in the 'sessions' table
	$query="INSERT INTO sessions (doctorid, studentid, offerer) VALUES (";
	if($_SESSION['usertype']=="doctor")
		$query.=$_SESSION['uid'].", ".$_POST['id'];
	else
		$query.=$_POST['id'].", ".$_SESSION['uid'];
	$query.=", \"{$_SESSION['usertype']}\");";
	
	$result=$mysqli->query($query);

	if($result) {
		echo json_encode(array("allowed" => 1, "sessionID" => $mysqli->insert_id));
		exit();
	}
}
echo json_encode(array("allowed" => 0));
?>