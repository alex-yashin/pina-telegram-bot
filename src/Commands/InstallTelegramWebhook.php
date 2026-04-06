<?php


namespace PinaTelegramBot\Commands;


use Pina\Command;
use PinaTelegramBot\Model\TelegramBot;

class InstallTelegramWebhook extends Command
{

    protected function execute($input = '')
    {
        $telegram = new TelegramBot($input);
        $telegram->initWebhook();
    }

}