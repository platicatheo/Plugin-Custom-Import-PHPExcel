//Replace jQuery with $
(function($) {


	$(window).on('load', function() {


		// AJAX for Francises search form below header
		$('[name="submit_zip_code"]').on('click', function() {

			var data = {};

			data['zip_code'] = $('[name="zip_code"]').val();

			$.post( document.location.origin + '/ownsites/premium/wp-content/plugins/franchise-map/ajax/franchises_pulling_ajax.php' , data, function(response) {

				var response = JSON.parse(response);
				// console.log( response );

				var $table = '<table class="table table-bordered table-hover">';
				$table += '<tr><th>Franchise Name</th><th>Phone</th><th>Website</th><th>Email</td><th>County Codes</th></tr>';

				$.each(response, function(key, value) {

					$table += '<tr><td>'+value.franchise_name+'</td><td><a href="tel:'+value.franchise_phone+'">'+value.franchise_phone+'</a></td><td><a href="'+value.franchise_website+'" target="_blank">'+value.franchise_website+'</a></td><td><a href="mailto:'+value.franchise_email+'">'+value.franchise_email+'</a></td><td>'+value.franchise_county_codes+'</td>';
				
				});

				$table += '</table>';

				$('#franchises_modal .modal-body').html('');
				$('#franchises_modal .modal-body').append($table);

				$('#franchises_modal').modal('show');

			});

		});

	});



})(jQuery);