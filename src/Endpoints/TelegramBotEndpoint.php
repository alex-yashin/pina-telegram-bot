<?php


namespace PinaTelegramBot\Endpoints;


use Pina\App;
use Pina\Data\DataCollection;
use Pina\Http\DelegatedCollectionEndpoint;
use PinaTelegramBot\Collections\TelegramBotCollection;

use function Pina\__;

class TelegramBotEndpoint extends DelegatedCollectionEndpoint
{
    protected function getCollectionTitle(): string
    {
        return __('Телеграм боты');
    }

    protected function makeDataCollection(): DataCollection
    {
        return App::make(TelegramBotCollection::class);
    }
}