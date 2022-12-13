<?php

namespace App\Services\Commands;

use App\Interfaces\CommandServiceInterface;
use App\Models\Agent;
use App\Repositories\ComplaintRepository;
use App\Services\BaseCommand;
use JetBrains\PhpStorm\ArrayShape;
use Telegram\Bot\Keyboard\Keyboard;

class CheckAgentCommand extends BaseCommand implements CommandServiceInterface
{
    const NAME = 'check_agent';
    const LOCALE_KEY = 'commands.' . self::NAME;
    const IS_PUBLIC_COMMAND = true;

    protected $name = self::NAME;

    protected $description;

    protected string $commandPattern = '/' . self::NAME;

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
                'description' => 'Проверить агента',
                'check_agent_input' => 'Введите имя пользователя, в формате: <b>agent_username</b>',
                'founded' => [
                    'count' => 'На данного агента найдены жалобы - :count шт.',
                    'count_from_user' => 'ВНИМАНИЕ!!! В отношении пользователя :username найдены жалобы - :count шт.',
                    'title' => '<b>Последние жалобы:</b>',
                    'content' => 'Жалоба от :date:',
                    'not_comment' => '<em>(отсутствует)</em>',
                    'label' => 'Комментарий: '
                ],
                'reliable_user' => 'Этот агент отмечен в нашей системе как надежный',
                'undefined' => 'На этого агента не поступало жалоб, но у нас еще нет о нем ни какой информации. Будьте осторожны при осуществлении сделки'
            ],
            'en' => [
                'description' => 'Проверить агента',
                'check_agent_input' => 'Введите имя пользователя, в формате: <b>agent_username</b>',
                'founded' => [
                    'count_from_user' => 'ВНИМАНИЕ!!! В отношении пользователя :username найдены жалобы - :count шт.',
                    'title' => '<b>Последние жалобы:</b>',
                    'content' => 'Жалоба от :date:',
                    'not_comment' => '<em>(отсутствует)</em>',
                    'label' => 'Комментарий: '
                ],
                'not_comment' => '<em>(нет комментария)</em>',
                'reliable_user' => 'Этот агент отмечен в нашей системе как надежный',
                'undefined' => 'На этого агента не поступало жалоб, но у нас еще нет о нем ни какой информации. Будьте осторожны при осуществлении сделки'
            ],
        ];

        return [
            self::NAME => $languages[$lang]
        ];
    }

    public function run(): void
    {
        $update = $this->getUpdate();
        $message = $update->getMessage();

        if ($this->checkFirstInit($update)) {
            $this->replyWithMessage([
                'parse_mode' => 'HTML',
                'text' => __(self::LOCALE_KEY . '.check_agent_input'),
                'reply_markup' => Keyboard::remove()
            ]);
        } else {
            $username = mb_strtolower(
                str_replace('@', '', trim($message->text))
            );

            /** @var ComplaintRepository $complaintRepository */
            $complaintRepository = resolve(ComplaintRepository::class);
            $complaint = $complaintRepository->getComplaintsMessage($username);

            if ($complaint['total']) {
                $this->replyWithMessage([
                    'parse_mode' => 'HTML',
                    'text' => __(self::LOCALE_KEY . '.founded.count', [
                        'count' => $complaint['total']
                    ]),
                ]);

                $this->replyWithMessage([
                    'parse_mode' => 'HTML',
                    'text' => $complaint['message'],
                ]);
            } else {
                /** @var Agent $agent */
                $agent = Agent::query()
                    ->where('username', '=', $username)
                    ->first();

                if ($agent) {
                    $this->replyWithMessage([
                        'parse_mode' => 'HTML',
                        'text' => __(self::LOCALE_KEY . '.reliable_user'),
                    ]);
                } else {
                    $this->replyWithMessage([
                        'parse_mode' => 'HTML',
                        'text' => __(self::LOCALE_KEY . '.undefined'),
                    ]);
                }
            }
        }
    }
}
