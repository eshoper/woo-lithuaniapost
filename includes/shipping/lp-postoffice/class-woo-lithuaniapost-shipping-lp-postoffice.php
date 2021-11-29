<?php
/**
 * Lithuania Post - Delivery To Home, Office Or Post Office
 *
 * @link       https://post.lt
 * @since      1.0.0
 *
 * @package    Woo_Lithuaniapost
 * @subpackage Woo_Lithuaniapost/includes/shipping
 */
defined( 'ABSPATH' ) || exit;

class Woo_Lithuaniapost_Shipping_Lp_Postoffice extends WC_Shipping_Method
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
        $this->id                 = 'woo_lithuaniapost_lp_postoffice';
        $this->instance_id        = absint( $instance_id );
        $this->method_title       = __( 'Lithuania Post - Delivery to home, office or post office', 'woo-lithuaniapost' );
        $this->method_description = __( 'Lithuania Post - Post office delivery method.', 'woo-lithuaniapost' );
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
        $this->instance_form_fields = include __DIR__ . '/includes/settings-lp-postoffice.php';
        $this->title                = $this->get_option( 'title' );
        $this->delivery_method      = $this->get_option ( 'delivery_method' );
        $this->cost                 = $this->get_option( 'cost' );
        $this->delivery_time        = $this->get_option( 'delivery_time' );
        $this->tax_status           = $this->get_option ( 'tax_status' );
        $this->free_shipping_cost   = $this->get_option ( 'free_shipping_cost' );
        $this->type                 = $this->get_option( 'type', 'class' );

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
     * Evaluate a cost from a sum/string.
     *
     * @param  string $sum Sum of shipping.
     * @param  array  $args Args, must contain `cost` and `qty` keys. Having `array()` as default is for back compat reasons.
     * @return string
     */
    protected function evaluate_cost ( $sum, $args = array() ) {
        // Add warning for subclasses.
        if ( ! is_array( $args ) || ! array_key_exists( 'qty', $args ) || ! array_key_exists( 'cost', $args ) ) {
            wc_doing_it_wrong( __FUNCTION__, '$args must contain `cost` and `qty` keys.', '4.0.1' );
        }

        include_once WC ()->plugin_path() . '/includes/libraries/class-wc-eval-math.php';

        // Allow 3rd parties to process shipping cost arguments.
        $args           = apply_filters( 'woocommerce_evaluate_shipping_cost_args', $args, $sum, $this );
        $locale         = localeconv();
        $decimals       = array( wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'], ',' );
        $this->fee_cost = $args['cost'];

        // Expand shortcodes.
        add_shortcode( 'fee', array( $this, 'fee' ) );

        $sum = do_shortcode(
            str_replace(
                array(
                    '[qty]',
                    '[cost]',
                ),
                array(
                    $args['qty'],
                    $args['cost'],
                ),
                $sum
            )
        );

        remove_shortcode( 'fee', array( $this, 'fee' ) );

        // Remove whitespace from string.
        $sum = preg_replace( '/\s+/', '', $sum );

        // Remove locale from string.
        $sum = str_replace( $decimals, '.', $sum );

        // Trim invalid start/end characters.
        $sum = rtrim( ltrim( $sum, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );

        // Do the math.
        return $sum ? WC_Eval_Math::evaluate( $sum ) : 0;
    }

    /**
     * Finds and returns shipping classes and the products with said class.
     *
     * @param mixed $package Package of items from cart.
     * @return array
     */
    public function find_shipping_classes ( $package ) {
        $found_shipping_classes = array ();

        foreach ( $package['contents'] as $item_id => $values ) {
            if ( $values['data']->needs_shipping () ) {
                $found_class = $values['data']->get_shipping_class ();

                if ( ! isset( $found_shipping_classes[ $found_class ] ) ) {
                    $found_shipping_classes[ $found_class ] = array();
                }

                $found_shipping_classes[ $found_class ][ $item_id ] = $values;
            }
        }

        return $found_shipping_classes;
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
            'id'       => $this->get_rate_id (),
            'label'    => $this->title,
            'cost'     => $this->cost,
            'taxes' => ''
        ];

        // Add shipping class costs.
        $shipping_classes = WC ()->shipping ()->get_shipping_classes ();

        if ( ! empty( $shipping_classes ) ) {
            $found_shipping_classes = $this->find_shipping_classes ( $packages );
            $highest_class_cost     = 0;

            foreach ( $found_shipping_classes as $shipping_class => $products ) {
                // Also handles BW compatibility when slugs were used instead of ids.
                $shipping_class_term = get_term_by( 'slug', $shipping_class, 'product_shipping_class' );
                $class_cost_string   = $shipping_class_term && $shipping_class_term->term_id ? $this->get_option( 'class_cost_' . $shipping_class_term->term_id, $this->get_option( 'class_cost_' . $shipping_class, '' ) ) : $this->get_option( 'no_class_cost', '' );

                if ( '' === $class_cost_string ) {
                    continue;
                }

                $class_cost = $this->evaluate_cost(
                    $class_cost_string,
                    array(
                        'qty'  => array_sum( wp_list_pluck( $products, 'quantity' ) ),
                        'cost' => array_sum( wp_list_pluck( $products, 'line_total' ) ),
                    )
                );

                if ( 'class' === $this->type ) {
                    $rate [ 'cost' ] += $class_cost;
                } else {
                    $highest_class_cost = $class_cost > $highest_class_cost ? $class_cost : $highest_class_cost;
                }
            }

            if ( 'order' === $this->type && $highest_class_cost ) {
                $rate [' cost' ] += $highest_class_cost;
            }
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
                $rate [ 'cost' ] = 0;
            }
        }

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
