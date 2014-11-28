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

function sendToDB(ref, room, key, data) {
	ref.child(room).child(key).set(data);
}

function recvFromDB(ref, room, type, callback) {
	ref.child(room).child(type).on("value", function (snapshot, key) {
		var data = snapshot.val();
		if(data)
			callback(data);
	});
}

CALLER="caller";
RECEIVER="receiver";
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
	var chat_room_link=document.querySelector('#chat-room-link');
	var chat_msgs=document.querySelector('#chat-msgs');
	var message=document.querySelector('#message');
	var sendButton=document.querySelector('#msgButton');
	var channel=null;

	//chat_room_link.innerText=location;
	chat_room_link.textContent=location;

	//connect to firebase database
	var dbRef=new Firebase("https://webrtc-iith.firebaseIO.com/");
	var roomRef=dbRef.child("rooms");

	var room_no=location.hash.substr(1);
	var type=RECEIVER, otherType=CALLER;

	//if no room number is given, then this user is the caller
	if (!room_no) {
		room_no = randomID();
		type = CALLER;
		otherType = RECEIVER;

		//chat_room_link.innerText+="#"+room_no;
		chat_room_link.textContent+="#"+room_no;
	}

	var iceservers=createIceServers(["stun:23.21.150.121", "stun:stun.l.google.com:19302", "turn:numb.viagenie.ca"],
		"louis%40mozilla.com",
		"webrtcdemo");



	var server = {
	iceServers: [
				createIceServer("stun:23.21.150.121"),
				createIceServer("stun:stun.l.google.com:19302"),
				createIceServer("turn:numb.viagenie.ca", "louis%40mozilla.com", "webrtcdemo")
				]
	};
	console.log(server);
	/* [
		{url: "stun:23.21.150.121"},
		{url: "stun:stun.l.google.com:19302"},
		{url: "turn:numb.viagenie.ca", credential: "webrtcdemo", username: "louis%40mozilla.com"}
		]
	};*/

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
		recvFromDB(roomRef, room_no, "candidate:" + otherType, function (candidate) {
			conn.addIceCandidate(new IceCandidate(JSON.parse(candidate)));
		});

		console.log("befoer");

		// send our ICE candidate
		sendToDB(roomRef, room_no, "candidate:" + type, JSON.stringify(e.candidate));

		console.log("asfgter");
	};


	var errorCallback=function(e) {
		console.log("Error: ", e);
	};

	var successCallback=function(stream) {
		local_video.src=URL.createObjectURL(stream);
		local_video.muted=true;
		conn.addStream(stream);
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
		if(type === CALLER) {

			channel=conn.createDataChannel("datachannel", {});

			bindMethods();

			//create offer SDP
			conn.createOffer(function(offer) {
				conn.setLocalDescription(offer);

				//send the offer SDP to FireBase
				sendToDB(roomRef, room_no, CALLER, JSON.stringify(offer));

				//wait for an answer SDP from FireBase
				recvFromDB(roomRef, room_no, RECEIVER, function(answer) {
					conn.setRemoteDescription(
						new SessionDescription(JSON.parse(answer))
					);
				});
			}, errorCallback, SDP_CONSTRAINTS);

		} else {

			conn.ondatachannel = function (e) {
				channel=e.channel;
				bindMethods();
			};

			//answerer needs to wait for an offer before generating the answer SDP
			recvFromDB(roomRef, room_no, CALLER, function (offer) {
				console.log("answerer");
				conn.setRemoteDescription(
					new SessionDescription(JSON.parse(offer))
				);
				console.log("answerer");
				// now we can generate our answer SDP
				conn.createAnswer(function (answer) {
					conn.setLocalDescription(answer);
					console.log("answerer");
					// send it to FireBase
					sendToDB(roomRef, room_no, RECEIVER, JSON.stringify(answer));
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