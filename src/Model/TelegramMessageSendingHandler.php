<?php

namespace PinaTelegramBot\Model;

class TelegramMessageSendingHandler
{

    public function __invoke(TelegramMessageSendingRequest $request)
    {
        $telegram = new TelegramBot($request->getBotId());
        $sentMessage = $telegram->sendMessage($request->getChatId(), $request->getText(), $request->getReplyTo());

        $event = new SentMessageEvent($request->getBotId(), $telegram, $sentMessage, $request->getSessionId());
        $event->trigger();
    }

}