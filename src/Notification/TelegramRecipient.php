<?php

namespace PinaTelegramBot\Notification;

use PinaNotifications\Messages\Message;
use PinaNotifications\Recipients\RecipientInterface;
use PinaNotifications\Transports\TransportRegistry;

class TelegramRecipient implements RecipientInterface
{
    protected $chatId;

    public function __construct(string $chatId)
    {
        $this->chatId = $chatId;
    }

    public function notify(Message $message, $replyTo = null): bool
    {
        /** @var Transport $transport */
        $transport = TransportRegistry::get('telegram');
        return $transport->send($this->chatId, $message, $replyTo);
    }
}