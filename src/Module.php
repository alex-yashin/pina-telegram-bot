<?php

namespace PinaTelegramBot;

use Pina\App;
use Pina\Events\QueuedListener;
use Pina\ModuleInterface;
use Pina\Router;
use PinaNotifications\Transports\TransportRegistry;
use PinaTelegramBot\Endpoints\TelegramBotChatEndpoint;
use PinaTelegramBot\Endpoints\TelegramBotChatSendEndpoint;
use PinaTelegramBot\Endpoints\TelegramBotEndpoint;
use PinaTelegramBot\Endpoints\TelegramBotFloodEndpoint;
use PinaTelegramBot\Endpoints\TelegramBotMessageEndpoint;
use PinaTelegramBot\Endpoints\TelegramBotPrivateChatEndpoint;
use PinaTelegramBot\Endpoints\TelegramBotSessionEndpoint;
use PinaTelegramBot\Endpoints\TelegramBotStartEndpoint;
use PinaTelegramBot\Model\MessageEvent;
use PinaTelegramBot\Model\MessageLogHandler;
use PinaTelegramBot\Model\SentMessageEvent;
use PinaTelegramBot\Model\TelegramMessageSendingHandler;
use PinaTelegramBot\Model\TelegramMessageSendingRequest;

class Module implements ModuleInterface
{

    public function __construct()
    {
        TransportRegistry::set('telegram', new Notification\Transport());

        MessageEvent::subscribeWithHighPriority(App::load(MessageLogHandler::class));
        SentMessageEvent::subscribeWithHighPriority(App::load(MessageLogHandler::class));

        TelegramMessageSendingRequest::subscribe(new QueuedListener(TelegramMessageSendingHandler::class));

        App::onLoad(Router::class, function (Router $router) {
            $router
                ->register('/telegram-webhook', Endpoints\TelegramWebhookEndpoint::class)
                ->permit('public')
                ->ignoreCSRF();

            $router->register('telegram-bots', TelegramBotEndpoint::class)->permit('root');
            $router->register('telegram-bots/:telegram_bot_id/flood', TelegramBotFloodEndpoint::class);
            $router->register('telegram-bots/:telegram_bot_id/start', TelegramBotStartEndpoint::class);

            $router->register('telegram-bots/:telegram_bot_id/chats', TelegramBotChatEndpoint::class);
            $router->register('telegram-bots/:telegram_bot_id/chats/:chat_id/send', TelegramBotChatSendEndpoint::class);
            $router->register('telegram-bots/:telegram_bot_id/chats/:chat_id/messages', TelegramBotMessageEndpoint::class);
            $router->register('telegram-bots/:telegram_bot_id/chats/:chat_id/sessions', TelegramBotSessionEndpoint::class);

            $router->register('telegram-bots/:telegram_bot_id/private-chats', TelegramBotPrivateChatEndpoint::class);
            $router->register('telegram-bots/:telegram_bot_id/private-chats/:chat_id/messages', TelegramBotMessageEndpoint::class);
            $router->register('telegram-bots/:telegram_bot_id/private-chats/:chat_id/sessions', TelegramBotSessionEndpoint::class);

            $router->register('telegram-bots/:telegram_bot_id/messages', TelegramBotMessageEndpoint::class);
            $router->register('telegram-bots/:telegram_bot_id/sessions', TelegramBotSessionEndpoint::class);
            $router->register('telegram-bots/:telegram_bot_id/sessions/:telegram_bot_session_id/messages', TelegramBotMessageEndpoint::class);
        });
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

}