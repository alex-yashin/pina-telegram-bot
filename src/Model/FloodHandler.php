<?php

namespace PinaTelegramBot\Model;

use Pina\Log;
use PinaTelegramBot\SQL\TelegramBotFloodGateway;

class FloodHandler
{
    public function __invoke(MessageEvent $message): bool
    {
        if ($message->isAnswered()) {
            return false;
        }

        if ($text = $this->getFlood($message->getBotId(), $message->getText())) {
            Log::info('telegram', 'Нашли ответ "' . $text . '" на сообщение "'. $message->getText().'"');
            return $message->answer($text)->getMessageId();
        }

        return false;
    }

    protected function getFlood($botId, $text)
    {
        $floods = TelegramBotFloodGateway::instance()
            ->whereActual()
            ->whereBy('telegram_bot_id', $botId)
            ->select('preg')
            ->select('replace')
            ->select('id')
            ->get();

        foreach ($floods as $flood) {
            $matches = [];
            if (@preg_match($flood['preg'], $text, $matches)) {
                TelegramBotFloodGateway::instance()->whereId($flood['id'])->markAsSent();
                return preg_replace($flood['preg'], $flood['replace'], $matches[0]);
            }
        }

        return '';
    }
}