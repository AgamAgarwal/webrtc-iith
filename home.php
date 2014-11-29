<?php
session_start();
if(!isset($_SESSION['uid'])) {
	header("Location: index.php");
	exit();
}
?>
<html>
<title>
	Home
</title>
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<script type="text/javascript" src="./files/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="./files/jquery.redirect.js"></script>
<script type="text/javascript" src="./files/bootstrap.min.js"></script>
<script type="text/javascript" src="./files/heartbeat.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		bind_onclick_functions=function() {
			$(".list-group-item").click(function(){
				var peer=$(this);
				$(".panel-content").html('');
				 $(".panel-content").show();
				 $(".panel-note").html('');
				 $(".panel-note").show();
				 $(".panel-content").append('<table class="table table-striped"><thead><tr><th>User ID</th><th>First Name</th><th>Last Name</th><th>Username</th></tr></thead><tbody>'
				 							+'<tr><td>'+peer.attr('id')
				 							+'<td>'+peer.attr('firstname')
				 							+'</td><td>'+peer.attr('lastname')
				 							+'</td><td>'+peer.attr('username')
				 							+'</td></tr>'
				 							+'</tbody></table>'
				 							+(peer.attr('online')=='1'?'<center><button type="button" class="btn btn-success" onclick="call('+peer.attr('id')+')">Connect</button></center>':''));
				 $(".panel-note").append('<blockquote><p>No Notes to display</p></blockquote>');
			});
		};

		update_online_list=function() {
			$.ajax({
				url: "online_list.php",
				error: function(jqXHR, textStatus, errorThrown) {
					console.log("Error while getting list: "+textStatus+" : "+errorThrown);
				},
				success: function(data, textStatus, jqXHR) {
					console.log($.parseJSON(data));
					var list=$.parseJSON(data);
					$("#online-list").html('');
					for(var i=0; i<list.length; i++) {
						$("#online-list").append("<li class=\"list-group-item\" "
												+"id=\""+list[i].id+"\" "
												+"username=\""+list[i].username+"\" "
												+"firstname=\""+list[i].firstname+"\" "
												+"lastname=\""+list[i].lastname+"\" "
												+"online=\""+list[i].online+"\">"
												+list[i].firstname+" "+list[i].lastname
												+(list[i].online=="1"?"<image style=\"float:right\" src=\"images/online.png\"/>":"")
												+"</li>");
					}
					bind_onclick_functions();
				}
			});
		}
		update_online_list();
		var online_list_timer=setInterval(update_online_list, 5000);


		call=function(peerID) {
			$.ajax({
				url: "call.php",
				type: "POST",
				data: {id: peerID},
				success: function(data, textStatus, jqXHR) {
					console.log(data);
					var data=$.parseJSON(data);
					if(data.allowed==1) {
						$.redirectPost("chat.php", {sessionID: data.sessionID, type: "offerer"});
					} else {
						alert("Unable to make a call. Please try again.");
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					alert("Unable to make a call. Please try again.");
				}
			});
		};

		check_incoming_calls=function() {
			$.ajax({
				url: "incoming_call.php",
				success: function(data, textStatus, jqXHR) {
					var data=$.parseJSON(data);
					console.log("recv from incoming_call.php");
					console.log(data);
					if(data.call==1) {
						$("#caller-name").text(data.name);
						$("#call-invitation").show();
						$("#incoming-decline").click(function() {
							$("#call-invitation").hide();
							//TODO: delete session request from table
						});
						$("#incoming-accept").click(function() {
							$.redirectPost("chat.php", {sessionID: data.sessionID, type: "answerer"});
						});
						setTimeout(function() {$("#call-invitation").hide();}, 120000);
					}
				}
			});
		};

		var incoming_call_timer=setInterval(check_incoming_calls, 4000);
	});

</script>

<link rel="stylesheet" href="./files/bootstrap.min.css">
<link rel="stylesheet" href="./files/bootstrap-theme.min.css">
<link rel="stylesheet" href="./files/player.css">
<link rel="stylesheet" href="./files/app.css">
</head>

<body>
		<div class="container">
			<nav id="nav">
			<a href="home.php" style = "font-size:26px;">WebRTC Project</a>
			<a href="#" style = ><?php echo $_SESSION['username'];?></a>
			<a href="logout.php" style="float:right"><code>Logout</code></a>
			<a href="./files/documentation.pdf" style = "float:right;"><code>about project</code></a>
			<a href="https://github.com/AgamAgarwal/webrtc-iith/" style = "float:right;"><code>&lt;code&gt;</code></a>
			</nav>   
			<div class="row">
			<div class="col-md-4">    
				
				<div class="panel panel-default">     
				<div class="panel-heading">
				<h3 class="panel-title">Online</h3>
				</div>           
				<ul id="online-list" class="list-group" style="max-height:75%; height:75%; word-wrap: break-word; overflow-y:scroll">
				</ul>  
				</div> 
			</div>
			<div class="col-md-8">    
				<div class="panel panel-default" style="max-height:35%; height:35%;">     
					<div class="panel-heading">
						<h3 class="panel-title">Details</h3>
					</div> 
					<div class="panel-content" style="display:none">
					</div>
				</div>
				
				<div class="panel panel-default" id="call-invitation" style="max-height: 20%; height:20%; width: 20%; float: right; display: none;">
					<div class="panel-heading"><span id="caller-name"></span> wants to talk to you!</div>
					<div class="panel-call">
						<button type="button" id="incoming-accept" class="btn btn-success">Accept</button>
						<button type="button" id="incoming-decline" class="btn btn-failure">Decline</button>
					</div>
				</div>

				<div class="panel panel-default" style="max-height:35%; height:35%;">  
					<div class="panel-heading">
						<h3 class="panel-title">Notes</h3>
					</div> 
					<div class="panel-note" style="display:none">
					</div>      
				</div> 
			</div>
		</div>
	</div>
</body>
</html>
