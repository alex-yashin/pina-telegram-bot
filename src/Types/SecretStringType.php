<?php

namespace PinaTelegramBot\Types;

use Pina\Types\StringType;

class SecretStringType extends StringType
{
    public function draw($value): string
    {
        return '*******';
    }

    protected function makeInput()
    {
        return parent::makeInput()->setType('password');
    }

}