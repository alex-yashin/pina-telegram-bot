<?php

namespace PinaTelegramBot\SQL;

use Pina\Data\Schema;
use Pina\TableDataGateway;
use Pina\Types\IntegerType;
use PinaMedia\Types\MediaType;

class TelegramBotMessageMediaGateway extends TableDataGateway
{
    public function getTable(): string
    {
        return "telegram_bot_message_media";
    }

    /**
     * @return Schema
     * @throws \Exception
     */
    public function getSchema(): Schema
    {
        $schema = parent::getSchema();
        $schema->add('telegram_bot_message_id', 'Сообщение', IntegerType::class);
        $schema->add('media_id', 'Медиа', MediaType::class);
        $schema->add('order', 'Порядок', IntegerType::class);
        return $schema;
    }

}