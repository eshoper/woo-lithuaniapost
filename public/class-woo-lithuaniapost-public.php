<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://post.lt
 * @since      1.0.0
 *
 * @package    Woo_Lithuaniapost
 * @subpackage Woo_Lithuaniapost/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woo_Lithuaniapost
 * @subpackage Woo_Lithuaniapost/public
 * @author     AB "Lietuvos Paštas" <info@post.lt>
 */
class Woo_Lithuaniapost_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct ( $plugin_name, $version )
    {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles ()
    {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Lithuaniapost_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Lithuaniapost_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style ( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-lithuaniapost-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
    {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Lithuaniapost_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Lithuaniapost_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script ( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-lithuaniapost-public.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Get formatted LPEXPRESS terminal list
	 *
	 * @return array
	 * @since 1.0.0
	 */
	protected function get_terminal_list ()
    {
        global $wpdb;

        $formatted_list = [];

        // Get terminals from DB
        $terminals = $wpdb->get_results ( sprintf ( 'SELECT * FROM %s',
			$wpdb->woo_lithuaniapost_lpexpress_terminals )
		) ?? [];

		foreach ( $terminals as $terminal ) {
			// Add city groups
			if ( ! array_key_exists ( $terminal->city, $formatted_list ) ) {
				$formatted_list [ $terminal->city ] = [];
			}

			// Formatted grouped list by city
			$formatted_list [ $terminal->city ][ $terminal->terminal_id ]
				= sprintf ( '%s - %s', $terminal->name, $terminal->address );
		}

		return $formatted_list;
    }

    /**
     * Render field for LP EXPRESS terminal
     *
     * @param $method
     * @param int $index
     * @since 1.0.0
     */
    public function render_terminal_field ( $method, $index )
    {
    	// Get selected method
        $selected_method = WC ()->session->get ( 'chosen_shipping_methods' ) [ $index ];

        // Run only if terminal method and only in checkout
		if ( $method->get_id () != 'woo_lithuaniapost_lpexpress_terminal' || !is_checkout () ) return;

        // Check if selected method is LP EXPRESS terminal
        if ( $selected_method == 'woo_lithuaniapost_lpexpress_terminal' ) {
        	include __DIR__ . '/partials/html-lpexpress-terminal.php';
		}
    }

    /**
     * Render delivery time
     *
     * @param $method
     * @param $index
     * @since 1.0.0
     */
    public function render_delivery_time ( $method, $index )
    {
        // Get selected method
        $selected_method = WC ()->session->get ( 'chosen_shipping_methods' ) [ $index ];

        // Run only if lp method and only in checkout
        if ( strpos ( $method->get_method_id (), 'woo_lithuaniapost' ) === false || !is_checkout () ) return;

        // Get all your existing shipping zones IDS
        $zone_ids = array_keys( array('') + WC_Shipping_Zones::get_zones() );

        // Loop through shipping Zones IDs
        foreach ( $zone_ids as $zone_id ) {
            // Get the shipping Zone object
            $shipping_zone = new WC_Shipping_Zone ( $zone_id );

            // Get all shipping method values for the shipping zone
            $shipping_methods = $shipping_zone->get_shipping_methods( true, 'values' );

            // The dump of protected data from the current shipping method
            $method_object = $shipping_methods [ $method->get_instance_id () ];

            if ( $method_object ) {
                if ( property_exists ( $method_object, 'delivery_time' ) && $method->get_method_id () == $selected_method ) {
                    $delivery_time = $method_object->delivery_time;
                    if ( $delivery_time ) {
                        include __DIR__ . '/partials/html-delivery-time.php';
                    }
                }
            }
        }
    }

	/**
	 * Custom terminal field validation
	 *
	 * @since 1.0.0
	 */
    public function validate_selected_terminal ()
	{
        // Get selected method
        $selected_method = WC ()->session->get ( 'chosen_shipping_methods' ) [ 0 ];

        // Run only if terminal method and only in checkout
        if ( $selected_method != 'woo_lithuaniapost_lpexpress_terminal' || !is_checkout () ) return;

		if ( isset ( $_POST [ 'woo_lithuaniapost_lpexpress_terminal_id' ] )
				&& empty ( $_POST [ 'woo_lithuaniapost_lpexpress_terminal_id' ] ) ) {
			wc_add_notice ( __( 'Please select LP EXPRESS terminal', 'woo-lithuaniapost' ), 'error' );
		} else {
			// Validate against random values
			global $wpdb;

			// Check if selected terminal exists in DB
			$terminal = $wpdb->get_results ( sprintf ('SELECT * FROM %s WHERE terminal_id = %s',
				$wpdb->woo_lithuaniapost_lpexpress_terminals, $_POST [ 'woo_lithuaniapost_lpexpress_terminal_id' ] )
			);

			if ( !$terminal ) {
				wc_add_notice ( __( 'Please select LP EXPRESS terminal', 'woo-lithuaniapost' ), 'error' );
			}
		}
	}

	/**
	 * Save shipping method settings and/or selected terminal
	 *
	 * @param $order_id
	 * @since 1.0.0
	 */
	public function save_order_meta_info ( $order_id )
	{
        $order = wc_get_order ( $order_id );

        // Get all your existing shipping zones IDS
        $zone_ids = array_keys( array('') + WC_Shipping_Zones::get_zones() );

        // Loop through shipping Zones IDs
        foreach ( $zone_ids as $zone_id )
        {
            // Get the shipping Zone object
            $shipping_zone = new WC_Shipping_Zone ( $zone_id );

            // Get all shipping method values for the shipping zone
            $shipping_methods = $shipping_zone->get_shipping_methods( true, 'values' );
            // The dump of protected data from the current shipping method

            foreach ( $order->get_shipping_methods () as $method ) {
                $method_object = $shipping_methods [ $method->get_instance_id () ];

                if ( property_exists ( $method_object, 'delivery_method' ) ) {
                    $order->update_meta_data (
                        '_woo_lithuaniapost_delivery_method',
                        $method_object->delivery_method
                    );
                }

                if ( property_exists ( $method_object, 'size' ) ) {
                    $order->update_meta_data (
                        '_woo_lithuaniapost_delivery_size',
                        $method_object->size
                    );
                }
            }
        }

        // Save terminal id
		if ( isset ( $_POST [ 'woo_lithuaniapost_lpexpress_terminal_id' ] ) ) {
			$order->update_meta_data (
				sprintf ( '_%s_id', 'woo_lithuaniapost_lpexpress_terminal' ),
				sanitize_text_field ( $_POST [ 'woo_lithuaniapost_lpexpress_terminal_id' ] )
			);
		}

        // Save default sender info json
        $order->update_meta_data (
            '_woo_lithuaniapost_sender_info',
            json_encode ( [
                'companyName'   => Woo_Lithuaniapost_Admin_Settings::get_option ( 'sender_name' ),
                'name'          => " ",
                'phone'         => Woo_Lithuaniapost_Admin_Settings::get_option ( 'sender_phone' ),
                'email'         => Woo_Lithuaniapost_Admin_Settings::get_option ( 'sender_email' ),
                'address' => [
                    'locality'   => Woo_Lithuaniapost_Admin_Settings::get_option ( 'sender_city' ),
                    'street'     => Woo_Lithuaniapost_Admin_Settings::get_option ( 'sender_street' ),
                    'building'   => Woo_Lithuaniapost_Admin_Settings::get_option ( 'sender_building' ),
                    'apartment'  => Woo_Lithuaniapost_Admin_Settings::get_option ( 'sender_apartment' ),
                    'postalCode' => Woo_Lithuaniapost_Admin_Settings::get_option ( 'sender_postcode' ),
                    'country'    => "LT"
                ]
            ] )
        );

        /**
         * Get shipping template from order
         */
        $template = apply_filters (
            'woo_lithuaniapost_shipping_template',
            $order->get_meta ( '_woo_lithuaniapost_delivery_method' ),
            $order->get_meta ( '_woo_lithuaniapost_delivery_size' )
        );

        if ( $cnData = apply_filters ( 'woo_lithuaniapost_shipping_template_get_cn_data',
            $order, $template [ 'id' ] ) ) {
            // Save default CN data
            $order->update_meta_data (
                '_woo_lithuaniapost_cn_data',
                json_encode ( $cnData )
            );
        }

        if ( $additional = apply_filters ( 'woo_lithuaniapost_shipping_template_additional_services',
            $order, $template [ 'id' ] ) ) {
            // Save default additional services JSON
            $order->update_meta_data (
                '_woo_lithuaniapost_additional',
                json_encode ( $additional )
            );
        }

        // Save parts
        $order->update_meta_data(
            '_woo_lithuaniapost_parts',
            1 // Default value
        );

        $order->save ();
	}

    /**
     * Disable COD payment if not available
     *
     * @param array $available_gateways
     * @return array
     * @since 1.0.0
     */
	public function disable_cod_payment ( $available_gateways )
    {
        $shipping_template = [ 'id' => -1 ];

        // Get all your existing shipping zones IDS
        $zone_ids = array_keys( array('') + WC_Shipping_Zones::get_zones () );

        if ( is_checkout () ) {
            // Get chosen shipping method
            $chosen_methods = WC ()->session->get( 'chosen_shipping_methods' );

            // Loop through shipping Zones IDs
            foreach ( $zone_ids as $zone_id )
            {
                // Get the shipping Zone object
                $shipping_zone = new WC_Shipping_Zone ( $zone_id );

                // Get all shipping method values for the shipping zone
                $shipping_methods = $shipping_zone->get_shipping_methods ( true, 'values' );

                foreach ( $shipping_methods as $method ) {
                    if ( $method->id == $chosen_methods [ 0 ] ) {
                        $shipping_template = apply_filters (
                            'woo_lithuaniapost_shipping_template',
                            $method->delivery_method,
                            $size = $method->size
                        );
                    }
                }
            }

            if ( strpos ( $chosen_methods [ 0 ], 'woo_lithuaniapost' ) != -1 ) {
                if ( WC()->customer->get_shipping_country() != 'LT' || !apply_filters ( 'woo_lithuaniapost_is_cod_available', $shipping_template [ 'id' ] ) )
                    unset ( $available_gateways [ 'cod' ] );
            }
        }

        return $available_gateways;
    }
}
