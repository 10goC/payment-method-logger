<?php
namespace PaymentMethodLogger\Controller;

use WC_Order;

class Webhook
{
    /**
     * Initialize controller.
     * Setup actions for cancelled and failed orders.
     */
    public static function init()
    {
        add_action( 'woocommerce_order_status_cancelled', [self::class, 'notify_webhook'], 10, 2 );
        add_action( 'woocommerce_order_status_failed', [self::class, 'notify_webhook'], 10, 2 );
    }

    /**
     * Sends POST request to webhook URL to nofify payment failure or cancelled order.
     */
    public static function notify_webhook( $order_id, WC_Order $order )
    {
        $webhookUrl = get_option( 'webhook-url' );
        if ( !$webhookUrl ) {
            // Bail early if no webhook URL
            return;
        }
        $ch = curl_init( $webhookUrl );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, [
            'order_id' => $order_id,
            'status' => $order->data['status'],
            'payment_method' => $order->data['payment_method'],
            'payment_method_title' => $order->data['payment_method_title']
        ] );
        curl_exec( $ch );
        curl_close( $ch );
    }
}
