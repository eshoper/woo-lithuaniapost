<?php
/**
 * The admin-specific api hooks functionality of the plugin.
 *
 * @package    Woo_Lithuaniapost
 * @subpackage Woo_Lithuaniapost/admin
 * @author     AB "Lietuvos Paštas" <info@post.lt>
 */
class Woo_Lithuaniapost_Admin_Api_Hooks
{
    /**
     * Authentication gateways
     */
    const AUTH_TEST_GATEWAY     = 'https://api-manosiuntostst.post.lt/oauth/token';
    const AUTH_DEFAULT_GATEWAY  = 'https://api-manosiuntos.post.lt/oauth/token';

    /**
     * API gateways
     */
    const TEST_GATEWAY      = 'https://api-manosiuntostst.post.lt/api/v1';
    const DEFAULT_GATEWAY   = 'https://api-manosiuntos.post.lt/api/v1';

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
     * Get AUTH gateway depending on test mode
     *
     * @return string
     * @since 1.0.0
     */
    protected function get_auth_gateway ()
    {
        return Woo_Lithuaniapost_Admin_Settings::get_option ( 'test_mode' ) == 'yes' ?
            self::AUTH_TEST_GATEWAY : self::AUTH_DEFAULT_GATEWAY;
    }

    /**
     * Get API gateway depending on test mode
     *
     * @return string
     * @since 1.0.0
     */
    protected function get_api_gateway ()
    {
        return Woo_Lithuaniapost_Admin_Settings::get_option ( 'test_mode' ) == 'yes' ?
            self::TEST_GATEWAY : self::DEFAULT_GATEWAY;
    }

    /**
     * Get access token
     *
     * @return string|null
     * @since 1.0.0
     */
    protected function get_access_token ()
    {
        global $wpdb;

        return $wpdb->get_var ( "SELECT access_token FROM {$wpdb->woo_lithuaniapost_api_token} WHERE id = 1" );
    }

    /**
     * Do request to api gateway
     *
     * @param string $endpoint
     * @param array $params
     * @param string $method
     * @param string $message
     * @return mixed
     * @since 1.0.0
     */
    protected function do_request ( $endpoint, $params = [], $method = 'GET', $message = 'Something wen\'t wrong please try again later.' )
    {
        // Authorization arguments
        $args = [
            'headers' => [
                'Content-type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => sprintf ( 'Bearer %s', $this->get_access_token () )
            ],
            'method' => $method,
            'body' => $method !== 'POST' && $method !== 'DELETE' ? $params :
                json_encode ( $params ),
            'timeout' => 15,
        ];

        // Send request to API
        $response = wp_remote_request ( sprintf ( '%s/%s', $this->get_api_gateway (), $endpoint ),
            $args );

        // Error Occurred
        if ( wp_remote_retrieve_response_code ( $response ) !== 200 ) {
            WC_Admin_Settings::add_error ( wp_remote_retrieve_body ( $response ) );


            if ( @$_GET [ 'page' ] != 'wc-settings'  && @$_GET [ 'post_type' ] != 'shop_order' ) {
                $error_name = "Lp_error_" . uniqid ();
                WC_Admin_Notices::add_custom_notice ( $error_name, wp_remote_retrieve_body ( $response ) );

                // Remove notice after execution
                wp_schedule_single_event ( time () + 1, 'woo_lithuaniapost_admin_remove_notice', [
                    'name' => $error_name
                ] );
            }

            return false;
        }

        return json_decode ( wp_remote_retrieve_body ( $response ) );
    }

    /**
     * Get API token
     *
     * @param array $params
     * @return string|bool
     * @since 1.0.0
     */
    protected function token_request ( $params )
    {
        // Build query for token request
        $args = [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'body' => http_build_query ( $params )
        ];

        $response = wp_remote_post ( $this->get_auth_gateway (), $args );

        // Access token granted
        if ( wp_remote_retrieve_response_code ( $response ) === 200 ) {
            return json_decode ( wp_remote_retrieve_body ( $response ) );
        }

        // Error occurred
        $error = json_decode ( wp_remote_retrieve_body ( $response ) );

        WC_Admin_Settings::add_error ( $error->error_description ??
            __( 'Something wen\'t wrong please try again later.', 'woo-lithuaniapost' ) );

        return false;
    }

