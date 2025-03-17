<?php

namespace PinaTelegramBot\Endpoints;

use Pina\App;
use Pina\Http\DelegatedCollectionEndpoint;
use Pina\Http\Request;
use PinaTelegramBot\Collections\TelegramBotPrivateChatCollection;
use function Pina\__;

class TelegramBotPrivateChatEndpoint extends DelegatedCollectionEndpoint
{

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->composer->configure(__('Приватные чаты'), __('Добавить'));
        $this->collection = App::make(TelegramBotPrivateChatCollection::class);
    }
}