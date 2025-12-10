<?php

namespace PinaTelegramBot\SQL;

use Pina\Data\Schema;
use Pina\TableDataGateway;
use Pina\Types\IntegerType;
use Pina\Types\StringType;
use Pina\Types\TextType;
use Pina\Types\TokenType;
use PinaMedia\Types\GalleryRelation;
use PinaTelegramBot\Types\TelegramBotType;

class TelegramBotMessageGateway extends TableDataGateway
{
    protected static $table = "telegram_bot_message";

    /**
     * @return Schema
     * @throws \Exception
     */
    public function getSchema()
    {
        $schema = parent::getSchema();
        $schema->addAutoincrementPrimaryKey();
        $schema->add('telegram_bot_id', 'Телеграм бот', TelegramBotType::class)->setMandatory();
        $schema->add('telegram_bot_session_id', 'Сессия', IntegerType::class)->setNullable();
        $schema->add('chat_id', 'ID Чата', TokenType::class)->setMandatory();
        $schema->add('message_id', 'ID Сообщения', TokenType::class)->setMandatory();
        $schema->add('user_id', 'ID Пользователя в телеграме', TokenType::class)->setMandatory();
        $schema->add('username', 'Пользователь', TokenType::class)->setMandatory();
        $schema->add('message', 'Сообщение', TextType::class);
        $schema->add('attachments', 'Вложения', new GalleryRelation(TelegramBotMessageMediaGateway::instance(), 'telegram_bot_message_id'));
        $schema->addCreatedAt();
        $schema->setStatic();
        return $schema;
    }

}