<?php


namespace PinaTelegramBot\Collections;

use Pina\Data\DataCollection;
use Pina\Data\Schema;
use PinaTelegramBot\Commands\InstallTelegramWebhook;
use PinaTelegramBot\SQL\TelegramBotGateway;

use function Pina\Events\queue;

class TelegramBotCollection  extends DataCollection
{
    protected function makeQuery()
    {
        return TelegramBotGateway::instance();
    }

    public function getListSchema(): Schema
    {
        return parent::getListSchema()->forgetField('token');
    }

    public function add(array $data, array $context = []): string
    {
        $id = parent::add($data, $context);

        $webhookInstallator = queue(InstallTelegramWebhook::class);
        $webhookInstallator($id);

        return $id;
    }

    public function update(string $id, array $data, array $context = [], array $fields = []): string
    {
        $id = parent::update($id, $data, $context, $fields);

        $webhookInstallator = queue(InstallTelegramWebhook::class);
        $webhookInstallator($id);

        return $id;
    }

}