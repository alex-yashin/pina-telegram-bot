<?php

namespace PinaTelegramBot\Endpoints;

use Pina\Data\DataCollection;
use Pina\Data\QueryDataCollection;
use Pina\Http\DelegatedCollectionEndpoint;

use PinaTelegramBot\SQL\TelegramBotFloodGateway;
use function Pina\__;

class TelegramBotFloodEndpoint extends DelegatedCollectionEndpoint
{
    protected function getCollectionTitle(): string
    {
        return __('Флуд');
    }

    protected function makeDataCollection(): DataCollection
    {
        return new QueryDataCollection(TelegramBotFloodGateway::instance());
    }
}