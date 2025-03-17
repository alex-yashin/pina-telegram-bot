<?php

namespace PinaTelegramBot\Endpoints;

use Pina\App;
use Pina\Data\DataRecord;
use Pina\Http\DelegatedCollectionEndpoint;
use Pina\Http\Request;
use PinaTelegramBot\Collections\TelegramBotStartCollection;

use function Pina\__;

class TelegramBotStartEndpoint extends DelegatedCollectionEndpoint
{

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->composer->configure(__('Ответы на входные параметры'), __('Добавить'));
        $this->composer->setItemCallback(function (DataRecord $record) {
            return $record->getMeta('param') ?? $record->getMeta('id');
        });
        $this->collection = App::make(TelegramBotStartCollection::class);
    }

}