    /**
     * Refresh token for cron
     *
     * @since 1.0.0
     */
    public function refresh_token ()
    {
        global $wpdb;

        $access_token = $wpdb->get_row ( "SELECT * FROM {$wpdb->woo_lithuaniapost_api_token} WHERE id = 1" );

        // Set timezone
        date_default_timezone_set ( 'Europe/Vilnius' );

        if ( $access_token && strtotime ( $access_token->expires ) - 1000 < strtotime ( date ( 'Y-m-d H:i:s' ) ) ) {
            // Send refresh token request
            $response = $this->token_request ( [
                'scope' => 'read+write',
                'grant_type' => 'refresh_token',
                'clientSystem' => 'PUBLIC',
                'refresh_token' => $access_token->refresh_token
            ] );

            /** @var \stdClass $response */
            if ( $response ) {
                // Truncate API token table
                $wpdb->query ( "TRUNCATE TABLE {$wpdb->woo_lithuaniapost_api_token}" );

                // Save API token
                $wpdb->insert ($wpdb->prefix . 'woo_lithuaniapost_api_token', [
                        'access_token' => $response->access_token,
                        'refresh_token' => $response->refresh_token,
                        'expires' => date ('Y-m-d H:i:s', time () + $response->expires_in )
                    ]
                );
            }
        }
    }

    /**
     * Schedule refresh token
     *
     * @since 1.0.0
     */
    public function schedule_refresh_token ()
    {
        if (! wp_next_scheduled ( 'woo_lithuaniapost_api_refresh_token' ) ) {
            wp_schedule_event ( time (), 'every_minute', 'woo_lithuaniapost_api_refresh_token' );
        }
    }

    /**
     * Call API and save API token
     *
     * @return bool
     * @since 1.0.0
     */
    public function save_access_token ()
    {
        global $wpdb;

        // Set timezone
        date_default_timezone_set ( 'Europe/Vilnius' );

        // Request for API token
        $response = $this->token_request ( [
            'grant_type' => 'password',
            'clientSystem' => 'public',
            'username' => Woo_Lithuaniapost_Admin_Settings::get_option ( 'api_username' ),
            'password' => Woo_Lithuaniapost_Admin_Settings::get_option ( 'api_password' ),
            'scope' => 'read+write'
        ] );

        /** @var \stdClass $response */
        if ( $response ) {
            // Truncate API token table
            $wpdb->query ( sprintf ( 'TRUNCATE TABLE %s',
                $wpdb->woo_lithuaniapost_api_token ) );

            // Save API token
            $wpdb->insert ( $wpdb->prefix . 'woo_lithuaniapost_api_token', [
                    'access_token' => $response->access_token,
                    'refresh_token' => $response->refresh_token,
                    'expires' => date ( 'Y-m-d H:i:s', time () + $response->expires_in )
                ]
            );

            return true;
        } else {
            // Truncate API token table credentials invalid
            $wpdb->query ( "TRUNCATE TABLE {$wpdb->woo_lithuaniapost_api_token}" );
            update_option ( 'woo_lithuaniapost_module_active', false );
        }

        return false;
    }

    /**
     * Save LPEXPRESS terminal list
     *
     * @since 1.0.0
     */
    public function save_terminal_list ()
    {
        global $wpdb;
        $terminals = $this->do_request ( 'address/terminals', [ 'size' => 999 ] );

        // Get LPEXPRESS terminal list
        if ( $terminals ) {
            // Truncate terminals table
            $wpdb->query ( "TRUNCATE TABLE {$wpdb->woo_lithuaniapost_lpexpress_terminals}" );
            /** @var \stdClass $terminal */
            foreach ( $terminals as $terminal ) {
                $wpdb->insert (
                    $wpdb->woo_lithuaniapost_lpexpress_terminals, [
                        'terminal_id' => $terminal->id,
                        'name' => $terminal->name,
                        'address' => $terminal->address,
                        'city' => $terminal->city
                    ]
                );
            }
        }
    }

