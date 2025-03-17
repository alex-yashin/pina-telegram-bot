<?php

namespace PinaTelegramBot\Notification;

use Klev\TelegramBotApi\Methods\SendMessage;
use Klev\TelegramBotApi\Telegram;
use Klev\TelegramBotApi\Types\LinkPreviewOptions;
use Klev\TelegramBotApi\Types\ReplyParameters;
use Pina\Log;
use PinaNotifications\Messages\Message;
use PinaNotifications\Transports\TransportInterface;
use PinaTelegramBot\Model\SentMessageEvent;
use PinaTelegramBot\SQL\TelegramBotChatGateway;
use PinaTelegramBot\SQL\TelegramBotGateway;
use PinaTelegramBot\SQL\TelegramBotPrivateChatGateway;

class Transport implements TransportInterface
{
    public function send(string $address, Message $message, $replyTo = null): bool
    {
        if (empty($address)) {
            return false;
        }

        $bot = $this->resolveBot($address);
        if (empty($bot)) {
            return false;
        }

        return $this->sendMessageToChat($bot['id'], $bot['username'], new Telegram($bot['token']), $address, $message, $replyTo);
    }

    protected function resolveBot($address)
    {
        $bot = TelegramBotGateway::instance()
            ->innerJoin(
                TelegramBotPrivateChatGateway::instance()->on('telegram_bot_id', 'id')
                    ->onBy('chat_id', $address)
            )
            ->select('id')
            ->select('username')
            ->select('token')
            ->first();

        if ($bot) {
            return $bot;
        }

        return TelegramBotGateway::instance()
            ->innerJoin(
                TelegramBotChatGateway::instance()->on('telegram_bot_id', 'id')
                    ->onBy('chat_id', $address)
            )
            ->select('id')
            ->select('username')
            ->select('token')
            ->first();
    }

    public function sendMessageToChat($botId, $botUsername, Telegram $bot, $chatId, Message $message, $replyTo = null): bool
    {
        $text = trim($message->getTitle() . ' ' . $message->getText(). ' ' . $message->getLink());

        Log::info('telegram', 'Пытаемся отправить сообщение в '. $chatId . ' для ' . $replyTo .': ' .$text);

        $message = new SendMessage($chatId, $text);

        //для форматирования html нужно следить за экранированием и тегами
        $message->parse_mode = '';
        $message->link_preview_options = new LinkPreviewOptions();
        $message->link_preview_options->is_disabled = true;
        $message->link_preview_options->url = '';
        $message->link_preview_options->prefer_small_media = false;
        $message->link_preview_options->prefer_large_media = false;
        $message->link_preview_options->show_above_text = false;

        $message->reply_parameters = new ReplyParameters();
        if ($replyTo) {
            $message->reply_parameters->message_id = $replyTo;
            $message->reply_parameters->chat_id = $chatId;
        } else {
            $message->reply_parameters->message_id = 0;
            $message->reply_parameters->chat_id = '';
        }
        $message->reply_parameters->allow_sending_without_reply = true;
        $message->reply_parameters->quote = '';
        $message->reply_parameters->quote_parse_mode = '';
        $message->reply_parameters->quote_position = 0;

        $sentMessage = $bot->sendMessage($message);

        $event = new SentMessageEvent($botId, $bot, $botUsername, $sentMessage);
        $event->trigger();

        return true;
    }


}