<?php

namespace PinaTelegramBot\Endpoints;

use Pina\App;
use Pina\Data\DataCollection;
use Pina\Http\DelegatedCollectionEndpoint;
use PinaTelegramBot\Collections\TelegramBotChatCollection;

use function Pina\__;

class TelegramBotChatEndpoint extends DelegatedCollectionEndpoint
{
    protected function getCollectionTitle(): string
    {
        return __('Чаты');
    }

    protected function makeDataCollection(): DataCollection
    {
        return App::make(TelegramBotChatCollection::class);
    }
}