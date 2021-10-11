<?php


class Woo_Lithuaniapost_Shipping_Lpexpress_Terminal extends WC_Shipping_Method
{
    /**
     * @var Woo_Lithuaniapost_Table_Rates $_table_rates
     */
    protected $_table_rates;

    /**
     * Constructor.
     *
     * @since 1.0.0
     * @param int $instance_id Shipping method instance ID.
     */
    public function __construct ( $instance_id = 0 )
    {
        $this->id                 = 'woo_lithuaniapost_lpexpress_terminal';
        $this->instance_id        = absint( $instance_id );
        $this->method_title       = __( 'LP EXPRESS - Delivery to terminal', 'woo-lithuaniapost' );
        $this->method_description = __( 'LP EXPRESS - Terminal shipping method.', 'woo-lithuaniapost' );
        $this->supports           = [
            'shipping-zones',
            'instance-settings',
        ];
        $this->init ();
        $this->_table_rates = new Woo_Lithuaniapost_Table_Rates ( $this->id );

        add_action( 'woocommerce_update_options_shipping_' . $this->id, [ $this, 'process_admin_options' ] );
    }

    /**
     * Init user set variables.
     *
     * @since 1.0.0
     */
    public function init ()
    {
        $this->instance_form_fields = include __DIR__ . '/includes/settings-lpexpress-terminal.php';
        $this->title                = $this->get_option ( 'title' );
        $this->delivery_method      = $this->get_option ( 'delivery_method' );
        $this->size                 = $this->get_option ( 'size' );
        $this->cost                 = $this->get_option ( 'cost' );
        $this->delivery_time        = $this->get_option ( 'delivery_time' );
        $this->tax_status           = $this->get_option ( 'tax_status' );
        $this->free_shipping_cost   = $this->get_option ( 'free_shipping_cost' );

        // Free shipping before discount
        $this->apply_free_shipping_before_discount = $this->get_option ( 'apply_free_shipping_before_discount' );
    }

    /**
     * Allow this method only in lithuania
     *
     * @since 1.0.0
     * @param array $package
     * @return bool
     */
    public function is_available ( $package )
    {
        return WC ()->customer->get_shipping_country () == 'LT';
    }

    /**
     * Calculate the shipping costs.
     *
     * @param array $packages
     * @return bool
     * @since 1.0.0
     */
    public function calculate_shipping ( $packages = [] )
    {
        // Use table rates
        if ( $this->cost == null ) {
            $this->_table_rates->calculate_table_rates ( $packages, $this->cost );
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
            'id'       => $this->get_rate_id (),
            'label'    => $this->title,
            'cost'     => $this->cost,
            'taxes' => ''
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
                <?php include __DIR__ . '/../rates/includes/html-tablerate-inputs.php'; ?>
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
        if ( $file = $_FILES [ $this->id . '_tablerates' ][ 'tmp_name' ] ) {
            if ( ( $handle = fopen ( $file, 'r' ) ) !== false ) {
                $this->_table_rates->process_table_rates ( $handle );
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
