<?php


class Woo_Lithuaniapost_Shipping_Lpexpress_Courier extends WC_Shipping_Method
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
        $this->id                 = 'woo_lithuaniapost_lpexpress_courier';
        $this->instance_id        = absint( $instance_id );
        $this->method_title       = __( 'LP EXPRESS - Delivery to home, office by courier', 'woo-lithuaniapost' );
        $this->method_description = __( 'LP EXPRESS - Courier shipping method.', 'woo-lithuaniapost' );
        $this->supports           = [
            'shipping-zones',
            'instance-settings',
        ];
        $this->init();
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
        $this->instance_form_fields = include __DIR__ . '/includes/settings-lpexpress-courier.php';
        $this->title                = $this->get_option( 'title' );
        $this->delivery_method      = $this->get_option( 'delivery_method' );
        $this->cost                 = $this->get_option( 'cost' );
        $this->delivery_time        = $this->get_option( 'delivery_time' );
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
