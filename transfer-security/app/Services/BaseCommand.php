<?php

namespace App\Services;

use App\Helpers\CommandHelper;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use JetBrains\PhpStorm\NoReturn;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Objects\Update;

/**
 * @property string name
 * @property string commandPattern
 *
 * @method run()
 */
class BaseCommand extends Command
{
    const REDIS_KEY_LAST_COMMAND = 'last_command_in_chat:';

    #[NoReturn]
    public function handle()
    {
        $this->checkPrivateChat();
        $this->setLanguage();

        $this->replyWithChatAction([
            'action' => Actions::TYPING
        ]);

        sleep(1);
        $this->run();

        Redis::set(
            self::REDIS_KEY_LAST_COMMAND . $this->getUpdate()
                ->getChat()
                ->id,
            $this->getName()
        );
    }

    private function setLanguage(): void
    {
        $lang = $this->getUpdate()
            ->getMessage()
            ->getFrom()
            ->getLanguageCode();

        if (in_array($lang, config('app.locales', []))) {
            app()->setLocale($lang);
        }
    }

    private function checkPrivateChat(): void
    {
        $check = CommandHelper::isPrivateChat($this->getUpdate());

        if (!$check) {
            $this->replyWithMessage([
                'parse_mode' => 'HTML',
                'text' => __('commands.base_command.only_private_chat', [
                    'name' => config('app.name')
                ]),
            ]);

            exit();
        }
    }

    protected function checkFirstInit(Update $update): bool
    {
        $attribute = $update->callback_query->data
            ?? $update->message->text
            ?? $this->name;

        return $attribute === $this->name
            || $attribute === $this->commandPattern;
    }

    protected function validate(string|int $input, string $message, array $rules): void
    {
        $validate = Validator::make(
            [
                'input' => $input
            ],
            [
                'input' => $rules
            ]
        );

        if ($validate->errors()->count()) {
            $this->replyWithMessage([
                'parse_mode' => 'HTML',
                'text' => $message
            ]);

            exit();
        }
    }

    protected function clearRedis(string $key, int $chatId): void
    {
        Redis::del($key . $chatId);
    }
}
