<?php

namespace PinaTelegramBot\Model;

use PinaTelegramBot\SQL\TelegramBotMessageGateway;

class MessageLogHandler
{
    public function __invoke(MessageEvent $message): bool
    {
        $m = [
            'telegram_bot_id' => $message->getBotId(),
            'telegram_bot_session_id' => $message->getSessionId(),
            'chat_id' => $message->getChatId(),
            'message_id' => $message->getMessageId(),
            'user_id' => $message->getUserId(),
            'username' => $message->getUsername(),
            'message' => $message->getText(),
            'attachments' => array_filter($message->downloadMedias()),
        ];

        $telegramBotMessageId = TelegramBotMessageGateway::instance()->insertGetId($m);
        TelegramBotMessageGateway::instance()->getSchema()->onUpdate($telegramBotMessageId, $m);

        return false;
    }
}