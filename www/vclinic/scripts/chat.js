var POLL_INTERVAL_UPDATE = 10000;
var POLL_INTERVAL_CHAT = 2000;
var ASSIGNED_STATUS_OFFLINE = 1;
var ASSIGNED_STATUS_ONLINE = 2;
var ASSIGNED_STATUS_NULL = 0;
var HOST = window.location.protocol+"//"+window.location.hostname
var SERVER_LOCATION = HOST+"/vclinic/ajax/";
var SIGNAL_SERVER_LOCATION = HOST+":8888/";
var COOKIE_NAME = "chatopen";
var COOKIE_PAGEOPEN = "pageopen";
var CHAT_OPEN = "OPEN";
var CHAT_CLOSE = "CLOSED";

var requestUpdater = null;
var requestMessenger = null;
var updateTimerID = null;
var pollInterval = null;
var panel = null;
var room = null;
var webrtc =null;
var runWebRTC = null;

var isChatOpen = false;
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
	document.getElementById("toggle-chat").addEventListener("click", toggleHandler);
	document.getElementById("send").addEventListener("click", sendHandler);
	document.getElementById("send-text").addEventListener("keyup", function(evt) { sendTextHandler(evt); });

	if(assignedStatus == ASSIGNED_STATUS_NULL)
		return;

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
		setupWebRTC();
	}

	var cookie_chatopen = getCookie(COOKIE_NAME);
	if(cookie_chatopen) {
		if(cookie_chatopen == CHAT_OPEN) {
			document.getElementById("chat-body").style.display = "block";
			document.getElementById("toggle-chat").value = "Close Chat";
			isChatOpen = true;
		}
		else {
			document.getElementById("toggle-chat").value = "Open Chat";
		}
	}
	else {
		if(assignedStatus == ASSIGNED_STATUS_ONLINE) {
			document.getElementById("chat-body").style.display = "block";
			isChatOpen = true;
		}
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

var toggleHandler = function() {
	if(!isChatOpen) {
		document.getElementById("chat-body").style.display = "block";
		document.getElementById("toggle-chat").value = "Close Chat";
		isChatOpen= true;
		setCookie(COOKIE_NAME, CHAT_OPEN);
	}
	else {
		document.getElementById("chat-body").style.display = "none";
		document.getElementById("toggle-chat").value = "Open Chat";	
		isChatOpen = false;
		setCookie(COOKIE_NAME, CHAT_CLOSE);
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

			document.getElementById("status").className = "online";
			replaceText("status", "ONLINE");
			requestMessenger = createXMLHttpRequest();
			setupWebRTC();
			if(!isChatOpen) {
				document.getElementById("chat-body").style.display = "block";
				document.getElementById("toggle-chat").value = "Close Chat";
				isChatOpen= true;
			}
		}
		else {
			clearTimeout(updateTimerID);
			pollInterval = POLL_INTERVAL_UPDATE;
			updateTimerID = setTimeout(function() { keepUpdating(); }, pollInterval);

			document.getElementById("status").className = "offline";
			replaceText("status", "OFFLINE");
			requestMessenger = null;
			destroyWebRTC();
			if(isChatOpen) {
				document.getElementById("chat-body").style.display = "none";
				document.getElementById("toggle-chat").value = "Open Chat";	
				isChatOpen = false;
			}
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

function setupWebRTC() {
	if(runWebRTC) {
		document.getElementById("localvideo-container").style.display = "block";
		setCookie(COOKIE_NAME, CHAT_OPEN);

		webrtc = new SimpleWebRTC({
		    localVideoEl: 'localvideo',
		    remoteVideosEl: '',
		    autoRequestMedia: true,
		    url: SIGNAL_SERVER_LOCATION
		});

		webrtc.on('videoAdded', function (video, peer) {
			var videosDiv = document.getElementById("remotevideo");
			var videoDiv = document.createElement("div");
			videoDiv.id = "container_" + webrtc.getDomId(peer);
			videoDiv.appendChild(video);
			video.oncontextmenu = function() { return false; };
			videosDiv.appendChild(videoDiv);
		});

		webrtc.on('videoRemoved', function (video, peer) {
			var videosDiv = document.getElementById("remotevideo");
			var videoDiv = document.getElementById(peer ? 'container_' + webrtc.getDomId(peer) : 'localScreenContainer');
			if(videosDiv && videoDiv)
				videosDiv.removeChild(videoDiv);
		});

		webrtc.on('readyToCall', function () {
		    webrtc.joinRoom(room);
		});
	}
}

function destroyWebRTC() {
	if(runWebRTC) {
		document.getElementById("localvideo-container").style.display = "none";
		setCookie(COOKIE_NAME, CHAT_CLOSE);
		webrtc.stopLocalVideo();
		webrtc.leaveRoom();
		webrtc.connection.disconnect();
		webrtc = null;
	}
}