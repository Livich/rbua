<?php
require_once "init.php";

#$api = new \TelegramBot\Api\BotApi(TOKEN);
#print_r($api->setWebhook('https://lviv.lilikovych.name:88/rbua/index.php', new CURLFile('./telegram.pem')));

$bot = new \TelegramBot\Api\Client(TOKEN);


$bot->command('subscribe', function ($message) use ($bot, $subscribers) {
    $subscribers->add($message->getChat()->getId());
    $bot->sendMessage($message->getChat()->getId(), "You have subscribed");
});

$bot->command('unsubscribe', function ($message) use ($bot, $subscribers) {
    $subscribers->del($message->getChat()->getId());
    $bot->sendMessage($message->getChat()->getId(), "You have unsubscribed");
});

$bot->command('help', function ($message) use ($bot) {
    $bot->sendMessage($message->getChat()->getId(), "/subscribe\n/unsubscribe\n/about");
});

$bot->command('about', function ($message) use ($bot) {
    $bot->sendMessage($message->getChat()->getId(), "Author: @Livich");
});

$bot->run();
