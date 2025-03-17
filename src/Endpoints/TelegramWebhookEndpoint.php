<?php


namespace PinaTelegramBot\Endpoints;

use Exception;
use Klev\TelegramBotApi\Telegram;
use Klev\TelegramBotApi\TelegramException;
use Pina\Http\Endpoint;
use Pina\Log;
use Pina\Response;
use PinaTelegramBot\Model\MessageEvent;
use PinaTelegramBot\SQL\TelegramBotGateway;

class TelegramWebhookEndpoint extends Endpoint
{
    /**
     * @return Response
     * @throws \League\Flysystem\FileExistsException
     * @throws Exception
     */
    public function store($id)
    {
        try {

            $config = TelegramBotGateway::instance()->findOrFail($id);
            if (empty($config['username']) || empty($config['token'])) {
                return Response::internalError();
            }

            $bot = new Telegram($config['token']);
            $update = $bot->getWebhookUpdates();

            Log::info('telegram', 'incomming update', ['update' => $update]);

            if ($update->message) {
                $message = new MessageEvent($id, $bot, $config['username'], $update->message);
                $message->trigger();
            }

        } catch (TelegramException $e) {
            Log::error('telegram', $e->getMessage());
        }
        return Response::ok()->json();
    }

}