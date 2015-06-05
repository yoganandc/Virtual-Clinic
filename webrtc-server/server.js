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

    	//Message FORCERELOAD is received
        if(typeof parsedInfo.forceReload !== 'undefined') {
            console.log(message);
            for(var i in clients) {
                if(typeof clients[i].caseId !== 'undefined') {
                    if(clients[i].caseId == parsedInfo.forceReload)
                        clients[i].send(JSON.stringify({"reload": true}));
                }
            }
        }
        //Client sends USER message
        else if(typeof parsedInfo.user !== 'undefined') {
    		console.log(message);
            this.user = parsedInfo.user;
            this.assigned = parsedInfo.assigned;
            clients.push(ws);
            for(var i in clients) {
                if(clients[i].assigned == this.user) {
                    this.partner = clients[i];
                    clients[i].partner = this;
                    this.send(JSON.stringify({'online': true}));
                    this.partner.send(JSON.stringify({'online': true}));
                }
            }
    	}
        //Client sends READY message
        else if(typeof parsedInfo.ready !== 'undefined') {
            this.ready = parsedInfo.ready;
            if((typeof this.partner.ready !== 'undefined') && (this.partner.ready == true)) {
                console.log('ready');
                this.partner.send(JSON.stringify({'initiate': true}));
            }    
        }
        //Client sends CHAT message
        else if(typeof parsedInfo.chat !== 'undefined') {
            var dbc = mysql.createConnection({host: DB_HOST, user: DB_USER, password: DB_PASSWORD, database: DB_NAME});
            var escapedChat = dbc.escape(parsedInfo.chat);
            var query = "INSERT INTO vc_messages (assigneduser_id, user_id, message) VALUES ("+this.assigned+", "+this.user+", "+escapedChat+")";
            dbc.query(query, function(error, results, fields) { if(error) console.log('query chat: '+error); });
            dbc.end(function(err) { console.log('chat: '+err); });
            if(this.partner)
                this.partner.send(message);
        }
        //Client sends LOCKCASEID message
        else if(typeof parsedInfo.lockCaseId !== 'undefined') {
            console.log(message);
            this.lockCaseId = parsedInfo.lockCaseId;
            var dbc = mysql.createConnection({host: DB_HOST, user: DB_USER, password: DB_PASSWORD, database: DB_NAME});
            var query = "UPDATE vc_case SET edit_lock=1 WHERE case_id="+this.lockCaseId;
            dbc.query(query, function(error, results, fields) { if(error) console.log('editcase query error: '+error); });
            dbc.end(function(err) { console.log('editcase: '+err); });
        }
        //Client sends UNLOCKCASEID message
        else if(typeof parsedInfo.unlockCaseId !== 'undefined') {
            console.log(message);
            var dbc = mysql.createConnection({host: DB_HOST, user: DB_USER, password: DB_PASSWORD, database: DB_NAME});
            var query = "UPDATE vc_case SET edit_lock=0 WHERE case_id="+this.lockCaseId;
            dbc.query(query, function(error, results, fields) { if(error) console.log('editcase query error: '+error); });
            dbc.end(function(err) { console.log('editcase: '+err); });
            delete this.lockCaseId;
        }
        //Client sends REGISTERCASEID message
        else if(typeof parsedInfo.registerCaseId !== 'undefined') {
            console.log(message);
            console.log('registered user'+this.user);
            this.caseId = parsedInfo.registerCaseId;
        }
        //Client sends UNGREGISTERCASEID message
        else if(typeof parsedInfo.unregisterCaseId !== 'undefined') {
            console.log(message);
            console.log('unregistered user'+this.user);
            delete this.caseId;
        }
        //Client sends SDP/ICE message
    	else {
	        console.log('received sdp/ice message from user '+this.user);
            this.send(message);
    	    if(this.partner) {
                this.partner.send(message);
            }
	    }
    });
    ws.on('close', function() {
        var query = "UPDATE vc_user_status SET status=0 WHERE status_id="+this.user;
        var dbc = mysql.createConnection({host: DB_HOST, user: DB_USER, password: DB_PASSWORD, database: DB_NAME});
        dbc.query(query, function(error, results, fields) { if(error) console.log('query close: '+error); });
        dbc.end(function(err) { console.log('close: '+err); });
        clients.splice(clients.indexOf(this), 1);
        if(this.partner) {
            this.partner.ready = false;
            this.partner.send(JSON.stringify({'hangup': true}));
            this.partner.partner = null;
        }
        if((typeof this.lockCaseId !== 'undefined')) {
            var dbc = mysql.createConnection({host: DB_HOST, user: DB_USER, password: DB_PASSWORD, database: DB_NAME});
            var query = "UPDATE vc_case SET edit_lock=0 WHERE case_id="+this.lockCaseId;
            dbc.query(query, function(error, results, fields) { if(error) console.log('editcase query error: '+error); });
            dbc.end(function(err) { console.log('editcase: '+err); });
            clients.splice(clients.indexOf(this), 1);
        }
    });
});
