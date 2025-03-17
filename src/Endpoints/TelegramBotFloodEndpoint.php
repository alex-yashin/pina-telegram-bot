<?php

namespace PinaTelegramBot\Endpoints;

use Pina\App;
use Pina\Http\DelegatedCollectionEndpoint;
use Pina\Http\Request;
use PinaTelegramBot\Collections\TelegramBotFloodCollection;

use function Pina\__;

class TelegramBotFloodEndpoint extends DelegatedCollectionEndpoint
{

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->composer->configure(__('Флуд'), __('Добавить'));
        $this->collection = App::make(TelegramBotFloodCollection::class);
    }

}