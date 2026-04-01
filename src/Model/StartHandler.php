<?php

namespace PinaTelegramBot\Model;

use PinaTelegramBot\SQL\TelegramBotGateway;
use PinaTelegramBot\SQL\TelegramBotStartGateway;

class StartHandler
{
    public function __invoke(MessageEvent $message)
    {
        if (strpos($message->getText(), '/start') !== 0) {
            return;
        }

        $param = trim(substr($message->getText(), strlen('/start ')));

        $start = TelegramBotStartGateway::instance()
            ->whereBy('param', $param)
            ->select('answer')
            ->select('id')
            ->first();

        if (!empty($start['answer'])) {
            $message->answer($start['answer']);
            return;
        }

        $intro = TelegramBotGateway::instance()->whereId($message->getBotId())->value('intro');
        $message->answer(!empty($intro) ? $intro : $this->getDefaultIntro());
    }

    protected function getDefaultIntro()
    {
        return 'Я - просто чат-бот, давайте посмотрим, чем я могу вам помочь.';
    }

}