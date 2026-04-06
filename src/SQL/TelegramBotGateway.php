<?php


namespace PinaTelegramBot\SQL;


use Exception;
use Pina\Data\Schema;
use Pina\TableDataGateway;
use Pina\Types\StringType;
use Pina\Types\URLType;
use PinaTelegramBot\Types\SecretStringType;

class TelegramBotGateway extends TableDataGateway
{
    public function getTable(): string
    {
        return "telegram_bot";
    }

    /**
     * @return Schema
     * @throws Exception
     */
    public function getSchema(): Schema
    {
        $schema = parent::getSchema();
        $schema->addAutoincrementPrimaryKey();
        $schema->add('username', 'Bot username', StringType::class)->setMandatory()->tag('title');
        $schema->add('token', 'Bot token', SecretStringType::class)->setMandatory();
        $schema->add('webhook_url', 'Webhook', URLType::class)->setMandatory();
        $schema->add('api_url', 'API URL', URLType::class)->setMandatory();
        $schema->add('file_api_url', 'File API URL', URLType::class)->setMandatory();
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