<?php

namespace PinaTelegramBot\Model;

use Pina\Events\Event;

class TelegramMessageSendingRequest extends Event
{

    protected $botId;
    protected $chatId;
    protected $text;
    protected $replyTo;
    protected $sessionId = null;

    public function __construct($botId, $chatId, $text, $replyTo, int $sessionId = null)
    {
        $this->botId = $botId;
        $this->chatId = $chatId;
        $this->text = $text;
        $this->replyTo = $replyTo;
        $this->sessionId = $sessionId;
    }

    public function queueable(): bool
    {
        return true;
    }

    public function serialize(): array
    {
        return [$this->botId, $this->chatId, $this->text, $this->replyTo, $this->sessionId];
    }

    public function getBotId()
    {
        return $this->botId;
    }

    public function getChatId()
    {
        return $this->chatId;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getReplyTo(): ?int
    {
        return $this->replyTo;
    }

    public function getSessionId(): ?int
    {
        return $this->sessionId;
    }
}