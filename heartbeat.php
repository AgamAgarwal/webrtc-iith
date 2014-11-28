<?php
session_start();

if(!isset($_SESSION['uid']))
	exit();

//connect to database
require_once("connect.php");

$query="UPDATE users SET online=1, last_heartbeat=CURRENT_TIMESTAMP WHERE id={$_SESSION['uid']};";
$result=$mysqli->query($query);
?>