<?php

namespace PinaTelegramBot\Model;

use PinaTelegramBot\SQL\TelegramBotChatGateway;
use PinaTelegramBot\SQL\TelegramBotPrivateChatGateway;

class ChatRegistrationHandler
{
    public function __invoke(MessageEvent $message): bool
    {
        if ($message->isPrivate()) {
            return $this->registerPrivateChat($message);
        }

        return $this->registerGroup($message);
    }

    protected function registerPrivateChat(MessageEvent $message): bool
    {
        $registered = TelegramBotPrivateChatGateway::instance()
            ->whereBy('telegram_bot_id', $message->getBotId())
            ->whereBy('username', $message->getUsername())
            ->exists();

        if ($registered) {
            return false;
        }

        TelegramBotPrivateChatGateway::instance()->insert([
            'telegram_bot_id' => $message->getBotId(),
            'username' => $message->getUsername(),
            'chat_id' => $message->getChatId(),
        ]);

        return false;
    }

    protected function registerGroup(MessageEvent $message): bool
    {
        $registered = TelegramBotChatGateway::instance()
            ->whereBy('telegram_bot_id', $message->getBotId())
            ->whereBy('chat_id', $message->getChatId())
            ->exists();

        if ($registered) {
            return false;
        }

        TelegramBotChatGateway::instance()->insert([
            'telegram_bot_id' => $message->getBotId(),
            'chat_id' => $message->getChatId(),
            'title' => $message->getChatTitle(),
        ]);

        return false;
    }
}