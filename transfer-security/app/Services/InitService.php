<?php

namespace App\Services;

use App\Helpers\CommandHelper;
use App\Interfaces\CommandServiceInterface;
use Illuminate\Support\Facades\Redis;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class InitService
{
    private Update $update;
    private bool $isPrivateChat;

    public function __construct()
    {
        $this->update = Telegram::commandsHandler(true);
        $this->isPrivateChat = CommandHelper::isPrivateChat($this->update);

        $this->init();
    }

    private function init(): void
    {
        $this->handleCommand();
        $this->chatModeration();

        exit();
    }

    private function handleCommand()
    {
        if ($this->isNeedToHandle()) {
            $message = $this->update->getMessage();
            $chat = $this->update->getChat();
            $command = $this->getCommand($message->text);

            /**
             * Если в тексте сообщения обнаруженна команда, она обрабатывается встроенным механизмом,
             * дополнительных действий не требуется
             */
            if (!$command) {
                $collBack = $this->update->callback_query->data ?? null;

                if ($collBack) {
                    Telegram::triggerCommand($collBack, $this->update);
                } else {
                    $lastCommand = Redis::get(BaseCommand::REDIS_KEY_LAST_COMMAND . $chat->id);

                    if ($lastCommand) {
                        Telegram::triggerCommand($lastCommand, $this->update);
                    }
                }
            }
        }
    }

    private function isNeedToHandle(): bool
    {
        $checkMessage = $this->update->getMessage()->text ?? null;
        $checkCollBack = $this->update->callback_query ?? null;

        return $this->isPrivateChat && ($checkMessage || $checkCollBack);
    }

    private function getCommand(string $messageText): ?CommandServiceInterface
    {
        $commands = Telegram::getCommands();

        return $commands[substr(trim($messageText), 1)] ?? null;
    }

    private function chatModeration(): void
    {
        if (!$this->isPrivateChat) {
            new ChatModerationService($this->update);
        }
    }
}
