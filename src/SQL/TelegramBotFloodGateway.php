<?php

namespace PinaTelegramBot\SQL;

use Pina\Data\Schema;
use Pina\TableDataGateway;
use Pina\Types\EnabledType;
use Pina\Types\StringType;
use Pina\Types\TimestampType;
use PinaTelegramBot\Types\CodeTokenType;
use PinaTelegramBot\Types\TelegramBotType;

class TelegramBotFloodGateway extends TableDataGateway
{
    protected static $table = "telegram_bot_flood";

    /**
     * @return Schema
     * @throws \Exception
     */
    public function getSchema()
    {
        $schema = parent::getSchema();
        $schema->addAutoincrementPrimaryKey();
        $schema->add('telegram_bot_id', 'Телеграм бот', TelegramBotType::class)->setMandatory();
        $schema->add('title', 'Наименование', StringType::class)->setMandatory();
        $schema->add('preg', 'Регулярное выражение', CodeTokenType::class)->setMandatory();
        $schema->add('replace', 'Ответ', CodeTokenType::class)->setMandatory();
        $schema->add('enabled', 'Активно', EnabledType::class);
        $schema->addCreatedAt();
        $schema->add('sent_at', 'Последняя отправка', TimestampType::class)
            ->setNullable()->setStatic();
        return $schema;
    }

    public function whereActual()
    {
        return $this->whereBy('enabled', 'Y')
            ->where($this->getAlias() . '.sent_at IS NULL OR ' . $this->getAlias() . '.sent_at < date_sub(NOW(), INTERVAL 1 DAY)');
    }

    public function markAsSent()
    {
        return $this->updateOperation($this->getAlias() . '.sent_at=NOW()');
    }


}