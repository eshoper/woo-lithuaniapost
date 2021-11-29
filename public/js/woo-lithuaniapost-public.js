(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$ ( document ).ready ( function () {
		$ ( '#woo_lithuaniapost_lpexpress_terminal_id' ).on ( 'change', function () {
			$.ajax({
				type : "POST",
				dataType : "json",
				url : "/wp-admin/admin-ajax.php",
				data : {
					action: "save_selected_lpexpress_terminal",
					city: $ ( '#woo_lithuaniapost_lpexpress_terminal_city' ).val (),
					terminal: $ ( '#woo_lithuaniapost_lpexpress_terminal_id' ).val ()
				}
			});
		});
	});
})( jQuery );
