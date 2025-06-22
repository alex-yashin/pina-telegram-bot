<?php

namespace PinaTelegramBot\Endpoints;

use Klev\TelegramBotApi\Methods\SendMessage;
use Klev\TelegramBotApi\Telegram;
use Klev\TelegramBotApi\Types\LinkPreviewOptions;
use Klev\TelegramBotApi\Types\ReplyParameters;
use Pina\Data\DataRecord;
use Pina\Data\Schema;
use Pina\Http\RichEndpoint;
use Pina\InternalErrorException;
use Pina\Log;
use Pina\Response;
use Pina\Types\StringType;
use PinaTelegramBot\SQL\TelegramBotGateway;

use function Pina\__;

class TelegramBotChatMessageEndpoint extends RichEndpoint
{
    public function title()
    {
        return 'Сообщения';
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
        $config = TelegramBotGateway::instance()->findOrFail($botId);
        if (empty($config['token'])) {
            throw new InternalErrorException();
        }

        $bot = new Telegram($config['token']);

        Log::info('telegram', 'Пытаемся отправить сообщение в '. $chatId .': ' .$text);

        $message = new SendMessage($chatId, $text);
//        $message->reply_to_message_id = $replyTo;
        $message->link_preview_options = new LinkPreviewOptions();
        $message->link_preview_options->is_disabled = true;
        $message->link_preview_options->url = '';
        $message->link_preview_options->prefer_small_media = false;
        $message->link_preview_options->prefer_large_media = false;
        $message->link_preview_options->show_above_text = false;

        $message->reply_parameters = new ReplyParameters();
        $message->reply_parameters->message_id = 0;
        $message->reply_parameters->chat_id = '';
        $message->reply_parameters->allow_sending_without_reply = true;
        $message->reply_parameters->quote = '';
        $message->reply_parameters->quote_parse_mode = 'html';
        $message->reply_parameters->quote_position = 0;

        $bot->sendMessage($message);

        return true;
    }

}