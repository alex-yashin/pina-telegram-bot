<?php

namespace PinaTelegramBot\Types;

use Pina\TableDataGateway;
use Pina\Types\QueryDirectoryType;
use PinaTelegramBot\SQL\TelegramBotGateway;

class TelegramBotType extends QueryDirectoryType
{

    protected function makeQuery(): TableDataGateway
    {
        return TelegramBotGateway::instance();
    }

}