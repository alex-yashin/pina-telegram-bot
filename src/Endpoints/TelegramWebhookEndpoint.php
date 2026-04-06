<?php


namespace PinaTelegramBot\Endpoints;

use Exception;
use Klev\TelegramBotApi\TelegramException;
use League\Flysystem\FileExistsException;
use Pina\Http\Endpoint;
use Pina\Log;
use Pina\Response;
use PinaTelegramBot\Model\MessageEvent;
use PinaTelegramBot\Model\TelegramBot;

class TelegramWebhookEndpoint extends Endpoint
{
    /**
     * @return Response
     * @throws FileExistsException
     * @throws Exception
     */
    public function store($id)
    {
        try {
            $telegram = new TelegramBot($id);

            $update = $telegram->getTelegram()->getWebhookUpdates();

            Log::info('telegram', 'incoming update', ['update' => $update]);

            if ($update->message) {
                $message = new MessageEvent($id, $telegram, $update->message);
                $message->trigger();
            }

        } catch (TelegramException $e) {
            Log::error('telegram', $e->getMessage());
        }
        return Response::ok()->json();
    }

}