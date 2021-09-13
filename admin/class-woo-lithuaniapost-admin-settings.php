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
 * The admin-specific settings functionality of the plugin.
 *
 * Defines the WooCommerce settings
 *
 * @package    Woo_Lithuaniapost
 * @subpackage Woo_Lithuaniapost/admin
 * @author     AB "Lietuvos Paštas" <info@post.lt>
 */
class Woo_Lithuaniapost_Admin_Settings
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
     * Create WooCommerce settings section
     *
     * @param array $sections
     * @return array
     * @since 1.0.0
     */
    public function create_settings_section ( $sections )
    {
        $sections [ 'lpsettings' ] = __( 'Lithuania Post', 'woo-lithuaniapost' );
        return $sections;
    }

    /**
     * Add settings to section
     *
     * @param array $settings
     * @param string $current_section
     * @return array
     * @since 1.0.0
     */
    public function add_settings_section ( $settings, $current_section )
    {
        /**
         * Check if current section is lpsettings
         */
        if ( $current_section == 'lpsettings' ) {
            $lpsettings = [
                [
                    'name' => __( 'Lithuania Post Shipping Settings', 'woo-lithuaniapost' ),
                    'type' => 'title',
                    'desc' => __( 'The following options are used to configure Lithuania Post shipping', 'woo-lithuaniapost' ),
                    'id' => 'lpsettings_authorization'
                ],
                /**
                 * Authorization settings
                 */
                // Title
                [
                    'name' => __( 'Authorization', 'woo-lithuaniapost' ),
                    'type' => 'title',
                    'id' => 'lpsettings_authorization'
                ],
                // Test Mode
                [
                    'name' => __( 'Test Mode', 'woo-lithuaniapost' ),
                    'type' => 'checkbox',
                    'id' => 'lpsettings_test_mode'
                ],
                // Username
                [
                    'name' => __( 'Username', 'woo-lithuaniapost' ),
                    'type' => 'text',
                    'id' => 'lpsettings_api_username',
                    'custom_attributes' => [ 'required' => 'required' ]
                ],
                // Password
                [
                    'name' => __( 'Password', 'woo-lithuaniapost' ),
                    'type' => 'password',
                    'id' => 'lpsettings_api_password',
                    'custom_attributes' => [ 'required' => 'required' ]
                ],
                // Section end
                [
                    'type' => 'sectionend',
                    'id' => 'lpsettings_authorization'
                ]
            ];

            // Reveal sender info when credentials created
            if ( get_option ( 'woo_lithuaniapost_module_active' ) ) {
                array_push ($lpsettings,
                    /**
                     * Sender inforamtion
                     */
                    // Title
                    [
                        'name' => __( 'Sender information', 'woo-lithuaniapost' ),
                        'type' => 'title',
                        'id' => 'lpsettings_sender'
                    ],
                    // Name
                    [
                        'name' => __( 'Name', 'woo-lithuaniapost' ),
                        'type' => 'text',
                        'desc_tip' => __( 'Maximum length: 100 characters', 'woo-lithuaniapost'  ),
                        'id' => 'lpsettings_sender_name',
                        'custom_attributes' => [ 'required' => 'required', 'maxlength' => 100 ]
                    ],
                    // Phone
                    [
                        'name' => __( 'Phone', 'woo-lithuaniapost' ),
                        'type' => 'text',
                        'desc_tip' => __( 'Format: 370XXXXXXXX', 'woo-lithuaniapost'  ),
                        'id' => 'lpsettings_sender_phone',
                        'custom_attributes' => [ 'required' => 'required' ]
                    ],
                    // Email
                    [
                        'name' => __( 'Email', 'woo-lithuaniapost' ),
                        'type' => 'email',
                        'desc_tip' => __( 'Maximum length: 128 characters', 'woo-lithuaniapost'  ),
                        'id' => 'lpsettings_sender_email',
                        'custom_attributes' => [ 'required' => 'required', 'maxlength' => 128 ]
                    ],
                    // Country
                    [
                        'name' => __( 'Country', 'woo-lithuaniapost' ),
                        'type' => 'select',
                        'options' => $this->get_country_list (),
                        'id' => 'lpsettings_sender_country',
                        'custom_attributes' => [ 'required' => 'required' ]
                    ],
                    // City
                    [
                        'name' => __( 'City', 'woo-lithuaniapost' ),
                        'type' => 'text',
                        'desc_tip' => __( 'Maximum length: 20 characters', 'woo-lithuaniapost'  ),
                        'id' => 'lpsettings_sender_city',
                        'custom_attributes' => [ 'required' => 'required', 'maxlength' => 20 ]
                    ],
                    // Street
                    [
                        'name' => __( 'Street', 'woo-lithuaniapost' ),
                        'type' => 'text',
                        'desc_tip' => __( 'Maximum length: 50 characters', 'woo-lithuaniapost'  ),
                        'id' => 'lpsettings_sender_street',
                        'custom_attributes' => [ 'required' => 'required', 'maxlength' => 50 ]
                    ],
                    // Building Number
                    [
                        'name' => __( 'Building Number', 'woo-lithuaniapost' ),
                        'type' => 'text',
                        'desc_tip' => __( 'Maximum length: 20 characters', 'woo-lithuaniapost'  ),
                        'id' => 'lpsettings_sender_building',
                        'custom_attributes' => [ 'required' => 'required', 'maxlength' => 20 ]
                    ],
                    // Apartment Number
                    [
                        'name' => __( 'Apartment Number', 'woo-lithuaniapost' ),
                        'type' => 'text',
                        'desc_tip' => __( 'Maximum length: 20 characters', 'woo-lithuaniapost'  ),
                        'id' => 'lpsettings_sender_apartment',
                        'custom_attributes' => [ 'required' => 'required', 'maxlength' => 20 ]
                    ],
                    // Post Code
                    [
                        'name' => __( 'Post Code', 'woo-lithuaniapost' ),
                        'type' => 'text',
                        'id' => 'lpsettings_sender_postcode',
                        'custom_attributes' => [ 'required' => 'required' ]
                    ],
                    // Section end
                    [
                        'type' => 'sectionend',
                        'id' => 'lpsettings_sender'
                    ],
                    /**
                     * Other settings
                     */
                    // Title
                    [
                        'name' => __( 'Other Settings', 'woo-lithuaniapost' ),
                        'type' => 'title',
                        'id' => 'lpsettings_other'
                    ],
                    [
                        'name' => __( 'Call courier automatically', 'woo-lithuaniapost' ),
                        'type' => 'checkbox',
                        'id' => 'lpsettings_call_courier_automatically'
                    ],
                    [
                        'name' => __( 'Consignment Formation', 'woo-lithuaniapost' ),
                        'type' => 'multiselect',
                        'options' => [
                            'priority'    => 'Priority',
                            'registered'  => 'Registered'
                        ],
                        'id' => 'lpsettings_consignment_formation'
                    ],
                    [
                        'name' => __( 'Label Format', 'woo-lithuaniapost' ),
                        'type' => 'select',
                        'options' => [
                            'LAYOUT_A4' => 'LAYOUT_A4',
                            'LAYOUT_MAX'    => 'LAYOUT_MAX',
                            'LAYOUT_10x15'  => 'LAYOUT_10x15'
                        ],
                        'id' => 'lpsettings_other_label_format'
                    ],
                    // Section end
                    [
                        'type' => 'sectionend',
                        'id' => 'lpsettings_other'
                    ]
                );
            }

            return $lpsettings;
        }

        /**
         * Return default settings
         */
        return $settings;
    }

    /**
     * Get settings options
     *
     * @param $option
     * @param string $prefix
     * @return mixed
     * @since 1.0.0
     */
    public static function get_option ( $option, $prefix = 'lpsettings_' )
    {
        return get_option ( $prefix . $option );
    }

    /**
     * Get formatted country list for options
     *
     * @param bool $code - if code needed instead of id
     * @return array
     * @since 1.0.0
     */
    public static function get_country_list ( $code = false )
    {
        global $wpdb;

        $list = [];
        $countries = $wpdb->get_results ("SELECT * FROM {$wpdb->woo_lithuaniapost_country_list}" );

        foreach ( $countries as $country ) {
            $list [ $code ? $country->country_code : $country->country_id ] = __( $country->country, 'woo-lithuaniapost' );
        }

        return $list;
    }
}
