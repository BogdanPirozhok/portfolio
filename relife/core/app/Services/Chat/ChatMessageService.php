<?php

namespace App\Services\Chat;

use App\Models\Chat\Chat;
use App\Models\Chat\ChatMessage;
use App\Models\User\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ChatMessageService
{
    /**
     * @param int $messageId
     * @return ChatMessage|null
     */
    public function findById(int $messageId): ?ChatMessage
    {
        return ChatMessage::find($messageId);
    }

    /**
     * @param Chat $chat
     * @return LengthAwarePaginator
     */
    public function getMessages(Chat $chat): LengthAwarePaginator
    {
        return $chat->messages()
            ->with('reads')
            ->paginate(20);
    }

    /**
     * @param Chat $chat
     * @param array $request
     * @return ChatMessage|null
     */
    public function store(Chat $chat, array $request): ?ChatMessage
    {
        /** @var ChatMessage $message */
        $message = $chat->messages()
            ->create([
                'user_id' => auth()->id(),
                'text' => $request['text'],
                'sent_at' => $request['timestamp']
            ]);

        $chat->updated_at = now();
        $chat->save();

        return $this->findById($message->id)->loadMissing('reads');
    }

    /**
     * @param ChatMessage $chatMessage
     * @return bool
     */
    public function delete(ChatMessage $chatMessage): bool
    {
        return $chatMessage->delete();
    }

    /**
     * @param Chat $chat
     * @return null|User
     */
    public function setRead(Chat $chat): ?User
    {
        /** @var User $user */
        $user = auth()->user();

        $unreadMessageIds = $chat->messages()
            ->doesntHave(
                'reads',
                'and',
                fn (Builder $query) => $query->where('user_id', '=', $user->id)
            )
            ->pluck('id');

        $user->readMessages()
            ->where('chat_id', '=', $chat->id)
            ->syncWithoutDetaching($unreadMessageIds);

        /** @var ChatService $chatService */
        $chatService = resolve(ChatService::class);

        return $chatService->show($chat);
    }

    /**
     * @param User $user
     * @param bool $isNeedCountReturn
     * @return bool|int
     */
    public function checkNewMessages(User $user, bool $isNeedCountReturn = false): bool|int
    {
        $query = $user->chats()
            ->whereHas(
                'messages',
                fn (Builder $query) => $query
                    ->where('user_id', '!=', $user->id)
                    ->doesntHave(
                        'reads',
                        'and',
                        fn (Builder $query) => $query->where('user_id', '=', $user->id)
                    )
            );

        return $isNeedCountReturn
            ? $query->count()
            : $query->exists();
    }
}
