<?php

namespace PinaTelegramBot\Collections;

use Pina\Data\DataCollection;
use PinaTelegramBot\SQL\TelegramBotStartGateway;

class TelegramBotStartCollection extends DataCollection
{
    protected function makeQuery()
    {
        return TelegramBotStartGateway::instance()->orderBy('param', 'asc');
    }

}