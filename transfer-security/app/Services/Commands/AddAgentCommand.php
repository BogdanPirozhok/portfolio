<?php

namespace App\Services\Commands;

use App\Interfaces\CommandServiceInterface;
use App\Models\Agent;
use App\Models\Country;
use App\Models\User;
use App\Services\BaseCommand;
use Illuminate\Support\Facades\Redis;
use JetBrains\PhpStorm\ArrayShape;
use Telegram\Bot\Keyboard\Keyboard;

class AddAgentCommand extends BaseCommand implements CommandServiceInterface
{
    const NAME = 'add_agent';
    const LOCALE_KEY = 'commands.' . self::NAME;
    const REDIS_KEY_AGENT_ID = 'agent_id:';
    const REDIS_KEY_CURRENT_USER = 'username:';
    const IS_PUBLIC_COMMAND = false;

    protected string $commandPattern = '/' . self::NAME;

    protected $name = self::NAME;
    protected $description;

    protected User $user;

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
                'description' => 'Добавить доверенного агента',
                'inputs' => [
                    'username' => 'Введите имя пользователя, в формате: <b>agent_username</b>',
                    'country' => 'Если агент работает в ограниченном регионе, укажите одну или несколько стран',
                    'more_country' => 'Добавьте еще одну страну, если это необходимо',
                ],
                'validate' => [
                    'username' => 'Имя пользователя указано не верно',
                    'auth' => 'У вас нет доступа для выполнения этого действия',
                    'country' => 'Указанной странны нет в списке, проверьте правильность ввода',
                    'user_exists' => 'Этот пользователь уже добавлен в систему. Введите другой логин'
                ],
                'actions' => [
                    'not_country' => 'Завершить',
                    'add_new_agent_txt' => 'Хотите добавить еще одного?',
                    'add_new_agent_btn' => 'Да'
                ],
                'finished' => 'Агент успешно добавлен. Благодарим за полезную информацию'
            ],
            'en' => [
                'description' => 'Добавить доверенного агента',
                'inputs' => [
                    'username' => 'Введите имя пользователя, в формате: <b>agent_username</b>',
                    'country' => 'Если агент работает в ограниченном регионе, укажите одну или несколько стран',
                    'more_country' => 'Добавьте еще одну страну, если это необходимо',
                ],
                'validate' => [
                    'username' => 'Имя пользователя указано не верно',
                    'auth' => 'У вас нет доступа для выполнения этого действия',
                    'country' => 'Указанной странны нет в списке, проверьте правильность ввода',
                    'user_exists' => 'Этот пользователь уже добавлен в систему. Введите другой логин'
                ],
                'actions' => [
                    'not_country' => 'Завершить',
                    'add_new_agent_txt' => 'Хотите добавить еще одного?',
                    'add_new_agent_btn' => 'Да'
                ],
                'finished' => 'Агент успешно добавлен. Благодарим за полезную информацию'
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
        $username = Redis::get(self::REDIS_KEY_CURRENT_USER . $chat->id)
            ?? $message->getFrom()->getUsername()
            ?? null;

        $this->auth($username);

        if ($this->checkFirstInit($update)) {
            $this->clearRedis(self::REDIS_KEY_AGENT_ID, $chat->id);

            $this->replyWithMessage([
                'parse_mode' => 'HTML',
                'text' => __(self::LOCALE_KEY . '.inputs.username'),
                'reply_markup' => Keyboard::remove()
            ]);
        } else {
            $agentId = Redis::get(self::REDIS_KEY_AGENT_ID . $chat->id);

            if (!$agentId) {
                $this->createAgent($message->text, $chat->id);
            } else if ($agentId > 0) {
                $this->addAgentCountry($message->text, $chat->id, $username, $agentId);
            } else {
                $this->showFinishedMessage();
            }
        }
    }

    private function auth(?string $username = null): void
    {
        if ($username) {
            /** @var null|User $user */
            $user = User::query()
                ->where('username', '=', $username)
                ->first();

            if ($user) {
                $this->user = $user;
                return;
            }
        }

        $this->replyWithMessage([
            'parse_mode' => 'HTML',
            'text' => __(self::LOCALE_KEY . '.validate.auth'),
            'reply_markup' => Keyboard::remove()
        ]);

        exit();
    }

    private function getCountriesKeyboard(Agent $agent): Keyboard
    {
        $countries = Country::query()
            ->whereNotIn('id', $agent->countries->pluck('id'))
            ->pluck('name')
            ->chunk(2);

        $keyboard = Keyboard::make(['resize_keyboard' => true]);

        $keyboard->row(
            Keyboard::inlineButton([
                'text' => __(self::LOCALE_KEY . '.actions.not_country')
            ]),
        );

        foreach ($countries as $countryCoupe) {
            $countryCoupe = $countryCoupe->values();

            if (isset($countryCoupe[1])) {
                $keyboard->row(
                    Keyboard::inlineButton([
                        'text' => $countryCoupe[0]
                    ]),
                    Keyboard::inlineButton([
                        'text' => $countryCoupe[1]
                    ])
                );
            } else {
                $keyboard->row(
                    Keyboard::inlineButton([
                        'text' => $countryCoupe[0]
                    ])
                );
            }
        }

        return $keyboard;
    }

    private function createAgent(string $agentUsername, int $chatId): void
    {
        $agentUsername = mb_strtolower(
            str_replace('@', '', trim($agentUsername))
        );

        $this->validate($agentUsername, __(self::LOCALE_KEY . '.validate.username'), [
            'string',
            'min:2'
        ]);

        $checkUser = $this->user->agents()
            ->where('username', '=', $agentUsername)
            ->exists();

        if (!$checkUser) {
            /** @var Agent $agent */
            $agent = $this->user->agents()->create([
                'username' => $agentUsername
            ]);

            Redis::set(self::REDIS_KEY_AGENT_ID . $chatId, $agent->id);

            $this->getCountriesKeyboard($agent);

            $this->replyWithMessage([
                'parse_mode' => 'HTML',
                'text' => __(self::LOCALE_KEY . '.inputs.country'),
                'reply_markup' => $this->getCountriesKeyboard($agent)
            ]);
        } else {
            $this->replyWithMessage([
                'parse_mode' => 'HTML',
                'text' => __(self::LOCALE_KEY . '.validate.user_exists'),
                'reply_markup' => Keyboard::remove()
            ]);
        }
    }

    private function addAgentCountry(string $input, int $chatId, string $username, int $agentId)
    {
        if ($input === __(self::LOCALE_KEY . '.actions.not_country')) {
            Redis::set(self::REDIS_KEY_AGENT_ID . $chatId, -1);
            Redis::set(self::REDIS_KEY_CURRENT_USER . $chatId, $username);

            $this->showFinishedMessage();
        } else {
            /** @var Country $country */
            $country = Country::query()
                ->where('name', 'like', '%' . $input . '%')
                ->first();

            /** @var Agent $agent */
            $agent = Agent::query()->find($agentId);

            if ($country) {
                $agent->countries()->attach($country->id);
                $agent->save();

                $this->replyWithMessage([
                    'parse_mode' => 'HTML',
                    'text' => __(self::LOCALE_KEY . '.inputs.more_country'),
                    'reply_markup' => $this->getCountriesKeyboard($agent)
                ]);
            } else {
                $this->replyWithMessage([
                    'parse_mode' => 'HTML',
                    'text' => __(self::LOCALE_KEY . '.validate.country'),
                    'reply_markup' => $this->getCountriesKeyboard($agent)
                ]);
            }
        }
    }

    private function showFinishedMessage(): void
    {
        $this->replyWithMessage([
            'parse_mode' => 'HTML',
            'text' => __(self::LOCALE_KEY . '.finished'),
            'reply_markup' => Keyboard::remove()
        ]);

        $this->replyWithMessage([
            'parse_mode' => 'HTML',
            'text' => __(self::LOCALE_KEY . '.actions.add_new_agent_txt'),
            'reply_markup' => Keyboard::make(['resize_keyboard' => true])
                ->inline()
                ->row(
                    Keyboard::inlineButton([
                        'text' => __(self::LOCALE_KEY . '.actions.add_new_agent_btn'),
                        'callback_data' => self::NAME
                    ]),
                )
        ]);
    }
}
