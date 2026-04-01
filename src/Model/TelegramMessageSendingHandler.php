<?php

namespace PinaTelegramBot\Model;

use Klev\TelegramBotApi\Methods\SendMessage;
use Klev\TelegramBotApi\Telegram;
use Klev\TelegramBotApi\Types\LinkPreviewOptions;
use Klev\TelegramBotApi\Types\ReplyParameters;
use PinaTelegramBot\SQL\TelegramBotGateway;

class TelegramMessageSendingHandler
{

    public function __invoke(TelegramMessageSendingRequest $request)
    {
        $message = new SendMessage($request->getChatId(), $request->getText());
//        $message->reply_to_message_id = $replyTo;
        $message->link_preview_options = new LinkPreviewOptions();
        $message->link_preview_options->is_disabled = true;
        $message->link_preview_options->url = '';
        $message->link_preview_options->prefer_small_media = false;
        $message->link_preview_options->prefer_large_media = false;
        $message->link_preview_options->show_above_text = false;

        $message->reply_parameters = new ReplyParameters();
        $message->reply_parameters->message_id = $request->getReplyTo();
        $message->reply_parameters->chat_id = $request->getChatId();
        $message->reply_parameters->allow_sending_without_reply = true;
        $message->reply_parameters->quote = '';
        $message->reply_parameters->quote_parse_mode = 'html';
        $message->reply_parameters->quote_position = 0;

        $config = TelegramBotGateway::instance()->findOrFail($request->getBotId());
        if (empty($config['username']) || empty($config['token'])) {
            throw new \Exception('wrong telegram bot configuration: ' . $request->getBotId());
        }

        $bot = new Telegram($config['token']);

        $sentMessage = $bot->sendMessage($message);

        $event = new SentMessageEvent($request->getBotId(), $bot, $config['username'], $sentMessage, $request->getSessionId());
        $event->trigger();
    }

}