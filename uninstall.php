<?php

global $wpdb;
$table_name1 = $wpdb->prefix . 'franchise_map_zip_county';
$table_name2 = $wpdb->prefix . 'franchise_map_locations';
$sql = "DROP TABLE IF EXISTS $table_name1;";
$wpdb->query($sql);

	$sql = "DROP TABLE IF EXISTS $table_name2;";
$wpdb->query($sql);
delete_option("franchise_map_plugin_version");