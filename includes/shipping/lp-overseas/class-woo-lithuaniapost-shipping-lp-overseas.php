<?php


class Woo_Lithuaniapost_Shipping_Lp_Overseas extends WC_Shipping_Method
{
    /**
     * @var Woo_Lithuaniapost_Country_Rates $_country_rates
     */
    protected $_country_rates;

    /**
     * Constructor.
     *
     * @since 1.0.0
     * @param int $instance_id Shipping method instance ID.
     */
    public function __construct ( $instance_id = 0 )
    {
        $this->id                 = 'woo_lithuaniapost_lp_overseas';
        $this->instance_id        = absint( $instance_id );
        $this->method_title       = __( 'Lithuania Post - Delivery to overseas', 'woo-lithuaniapost' );
        $this->method_description = __( 'Lithuania Post - Delivery to overseas shipping method.', 'woo-lithuaniapost' );
        $this->supports           = [
            'shipping-zones',
            'instance-settings',
        ];
        $this->init();
        $this->_country_rates = new Woo_Lithuaniapost_Country_Rates ( $this->id );

        add_action( 'woocommerce_update_options_shipping_' . $this->id, [ $this, 'process_admin_options' ] );
    }

    /**
     * Init user set variables.
     *
     * @since 1.0.0
     */
    public function init ()
    {
        $this->instance_form_fields = include __DIR__ . '/includes/settings-lp-overseas.php';
        $this->title                = $this->get_option( 'title' );
        $this->delivery_method      = $this->get_option( 'delivery_method' );
        $this->cost                 = $this->get_option( 'cost' );
        $this->delivery_time        = $this->get_option( 'delivery_time' );
        $this->tax_status           = $this->get_option ( 'tax_status' );
        $this->free_shipping_cost   = $this->get_option ( 'free_shipping_cost' );

        // Free shipping before discount
        $this->apply_free_shipping_before_discount = $this->get_option ( 'apply_free_shipping_before_discount' );
    }

    /**
     * Allow this method only in overseas countries
     * TODO: Need to add only available countries from LP DB table
     *
     * @since 1.0.0
     * @param array $package
     * @return bool
     */
    public function is_available ( $package )
    {
        return WC ()->customer->get_shipping_country () != 'LT';
    }

    /**
     * Calculate the shipping costs.
     *
     * @since 1.0.0
     * @param array $packages
     * @return bool
     */
    public function calculate_shipping ( $packages = [] )
    {
        global $wpdb;

        // Use country rates
        if ( $this->cost == null ) {
            $this->_country_rates->calculate_country_rates ( $this->cost );
            if ( !$this->cost ) return false;
        }

        // Free shipping from minimal amount
        if ( $this->free_shipping_cost ) {
            // Price with discount
            $cart_subtotal = WC ()->cart->get_subtotal () - WC ()->cart->get_discount_total ();

            // Price plus discount
            if ( $this->apply_free_shipping_before_discount == 'yes' ) {
                $cart_subtotal += WC ()->cart->get_discount_total ();
            }

            if ( $this->free_shipping_cost <= $cart_subtotal ) {
                $this->cost = 0;
            }
        }

        $rate = [
            'id'       => $this->id,
            'label'    => $this->title,
            'cost'     => $this->cost,
            'calc_tax' => 'per_item'
        ];

        $this->add_rate ( $rate );
    }

    /**
     * Instance options with table rate inputs
     *
     * @since 1.0.0
     */
    public function instance_options ()
    {
        ?>
        <table class="form-table">
            <?php $this->generate_settings_html ( $this->get_instance_form_fields () ); ?>
            <?php include __DIR__ . '/../rates/includes/html-country-rate-inputs.php'; ?>
        </table>
        <?php
    }

    /**
     * Admin options
     */
    public function admin_options ()
    {
        $this->instance_options();
    }

    /**
     * Admin options HTML
     *
     * @return false|string
     * @since 1.0.0
     */
    public function get_admin_options_html ()
    {
        ob_start();
        $this->instance_options ();
        return ob_get_clean();
    }

    /**
     * Save table rates here and process other options
     *
     * @return bool
     * @since 1.0.0
     */
    public function process_admin_options ()
    {
        if ( $file = $_FILES [ $this->id . '_countryrates' ][ 'tmp_name' ] ) {
            if ( ( $handle = fopen ( $file, 'r' ) ) !== false ) {
                $this->_country_rates->process_country_rates ( $handle );
            }
        }

        return parent::process_admin_options ();
    }

    /**
     * Sanitize the cost field.
     *
     * @since 1.0.0
     * @param string $value Unsanitized value.
     * @return string
     */
    public function sanitize_cost ( $value )
    {
        $value = is_null( $value ) ? '' : $value;
        $value = wp_kses_post( trim( wp_unslash( $value ) ) );
        $value = str_replace( [ get_woocommerce_currency_symbol(), html_entity_decode( get_woocommerce_currency_symbol() ) ],
            '', $value );

        return $value;
    }
}
