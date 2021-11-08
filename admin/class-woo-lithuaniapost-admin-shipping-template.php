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
class Woo_Lithuaniapost_Admin_Shipping_Template
{
    /**
     * Default constants for CN data
     */
    const CN_PARCEL_TYPE            = 'sell';
    const CN_PARCEL_TYPE_NOTES      = 'Sell Items';
    const CN_PARCEL_DESCRIPTION     = 'Sell';

    // Special drawing right coefficient
    const SDR_COF = 1.786;

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
     * Get additional services
     *
     * @param WC_Order $order
     * @param int $template_id
     * @return array
     * @since 1.0.0
     */
    public function get_additional_services ( $order, $template_id )
    {
        $result = [];
        $payment_method = $order->get_payment_method ();
        $selected_method = WC ()->session->get ( 'chosen_shipping_methods' ) [ 0 ];

        if ( $payment_method == 'cod' ) {
            // COD Value
            $result [] = [ 'id' => 8, 'amount' => $order->get_total () ];
        }

        // Priority - Registered
        foreach ( Woo_Lithuaniapost_Admin_Settings::get_option ( 'consignment_formation' ) as $formation ) {
            if ( !$this->is_lpexpress_method ( $selected_method ) && $formation == 'priority' ) {
                $result [] = [ 'id' => 1 ]; // Priority
            }

            if ( !$this->is_lpexpress_method ( $selected_method ) && $formation == 'registered' || $template_id == 73 || $template_id == 70 ) {
                $result [] = [ 'id' => 2 ]; // Registered
            }
        }

        return $result;
    }

    /**
     * Get is LP EXPRESS method
     * @param string $shipping_method
     * @return bool
     */
    protected function is_lpexpress_method ( $shipping_method )
    {
        return strpos ( $shipping_method, 'woo_lithuaniapost_lpexpress' ) !== false;
    }

    /**
     * Get SDR value
     *
     * @param $price
     * @return float|int
     * @since 1.0.0
     */
    public function get_sdr_value ( $price )
    {
        return self::SDR_COF * $price;
    }

    /**
     * Get CN data defaults
     *
     * @param WC_Order $order
     * @param string $cn_form
     * @return array
     * @since 1.0.0
     */
    public function get_cn_data_defaults ( $order, $cn_form )
    {
        $cn_parts = [];

        foreach ( $order->get_items () as $item ) {
            $cn_parts [] = [
                'summary' => $item->get_name (),
                'amount' => $item->get_total (),
                'countryCode' => 'LT', // Default country of origin
                'currencyCode' => $order->get_currency (),
                'hsCode' => '',
                'weight' => $item->get_product ()->get_weight () * 1000, // Weight in grams
                'quantity' => intval ( $item->get_quantity () )
            ];
        }

        return [
            $cn_form => [
                'parcelType' => self::CN_PARCEL_TYPE,
                'parcelTypeNotes' => self::CN_PARCEL_TYPE_NOTES,
                'description' => self::CN_PARCEL_DESCRIPTION,
                'cnParts' => $cn_parts
            ]
        ];
    }

    /**
     * Is CN22 Form
     *
     * @param WC_Order $order
     * @param int $template_id
     * @return bool
     * @since 1.0.0
     */
    public function is_cn22 ( $order, $template_id )
    {
        // Check if not in EU
        if ( !in_array ( $order->get_shipping_country (), WC_Countries::get_european_union_countries () ) ) {
            // CN22
            if ( in_array ( $template_id, [ 42, 43, 70, 73 ] )
                && $this->get_sdr_value ( $order->get_total () ) < 300 ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Is CN23 Form
     *
     * @param WC_Order $order
     * @param $template_id
     * @return bool
     * @since 1.0.0
     */
    public function is_cn23 ( $order, $template_id )
    {
        // Check if not in EU
        if ( !in_array ( $order->get_shipping_country (), WC_Countries::get_european_union_countries () ) ) {
            // CN23
            if ( in_array ( $template_id, [ 42, 43, 70, 73 ] )
                && $this->get_sdr_value ( $order->get_total () ) > 300 ) {
                return true;
            }

            // CN23
            if ( in_array ( $template_id, [ 45, 49, 50, 51, 52, 53 ] ) ) {
                return true;
            }
        }

        // CN23
        if ( $template_id == 44 ) {
            return true;
        }

        return false;
    }

    /**
     * Get CN document data
     *
     * @param $order
     * @param $template_id
     * @return array|null
     * @since 1.0.0
     */
    public function get_cn_data ( $order, $template_id )
    {
        if ( $this->is_cn22 ( $order, $template_id ) ) {
            return $this->get_cn_data_defaults ( $order, 'cn22Form' );
        }

        if ( $this->is_cn23 ( $order, $template_id ) ) {
            return $this->get_cn_data_defaults ( $order, 'cn23Form' );
        }

        return null;
    }

    /**
     * Get shipping template by shipping type and size
     *
     * @param $type
     * @param null $size
     * @return array|bool
     * @since 1.0.0
     */
    public function get_shipping_template ( $type, $size = null )
    {
        global $wpdb;

        // For tracked types fixtures
        if ( $type == 'SMALL_CORESPONDENCE_TRACKED' ) {
            $size = 'Small';
        }

        if ( $type == 'MEDIUM_CORESPONDENCE_TRACKED' ) {
            $size = 'Medium';
        }

        if ( $type == 'EBIN' ) {
            $size = null;
        }

        /**
         * Shipping templates JSON data
         */
        $result = $wpdb->get_row (
            "SELECT shipping_templates FROM {$wpdb->woo_lithuaniapost_shipping_templates}"
        );

        if ( $result ) {
            $shipping_templates = json_decode ( $result->shipping_templates, true );

            /**
             * Get shipping template according to type and/or size
             */
            $template = array_filter ( $shipping_templates, function ( $template ) use ( $type, $size ) {
                return $template [ 'type' ] == $type && @$template [ 'size' ] == $size;
            });

            // First result
            $template = reset ( $template );

            return $template;
        }

        return false;
    }

    /**
     * @param $template_id
     * @return bool
     */
    public function is_cod_available ( $template_id )
    {
        return in_array ( $template_id, [ 42, 43, 44, 45, 46, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63 ] );
    }
}
