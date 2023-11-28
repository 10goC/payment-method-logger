<?php
namespace PaymentMethodLogger;

class View
{
    /**
     * Renders the view
     */
    public static function render( $template, $vars = [] )
    {
        load_template( Plugin::base_path() . "views/$template.php", true, $vars );
    }
}
