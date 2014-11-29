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
	echo json_encode(array("allowed" => 1));
	exit();
}
echo json_encode(array("allowed" => 0));
?>