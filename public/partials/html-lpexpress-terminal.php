<?php
/**
 * HTML for LP EXPRESS terminals
 *
 * @link       https://post.lt
 * @since      1.0.0
 *
 * @package    Woo_Lithuaniapost
 * @subpackage Woo_Lithuaniapost/public/partials
 */

/**
 * @var Woo_Lithuaniapost_Public $this
 */
?>
<br>
<label for="woo_lithuaniapost_lpexpress_terminal_id" class="">
    <?php echo __( 'Terminal:', 'woo-lithuaniapost' ); ?>&nbsp;<abbr class="required">*</abbr>
</label>
<select name="woo_lithuaniapost_lpexpress_terminal_city" id="woo_lithuaniapost_lpexpress_terminal_city">
    <option value=""><?php echo __( 'Please select LP EXPRESS terminal locality..', 'woo-lithuaniapost' ); ?></option>
    <?php foreach ( $this->get_terminal_list () as $city => $terminals ): ?>
        <option label="<?php echo $city; ?>" <?php if ( !empty ( $selected_terminal_session ) && $selected_terminal_session [ 'city' ] == $city ): ?>selected<?php endif; ?>><?php echo $city; ?></option>
    <?php endforeach; ?>
</select>
<select name="woo_lithuaniapost_lpexpress_terminal_id" id="woo_lithuaniapost_lpexpress_terminal_id"
        class="select" <?php if ( !empty ( $selected_terminal_session ) && $selected_terminal_session [ 'terminal' ] != null ): ?> style="display: block" <?php else: ?>style="display: none"<?php endif; ?> >
    <option value=""><?php echo __( 'Please select LP EXPRESS terminal..', 'woo-lithuaniapost' ); ?></option>
    <?php foreach ( $this->get_terminal_list () as $city => $terminals ): ?>
        <?php foreach ( $terminals as $terminal_id => $terminal ): ?>
            <option value="<?php echo $terminal_id; ?>" data-city="<?php echo $city; ?>" <?php if ( !empty ( $selected_terminal_session ) && $selected_terminal_session [ 'terminal' ] == $terminal_id ): ?>selected<?php endif; ?>>
                <?php echo $terminal; ?>
            </option>
        <?php endforeach; ?>
    <?php endforeach; ?>
</select>
<script>
    jQuery ( document ).ready ( function () {
        jQuery ( '#woo_lithuaniapost_lpexpress_terminal_city' ).on ( 'change', function () {
            let terminalSelect = jQuery ( '#woo_lithuaniapost_lpexpress_terminal_id' );
            let terminalSelectForstOption = jQuery ( '#woo_lithuaniapost_lpexpress_terminal_id option:first' );

            if ( jQuery ( this ).val () != '' ) {
                terminalSelect.css ( { 'display': 'block' } );

                terminalSelect.children ( 'option' ).hide ();
                terminalSelectForstOption.css ( { 'display': 'block' } );
                terminalSelect.val (
                    terminalSelectForstOption.val ()
                );

                terminalSelect.children ( 'option[data-city="' + jQuery ( this ).val () + '"]' ).css ( { 'display': 'block' } );
            } else {
                terminalSelect.css ( { 'display': 'none' } );
                terminalSelect.val (
                    terminalSelectForstOption.val ()
                );
            }
        });
    });
</script>
