<?php

namespace PinaTelegramBot\SQL;

use Pina\Data\Schema;
use Pina\TableDataGateway;
use Pina\Types\StringType;
use PinaTelegramBot\Types\TelegramBotType;

class TelegramBotPrivateChatGateway extends TableDataGateway
{
    protected static $table = "telegram_bot_private_chat";

    /**
     * @return Schema
     * @throws \Exception
     */
    public function getSchema()
    {
        $schema = parent::getSchema();
        $schema->add('telegram_bot_id', 'Телеграм бот', TelegramBotType::class)->setMandatory();
        $schema->add('username', 'Пользователь', StringType::class)->setMandatory();
        $schema->add('chat_id', 'ID Чата', StringType::class)->setMandatory();
        $schema->setPrimaryKey(['telegram_bot_id', 'username']);
        $schema->addUniqueKey(['chat_id', 'telegram_bot_id']);
        return $schema;

    }
}