/**
 * JS file
 *
 * @package WpFaqSchema
 */

( function( $ ) {
	$( '.wp-faq-schema-items' ).on( 'click', '.wp-faq-schema-header', function( e ) {
		e.preventDefault();
		$( this ).parent().toggleClass( 'close' );
		$( this ).next().toggleClass( 'close' );
	} );

	$( '.wp-faq-schema-items' ).on( 'click', '.wp-faq-schema-delete', function( e ) {
		e.preventDefault();
		e.stopPropagation();
		if ( confirm( $( this ).data( 'confirm' ) ) ) {
			$( this ).parents( '.wp-faq-schema-item' ).remove();
		}
	} );

	$( '.wp-faq-schema-add' ).on( 'click', function() {
		var index = $( this ).data( 'index' ),
			title = $( this ).data( 'title' ),
			cont = $( this ).prev(),
			tmpl = $( '#tmpl-faq-schema-form' ).html();

			tmpl = tmpl.replace( '{TITLE}', title.replace( '%s', parseInt( index ) + 1 ) );
			tmpl = tmpl.replace( /\{INDEX\}/g, index );

			cont.append( tmpl );

			$( this ).data( 'index', parseInt( index ) + 1 );
	} );

	var timeOutTime = null;

	$( '#wp-faq-schema-export' ).on( 'click', function( e ) {
		e.preventDefault();

		$( '#wp-faq-schema-import' ).prop( 'disabled', true );
		$( '#wp-faq-schema-export' ).prop( 'disabled', true );
		$( '#wp-faq-schema-import-file' ).prop( 'disabled', true );
		clearTimeout( timeOutTime );
		$( '#export-message' ).html( '' );

		var data = {
			action: 'wfs_export',
			wfs_nonce: $( '#wfs-nonce' ).val(),
		};

		$.post( wfs_ajax, data, function( res ) {
			if ( typeof res.success !== 'undefined' && ! res.success ) {
				$( '#export-message' ).append( $( '<span class="error">' + res.data + '</span>' ) );
				timeOutTime = setTimeout( function() {
					$( '#export-message' ).html( '' );
				}, 5 * 1000 );
				return;
			}
			var byteNumbers = new Uint8Array( res.length );

			for (var i = 0; i < res.length; i++) {
				byteNumbers[i] = res.charCodeAt(i);
			}

			var blob = new Blob( [ byteNumbers ], { type: 'text/csv' } );

			var uri = URL.createObjectURL( blob );

			// Construct the <a> element
			var link = document.createElement( 'a' );
			link.download = 'wp-faq-schema_' + Date.now().toString(36) + '.csv';
			link.href = uri;

			document.body.appendChild( link );
			link.click();

			// Cleanup the DOM
			document.body.removeChild( link );
			delete link;
		} ).always( function() {
			$( '#wp-faq-schema-import' ).prop( 'disabled', false );
			$( '#wp-faq-schema-export' ).prop( 'disabled', false );
			$( '#wp-faq-schema-import-file' ).prop( 'disabled', false );
		} );
	} );

	$( '#wp-faq-schema-import' ).on( 'click', function( e ) {
		e.preventDefault();

		$( '#wp-faq-schema-import' ).prop( 'disabled', true );
		$( '#wp-faq-schema-export' ).prop( 'disabled', true );
		$( '#wp-faq-schema-import-file' ).prop( 'disabled', true );

		clearTimeout( timeOutTime );
		$( '#import-message' ).html( '' );
		$( '#export-message' ).html( '' );

		var file = document.getElementById("wp-faq-schema-import-file").files[0];

		if ( file ) {
			var reader = new FileReader();
			reader.readAsText(file, 'UTF-8');
			reader.onload = function (evt) {
				var data = Papa.parse( evt.target.result );
				var csvData = [];
				data.data.forEach( function( item, i ) {
					if ( item.length > 3 && i > 0 ) {
						csvData.push( item );
					}
				} );

				$.post( wfs_ajax, {
					action: 'wfs_import',
					items: csvData,
					wfs_nonce: $( '#wfs-nonce' ).val(),
				}, function( res ) {
					if ( res.success ) {
						$( '#import-message' ).append( $( '<span>' + res.data + '</span>' ) );
						timeOutTime = setTimeout( function() {
							$( '#import-message' ).html( '' );
						}, 5 * 1000 );
					} else {
						$( '#import-message' ).append( $( '<span class="error">' + res.data + '</span>' ) );
						timeOutTime = setTimeout( function() {
							$( '#import-message' ).html( '' );
						}, 5 * 1000 );
					}
				} ).always( function() {
					$( '#wp-faq-schema-import' ).prop( 'disabled', false );
					$( '#wp-faq-schema-export' ).prop( 'disabled', false );
					$( '#wp-faq-schema-import-file' ).prop( 'disabled', false );
				} );
			}
			reader.onerror = function (evt) {
				$( '#import-message' ).append( $( '<span class="error">There is no valid file.</span>' ) );
				timeOutTime = setTimeout( function() {
					$( '#import-message' ).html( '' );
				}, 5 * 1000 );

				$( '#wp-faq-schema-import' ).prop( 'disabled', false );
				$( '#wp-faq-schema-export' ).prop( 'disabled', false );
				$( '#wp-faq-schema-import-file' ).prop( 'disabled', false );
			}
		} else {
			$( '#import-message' ).append( $( '<span class="error">There is no valid file.</span>' ) );
			timeOutTime = setTimeout( function() {
				$( '#import-message' ).html( '' );
			}, 5 * 1000 );
			$( '#wp-faq-schema-import' ).prop( 'disabled', false );
			$( '#wp-faq-schema-export' ).prop( 'disabled', false );
			$( '#wp-faq-schema-import-file' ).prop( 'disabled', false );
		}
	} );


	$( '[data-slug="faq-schema-for-pages-and-posts"] .row-actions .deactivate > a' ).on( 'click', function( e ) {
		e.preventDefault();

		Swal.fire( {
			// title: 'Error!',
			html: 'Visit <a href="https://www.onlinemarketinggurus.com.au/faq-schema-plugin">https://www.onlinemarketinggurus.com.au/faq-schema-plugin</a> to let us know if the plugin doesn\'t contain functionality that you\'re after or if something wasn\'t working, we appreciate your support.',
			icon: 'info',
			confirmButtonText: 'Get support',
			footer: '<a href="'+$(this).attr('href')+'">Skip & Deactivate</a>'
		} ).then( function( result ) {
			if ( result.value ) {
				var link = $( '<a href="https://www.onlinemarketinggurus.com.au/faq-schema-plugin" target="_blank"/>' )
				$( 'body' ).append( link );
				link[0].click();
			}
		} );
	} );
} )( jQuery );
