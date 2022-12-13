<?php

namespace App\Services;

use App\Helpers\CommandHelper;
use App\Interfaces\CommandServiceInterface;
use App\Repositories\ComplaintRepository;
use Illuminate\Support\Facades\Redis;
use JetBrains\PhpStorm\NoReturn;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class ChatModerationService
{
    private Update $update;

    public function __construct(Update $update)
    {
        $this->update = $update;

        $this->handle();
    }

    private function handle(): void
    {
        $message = $this->update->getMessage();
        $chat = $this->update->getChat();

        $username = $message->getFrom()->getUsername();

        /** @var ComplaintRepository $complaintRepository */
        $complaintRepository = resolve(ComplaintRepository::class);
        $complaint = $complaintRepository->getComplaintsMessage($username);

        if ($complaint['total']) {
            Telegram::sendMessage([
                'chat_id' => $chat->id,
                'parse_mode' => 'HTML',
                'text' => __('commands.check_agent.founded.count_from_user', [
                        'username' => '@' . $username,
                        'count' => $complaint['total']
                    ])
                    . "\n\n" . $complaint['message'],
            ]);
        }
    }
}
