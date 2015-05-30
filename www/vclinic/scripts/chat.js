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

var POLL_INTERVAL_UPDATE = 10000;
var POLL_INTERVAL_CHAT = 2000;
var ASSIGNED_STATUS_OFFLINE = 1;
var ASSIGNED_STATUS_ONLINE = 2;
var ASSIGNED_STATUS_NULL = 0;
var HOST = window.location.protocol+"//"+window.location.hostname
var SERVER_LOCATION = HOST+"/vclinic/ajax/";
var SIGNAL_SERVER_LOCATION = HOST+":8888/";
var COOKIE_PAGEOPEN = "pageopen";
var CHAT_OPEN = "OPEN";
var CHAT_CLOSE = "CLOSED";

var requestUpdater = null;
var requestMessenger = null;
var updateTimerID = null;
var pollInterval = null;
var panel = null;
var room = null;
var runWebRTC = null;

var assignedStatus = ASSIGNED_STATUS_NULL;

window.addEventListener("load", readyChat);

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

	assignedStatus = document.getElementById("chat-container").getAttribute("data-status");
	if(assignedStatus == ASSIGNED_STATUS_NULL)
		return;

	//ADD CODE TO HANDLE NO ASSIGNED USER

	document.getElementById("send").addEventListener("click", sendHandler);
	document.getElementById("send-text").addEventListener("keyup", function(evt) { sendTextHandler(evt); });

	panel = document.getElementById("text-chat-panel");
	room = document.getElementById("chat-container").getAttribute("data-room");

	var requestPreviousMessages = createXMLHttpRequest();
	requestPreviousMessages.onreadystatechange = function() {
		if((requestPreviousMessages.readyState == 4) && (requestPreviousMessages.status == 200)) {
			var jsonData = JSON.parse(requestPreviousMessages.responseText);
			setupMessages(jsonData);
		}
	};
	requestPreviousMessages.open("GET", SERVER_LOCATION+"previousmessages.php", true);
	requestPreviousMessages.setRequestHeader("X-Requested-With", "XMLHttpRequest");
	requestPreviousMessages.send(null);

	requestUpdater = createXMLHttpRequest();
	requestMessenger = createXMLHttpRequest();

	if(assignedStatus == ASSIGNED_STATUS_OFFLINE) {
		pollInterval = POLL_INTERVAL_UPDATE;
	}

	if(assignedStatus == ASSIGNED_STATUS_ONLINE) {
		pollInterval = POLL_INTERVAL_CHAT;
	}
	if(runWebRTC) {
		pageReady();
	}

	sendUpdate();
	updateTimerID = setTimeout(function() { keepUpdating(); }, pollInterval);
}

function createXMLHttpRequest() {
	try {
		return new XMLHttpRequest();
	}
	catch(e) {
		return null;
	}
}

function sendUpdate() {
	if(requestUpdater) {
		try {
			requestUpdater.onreadystatechange = function() {
				if((requestUpdater.readyState == 4) && (requestUpdater.status == 200)) {
					console.log('updated');
					var jsonData = JSON.parse(requestUpdater.responseText);
					updateChat(jsonData.status, jsonData.messages);
				}
			};
			requestUpdater.open("POST", SERVER_LOCATION+"updatestatus.php", true);
			requestUpdater.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
			requestUpdater.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			requestUpdater.send(null);
		}
		catch(e) {
			console.log("update ping unsuccessful.\n");
		}
	}
}

function keepUpdating() {
	sendUpdate();
	updateTimerID = window.setTimeout(function() { keepUpdating(); }, pollInterval);
}

function updateChat(status, messages) {
	if(status != assignedStatus) {
		assignedStatus = status;
		if(status == ASSIGNED_STATUS_ONLINE) {
			clearTimeout(updateTimerID);
			pollInterval = POLL_INTERVAL_CHAT;
			updateTimerID = setTimeout(function() { keepUpdating(); }, pollInterval);
		}
		else {
			clearTimeout(updateTimerID);
			pollInterval = POLL_INTERVAL_UPDATE;
			updateTimerID = setTimeout(function() { keepUpdating(); }, pollInterval);
		}
	}
	if(assignedStatus == ASSIGNED_STATUS_ONLINE) {
		try {
			if(messages) {
				for(var i=0; i<messages.length; i++) {
					var divElem = document.createElement("div");
					divElem.className = "message";
					var elem = document.createElement("p");
					elem.className = "message-from";
					elem.appendChild(document.createTextNode(messages[i]));
					divElem.appendChild(elem);
					panel.appendChild(divElem);
					panel.scrollTop = panel.scrollHeight;
				}
			}
		}
		catch(e) {
			//do nothing
		}
	}
}

function replaceText(id, newText) {
	var node = document.getElementById(id);
	while(node.firstChild)
		node.removeChild(node.firstChild);
	node.appendChild(document.createTextNode(newText));
}

function sendMessage(message) {
	if(requestMessenger) {
		try {
			requestMessenger.onreadystatechange = function() {
				if((requestMessenger.readyState == 4) && (requestMessenger.status == 200)) {
					
				}
			};
			requestMessenger.open("POST", SERVER_LOCATION+"sendmessage.php", true);
			requestMessenger.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
			requestMessenger.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			requestMessenger.send("message="+message);
		}
		catch(e) {
			console.log("update ping unsuccessful.\n");
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

function sendTextHandler(evt) {
	if(evt.keyCode == 13)
		sendHandler();
	else
		return;
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

function pageReady() {
    localVideo = document.getElementById('localVideo');
    remoteVideo = document.getElementById('remoteVideo');

    serverConnection = new WebSocket('ws://127.0.0.1:3434');

    function sendRoomInfo() {
        console.log('sendRoomInfo');
        if(serverConnection.readyState == 1) {
            serverConnection.send(JSON.stringify({'room': room}));
        }
        else {
            setTimeout(sendRoomInfo, 5);
        }
    }
    sendRoomInfo();

    serverConnection.onmessage = function(message) {
        if(!peerConnection) start(false);

        var signal = JSON.parse(message.data);
        if(signal.sdp) {
            console.log('received sdp info: '+signal.identity);
            peerConnection.setRemoteDescription(new RTCSessionDescription(signal.sdp), function() {
                peerConnection.createAnswer(function(localDescription) {
                    peerConnection.setLocalDescription(localDescription, function () {
                        console.log('sending sdp answer');
                        var tmp3 = identity + "-" + (j++);
                        serverConnection.send(JSON.stringify({'sdp': localDescription, 'identity': tmp3}));
                    }, errorHandler);
                }, errorHandler);
            }, errorHandler);
        } else if(signal.ice) {
            console.log('received ice candidate: '+signal.identity);
            peerConnection.addIceCandidate(new RTCIceCandidate(signal.ice));
        } else if(signal.hangup) {
            console.log('call ended');
            peerConnection.close();
            peerConnection = null;
            document.getElementById("localvideo-container").style.display = "none";
            document.getElementById("status").className = "offline";
			replaceText("status", "OFFLINE");
        }
    };

     window.addEventListener("beforeunload", function() {
        serverConnection.send(JSON.stringify({'hangup': true}));
    });

    if(navigator.getUserMedia) {
        navigator.getUserMedia({ video: true, audio: true }, function(stream) {
            localStream = stream;
            localVideo.src = window.URL.createObjectURL(stream);
            if(assignedStatus == ASSIGNED_STATUS_ONLINE) {
				start(true);
			}
        }, errorHandler);
    } else {
        alert('Your browser does not support getUserMedia API');
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
		document.getElementById("status").className = "online";
		replaceText("status", "ONLINE");
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
