<?php
/**
 * Settings for LPEXPRESS - Courier
 *
 * @link       https://post.lt
 * @since      1.0.0
 *
 * @package    Woo_Lithuaniapost
 * @subpackage Woo_Lithuaniapost/includes/shipping
 */

defined( 'ABSPATH' ) || exit;

return [
    'title'      => [
        'title'       => __( 'Method title', 'woocommerce' ),
        'type'        => 'text',
        'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
        'default'     => __( 'LP EXPRESS - Delivery to home, office by courier', 'woo-lithuaniapost' ),
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
    'delivery_time'         => [
        'title'         => __( 'Delivery time', 'woo-lithuaniapost' ),
        'type'          => 'text',
        'description'   => __( 'Example: 1 - 4 d.d.', 'woo-lithuaniapost' ),
        'default'       => null,
        'desc_tip'      => true,
    ],
    'cost'       => [
        'title'             => __( 'Cost', 'woocommerce' ),
        'type'              => 'text',
        'placeholder'       => '',
        'description'       => __( 'Leave empty if you wan\'t to use table rates.', 'woo-lithuaniapost' ),
        'default'           => '0',
        'desc_tip'          => true,
        'sanitize_callback' => [ $this, 'sanitize_cost' ],
    ],
    'free_shipping_cost' => [
        'title'             => __( 'Minimal order amount for free shipping', 'woo-lithuaniapost' ),
        'type'              => 'text',
        'default'           => null,
    ],
    'apply_free_shipping_before_discount' => [
        'title' => __( 'Coupon discount', 'woo-lithuaniapost' ),
        'label' => __( 'Apply minimal amount rule before coupon discount', 'woo-lithuaniapost' ),
        'type' => 'checkbox',
        'default' => 'no'
    ],
    'tax_status' => [
        'title' => __( 'Tax status', 'woocommerce' ),
        'type'          => 'select',
        'options'     => [
            'taxable'  => __( 'Taxable', 'woocommerce' ),
            'none'     => _x( 'None', 'Tax status', 'woocommerce' ),
        ],
    ]
];
