<?php
namespace PaymentMethodLogger\Util\Validator;

abstract class AbstractValidator implements ValidatorInterface
{
    protected $value;
    protected $message;

    /**
     * Get error message
     */
    public function get_message()
    {
        return $this->message;
    }
}
