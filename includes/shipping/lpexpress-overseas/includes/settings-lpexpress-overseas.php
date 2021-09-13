<?php
/**
 * Settings for Lithuania Post - Overseas
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
        'default'     => __( 'LP EXPRESS - Delivery to overseas', 'woo-lithuaniapost' ),
        'desc_tip'    => true,
    ],
    'delivery_method'       => [
        'title'         => __( 'Shipping method', 'woo-lithuaniapost' ),
        'type'          => 'select',
        'options'       => [
            'CHCA' => 'CHCA',
            'EBIN' => 'EBIN',
        ],
    ],
    'size'          => [
        'title'         => __( 'Shipping size', 'woo-lithuaniapost' ),
        'type'          => 'select',
        'options'       => [
            'XSmall' => 'XS',
            'Small'  => 'S',
            'Medium' => 'M',
            'Large'  => 'L',
            'XLarge' => 'XL'
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
        'description'       => __( 'Leave empty if you wan\'t to use country rates.', 'woo-lithuaniapost' ),
        'default'           => '0',
        'desc_tip'          => true,
        'sanitize_callback' => [ $this, 'sanitize_cost' ],
    ],
];
