<?php

namespace PinaTelegramBot\Types;

use Pina\Html;
use Pina\Types\StringType;

class CodeTokenType extends StringType
{

    /**
     * @param mixed $value
     * @return string
     * @throws \Exception
     */
    public function draw($value): string
    {
        return Html::nest('pre/code', htmlentities(parent::draw($value)));
    }

}