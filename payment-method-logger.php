<?php
/**
 * Plugin Name: Payment Method Logger
 * Description: Logs order meta data upon order completion
 * Version: 1.0
 * Author: Diego Curyk
 */
namespace PaymentMethodLogger;

class Plugin
{
    const VERSION = '1.0';

    /**
     * Initialize plugin
     */
    public function init()
    {
        // Register autoloader
        $this->register_autoloader();

        // Load textdomain
        load_plugin_textdomain('payment-method-logger', false,  'payment-method-logger/languages');

        // Do nothing else without WooCommerce active
        if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            return;
        }
        
        // Initialize controllers
        Controller\Admin::init();
        Controller\Logger::init();
        Controller\Webhook::init();
    }

    /**
     * Get base path
     */
    public static function base_path()
    {
        return plugin_dir_path( __FILE__ );
    }

    /**
     * Register autoloader
     */
    public function register_autoloader()
    {
        spl_autoload_register( function ( $class ) {
            $class = ltrim( $class, '\\' );
            $class = preg_replace( '/^PaymentMethodLogger\\\/', '', $class );
            $class = str_replace( '\\', DIRECTORY_SEPARATOR, $class );
            $file = self::base_path() . "src/$class.php";
            if ( is_readable( $file ) ) {
                require $file;
            }
        });
    }
}

$paymentMethodLogger = new Plugin();
$paymentMethodLogger->init();