<?php
if(session_id()!=='')
	session_start();
if(isset($_SESSION) && isset($_SESSION['uid'])) {
	header("Location: chat.php");
}
require_once("connect.php");
if(!isset($_POST['username']) || !isset($_POST['password']) || $_POST['username']=="" || $_POST['password']=="") {
	header("Location: index.php");
	exit();
}

$username=$mysqli->real_escape_string($_POST['username']);
$password=$mysqli->real_escape_string($_POST['password']);

$query="SELECT * FROM users WHERE username='{$username}' AND password=md5('{$password}')";
$result=$mysqli->query($query);
if($result && $obj=$result->fetch_object()) {
	session_start();
	session_unset();

	$_SESSION['uid']=$obj->id;
	$_SESSION['username']=$obj->username;
	$_SESSION['email']=$obj->email;
	$_SESSION['firstname']=$obj->firstname;
	$_SESSION['lastname']=$obj->lastname;
	$_SESSION['usertype']=$obj->usertype;
	
	//redirect to home
	header('Location: home.php');
} else {
	header("Location: index.php?err=invalid");
}
?>