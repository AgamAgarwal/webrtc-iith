function hasGetUserMedia() {
	navigator.getUserMedia=navigator.webkitGetUserMedia ||
		navigator.mozGetUserMedia ||
		navigator.msGetUserMedia ||
		navigator.getUserMedia;
	return !!navigator.getUserMedia;
}

function setCompatibilty() {
	//window.RTCPeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;
	SessionDescription = window.RTCSessionDescription = window.RTCSessionDescription || window.mozRTCSessionDescription || window.webkitRTCSessionDescription;
	IceCandidate = window.RTCIceCandidate;// = window.RTCIceCandidate || window.mozRTCIceCandidate || window.webkitRTCIceCandidate;
}

function randomID() {
	return (Math.random() * 10000 + 10000 | 0).toString();
}

function sendToDB(dataType, value) {
	if(dataType!="sdp" && dataType!="candidate")
		return;
	$.ajax({
		url: "insert_data.php",
		type: "POST",
		data: {dataType: dataType, data: JSON.stringify(value), sessionID: sessionID},
		success: function(resp, textStatus, jqXHR) {
			var resp=$.parseJSON(resp);
			console.log("send resp:")
			console.log(resp);
			if(resp.success==1)
				console.log("data inserted at server");
			else
				setTimeout(function() {sendToDB(dataType, value);}, 500);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			setTimeout(function() {sendToDB(dataType, value);}, 500);
		}
	});
}

function recvFromDB(dataType, callback) {
	if(dataType!="sdp" && dataType!="candidate")
		return;
	$.ajax({
		url: "get_data.php",
		type: "POST",
		data: {dataType: dataType, sessionID: sessionID},
		success: function(resp, textStatus, jqXHR) {
			var resp=$.parseJSON(resp);
			console.log("receive resp: ");
			console.log(resp);
			if(resp.success==1 && resp.data!=null)
				callback(resp.data);
			else
				setTimeout(function() {recvFromDB(dataType, callback);}, 1000);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			setTimeout(function() {recvFromDB(dataType, callback);}, 1000);
		}
	});
}

OFFERER="offerer";
ANSWERER="answerer";
CANDIDATE="candidate";
SDP="sdp";
GUM_CONSTRAINTS={
	video: true,
	audio: true
};

SDP_CONSTRAINTS = {
	mandatory: {
		OfferToReceiveAudio: true,
		OfferToReceiveVideo: true
	}
};

function init() {
	/*if(!hasGetUserMedia()) {
		alert("Sorry! getUserMedia() is not supported by your browser.");
		return;
	}*/

	setCompatibilty();

	//getting elements
	var local_video=document.querySelector('#local-video');
	var remote_video=document.querySelector('#remote-video');
	var chat_msgs=document.querySelector('#chat-msgs');
	var message=document.querySelector('#message');
	var sendButton=document.querySelector('#msgButton');
	var channel=null;
	var localMediaStream=null

	var server = {
	iceServers: [
				createIceServer("stun:23.21.150.121"),
				createIceServer("stun:stun.l.google.com:19302"),
				createIceServer("turn:numb.viagenie.ca", "louis%40mozilla.com", "webrtcdemo")
				]
	};
	console.log(server);

	var options = {
	optional: [
		{DtlsSrtpKeyAgreement: true}
		]
	};

	//create the PeerConnection
	var conn = new RTCPeerConnection(server, options);

	conn.onicecandidate = function (e) {
		// take the first candidate that isn't null
		if (!e.candidate) { return; }
		conn.onicecandidate = null;

		// request the other peers ICE candidate
		recvFromDB(CANDIDATE, function (candidate) {
			console.log(candidate);
			conn.addIceCandidate(new IceCandidate(JSON.parse(candidate)));
			console.log("ice candidate added");
		});

		console.log("befoer");

		// send our ICE candidate
		sendToDB(CANDIDATE, e.candidate);

		console.log("asfgter");
	};


	var errorCallback=function(e) {
		console.log("Error: ", e);
	};

	var successCallback=function(stream) {
		local_video.src=URL.createObjectURL(stream);
		local_video.muted=true;
		conn.addStream(stream);
		localMediaStream=stream;
		$("#toggle-video").click(function() {
			var videoTracks=localMediaStream.getVideoTracks();
			if(videoTracks[0]) {
				if(videoTracks[0].enabled) {
					videoTracks[0].enabled=false;
					$("#toggle-video").text("Turn on video");
				} else {
					videoTracks[0].enabled=true;
					$("#toggle-video").text("Turn off video");
				}
			}
		});
		connect();
	}

	conn.onaddstream = function (e) {
		remote_video.src = URL.createObjectURL(e.stream);
	};

	//get user media
	//navigator.
	getUserMedia(GUM_CONSTRAINTS, successCallback, errorCallback)

	//the connection function - this is where everything happens
	function connect() {
		if(type === OFFERER) {
			console.log("You are offerer");
			channel=conn.createDataChannel("datachannel", {});

			bindMethods();

			//create offer SDP
			conn.createOffer(function(offer) {
				conn.setLocalDescription(offer);

				//send the offer SDP to FireBase
				sendToDB(SDP, offer);

				//wait for an answer SDP from FireBase
				recvFromDB(SDP, function(answer) {
					conn.setRemoteDescription(
						new SessionDescription(JSON.parse(answer))
					);
				});
			}, errorCallback, SDP_CONSTRAINTS);

		} else {
			console.log("You are answerer");
			conn.ondatachannel = function (e) {
				channel=e.channel;
				bindMethods();
			};

			//answerer needs to wait for an offer before generating the answer SDP
			recvFromDB(SDP, function (offer) {
				console.log("answerer");
				console.log(offer);
				conn.setRemoteDescription(
					new SessionDescription(JSON.parse(offer))
				);
				console.log("answerer");
				// now we can generate our answer SDP
				conn.createAnswer(function (answer) {
					conn.setLocalDescription(answer);
					console.log("answerer");
					// send it to FireBase
					sendToDB(SDP, answer);
				}, errorCallback, SDP_CONSTRAINTS);	
			});	
		}
	}

	//binding methods to data channel
	function bindMethods() {
		channel.onopen=function(){console.log("Data Channel open.")};

		channel.onmessage = function (e) {
			chat_msgs.innerHTML="<span style=\"color:red\">Peer:</span> "+e.data+"<br/>"+chat_msgs.innerHTML;
		};

		message.disabled=false;
		sendButton.disabled=false;
	}

	sendMessage=function() {
		if(channel==null)
			return;
		channel.send(message.value);
		chat_msgs.innerHTML="<span style=\"color:blue\">You:</span> "+message.value+"<br/>"+chat_msgs.innerHTML;
		message.value="";
	}

	checkKey=function(e) {
		console.log(e);
		if(e.keyCode==13)
			sendMessage();
	}
}