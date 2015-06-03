<?php
	define('VC_LOCATION', 'http://'.$_SERVER['HTTP_HOST'].'/vclinic/');
	define('VC_INCLUDE', '../../include/vclinic/');

	define('VC_TECHNICIAN', 't');
	define('VC_DOCTOR', 'd');
	define('VC_ADMINISTRATOR', 'a');

	define('VC_UPLOADPATH', 'images/');
	define('VC_MAXFILESIZE', '2097152');
	define('VC_DPWIDTH', '225');
	define('VC_DPHEIGHT', '225');

	define('VC_PATTERN_NAME', '/^[a-zA-Z]{2,40}$/');
	define('VC_PATTERN_BDAY', '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/');
	define('VC_PATTERN_PHONE', '/^[0-9]{0,15}$/');
	define('VC_PATTERN_OCCUPATION', '/^.{0,40}$/');
	define('VC_PATTERN_CITY', '/^[a-zA-Z ]{0,40}$/');
	define('VC_PATTERN_ADDRESS', '/^.{0,80}$/');
	define('VC_PATTERN_PINCODE', '/^\d{6}$/');

	define('VC_COMPLAINT_UNLISTED', '8');
	define('VC_TEST_UNLISTED', '4');
	define('VC_TREATMENT_UNLISTED', '38');
	define('VC_CHRONIC', '1');
	define('VC_ACUTE', '0');

	define('VC_UPLOAD_NONE', '0');
	define('VC_UPLOAD_FILE', '1');
	define('VC_UPLOAD_PHOTO', '2');
?>
