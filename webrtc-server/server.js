var WebSocketServer = require('ws').Server;

var wss = new WebSocketServer({port: 3434});

wss.broadcast = function(data) {
    for(var i in this.clients) {
    	var room = JSON.parse(data).room;
    	if(this.clients[i].room == room) {
    		console.log('send: %s', data);
        	this.clients[i].send(data);
        }
    }
};

wss.on('connection', function(ws) {
    ws.on('message', function(message) {
    	var parsedInfo = JSON.parse(message);
    	if(parsedInfo.room) {
    		console.log('received: %s', message);
    		this.room = parsedInfo.room;
    	}
    	else {
	        console.log('received: %s', message);
	        parsedInfo['room'] = this.room;
	        wss.broadcast(JSON.stringify(parsedInfo));
	    }
    });
});
