<?php

// error_reporting(-1);
// ini_set('display_errors', 'On');

// Get acces to global variable $wpdb when using a standalone script like this
$path = $_SERVER['DOCUMENT_ROOT'].'/ownsites/premium';
require_once $path . '/wp-load.php';

global $wpdb;
$table_name1 = $wpdb->prefix . 'franchise_map_zip_county';
$table_name2 = $wpdb->prefix . 'franchise_map_locations';


$request = $_REQUEST;
$query_results = [];

$zip_code = $request['zip_code'];

// echo '<pre>'; print_r($request); echo '</pre>';


// Get the country code from the zip_county table
$county_code = $wpdb->get_results(
						"SELECT county_code FROM $table_name1 WHERE zip = '$zip_code'"
					);

$county_code = $county_code[0]->county_code;


// Get the franchises data from the francchises table
$franchises = $wpdb->get_results(
						"SELECT * FROM $table_name2 WHERE franchise_county_codes LIKE '%$county_code%' ORDER BY RAND()"
					);

$franchises_for_sending = array();

foreach($franchises as $franchise) {
	$franchises_for_sending[$franchise->franchise_name]['franchise_id'] = $franchise->franchise_id;
	$franchises_for_sending[$franchise->franchise_name]['franchise_name'] = $franchise->franchise_name;
	$franchises_for_sending[$franchise->franchise_name]['franchise_phone'] = $franchise->franchise_phone;
	$franchises_for_sending[$franchise->franchise_name]['franchise_website'] = $franchise->franchise_website;
	$franchises_for_sending[$franchise->franchise_name]['franchise_email'] = $franchise->franchise_email;
	$franchises_for_sending[$franchise->franchise_name]['franchise_county_codes'] = $franchise->franchise_county_codes;
}

// print_r($franchises_for_sending);	


die( json_encode($franchises_for_sending) );
