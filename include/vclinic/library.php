<?php
    define('VC_PATTERN_EMAIL', '/^[a-zA-Z0-9][a-zA-Z0-9\._\-&!?=#]*@/');
    define('VC_EMAIL_LENGTH', '40');
    define('VC_ROOM_PHRASELENGTH', '40');
    define('VC_NUM_ALLOWEDCHARS', '36');

    function win_checkdnsrr($domain, $rectype='') {
    	if(!empty($domain)) {
    		if($rectype=='')
    			$rectype = 'MX';
    		exec("nslookup -type=$rectype $domain", $output);
    		foreach($output as $line) {
    			if(preg_match("/^$domain/", $line))
    				return true;
    		}
    		return false;
    	}
    	return false;
    }

    function check_email($email) {
    	if(empty($email))
    		return true;
    	if(!preg_match(VC_PATTERN_EMAIL, $email))
    		return false;
    	$domain = preg_replace(VC_PATTERN_EMAIL, '', $email);
    	if(!win_checkdnsrr($domain))
    		return false;
    	if(!(strlen($email) <= VC_EMAIL_LENGTH))
    		return false;
    	return true;
    }

    function remove_file($upload_type, $file_location) {
        if($upload_type == VC_UPLOAD_FILE && !empty($file_location))
            unlink($file_location);
    }
?>
