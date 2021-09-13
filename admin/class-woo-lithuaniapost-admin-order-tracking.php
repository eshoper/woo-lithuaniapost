<?php
/**
 * Order tracking
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
class Woo_Lithuaniapost_Admin_Order_Tracking
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct ( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Schedule tracking info
     *
     * @since 1.0.0
     */
    public function schedule_tracking_info ()
    {
        if (! wp_next_scheduled ( 'woo_lithuaniapost_save_tracking_events' ) ) {
            wp_schedule_event ( time (), 'every_minute', 'woo_lithuaniapost_save_tracking_events' );
        }
    }

    /**
     * Get state by state code
     *
     * @param $state_code
     * @return string|null
     */
    public function get_state_by_code ( $state_code )
    {
        $state = [
            'ACCEPTED' => __( 'Accepted', 'woo-lithuaniapost' ),
            'BP_TERMINAL_REQUEST_ACCEPTED' => __( 'LP express terminal request accepted', 'woo-lithuaniapost' ),
            'BP_TERMINAL_REQUEST_FAILED' => __( 'LP express terminal request failed', 'woo-lithuaniapost' ),
            'BP_TERMINAL_REQUEST_REJECTEDACCEPTED' => __( 'LP express terminal request rejected', 'woo-lithuaniapost' ),
            'BP_TERMINAL_REQUEST_SENT' => __( 'BP terminal request submitted', 'woo-lithuaniapost' ),
            'CANCELLED' => __( 'Canceled', 'woo-lithuaniapost' ),
            'DA_ACCEPTED_LP' => __( 'The parcel was taken from the Lithuanian Post Office', 'woo-lithuaniapost' ),
            'DA_ACCEPTED' => __( 'Parcel accepted from sender', 'woo-lithuaniapost' ),
            'DA_DELIVERED_LP' => __( 'The parcel was delivered to the Lithuanian Post Office', 'woo-lithuaniapost' ),
            'DA_DELIVERED' => __( 'The shipment has been delivered to the receiver', 'woo-lithuaniapost' ),
            'DA_DELIVERY_FAILED' => __( 'Delivery failed', 'woo-lithuaniapost' ),
            'DA_EXPORTED' => __( 'Item shipped from Lithuania', 'woo-lithuaniapost' ),
            'DA_PASSED_FOR_DELIVERY' => __( 'The parcel has been handed over to the courier for delivery', 'woo-lithuaniapost' ),
            'DA_RETURNED' => __( 'Shipment returned', 'woo-lithuaniapost' ),
            'DA_RETURNING' => __( 'The parcel is being returned', 'woo-lithuaniapost' ),
            'DEAD' => __( 'The itemt was delivered for destruction', 'woo-lithuaniapost' ),
            'DELIVERED' => __( 'Delivered', 'woo-lithuaniapost' ),
            'DEP_RECEIVED' => __( 'Item accepted at distribution center', 'woo-lithuaniapost' ),
            'DEP_SENT' => __( 'The item shall be transported to another distribution center', 'woo-lithuaniapost' ),
            'DESTROYED' => __( 'Destroyed', 'woo-lithuaniapost' ),
            'DISAPPEARED' => __( 'It\'s gone', 'woo-lithuaniapost' ),
            'EDA' => __( 'The shipment was detained at the distribution center of the recipient country', 'woo-lithuaniapost' ),
            'EDB' => __( 'The item has been presented to the customs authorities of the country of destination', 'woo-lithuaniapost' ),
            'EDC' => __( 'The consignment is subject to customs controls in the country of destination', 'woo-lithuaniapost' ),
            'EDD' => __( 'Consignment at the distribution center in the country of destination', 'woo-lithuaniapost' ),
            'EDE' => __( 'The shipment was sent from a distribution center in the recipient country', 'woo-lithuaniapost' ),
            'EDF' => __( 'The shipment is on hold in the recipient\'s post office', 'woo-lithuaniapost' ),
            'EDG' => __( 'The shipment has been delivered for delivery', 'woo-lithuaniapost' ),
            'EDH' => __( 'The shipment was delivered to the place of collection', 'woo-lithuaniapost' ),
            'EMA' => __( 'Consignment accepted from sender', 'woo-lithuaniapost' ),
            'EMB' => __( 'Consignment at distribution center', 'woo-lithuaniapost' ),
            'EMC' => __( 'Consignment shipped from Lithuania', 'woo-lithuaniapost' ),
            'EMD' => __( 'Consignment in the country of destination', 'woo-lithuaniapost' ),
            'EME' => __( 'Consignment at the customs office of destination', 'woo-lithuaniapost' ),
            'EMF' => __( 'The shipment was sent to the recipient\'s post office', 'woo-lithuaniapost' ),
            'EMG' => __( 'Parcel at the recipient\'s post office', 'woo-lithuaniapost' ),
            'EMH' => __( 'Attempt to deliver failed', 'woo-lithuaniapost' ),
            'EMI' => __( 'The shipment has been delivered to the consignee', 'woo-lithuaniapost' ),
            'EXA' => __( 'The consignment has been presented to the customs authorities of the country of departure', 'woo-lithuaniapost' ),
            'EXB' => __( 'The consignment was detained at the office of departure', 'woo-lithuaniapost' ),
            'EXC' => __( 'The consignment has been checked at the customs office of dispatch', 'woo-lithuaniapost' ),
            'EXD' => __( 'Thei tem is detained at the dispatch center of the country of dispatch', 'woo-lithuaniapost' ),
            'EXPORTED' => __( 'Exported', 'woo-lithuaniapost' ),
            'EXX' => __( 'The shipment has been canceled from the sender\'s country', 'woo-lithuaniapost' ),
            'FETCHCODE' => __( 'The shipment was delivered to the parcel self-service terminal', 'woo-lithuaniapost' ),
            'HANDED_IN_BKIS' => __( 'Served (BKIS)', 'woo-lithuaniapost' ),
            'HANDED_IN_POST' => __( 'Served at the post office', 'woo-lithuaniapost' ),
            'HANDED_TO_GOVERNMENT' => __( 'Transferred to the State', 'woo-lithuaniapost' ),
            'IMPLICATED' => __( 'Included', 'woo-lithuaniapost' ),
            'INFORMED' => __( 'Receipt message', 'woo-lithuaniapost' ),
            'LABEL_CANCELLED' => __( 'Delivery tag canceled', 'woo-lithuaniapost' ),
            'LABEL_CREATED' => __( 'Delivery tag created', 'woo-lithuaniapost' ),
            'LP_DELIVERY_FAILED' => __( 'Delivery failed', 'woo-lithuaniapost' ),
            'LP_RECEIVED' => __( 'The parcel was received at the Lithuanian Post Office', 'woo-lithuaniapost' ),
            'NOT_INCLUDED' => __( 'Not included', 'woo-lithuaniapost' ),
            'NOT_SET' => __( 'Unknown', 'woo-lithuaniapost' ),
            'ON_HOLD' => __( 'Detained', 'woo-lithuaniapost' ),
            'PARCEL_DELIVERED' => __( 'The shipment was delivered to the parcel self-service terminal', 'woo-lithuaniapost' ),
            'PARCEL_DEMAND' => __( 'Secure on demand', 'woo-lithuaniapost' ),
            'PARCEL_DETAINED' => __( 'Detained', 'woo-lithuaniapost' ),
            'PARCEL_DROPPED' => __( 'The shipment is placed in the parcel self-service terminal for shipment', 'woo-lithuaniapost' ),
            'PARCEL_LOST' => __( 'The shipment is gone', 'woo-lithuaniapost' ),
            'PARCEL_PICKED_UP_AT_LP' => __( 'The shipment has been delivered to the receiver', 'woo-lithuaniapost' ),
            'PARCEL_PICKED_UP_BY_DELIVERYAGENT' => __( 'The parcel is taken by courier from the parcel self-service terminal', 'woo-lithuaniapost' ),
            'PARCEL_PICKED_UP_BY_RECIPIENT' => __( 'The shipment has been withdrawn by the receiver', 'woo-lithuaniapost' ),
            'RECEIVED_FROM_ANY_POST' => __( 'Received', 'woo-lithuaniapost' ),
            'RECEIVED' => __( 'Received', 'woo-lithuaniapost' ),
            'REDIRECTED_AT_HOME' => __( 'Forwarded', 'woo-lithuaniapost' ),
            'REDIRECTED_IN_POST' => __( 'Forwarded in post office', 'woo-lithuaniapost' ),
            'REDIRECTED' => __( 'Forwarded-Served', 'woo-lithuaniapost' ),
            'REDIRECTING' => __( 'Forwarding started', 'woo-lithuaniapost' ),
            'REFUND_AT_HOME' => __( 'Refunded', 'woo-lithuaniapost' ),
            'REFUNDED_IN_POST' => __( 'Returned to post office', 'woo-lithuaniapost' ),
            'REFUNDED' => __( 'Refunded', 'woo-lithuaniapost' ),
            'REFUNDING' => __( 'Return started', 'woo-lithuaniapost' ),
            'SENT' => __( 'Sent', 'woo-lithuaniapost' ),
            'STORING' => __( 'Transferred to storage', 'woo-lithuaniapost' ),
            'TRANSFERRED_FOR_DELIVERY' => __( 'Passed on for deliver', 'woo-lithuaniapost' ),
            'UNDELIVERED' => __( 'Not delivered', 'woo-lithuaniapost' ),
            'UNSUCCESSFUL_DELIVERY' => __( 'Delivery failed' )
        ];

        return $state_code != null && key_exists ( $state_code, $state ) ? $state [ $state_code ] : null;
    }

    /**
     * Save tracking events from API
     *
     * @since 1.0.0
     */
    public function save_tracking_events ()
    {
        global $wpdb;

        $trackings = $wpdb->get_results ( "SELECT * FROM {$wpdb->woo_lithuaniapost_tracking_events}" );

        foreach ( $trackings as $tracking ) {
            // Update only every 8 hours
            if ( $tracking->updated == null || strtotime ( $tracking->updated ) < strtotime ( '-8 hours' ) ) {
                if ( $events = apply_filters ( 'woo_lithuaniapost_api_get_tracking', $tracking->barcode ) ) {
                    $wpdb->update ( $wpdb->woo_lithuaniapost_tracking_events, [
                        'state' => $events->state,
                        'events' => json_encode ( $events->events ),
                        'updated' => date ( 'Y-m-d H:i:s' )
                    ], [ 'barcode' => $tracking->barcode ] );
                }
            }
        }
    }

    /**
     * Get tracking events by order id
     *
     * @param $order_id
     * @return array|object|void|null
     */
    public function get_tracking_events ( $order_id )
    {
        global $wpdb;
        return $wpdb->get_row ( "SELECT * FROM {$wpdb->woo_lithuaniapost_tracking_events} WHERE order_id={$order_id}" );
    }
}
