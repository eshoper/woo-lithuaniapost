<?php
/**
 * WooCommerce custom order actions
 * Create label
 * Call courier
 * Print label
 * Print manifest
 * Print CN23 form
 * Print all documents
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
class Woo_Lithuaniapost_Admin_Order_Actions
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
     * Check if shipping method belongs to LP
     *
     * @return bool
     * @since 1.0.o
     */
    protected function is_shipping_method ()
    {
        $order = wc_get_order ( get_the_ID () );

        foreach ( $order->get_shipping_methods () as $method ) {
            return strpos ( $method->get_method_id (), 'woo_lithuaniapost' ) !== false;
        }

        return false;
    }

    /**
     * Check if lpexpress method
     *
     * @return bool
     * @since 1.0.0
     */
    protected function is_lpexpress_method ()
    {
        $order = wc_get_order ( get_the_ID () );

        foreach ( $order->get_shipping_methods () as $method ) {
            return strpos ( $method->get_method_id (), 'lpexpress' ) !== false;
        }

        return false;
    }

    /**
     * Add custom order actions
     *
     * @param $actions
     * @return array
     * @since 1.0.0
     */
    public function add_order_actions ( $actions )
    {
        if ( $this->is_shipping_method () ) {
            $order = wc_get_order ( get_the_ID () );
            $template = apply_filters (
                'woo_lithuaniapost_shipping_template',
                $order->get_meta ( '_woo_lithuaniapost_delivery_method' ),
                $order->get_meta ( '_woo_lithuaniapost_delivery_size' )
            );

            if ( $order->get_status () != 'lp-label-created' && $order->get_status () != 'lp-courier-called' ) {
                $actions [ 'woo_lp_create_label' ] = __( 'Create Shipping Label', 'woo-lithuaniapost' );
            }

            if ( $order->get_status () == 'lp-label-created' || $order->get_status () == 'lp-courier-called' ) {
                $actions [ 'woo_lp_cancel_label' ]    = __( 'Cancel Shipping Label', 'woo-lithuaniapost' );
                $actions [ 'woo_lp_print_label' ]     = __( 'Print Shipping Label', 'woo-lithuaniapost' );

                if ( $order->get_status () == 'lp-courier-called' ) {
                    $actions [ 'woo_lp_print_manifest' ]  = __( 'Print Manifest', 'woo-lithuaniapost' );
                } else {
                    if ( $this->is_lpexpress_method ()
                        && Woo_Lithuaniapost_Admin_Settings::get_option ( 'call_courier_automatically' ) != 'yes' ) {
                        $actions [ 'woo_lp_call_courier' ]  = __( 'Call Courier', 'woo-lithuaniapost' );
                    }
                }

                if ( apply_filters ( 'woo_lithuaniapost_shipping_template_is_cn23', $order, $template [ 'id' ] ) ) {
                    $actions [ 'woo_lp_print_cn_form' ]   = __( 'Print CN23 Form', 'woo-lithuaniapost' );
                }

                $actions [ 'woo_lp_print_all' ]       = __( 'Print All Documents', 'woo-lithuaniapost' );
            }

        }

        return $actions;
    }

    /**
     * Format shipment data hook
     *
     * @param WC_Order $order
     * @since 1.0.0
     * @return array
     */
    public function get_shipment_data ( $order )
    {
        /**
         * Get shipping template from order
         */
        $template = apply_filters (
            'woo_lithuaniapost_shipping_template',
            $order->get_meta ( '_woo_lithuaniapost_delivery_method' ),
            $order->get_meta ( '_woo_lithuaniapost_delivery_size' )
        );

        /**
         * Get parcel weight
         */
        $parcelWeight = 0;

        foreach ( $order->get_items () as $item ) {
            $_product = wc_get_product ( $item->get_data ()[ 'product_id' ] );
            $parcelWeight += floatval ( $_product->get_weight () );
        }

        $receiver_phone = $order->get_billing_phone ();

        // Convert to 370xxxxxxx
        if ( substr ( $receiver_phone, 0, 1 ) === "8" ) {
            $receiver_phone = substr_replace ( $receiver_phone, "370", 0, 1 );
        }

        /**
         * Form shipment data
         */
        $shipment_data = [
            'template' => $template [ 'id' ],
            'receiver' => [
                'name'    => sprintf ('%s %s',
                    $order->get_shipping_first_name (),
                    $order->get_shipping_last_name ()
                ),
                'address' => [
                    'locality'          => $order->get_shipping_city (),
                    $template [ 'id' ] == 73 ? 'address1' : 'freeformAddress' => $order->get_shipping_address_1 () . ' ' . $order->get_shipping_address_2 (),
                    'postalCode'        => $order->get_shipping_postcode (),
                    'country'           => $order->get_shipping_country ()
                ],
                'phone' => $receiver_phone,
                'email' => $order->get_billing_email ()
            ],
            'sender' => json_decode ( $order->get_meta ( '_woo_lithuaniapost_sender_info' ), true ),
            'partCount' => intval ( $order->get_meta ( '_woo_lithuaniapost_parts' ) ),
            'weight'    => $template [ 'weight' ] > 0 ? $parcelWeight * 1000 : null,
            'documents' => json_decode ( $order->get_meta ( '_woo_lithuaniapost_cn_data' ), true ),
            'additionalServices' => json_decode ( $order->get_meta ( '_woo_lithuaniapost_additional' ), true )
        ];

        /**
         * If terminal shipping method selected
         */
        /** @var WC_Order_Item_Shipping $shipping_method */
        $shipping_method = reset ( $order->get_shipping_methods () );

        if ( $shipping_method->get_method_id () == 'woo_lithuaniapost_lpexpress_terminal' ) {
            $shipment_data [ 'receiver' ][ 'terminalId' ] = $order->get_meta ( '_woo_lithuaniapost_lpexpress_terminal_id' );
        }

        return $shipment_data;
    }

    /**
     * Process API create shipping label
     *
     * @param WC_Order $order
     * @since 1.0.0
     */
    public function process_create_label ( $order )
    {
        global $wpdb;

        /**
         * Send shipment data to API
         */
        if ( $shipping_item_id = apply_filters ( 'woo_lithuaniapost_api_create_shipping_item', $this->get_shipment_data ( $order ) ) ) {
            if ( $cart_id = apply_filters ( 'woo_lithuaniapost_api_initiate_shipping', [ $shipping_item_id ] ) ) {
                if ( $barcode = apply_filters ( 'woo_lithuaniapost_api_get_barcode', $shipping_item_id ) ) {
                    // Save meta to order
                    $order->update_status ( 'wc-lp-label-created' );
                    $order->update_meta_data ( '_woo_lithuaniapost_barcode', $barcode );
                    $order->update_meta_data ( '_woo_lithuaniapost_cart_id', $cart_id );
                    $order->update_meta_data ( '_woo_lithuaniapost_shipping_item_id', $shipping_item_id );
                    $order->save ();

                    // Insert barcode for tracking
                    $wpdb->insert ( $wpdb->woo_lithuaniapost_tracking_events, [
                        'order_id' => $order->get_id (),
                        'barcode' => $barcode
                    ] );
                }
            }
        } else {
            // If error occured
            return $order->get_id ();
        }
    }

    /**
     * Cancel shipping label
     *
     * @param WC_Order $order
     * @since 1.0.0
     */
    public function process_cancel_label ( $order )
    {
        global $wpdb;

        $shipping_item_id = $order->get_meta ( '_woo_lithuaniapost_shipping_item_id' );

        if ( apply_filters ( 'woo_lithuaniapost_api_cancel_label', $shipping_item_id ) ) {
            // Remove meta from order
            $order->update_meta_data ( '_woo_lithuaniapost_barcode', null );
            $order->update_meta_data ( '_woo_lithuaniapost_cart_id', null );
            $order->update_meta_data ( '_woo_lithuaniapost_shipping_item_id', null );
            $order->update_status ( 'wc-processing' );
            $order->save ();

            // Insert barcode for tracking
            $wpdb->delete ( $wpdb->woo_lithuaniapost_tracking_events, [
                'order_id' => $order->get_id ()
            ] );

        } else {
            // If error occured
            return $order->get_id ();
        }
    }

    /**
     * Print shipping label
     *
     * @param WC_Order $order
     * @since 1.0.0
     */
    public function process_print_label ( $order )
    {
        $filename = sprintf ( 'label_%s_%s.pdf',
            $order->get_order_number (),
            $order->get_meta ( '_woo_lithuaniapost_barcode' )
        );

        $shipping_item_id = $order->get_meta ( '_woo_lithuaniapost_shipping_item_id' );

        if ( $label_content = apply_filters ( 'woo_lithuaniapost_api_create_sticker', $shipping_item_id ) ) {
            // Send file
            woo_lithuaniapost_file_headers ( $filename, 'application/pdf' );

            $output = fopen ( 'php://output', 'w' );
            fwrite ( $output, base64_decode ( $label_content ) );
            readfile ( 'php://output' );

            die ();
        }
    }

    /**
     * Call courier
     *
     * @param WC_Order $order
     * @return bool
     * @since 1.0.0
     */
    public function process_call_courier ( $order )
    {
        $shipping_item_id = $order->get_meta ( '_woo_lithuaniapost_shipping_item_id' );

        if ( $response = apply_filters ( 'woo_lithuaniapost_api_call_courier', [ $shipping_item_id ] ) ) {
            $order->update_status ( 'wc-lp-courier-called' );
        } else {
            // If error occured
            return $order->get_id ();
        }
    }

    /**
     * Print manifest
     *
     * @param WC_Order $order
     * @since 1.0.0
     */
    public function process_print_manifest ( $order )
    {
        $filename = sprintf ( 'manifest_%s_%s.pdf',
            $order->get_order_number (),
            $order->get_meta ( '_woo_lithuaniapost_barcode' )
        );

        $cart_id = $order->get_meta ( '_woo_lithuaniapost_cart_id' );

        if ( $manifest_content = apply_filters ( 'woo_lithuaniapost_api_get_manifest', $cart_id ) ) {
            // Send file
            woo_lithuaniapost_file_headers ( $filename, 'application/pdf' );

            $output = fopen ( 'php://output', 'w' );
            fwrite ( $output, base64_decode ( $manifest_content ) );
            readfile ( 'php://output' );

            die ();
        }
    }

    /**
     * Print CN form
     *
     * @param WC_Order $order
     * @since 1.0.0
     */
    public function process_print_cn_form ( $order )
    {
        $filename = sprintf ( 'cn23_%s_%s.pdf',
            $order->get_order_number (),
            $order->get_meta ( '_woo_lithuaniapost_barcode' )
        );

        $cart_id = $order->get_meta ( '_woo_lithuaniapost_cart_id' );

        if ( $cn23_content = apply_filters ( 'woo_lithuaniapost_api_get_cn23_form', $cart_id ) ) {
            // Send file
            woo_lithuaniapost_file_headers ( $filename, 'application/pdf' );

            $output = fopen ( 'php://output', 'w' );
            fwrite ( $output, base64_decode ( $cn23_content ) );
            readfile ( 'php://output' );

            die ();
        }
    }

    /**
     * Merge all documents
     *
     * @param WC_Order $order
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    public function process_print_all ( $order )
    {
        require_once plugin_dir_path ( dirname ( __FILE__ ) ) . '/vendor/autoload.php';

        $pdf = new setasign\Fpdi\Fpdi ();

        $filename = sprintf ( 'documents_%s_%s.pdf',
            $order->get_order_number (),
            $order->get_meta ( '_woo_lithuaniapost_barcode' )
        );

        $shipping_item_id = $order->get_meta ( '_woo_lithuaniapost_shipping_item_id' );
        $cart_id = $order->get_meta ( '_woo_lithuaniapost_cart_id' );

        if ( $label_content = apply_filters ( 'woo_lithuaniapost_api_create_sticker', $shipping_item_id ) ) {
            $pdf->setSourceFile ( \setasign\Fpdi\PdfParser\StreamReader::createByString ( base64_decode ( $label_content ) ) );
            $tpl = $pdf->importPage ( 1 );
            $pdf->AddPage ();
            $pdf->useTemplate ( $tpl, 0, 0, null, null, true );
        }

        if ( $manifest_content = apply_filters ( 'woo_lithuaniapost_api_get_manifest', $cart_id ) ) {
            $pdf->setSourceFile ( \setasign\Fpdi\PdfParser\StreamReader::createByString ( base64_decode ( $manifest_content ) ) );
            $tpl = $pdf->importPage ( 1 );
            $pdf->AddPage ();
            $pdf->useTemplate ( $tpl, 0, 0, null, null, true );
        }

        if ( $cn23_content = apply_filters ( 'woo_lithuaniapost_api_get_cn23_form', $cart_id ) ) {
            $pdf->setSourceFile ( \setasign\Fpdi\PdfParser\StreamReader::createByString ( base64_decode ( $cn23_content ) ) );
            $tpl = $pdf->importPage ( 1 );
            $pdf->AddPage ();
            $pdf->useTemplate ( $tpl, 0, 0, null, null, true );
        }

        $pdf->Output ( 'D', $filename );
        die ();
    }
}
