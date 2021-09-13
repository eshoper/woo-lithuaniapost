<?php
/**
 * Provide a admin area meta box view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://post.lt
 * @since      1.0.0
 *
 * @package    Woo_Lithuaniapost
 * @subpackage Woo_Lithuaniapost/admin/partials/includes
 */

/**
 * @var Woo_Lithuaniapost_Admin_Order_Meta_Box $this
 */

$template = apply_filters (
    'woo_lithuaniapost_shipping_template',
    $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ),
    $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_size' )
);
?>
<div id="lpshipping-shipment-modal">
    <input type="hidden" name="order_id" value="<?php echo $this->get_order ()->get_id (); ?>" />
    <div class="lpshipping-shipment-nav" style="display: flex; border-bottom: 1px solid #e4e4e4;">
        <?php if ( $this->get_shipping_method () != 'woo_lithuaniapost_lpexpress_postoffice' ): ?>
            <div class="step active" data-tab="1" style="padding: 20px; padding-bottom: 0px;
                border-right: 1px solid #e4e4e4; padding-top: 5px;">
                <h3><?php _e( 'Parcel Information', 'woo-lithuaniapost' ); ?></h3>
            </div>
        <?php endif; ?>
        <div class="step <?php echo $this->get_shipping_method () == 'woo_lithuaniapost_lpexpress_postoffice' ? 'active' : ''; ?>" data-tab="2" style="padding: 20px; padding-bottom: 0px;
            border-right: 1px solid #e4e4e4; padding-top: 5px;">
            <h3><?php _e( 'Sender Information', 'woo-lithuaniapost' ); ?></h3>
        </div>
        <?php if ( apply_filters ( 'woo_lithuaniapost_shipping_template_is_cn22', $this->get_order (), $template [ 'id' ] ) ): ?>
            <div class="step" data-tab="3" style="padding: 20px; padding-bottom: 0px;
                border-right: 1px solid #e4e4e4; padding-top: 5px;">
                <h3><?php _e( 'CN22 Declaration', 'woo-lithuaniapost' ); ?></h3>
            </div>
        <?php endif; ?>
        <?php if ( apply_filters ( 'woo_lithuaniapost_shipping_template_is_cn23', $this->get_order (), $template [ 'id' ] ) ): ?>
            <div class="step" data-tab="4" style="padding: 20px; padding-bottom: 0px;
                border-right: 1px solid #e4e4e4; padding-top: 5px;">
                <h3><?php _e( 'CN23 Declaration', 'woo-lithuaniapost' ); ?></h3>
            </div>
        <?php endif; ?>
    </div>
    <div class="tab <?php echo $this->get_shipping_method () != 'woo_lithuaniapost_lpexpress_postoffice' ? 'tab-active' : ''; ?>" data-tab="1">
        <?php if ( $this->get_shipping_method () != 'woo_lithuaniapost_lp_postoffice'
            && $this->get_shipping_method () != 'woo_lithuaniapost_lp_overseas' ): // LPEXPRESS ?>
            <div style="border-bottom: 1px solid #e4e4e4; margin-top: 20px; padding-bottom: 20px">
                <span><h3><?php _e( 'Ship From:', 'woo-lithuaniapost' ); ?></h3></span>
                <ul class="lpshipping-list-shipfrom">
                    <li class="lpshipping-list-shipfrom-item">
                        <label class="lpshipping-list-shipfrom-item-label <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'HC' || $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'EBIN' ? 'active' : ''; ?>">
                            <img src="<?php echo plugin_dir_url ( __FILE__ ) . 'assets/images/portable-post.svg'; ?>" alt="">
                            <input type="radio" name="shipping_type" value="<?php echo $this->get_shipping_method () != 'woo_lithuaniapost_lpexpress_terminal' ? 'EBIN' : 'HC'; ?>"
                                <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'HC' || $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'EBIN' ? 'checked' : ''; ?> />
                            <span><?php _e( 'LP EXPRESS Courier', 'woo-lithuaniapost' ); ?></span>
                        </label>
                    </li>
                    <li class="lpshipping-list-shipfrom-item">
                        <label class="lpshipping-list-shipfrom-item-label <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'CC' || $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'CHCA' ? 'active' : ''; ?>">
                            <img src="<?php echo plugin_dir_url ( __FILE__ ) . 'assets/images/parcel-terminal.svg'; ?>" alt="">
                            <input type="radio" name="shipping_type" value="<?php echo $this->get_shipping_method () != 'woo_lithuaniapost_lpexpress_terminal' ? 'CHCA' : 'CC'; ?>"
                                <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'CC' || $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'CHCA' ? 'checked' : ''; ?> />
                            <span><?php _e( 'LP EXPRESS Terminal', 'woo-lithuaniapost' ); ?></span>
                        </label>
                    </li>
                </ul>
            </div>
            <div style="display: flex; margin-top: 20px">
                <div>
                    <span><h3><?php _e( 'The Item Being Sent', 'woo-lithuaniapost' ); ?></h3></span>
                    <ul class="lpshipping-list-sizes <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'EBIN' ? 'disabled' : ''; ?>">
                        <li class="lpshipping-list-sizes-item">
                            <label class="lpshipping-list-sizes-item-size <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_size' ) == 'XSmall' ? 'active' : ''; ?>">
                                <img src="<?php echo plugin_dir_url ( __FILE__ ) . 'assets/images/box-type-1.svg'; ?>" alt="">
                                <input type="radio" name="shipping_size" value="XSmall" <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_size' ) == 'XSmall' ? 'checked' : ''; ?> />
                                <span>XS - <?php _e( 'to', 'woo-lithuaniapost' ); ?> 30 kg</span>
                                <span class="color-gray">185/610/80 mm</span>
                            </label>
                        </li>
                        <li class="lpshipping-list-sizes-item">
                            <label class="lpshipping-list-sizes-item-size <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_size' ) == 'Small' ? 'active' : ''; ?>">
                                <img src="<?php echo plugin_dir_url ( __FILE__ ) . 'assets/images/box-type-2.svg'; ?>" alt="">
                                <input type="radio" name="shipping_size" value="Small" <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_size' ) == 'Small' ? 'checked' : ''; ?> />
                                <span>S - <?php _e( 'to', 'woo-lithuaniapost' ); ?> 30 kg</span>
                                <span class="color-gray">350/610/80 mm</span>
                            </label>
                        </li>
                        <li class="lpshipping-list-sizes-item">
                            <label class="lpshipping-list-sizes-item-size <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_size' ) == 'Medium' ? 'active' : ''; ?>">
                                <img src="<?php echo plugin_dir_url ( __FILE__ ) . 'assets/images/box-type-3.svg'; ?>" alt="">
                                <input type="radio" name="shipping_size" value="Medium" <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_size' ) == 'Medium' ? 'checked' : ''; ?> />
                                <span>M - <?php _e( 'to', 'woo-lithuaniapost' ); ?> 30 kg</span>
                                <span class="color-gray">350/610/175 mm</span>
                            </label>
                        </li>
                        <li class="lpshipping-list-sizes-item">
                            <label class="lpshipping-list-sizes-item-size <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_size' ) == 'Large' ? 'active' : ''; ?>">
                                <img src="<?php echo plugin_dir_url ( __FILE__ ) . 'assets/images/box-type-4.svg'; ?>" alt="">
                                <input type="radio" name="shipping_size" value="Large" <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_size' ) == 'Large' ? 'checked' : ''; ?> />
                                <span>L - <?php _e( 'to', 'woo-lithuaniapost' ); ?> 30 kg</span>
                                <span class="color-gray">350/610/365 mm</span>
                            </label>
                        </li>
                        <li class="lpshipping-list-sizes-item">
                            <label class="lpshipping-list-sizes-item-size <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_size' ) == 'XLarge' ? 'active' : ''; ?>">
                                <img src="<?php echo plugin_dir_url ( __FILE__ ) . 'assets/images/box-type-5.svg'; ?>" alt="">
                                <input type="radio" name="shipping_size" value="XLarge" <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_size' ) == 'XLarge' ? 'checked' : ''; ?> />
                                <span>XL - <?php _e( 'to', 'woo-lithuaniapost' ); ?> 30 kg</span>
                                <span class="color-gray">350/610/745 mm</span>
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
        <?php else: // LP ?>
            <?php if ( $this->get_shipping_method () == 'woo_lithuaniapost_lp_postoffice' ): ?>
                <div style="display: flex; margin-top: 20px">
                    <div style="width: 75%">
                        <span><h3><?php _e( 'The Item Being Sent', 'woo-lithuaniapost' ); ?></h3></span>
                        <ul class="lpshipping-list-sizes">
                            <li class="lpshipping-list-sizes-item">
                                <label class="lpshipping-list-sizes-item-size <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'SMALL_CORESPONDENCE' ? 'active' : ''; ?>">
                                    <img src="<?php echo plugin_dir_url ( __FILE__ ) . 'assets/images/box-type-3.svg'; ?>" alt="">
                                    <input type="radio" name="shipping_type" value="SMALL_CORESPONDENCE" />
                                    <span>S - <?php _e( 'to', 'woo-lithuaniapost' ); ?> 0.5 kg</span>
                                    <span class="color-gray">20/381/305 mm</span>
                                </label>
                            </li>
                            <li class="lpshipping-list-sizes-item">
                                <label class="lpshipping-list-sizes-item-size <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'BIG_CORESPONDENCE' ? 'active' : ''; ?>">
                                    <img src="<?php echo plugin_dir_url ( __FILE__ ) . 'assets/images/box-type-3.svg'; ?>" alt="">
                                    <input type="radio" name="shipping_type" value="BIG_CORESPONDENCE"
                                        <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'BIG_CORESPONDENCE' ? 'checked' : ''; ?> />
                                    <span>M - <?php _e( 'to', 'woo-lithuaniapost' ); ?> 2 kg</span>
                                    <span class="color-gray">600/600/600 mm</span>
                                </label>
                            </li>
                            <li class="lpshipping-list-sizes-item">
                                <label class="lpshipping-list-sizes-item-size <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'PACKAGE' ? 'active' : ''; ?>">
                                    <img src="<?php echo plugin_dir_url ( __FILE__ ) . 'assets/images/box-type-3.svg'; ?>" alt="">
                                    <input type="radio" name="shipping_type" value="PACKAGE"
                                        <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'PACKAGE' ? 'checked' : ''; ?> />
                                    <span>L - <?php _e( 'to', 'woo-lithuaniapost' ); ?> 30 kg</span>
                                    <span class="color-gray">2/1050/1050 mm</span>
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ( $this->get_shipping_method () == 'woo_lithuaniapost_lp_overseas' ): // LP ?>
                <div style="display: flex; margin-top: 20px">
                    <div style="width: 75%">
                        <span><h3><?php _e( 'The Item Being Sent', 'woo-lithuaniapost' ); ?></h3></span>
                        <ul class="lpshipping-list-sizes">
                            <li class="lpshipping-list-sizes-item">
                                <label class="lpshipping-list-sizes-item-size <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'SMALL_CORESPONDENCE' ? 'active' : ''; ?>">
                                    <img src="<?php echo plugin_dir_url ( __FILE__ ) . 'assets/images/box-type-3.svg'; ?>" alt="">
                                    <input type="radio" name="shipping_type" value="SMALL_CORESPONDENCE" />
                                    <span>S - <?php _e( 'to', 'woo-lithuaniapost' ); ?>  0.5 kg</span>
                                    <span class="color-gray">20/381/305 mm</span>
                                </label>
                            </li>
                            <li class="lpshipping-list-sizes-item">
                                <label class="lpshipping-list-sizes-item-size <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'BIG_CORESPONDENCE' ? 'active' : ''; ?>">
                                    <img src="<?php echo plugin_dir_url ( __FILE__ ) . 'assets/images/box-type-3.svg'; ?>" alt="">
                                    <input type="radio" name="shipping_type" value="BIG_CORESPONDENCE"
                                        <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'BIG_CORESPONDENCE' ? 'checked' : ''; ?> />
                                    <span>M - <?php _e( 'to', 'woo-lithuaniapost' ); ?>  2 kg</span>
                                    <span class="color-gray">600/600/600 mm</span>
                                </label>
                            </li>
                            <li class="lpshipping-list-sizes-item">
                                <label class="lpshipping-list-sizes-item-size <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'PACKAGE' ? 'active' : ''; ?>">
                                    <img src="<?php echo plugin_dir_url ( __FILE__ ) . 'assets/images/box-type-3.svg'; ?>" alt="">
                                    <input type="radio" name="shipping_type" value="PACKAGE"
                                        <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'PACKAGE' ? 'checked' : ''; ?> />
                                    <span>L - <?php _e( 'to', 'woo-lithuaniapost' ); ?>  30 kg</span>
                                    <span class="color-gray">2/1050/1050 mm</span>
                                </label>
                            </li>
                            <li class="lpshipping-list-sizes-item">
                                <label class="lpshipping-list-sizes-item-size <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'SMALL_CORESPONDENCE_TRACKED' ? 'active' : ''; ?>">
                                    <img src="<?php echo plugin_dir_url ( __FILE__ ) . 'assets/images/box-type-3.svg'; ?>" alt="">
                                    <input type="radio" name="shipping_type" value="SMALL_CORESPONDENCE_TRACKED"
                                        <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'SMALL_CORESPONDENCE_TRACKED' ? 'checked' : ''; ?> />
                                    <span>S_TRACKED</span>
                                    <span class="color-gray">To 500 g</span>
                                </label>
                            </li>
                            <li class="lpshipping-list-sizes-item">
                                <label class="lpshipping-list-sizes-item-size <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'MEDIUM_CORESPONDENCE_TRACKED' ? 'active' : ''; ?>">
                                    <img src="<?php echo plugin_dir_url ( __FILE__ ) . 'assets/images/box-type-3.svg'; ?>" alt="">
                                    <input type="radio" name="shipping_type" value="MEDIUM_CORESPONDENCE_TRACKED"
                                        <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_method' ) == 'MEDIUM_CORESPONDENCE_TRACKED' ? 'checked' : ''; ?> />
                                    <span>M_TRACKED</span>
                                    <span class="color-gray">To 2 kg</span>
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ( $this->get_shipping_method () == 'woo_lithuaniapost_lpexpress_terminal' ): ?>
            <div class="additional-options">
                <div style="width: 75%" style="margin-left: 10px">
                    <span><h3><?php _e( 'Ship To:', 'woo-lithuaniapost' ); ?></h3></span>
                    <div class="admin__field-control">
                        <b><?php _e( 'Terminal:', 'woo-lithuaniapost' ); ?></b> <select name="terminal_id" class="admin__control-select">
                            <?php foreach ( array_reverse ( $this->get_terminal_list () ) as $city => $terminals ): ?>
                                <optgroup label="<?php echo $city; ?>">
                                    <?php foreach ( $terminals as $terminal_id => $terminal ): ?>
                                        <option value="<?php echo $terminal_id; ?>" <?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_lpexpress_terminal_id' ) == $terminal_id ? 'selected' : ''; ?>>
                                            <?php echo $terminal; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="additional-options">
            <div class="admin__field">
                <?php if ( $this->get_order ()->get_payment_method () === 'cod' ): ?>
                    <?php foreach ( json_decode ( $this->get_order ()->get_meta ( '_woo_lithuaniapost_additional' ) ) as $service ): ?>
                        <?php if ( $service->id == 8 ): ?>
                            <?php $cod = number_format ( $service->amount, 2 ); ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <label style="margin-left: 10px" class="admin__field-label">COD (&euro;):
                        <input type="number" step="0.01"
                               value="<?php echo $cod ?? $this->get_order ()->get_total (); ?>"
                               min="0.00" class="admin__control-text" name="cod" />
                    </label>
                <?php endif; ?>
                <?php if ( !in_array ( $template [ 'id' ], [ 42, 43, 44 ] )
                        && !in_array ( $template [ 'type' ], [ 'MEDIUM_CORESPONDENCE_TRACKED', 'SMALL_CORESPONDENCE_TRACKED' ] ) ): ?>
                    <label style="margin-left: 10px" class="admin__field-label"><?php _e( 'Parts:', 'woo-lithuaniapost' ); ?>
                        <input type="number" step="1"
                               value="<?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_parts' ) ?? 1; ?>"
                               min="1" class="admin__control-text" name="parts" required />
                    </label>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Sender info -->
    <?php $sender = json_decode ( $this->get_order ()->get_meta ( '_woo_lithuaniapost_sender_info' ) ); ?>
    <div class="tab <?php echo $this->get_shipping_method () == 'woo_lithuaniapost_lpexpress_postoffice' ? 'tab-active' : ''; ?>" data-tab="2" style="margin-top: 20px; font-size: 14px;">
        <h3><?php _e( 'Sender Information', 'woo-lithuaniapost' ); ?></h3>
        <div class="admin__field">
            <label for="sender_name" class="admin__field-label">
                <?php _e('Name', 'woo-lithuaniapost'); ?> <span style="color:red">*</span>
            </label>
            <div class="admin__field-control">
                <input type="hidden" name="sender[name]" value="<?php echo ' '; ?>" />
                <input name="sender[companyName]"
                       id="sender_name" type="text" class="admin__control-text widefat"
                       value="<?php echo htmlspecialchars ( stripslashes ( $sender->companyName ) ); ?>"
                       required />
            </div>
        </div>
        <div class="admin__field">
            <label for="sender_phone" class="admin__field-label">
                <?php _e('Phone', 'woo-lithuaniapost'); ?> <span style="color:red">*</span>
            </label>
            <div class="admin__field-control">
                <input name="sender[phone]"
                       id="sender_phone" type="text" class="admin__control-text widefat"
                       value="<?php echo $sender->phone; ?>"
                       required />
            </div>
        </div>
        <div class="admin__field">
            <label for="sender_email" class="admin__field-label">
                <?php _e('Email', 'woo-lithuaniapost'); ?> <span style="color:red">*</span>
            </label>
            <div class="admin__field-control">
                <input type="email" name="sender[email]"
                       id="sender_email" class="admin__control-text widefat"
                       value="<?php echo $sender->email; ?>"
                       required />
            </div>
        </div>
        <div class="admin__field">
            <label for="sender_country" class="admin__field-label">
                <?php _e('Country', 'woo-lithuaniapost'); ?> <span style="color:red">*</span>
            </label>
            <div class="admin__field-control">
                <select name="sender[address][country]" id="sender_country" class="admin__control-select widefat">
                    <?php foreach ( Woo_Lithuaniapost_Admin_Settings::get_country_list ( true ) as $code => $country ): ?>
                        <option value="<?php echo $code; ?>" <?php echo $code == $sender->address->country ? 'selected' : ''; ?>>
                            <?php echo $country; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="admin__field">
            <label for="sender_city" class="admin__field-label">
                <?php _e('City', 'woo-lithuaniapost'); ?> <span style="color:red">*</span>
            </label>
            <div class="admin__field-control">
                <input type="text" name="sender[address][locality]"
                       value="<?php echo $sender->address->locality ?>"
                       id="sender_city" class="admin__control-text widefat"
                       required />
            </div>
        </div>
        <div class="admin__field">
            <label for="sender_street" class="admin__field-label">
                <?php _e('Street', 'woo-lithuaniapost'); ?> <span style="color:red">*</span>
            </label>
            <div class="admin__field-control">
                <input type="text" name="sender[address][street]"
                       value="<?php echo $sender->address->street ?>"
                       id="sender_street" class="admin__control-text widefat"
                       required />
            </div>
        </div>
        <div class="admin__field">
            <label for="sender_building" class="admin__field-label">
                <?php _e('Building Number', 'woo-lithuaniapost'); ?> <span style="color:red">*</span>
            </label>
            <div class="admin__field-control">
                <input type="text" name="sender[address][building]"
                       value="<?php echo $sender->address->building ?>"
                       id="sender_building" class="admin__control-text widefat"
                       required />
            </div>
        </div>
        <div class="admin__field">
            <label for="sender_apartment" class="admin__field-label">
                <?php _e('Apartment', 'woo-lithuaniapost'); ?> <span style="color:red">*</span>
            </label>
            <div class="admin__field-control">
                <input type="text" name="sender[address][apartment]"
                       value="<?php echo $sender->address->apartment ?>"
                       id="sender_apartment" class="admin__control-text widefat"
                       required />
            </div>
        </div>
        <div class="admin__field">
            <label for="sender_postcode" class="admin__field-label">
                <?php _e('Post Code', 'woo-lithuaniapost'); ?> <span style="color:red">*</span>
            </label>
            <div class="admin__field-control">
                <input type="text" name="sender[address][postalCode]"
                       value="<?php echo $sender->address->postalCode; ?>"
                       id="sender_postcode" class="admin__control-text widefat"
                       required />
            </div>
        </div>
    </div>
    <?php if ( apply_filters ( 'woo_lithuaniapost_shipping_template_is_cn22', $this->get_order (), $template [ 'id' ] ) ): ?>
        <?php $cn_data = json_decode ( $this->get_order ()->get_meta ( '_woo_lithuaniapost_cn_data' ) ); ?>
        <div class="tab" data-tab="3" style="margin-top: 20px;">
            <h3><?php _e('CN22 Declaration', 'woo-lithuaniapost'); ?></h3>
            <div class="admin__field">
                <label for="parcel_type" class="admin__field-label">
                    <?php _e( 'Parcel Type', 'woo-lithuaniapost'); ?> <span style="color:red">*</span>
                </label>
                <div class="admin__field-control">
                    <select name="cn22Form[parcelType]" id="parcel_type" class="admin__control-select widefat">
                        <option value="Sell"><?php _e( 'Sell', 'woo-lithuaniapost'); ?></option>
                        <option value="Gift" <?php echo $cn_data->cn22Form->parcelType == 'Gift' ? 'selected' : ''; ?>><?php _e( 'Gift', 'woo-lithuaniapost'); ?></option>
                        <option value="Document" <?php echo $cn_data->cn22Form->parcelType == 'Document' ? 'selected' : ''; ?>><?php _e( 'Document', 'woo-lithuaniapost'); ?></option>
                        <option value="Sample" <?php echo $cn_data->cn22Form->parcelType == 'Sample' ? 'selected' : ''; ?>><?php _e( 'Sample', 'woo-lithuaniapost'); ?></option>
                        <option value="Return" <?php echo $cn_data->cn22Form->parcelType == 'Return' ? 'selected' : ''; ?>><?php _e( 'Return', 'woo-lithuaniapost'); ?></option>
                        <option value="Other" <?php echo $cn_data->cn22Form->parcelType == 'Other' ? 'selected' : ''; ?>><?php _e( 'Other', 'woo-lithuaniapost'); ?></option>
                    </select>
                </div>
            </div>
            <div class="admin__field">
                <label for="parcel_type_notes" class="admin__field-label">
                    <?php _e('Parcel Type Notes', 'woo-lithuaniapost'); ?> <span style="color:red">*</span>
                </label>
                <div class="admin__field-control">
                    <input name="cn22Form[parcelTypeNotes]"
                           value="<?php echo $cn_data->cn22Form->parcelTypeNotes ?: 'Sell Items'; ?>"
                           id="parcel_type_notes" class="admin__control-text widefat"
                           required />
                </div>
            </div>
            <div class="admin__field">
                <label for="parcel_description" class="admin__field-label">
                    <?php _e('Parcel Description', 'woo-lithuaniapost'); ?> <span style="color:red">*</span>
                </label>
                <div class="admin__field-control">
            <textarea name="cn22Form[parcelDescription]" id="parcel_description"
                      class="admin__control-textarea widefat"
                      required><?php echo $cn_data->cn22Form->parcelDescription ?: 'Sell'; ?></textarea>
                </div>
            </div>
            <section class="admin__page-section">
                <div class="admin__page-section-title">
                    <span class="title"><?php _e( 'Parcel Items', 'woo-lithuaniapost' ) ?></span>
                </div>
                <div class="admin__table-wrapper">
                    <table class="data-table admin__table-primary edit-order-table">
                        <thead>
                        <tr class="headings">
                            <th class="col-summary">
                        <span>
                            <?php _e( 'Summary', 'woo-lithuaniapost' ); ?> <span style="color:red">*</span>
                        </span>
                            </th>
                            <th class="col-amount">
                        <span>
                            <?php _e( 'Amount', 'woo-lithuaniapost' ); ?> <span style="color:red">*</span>
                        </span>
                            </th>
                            <th class="col-country">
                        <span>
                            <?php _e( 'Country of Origin', 'woo-lithuaniapost' ); ?> <span style="color:red">*</span>
                        </span>
                            </th>
                            <th class="col-currency">
                        <span>
                            <?php _e( 'Currency', 'woo-lithuaniapost' ); ?> <span style="color:red">*</span>
                        </span>
                            </th>
                            <th class="col-hs-code">
                                <span><?php _e( 'HS Item Number', 'woo-lithuaniapost' ); ?></span>
                            </th>
                            <th class="col-weight">
                        <span>
                            <?php _e( 'Weight', 'woo-lithuaniapost' ); ?> (g) <span style="color:red">*</span>
                        </span>
                            </th>
                            <th class="col-quantity">
                        <span>
                            <?php _e( 'Quantity', 'woo-lithuaniapost' ); ?> <span style="color:red">*</span>
                        </span>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="even">
                        <?php /* @var \Magento\Catalog\Model\Product $item */ ?>
                        <?php $counter = -1; foreach ( $cn_data->cn22Form->cnParts as $item ): $counter++; ?>
                            <tr>
                                <td class="col-summary">
                                    <div class="admin__field-control">
                                        <input name="cn22Form[cnParts][<?php echo $counter; ?>][summary]" type="text" id="summary"
                                               class="admin__control-text" value="<?php echo $item->summary; ?>"
                                               required />
                                    </div>
                                </td>
                                <td class="col-amount">
                                    <div class="admin__field-control">
                                        <input name="cn22Form[cnParts][<?php echo $counter; ?>][amount]" type="number"
                                               value="<?php echo number_format ( $item->amount, 2 ); ?>"
                                               step="0.01" id="amount" class="admin__control-text"
                                               required />
                                    </div>
                                </td>
                                <td class="col-country" style="max-width: 155px">
                                    <select name="cn22Form[cnParts][<?php echo $counter; ?>][countryCode]" id="countryCode" class="admin__control-select">
                                        <?php foreach ( Woo_Lithuaniapost_Admin_Settings::get_country_list ( true ) as $code => $country ): ?>
                                            <option value="<?php echo $code; ?>" <?php echo $code == $item->countryCode ? 'selected' : ''; ?>>
                                                <?php echo $country; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="col-currency">
                                    <select name="cn22Form[cnParts][<?php echo $counter; ?>][currencyCode]" id="currencyCode" class="admin__control-select">
                                        <option value="EUR">EUR</option>
                                        <option value="USD" <?php echo $item->currencyCode
                                        == 'USD' ? 'selected' : ''; ?>>USD</option>
                                    </select>
                                </td>
                                <td class="col-hs-code">
                                    <div class="admin__field-control">
                                        <input name="cn22Form[cnParts][<?php echo $counter; ?>][hsCode]" id="hscode"
                                               value="<?php echo $item->hsCode; ?>" class="admin__control-text" />
                                    </div>
                                </td>
                                <td class="col-weight">
                                    <div class="admin__field-control">
                                        <input name="cn22Form[cnParts][<?php echo $counter; ?>][weight]" type="number" value="<?php echo $item->weight; ?>"
                                               step="0.01" id="weight" class="admin__control-text"
                                               required />
                                    </div>
                                </td>
                                <td class="col-quantity">
                                    <div class="admin__field-control">
                                        <input name="cn22Form[cnParts][<?php echo $counter; ?>][quantity]" type="number"
                                               value="<?php echo intval ( $item->quantity ); ?>"
                                               step="1" id="quantity" class="admin__control-text"
                                               required>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    <?php endif; ?>
    <?php if ( apply_filters ( 'woo_lithuaniapost_shipping_template_is_cn23', $this->get_order (), $template [ 'id' ] ) ): ?>
        <?php $cn_data = json_decode ( $this->get_order ()->get_meta ( '_woo_lithuaniapost_cn_data' ) ); ?>
        <div class="tab" data-tab="4" style="margin-top: 20px">
            <h3><?php _e( 'CN23 Declaration', 'woo-lithuaniapost' ); ?></h3>
            <div class="admin__field">
                <label for="parcel_type" class="admin__field-label">
                    <?php _e( 'Parcel Type', 'woo-lithuaniapost' ); ?> <span style="color:red">*</span>
                </label>
                <div class="admin__field-control">
                    <select name="cn23Form[parcelType]" id="parcel_type" class="admin__control-select widefat">
                        <option value="Sell"><?php _e( 'Sell', 'woo-lithuaniapost'); ?></option>
                        <option value="Gift" <?php echo $cn_data->cn23Form->parcelType == 'Gift' ? 'selected' : ''; ?>><?php _e( 'Gift', 'woo-lithuaniapost'); ?></option>
                        <option value="Document" <?php echo $cn_data->cn23Form->parcelType == 'Document' ? 'selected' : ''; ?>><?php _e( 'Document', 'woo-lithuaniapost'); ?></option>
                        <option value="Sample" <?php echo $cn_data->cn23Form->parcelType == 'Sample' ? 'selected' : ''; ?>><?php _e( 'Sample', 'woo-lithuaniapost'); ?></option>
                        <option value="Return" <?php echo $cn_data->cn23Form->parcelType == 'Return' ? 'selected' : ''; ?>><?php _e( 'Return', 'woo-lithuaniapost'); ?></option>
                        <option value="Other" <?php echo $cn_data->cn23Form->parcelType == 'Other' ? 'selected' : ''; ?>><?php _e( 'Other', 'woo-lithuaniapost'); ?></option>
                    </select>
                </div>
            </div>
            <div class="admin__field">
                <label for="parcel_type_notes" class="admin__field-label">
                    <?php _e('Parcel Type Notes', 'woo-lithuaniapost'); ?>
                </label>
                <div class="admin__field-control">
                    <input name="cn23Form[parcelTypeNotes]"
                           value="<?php echo $cn_data->cn23Form->parcelTypeNotes ?: 'Sell Items'; ?>"
                           id="parcel_type_notes" type="text" class="admin__control-text widefat" />
                </div>
            </div>
            <div class="admin__field">
                <label for="exporter_customs_code" class="admin__field-label">
                    <?php _e('Exporter Customs Code', 'woo-lithuaniapost'); ?>
                </label>
                <div class="admin__field-control">
                    <input name="cn23Form[exporterCustomsCode]"
                           value="<?php echo $cn_data->cn23Form->exporterCustomsCode; ?>"
                           id="exporter_customs_code" type="text" class="admin__control-text widefat" />
                </div>
            </div>
            <div class="admin__field">
                <label for="license" class="admin__field-label">
                    <?php _e('License', 'woo-lithuaniapost'); ?>
                </label>
                <div class="admin__field-control">
                    <input name="cn23Form[license]"
                           value="<?php echo $cn_data->cn23Form->license; ?>"
                           id="license" type="text" class="admin__control-text widefat" />
                </div>
            </div>
            <div class="admin__field">
                <label for="certificate" class="admin__field-label">
                    <?php _e('Certificate', 'woo-lithuaniapost'); ?>
                </label>
                <div class="admin__field-control">
                    <input name="cn23Form[certificate]"
                           value="<?php echo $cn_data->cn23Form->certificate; ?>"
                           id="certificate" type="text" class="admin__control-text widefat" />
                </div>
            </div>
            <div class="admin__field">
                <label for="invoice" class="admin__field-label">
                    <?php _e('Invoice', 'woo-lithuaniapost'); ?>
                </label>
                <div class="admin__field-control">
                    <input name="cn23Form[invoice]"
                           value="<?php echo $cn_data->cn23Form->invoice; ?>"
                           id="invoice" type="text" class="admin__control-text widefat" />
                </div>
            </div>
            <div class="admin__field">
                <label for="invoice" class="admin__field-label">
                    <?php _e('Notes', 'woo-lithuaniapost'); ?>
                </label>
                <div class="admin__field-control">
            <textarea name="cn23Form[notes]" id="parcel_description"
                      class="admin__control-textarea widefat"><?php echo $cn_data->cn23Form->notes; ?></textarea>
                </div>
            </div>
            <div class="admin__field">
                <label for="faiilure_instructions" class="admin__field-label">
                    <?php _e('Failure Instruction', 'woo-lithuaniapost'); ?>
                </label>
                <div class="admin__field-control">
                    <select name="cn23Form[failureInstruction]" id="parcel_type" class="admin__control-select widefat">
                        <option value="RETURN_TO_SENDER_NON_PRIORITY"
                            <?php echo $cn_data->cn23Form->failureInstruction == 'RETURN_TO_SENDER_NON_PRIORITY' ? 'selected' : ''; ?>
                        >RETURN_TO_SENDER_NON_PRIORITY</option>
                        <option value="RETURN_TO_SENDER_PRIORITY"
                            <?php echo $cn_data->cn23Form->failureInstruction == 'RETURN_TO_SENDER_PRIORITY' ? 'selected' : ''; ?>
                        >RETURN_TO_SENDER_PRIORITY</option>
                        <option value="TREAT_AS_ABANDONED"
                            <?php echo $cn_data->cn23Form->failureInstruction == 'TREAT_AS_ABANDONED' ? 'selected' : ''; ?>
                        >TREAT_AS_ABANDONED</option>
                    </select>
                </div>
            </div>
            <div class="admin__field">
                <label for="importer_code" class="admin__field-label">
                    <?php _e('Importer Code', 'woo-lithuaniapost'); ?>
                </label>
                <div class="admin__field-control">
                    <input name="cn23Form[importerCode]"
                           value="<?php echo $cn_data->cn23Form->importerCode; ?>"
                           id="importer_code" type="text" class="admin__control-text widefat" />
                </div>
            </div>
            <div class="admin__field">
                <label for="importer_customs_code" class="admin__field-label">
                    <?php _e('Importer Customs Code', 'woo-lithuaniapost'); ?>
                </label>
                <div class="admin__field-control">
                    <input name="cn23Form[importerCustomsCode]"
                           value="<?php echo $cn_data->cn23Form->importerCustomsCode; ?>"
                           id="importer_customs_code" type="text" class="admin__control-text widefat" />
                </div>
            </div>
            <div class="admin__field">
                <label for="importer_email" class="admin__field-label">
                    <?php _e('Importer Email', 'woo-lithuaniapost'); ?>
                </label>
                <div class="admin__field-control">
                    <input name="cn23Form[importerEmail]"
                           value="<?php echo $cn_data->cn23Form->importerEmail; ?>"
                           id="importer_email" type="text" class="admin__control-text widefat" />
                </div>
            </div>
            <div class="admin__field">
                <label for="importer_fax" class="admin__field-label">
                    <?php _e('Importer Fax', 'woo-lithuaniapost'); ?>
                </label>
                <div class="admin__field-control">
                    <input name="cn23Form[importerFax]"
                           value="<?php echo $cn_data->cn23Form->importerFax; ?>"
                           id="importer_fax" type="text" class="admin__control-text widefat" />
                </div>
            </div>
            <div class="admin__field">
                <label for="importer_phone" class="admin__field-label">
                    <?php _e('Importer Phone', 'woo-lithuaniapost'); ?>
                </label>
                <div class="admin__field-control">
                    <input name="cn23Form[importerPhone]"
                           value="<?php echo $cn_data->cn23Form->importerPhone; ?>"
                           id="importer_phone" type="text" class="admin__control-text widefat" />
                </div>
            </div>
            <div class="admin__field">
                <label for="importer_tax_code" class="admin__field-label">
                    <?php _e('Importer Tax Code', 'woo-lithuaniapost'); ?>
                </label>
                <div class="admin__field-control">
                    <input name="cn23Form[importerTaxCode]"
                           value="<?php echo $cn_data->cn23Form->importerTaxCode; ?>"
                           id="importer_tax_code" type="text" class="admin__control-text widefat" />
                </div>
            </div>
            <div class="admin__field">
                <label for="importer_vat_code" class="admin__field-label">
                    <?php _e('Importer Vat Code', 'woo-lithuaniapost'); ?>
                </label>
                <div class="admin__field-control">
                    <input name="cn23Form[importerVatCode]"
                           value="<?php echo $cn_data->cn23Form->importerVatCode; ?>"
                           id="importer_vat_code" type="text" class="admin__control-text widefat" />
                </div>
            </div>
            <div class="admin__field">
                <label for="description" class="admin__field-label">
                    <?php _e( 'Description', 'woo-lithuaniapost' ); ?>
                </label>
                <div class="admin__field-control">
            <textarea name="cn23Form[description]" id="description"
                      class="admin__control-textarea widefat"><?php echo $cn_data->cn23Form->description; ?></textarea>
                </div>
            </div>
            <section class="admin__page-section">
                <div class="admin__page-section-title">
                    <span class="title"><?php _e( 'Parcel Items', 'woo-lithuaniapost' ) ?></span>
                </div>
                <div class="admin__table-wrapper">
                    <table class="data-table admin__table-primary edit-order-table">
                        <thead>
                        <tr class="headings">
                            <th class="col-summary">
                        <span>
                            <?php _e( 'Summary', 'woo-lithuaniapost' ); ?> <span style="color:red">*</span>
                        </span>
                            </th>
                            <th class="col-amount">
                        <span>
                            <?php _e( 'Amount', 'woo-lithuaniapost' ); ?> <span style="color:red">*</span>
                        </span>
                            </th>
                            <th class="col-country">
                        <span>
                            <?php _e( 'Country of Origin', 'woo-lithuaniapost' ); ?> <span style="color:red">*</span>
                        </span>
                            </th>
                            <th class="col-currency">
                        <span>
                            <?php _e( 'Currency', 'woo-lithuaniapost' ); ?> <span style="color:red">*</span>
                        </span>
                            </th>
                            <th class="col-hs-code">
                                <span><?php _e( 'HS Item Number', 'woo-lithuaniapost' ); ?></span>
                            </th>
                            <th class="col-weight">
                        <span>
                            <?php _e( 'Weight', 'woo-lithuaniapost' ); ?> (g) <span style="color:red">*</span>
                        </span>
                            </th>
                            <th class="col-quantity">
                        <span>
                            <?php _e( 'Quantity', 'woo-lithuaniapost' ); ?> <span style="color:red">*</span>
                        </span>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="even">
                        <?php $counter = -1; foreach ( $cn_data->cn23Form->cnParts as $item ): $counter++; ?>
                            <tr>
                                <td class="col-summary">
                                    <div class="admin__field-control">
                                        <input name="cn23Form[cnParts][<?php echo $counter; ?>][summary]" type="text" id="summary"
                                               class="admin__control-text" type="text"  value="<?php echo $item->summary; ?>"
                                               required />
                                    </div>
                                </td>
                                <td class="col-amount">
                                    <div class="admin__field-control">
                                        <input name="cn23Form[cnParts][<?php echo $counter; ?>][amount]" type="number"
                                               value="<?php echo number_format ( $item->amount, 2 ); ?>"
                                               step="0.01" id="amount" type="text" class="admin__control-text"
                                               required />
                                    </div>
                                </td>
                                <td class="col-country" style="max-width: 155px">
                                    <select name="cn23Form[cnParts][<?php echo $counter; ?>][countryCode]" id="countryCode" class="admin__control-select">
                                        <?php foreach ( Woo_Lithuaniapost_Admin_Settings::get_country_list ( true ) as $code => $country ): ?>
                                            <option value="<?php echo $code; ?>" <?php echo $code == $item->countryCode ? 'selected' : ''; ?>>
                                                <?php echo $country; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="col-currency">
                                    <select name="cn23Form[cnParts][<?php echo $counter; ?>][currencyCode]" id="currencyCode" class="admin__control-select">
                                        <option value="EUR">EUR</option>
                                        <option value="USD" <?php echo $item->currencyCode
                                        == 'USD' ? 'selected' : ''; ?>>USD</option>
                                    </select>
                                </td>
                                <td class="col-hs-code">
                                    <div class="admin__field-control">
                                        <input name="cn23Form[cnParts][<?php echo $counter; ?>][hsCode]" id="hscode"
                                               value="<?php echo $item->hsCode; ?>"  type="text"class="admin__control-text" />
                                    </div>
                                </td>
                                <td class="col-weight">
                                    <div class="admin__field-control">
                                        <input name="cn23Form[cnParts][<?php echo $counter; ?>][weight]" type="number"
                                               value="<?php echo $item->weight; ?>"
                                               step="0.01" id="weight" type="text" class="admin__control-text"
                                               required />
                                    </div>
                                </td>
                                <td class="col-quantity">
                                    <div class="admin__field-control">
                                        <input name="cn23Form[cnParts][<?php echo $counter; ?>][quantity]" type="number"
                                               value="<?php echo intval ( $item->quantity ); ?>"
                                               step="1" id="quantity" type="text" class="admin__control-text"
                                               required />
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    <?php endif; ?>
    <div id="lpshipping-shipping-modal-footer">
        <button class="button button-primary right"><?php _e( 'Save Changes', 'woo-lithuaniapost' ); ?></button>
    </div>
