var chatIncluded = true;
var identity;
var i = 0;
var j = 0;

var localVideo;
var remoteVideo;
var peerConnection;
var peerConnectionConfig = {'iceServers': [{'url': 'stun:stun.services.mozilla.com'}, {'url': 'stun:stun.l.google.com:19302'}]};

navigator.getUserMedia = navigator.getUserMedia || navigator.mozGetUserMedia || navigator.webkitGetUserMedia;
window.RTCPeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;
window.RTCIceCandidate = window.RTCIceCandidate || window.mozRTCIceCandidate || window.webkitRTCIceCandidate;
window.RTCSessionDescription = window.RTCSessionDescription || window.mozRTCSessionDescription || window.webkitRTCSessionDescription;

var SERVER_LOCATION = window.location.protocol+"//"+window.location.hostname+"/vclinic/ajax/";
var SIGNAL_SERVER_LOCATION = 'ws://'+window.location.hostname+":3434/";

var COOKIE_PAGEOPEN = "pageopen";
var CHAT_OPEN = "OPEN";
var CHAT_CLOSE = "CLOSED";

var panel = null;
var room = null;
var user = null;
var assigned = null;
var runWebRTC = null;

var userSet = false;

window.addEventListener("load", readyChat);
window.addEventListener("beforeunload", function() { serverConnection.close(); })

function readyChat() {
	var pageopen = getCookie(COOKIE_PAGEOPEN);
	if(pageopen) {
		if(pageopen == CHAT_CLOSE) {
			runWebRTC = true;
			setCookie(COOKIE_PAGEOPEN, CHAT_OPEN);
		}
		else 
			runWebRTC = false;
	}
	else {
		setCookie(COOKIE_PAGEOPEN, CHAT_OPEN);
		runWebRTC = true;
	}
	
	window.addEventListener("unload", function() {
		setCookie(COOKIE_PAGEOPEN, CHAT_CLOSE);
	});

	if(document.getElementById("chat-container").getAttribute("data-status") == 0)
		return;

	document.getElementById("send").addEventListener("click", sendHandler);
	document.getElementById("send-text").addEventListener("keyup", function(evt) { sendTextHandler(evt); });

	panel = document.getElementById("text-chat-panel");
	user =  document.getElementById("chat-container").getAttribute("data-user");
	assigned = document.getElementById("chat-container").getAttribute("data-assigned");
	room = document.getElementById("chat-container").getAttribute("data-room");

	var requestPreviousMessages = new XMLHttpRequest();
	requestPreviousMessages.onreadystatechange = function() {
		if((requestPreviousMessages.readyState == 4) && (requestPreviousMessages.status == 200)) {
			var jsonData = JSON.parse(requestPreviousMessages.responseText);
			setupMessages(jsonData);
		}
	};
	requestPreviousMessages.open("GET", SERVER_LOCATION+"previousmessages.php", true);
	requestPreviousMessages.setRequestHeader("X-Requested-With", "XMLHttpRequest");
	requestPreviousMessages.send(null);

	if(runWebRTC) {
		pageReady();
	}
}

function setupMessages(jsonData) {
	if(jsonData.success == "0")
		return;
	else {
		for(var i=0;i<jsonData.messages.length;i++) {
			var divElem = document.createElement("div");
			divElem.className = "message";
			var elem = document.createElement("p");
			if(jsonData.messages[i].from == true) 
				elem.className = "message-to";				
			else
				elem.className = "message-from";
			elem.appendChild(document.createTextNode(jsonData.messages[i].message));
			divElem.appendChild(elem);
			panel.appendChild(divElem);
			panel.scrollTop = panel.scrollHeight;
		}
	}
}

function sendHandler() {
	var message = document.getElementById("send-text").value;

	if(!message) 
		return;

	document.getElementById("send-text").value = "";
	var divElem = document.createElement("div");
	divElem.className = "message";
	var elem = document.createElement("p");
	elem.className = "message-to";
	elem.appendChild(document.createTextNode(message));
	divElem.appendChild(elem);
	panel.appendChild(divElem);
	panel.scrollTop = panel.scrollHeight;
	sendMessage(message);
}

function sendTextHandler(evt) {
	if(evt.keyCode == 13)
		sendHandler();
	else
		return;
}

function sendMessage(message) {
	if(runWebRTC) serverConnection.send(JSON.stringify({'chat': message}));
}

