<?php
/**
 * Settings for Lithuania Post - Post Office
 *
 * @link       https://post.lt
 * @since      1.0.0
 *
 * @package    Woo_Lithuaniapost
 * @subpackage Woo_Lithuaniapost/includes/shipping
 */

defined( 'ABSPATH' ) || exit;

return [
    'title'         => [
        'title'       => __( 'Method title', 'woocommerce' ),
        'type'        => 'text',
        'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
        'default'     => __( 'Lithuania Post - Delivery to home, office or post office', 'woo-lithuaniapost' ),
        'desc_tip'    => true,
    ],
    'fictional_size'          => [
        'title'         => __( 'Shipping size', 'woo-lithuaniapost' ),
        'type'          => 'select',
        'options'       => [
            'S' => 'S',
            'M' => 'M',
            'L' => 'L'
        ],
    ],
    'delivery_method'          => [
        'title'         => __( 'Shipping type', 'woo-lithuaniapost' ),
        'type'          => 'select',
        'options'       => [
            'SMALL_CORESPONDENCE' => __( 'Corespondence', 'woo-lithuaniapost' ),
            'BIG_CORESPONDENCE'   => __( 'Corespondence', 'woo-lithuaniapost' ),
            'PACKAGE'             => __( 'Package', 'woo-lithuaniapost' )
        ],
    ],
    'delivery_time' => [
        'title'         => __( 'Delivery time', 'woo-lithuaniapost' ),
        'type'          => 'text',
        'description'   => __( 'Example: 1 - 4 d.d.', 'woo-lithuaniapost' ),
        'default'       => null,
        'desc_tip'      => true,
    ],
    'cost'          => [
        'title'             => __( 'Cost', 'woocommerce' ),
        'type'              => 'text',
        'placeholder'       => '',
        'description'       => __( 'Leave empty if you wan\'t to use table rates.', 'woo-lithuaniapost' ),
        'default'           => '0',
        'desc_tip'          => true,
        'sanitize_callback' => [ $this, 'sanitize_cost' ],
    ],
];