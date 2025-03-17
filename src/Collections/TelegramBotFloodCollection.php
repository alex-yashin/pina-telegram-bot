<?php

namespace PinaTelegramBot\Collections;

use Pina\Data\DataCollection;
use PinaTelegramBot\SQL\TelegramBotFloodGateway;

class TelegramBotFloodCollection extends DataCollection
{
    protected function makeQuery()
    {
        return TelegramBotFloodGateway::instance();
    }

}