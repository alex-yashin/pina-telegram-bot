<?php

namespace PinaTelegramBot\Endpoints;

use Pina\App;
use Pina\Http\DelegatedCollectionEndpoint;
use Pina\Http\Request;
use PinaTelegramBot\Collections\TelegramBotChatCollection;

use function Pina\__;

class TelegramBotChatEndpoint extends DelegatedCollectionEndpoint
{

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->composer->configure(__('Чаты'), __('Добавить'));
        $this->collection = App::make(TelegramBotChatCollection::class);
    }

}