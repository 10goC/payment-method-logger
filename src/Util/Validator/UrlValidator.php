<?php
namespace PaymentMethodLogger\Util\Validator;

class UrlValidator extends AbstractValidator
{
    /**
     * Validate URL
     */
    public function validate( $value )
    {
        $success = filter_var( $value, FILTER_VALIDATE_URL );
        if (!$success) {
            $this->message = __( 'Invalid URL', 'payment-method-logger' );
        }
        return $success;
    }

}
