<?php

namespace PinaTelegramBot\SQL;

use Pina\Data\Schema;
use Pina\TableDataGateway;
use Pina\Types\StringType;
use Pina\Types\TokenType;
use PinaTelegramBot\Types\TelegramBotType;

class TelegramBotChatGateway extends TableDataGateway
{
    protected static $table = "telegram_bot_chat";

    /**
     * @return Schema
     * @throws \Exception
     */
    public function getSchema()
    {
        $schema = parent::getSchema();
        $schema->add('telegram_bot_id', 'Телеграм бот', TelegramBotType::class)->setMandatory();
        $schema->add('chat_id', 'ID Чата', TokenType::class)->setMandatory();
        $schema->add('title', 'Название чата', StringType::class)->setMandatory();
        $schema->setPrimaryKey(['telegram_bot_id', 'chat_id']);
        return $schema;
    }
}