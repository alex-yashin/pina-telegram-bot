<?php

namespace PinaTelegramBot;

use Pina\App;
use Pina\ModuleInterface;
use PinaNotifications\Transports\TransportRegistry;
use PinaTelegramBot\Endpoints\TelegramBotChatEndpoint;
use PinaTelegramBot\Endpoints\TelegramBotChatSendEndpoint;
use PinaTelegramBot\Endpoints\TelegramBotEndpoint;
use PinaTelegramBot\Endpoints\TelegramBotFloodEndpoint;
use PinaTelegramBot\Endpoints\TelegramBotMessageEndpoint;
use PinaTelegramBot\Endpoints\TelegramBotPrivateChatEndpoint;
use PinaTelegramBot\Endpoints\TelegramBotStartEndpoint;
use PinaTelegramBot\Model\MessageEvent;
use PinaTelegramBot\Model\MessageLogHandler;
use PinaTelegramBot\Model\SentMessageEvent;

class Module implements ModuleInterface
{

    public function __construct()
    {
        TransportRegistry::set('telegram', new Notification\Transport());

        MessageEvent::subscribeWithHighPriority(App::load(MessageLogHandler::class));
        SentMessageEvent::subscribeWithHighPriority(App::load(MessageLogHandler::class));
    }

    public function getPath()
    {
        return __DIR__;
    }

    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function getTitle()
    {
        return 'TelegramBot';
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function http()
    {
        App::router()
            ->register('/telegram-webhook', Endpoints\TelegramWebhookEndpoint::class)
            ->permit('public')
            ->ignoreCSRF();

        App::router()->register('telegram-bots', TelegramBotEndpoint::class)->permit('root');
        App::router()->register('telegram-bots/:telegram_bot_id/flood', TelegramBotFloodEndpoint::class, ['telegram_bot_id']);
        App::router()->register('telegram-bots/:telegram_bot_id/start', TelegramBotStartEndpoint::class, ['telegram_bot_id']);
        App::router()->register('telegram-bots/:telegram_bot_id/messages', TelegramBotMessageEndpoint::class, ['telegram_bot_id']);
        App::router()->register('telegram-bots/:telegram_bot_id/chats', TelegramBotChatEndpoint::class, ['telegram_bot_id']);
        App::router()->register('telegram-bots/:telegram_bot_id/chats/:chat_id/send', TelegramBotChatSendEndpoint::class, ['telegram_bot_id', 'chat_id']);
        App::router()->register('telegram-bots/:telegram_bot_id/chats/:chat_id/messages', TelegramBotMessageEndpoint::class, ['telegram_bot_id', 'chat_id']);
        App::router()->register('telegram-bots/:telegram_bot_id/private-chats', TelegramBotPrivateChatEndpoint::class, ['telegram_bot_id']);
        App::router()->register('telegram-bots/:telegram_bot_id/private-chats/:chat_id/messages', TelegramBotMessageEndpoint::class, ['telegram_bot_id', 'chat_id']);

        return [];
    }

}