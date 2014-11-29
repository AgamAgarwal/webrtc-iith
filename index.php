<?php
session_start();
if(isset($_SESSION) && isset($_SESSION['uid'])) {
	header("Location: home.php");
	exit();
}
?>
<html>
<title>
  Sign in/Sign Up
</title>
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<script type="text/javascript" src="./files/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="./files/bootstrap.min.js"></script>

<link rel="stylesheet" href="./files/bootstrap.min.css">
<link rel="stylesheet" href="./files/bootstrap-theme.min.css">
<link rel="stylesheet" href="./files/player.css">
<link rel="stylesheet" href="./files/app.css">
</head>

<body>
	<div class="container"> 
		<nav id="nav">
		  <a href="#" style = "font-size:26px;">WebRTC Project</a></h1> 
		  <a href="./files/documentation.pdf" style = "float:right;"><code>about project</code></a>
		  <a href="https://github.com/AgamAgarwal/webrtc-iith/" style = "float:right;"><code>&lt;code&gt;</code></a>
		</nav>   
		<div id="loginbox" style="margin-top:50px;" class="mainbox col-md-10 col-md-offset-1 col-sm-8 col-sm-offset-1">                    
			<div class="panel panel-info" >
					<div class="panel-heading">
						<div class="panel-title">Sign In</div>
						<div style="float:right; position: relative; top:-10px"> Don't have an account! 
										<a href="#" onClick="$('#loginbox').hide(); $('#signupbox').show()">
											Sign Up Here
										</a></div>
					</div>     

					<div style="padding-top:30px" class="panel-body" >

						<div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
							
						<form action="login_submit.php" id="loginform" class="form-horizontal" role="form" method="post">
									
							<div style="margin-bottom: 25px" class="input-group">
										<span class="input-group-addon"></span>
										<input id="username" type="text" class="form-control" name="username" value="" placeholder="username or email">                                        
									</div>
								
							<div style="margin-bottom: 25px" class="input-group">
										<span class="input-group-addon"></span>
										<input id="password" type="password" class="form-control" name="password" placeholder="password">
									</div>


							
								<div class="form-group">
									</br>                                        
									<div class="col-md-9">
										<button id="btn-signup" type="submit" class="btn btn-info">Sign In</button>
									</div>
								</div>   
							</form>     



						</div>                     
					</div>  
		</div>
		<div id="signupbox" style="display:none; margin-top:50px" class="mainbox col-md-10 col-md-offset-1 col-sm-8 col-sm-offset-1">
					<div class="panel panel-info">
						<div class="panel-heading">
							<div class="panel-title">Sign Up</div>
							<div style="float:right; position: relative; top:-10px"><a id="signinlink" href="#" onclick="$('#signupbox').hide(); $('#loginbox').show()">Sign In</a></div>
						</div>  
						<div class="panel-body" >
							<form action="signup_submit.php" id="signupform" class="form-horizontal" role="form" method="post">
								
								<div id="signupalert" style="display:none" class="alert alert-danger">
									<p>Error:</p>
									<span></span>
								</div>
									
								
								  
								<div class="form-group">
									<label for="email" class="col-md-3 control-label">Email</label>
									<div class="col-md-9">
										<input type="email" class="form-control" name="email" placeholder="Email Address">
									</div>
								</div>
								
								<div class="form-group">
									<label for="username" class="col-md-3 control-label">Username</label>
									<div class="col-md-9">
										<input type="text" class="form-control" name="username" placeholder="Username">
									</div>
								</div>
								
								<div class="form-group">
									<label for="firstname" class="col-md-3 control-label">First Name</label>
									<div class="col-md-9">
										<input type="text" class="form-control" name="firstname" placeholder="First Name">
									</div>
								</div>
								<div class="form-group">
									<label for="lastname" class="col-md-3 control-label">Last Name</label>
									<div class="col-md-9">
										<input type="text" class="form-control" name="lastname" placeholder="Last Name">
									</div>
								</div>
								
								<div class="form-group">
									<label for="usertype" class="col-md-3 control-label">User Type</label>
										<div class="col-md-3">
											<input type="radio" name="usertype" id="optionsRadios1" value="doctor" >
											Doctor
										</div>
										<div class="col-md-3">
											<input type="radio" name="usertype" id="optionsRadios2" value="student">
											Student
										</div>
									</label>
								</div>
								<div class="form-group">
									<label for="password" class="col-md-3 control-label">Password</label>
									<div class="col-md-9">
										<input type="password" class="form-control" name="password" placeholder="Password">
									</div>
								</div>
									
								<div class="form-group">
									<!-- Button -->                                        
									<div class="col-md-offset-3 col-md-9">
										<button id="btn-signup" type="submit" class="btn btn-info">Sign Up</button>
									</div>
								</div>
								
							</form>
						 </div>
					</div>

			   
			   
				
		 </div> 
	</div>
</body></html>
	