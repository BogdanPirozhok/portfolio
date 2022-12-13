<?php

namespace App\Services\Commands;

use App\Interfaces\CommandServiceInterface;
use App\Models\Complaint;
use App\Services\BaseCommand;
use Illuminate\Support\Facades\Redis;
use JetBrains\PhpStorm\ArrayShape;
use Telegram\Bot\Keyboard\Keyboard;

class ComplaintCommand extends BaseCommand implements CommandServiceInterface
{
    const NAME = 'complain';
    const LOCALE_KEY = 'commands.' . self::NAME;
    const REDIS_KEY_COMPLAINT_ID = 'complaint_id:';
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
                'description' => 'Подать жалобу на агента',
                'inputs' => [
                    'defendant_username' => 'Введите имя пользователя, в формате: <b>agent_username</b>',
                    'cause_text' => 'Пожалуйста, коротко, опишите причину жалобы',
                ],
                'validate' => [
                    'defendant_username' => 'Жалоба не принята. Имя пользователя указано не верно',
                    'cause_text' => 'Описание не принято. Текст должен содержать от 5-ти до 300-та символов',
                ],
                'finished' => 'Жалоба зафиксирована, багодарим вас за бдительность',
                'buttons' => [
                    'add_complain' => 'Подать еще одну жалобу?'
                ]
            ],
            'en' => [
                'description' => 'Подать жалобу на агента',
                'inputs' => [
                    'defendant_username' => 'Введите имя пользователя, в формате: <b>agent_username</b>',
                    'cause_text' => 'Пожалуйста, коротко, опишите причину жалобы',
                ],
                'validate' => [
                    'defendant_username' => 'Жалоба не принята. Имя пользователя указано не верно',
                    'cause_text' => 'Описание не принято. Текст должен содержать от 5-ти до 300-та символов',
                ],
                'finished' => 'Жалоба зафиксирована, багодарим вас за бдительность',
                'buttons' => [
                    'add_complain' => 'Подать еще одну жалобу?'
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
        $chat = $update->getChat();
        $message = $update->getMessage();

        if ($this->checkFirstInit($update)) {
            $this->clearRedis(self::REDIS_KEY_COMPLAINT_ID, $chat->id);

            $this->replyWithMessage([
                'parse_mode' => 'HTML',
                'text' => __(self::LOCALE_KEY . '.inputs.defendant_username'),
                'reply_markup' => Keyboard::remove()
            ]);
        } else {
            $complaintId = Redis::get(self::REDIS_KEY_COMPLAINT_ID . $chat->id);

            if (!$complaintId) {
                $this->setDefendantUsername(
                    $chat->id,
                    $message->text,
                    $message->getFrom()->getUsername() ?? null
                );
            } else if ($complaintId > 0) {
                $this->setCauseText(
                    $chat->id,
                    $complaintId,
                    $message->text
                );
            } else {
                $this->showFinishedMessage();
            }
        }
    }

    private function setDefendantUsername(
        int $chatId,
        string $defendantUsername,
        ?string $complainantUsername = null,
    ): void {
        $defendantUsername = mb_strtolower(
            str_replace('@', '', trim($defendantUsername))
        );

        $this->validate($defendantUsername, __(self::LOCALE_KEY . '.validate.defendant_username'), [
            'string',
            'min:2',
        ]);

        /** @var Complaint $complaint */
        $complaint = Complaint::query()
            ->create([
                'defendant_username' => strtolower($defendantUsername),
                'complainant_username' => strtolower($complainantUsername)
            ]);

        Redis::set(self::REDIS_KEY_COMPLAINT_ID . $chatId, $complaint->id);

        $this->replyWithMessage([
            'parse_mode' => 'HTML',
            'text' => __(self::LOCALE_KEY . '.inputs.cause_text')
        ]);
    }

    private function setCauseText(
        int $chatId,
        int $complaintId,
        string $causeText,
    ): void {
        $causeText = trim($causeText);

        $this->validate($causeText, __(self::LOCALE_KEY . '.validate.cause_text'), [
            'string',
            'min:5',
            'max:300'
        ]);

        Complaint::query()
            ->where('id', '=', $complaintId)
            ->update([
                'cause_text' => $causeText
            ]);

        Redis::set(self::REDIS_KEY_COMPLAINT_ID . $chatId, -1);

        $this->showFinishedMessage();
    }

    private function showFinishedMessage(): void
    {
        $this->replyWithMessage([
            'parse_mode' => 'HTML',
            'text' => __(self::LOCALE_KEY . '.finished'),
            'reply_markup' => Keyboard::make(['resize_keyboard' => true])
                ->inline()
                ->row(
                    Keyboard::inlineButton([
                        'text' => __(self::LOCALE_KEY . '.buttons.add_complain'),
                        'callback_data' => self::NAME
                    ]),
                )
        ]);
    }
}
