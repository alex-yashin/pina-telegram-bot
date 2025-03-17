<?php

namespace PinaTelegramBot\Notification;

use PinaNotifications\Messages\Message;

class TelegramNotifier
{

    public function notify(string $chatId, Message $message, $replyTo = null): bool
    {
        $telegramRecipient = new TelegramRecipient($chatId);
        return $telegramRecipient->notify($message, $replyTo);
    }

}