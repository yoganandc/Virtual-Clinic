(function() {
	navigator.getUserMedia = navigator.getUserMedia || navigator.mozGetUserMedia || navigator.webkitGetUserMedia;

	var stream;
	var video;
	var canvas;
	var photo;
	var startButton;
	var pictureType = null;
	var streaming = false;
	var leftOffset;
	var SIDE = 225;

	function pageReady() {
		var currentPictureType = null;
		var radios = document.getElementsByName("upload_type");
		for(var i = 0; i < radios.length; i++) {
			if(radios[i].checked) 
				pictureType = currentPictureType = Number(radios[i].value);
			radios[i].addEventListener("click", radioHandler);
		}

		if(!currentPictureType)
			pictureType = currentPictureType = 0;

		switch(currentPictureType) {
			case 0:
				break;
			case 1:
				document.getElementById("file-row").style.display = "table-row";
				break;
			case 2:
				document.getElementById("camera-row").style.display = "table-row";
				startCamera();
				break;
		}

		video = document.getElementById('video');
	    canvas = document.getElementById('canvas');
	    photo = document.getElementById('photo');
	    startButton = document.getElementById('startbutton');

	    canvas.setAttribute("width", SIDE);
	    canvas.setAttribute("height", SIDE);

	    startButton.addEventListener("click", function(evt) { if(streaming) takePicture(); evt.preventDefault(); }, false);
	    clearPicture();
	}

	function radioHandler(evt) {
		var radioVal = Number(evt.target.value);
		if(radioVal != pictureType) {
			pictureType = radioVal;
			switch(radioVal) {
				case 0:
					document.getElementById("file-row").style.display = "none";
					document.getElementById("camera-row").style.display = "none";
					clearFileInput();
					stopCamera();
					break;
				case 1:
					document.getElementById("file-row").style.display = "table-row";
					document.getElementById("camera-row").style.display = "none";
					stopCamera();
					break;
				case 2:
					document.getElementById("file-row").style.display = "none";
					document.getElementById("camera-row").style.display = "table-row";
					clearFileInput();
					startCamera();
					break;
			}
		}
	}

	function startCamera() {
		if(navigator.getUserMedia) {
	    	navigator.getUserMedia({video: true, audio: false}, function(localStream) { 
	    		video.src = window.URL.createObjectURL(localStream);
	    		stream = localStream;
	    		video.addEventListener('canplay', function(evt) { 
	    			if(!streaming) 
	    				streaming = true;
				    leftOffset = ((SIDE * (video.videoWidth/video.videoHeight)) - SIDE)/2;
				    video.style.left = "-" + Math.round(leftOffset) + "px";
	    		}, false);
	    	}, function(error) { console.log(error); })
	    }
	}

	function stopCamera() {
		if(streaming) {
			streaming = false;
			stream.stop();
		}
		clearPicture();
		document.getElementById("encoded_picture").value = "";
	}

	function takePicture() {
		var context = canvas.getContext('2d');
		context.drawImage(video, Math.round(leftOffset * (video.videoHeight/SIDE)), 0, video.videoHeight, video.videoHeight, 0, 0, canvas.width, canvas.height);
		var data = canvas.toDataURL('image/png');
		photo.setAttribute("src", data);
		var splitData = data.split(",");
		document.getElementById("encoded_picture").value = splitData[1];
	}

	function clearPicture() {
		var context = canvas.getContext('2d');
	    context.fillStyle = "#AAA";
	    context.fillRect(0, 0, canvas.width, canvas.height);
    	photo.setAttribute('src', canvas.toDataURL('image/png'));
   	}

	function clearFileInput() {
		var oldInput = document.getElementById("picture");
		var newInput = document.createElement("input");
		newInput.setAttribute("type", "file");
		newInput.id = oldInput.id;
		newInput.name = oldInput.name;
		oldInput.parentNode.replaceChild(newInput, oldInput);
	}

	window.addEventListener("load", pageReady);
})();
