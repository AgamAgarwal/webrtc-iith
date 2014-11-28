<?php
session_start();
if(!isset($_SESSION['uid'])) {
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
<script type='text/javascript' src='https://cdn.firebase.com/v0/firebase.js'></script>
<script type="text/javascript" src="./files/webrtc.js"></script>

<link rel="stylesheet" href="./files/bootstrap.min.css">
<link rel="stylesheet" href="./files/bootstrap-theme.min.css">
<link rel="stylesheet" href="./files/player.css">
<link rel="stylesheet" href="./files/app.css">
</head>

<body onload="init()">
	<div id="container">  
  
	<nav id="nav">
	  <a href="#" style = "font-size:26px;">WebRTC Project</a></h1>
	  <a href="logout.php" style="float:right"><code>Logout</code></a>
	  <a href="./files/documentation.pdf" style = "float:right;"><code>about project</code></a>
	  <a href="https://github.com/AgamAgarwal/webrtc-iith/" style = "float:right;"><code>&lt;code&gt;</code></a>
	</nav>	  
	
	<div class="alert alert-danger ng-binding" ng-show="errorText" ng-bind="errorText" style="display: none;"></div>
		
	<div class="row">
	  <div class="col-md-4">	  
			  
		<div ng-show="!isLoggedIn()" class="panel panel-default">
		  <div class="panel-heading">
			<h3 class="panel-title">Chat Room Link</h3>
		  </div>
		  <div class="panel-body">
			  <center>
				  <div class="input-group">
						<p type="text" class="form-control ng-pristine ng-valid" ng-model="handle" id="chat-room-link">
						
						</p>
				  </div>
			  </center>
		  </div>   
		</div> 
	   

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
				  <li class="list-group-item" id="chat-msgs" style="max-height:270px; height:270px; word-wrap: break-word; overflow-y:scroll" ></li>
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
			  </div>				   
		  </div>

		  <div class="modal fade" id="incoming-call-modal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-sm">
			  <div class="modal-content">
				<div class="modal-header">
				  <h4 class="modal-title">Incoming call...</h4>
				</div>
				<div class="modal-body">
				  <p>You have an incoming video call from <span class="caller"></span></p>
				  <div class="btn-group btn-group-justified">
					<div class="btn-group">
					  <button type="button" class="btn btn-success accept">Accept</button>
					</div>
					<div class="btn-group">
					  <button type="button" class="btn btn-danger reject">Reject</button>
					</div>
				  </div>
				</div>
			  </div>
			</div>
		  </div>
		</div>			  
	  </div> 
	</div> 
  </div>

</div></body></html>