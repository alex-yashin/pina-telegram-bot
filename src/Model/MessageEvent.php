<?php

namespace PinaTelegramBot\Model;

use Klev\TelegramBotApi\Methods\SendMessage;
use Klev\TelegramBotApi\Telegram;
use Klev\TelegramBotApi\TelegramException;
use Klev\TelegramBotApi\Types\LinkPreviewOptions;
use Klev\TelegramBotApi\Types\ReplyParameters;
use Pina\App;
use Pina\Events\Event;
use Pina\Log;
use PinaMedia\File;
use PinaTelegramBot\SQL\TelegramBotSessionGateway;

class MessageEvent extends Event
{
    protected $botId = 0;

    protected $botUsername = '';

    /** @var Telegram */
    protected $bot;

    /** @var \Klev\TelegramBotApi\Types\Message */
    protected $message;

    protected $isAnswered = false;

    protected $sessionId = null;

    public function __construct($botId, Telegram $bot, string $botUsername, \Klev\TelegramBotApi\Types\Message $message)
    {
        $this->botId = $botId;
        $this->bot = $bot;
        $this->botUsername = $botUsername;
        $this->message = $message;
        $this->sessionId = TelegramBotSessionGateway::instance()
            ->whereBy('telegram_bot_id', $this->botId)
            ->whereBy('chat_id', $this->getChatId())
            ->whereBy('user_id', $this->getUserId())
            ->whereNotExpired()
            ->orderBy('id', 'desc')
            ->value('id');
    }

    public function queueable(): bool
    {
        return false;
    }

    public function serialize(): array
    {
        return [];
    }

    public function isAnswered(): bool
    {
        return $this->isAnswered;
    }

    public function getMessageId()
    {
        return $this->message->message_id;
    }

    public function getChatId()
    {
        return $this->message->chat->id;
    }

    public function isPrivate(): bool
    {
        return $this->message->chat->type  == 'private';
    }

    public function isMentioned(): bool
    {
        return  in_array('@' . $this->botUsername, $this->getMentions());
    }

    public function getBotId(): int
    {
        return $this->botId;
    }

    public function getUserId()
    {
        return $this->message->from->id;
    }

    public function getUsername(): string
    {
        return $this->message->from->username ?? '';
    }

    public function getFirstName(): string
    {
        return $this->message->from->first_name;
    }

    public function getLastName(): string
    {
        return $this->message->from->last_name ?? '';
    }

    public function getLanguageCode(): string
    {
        return $this->message->from->language_code ?? '';
    }

    public function getChatType(): string
    {
        return $this->message->chat->type;
    }

    public function getChatTitle(): string
    {
        return $this->message->chat->title ?? '';
    }

    public function getText(): string
    {
        return trim($this->message->caption . ' ' . $this->message->text);
    }

    public function getRepliedText(): string
    {
        return $this->getRepliedTextLoop($this->message);
    }

    protected function getRepliedTextLoop(\Klev\TelegramBotApi\Types\Message $message)
    {
        if (is_null($message->reply_to_message)) {
            return '';
        }
        return trim($message->reply_to_message->caption . ' ' . $message->reply_to_message->text . "\r\n" . $this->getRepliedTextLoop($message->reply_to_message));
    }

    public function getRepliedMessageIds(): array
    {
        return $this->getRepliedMessageIdsLoop($this->message);
    }

    protected function getRepliedMessageIdsLoop(\Klev\TelegramBotApi\Types\Message $message): array
    {
        if (is_null($message->reply_to_message)) {
            return [];
        }
        $parentIds = $this->getRepliedMessageIdsLoop($message->reply_to_message);
        $parentIds[] = $message->reply_to_message->message_id;
        return $parentIds;
    }

    public function startSession(string $param)
    {
        $session = [
            'telegram_bot_id' => $this->getBotId(),
            'chat_id' => $this->getChatId(),
            'user_id' => $this->getUserId(),
            'param' => $param,
            'username' => $this->getUsername(),
        ];
        $this->sessionId = TelegramBotSessionGateway::instance()->insertGetId($session);
        return $this->sessionId;
    }

    public function getSessionId()
    {
        return $this->sessionId;
    }

    public function answer($text): MessageEvent
    {
        $chatId = $this->getChatId();
        $replyTo = $this->getMessageId();

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

        $sentMessage = $this->bot->sendMessage($message);

        $this->isAnswered = true;

        $event = new SentMessageEvent($this->botId, $this->bot, $this->botUsername, $sentMessage);
        $event->trigger();

        return $event;
    }

    public function downloadMedias(): array
    {
        $mediaIds = [];
        if ($this->message->photo) {
            //фотографии в массиве - это одна фотография, но разных размеров, берем последнюю, как самую большую
            $file = array_pop($this->message->photo);
            $mediaIds[] = $this->download($file->file_id);
            Log::info('telegram', 'file', ['file' => $file, 'media_id' => $mediaIds]);
        }

        if ($this->message->document) {
            $mediaIds[]= $this->download($this->message->document->file_id, $this->message->document->file_name);
            Log::info('telegram', 'file', ['document' => $this->message->document, 'media_id' => $mediaIds]);
        }

        if ($this->message->reply_to_message && $this->message->reply_to_message->photo) {
            //фотографии в массиве - это одна фотография, но разных размеров, берем последнюю, как самую большую
            $file = array_pop($this->message->reply_to_message->photo);
            $mediaIds[] = $this->download($file->file_id);
            Log::info('telegram', 'file', ['file' => $file, 'media_id' => $mediaIds]);
        }

        if ($this->message->reply_to_message && $this->message->reply_to_message->document) {
            $mediaIds[]= $this->download($this->message->reply_to_message->document->file_id, $this->message->reply_to_message->document->file_name);
            Log::info('telegram', 'file', ['document' => $this->message->reply_to_message->document, 'media_id' => $mediaIds]);
        }

        return $mediaIds;
    }

    protected function getMentions()
    {
        $mentions = [];
        if ($this->message->entities) {
            foreach ($this->message->entities as $entity) {
                if ($entity->type != 'mention') {
                    continue;
                }

                $mentions[] = mb_substr($this->message->text, $entity->offset, $entity->length);
            }
        }

        if ($this->message->caption_entities) {
            foreach ($this->message->caption_entities as $entity) {
                if ($entity->type != 'mention') {
                    continue;
                }

                $mentions[] = mb_substr($this->message->caption, $entity->offset, $entity->length);
            }
        }
        return $mentions;
    }

    /**
     * @param $fileId
     * @return string
     * @throws TelegramException
     * @throws \Exception
     */
    protected function download($fileId, $fileName = '')
    {
        $suffix = '';
        if ($fileName) {
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            if ($ext) {
                $suffix = '.' . $ext;
            }
        }
        $tmpPath = App::tmp() . '/' . uniqid('download', true);
        $this->bot->downloadFile($fileId, $tmpPath);
        $mediaFile = new File($tmpPath, rand(1, 100000) . $suffix, null, $fileName);
        $mediaFile->moveToStorage();
        return $mediaFile->saveMeta();
    }

}