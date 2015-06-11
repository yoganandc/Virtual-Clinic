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
        //Message LOGOUT is received
        if(typeof parsedInfo.logout !== 'undefined') {
            console.log(message);
            for(var i in clients) {
                if(typeof clients[i].user !== 'undefined') {
                    if((clients[i].user == parsedInfo.logout) || (clients[i].user == parsedInfo.logoutAssigned)) {
                        clients[i].send(JSON.stringify({'hangup': true}));
                    }
                }
            }
        }
        //Client sends USER message
        else if(typeof parsedInfo.user !== 'undefined') {
    		console.log(message);
            this.user = parsedInfo.user;
            this.assigned = parsedInfo.assigned;
            clients.push(ws);
            console.log(clients.length);
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
            if(typeof this.user === 'undefined') {
                clients.push(this);
                console.log(clients.length);
            }
            this.caseId = parsedInfo.registerCaseId;
            this.caseUser = parsedInfo.registerCaseUser;
        }
        //Client sends UNREGISTERCASEID message
        else if(typeof parsedInfo.unregisterCaseId !== 'undefined') {
            console.log(message);
            if(typeof this.user === 'undefined') {
                clients.splice(clients.indexOf(this), 1);
                console.log(clients.length);
            }
            delete this.caseId;
            delete this.caseUser;
        }
        //Client sends FORWARDCASE message
        else if(typeof parsedInfo.forwardCase !== 'undefined') {
            console.log(message);
            var dbc = mysql.createConnection({host: DB_HOST, user: DB_USER, password: DB_PASSWORD, database: DB_NAME});
            var query = "INSERT INTO vc_forward (user_id, case_id) VALUES ("+parsedInfo.forwardUser+", "+parsedInfo.forwardCase+")";
            dbc.query(query, function(error, results, fields) { if(error) console.log('forwardcase query error: '+error); });
            dbc.end(function(err) { 
                console.log('forwardcase: '+err);
                for(var i in clients) {
                    if(clients[i].registerIndex !== 'undefined') {
                        if((clients[i].registerIndex == parsedInfo.forwardUser) || (clients[i].registerIndex == parsedInfo.forwardAssigned))
                            clients[i].send(JSON.stringify({'reload': true}));
                    }
                    if(clients[i].caseId !== 'undefined') {
                        if((clients[i].caseId == parsedInfo.forwardCase) && ((clients[i].caseUser == parsedInfo.forwardUser) || (clients[i].caseUser == parsedInfo.forwardAssigned)))
                            clients[i].send(JSON.stringify({'reload': true}));
                    }
                }  
            });
            
        }
        //Client sends RETURNCASE message
        else if(typeof parsedInfo.returnCase !== 'undefined') {
            console.log(message);
            var dbc = mysql.createConnection({host: DB_HOST, user: DB_USER, password: DB_PASSWORD, database: DB_NAME});
            var query = "UPDATE vc_forward SET status=1 WHERE forward_id="+parsedInfo.returnCaseId;
            dbc.query(query, function(error, results, fields) { if(error) console.log('returncase query error: '+error); });
            dbc.end(function(err) { 
                console.log('returncase: '+err); 
                for(var i in clients) {
                    if(clients[i].registerIndex !== 'undefined') {
                        if((clients[i].registerIndex == parsedInfo.returnUser) || (clients[i].registerIndex == parsedInfo.returnAssigned))
                            clients[i].send(JSON.stringify({'reload': true}));
                    }
                    if(clients[i].caseId !== 'undefined') {
                        if((clients[i].caseId == parsedInfo.returnCase) && ((clients[i].caseUser == parsedInfo.returnUser) || (clients[i].caseUser == parsedInfo.returnAssigned)))
                            clients[i].send(JSON.stringify({'reload': true}));
                    }
                }
            });
        }
        //Client sends DISMISSCASE message
        else if(typeof parsedInfo.dismissCase !== 'undefined') {
            console.log(message);
            var dbc = mysql.createConnection({host: DB_HOST, user: DB_USER, password: DB_PASSWORD, database: DB_NAME});
            var query = "DELETE FROM vc_forward WHERE forward_id="+parsedInfo.dismissCaseId;
            dbc.query(query, function(error, results, fields) { if(error) console.log('dismisscase query error: '+error); });
            dbc.end(function(err) { 
                console.log('dismisscase: '+err); 
                for(var i in clients) {
                    if(clients[i].registerIndex !== 'undefined') {
                        if((clients[i].registerIndex == parsedInfo.dismissUser))
                            clients[i].send(JSON.stringify({'reload': true}));
                    }
                    if(clients[i].caseId !== 'undefined') {
                        if((clients[i].caseId == parsedInfo.dismissCase) && (clients[i].caseUser == parsedInfo.dismissUser))
                            clients[i].send(JSON.stringify({'reload': true}));
                    }
                }
            });
        }
        //Client sends REGISTERINDEX message
        else if(typeof parsedInfo.registerIndex !== 'undefined') {
            console.log(message);
            if(typeof this.user === 'undefined') {
                clients.push(this);
                console.log(clients.length);
            }
            this.registerIndex = parsedInfo.registerIndex;
        }
        //Client sends UNREGISTERINDEX message
        else if(typeof parsedInfo.unregisterIndex !== 'undefined') {
            console.log(message);
            if(typeof this.user === 'undefined') {
                clients.splice(clients.indexOf(this), 1);
                console.log(clients.length);
            }
            delete this.registerIndex;
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

        //If database query is required or client has been pushed into clients array, handle it here

        if((typeof this.lockCaseId !== 'undefined')) {
            var dbc = mysql.createConnection({host: DB_HOST, user: DB_USER, password: DB_PASSWORD, database: DB_NAME});
            var query = "UPDATE vc_case SET edit_lock=0 WHERE case_id="+this.lockCaseId;
            dbc.query(query, function(error, results, fields) { if(error) console.log('editcase query error: '+error); });
            dbc.end(function(err) { console.log('editcase: '+err); });
        }
        if((typeof this.caseId !== 'undefined')) {
            if((typeof this.user === 'undefined')) {
                clients.splice(clients.indexOf(this), 1);
                console.log(clients.length);
            }
        }
        if(typeof this.registerIndex !== 'undefined') {
            if(typeof this.user === 'undefined') {
                clients.splice(clients.indexOf(this), 1);
                console.log(clients.length);
            }
        }
        if((typeof this.user !== 'undefined')) {
            var query = "UPDATE vc_user_status SET status=0 WHERE status_id="+this.user;
            var dbc = mysql.createConnection({host: DB_HOST, user: DB_USER, password: DB_PASSWORD, database: DB_NAME});
            dbc.query(query, function(error, results, fields) { if(error) console.log('query close: '+error); });
            dbc.end(function(err) { console.log('close: '+err); });
            clients.splice(clients.indexOf(this), 1);
            console.log(clients.length);
            if(this.partner) {
                this.partner.ready = false;
                this.partner.send(JSON.stringify({'hangup': true}));
                this.partner.partner = null;
            }
        }
    });
});
