<?php
namespace PaymentMethodLogger\Controller;

use PaymentMethodLogger\Util\FileWriter;
use WC_Order;

class Logger
{
    /**
     * Initialize controller.
     * Setup action for successful orders.
     */
    public static function init()
    {
        add_action( 'woocommerce_payment_complete', [self::class, 'log_order'], 10, 2 );
    }

    /**
     * Log order details
     */
    public static function log_order( $order_id, $transaction_id = null )
    {
        $filename = 'order-' . str_pad($order_id, 10, '0', STR_PAD_LEFT) . '.json';
        $order = new WC_Order( $order_id );
        $upload_dir = wp_get_upload_dir();
        $filepath = $upload_dir['basedir'] . '/wc-logs';
        $writer = new FileWriter();
        $writer->write( "$filepath/$filename", json_encode( $order->data, JSON_PRETTY_PRINT ) );
    }
}