function pageReady() {
    localVideo = document.getElementById('localVideo');
    remoteVideo = document.getElementById('remoteVideo');

    serverConnection = new WebSocket(SIGNAL_SERVER_LOCATION);

    function sendRoomInfo() {
        console.log('sendRoomInfo');
        if(serverConnection.readyState == 1) {
            serverConnection.send(JSON.stringify({'user': user, 'assigned': assigned}));
            userSet = true;
        }
        else {
            setTimeout(sendRoomInfo, 5);
        }
    }
    sendRoomInfo();

    serverConnection.onmessage = function(message) {
        var signal = JSON.parse(message.data);
        if(signal.online) {
        	console.log('online');
        	document.getElementById("status").className = "online";
			replaceText("status", "ONLINE");
        	getMedia();
        }
        else if(signal.chat) {
        	updateChat(signal.chat);
        } 
        else if(signal.initiate) {
        	start(true);
        }
        else if(signal.reload) {
            document.getElementById("vc-iframe").contentWindow.location.reload();
        }
        else if(signal.sdp) {
            console.log('received sdp info: '+signal.identity);
            if(!peerConnection) start(false);
            peerConnection.setRemoteDescription(new RTCSessionDescription(signal.sdp), function() {
                peerConnection.createAnswer(function(localDescription) {
                    peerConnection.setLocalDescription(localDescription, function () {
                        console.log('sending sdp answer');
                        var tmp3 = identity + "-" + (j++);
                        serverConnection.send(JSON.stringify({'sdp': localDescription, 'identity': tmp3}));
                    }, errorHandler);
                }, errorHandler);
            }, errorHandler);
        } 
        else if(signal.ice) {
            console.log('received ice candidate: '+signal.identity);
            if(!peerConnection) start(false);
            peerConnection.addIceCandidate(new RTCIceCandidate(signal.ice));
        } 
        else if(signal.hangup) {
            console.log('call ended');
            if(peerConnection) {
	            peerConnection.close();
	            peerConnection = null;
            }
            if((typeof localStream !== 'undefined') && (localStream)) {
	            localStream.stop();
                localStream = null;
	        }
            document.getElementById("localvideo-container").style.display = "none";
            document.getElementById("status").className = "offline";
			replaceText("status", "OFFLINE");
        }
    };
}

function getMedia() {
	if(navigator.getUserMedia) {
        navigator.getUserMedia({ video: true, audio: false }, function(stream) {
            localStream = stream;
            localVideo.src = window.URL.createObjectURL(stream);
            serverConnection.send(JSON.stringify({'ready': true}));
        }, errorHandler);
    } else {
        alert('Your browser does not support video-chat.');
    }
}

function start(isCaller) {
    if(isCaller)
        identity = "Caller";
    else
        identity = "Answerer";

    peerConnection = new RTCPeerConnection(peerConnectionConfig);

    peerConnection.onicecandidate = function(event) {
        if(event.candidate != null) {
            console.log('sending ice candidate');
            var tmp1  = identity + "-" + (i++);
            serverConnection.send(JSON.stringify({'ice': event.candidate, 'identity': tmp1}));
        }
    };

    peerConnection.onaddstream = function(event) {
        console.log("got remote stream");
        remoteVideo.src = window.URL.createObjectURL(event.stream);
        document.getElementById("localvideo-container").style.display = "block";
    };

    peerConnection.addStream(localStream);

    if(isCaller) {
        peerConnection.createOffer(function(localDescription) {
            peerConnection.setLocalDescription(localDescription, function () {
                console.log('sending sdp info');
                var tmp2 = identity + "-" + (j++);
                serverConnection.send(JSON.stringify({'sdp': localDescription, 'identity': tmp2}));
            }, errorHandler);
        }, errorHandler);
    }
}

function errorHandler(error) {
    console.log(error);
}

function updateChat(message) {
	var divElem = document.createElement("div");
	divElem.className = "message";
	var elem = document.createElement("p");
	elem.className = "message-from";
	elem.appendChild(document.createTextNode(message));
	divElem.appendChild(elem);
	panel.appendChild(divElem);
	panel.scrollTop = panel.scrollHeight;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') 
        	c = c.substring(1);
        if (c.indexOf(name) == 0) 
        	return c.substring(name.length, c.length);
    }
    return null;
}

function setCookie(cname, cvalue) {
    document.cookie = cname + "=" + cvalue + "; path=/";
}

function replaceText(id, newText) {
	var node = document.getElementById(id);
	while(node.firstChild)
		node.removeChild(node.firstChild);
	node.appendChild(document.createTextNode(newText));
}
