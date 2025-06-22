<?php

namespace PinaTelegramBot\Endpoints;

use Pina\App;
use Pina\Data\DataCollection;
use Pina\Http\DelegatedCollectionEndpoint;
use PinaTelegramBot\Collections\TelegramBotStartCollection;

use function Pina\__;

class TelegramBotStartEndpoint extends DelegatedCollectionEndpoint
{
    protected function getCollectionTitle(): string
    {
        return __('Ответы на входные параметры');
    }

    protected function makeDataCollection(): DataCollection
    {
        return App::make(TelegramBotStartCollection::class);
    }
}