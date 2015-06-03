(function() {
	navigator.getUserMedia = navigator.getUserMedia || navigator.mozGetUserMedia || navigator.webkitGetUserMedia;

	var video;
	var canvas;
	var camera;
	var photo;
	var output;
	var startButton;
	var streaming = false;

	var INITIAL_HEIGHT = 480;
	var WIDTH = 640;

	window.addEventListener("load", pageReady);

	function pageReady() {
		document.getElementById("cancel-test").addEventListener("click", function(evt) { window.close(); evt.preventDefault(); });

		video = document.getElementById('video');
		canvas = document.getElementById('canvas');
		camera = document.getElementById('camera');
	    photo = document.getElementById('photo');
	    output = document.getElementById('output');
	    startButton = document.getElementById('startbutton');

	    canvas.setAttribute('width', WIDTH);

		if(navigator.getUserMedia) {
	    	navigator.getUserMedia({video: true, audio: false}, function(localStream) { 
	    		video.src = window.URL.createObjectURL(localStream);
	    		video.addEventListener('canplay', function(evt) { 
	    			if(!streaming) 
	    				streaming = true;
	    			var ratio = video.videoWidth / video.videoHeight;
	    			var height = Math.round(WIDTH / ratio);
	    			video.setAttribute("height", height);
	    			canvas.setAttribute("height", height);
	    			camera.setAttribute("height", height);
	    			output.setAttribute("height", height);
	    			window.resizeBy(0, (-(INITIAL_HEIGHT - height)));
	    			clearPicture();
	    		}, false);
	    	}, function(error) { console.log(error); })
	    }

	    startButton.addEventListener("click", function(evt) { if(streaming) takePicture(); evt.preventDefault(); }, false);
	}

	function takePicture() {
		var context = canvas.getContext('2d');
		context.drawImage(video, 0, 0, canvas.width, canvas.height);
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
})();