    /**
     * Schedule update terminal list
     *
     * @since 1.0.0
     */
    public function schedule_update_terminal_list ()
    {
        if (! wp_next_scheduled ( 'woo_lithuaniapost_api_update_terminals' ) ) {
            wp_schedule_event ( time (), 'weekly', 'woo_lithuaniapost_api_update_terminals' );
        }
    }

    /**
     * Save available country list
     *
     * @since 1.0.0
     */
    public function save_available_country_list ()
    {
        global $wpdb;
        $countries = $this->do_request ( 'address/country', [ 'size' => 999 ] );

        if ( $countries ) {
            // Truncate country list table
            $wpdb->query ( "TRUNCATE TABLE {$wpdb->woo_lithuaniapost_country_list}");
            /** @var \stdClass $country */
            foreach ( $countries as $country ) {
                $wpdb->insert ( $wpdb->woo_lithuaniapost_country_list, [
                        'country_id'    => $country->id,
                        'country'       => $country->country,
                        'country_code'  => $country->code
                    ]
                );
            }
        }
    }

    /**
     * Save shipping templates
     *
     * @since 1.0.0
     */
    public function save_shipping_templates ()
    {
        global $wpdb;

        $templates = $this->do_request ( 'shipping/shippingItemTemplates', [],
            'OPTIONS' );

        if ( $templates ) {
            // Truncate shipping templates table
            $wpdb->query ( "TRUNCATE TABLE {$wpdb->woo_lithuaniapost_shipping_templates}" );
            $wpdb->insert ( $wpdb->woo_lithuaniapost_shipping_templates, [
                'shipping_templates' => json_encode ( $templates )
            ] );
        }
    }

    /**
     * Verify sender postcode
     *
     * @since 1.0.0
     */
    public function verify_sender_postcode ()
    {
        // Request countryId should be captured from API
        $this->do_request ( 'address/verification', [
            'countryId' => Woo_Lithuaniapost_Admin_Settings::get_option ( 'sender_country' ),
            'locality' => '-',
            'street' => '-',
            'building' => '-',
            'postalCode' => Woo_Lithuaniapost_Admin_Settings::get_option ( 'sender_postcode' )
        ], 'POST', 'Sender post code is invalid. Please enter a valid post code and try again.' );
    }

    /**
     * Verify sender city
     *
     * @since 1.0.0
     */
    public function verify_sender_city ()
    {
        $response = $this->do_request ( sprintf ( 'address/country/%s/locality',
            Woo_Lithuaniapost_Admin_Settings::get_option ( 'sender_country' )
        ), [
            'keyword' => Woo_Lithuaniapost_Admin_Settings::get_option ( 'sender_city' )
        ] );

        if ( empty ( $response ) ) {
            WC_Admin_Settings::add_error (
                __( 'Sender city is invalid. Please enter a valid city and try again.' , 'woo-lithuaniapost' )
            );
        }
    }

    /**
     * Run API sequence on save settings
     * - Save API token
     * - Save Terminal List
     * - Save Available Country List
     * - Save Shipping Templates
     * - Verify Sender Postcode
     * - Verify Sender City
     *
     * @since 1.0.0
     */
    public function run_sequence ()
    {
        // Run sequence only if in lpsettings section
        if ( $_GET [ 'section' ] == 'lpsettings' ) {
            // TODO: Run sequence if only credentials or test mode changed
            // Save access token
            if ( $this->save_access_token () ) {
                // Do the rest of the sequence
                if ( !get_option ( 'woo_lithuaniapost_module_active' ) ) {
                    $this->save_terminal_list ();
                    $this->save_available_country_list ();
                    $this->save_shipping_templates ();

                    update_option ( 'woo_lithuaniapost_module_active', true );
                } else {
                    // Verify after module is active
                    $this->verify_sender_postcode ();
                    $this->verify_sender_city ();
                }
            }
        }
    }

