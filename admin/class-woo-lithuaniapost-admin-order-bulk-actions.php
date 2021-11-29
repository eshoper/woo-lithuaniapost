<?php
/**
 * WooCommerce custom order bulk actions
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
class Woo_Lithuaniapost_Admin_Order_Bulk_Actions
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
     * Register bulk actions
     *
     * @param $actions
     * @return mixed
     * @since 1.0.0
     */
    public function register_bulk_actions ( $actions )
    {
        $actions [ 'woo_lp_create_label' ]    = __( 'LP Create Shipping Labels', 'woo-lithuaniapost' );
        $actions [ 'woo_lp_cancel_label' ]    = __( 'LP Cancel Shipping Labels', 'woo-lithuaniapost' );
        $actions [ 'woo_lp_call_courier' ]    = __( 'LP Call Courier', 'woo-lithuaniapost' );
        $actions [ 'woo_lp_print_label' ]     = __( 'LP Print Shipping Labels', 'woo-lithuaniapost' );
        $actions [ 'woo_lp_print_manifest' ]  = __( 'LP Print Manifests', 'woo-lithuaniapost' );
        $actions [ 'woo_lp_print_cn_form' ]   = __( 'LP Print CN23 Forms', 'woo-lithuaniapost' );
        $actions [ 'woo_lp_print_all' ]       = __( 'LP Print All Documents', 'woo-lithuaniapost' );

        return $actions;
    }

    /**
     * Handle create labels
     *
     * @param $redirect_to
     * @param $action
     * @param $post_ids
     * @return mixed
     * @since 1.0.0
     */
    public function handle_create_labels ( $redirect_to, $action, $post_ids )
    {
        global $wpdb;

        $shipping_item_ids = [];
        $error_orders = [];

        if ( $action !== 'woo_lp_create_label' ) {
            return $redirect_to; // exit
        }

        foreach ( $post_ids as $post_id ) {
            $order = wc_get_order ( $post_id );
            $shipment_data = apply_filters ( 'woo_lithuaniapost_get_shipment_data', $order );

            if ( $shipping_item_id = apply_filters ( 'woo_lithuaniapost_api_create_shipping_item', $shipment_data ) ) {
                array_push ( $shipping_item_ids, $shipping_item_id );
                $order->update_meta_data ( '_woo_lithuaniapost_shipping_item_id', $shipping_item_id );
                $order->save ();
            } else {
                $error_orders [] = $post_id;
            }
        }

        if ( $cart_id = apply_filters ( 'woo_lithuaniapost_api_initiate_shipping', $shipping_item_ids ) ) {
            foreach ( $post_ids as $post_id ) {
                $order = wc_get_order ( $post_id );
                if ( $barcode = apply_filters ( 'woo_lithuaniapost_api_get_barcode',
                        $order->get_meta ( '_woo_lithuaniapost_shipping_item_id' ) ) ) {
                    // Save meta to order
                    $order->update_status ( 'wc-lp-label-created' );
                    $order->update_meta_data ( '_woo_lithuaniapost_barcode', $barcode );
                    $order->update_meta_data ( '_woo_lithuaniapost_cart_id', $cart_id );
                    $order->save ();

                    // Insert barcode for tracking
                    $wpdb->insert ( $wpdb->woo_lithuaniapost_tracking_events, [
                        'order_id' => $order->get_id (),
                        'barcode' => $barcode
                    ] );
                }
            }
        }

        $redirect_to = add_query_arg ( [
            'post_type'   => 'shop_order',
            'bulk_lp_create_labels' => 1,
            'bulk_lp_error' => implode ( ',', $error_orders ),
            ]
        );

        return $redirect_to;
    }

    /**
     * Handle cancel labels
     *
     * @param $redirect_to
     * @param $action
     * @param $post_ids
     * @return mixed
     * @since 1.0.0
     */
    public function handle_cancel_labels ( $redirect_to, $action, $post_ids )
    {
        $error_orders = [];

        if ( $action !== 'woo_lp_cancel_label' ) {
            return $redirect_to; // exit
        }

        foreach ( $post_ids as $post_id ) {
            $order = wc_get_order ( $post_id );
            $order_id = apply_filters ( 'woocommerce_order_action_woo_lp_cancel_label', $order );

            if ( $order_id ) {
                $error_orders [] = $order_id;
            }
        }

        $redirect_to = add_query_arg ( [
                'post_type'   => 'shop_order',
                'bulk_lp_cancel_labels' => 1,
                'bulk_lp_error' => implode ( ',', $error_orders ),
            ]
        );

        return $redirect_to;
    }

    /**
     * Handle print labels
     *
     * @param $redirect_to
     * @param $action
     * @param $post_ids
     * @return mixed
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    public function handle_print_labels ( $redirect_to, $action, $post_ids )
    {
        if ( $action !== 'woo_lp_print_label' ) {
            return $redirect_to; // exit
        }

        require_once plugin_dir_path ( dirname ( __FILE__ ) ) . '/vendor/autoload.php';

        $pdf = new setasign\Fpdi\Fpdi ();

        $filename = sprintf ( 'lp_labels_%s.pdf',
            date ( 'Y-m-d H:i:s' )
        );

        foreach ( $post_ids as $post_id ) {
            $order = wc_get_order ( $post_id );

            $shipping_item_id = $order->get_meta ( '_woo_lithuaniapost_shipping_item_id' );

            if ( $label_content = apply_filters ( 'woo_lithuaniapost_api_create_sticker', $shipping_item_id ) ) {
                $pdf->setSourceFile ( \setasign\Fpdi\PdfParser\StreamReader::createByString ( base64_decode ( $label_content ) ) );
                $tpl = $pdf->importPage ( 1 );
                $pdf->AddPage ();
                $pdf->useTemplate ( $tpl, 0, 0, null, null, true );
            }
        }

        // Output file
        $pdf->Output ( 'D', $filename );
        die ();
    }

    /**
     * Call courier
     *
     * @param $redirect_to
     * @param $action
     * @param $post_ids
     * @return mixed
     */
    public function handle_call_courier ( $redirect_to, $action, $post_ids )
    {
        $error_orders = [];
        $shipping_item_ids = [];

        if ( $action !== 'woo_lp_call_courier' ) {
            return $redirect_to; // exit
        }

        foreach ( $post_ids as $post_id ) {
            $order = wc_get_order ( $post_id );
            array_push ( $shipping_item_ids, $order->get_meta ( '_woo_lithuaniapost_shipping_item_id' ) );
        }

        if ( $response = apply_filters ( 'woo_lithuaniapost_api_call_courier', $shipping_item_ids ) ) {
            foreach ( $post_ids as $post_id ) {
                $order = wc_get_order ( $post_id );
                $order->update_status ( 'wc-lp-courier-called' );
            }
        } else {
            $error_orders = [ 1 ];
        }

        $redirect_to = add_query_arg ( [
                'post_type'   => 'shop_order',
                'bulk_lp_call_courier' => 1,
                'bulk_lp_error' => implode ( ',', $error_orders ),
            ]
        );

        return $redirect_to;
    }

    /**
     * Handle print manifests
     *
     * @param $redirect_to
     * @param $action
     * @param $post_ids
     * @return mixed
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    public function handle_print_manifests ( $redirect_to, $action, $post_ids )
    {
        $cart_ids = [];

        if ( $action !== 'woo_lp_print_manifest' ) {
            return $redirect_to; // exit
        }

        require_once plugin_dir_path ( dirname ( __FILE__ ) ) . '/vendor/autoload.php';

        $pdf = new setasign\Fpdi\Fpdi ();

        $filename = sprintf ( 'lp_manifests_%s.pdf',
            date ( 'Y-m-d H:i:s' )
        );

        foreach ( $post_ids as $post_id ) {
            $order = wc_get_order ( $post_id );
            array_push (  $cart_ids, $order->get_meta ( '_woo_lithuaniapost_cart_id' ) );
        }

        // Filter out duplicates
        $cart_ids = array_unique ( $cart_ids );

        foreach ( $cart_ids as $cart_id ) {
            if ( $manifest_content = apply_filters ( 'woo_lithuaniapost_api_get_manifest', $cart_id ) ) {
                $pdf->setSourceFile ( \setasign\Fpdi\PdfParser\StreamReader::createByString ( base64_decode ( $manifest_content ) ) );
                $tpl = $pdf->importPage ( 1 );
                $pdf->AddPage ();
                $pdf->useTemplate ( $tpl, 0, 0, null, null, true );
            }
        }

        // Output file
        $pdf->Output ( 'D', $filename );
        die ();
    }

    /**
     * Print CN forms
     *
     * @param $redirect_to
     * @param $action
     * @param $post_ids
     * @return mixed
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    public function handle_print_cn_forms ( $redirect_to, $action, $post_ids )
    {
        if ( $action !== 'woo_lp_print_cn_form' ) {
            return $redirect_to; // exit
        }

        require_once plugin_dir_path ( dirname ( __FILE__ ) ) . '/vendor/autoload.php';

        $pdf = new setasign\Fpdi\Fpdi ();

        $filename = sprintf ( 'lp_cnforms_%s.pdf',
            date ( 'Y-m-d H:i:s' )
        );

        foreach ( $post_ids as $post_id ) {
            $order = wc_get_order ( $post_id );

            $cart_id = $order->get_meta ( '_woo_lithuaniapost_cart_id' );

            if ( $cn23_content = apply_filters ( 'woo_lithuaniapost_api_get_cn23_form', $cart_id ) ) {
                $pdf->setSourceFile ( \setasign\Fpdi\PdfParser\StreamReader::createByString ( base64_decode ( $cn23_content ) ) );
                $tpl = $pdf->importPage ( 1 );
                $pdf->AddPage ();
                $pdf->useTemplate ( $tpl, 0, 0, null, null, true );
            }
        }

        // Output file
        $pdf->Output ( 'D', $filename );
        die ();
    }

    /**
     * Handle print all documents
     *
     * @param $redirect_to
     * @param $action
     * @param $post_ids
     * @return mixed
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    public function handle_print_all_documents ( $redirect_to, $action, $post_ids )
    {
        if ( $action !== 'woo_lp_print_all' ) {
            return $redirect_to; // exit
        }

        require_once plugin_dir_path ( dirname ( __FILE__ ) ) . '/vendor/autoload.php';

        $pdf = new setasign\Fpdi\Fpdi ();

        $filename = sprintf ( 'lp_documents_%s.pdf',
            date ( 'Y-m-d H:i:s' )
        );

        foreach ( $post_ids as $post_id ) {
            $order = wc_get_order ( $post_id );

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
        }

        // Output file
        $pdf->Output ( 'D', $filename );
        die ();
    }

    /**
     * Bulk order action notices
     *
     * @since 1.0.0
     */
    public function bulk_action_admin_notice ()
    {
        if ( $errors = @$_REQUEST [ 'bulk_lp_error' ] ):
            printf (
                '<div id="message" class="updated error fade"><p>' .
                _n ( 'Could not complete action for orders %s.', 'Could not complete action for orders %s.', $errors,
                    'woo-lithuaniapost' )
                . '</p></div>',
                $errors
            );
        endif;

        if ( @$_REQUEST [ 'bulk_lp_call_courier' ] ):
            ?>
                <div id="message" class="updated fade">
                    <p><?php _e ( 'Call courier action complete.', 'woo-lithuaniapost' ); ?></p>
                </div>
            <?php
        endif;

        if ( @$_REQUEST [ 'bulk_lp_create_labels' ] ):
            ?>
                <div id="message" class="updated fade">
                    <p><?php _e ( 'Create labels action complete.', 'woo-lithuaniapost' ); ?></p>
                </div>
            <?php
        endif;

        if ( @$_REQUEST [ 'bulk_lp_cancel_labels' ] ):
            ?>
                <div id="message" class="updated fade">
                    <p><?php _e ( 'Cancel labels action complete.', 'woo-lithuaniapost' ); ?></p>
                </div>
            <?php
        endif;
    }
}
