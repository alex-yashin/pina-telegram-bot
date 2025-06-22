<?php

namespace PinaTelegramBot\Endpoints;

use Pina\App;
use Pina\Data\DataCollection;
use Pina\Http\DelegatedCollectionEndpoint;
use PinaTelegramBot\Collections\TelegramBotFloodCollection;

use function Pina\__;

class TelegramBotFloodEndpoint extends DelegatedCollectionEndpoint
{
    protected function getCollectionTitle(): string
    {
        return __('Флуд');
    }

    protected function makeDataCollection(): DataCollection
    {
        return App::make(TelegramBotFloodCollection::class);
    }
}