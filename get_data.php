<?php
session_start();
if(!isset($_SESSION['uid']) || !isset($_POST['dataType']) || !isset($_POST['sessionID'])
	|| ($_POST['dataType']!="sdp" && $_POST['dataType']!="candidate")) {
	echo json_encode(array("success" => 0, "error" => "invalid request"));
	exit();
}

require_once('connect.php');

$dataType=$mysqli->real_escape_string($_POST['dataType']);
$sessionID=$mysqli->real_escape_string($_POST['sessionID']);


$query="SELECT ".$dataType."_";
if($_SESSION['usertype']=="doctor")
	$query.="student";
else
	$query.="doctor";
$query.=" AS data FROM sessions WHERE id=".$sessionID
	." AND ".$_SESSION['usertype']."id=".$_SESSION['uid'].";";

$result=$mysqli->query($query);

if($result && $obj=$result->fetch_object()) {
	echo json_encode(array("success" => 1, "data" => $obj->data));
	exit();
}
echo json_encode(array("success" => 0, "error" => "nothing matched", "query" => $query, "got" => $result));
?>