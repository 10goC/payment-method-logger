<?php
namespace PaymentMethodLogger\Controller;

use PaymentMethodLogger\Plugin;
use PaymentMethodLogger\Util\Validator\UrlValidator;
use PaymentMethodLogger\View;
use WC_Order;

class Admin
{
    /**
     * Initialize controller.
     * Setup action for admin menu hook.
     * Setup meta boxes.
     * Add CSS.
     */
    public static function init()
    {
        add_action( 'admin_menu', [self::class, 'register_payments_menu_link'], 80 );
        add_action( 'add_meta_boxes', [ self::class, 'add_log_meta_box' ], 50 );
        add_action( 'admin_enqueue_scripts', [ self::class, 'admin_styles' ] );
    }

    /**
     * Register payments admin menu
     */
    public static function register_payments_menu_link()
    {
        $adminController = new Admin;

        // Add menu item
        add_submenu_page( 'woocommerce', __( 'Logger Fallback', 'payment-method-logger' ), __( 'Logger Fallback', 'payment-method-logger' ), 'manage_woocommerce', 'logger-fallback', [$adminController, 'logger_fallback'] );

        // Admin action for saving changes
        add_action( 'admin_action_save-logger-fallback', [$adminController, 'save_logger_fallback'] );
    }

    /**
     * Add meta box
     */
    public static function add_log_meta_box()
    {
        add_meta_box( 'order-log-meta-box', __( 'Order Log', 'payment-method-logger' ), [self::class, 'render_order_log'], 'woocommerce_page_wc-orders', 'normal', 'default' );
    }

    /**
     * Renders the logger fallback page of the admin section
     */
    public function logger_fallback()
    {
        if ( !empty( $_GET['saved'] ) ) {
            add_action( 'admin_notices', [$this, 'show_settings_saved_notice'] );
        }
        if ( !empty( $_GET['error'] ) ) {
            add_action( 'admin_notices', [$this, 'show_error_notice'] );
        }
        View::render( 'admin/logger-fallback' );
    }

    /**
     * Saves the webhook URL
     */
    public function save_logger_fallback()
    {
        $redirect = admin_url( 'admin.php?page=logger-fallback' );
        if ( isset( $_POST['webhook-url'] ) ) {
            check_admin_referer( 'payment-method-logger' );
            $urlValidator = new UrlValidator();
            if ($urlValidator->validate( $_POST['webhook-url'] )) {
                update_option( 'webhook-url', $_POST['webhook-url'] );
                $redirect = add_query_arg(['saved' => 1], $redirect);
            } else {
                $user_id = get_current_user_id();
                $redirect = add_query_arg(['error' => 1], $redirect);
                set_transient( "payment-method-logger-error-$user_id", $urlValidator->get_message(), MINUTE_IN_SECONDS );
            }
        }
        wp_redirect( $redirect );
    }

    /**
     * Displays a success notice
     */
    public function show_settings_saved_notice()
    {
        View::render( 'admin/notice', [
            'class' => 'notice-success',
            'message' => __( 'Settings saved', 'payment-method-logger' )
        ] );
    }

    /**
     * Displays an error notice
     */
    public function show_error_notice()
    {
        $user_id = get_current_user_id();
        View::render( 'admin/notice', [
            'class' => 'notice-error',
            'message' => get_transient( "payment-method-logger-error-$user_id" )
        ] );
    }

    /**
     * Render order log meta box
     */
    public static function render_order_log( WC_Order $order )
    {
        $order_id = $order->id;
        $filename = 'order-' . str_pad($order_id, 10, '0', STR_PAD_LEFT) . '.json';
        $upload_dir = wp_get_upload_dir();
        $filepath = $upload_dir['basedir'] . '/wc-logs';
        if ( !file_exists( "$filepath/$filename") ) {
            return;
        }
        $contents = file_get_contents( "$filepath/$filename" );
        echo '<pre>' . self::pretty_print( $contents ) . '</pre>';
    }

    /**
     * Add colors to JSON formatted string
     */
    public static function pretty_print( $json )
    {
        $lines = explode( PHP_EOL, $json );
        $out = [];
        foreach ( $lines as $line ) {
            $parts = explode( ':', $line );
            if ( count( $parts ) > 1 ) {
                $key = $parts[0];
                $value = trim( substr( $line, strlen( $key ) + 1 ) );
                $key = '<span class="key">' . $key . '</span>';
                if ( is_numeric( trim( $value, ',' ) ) ) {
                    $value = '<span class="number">' . $value . '</span>';
                } else if ( preg_match( '/(true|false)/i', $value ) ) {
                    $value = '<span class="boolean">' . $value . '</span>';
                } else if ( in_array( trim( $value, ',' ), [ '[]', '{}', '""' ] ) ) {
                    $value = '<span class="empty">' . $value . '</span>';
                } else {
                    $value = '<span class="string">' . $value . '</span>';
                }
                $out[] = $key . ': ' . $value;
            } else {
                $out[] = $line;
            }
        }
        return implode( PHP_EOL, $out );
    }

    /**
     * Add admin styles
     */
    public static function admin_styles()
    {
        wp_enqueue_style( 'admin-styles', Plugin::base_url() . '/assets/css/admin.css', [], Plugin::VERSION );
    }

}