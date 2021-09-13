<?php

?>
<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="woocommerce_woo_lithuaniapost_lpexpress_terminal_table_rates">
            <?php echo __( 'Export Price vs Weight', 'woo-lithuaniapost' ); ?>
        </label>
    </th>
    <td class="forminp">
        <fieldset>
            <legend class="screen-reader-text">
                            <span>
                                <?php echo __( 'Export Price vs Weight', 'woo-lithuaniapost' ); ?>
                            </span>
            </legend>
            <a href="<?php echo admin_url( 'admin-post.php' ); ?>?action=woo-ltpost-export-table-rates&method=<?php echo $this->id; ?>"
               class="button button-primary"><?php echo __( 'Export', 'woo-lithuaniapost' ); ?></a>
        </fieldset>
    </td>
</tr>
<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="<?php echo $this->id; ?>_tablerates">
            <?php echo __( 'Import Price vs Weight', 'woo-lithuaniapost' ); ?>
        </label>
    </th>
    <td class="forminp">
        <fieldset>
            <legend class="screen-reader-text">
                <span>
                    <?php echo __( 'Import Price vs Weight', 'woo-lithuaniapost' ); ?>
                </span>
            </legend>
            <input class="input-text regular-input " type="file"
                   name="<?php echo $this->id; ?>_tablerates"
                   id="<?php echo $this->id; ?>_tablerates"
                   style="" value="" placeholder="">
        </fieldset>
    </td>
</tr>
