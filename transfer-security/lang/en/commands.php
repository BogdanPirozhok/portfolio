<?php

use App\Services\Commands\AddAgentCommand;
use App\Services\Commands\CheckAgentCommand;
use App\Services\Commands\ComplaintCommand;
use App\Services\Commands\HelpCommand;
use App\Services\Commands\StartCommand;

return [
    ...StartCommand::getDictionary('en'),
    ...CheckAgentCommand::getDictionary('en'),
    ...ComplaintCommand::getDictionary('en'),
    ...AddAgentCommand::getDictionary('en'),
    ...HelpCommand::getDictionary('en'),

    'base_command' => [
        'only_private_chat' => 'Бот <b>:name</b> не выполняет команды в публичных группах и каналах. Его цель - уведомлять учасников чата об угрозе мошеничества'
    ]
];
