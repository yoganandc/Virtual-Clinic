<?php
	define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

    if(!IS_AJAX) {
        die('Restricted access');
    }

    require_once('usersession.php');
?>
