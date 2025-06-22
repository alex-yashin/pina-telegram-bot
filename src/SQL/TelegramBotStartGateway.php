<?php

namespace PinaTelegramBot\SQL;

use Pina\Data\Schema;
use Pina\TableDataGateway;
use Pina\Types\StringType;
use Pina\Types\TokenType;

class TelegramBotStartGateway extends TableDataGateway
{
    protected static $table = "telegram_bot_start";

    /**
     * @return Schema
     * @throws \Exception
     */
    public function getSchema()
    {
        $schema = parent::getSchema();
        $schema->addAutoincrementPrimaryKey();
        $schema->add('param', 'Код параметра /start', TokenType::class)->setMandatory()->tag('title');
        $schema->addUniqueKey(['param']);
        $schema->add('answer', 'Ответ', StringType::class)->setMandatory();
        return $schema;
    }

    public function whereSessionId($sessionId)
    {
        return $this->innerJoin(
            TelegramBotSessionGateway::instance()->on('param', 'param')->onBy('id', $sessionId)
        );
    }

}