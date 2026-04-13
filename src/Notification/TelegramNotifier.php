<?php

namespace PinaTelegramBot\Notification;

use PinaNotifications\Messages\MessageInterface;

class TelegramNotifier
{

    public function notify(string $chatId, MessageInterface $message, $replyTo = null): bool
    {
        $telegramRecipient = new TelegramRecipient($chatId);
        return $telegramRecipient->notify($message, $replyTo);
    }

}