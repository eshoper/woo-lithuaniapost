<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://post.lt
 * @since      1.0.0
 *
 * @package    Woo_Lithuaniapost
 * @subpackage Woo_Lithuaniapost/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Lithuaniapost
 * @subpackage Woo_Lithuaniapost/admin
 * @author     AB "Lietuvos Paštas" <info@post.lt>
 */
class Woo_Lithuaniapost_Admin
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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct ( $plugin_name, $version )
    {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-lithuaniapost-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts ()
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
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-lithuaniapost-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'woo_lithuaniapost_admin', array( 'ajax_url' => admin_url ( 'admin-ajax.php' ) ) );
	}

    /**
     * Register custom order statuses
     *
     * @since 1.0.0
     */
	public function register_order_statuses ()
    {
        register_post_status ( 'wc-lp-label-created', [
            'label'                     => _x( 'Shipment Created', 'Order status', 'woocommerce' ),
            'public'                    => false,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            /* translators: %s: number of orders */
            'label_count'               => _n_noop ( 'Shipment Created <span class="count">(%s)</span>', 'Shipment Created <span class="count">(%s)</span>', 'woo-lithuaniapost' )
        ] );

        register_post_status ( 'wc-lp-courier-called', [
            'label'                     => _x( 'Courier Called', 'Order status', 'woocommerce' ),
            'public'                    => false,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            /* translators: %s: number of orders */
            'label_count'               => _n_noop ( 'Courier Called <span class="count">(%s)</span>', 'Courier Called <span class="count">(%s)</span>', 'woo-lithuaniapost' )
        ] );
    }

    /**
     * Add statuses to WooCommerce
     *
     * @param $order_statuses
     * @return array
     */
    public function order_statuses ( $order_statuses )
    {
        $order_statuses [ 'wc-lp-label-created' ] = _x( 'Shipment Created', 'Order status', 'woo-lithuaniapost' );
        $order_statuses [ 'wc-lp-courier-called' ]  = _x( 'Courier Called', 'Order status', 'woo-lithuaniapost' );

        return $order_statuses;
    }

    /**
     * Display terminal inside order view
     *
     * @param int $order_id
     * @since 1.0.0
     */
	public function display_terminal_order ( $order_id )
    {
       if ( $terminal_id = get_post_meta ( $order_id, sprintf ( '_%s_id', 'woo_lithuaniapost_lpexpress_terminal' ), true ) ):
            global $wpdb;

            $terminal = $wpdb->get_results ( sprintf ('SELECT * FROM %s WHERE terminal_id = %s',
                   $wpdb->woo_lithuaniapost_lpexpress_terminals, $terminal_id )
            );
       ?>
            <tr class="shipping">
                <td class="thumb"><div></div></td>
                <td colspan="5">
                    <?php echo sprintf ( '<b>%s</b>: %s - %s, %s',
                        __( 'Terminal', 'woo-lithuaniapost' ), $terminal [ 0 ]->name, $terminal [ 0 ]->address, $terminal [ 0 ]->city
                    ); ?>
                </td>
            </tr>
       <?php endif;
    }

    /**
     * Export table rates
     *
     * @return false|resource
     * @since 1.0.0
     */
    public function export_table_rates ()
    {
        global $wpdb;

        // Shipping method ID
        $method_id = $_GET [ 'method' ];

        // File name
        $filename = str_replace ( 'woo_lithuaniapost_', 'tablerates_', $method_id . '.csv' );

        woo_lithuaniapost_file_headers ( $filename, 'text/csv' );

        /**
         * Create a file pointer connected to the output stream
         */
        $output = fopen ( 'php://output', 'w' );

        /**
         * Output the column headings
         */
        fputcsv ( $output, [ __( 'Weight To', 'woo-lithuaniapost' ), __( 'Price', 'woo-lithuaniapost' ) ] );

        /**
         * Fill data
         */
        $results = $wpdb->get_results (
            $wpdb->prepare ( "SELECT weight_to, price FROM {$wpdb->woo_lithuaniapost_table_rates} 
                        WHERE method_id = %s", $method_id ), ARRAY_A
        );

        foreach ( $results as $key => $rate ) {
            $modified_values = [ $rate [ 'weight_to' ], $rate [ 'price' ] ];
            fputcsv ( $output, $modified_values );
        }

        return $output;
    }

    /**
     * Export country rates
     *
     * @return false|resource
     * @since 1.0.0
     */
    public function export_country_rates ()
    {
        global $wpdb;

        // Shipping method ID
        $method_id = $_GET [ 'method' ] . '.csv';

        // File name
        $filename = str_replace ( 'woo_lithuaniapost_', 'countryrates_', $method_id . '.csv' );

        woo_lithuaniapost_file_headers ( $filename, 'text/csv' );

        /**
         * Create a file pointer connected to the output stream
         */
        $output = fopen ( 'php://output', 'w' );

        /**
         * Output the column headings
         */
        fputcsv ( $output, [ __( 'Country', 'woo-lithuaniapost' ), __( 'Price', 'woo-lithuaniapost' ) ] );

        /**
         * Fill data
         */
        $results = $wpdb->get_results (
            $wpdb->prepare ( "SELECT country, price FROM {$wpdb->woo_lithuaniapost_country_rates} 
                        WHERE method_id = %s", $method_id ), ARRAY_A
        );

        foreach ( $results as $key => $rate ) {
            $modified_values = [ $rate [ 'country' ], $rate [ 'price' ] ];
            fputcsv ( $output, $modified_values );
        }

        return $output;
    }

    /**
     * Update order meta data
     *
     * @since 1.0.0
     */
    public function save_shipping_data ()
    {
        $order = wc_get_order ( $_POST [ 'order_id' ] );

        if ( $order ) {
            $shipping_type = $_POST [ 'shipping_type' ];
            $shipping_size = $_POST [ 'shipping_size' ];
            $terminal_id   = $_POST [ 'terminal_id' ];
            $cod           = $_POST [ 'cod' ];
            $parts         = $_POST [ 'parts' ];
            $sender        = $_POST [ 'sender' ];
            $cn23          = isset ( $_POST [ 'cn23Form' ] ) ? [ 'cn23Form' => array_filter ( $_POST [ 'cn23Form' ] ) ] : null;
            $cn22          = isset ( $_POST [ 'cn22Form' ] ) ? [ 'cn22Form' => array_filter ( $_POST [ 'cn22Form' ] ) ] : null;

            // Shipping method
            $order->update_meta_data ('_woo_lithuaniapost_delivery_method', $shipping_type );

            // Shiping size
            $order->update_meta_data('_woo_lithuaniapost_delivery_size', $shipping_size );

            // Terminal
            $order->update_meta_data ('_woo_lithuaniapost_lpexpress_terminal_id', $terminal_id );

            // Save sender info json
            $order->update_meta_data ('_woo_lithuaniapost_sender_info', json_encode ( $sender ) );

            $template = apply_filters (
                'woo_lithuaniapost_shipping_template',
                $order->get_meta ( '_woo_lithuaniapost_delivery_method' ),
                $order->get_meta ( '_woo_lithuaniapost_delivery_size' )
            );

            if ( $additional = apply_filters ( 'woo_lithuaniapost_shipping_template_additional_services',
                $order, $template [ 'id' ] ) ) {

                // Save default additional services JSON
                $order->update_meta_data (
                    '_woo_lithuaniapost_additional',
                    json_encode ( $additional )
                );
            }

            // Update additional services COD value
            if ( $additional_services = json_decode ( $order->get_meta  ('_woo_lithuaniapost_additional' ) ) ) {
                foreach ( $additional_services as $service ) {
                    // COD value
                    if ( $service->id == 8 ) {
                        $service->amount = $cod;
                    }
                }
            }

            $order->update_meta_data ('_woo_lithuaniapost_additional', json_encode ( $additional_services ) );

            $is_cn22 = apply_filters ( 'woo_lithuaniapost_shipping_template_is_cn22', $order, $template [ 'id' ] );
            $is_cn23 = apply_filters ( 'woo_lithuaniapost_shipping_template_is_cn23', $order, $template [ 'id' ] );

            if ( $is_cn22 && $cn22 == null ) {
                $cn22 = apply_filters ( 'woo_lithuaniapost_shipping_template_get_cn_data_defaults', $order, 'cn22Form' );
            }

            if ( $is_cn23 && $cn23 == null ) {
                $cn23 = apply_filters ( 'woo_lithuaniapost_shipping_template_get_cn_data_defaults', $order, 'cn23Form' );
            }

            $order->update_meta_data (
                '_woo_lithuaniapost_cn_data',
                $is_cn22 || $is_cn23 ? json_encode ( $cn22 ?? $cn23 ) : null
            );

            // Update parts
            $order->update_meta_data(
                '_woo_lithuaniapost_parts',
                !in_array ( $template [ 'id' ], [ 42, 43, 44 ] )
                && !in_array ( $template [ 'type' ], [ 'MEDIUM_CORESPONDENCE_TRACKED', 'SMALL_CORESPONDENCE_TRACKED' ] ) ? $parts ?? 1 : 1
            );

            $order->save ();
        }

        wp_die ();
    }

    /**
     * Remove notice
     *
     * @param $name
     * @since 1.0.0
     */
    public function remove_notice ( $name )
    {
        WC_Admin_Notices::remove_notice ( $name );
    }

    /**
     * Add allowed terminal checkbox to product admin
     *
     * @since 2.1.1
     */
    public function add_allowed_terminal_checkbox ()
    {
        global $post;

        $input_checkbox = get_post_meta ( $post->ID, 'woo_lithuaniapost_allowed_terminal', true );
        if ( empty ( $input_checkbox ) ) $input_checkbox = '';

        woocommerce_wp_checkbox ( [
            'id'            => 'woo_lithuaniapost_allowed_terminal',
            'label'         => __( 'Lithuania Post', 'woo-lithuaniapost' ),
            'description'   => __( 'Disable LP EXPRESS terminal shipping for this product', 'woo-lithuaniapost' ),
            'value'         => $input_checkbox,
        ] );
    }

    /**
     * Save allowed LP EXPRESS terminal checkbox
     *
     * @param int $post_id
     */
    public function save_allowed_terminal_checkbox ( $post_id )
    {
        $_terminal_option = isset ( $_POST [ 'woo_lithuaniapost_allowed_terminal' ] ) ? 'yes' : '';
        update_post_meta ( $post_id, 'woo_lithuaniapost_allowed_terminal', $_terminal_option );
    }
}
