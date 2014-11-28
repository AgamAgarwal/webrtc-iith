function hasGetUserMedia() {
	navigator.getUserMedia=navigator.getUserMedia ||
		navigator.webkitGetUserMedia ||
		navigator.mozGetUserMedia ||
		navigator.msGetUserMedia;
	return !!navigator.getUserMedia;
}

function setCompatibilty() {
	window.RTCPeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;
	SessionDescription = window.RTCSessionDescription || window.mozRTCSessionDescription || window.webkitRTCSessionDescription;
	IceCandidate = window.RTCIceCandidate || window.mozRTCIceCandidate || window.webkitRTCIceCandidate;
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
	if(!hasGetUserMedia()) {
		alert("Sorry! getUserMedia() is not supported ion your browser.");
		return;
	}

	setCompatibilty();

	//getting elements
	var local_video=document.querySelector('#local-video');
	var remote_video=document.querySelector('#remote-video');
	var chat_room_link=document.querySelector('#chat-room-link');

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

	var server = {
	iceServers: [
		{url: "stun:23.21.150.121"},
		{url: "stun:stun.l.google.com:19302"},
		{url: "turn:numb.viagenie.ca", credential: "webrtcdemo", username: "louis%40mozilla.com"}
		]
	};

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
		conn.addStream(stream);
		connect();
	}

	conn.onaddstream = function (e) {
		remote_video.src = URL.createObjectURL(e.stream);
	};

	//get user media
	navigator.getUserMedia(GUM_CONSTRAINTS, successCallback, errorCallback)

	//the connection function - this is where everything happens
	function connect() {
	if (type === CALLER) {
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

}