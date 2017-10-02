<?php

	/// MOODLE ADMINISTRATION SETUP STEPS
	// 1- Install the plugin
	// 2- Enable web service advance feature (Admin > Advanced features)
	// 3- Enable XMLRPC protocol (Admin > Plugins > Web services > Manage protocols)
	// 4- Create a token for a specific user and for the service 'Booking API' (Admin > Plugins > Web services > Manage tokens)
	// 5- Run this script directly from your browser: you should see 'Hello, FIRSTNAME'

	// Lokalni
	$token = 'f88756214144ce382a81acc139878edf'; // Token - from moodle - user must have rights
	$domainname = 'http://localhost'; // Moodle URL
	$courseroomid = '1'; // Course ID

	// Don't touch ...
	$functionname = 'local_bookingapi_bookings';

	header('Content-Type: text/plain');
	$serverurl = $domainname . '/webservice/xmlrpc/server.php'. '?wstoken=' . $token;
	require_once('./curl.php');
	$curl = new curl;
	$post = xmlrpc_encode_request($functionname, array($courseroomid));
	$resp = xmlrpc_decode($curl->post($serverurl, $post));
	var_dump($resp);
?>
