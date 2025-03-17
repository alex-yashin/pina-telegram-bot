<?php


namespace PinaTelegramBot\Commands;


use Klev\TelegramBotApi\Methods\SendMessage;
use Klev\TelegramBotApi\Telegram;
use Klev\TelegramBotApi\TelegramException;
use Klev\TelegramBotApi\Types\LinkPreviewOptions;
use Klev\TelegramBotApi\Types\ReplyParameters;
use Pina\Command;
use Pina\Config;
use Pina\Log;
use Pina\Response;

class TestTelegram extends Command
{
    /**
     * @param string $input
     * @throws \Exception
     */
    protected function execute($input = '')
    {
        list($chatId, $messageId) = explode('|', $input);
        echo $chatId."!".$messageId."?";
        $this->answer($chatId, $messageId, 'Тестовое сообщение!');
    }


    protected function bot(): Telegram
    {
        static $bot = null;
        if (!is_null($bot)) {
            return $bot;
        }
        $bot = new Telegram(Config::get('telegram', 'bot'));
        return $bot;
    }

    /**
     * @param $chatId
     * @param $replyTo
     * @param $text
     * @return Response
     * @throws TelegramException
     */
    protected function answer($chatId, $replyTo, $text)
    {
        Log::info('telegram', 'Пытаемся отправить сообщение в '. $chatId. ' для ' . $replyTo .': ' .$text);
        $message = new SendMessage($chatId, $text);
//        $message->reply_to_message_id = $replyTo;
        $message->link_preview_options = new LinkPreviewOptions();
        $message->link_preview_options->is_disabled = true;
        $message->link_preview_options->url = '';
        $message->link_preview_options->prefer_small_media = false;
        $message->link_preview_options->prefer_large_media = false;
        $message->link_preview_options->show_above_text = false;

        $message->reply_parameters = new ReplyParameters();
        $message->reply_parameters->message_id = $replyTo;
        $message->reply_parameters->chat_id = $chatId;
        $message->reply_parameters->allow_sending_without_reply = true;
        $message->reply_parameters->quote = '';
        $message->reply_parameters->quote_parse_mode = 'html';
        $message->reply_parameters->quote_position = 0;

        $this->bot()->sendMessage($message);
        return Response::ok()->json();
    }
}