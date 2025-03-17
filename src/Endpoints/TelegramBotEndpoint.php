<?php


namespace PinaTelegramBot\Endpoints;


use Pina\App;
use Pina\Data\DataRecord;
use Pina\Http\DelegatedCollectionEndpoint;
use Pina\Http\Request;
use PinaTelegramBot\Collections\TelegramBotCollection;

use function Pina\__;

class TelegramBotEndpoint extends DelegatedCollectionEndpoint
{

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->composer->configure(__('Телеграм боты'), 'Добавить бота');
        $this->composer->setItemCallback(function(DataRecord $item) {
            $data = $item->getTextData();
            return $data['username'] ?? '';
        });
        $this->collection = App::make(TelegramBotCollection::class);
    }

}