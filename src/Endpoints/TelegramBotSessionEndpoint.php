<?php

namespace PinaTelegramBot\Endpoints;

use Pina\Data\DataCollection;
use Pina\Data\QueryDataCollection;
use Pina\Http\DelegatedCollectionEndpoint;
use PinaTelegramBot\SQL\TelegramBotSessionGateway;
use function Pina\__;

class TelegramBotSessionEndpoint extends DelegatedCollectionEndpoint
{
    protected function getCollectionTitle(): string
    {
        return __('Сессии');
    }

    protected function makeDataCollection(): DataCollection
    {
        return new QueryDataCollection(TelegramBotSessionGateway::instance()->orderBy('id', 'desc'));
    }

}