    /**
     * Create shipping item
     *
     * @param $request_data
     * @return int|null
     * @since 1.0.0
     */
    public function create_shipping_item ( $request_data )
    {
        $response = $this->do_request ( 'shipping', $request_data, 'POST' );

        if ( $response ) {
            if ( !$response || !property_exists ( $response, 'id' ) ) {
                return null;
            }

            return $response->id;
        }

        return null;
    }

    /**
     * Initiate shipping
     *
     * @param array $shipping_item_ids
     * @return int|null
     * @since 1.0.0
     */
    public function initiate_shipping ( $shipping_item_ids )
    {
        $response = $this->do_request ( 'shipping/initiate', $shipping_item_ids,
            'POST' );

        if ( $response ) {
            return $response [ 0 ];
        }

        return null;
    }

    /**
     * Create sticker
     *
     * @param $shipping_item_id
     * @return mixed
     * @since 1.0.0
     */
    public function create_sticker ( $shipping_item_id )
    {
        $response = $this->do_request ( 'documents/item/sticker', [
            'itemId' => $shipping_item_id,
            'layout' => Woo_Lithuaniapost_Admin_Settings::get_option ( 'other_label_format' )
        ] );

        if ( $response ) {
            return !empty ( $response ) ? $response [ 0 ]->label : null;
        }

        return null;
    }

    /**
     * Cancel shipping label
     *
     * @param $shipping_item_id
     * @return bool
     * @since 1.0.0
     */
    public function cancel_label ( $shipping_item_id )
    {
        $response = $this->do_request ( sprintf ( '%s/%s', 'shipping', $shipping_item_id ), null,
            'DELETE' );

        return $response;
    }

    /**
     * Call courier
     *
     * @param array $shipping_item_ids
     * @return bool
     * @since 1.0.0
     */
    public function call_courier ( $shipping_item_ids )
    {
        $response = $this->do_request( 'shipping/courier/call', $shipping_item_ids,
            'POST' );

        if ( $response ) {
            return true;
        }

        return false;
    }

    /**
     * Get manifest base64 encoded pdf
     *
     * @param $cart_id
     * @return mixed
     * @since 1.0.0
     */
    public function get_manifest ( $cart_id )
    {
        $response = $this->do_request( sprintf ( 'documents/cart/%s/manifest', $cart_id ),
            [] );

        if ( $response ) {
            return $response->document;
        }

        return null;
    }

    /**
     * Get barcode
     *
     * @param $shipping_item_id
     * @return string|null
     * @since 1.0.0
     */
    public function get_barcode ( $shipping_item_id )
    {
        $response = $this->do_request ( sprintf ( '%s/%s', 'shipping', $shipping_item_id ), [],
            'GET' );

        if ( $response ) {
            return $response->barcode;
        }

        return null;
    }

    /**
     * Get CN23 form base64 encoded pdf
     *
     * @param $cart_id
     * @throws \Exception
     * @return mixed
     */
    public function get_cn23_form ( $cart_id )
    {
        $response = $this->do_request ( sprintf ( 'documents/cart/%s/cn23', $cart_id ), [] );

        if ( $response ) {
            return $response != null && property_exists ( $response, 'document' ) ? $response->document : null;
        }

        return null;
    }

    /**
     * Used for tracking cronjob
     *
     * @param $barcode
     * @return mixed
     */
    public function get_tracking ( $barcode )
    {
        $response = $this->do_request ( sprintf ( '%s/%s', 'tracking/byBarcode', $barcode ),
            [] );

        if ( $response ) {
            return $response != null && property_exists ( $response, 'state' ) &&
                $response->state != 'STATE_NOT_FOUND' ? $response : null;
        }

        return null;
    }
}
