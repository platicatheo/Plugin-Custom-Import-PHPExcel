<?php
/*
Plugin Name: Custom Import Theo
Plugin URI:  https://webloungedesign.com
Description: This is a custom import Plugin
Version:     1.0
Author:      Theo Platica
Author URI:  https://www.upwork.com/freelancers/~01828aa73189bec8e6
Text Domain: custom_import_theo
Domain Path: /languages
License:     GPL2
 
Custom_Import_Theo is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Custom_Import_Theo is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
*/


// error_reporting(-1);
// ini_set('display_errors', 'On');



//This line blocks direct access to the this file
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


if ( ! class_exists( 'Custom_Import_Theo' ) ) {

	/**
	 * Class for Custom_Import_Theo plugin
	 */
	class Custom_Import_Theo {


		function __construct() {
			// Load plugin text-domain
			add_action( 'init', array($this, 'myplugin_load_textdomain') );

			// Create tables when activate
			// register_activation_hook( __FILE__, array($this,'my_plugin_create_table') );

			/**
			 * To delete the tables, a file "uninstall.php" was created in the root folder of the plugin
			 * The hook below deletes the tables on plugin deactivation
			*/
			// register_deactivation_hook( __FILE__, array($this,'my_plugin_remove_database') );

			// Create Admin Menu page
			add_action('admin_menu', array($this, 'my_plugin_menu') );

			// Inclde css and js files for Front End
			add_action( 'wp_enqueue_scripts', array($this, 'load_front_end_scripts_and_styles') );

			// Include css and js files for Admin Side
			add_action( 'admin_enqueue_scripts', array($this, 'franchises_enqueue_styles_and_scripts') );

			//Get form
			// add_action( 'wpv_after_top_header', array($this, 'do_fe_form') );

			//Get Modal
			// add_action( 'wp_footer', array($this, 'do_fe_modal') );
		}




		/**
		 * Load plugin text-domain function
		*/
		function myplugin_load_textdomain() {
		  load_plugin_textdomain( 'custom_import_theo' ); 
		}



		/**
		 * Inclde css and js files for Front End
		*/
		function load_front_end_scripts_and_styles()
		{
			wp_enqueue_script('jquery_for_fe', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "")."://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js");
			wp_enqueue_script( 'franchises_res_script', plugins_url('/js/show_franchises.js', __FILE__));
			// Include default css file
			wp_enqueue_style( 'custom_import_theo', plugins_url('/css/fe.css', __FILE__ ) );
			wp_enqueue_style( 'fe_bootstr_mod', "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" );
			wp_enqueue_script('fe_bootstr_mod', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js');
		}



		/**
		 * Inclde css and js files for Back End
		*/
		function franchises_enqueue_styles_and_scripts() {
			wp_enqueue_style('css-for-franchise-b', plugins_url('/css/be.css?ver=5.0.0', __FILE__), '', time() );
		}



		/**
		 * Create Admin Menu page
		*/
		function my_plugin_menu() {
			add_menu_page('Custom Import Theo', 'Custom Import Theo', 'administrator', 'custom-import-theo-settings', array($this, 'do_menu_page'));
		}




		/**
		 * This function returns the HTML for the Admin Page
		 * and updates the DB with the data from the CSV files
		 *@return Plugin html form
		*/
		function do_menu_page()
		{

			global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();

			require_once "vendor/autoload.php";
			require_once "filters/phpexcel_filters.php";


			if(isset($_POST['submit_custom_upl'])) {

				$meta_key = 'categories_mapping';
				$taxonomy = $_REQUEST['import_selection'];

				$query_results = [];

				// echo '<pre>'; print_r($_FILES); echo '</pre>';

				if( $_FILES['upload_excel']['size'] && $_FILES['upload_excel']['name'] && $_FILES['upload_excel']['tmp_name'] && $_FILES['upload_excel']['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ) {


					$fileName = $_FILES['upload_excel']['tmp_name'];					 

					$sampleFilter = new SampleReadFilter();

					/** automatically detect the correct reader to load for this file type */
					$excelReader = PHPExcel_IOFactory::createReaderForFile($fileName);

					$excelReader->setReadFilter($sampleFilter);

					//if we dont need any formatting on the data
					$excelReader->setReadDataOnly();
					 
					//load only certain sheets from the file
					// $loadSheets = array('Product Listing');
					$worksheetList = $excelReader->listWorksheetNames($fileName);
					$sheetname = $worksheetList[0]; 
					$excelReader->setLoadSheetsOnly($sheetname);
					 

					$excelObj = $excelReader->load($fileName);

					$arrays_of_vals = $excelObj->getActiveSheet()->toArray();
					//show the final array
					// var_dump($return);
					// echo '<pre>'; print_r($arrays_of_vals); echo '</pre>';

					$final_array = array();

					foreach( $arrays_of_vals as $array_of_vals ) {
						$main_category = $array_of_vals[0];
						$subcateg = $array_of_vals[1];
						$manufacturer = $array_of_vals[2];

						if( $manufacturer ) {
							$final_array[$manufacturer][$main_category][] = $subcateg;
						}

					}


					foreach( $final_array as $manufacturer_name=>$manufacturer_cats ) {
						$term = get_term_by( 'name', $manufacturer_name, $taxonomy);
						$term_id = $term->term_id;

						$i = 0;
						$final_string = '';

						$total_cats_subcats = count($manufacturer_cats);
						foreach( $manufacturer_cats as $cat_name=>$subcats ) {
							$cat = get_term_by( 'name', $cat_name, 'product_cat' );
							$cat_id = $cat->term_id;

							$subcats_no = count($subcats);
							$total_cats_subcats += $subcats_no;

							$final_string .= 'i:'.$i.';s:4:"'.$cat_id.'";';
							$i++;

							foreach( $subcats as $subcat_name ) {
								$args = array(
													'name'=>trim($subcat_name),
													'parent'=>$cat_id,
													'hide_empty'=>FALSE
												);
								$subcat = get_terms( 'product_cat', $args );
								$subcat_id = $subcat[0]->term_id;

								// echo '<pre>'; print_r($subcat); echo '</pre>';

								$final_string .= 'i:'.$i.';s:5:"'.$subcat_id.'";';
								$i++;
							}
						}

						$final_string = 'a:'.$total_cats_subcats.':{'.$final_string.'}';

						update_term_meta($term_id, $meta_key, $final_string);

						// echo '<pre>'; print_r($manufacturer_name); echo '</pre>';
						// echo '<pre>'; print_r($term_id); echo '</pre>';
						// echo '<pre>'; print_r($final_string); echo '</pre>';
					}

					// echo '<pre>'; print_r($final_array); echo '</pre>';

				}

				// $query_results[] = $wpdb->query();

				// echo '<pre>'; var_dump($query_results); echo '</pre>';


				// if( count($query_results) ) {
				// 	$flag = true;
				// 	foreach($query_results as $query_result) {
				// 		if( $query_result === false ) {
				// 			$flag = false;
				// 		}
				// 	}		
				// }

			}



			ob_start();
			$html = '';
			?>


			<h1 class="plugin_title_th"><?= __('Custom Import Theo Plugin', 'custom_import_theo'); ?></h1><br>

			<?php
			if(isset($_POST['submit_custom_upl'])) {
					echo '<p class="success_custom_upload">'.__('The import has been completed.', 'custom_import_theo').'</p>';
			}
			?>


			<form class="custom_import_theo all_fields_form" method="post" action="" enctype="multipart/form-data">

				<p class="title_p"><?= __('Select Type of import', 'custom_import_theo'); ?></p>

	      <div><label><input type="radio" name="import_selection" value="pa_category-type-en" required /> Category Types</label></div>
	      <div><label><input type="radio" name="import_selection" value="pa_manufacturer-en" /> Manufacturers</label></div>

				<p class="title_p"><?= __('Upload the Excel file', 'custom_import_theo'); ?></p>
				<input type="file" accept=".xlsx" name="upload_excel" required><br><br><br>

				<input type="submit" class="submit btn-default" name="submit_custom_upl" value="<?= __('Import Excel', 'custom_import_theo'); ?>"/>

			</form>

			<?php

			// if(isset($flag)) {
			// 	if($flag) {
			// 		echo '<p class="success">'.__('The data was successfully inserted in the database.', 'custom_import_theo').'</p>';
			// 	}
			// 	else {
			// 		echo '<p class="error">'.__('Something went wrong.', 'custom_import_theo').'</p>';				
			// 	}
			// }

			?>

			<?php
			$html .= ob_get_contents();

			ob_end_clean();

			echo $html;

		}


	}

}

$custom_import_theo = new Custom_Import_Theo();