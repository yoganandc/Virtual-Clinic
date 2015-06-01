var WebSocketServer = require('ws').Server;
var mysql = require('mysql');

var DB_HOST = 'www.virtualclinic.com';
var DB_USER = 'root';
var DB_PASSWORD = 'telemedicine';
var DB_NAME = 'virtualclinic';

var wss = new WebSocketServer({port: 3434});
var clients = [];

wss.on('connection', function(ws) {
    ws.on('message', function(message) {
    	var parsedInfo = JSON.parse(message);

    	if(parsedInfo.room) {
    		console.log(message);
    		this.room = parsedInfo.room;
            this.user = parsedInfo.user;
            this.assigned = parsedInfo.assigned;
            clients.push(ws);
            for(var i in clients) {
                if((clients[i].room == this.room) && (clients[i] != this)) {
                    this.initiator = true;
                    this.partner = clients[i];
                    clients[i].partner = this;
                    this.send(JSON.stringify({'online': true}));
                    this.partner.send(JSON.stringify({'online': true}));
                }
            }
    	}
        else if(parsedInfo.chat) {
            var dbc = mysql.createConnection({host: DB_HOST, user: DB_USER, password: DB_PASSWORD, database: DB_NAME});
            var escapedChat = dbc.escape(parsedInfo.chat);
            var query = "INSERT INTO vc_messages (assigneduser_id, user_id, message) VALUES ("+this.assigned+", "+this.user+", "+escapedChat+")";
            dbc.query(query, function(error, results, fields) { });
            dbc.end();
            if(this.partner)
                this.partner.send(message);
        }
        else if(parsedInfo.ready) {
            this.ready = parsedInfo.ready;
            if(this.partner.ready) {
                console.log('ready');
                if(this.initiator) 
                    this.send(JSON.stringify({'initiate': true}));
                else
                    this.partner.send(JSON.stringify({'initiate': true}));
            }    
        }
    	else {
	        console.log('received sdp/ice message: '+message);
            this.send(message);
    	    if(this.partner) {
                this.partner.send(message);
            }
	    }
    });
    ws.on('close', function() {
        var query = "UPDATE vc_user_status SET status=0, room=NULL WHERE status_id="+this.user;
        var dbc = mysql.createConnection({host: DB_HOST, user: DB_USER, password: DB_PASSWORD, database: DB_NAME});
        dbc.query(query, function(error, results, fields) { });
        dbc.end();
        clients.splice(clients.indexOf(this), 1);
        if(this.partner) {
            this.partner.send(JSON.stringify({'hangup': true}));
            this.partner.partner = null;
        }
    });
});
