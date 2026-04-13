<?php

namespace PinaTelegramBot\Notification;

use PinaNotifications\Messages\MessageInterface;
use PinaNotifications\Recipients\RecipientInterface;
use PinaNotifications\Transports\TransportRegistry;

class TelegramRecipient implements RecipientInterface
{
    protected string $chatId;

    public function __construct(string $chatId)
    {
        $this->chatId = $chatId;
    }

    public function notify(MessageInterface $message, $replyTo = null): bool
    {
        /** @var Transport $transport */
        $transport = TransportRegistry::get('telegram');
        return $transport->send($this->chatId, $message, $replyTo);
    }
}