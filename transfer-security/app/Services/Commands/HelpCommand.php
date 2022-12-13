<?php

namespace App\Services\Commands;

use App\Interfaces\CommandServiceInterface;
use App\Models\User;
use App\Services\BaseCommand;
use JetBrains\PhpStorm\ArrayShape;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class HelpCommand extends BaseCommand implements CommandServiceInterface
{
    const NAME = 'help';
    const LOCALE_KEY = 'commands.' . self::NAME;
    const IS_PUBLIC_COMMAND = true;

    protected string $commandPattern = '/' . self::NAME;

    protected $name = self::NAME;
    protected $description;

    public function __construct()
    {
        $this->setDescription(__(self::LOCALE_KEY . '.description'));
    }

    #[ArrayShape([
        self::NAME => 'array'
    ])]
    public static function getDictionary($lang): array
    {
        $languages = [
            'ru' => [
                'description' => 'Список доступных комманд',
            ],
            'en' => [
                'description' => 'Список доступных комманд',
            ]
        ];

        return [
            self::NAME => $languages[$lang]
        ];
    }

    public function run(): void
    {
        $username = $this->getUpdate()
            ->getMessage()
            ->getFrom()
            ->getUsername();

        $checkGuard = User::query()
            ->where('username', '=', $username)
            ->exists();

        $commands = Telegram::getCommands();
        $response = '';

        /** @var CommandServiceInterface $command */
        foreach ($commands as $command) {
            if ($command::IS_PUBLIC_COMMAND || $checkGuard) {
                $response .= sprintf(
                    '/%s - %s' . PHP_EOL,
                    $command->getName(),
                    $command->getDescription()
                );
            }
        }

        $this->replyWithMessage([
            'parse_mode' => 'HTML',
            'text' => $response,
            'reply_markup' => Keyboard::remove()
        ]);
    }
}
