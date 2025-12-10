<?php

namespace PinaTelegramBot\Endpoints;

use Pina\Data\DataCollection;
use Pina\Data\QueryDataCollection;
use Pina\Http\DelegatedCollectionEndpoint;
use PinaTelegramBot\SQL\TelegramBotPrivateChatGateway;
use function Pina\__;

class TelegramBotPrivateChatEndpoint extends DelegatedCollectionEndpoint
{
    protected function getCollectionTitle(): string
    {
        return __('Приватные чаты');
    }

    protected function makeDataCollection(): DataCollection
    {
        return new QueryDataCollection(TelegramBotPrivateChatGateway::instance());
    }
}