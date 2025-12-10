<?php

namespace PinaTelegramBot\Model;

use PinaTelegramBot\SQL\TelegramBotGateway;
use PinaTelegramBot\SQL\TelegramBotStartGateway;

class StartHandler
{
    public function __invoke(MessageEvent $message): bool
    {
        if (strpos($message->getText(), '/start') !== 0) {
            return false;
        }

        $param = trim(substr($message->getText(), strlen('/start ')));

        $start = TelegramBotStartGateway::instance()
            ->whereBy('param', $param)
            ->select('answer')
            ->select('id')
            ->first();

        if (!empty($start['answer'])) {
            return $message->answer($start['answer'])->getMessageId();
        }

        $intro = TelegramBotGateway::instance()->whereId($message->getBotId())->value('intro');
        return $message->answer(!empty($intro) ? $intro : $this->getDefaultIntro())->getMessageId();
    }

    protected function getDefaultIntro()
    {
        return 'Я - просто чат-бот, давайте посмотрим, чем я могу вам помочь.';
    }

}