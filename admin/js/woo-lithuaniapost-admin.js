(function( $ ) {
	'use strict';

	/**
	 * Convert serialized array to JSON object
	 */
	$.fn.getForm2obj = function () {
		var _ = {};
		$.map(this.serializeArray(), function(n) {
			const keys = n.name.match(/[a-zA-Z0-9_]+|(?=\[\])/g);
			if (keys.length > 1) {
				let tmp = _;
				let j;
				let pop = keys.pop();
				for (let i = 0; i < keys.length, j = keys[i]; i++) {
					tmp[j] = (!tmp[j] ? (pop === '') ? [] : {} : tmp[j]), tmp = tmp[j];
				}
				if (pop === '') tmp = (!Array.isArray(tmp) ? [] : tmp), tmp.push(n.value);
				else tmp[pop] = n.value;
			} else _[keys.pop()] = n.value;
		});
		return _;
	};

	$ ( 'document' ).ready ( function () {
		$('#lpshipping-shipping-modal-footer button').on ( 'click' , function ( e ) {
			e.preventDefault ();
			if ( $ ( '#post' )[0].checkValidity () ) {
				var data = $.extend ( $ ( '#lpshipping-shipment-modal :input' ).getForm2obj (), {
					action     : 'woo_lithuaniapost_save_shipment',
					dataType   : 'json',
				} );

				$ ( '#lpshipping-shipment-modal' ).block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});

				$.ajax( {
					url:     woo_lithuaniapost_admin.ajax_url,
					data:    data,
					type:    'POST',
					success: function( response ) {
						$ ( '#lpshipping-shipment-modal' ).unblock ();
						location.reload ();
					}
				} );
			} else {
				$ ( '#post' )[0].reportValidity ()
			}
		});

		let dependencyFieldsPostOffice = {
			'S': [ 'SMALL_CORESPONDENCE' ],
			'M': [ 'BIG_CORESPONDENCE' ],
			'L': [ 'PACKAGE' ]
		};

		let dependencyFieldsOverseas = {
			'S': [ 'SMALL_CORESPONDENCE', 'SMALL_CORESPONDENCE_TRACKED' ],
			'M': [ 'BIG_CORESPONDENCE', 'MEDIUM_CORESPONDENCE_TRACKED' ],
			'L': [ 'PACKAGE' ]
		};

		let postOfficeSize = $ ( '#woocommerce_woo_lithuaniapost_lp_postoffice_fictional_size' );
		let postOfficeMethod = $ ( '#woocommerce_woo_lithuaniapost_lp_postoffice_delivery_method option' );

		postOfficeMethod.each ( function () {
			if ( $.inArray ( $ ( this ).val (), dependencyFieldsPostOffice [ postOfficeSize.val () ] ) === -1 ) {
				$ ( this ).css ( { 'display': 'none' } );
			} else {
				$ ( this ).css ( { 'display': 'block' } );
			}
		});

		postOfficeSize.on ( 'change', function () {
			postOfficeMethod.each ( function () {
				if ( $.inArray ( $ ( this ).val (), dependencyFieldsPostOffice [ postOfficeSize.val () ] ) === -1 ) {
					$ ( this ).css ( { 'display': 'none' } );
				} else {
					$ ( this ).css ( { 'display': 'block' } );
				}
			});

			postOfficeMethod.each ( function () {
				if ( $ ( this ).css( 'display' ) === 'block' ) {
					$ ( '#woocommerce_woo_lithuaniapost_lp_postoffice_delivery_method' )
						.val ( $ ( this ).val () );
					return false;
				}
			});
		});

		let postOfficeOverseasSize = $ ( '#woocommerce_woo_lithuaniapost_lp_overseas_fictional_size' );
		let postOfficeOverseasMethod = $ ( '#woocommerce_woo_lithuaniapost_lp_overseas_delivery_method option' );

		postOfficeOverseasMethod.each ( function () {
			if ( $.inArray ( $ ( this ).val (), dependencyFieldsOverseas [ postOfficeOverseasSize.val () ] ) === -1 ) {
				$ ( this ).css ( { 'display': 'none' } );
			} else {
				$ ( this ).css ( { 'display': 'block' } );
			}
		});

		postOfficeOverseasSize.on ( 'change', function () {
			postOfficeOverseasMethod.each ( function () {
				if ( $.inArray ( $ ( this ).val (), dependencyFieldsOverseas [ postOfficeOverseasSize.val () ] ) === -1 ) {
					$ ( this ).css ( { 'display': 'none' } );
				} else {
					$ ( this ).css ( { 'display': 'block' } );
				}
			});

			postOfficeOverseasMethod.each ( function () {
				if ( $ ( this ).css( 'display' ) === 'block' ) {
					$ ( '#woocommerce_woo_lithuaniapost_lp_overseas_delivery_method' )
						.val ( $ ( this ).val () );
					return false;
				}
			});
		});
	});

})( jQuery );
