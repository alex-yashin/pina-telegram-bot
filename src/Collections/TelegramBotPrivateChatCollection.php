<?php

namespace PinaTelegramBot\Collections;

use Pina\Data\DataCollection;
use PinaTelegramBot\SQL\TelegramBotPrivateChatGateway;

class TelegramBotPrivateChatCollection extends DataCollection
{
    protected function makeQuery()
    {
        return TelegramBotPrivateChatGateway::instance();
    }

}