<?php

namespace PinaTelegramBot\Endpoints;

use Pina\Data\DataCollection;
use Pina\Data\QueryDataCollection;
use Pina\Http\DelegatedCollectionEndpoint;

use PinaTelegramBot\SQL\TelegramBotStartGateway;
use function Pina\__;

class TelegramBotStartEndpoint extends DelegatedCollectionEndpoint
{
    protected function getCollectionTitle(): string
    {
        return __('Ответы на входные параметры');
    }

    protected function makeDataCollection(): DataCollection
    {
        return new QueryDataCollection(TelegramBotStartGateway::instance()->orderBy('param', 'asc'));
    }
}