'use strict';
var RTCPeerConnection=null;
var getUserMedia=null;
var attachMediaStream=null;
var reattachMediaStream=null;
var webrtcDetectedBrowser=null;
var webrtcDetectedVersion=null;

function trace(text){
	if(text[text.length-1]==='\n'){
		text=text.substring(0,text.length-1);
	}
	console.log((window.performance.now()/1000).toFixed(3)+': '+text);
}

function maybeFixConfiguration(pcConfig){
	if(!pcConfig){
		return;
	}
	for(var i=0;i<pcConfig.iceServers.length;i++){
		if(pcConfig.iceServers[i].hasOwnProperty('urls')){
			pcConfig.iceServers[i].url=pcConfig.iceServers[i].urls;
			delete pcConfig.iceServers[i].urls;
		}
	}
}

if(navigator.mozGetUserMedia){
	console.log('This appears to be Firefox');
	webrtcDetectedBrowser='firefox';
	webrtcDetectedVersion=parseInt(navigator.userAgent.match(/Firefox\/([0-9]+)\./)[1],10);
	RTCPeerConnection=function(pcConfig,pcConstraints){
		maybeFixConfiguration(pcConfig);
		return new mozRTCPeerConnection(pcConfig,pcConstraints);
	};
	window.RTCSessionDescription=mozRTCSessionDescription;
	window.RTCIceCandidate=mozRTCIceCandidate;
	getUserMedia=navigator.mozGetUserMedia.bind(navigator);
	navigator.getUserMedia=getUserMedia;
	window.createIceServer=function(url,username,password){
		var iceServer=null;
		var urlParts=url.split(':');
		if(urlParts[0].indexOf('stun')===0){
			iceServer={'url':url};
		}else if(urlParts[0].indexOf('turn')===0){
			if(webrtcDetectedVersion<27){
				var turnUrlParts=url.split('?');
				if(turnUrlParts.length===1||turnUrlParts[1].indexOf('transport=udp')===0){
					iceServer={'url':turnUrlParts[0],'credential':password,'username':username};
				}
			}else{
				iceServer={'url':url,'credential':password,'username':username};
			}
		}
		return iceServer;
	};
	window.createIceServers=function(urls,username,password){
		var iceServers=[];
		for(var i=0;i<urls.length;i++){
			var iceServer=window.createIceServer(urls[i],username,password);
			if(iceServer!==null){
				iceServers.push(iceServer);
			}
		}
		return iceServers;
	};
	attachMediaStream=function(element,stream){
		console.log('Attaching media stream');
		element.mozSrcObject=stream;
	};
	reattachMediaStream=function(to,from){
		console.log('Reattaching media stream');
		to.mozSrcObject=from.mozSrcObject;
	};
} else if(navigator.webkitGetUserMedia){
	console.log('This appears to be Chrome');
	webrtcDetectedBrowser='chrome';
	var result=navigator.userAgent.match(/Chrom(e|ium)\/([0-9]+)\./);
	if(result!==null){
		webrtcDetectedVersion=parseInt(result[2],10);
	}else{
		webrtcDetectedVersion=999;
	}
	window.createIceServer=function(url,username,password){
		var iceServer=null;
		var urlParts=url.split(':');
		if(urlParts[0].indexOf('stun')===0){
			iceServer={'url':url};
		}else if(urlParts[0].indexOf('turn')===0){
			iceServer={'url':url,'credential':password,'username':username};
		}
		return iceServer;
	};
	window.createIceServers=function(urls,username,password){
		console.log("ceae");
		var iceServers=[];
		if(webrtcDetectedVersion>=34){
			iceServers={'urls':urls,'credential':password,'username':username};
		}else{
			for(var i=0;i<urls.length;i++){
				var iceServer=window.createIceServer(urls[i],username,password);
				if(iceServer!==null){
					iceServers.push(iceServer);
				}
			}
		}
		return iceServers;
	};
	RTCPeerConnection=function(pcConfig,pcConstraints){
		if(webrtcDetectedVersion<34){
			maybeFixConfiguration(pcConfig);
		}
		return new webkitRTCPeerConnection(pcConfig,pcConstraints);
	};
	getUserMedia=navigator.webkitGetUserMedia.bind(navigator);
	navigator.getUserMedia=getUserMedia;
	attachMediaStream=function(element,stream){
		if(typeof element.srcObject!=='undefined'){
			element.srcObject=stream;
		}else if(typeof element.mozSrcObject!=='undefined'){
			element.mozSrcObject=stream;
		}else if(typeof element.src!=='undefined'){
			element.src=URL.createObjectURL(stream);
		}else{
			console.log('Error attaching stream to element.');
		}
	};
	reattachMediaStream=function(to,from){
		to.src=from.src;
	};
}else{
	console.log('Browser does not appear to be WebRTC-capable');
}