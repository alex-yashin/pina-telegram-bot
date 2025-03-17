<?php


namespace PinaTelegramBot\Commands;


use Klev\TelegramBotApi\Methods\SetWebhook;
use Klev\TelegramBotApi\Telegram;
use Klev\TelegramBotApi\TelegramException;
use Pina\App;
use Pina\Command;
use Pina\Log;
use PinaTelegramBot\SQL\TelegramBotGateway;

class InstallTelegramWebhook extends Command
{

    protected function execute($input = '')
    {
        $id = $input;

        $config = TelegramBotGateway::instance()->findOrFail($id);

        $webhookUrl = App::link('telegram-webhook/:id', ['id' => $id]);
        if (!$this->isCorrectWebhook($webhookUrl)) {
            Log::error('telegram', 'Wrong webhook url ', ['url' => $webhookUrl]);
            return;
        }

        try {
            $bot = new Telegram($config['token']);
            $info = $bot->getWebhookInfo();
            if ($info->url != $webhookUrl) {
                $webhook = new SetWebhook($webhookUrl);
                $bot->setWebhook($webhook);
            }
        } catch (TelegramException $e) {
            Log::error('telegram', $e->getMessage(), ['url' => $webhookUrl]);
        }
    }

    protected function isCorrectWebhook($url)
    {
        $host = parse_url($url, PHP_URL_HOST);
        return !empty($host);
    }
}