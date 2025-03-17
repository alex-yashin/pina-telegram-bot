<?php


namespace PinaTelegramBot\SQL;


use Pina\Data\Schema;
use Pina\TableDataGateway;
use Pina\Types\StringType;
use PinaTelegramBot\Types\SecretStringType;

class TelegramBotGateway extends TableDataGateway
{
    protected static $table = "telegram_bot";

    /**
     * @return Schema
     * @throws \Exception
     */
    public function getSchema()
    {
        $schema = parent::getSchema();
        $schema->addAutoincrementPrimaryKey();
        $schema->add('username', 'Bot username', StringType::class)->setMandatory();
        $schema->add('token', 'Bot token', SecretStringType::class)->setMandatory();
        $schema->add('intro', 'Приветственное сообщение', StringType::class)->setMandatory()->setDetailed()
            ->setDescription('Выводится в приватном чате в ответ на команду /start, если параметр не указан');
        $schema->addCreatedAt('Создано');
        $schema->addUniqueKey(['username']);
        return $schema;
    }

    public function selectTitle($alias = 'title')
    {
        return $this->selectAs('username', $alias);
    }
}