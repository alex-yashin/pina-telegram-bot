<?php

namespace PinaTelegramBot\Endpoints;

use Pina\Data\DataCollection;
use Pina\Data\QueryDataCollection;
use Pina\Http\DelegatedCollectionEndpoint;
use PinaTelegramBot\SQL\TelegramBotMessageGateway;
use function Pina\__;

class TelegramBotMessageEndpoint extends DelegatedCollectionEndpoint
{
    protected function getCollectionTitle(): string
    {
        return __('Сообщения');
    }

    protected function makeDataCollection(): DataCollection
    {
        return new QueryDataCollection(TelegramBotMessageGateway::instance()->orderBy('id', 'desc'));
    }
}