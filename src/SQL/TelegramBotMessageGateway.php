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
        $schema->addCreatedAt()->setDetailed(false);
        $schema->add('telegram_bot_id', 'Телеграм бот', TelegramBotType::class)->setMandatory();
        $schema->add('telegram_bot_session_id', 'Сессия', IntegerType::class)->setNullable();
        $schema->add('chat_id', 'ID Чата', TokenType::class);
        $schema->add('message_id', 'ID Сообщения', TokenType::class);
        $schema->add('user_id', 'ID Пользователя в телеграме', TokenType::class)->setDetailed();
        $schema->add('username', 'Пользователь', TokenType::class);
        $schema->add('message', 'Сообщение', TextType::class);
        $schema->add('attachments', 'Вложения', new GalleryRelation(TelegramBotMessageMediaGateway::instance(), 'telegram_bot_message_id'));
        $schema->addUniqueKey(['telegram_bot_id', 'chat_id', 'message_id']);
        $schema->setStatic();
        return $schema;
    }

}