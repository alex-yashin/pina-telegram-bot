<?php

namespace PinaTelegramBot\Endpoints;

use Pina\Data\DataRecord;
use Pina\Data\Schema;
use Pina\Http\RichEndpoint;
use Pina\Log;
use Pina\Response;
use Pina\Types\StringType;
use PinaTelegramBot\Model\TelegramBot;

class TelegramBotChatSendEndpoint extends RichEndpoint
{
    public function title()
    {
        return 'Отправить';
    }

    public function index()
    {
        $this->makeCollectionComposer($this->title())->index($this->location());

        $record = new DataRecord([], $this->getSchema());
        $form = $this->makeRecordForm($this->location()->link('@'), 'post', $record);
        return $form->wrap($this->makeSidebarWrapper());
    }

    public function store($temp, $chatId, $botId)
    {
        $normalized = $this->getSchema()->normalize($this->request()->all());
        $message = $normalized['message'] ?? '';

        $this->send($botId, $chatId, $message);

        return Response::ok()->contentLocation($this->location()->link('@@'));
    }

    protected function getSchema()
    {
        $schema = new Schema();
        $schema->add('message', 'Сообщение',  StringType::class);
        return $schema;
    }

    public function send(string $botId, string $chatId, string $text): bool
    {
        Log::info('telegram', 'Пытаемся отправить сообщение в '. $chatId .': ' .$text);

        $telegram = new TelegramBot($botId);
        $telegram->sendMessage($chatId, $text);

        return true;
    }

}