<?php


class Woo_Lithuaniapost_Admin_Order_Meta_Box
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
     * Get order
     *
     * @return bool|WC_Order|WC_Order_Refund
     * @since 1.0.0
     */
    public function get_order ()
    {
        return wc_get_order ( get_the_ID () );
    }

    /**
     * Check if shipping method belongs to LP
     *
     * @return bool
     * @since 1.0.o
     */
    protected function is_shipping_method ()
    {
        return strpos ( $this->get_shipping_method (), 'woo_lithuaniapost' ) !== false;
    }

    /**
     * Get shipping method
     *
     * @return string
     * @since 1.0.0
     */
    public function get_shipping_method ()
    {
        $order = $this->get_order ();

        if ( $order ) {
            foreach ( $order->get_shipping_methods () as $method ) {
                return $method->get_method_id ();
            }
        }

        return null;
    }

    /**
     * Add meta box for shipment controls
     *
     * @since 1.0.0
     */
    public function add_order_meta_box ()
    {
        // Add metabox only if LP method is selected
        if ( $this->is_shipping_method () ) {
            add_meta_box (
                'woo-lithuaniapost-order-meta-box',
                __( 'Lithuania Post', 'woo-lithuaniapost' ),
                [ $this, 'order_meta_box_content' ],
                'shop_order',
                'advanced',
                'high'
            );
        }
    }

    /**
     * Get tracking code
     *
     * @return string
     */
    public function get_tracking_code ()
    {
        return $this->get_order ()->get_meta ( '_woo_lithuaniapost_barcode' );
    }

    /**
     * Get formatted LP EXPRESS terminal list
     *
     * @return array
     * @since 1.0.0
     */
    public function get_terminal_list ()
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
     * Meta box content
     *
     * @since 1.0.0
     */
    public function order_meta_box_content ()
    {
        require plugin_dir_path ( dirname ( __FILE__ ) ) .
            'admin/partials/woo-lithuaniapost-admin-display.php';
    }
}
