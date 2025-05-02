<?php

namespace PinaTelegramBot\SQL;

use Pina\Data\Schema;
use Pina\TableDataGateway;
use Pina\Types\IntegerType;
use Pina\Types\TokenType;

class TelegramBotSessionGateway extends TableDataGateway
{
    protected static $table = "telegram_bot_session";

    /**
     * @return Schema
     * @throws \Exception
     */
    public function getSchema()
    {
        $schema = parent::getSchema();
        $schema->addAutoincrementPrimaryKey();
        $schema->add('telegram_bot_id', 'Телеграм бот', IntegerType::class)->setMandatory();
        $schema->add('chat_id', 'ID Чата', TokenType::class)->setMandatory();
        $schema->add('user_id', 'ID Пользователя в телеграме', TokenType::class)->setMandatory();
        $schema->addKey(['telegram_bot_id', 'chat_id', 'user_id']);

        $schema->add('param', 'Код параметра /start', TokenType::class)->setMandatory();
        $schema->add('username', 'Пользователь', TokenType::class)->setMandatory();
        $schema->addCreatedAt();
        return $schema;
    }

    public function whereNotExpired()
    {
        return $this->where($this->getAlias() . '.created_at > date_sub(NOW(), INTERVAL 1 HOUR)');
    }

}