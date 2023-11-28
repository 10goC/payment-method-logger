<?php
namespace PaymentMethodLogger\Util\Validator;

interface ValidatorInterface {

    public function validate($value);
    public function get_message();

}
