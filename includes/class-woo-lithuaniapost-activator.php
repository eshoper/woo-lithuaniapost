<?php

/**
 * Fired during plugin activation
 *
 * @link       https://post.lt
 * @since      1.0.0
 *
 * @package    Woo_Lithuaniapost
 * @subpackage Woo_Lithuaniapost/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Woo_Lithuaniapost
 * @subpackage Woo_Lithuaniapost/includes
 * @author     AB "Lietuvos Paštas" <info@post.lt>
 */
class Woo_Lithuaniapost_Activator
{
	/**
     * Create database tables
	 * @since    1.0.0
	 */
	public static function activate ()
    {
        global $wpdb;
        require_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );

        /**
         * Create table wp_woo_lithuaniapost_api_token
         */
        $charset_collate = $wpdb->get_charset_collate ();

        //Check to see if the table exists already, if not, then create it
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->woo_lithuaniapost_api_token'" )
            != $wpdb->woo_lithuaniapost_api_token )
        {
            $sql = "CREATE TABLE $wpdb->woo_lithuaniapost_api_token (
                id int(11) NOT NULL auto_increment,
                access_token varchar(255) NOT NULL,
                refresh_token varchar(255) NOT NULL,
                expires varchar(255) NOT NULL,
                UNIQUE KEY id (id)
            ) $charset_collate;";

            dbDelta( $sql );
        }

        /**
         * Create table wo_lithuaniapost_lpexpress_terminals
         */
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->woo_lithuaniapost_lpexpress_terminals'" )
            != $wpdb->woo_lithuaniapost_lpexpress_terminals )
        {
            $sql = "CREATE TABLE $wpdb->woo_lithuaniapost_lpexpress_terminals (
                id int(11) NOT NULL auto_increment,
                terminal_id varchar(255) NOT NULL,
                name varchar(255) NOT NULL,
                address varchar(255) NOT NULL,
                city varchar(255) NOT NULL,
                UNIQUE KEY id (id)
            ) $charset_collate;";

            dbDelta( $sql );
        }

        /**
         * Create table woo_lithuaniapost_country_list
         */
        if ( $wpdb->get_var ( "SHOW TABLES LIKE '$wpdb->woo_lithuaniapost_country_list'" ) !=
            $wpdb->woo_lithuaniapost_country_list ) {
            $sql = "CREATE TABLE $wpdb->woo_lithuaniapost_country_list (
                id int(11) NOT NULL auto_increment,
                country_id int NOT NULL,
                country varchar(255) NOT NULL,
                country_code varchar(255) NOT NULL,
                UNIQUE KEY id (id)
            ) $charset_collate;";

            dbDelta( $sql );
        }

        /**
         * Create table woo_lithuaniapost_shipping_templates
         */
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->woo_lithuaniapost_shipping_templates'" ) !=
            $wpdb->woo_lithuaniapost_shipping_templates ) {
            $sql = "CREATE TABLE $wpdb->woo_lithuaniapost_shipping_templates (
                id int(11) NOT NULL auto_increment,
                shipping_templates TEXT NOT NULL,
                UNIQUE KEY id (id)
            ) $charset_collate;";

            dbDelta( $sql );
        }

        /**
         * Create table woo_lithuaniapost_table_rates
         */
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->woo_lithuaniapost_table_rates'" ) !=
            $wpdb->woo_lithuaniapost_table_rates ) {
            $sql = "CREATE TABLE $wpdb->woo_lithuaniapost_table_rates (
                id int(11) NOT NULL auto_increment,
                method_id TEXT NOT NULL,
                weight_to int(12) NOT NULL,
                price double NOT NULL,
                UNIQUE KEY id (id)
            ) $charset_collate;";

            dbDelta( $sql );
        }

        /**
         * Create table woo_lithuaniapost_country_rates
         */
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->woo_lithuaniapost_country_rates'" ) !=
            $wpdb->woo_lithuaniapost_country_rates ) {
            $sql = "CREATE TABLE $wpdb->woo_lithuaniapost_country_rates (
                id int(11) NOT NULL auto_increment,
                method_id TEXT NOT NULL,
                country varchar(255) NOT NULL,
                price double NOT NULL,
                UNIQUE KEY id (id)
            ) $charset_collate;";

            dbDelta( $sql );
        }

        /**
         * Create table woo_lithuaniapost_tracking_events
         */
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->woo_lithuaniapost_tracking_events'" ) !=
            $wpdb->woo_lithuaniapost_tracking_events ) {
            $sql = "CREATE TABLE $wpdb->woo_lithuaniapost_tracking_events (
                id int(11) NOT NULL auto_increment,
                order_id int NOT NULL,
                barcode varchar(255) NOT NULL,
                state varchar(255) NULL,
                events TEXT NULL,
                updated datetime NULL,
                UNIQUE KEY id (id)
            ) $charset_collate;";

            dbDelta( $sql );
        }
    }
}
