<?php

namespace PinaTelegramBot\Notification;

use Klev\TelegramBotApi\Telegram;
use Pina\Log;
use PinaNotifications\Messages\Message;
use PinaNotifications\Transports\TransportInterface;
use PinaTelegramBot\SQL\TelegramBotChatGateway;
use PinaTelegramBot\SQL\TelegramBotGateway;
use PinaTelegramBot\SQL\TelegramBotPrivateChatGateway;
use PinaTelegramBot\Model\TelegramMessageSendingRequest;

class Transport implements TransportInterface
{
    public function send(string $address, Message $message, $replyTo = null): bool
    {
        if (empty($address)) {
            return false;
        }

        $botId = $this->resolveBot($address);
        if (empty($botId)) {
            return false;
        }

        return $this->sendMessageToChat($botId, $address, $message, $replyTo);
    }

    protected function resolveBot($address)
    {
        $botId = TelegramBotGateway::instance()
            ->innerJoin(
                TelegramBotPrivateChatGateway::instance()->on('telegram_bot_id', 'id')
                    ->onBy('chat_id', $address)
            )
            ->value('id');

        if ($botId) {
            return $botId;
        }

        return TelegramBotGateway::instance()
            ->innerJoin(
                TelegramBotChatGateway::instance()->on('telegram_bot_id', 'id')
                    ->onBy('chat_id', $address)
            )
            ->value('id');
    }

    public function sendMessageToChat($botId, $chatId, Message $message, $replyTo = null): bool
    {
        $text = trim($message->getTitle() . ' ' . $message->getText(). ' ' . $message->getLink());

        Log::info('telegram', 'Пытаемся отправить сообщение в '. $chatId . ' для ' . $replyTo .': ' .$text);

        $event = new TelegramMessageSendingRequest($botId, $chatId, $text, $replyTo);
        $event->trigger();

        return true;
    }


}