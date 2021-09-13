<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://post.lt
 * @since      1.0.0
 *
 * @package    Woo_Lithuaniapost
 * @subpackage Woo_Lithuaniapost/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Woo_Lithuaniapost
 * @subpackage Woo_Lithuaniapost/includes
 * @author     AB "Lietuvos Paštas" <info@post.lt>
 */
class Woo_Lithuaniapost {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Woo_Lithuaniapost_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct ()
    {
		if ( defined ( 'WOO_LITHUANIAPOST_VERSION' ) ) {
			$this->version = WOO_LITHUANIAPOST_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'woo-lithuaniapost';

		$this->define_tables ();
		$this->load_dependencies ();
		$this->set_locale ();
		$this->define_admin_hooks ();
		$this->define_public_hooks ();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Woo_Lithuaniapost_Loader. Orchestrates the hooks of the plugin.
	 * - Woo_Lithuaniapost_i18n. Defines internationalization functionality.
	 * - Woo_Lithuaniapost_Admin. Defines all hooks for the admin area.
     * - Woo_Lithuaniapost_Admin_Settings. Defines all hooks for the settings.
	 * - Woo_Lithuaniapost_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies ()
    {
        /**
         * Plugin functions
         */
        require_once plugin_dir_path( dirname ( __FILE__ ) ) . 'includes/woo-lithuaniapost-functions.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname ( __FILE__ ) ) . 'includes/class-woo-lithuaniapost-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname ( __FILE__ ) ) . 'includes/class-woo-lithuaniapost-i18n.php';

        /**
         * The class responsible for shipping template
         * of the plugin.
         */
        require_once plugin_dir_path( dirname ( __FILE__ ) ) . 'admin/class-woo-lithuaniapost-admin-shipping-template.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname ( __FILE__ ) ) . 'admin/class-woo-lithuaniapost-admin.php';

        /**
         * The class responsible for defining all actions that occur in the admin area settings.
         */
        require_once plugin_dir_path( dirname ( __FILE__ ) ) . 'admin/class-woo-lithuaniapost-admin-order-actions.php';

        /**
         * The class responsible for defining all bulk actions that occur in the admin area settings.
         */
        require_once plugin_dir_path( dirname ( __FILE__ ) ) . 'admin/class-woo-lithuaniapost-admin-order-bulk-actions.php';

        /**
         * The class responsible for defining all actions that occur in the admin area settings.
         */
        require_once plugin_dir_path( dirname ( __FILE__ ) ) . 'admin/class-woo-lithuaniapost-admin-settings.php';

        /**
         * The class responsible for shipment tracking info
         */
        require_once plugin_dir_path( dirname ( __FILE__ ) ) . 'admin/class-woo-lithuaniapost-admin-order-tracking.php';

        /**
         * The class responsible for defining all actions that occur in the admin area settings.
         */
        require_once plugin_dir_path( dirname ( __FILE__ ) ) . 'admin/class-woo-lithuaniapost-admin-order-meta-box.php';

        /**
         * The class responsible for defining all api actions.
         */
        require_once plugin_dir_path( dirname ( __FILE__ ) ) . 'admin/class-woo-lithuaniapost-admin-api-hooks.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path ( dirname ( __FILE__ ) ) . 'public/class-woo-lithuaniapost-public.php';

		$this->loader = new Woo_Lithuaniapost_Loader ();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Woo_Lithuaniapost_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale ()
    {
		$plugin_i18n = new Woo_Lithuaniapost_i18n ();
		$this->loader->add_action ( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

    /**
     * Load shipping classes
     *
     * @since    1.0.0
     * @access   public
     */
	public function shipping_init ()
    {
        // Class responsible for table rates
        if ( ! class_exists ( 'Woo_Lithuaniapost_Table_Rates' ) ) {
            include_once plugin_dir_path ( dirname ( __FILE__ ) ) .
                'includes/shipping/rates/class-woo-lithuaniapost-tablerates.php';
        }

        // Class responsible for country rates
        if ( ! class_exists ( 'Woo_Lithuaniapost_Country_Rates' ) ) {
            include_once plugin_dir_path ( dirname ( __FILE__ ) ) .
                'includes/shipping/rates/class-woo-lithuaniapost-country-rates.php';
        }

        // Lithuania Post - Post Office shipping method
        if ( ! class_exists ( 'Woo_Lithuaniapost_Shipping_Lp_Postoffice' ) ) {
            include_once plugin_dir_path ( dirname ( __FILE__ ) ) .
                'includes/shipping/lp-postoffice/class-woo-lithuaniapost-shipping-lp-postoffice.php';
        }

        // Lithuania Post - Overseas shipping method
        if ( ! class_exists ( 'Woo_Lithuaniapost_Shipping_Lp_Overseas' ) ) {
            include_once plugin_dir_path ( dirname ( __FILE__ ) ) .
                'includes/shipping/lp-overseas/class-woo-lithuaniapost-shipping-lp-overseas.php';
        }

        // LP EXPRESS - Courier shipping method
        if ( ! class_exists ( 'Woo_Lithuaniapost_Shipping_Lpexpress_Courier' ) ) {
            include_once plugin_dir_path ( dirname ( __FILE__ ) ) .
                'includes/shipping/lpexpress-courier/class-woo-lithuaniapost-shipping-lpexpress-courier.php';
        }

        // LP EXPRESS - Terminal shipping method
        if ( ! class_exists ( 'Woo_Lithuaniapost_Shipping_Lpexpress_Terminal' ) ) {
            include_once plugin_dir_path ( dirname ( __FILE__ ) ) .
                'includes/shipping/lpexpress-terminal/class-woo-lithuaniapost-shipping-lpexpress-terminal.php';
        }

        // LP EXPRESS - Post office shipping method
        if ( ! class_exists ( 'Woo_Lithuaniapost_Shipping_Lpexpress_Postoffice' ) ) {
            include_once plugin_dir_path ( dirname ( __FILE__ ) ) .
                'includes/shipping/lpexpress-postoffice/class-woo-lithuaniapost-shipping-lpexpress-postoffice.php';
        }

        // Lithuania Post - Overseas shipping method
        if ( ! class_exists ( 'Woo_Lithuaniapost_Shipping_Lpexpress_Overseas' ) ) {
            include_once plugin_dir_path ( dirname ( __FILE__ ) ) .
                'includes/shipping/lpexpress-overseas/class-woo-lithuaniapost-shipping-lpexpress-overseas.php';
        }
    }

    /**
     * Load shipping methods.
     * @param array $methods
     * @return array
     *
     * @since    1.0.0
     * @access   public
     */
    public function load_shipping_methods ( $methods )
    {
        // Register main classes
        $methods [ 'woo_lithuaniapost_lp_postoffice' ]          = 'Woo_Lithuaniapost_Shipping_Lp_Postoffice';
        $methods [ 'woo_lithuaniapost_lp_overseas' ]            = 'Woo_Lithuaniapost_Shipping_Lp_Overseas';
        $methods [ 'woo_lithuaniapost_lpexpress_courier' ]      = 'Woo_Lithuaniapost_Shipping_Lpexpress_Courier';
        $methods [ 'woo_lithuaniapost_lpexpress_terminal' ]     = 'Woo_Lithuaniapost_Shipping_Lpexpress_Terminal';
        $methods [ 'woo_lithuaniapost_lpexpress_postoffice' ]   = 'Woo_Lithuaniapost_Shipping_Lpexpress_Postoffice';
        $methods [ 'woo_lithuaniapost_lpexpress_overseas' ]     = 'Woo_Lithuaniapost_Shipping_Lpexpress_Overseas';

        return $methods;
    }

    /**
     * Register custom tables within $wpdb object.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_tables ()
    {
        global $wpdb;

        // List of tables without prefixes.
        $tables = [
            'woo_lithuaniapost_api_token'           => 'woo_lithuaniapost_api_token',
            'woo_lithuaniapost_lpexpress_terminals' => 'woo_lithuaniapost_lpexpress_terminals',
            'woo_lithuaniapost_country_list'        => 'woo_lithuaniapost_country_list',
            'woo_lithuaniapost_shipping_templates'  => 'woo_lithuaniapost_shipping_templates',
            'woo_lithuaniapost_table_rates'         => 'woo_lithuaniapost_table_rates',
            'woo_lithuaniapost_country_rates'       => 'woo_lithuaniapost_country_rates',
            'woo_lithuaniapost_tracking_events'     => 'woo_lithuaniapost_tracking_events'
        ];

        foreach ( $tables as $name => $table ) {
            $wpdb->$name    = $wpdb->prefix . $table;
            $wpdb->tables[] = $table;
        }
    }

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks ()
    {
        $plugin_shipping_template   = new Woo_Lithuaniapost_Admin_Shipping_Template ( $this->get_plugin_name (), $this->get_version () );
        $plugin_admin               = new Woo_Lithuaniapost_Admin ( $this->get_plugin_name (), $this->get_version () );
        $plugin_admin_settings      = new Woo_Lithuaniapost_Admin_Settings ( $this->get_plugin_name (), $this->get_version () );
        $plugin_admin_actions       = new Woo_Lithuaniapost_Admin_Order_Actions ( $this->get_plugin_name (), $this->get_version () );
        $plugin_admin_bulk_actions  = new Woo_Lithuaniapost_Admin_Order_Bulk_Actions ( $this->get_plugin_name (), $this->get_version () );
        $plugin_api_hooks           = new Woo_Lithuaniapost_Admin_Api_Hooks ( $this->get_plugin_name (), $this->get_version () );
        $plugin_order_tracking      = new Woo_Lithuaniapost_Admin_Order_Tracking ( $this->get_plugin_name (), $this->get_version () );
        $plugin_admin_meta_box      = new Woo_Lithuaniapost_Admin_Order_Meta_Box ( $this->get_plugin_name (), $this->get_version () );

        /**
         * Shipping template hooks
         */
        $this->loader->add_filter ('woo_lithuaniapost_shipping_template', $plugin_shipping_template, 'get_shipping_template', 10, 2 );
        $this->loader->add_filter ('woo_lithuaniapost_shipping_template_additional_services', $plugin_shipping_template, 'get_additional_services', 10, 2 );
        $this->loader->add_filter ('woo_lithuaniapost_shipping_template_get_sdr_value', $plugin_shipping_template, 'get_sdr_value', 10, 1 );
        $this->loader->add_filter ('woo_lithuaniapost_shipping_template_get_cn_data_defaults', $plugin_shipping_template, 'get_cn_data_defaults', 10, 2 );
        $this->loader->add_filter ('woo_lithuaniapost_shipping_template_is_cn22', $plugin_shipping_template, 'is_cn22', 10, 2 );
        $this->loader->add_filter ('woo_lithuaniapost_shipping_template_is_cn23', $plugin_shipping_template, 'is_cn23', 10, 2 );
        $this->loader->add_filter ('woo_lithuaniapost_shipping_template_get_cn_data', $plugin_shipping_template, 'get_cn_data', 10, 2 );
        $this->loader->add_filter ('woo_lithuaniapost_is_cod_available', $plugin_shipping_template, 'is_cod_available', 10, 1 );


		/**
         * Admin hooks
         */
		$this->loader->add_action ('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action ('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action ('init', $plugin_admin, 'register_order_statuses', 20 );
		$this->loader->add_action ('wc_order_statuses', $plugin_admin, 'order_statuses' );
        $this->loader->add_action ('admin_post_woo-ltpost-export-table-rates', $plugin_admin, 'export_table_rates' );
        $this->loader->add_action ('admin_post_woo-ltpost-export-country-rates', $plugin_admin, 'export_country_rates' );
        $this->loader->add_action ('woocommerce_admin_order_items_after_shipping', $plugin_admin, 'display_terminal_order', 10, 1);
        $this->loader->add_action ('wp_ajax_woo_lithuaniapost_save_shipment', $plugin_admin, 'save_shipping_data' );
        $this->loader->add_action ('woo_lithuaniapost_admin_remove_notice', $plugin_admin, 'remove_notice', 10, 1 );


        /**
         * Create settings section
         */
		$this->loader->add_action ('woocommerce_get_sections_shipping', $plugin_admin_settings, 'create_settings_section' );

        /**
         * Add settings section
         */
        $this->loader->add_action ('woocommerce_get_settings_shipping', $plugin_admin_settings, 'add_settings_section', 10, 2);

        /**
         * Add custom actions to order actions dropdown
         */
        $this->loader->add_action ('woocommerce_order_actions', $plugin_admin_actions, 'add_order_actions' );

        /**
         * Process action
         */
        $this->loader->add_action ('woocommerce_order_action_woo_lp_create_label', $plugin_admin_actions, 'process_create_label' );
        $this->loader->add_action ('woocommerce_order_action_woo_lp_cancel_label', $plugin_admin_actions, 'process_cancel_label' );
        $this->loader->add_action ('woocommerce_order_action_woo_lp_print_label', $plugin_admin_actions, 'process_print_label' );
        $this->loader->add_action ('woocommerce_order_action_woo_lp_call_courier', $plugin_admin_actions, 'process_call_courier' );
        $this->loader->add_action ('woocommerce_order_action_woo_lp_print_manifest', $plugin_admin_actions, 'process_print_manifest' );
        $this->loader->add_action ('woocommerce_order_action_woo_lp_print_cn_form', $plugin_admin_actions, 'process_print_cn_form' );
        $this->loader->add_action ('woocommerce_order_action_woo_lp_print_all', $plugin_admin_actions, 'process_print_all' );
        $this->loader->add_filter ( 'woo_lithuaniapost_get_shipment_data', $plugin_admin_actions, 'get_shipment_data', 10, 1 );

        /**
         * Bulk actions
         */

        $this->loader->add_filter ( 'bulk_actions-edit-shop_order', $plugin_admin_bulk_actions, 'register_bulk_actions', 20, 1 );

        /**
         * Bulk action handlers
         */
        $this->loader->add_filter ( 'handle_bulk_actions-edit-shop_order', $plugin_admin_bulk_actions, 'handle_create_labels', 10, 3 );
        $this->loader->add_filter ( 'handle_bulk_actions-edit-shop_order', $plugin_admin_bulk_actions, 'handle_cancel_labels', 10, 3 );
        $this->loader->add_filter ( 'handle_bulk_actions-edit-shop_order', $plugin_admin_bulk_actions, 'handle_print_labels', 10, 3 );
        $this->loader->add_filter ( 'handle_bulk_actions-edit-shop_order', $plugin_admin_bulk_actions, 'handle_call_courier', 10, 3 );
        $this->loader->add_filter ( 'handle_bulk_actions-edit-shop_order', $plugin_admin_bulk_actions, 'handle_print_manifests', 10, 3 );
        $this->loader->add_filter ( 'handle_bulk_actions-edit-shop_order', $plugin_admin_bulk_actions, 'handle_print_cn_forms', 10, 3 );
        $this->loader->add_filter ( 'handle_bulk_actions-edit-shop_order', $plugin_admin_bulk_actions, 'handle_print_all_documents', 10, 3 );
        $this->loader->add_action ( 'admin_notices', $plugin_admin_bulk_actions, 'bulk_action_admin_notice' );

        /**
         * API Hooks
         */
		$this->loader->add_filter ('woo_lithuaniapost_api_create_shipping_item', $plugin_api_hooks, 'create_shipping_item', 10, 1 );
		$this->loader->add_filter ('woo_lithuaniapost_api_initiate_shipping', $plugin_api_hooks, 'initiate_shipping', 10, 1 );
		$this->loader->add_filter ('woo_lithuaniapost_api_create_sticker', $plugin_api_hooks, 'create_sticker', 10, 1 );
		$this->loader->add_filter ('woo_lithuaniapost_api_get_barcode', $plugin_api_hooks, 'get_barcode', 10, 1 );
		$this->loader->add_filter ('woo_lithuaniapost_api_call_courier', $plugin_api_hooks, 'call_courier', 10, 1 );
		$this->loader->add_filter ('woo_lithuaniapost_api_get_manifest', $plugin_api_hooks, 'get_manifest', 10, 1 );
		$this->loader->add_filter ('woo_lithuaniapost_api_cancel_label', $plugin_api_hooks, 'cancel_label', 10, 1 );
		$this->loader->add_filter ('woo_lithuaniapost_api_refresh_token', $plugin_api_hooks, 'refresh_token', 10 );
		$this->loader->add_filter ('woo_lithuaniapost_api_update_terminals', $plugin_api_hooks, 'save_terminal_list', 10 );
		$this->loader->add_filter ('woo_lithuaniapost_api_get_cn23_form', $plugin_api_hooks, 'get_cn23_form', 10, 1 );
		$this->loader->add_filter ('woo_lithuaniapost_api_get_tracking', $plugin_api_hooks, 'get_tracking', 10, 1 );
		$this->loader->add_action ( 'init', $plugin_api_hooks, 'schedule_refresh_token' );
		$this->loader->add_action ( 'init', $plugin_api_hooks, 'schedule_update_terminal_list' );

        /**
         * On options update run save sequence
         */
        $this->loader->add_action ('woocommerce_update_options_shipping', $plugin_api_hooks, 'run_sequence' );

        /**
         * Add order meta box
         */
        $this->loader->add_action ('add_meta_boxes', $plugin_admin_meta_box, 'add_order_meta_box' );

        /**
         * Add tracking hooks
         */
        $this->loader->add_filter ( 'woo_lithuaniapost_save_tracking_events', $plugin_order_tracking, 'save_tracking_events' );
        $this->loader->add_filter ( 'woo_lithuaniapost_get_tracking_events', $plugin_order_tracking, 'get_tracking_events' );
        $this->loader->add_filter ( 'woo_lithuaniapost_get_state_by_code', $plugin_order_tracking, 'get_state_by_code' );
        $this->loader->add_action ( 'init', $plugin_order_tracking, 'schedule_tracking_info' );

        /**
         * Shipping methods initiate and load
         */
        $this->loader->add_action ('woocommerce_shipping_init', $this, 'shipping_init' );
        $this->loader->add_action ('woocommerce_shipping_methods', $this, 'load_shipping_methods' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks ()
    {
		$plugin_public = new Woo_Lithuaniapost_Public ( $this->get_plugin_name (), $this->get_version () );

		$this->loader->add_action ( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action ( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

        $this->loader->add_filter ( 'woocommerce_after_shipping_rate', $plugin_public, 'render_terminal_field', 20, 2 );
        $this->loader->add_filter ( 'woocommerce_after_shipping_rate', $plugin_public, 'render_delivery_time', 20, 2 );
        $this->loader->add_action ( 'woocommerce_checkout_process', $plugin_public, 'validate_selected_terminal' );
        $this->loader->add_action ( 'woocommerce_checkout_update_order_meta', $plugin_public, 'save_order_meta_info', 30, 1 );
        $this->loader->add_action ( 'woocommerce_available_payment_gateways', $plugin_public, 'disable_cod_payment', 10, 1 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run ()
    {
		$this->loader->run ();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name ()
    {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Woo_Lithuaniapost_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader ()
    {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version ()
    {
		return $this->version;
	}

}
