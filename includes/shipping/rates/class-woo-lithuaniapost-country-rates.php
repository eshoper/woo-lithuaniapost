<?php


class Woo_Lithuaniapost_Country_Rates
{
    /**
     * @var string $_method_id
     */
    protected $_method_id;

    /**
     * Woo_Lithuaniapost_Country_Rates constructor.
     * @param string $method_id
     */
    public function __construct ( $method_id )
    {
        $this->_method_id = $method_id;
    }

    /**
     * Process country rates
     *
     * @param resource $handle
     * @since 1.0.0
     */
    public function process_country_rates ( $handle )
    {
        global $wpdb;

        // Skip first line
        fgets ( $handle );

        // Delete table rates associated with current method
        $wpdb->delete (
            $wpdb->woo_lithuaniapost_country_rates, [
                'method_id' => $this->_method_id
            ]
        );

        // Read data
        while ( ( $data = fgetcsv ( $handle ) ) !== false ) {
            $wpdb->insert (
                $wpdb->woo_lithuaniapost_country_rates, [
                    'method_id' => $this->_method_id,
                    'country' => $data [ 0 ],
                    'price'     => $data [ 1 ]
                ]
            );
        }
    }

    /**
     * Calculate country price
     *
     * @param $method_id
     * @param $cost
     * @return bool
     */
    public function calculate_country_rates ( &$cost )
    {
        global $wpdb;

        // Selected destination of customer
        $selected_destination   = WC ()->customer->get_shipping_country ();

        $price = $wpdb->get_var (
            $wpdb->prepare ( "SELECT price FROM {$wpdb->woo_lithuaniapost_country_rates}
                    WHERE method_id = %s AND country = %s", $this->_method_id, $selected_destination )
        );

        $cost = $price;
    }
}
