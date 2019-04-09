// jQuery(document).ready(function(){
// 	var $Container = jQuery('#give-license-activator-wrap'),
// 	    $form = jQuery('form', $Container),
// 		$errorContainer = jQuery( '.give-errors', $Container ),
// 		apiURL = 'http://givewp.test/chechout',
// 		data = {
// 			edd_action: 'check_license',
// 			license : '',
// 			url: window.location.origin
// 		};
//
// 	$form.on( 'submit', function(){
// 		data.license = jQuery( 'input[name="give_license_key"]', jQuery(this) ).val().trim();
//
// 		// Remove all errors.
// 		$errorContainer.empty();
//
// 		if( ! data.license ) {
// 			$errorContainer.html( '<div class="give-notice notice error"><p>License is empty</p></div>' );
// 			return false;
// 		}
//
// 		fetch( apiURL + '?' + encodeQueryData(data) )
// 			.then(function(response){ return JSON.parse( response )})
// 			.then(function ( response ) {
// 				console.log( response );
// 			});
//
// 		return false;
// 	});
//
// 	function encodeQueryData(data) {
// 		const ret = [];
// 		for (let d in data)
// 			ret.push(encodeURIComponent(d) + '=' + encodeURIComponent(data[d]));
// 		return ret.join('&');
// 	}
// });

jQuery(document).ready(function(){
	var $container = jQuery('#give-addon-uploader-wrap'),
		$form = jQuery('form', $container),
		$file = jQuery( 'input[type="file"]', $form );

	// Stop page redirects when drop zip file.
	jQuery('html').on('drop', function(e) { e.preventDefault(); e.stopPropagation(); });

	// Drop
	// @todo: add validation to upload only zip files
	$container.on('drop', function (e) {
		e.stopPropagation();
		e.preventDefault();

		jQuery(this).removeClass('thick-border');

		var file = e.originalEvent.dataTransfer.files,
			fd = new FormData();

		fd.append( 'file', file[0] );

		giveUploadData(fd);
	});

	// Drag over
	$container.on('dragover', function (e) {
		jQuery(this).addClass('thick-border');
	}).on('dragleave', function (e) {
		jQuery(this).removeClass('thick-border');
	});


	// Prevent click event loop.
	$file.on( 'click', function(e){e.stopPropagation();});

	$container.on( 'click', function(e){
		e.stopPropagation();
		e.preventDefault();

		$file.click();
	});

	$file.on( 'change', function(e){
		e.stopPropagation();
		e.preventDefault();

		var fd = new FormData(),
			files = $file[0].files[0] ;


		if( ! files ){
			return false;
		}

		fd.append('file',files);
		giveUploadData(fd);
	} );

	/**
	 * Sending AJAX request and upload file
	 *
	 * @since 2.5.0
	 * @param formdata
	 */
	function giveUploadData(formdata){
		jQuery.ajax({
			url: `${ajaxurl}?action=give_upload_addon&_wpnonce=${jQuery( 'input[name="_give_upload_addon"]', $form ).val().trim()}`,
			method: 'POST',
			data: formdata,
			contentType: false,
			processData: false,
			dataType: 'json',
			beforeSend: function(){
				$container.html('Uploading File...');
			},
			success: function(response){
				if( true === response.success ) {
					$container.html('Uploaded ! ');

					return;
				}

				$container.html('Error: check console for more information.');
				console.log( response );
			}
		});
	}
});
