<?php
//ini_set('display_errors', 1);
session_start();

if(!isset($_SESSION['uid']) || !isset($_POST['dataType']) || !isset($_POST['data']) || !isset($_POST['sessionID'])
	|| ($_POST['dataType']!="sdp" && $_POST['dataType']!="candidate")) {
	echo json_encode(array("success" => 0, "error" => "invalid request"));
	exit();
}

require_once('connect.php');

$dataType=$mysqli->real_escape_string($_POST['dataType']);
$data=$_POST['data'];
$data=$mysqli->real_escape_string($_POST['data']);
$sessionID=$mysqli->real_escape_string($_POST['sessionID']);


$query="UPDATE sessions SET ";
$query.=$dataType."_".$_SESSION['usertype'];
$query.="='".$data."'";
$query.=" WHERE id=".$sessionID." AND ".$_SESSION['usertype']."id=".$_SESSION['uid'];

$mysqli->query($query);

if($mysqli->affected_rows>=1) {
	echo json_encode(array("success" => 1));
	exit();
}

echo json_encode(array("success" => 0, "error" => "nothing matched", "query" => $query));
?>