<?php

namespace PinaTelegramBot\Model;

use Klev\TelegramBotApi\Methods\SendMessage;
use Klev\TelegramBotApi\Methods\SetWebhook;
use Klev\TelegramBotApi\Telegram;
use Klev\TelegramBotApi\TelegramException;
use Klev\TelegramBotApi\Types\LinkPreviewOptions;
use Klev\TelegramBotApi\Types\Message;
use Klev\TelegramBotApi\Types\ReplyParameters;
use Pina\App;
use Pina\InternalErrorException;
use Pina\Log;
use PinaTelegramBot\SQL\TelegramBotGateway;

class TelegramBot
{

    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getTelegram(): Telegram
    {
        $config = TelegramBotGateway::instance()->findOrFail($this->id);
        if (empty($config['token'])) {
            throw new InternalErrorException();
        }

        $bot = new Telegram($config['token']);
        if ($config['api_url']) {
            $bot->setApiEndpoint($config['api_url']);
        }

        if ($config['file_api_url']) {
            $bot->setFileApiEndpoint($config['file_api_url']);
        }

        return $bot;
    }

    public function getUsername(): string
    {
        return TelegramBotGateway::instance()->whereId($this->id)->value('username');
    }

    public function sendMessage(string $chatId, string $text, int $replyTo = null): Message
    {
        $message = new SendMessage($chatId, $text);
//        $message->reply_to_message_id = $replyTo;
        $message->link_preview_options = new LinkPreviewOptions();
        $message->link_preview_options->is_disabled = true;
        $message->link_preview_options->url = '';
        $message->link_preview_options->prefer_small_media = false;
        $message->link_preview_options->prefer_large_media = false;
        $message->link_preview_options->show_above_text = false;

        $message->reply_parameters = new ReplyParameters();
        $message->reply_parameters->message_id = !empty($replyTo) ? $replyTo : 0;
        $message->reply_parameters->chat_id = !empty($replyTo) ? $chatId : '';
        $message->reply_parameters->allow_sending_without_reply = true;
        $message->reply_parameters->quote = '';
        $message->reply_parameters->quote_parse_mode = 'html';
        $message->reply_parameters->quote_position = 0;

        return $this->getTelegram()->sendMessage($message);
    }

    public function initWebhook()
    {
        $webhookUrl = TelegramBotGateway::instance()->whereId($this->id)->value('webhook_url');
        if (empty($webhookUrl)) {
            $webhookUrl = App::link('telegram-webhook/:id', ['id' => $this->id]);
        }

        if (!$this->isCorrectWebhook($webhookUrl)) {
            Log::error('telegram', 'Wrong webhook url ', ['url' => $webhookUrl]);
            return;
        }

        try {
            $bot = $this->getTelegram();
            $info = $bot->getWebhookInfo();
            if ($info->url != $webhookUrl) {
                $webhook = new SetWebhook($webhookUrl);
                $bot->setWebhook($webhook);
                Log::info('telegram', 'Webhook updated from '. $info->url. ' to '.$webhookUrl);
            } else {
                Log::info('telegram', 'Webhook has address '. $info->url);
            }
        } catch (TelegramException $e) {
            Log::error('telegram', $e->getMessage(), ['url' => $webhookUrl]);
        }
    }


    protected function isCorrectWebhook($url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        return !empty($host);
    }

}