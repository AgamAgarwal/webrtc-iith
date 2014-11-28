<?php
session_start();
require_once("connect.php");
if(!isset($_POST['email']) || !isset($_POST['username']) || !isset($_POST['firstname']) || !isset($_POST['lastname']) || !isset($_POST['password']) || !isset($_POST['usertype'])
	|| $_POST['username']=="" || $_POST['email']=="" || $_POST['firstname']=="" || $_POST['lastname']=="" || $_POST['password']=="") {
	header("Location: index.php");
	exit();
}

$username=$mysqli->real_escape_string($_POST['username']);
$email=$mysqli->real_escape_string($_POST['email']);
$firstname=$mysqli->real_escape_string($_POST['firstname']);
$lastname=$mysqli->real_escape_string($_POST['lastname']);
$password=$mysqli->real_escape_string($_POST['password']);
$usertype=$mysqli->real_escape_string($_POST['usertype']);


//check if user exists
$query="SELECT * FROM users WHERE username='{$username}'";
$result=$mysqli->query($query);
if($result->num_rows>=1) {
	header("Location: index.php?err=username_taken");
	exit();
}

//verify email
if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  header("Location: index.php?err=email");
  exit();
}

//check if email exists
$query="SELECT * FROM users WHERE email='{$email}'";
$result=$mysqli->query($query);
if($result->num_rows>=1) {
	header("Location: index.php?err=email_taken");
	exit();
}

//all is well

//insert into table
$query="INSERT INTO users (username, email, firstname, lastname, password, usertype) VALUES";
$query.="('{$username}', '{$email}', '{$firstname}', '{$lastname}', md5('{$password}'), '{$usertype}');";
$result=$mysqli->query($query);

if(!$result) {
	header("Location: index.php?err=failed");
	exit();
}
header("Refresh: 3; URL=index.php");

?>

<h3>Sign Up success</h3>