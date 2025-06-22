<?php

namespace PinaTelegramBot\Endpoints;

use Pina\App;
use Pina\Data\DataCollection;
use Pina\Http\DelegatedCollectionEndpoint;
use PinaTelegramBot\Collections\TelegramBotPrivateChatCollection;
use function Pina\__;

class TelegramBotPrivateChatEndpoint extends DelegatedCollectionEndpoint
{
    protected function getCollectionTitle(): string
    {
        return __('Приватные чаты');
    }

    protected function makeDataCollection(): DataCollection
    {
        return App::make(TelegramBotPrivateChatCollection::class);
    }
}