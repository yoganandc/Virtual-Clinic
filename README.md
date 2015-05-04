# Virtual-Clinic
This application aims to provide two main functions: 

1. Enable virtual interaction between a doctor and patient through video and text chat.
2. Hold records of all patients' case histories and corresponding treament prescribed.

### How to set it up:
1. set the variables in /include/vclinic/appvars.php to your MySQL database credentials.
2. execute virtualclinic.sql & states.sql on your MySQL database.
3. copy all the files to the *htdocs* folder of your Apache server. (document root is assumed to be */htdocs/www* here.)
4. start the nodejs *signalmaster* server.
5. direct your browser to your server location and log in as *admin* (password is the same).

###Other information:
* The video chat in this application is implemented using [SimpleWebRTC](https://github.com/henrikjoreteg/SimpleWebRTC).
* WebRTC applications require a signaling server. This is implemented using [signalmaster](https://github.com/andyet/signalmaster).