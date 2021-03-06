<?php
session_start();
if(!isset($_SESSION['uid']) || !isset($_POST['sessionID']) || !isset($_POST['type'])
 || ($_POST['type']!="offerer" && $_POST['type']!="answerer")) {
	header("Location: index.php");
	exit();
}
?>
<html>
<title>
  WebRTC Project
</title>
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<script type="text/javascript" src="./files/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="./files/bootstrap.min.js"></script>

<script type="text/javascript" src="./files/adapter.js"></script>

<script type="text/javascript">
//javascript to set variables used by webrtc.js
type=<?php echo "\"".$_POST['type']."\""; ?>;
otherType=<?php echo "\"".($_POST['type']=="offerer"?"answerer":"offerer")."\""; ?>;
usertype=<?php echo "\"".$_SESSION['usertype']."\""; ?>;
sessionID=<?php echo "\"".$_POST['sessionID']."\""; ?>;
</script>
<script type="text/javascript" src="./files/webrtc.js"></script>
<script type="text/javascript" src="./files/heartbeat.js"></script>

<link rel="stylesheet" href="./files/bootstrap.min.css">
<link rel="stylesheet" href="./files/bootstrap-theme.min.css">
<link rel="stylesheet" href="./files/player.css">
<link rel="stylesheet" href="./files/app.css">
</head>

<body onload="init()">
	<div id="container">  
  
	<nav id="nav">
	  <a href="home.php" style = "font-size:26px;">WebRTC Project</a>
	  <a href="#" style = ><?php echo $_SESSION['username'];?></a>
	  <a href="logout.php" style="float:right"><code>Logout</code></a>
	  <a href="./files/documentation.pdf" style = "float:right;"><code>about project</code></a>
	  <a href="https://github.com/AgamAgarwal/webrtc-iith/" style = "float:right;"><code>&lt;code&gt;</code></a>
	</nav>	  
	
	<div class="alert alert-danger ng-binding" ng-show="errorText" ng-bind="errorText" style="display: none;"></div>
		
	<div class="row">
	  <div class="col-md-4">	  
			  
		<div class="panel panel-default" id="chat-room-link"></div> 
	   

	   <div id="messages">
			<div class="panel panel-default">			
			  <div class="panel-heading">
				<h3 class="panel-title">Conversation</h3>
			  </div>					 
			  <ul class="list-group">
					<li class="list-group-item">
						<div class="input-group">
							<input type="text" class="form-control message-text ng-pristine ng-valid" ng-model="messageText" placeholder="Type your message here" id="message" onkeydown="checkKey(event)" disabled="true">
							<span class="input-group-btn">
							  <button class="btn btn-default" id="msgButton" onclick="sendMessage()" disabled="true">Send!</button>
							</span>
					  </div>
				  </li>
				  <li class="list-group-item" id="chat-msgs" style="max-height:200px; height:200px; word-wrap: break-word; overflow-y:scroll" ></li>
			  </ul>  
			</div>   
		</div>

	  </div>
	  
	  <div class="col-md-8">   
			  
		<div id="video">
		  <div class="panel panel-default">		  
			<div class="panel-heading">
			  <h3 class="panel-title">Video</h3>
			</div>			
			<div class="panel-body">
			  <div class="local-video-wrap" style="height: 96px; width: 128px;">	
				<video class="local-video" style="height: 96px; width: 128px;" id="local-video" autoplay></video>
				<button id="start-local-video" class="btn btn-primary" ng-click="startLocalVideo()" ng-show="remoteVideoPeer &amp;&amp; !localVideoPlaying" style="display: none;">Start video</button>  
			  </div>		   
			  <div class="remote-video-wrap" style="height: 450px; width: 768px;">	   
				<video class="remote-video" id="remote-video" style="height: 450px; width: 768px;" autoplay></video>  
				<div id="control">
				  <div class="panel panel-default">
				  		<center>
		                <button type="button" id="toggle-video" class="btn btn-primary">Turn off video</button>	
				  		</center>
				</div>
			  </div>				   
		  </div>
		</div>			  
	  </div> 
	</div> 
  </div>

</div></body></html>