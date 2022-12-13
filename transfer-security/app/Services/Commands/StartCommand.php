<?php

namespace App\Services\Commands;

use App\Interfaces\CommandServiceInterface;
use App\Services\BaseCommand;
use JetBrains\PhpStorm\ArrayShape;
use Telegram\Bot\Keyboard\Keyboard;

class StartCommand extends BaseCommand implements CommandServiceInterface
{
    const NAME = 'start';
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
                'description' => 'Запустить бот',
                'message' => 'Здрасвуйте <b>:name</b>, приветствуем в <b>'. config('app.name') .'!</b>'
                    . "\n\n" . 'Этот Бот предназначен для проверки на благонадежность, агентов предлагающих услуги обмена активами.',
                'actions' => [
                    'cta' => 'Укажите что бы вы хотели сделать:',
                    'not_input' => 'Необходимо выбрать действие'
                ],
                'buttons' => [
                    'check_agent' => 'Проверить агента',
                    'complain_agent' => 'Пожаловаться на агента',
                ]
            ],
            'en' => [
                'description' => 'Запустить бот',
                'message' => 'Здрасвуйте <b>:name</b>, приветствуем в <b>'. config('app.name') .'!</b>'
                    . "\n\n" . 'Этот Бот предназначен для проверки на благонадежность, агентов предлагающих услуги обмена активами.',
                'actions' => [
                    'cta' => 'Укажите что бы вы хотели сделать:',
                    'not_input' => 'Необходимо выбрать действие'
                ],
                'buttons' => [
                    'check_agent' => 'Проверить агента',
                    'complain_agent' => 'Пожаловаться на агента',
                ]
            ]
        ];

        return [
            self::NAME => $languages[$lang]
        ];
    }

    public function run(): void
    {
        $update = $this->getUpdate();

        $userFirstName = $update
            ->getMessage()
            ->getFrom()
            ->getFirstName();

        $keyboard = Keyboard::make(['resize_keyboard' => true])
            ->inline()
            ->row(
                Keyboard::inlineButton([
                    'text' => __(self::LOCALE_KEY . '.buttons.check_agent'),
                    'callback_data' => CheckAgentCommand::NAME
                ]),
                Keyboard::inlineButton([
                    'text' => __(self::LOCALE_KEY . '.buttons.complain_agent'),
                    'callback_data' => ComplaintCommand::NAME
                ])
            );

        if ($this->checkFirstInit($update)) {
            $this->replyWithMessage([
                'parse_mode' => 'HTML',
                'text' => __(self::LOCALE_KEY . '.message', [
                    'name' => $userFirstName
                ]),
                'reply_markup' => Keyboard::remove()
            ]);
        } else {
            $this->replyWithMessage([
                'parse_mode' => 'HTML',
                'text' => __(self::LOCALE_KEY . '.actions.not_input', [
                    'name' => $userFirstName
                ]),
                'reply_markup' => Keyboard::remove()
            ]);
        }

        $this->replyWithMessage([
            'parse_mode' => 'HTML',
            'text' => __(self::LOCALE_KEY . '.actions.cta'),
            'reply_markup' => $keyboard
        ]);
    }
}