</div>


<script>
    jQuery ( '#lpshipping-shipment-modal .lpshipping-list-sizes-item-size' ).on ( 'click', function () {
        jQuery ( '#lpshipping-shipment-modal .lpshipping-list-sizes-item-size.active' ).removeClass ( 'active' );
        jQuery ( this ).addClass ( 'active' );
    });

    jQuery ( '#lpshipping-shipment-modal .lpshipping-list-shipfrom-item-label' ).on ( 'click', function () {
        jQuery ( '#lpshipping-shipment-modal .lpshipping-list-shipfrom-item-label.active' ).removeClass ( 'active' );
        jQuery ( this ).addClass ( 'active' );

        if ( jQuery ( this ).children ( 'input' ).val () === "EBIN" ) {
            jQuery ( '#lpshipping-shipment-modal .lpshipping-list-sizes' ).addClass ( 'disabled' );
            jQuery ( '#lpshipping-shipment-modal .lpshipping-list-sizes-item-size.active' ).removeClass ( 'active' );
            jQuery ( '#lpshipping-shipment-modal .lpshipping-list-sizes input' ).prop ( 'checked', false );
        } else {
            jQuery ( '#lpshipping-shipment-modal .lpshipping-list-sizes' ).removeClass ( 'disabled' );
            jQuery ( '#lpshipping-shipment-modal .lpshipping-list-sizes-item-size.active' ).removeClass ( 'active' );
            jQuery ( '#lpshipping-shipment-modal .lpshipping-list-sizes input[value="<?php echo $this->get_order ()->get_meta ( '_woo_lithuaniapost_delivery_size' ); ?>"]')
                .prop ( 'checked', true ).parent ().addClass ( 'active' );
        }
    });

    jQuery ( '#lpshipping-shipment-modal .lpshipping-shipment-nav .step' ).on ( 'click', function () {
        jQuery ( '#lpshipping-shipment-modal .lpshipping-shipment-nav .step' ).removeClass ( 'active' );
        jQuery ( this ).addClass ( 'active' );

        jQuery ( '#lpshipping-shipment-modal .tab').removeClass ( 'tab-active' );
        jQuery ( '#lpshipping-shipment-modal .tab[data-tab="' + jQuery ( this ).data ( 'tab' ) + '"]' ).addClass ( 'tab-active' );
    });
</script>
