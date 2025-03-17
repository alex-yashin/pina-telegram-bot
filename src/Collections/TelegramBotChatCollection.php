<?php

namespace PinaTelegramBot\Collections;

use Pina\Data\DataCollection;
use PinaTelegramBot\SQL\TelegramBotChatGateway;

class TelegramBotChatCollection  extends DataCollection
{
    protected function makeQuery()
    {
        return TelegramBotChatGateway::instance();
    }

}