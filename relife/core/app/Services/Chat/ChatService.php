<?php

namespace App\Services\Chat;

use App\Models\Chat\Chat;
use App\Models\User\User;
use Illuminate\Pagination\LengthAwarePaginator;

class ChatService
{
    /**
     * @return LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator
    {
        /** @var User $user */
        $user = auth()->user();

        return Chat::query()
            ->whereIn('chats.id', $user->chats->pluck('id'))
            ->with('messages.reads')
            ->withWhereHas(
                'users',
                fn ($query) => $query->with([
                    'profile.file',
                    'profile.country',
                ])
                    ->where('users.id', '!=', $user->id)
            )
            ->orderByDesc('chats.updated_at')
            ->paginate(15);
    }


    /**
     * @param Chat $chat
     * @return User|null
     */
    public function show(Chat $chat): ?User
    {
        /** @var User $user */
        $user = auth()->user();

        /** @var User $returnedChat */
        $returnedChat = User::query()
            ->where('id', '!=', $user->id)
            ->with([
                'profile',
                'profile.file',
                'profile.country',
                'chats' => fn ($query) => $query->where('chats.id', $chat->id),
                'chats.messages',
                'chats.messages.reads',
            ])
            ->whereHas(
                'chats',
                fn ($query) => $query
                    ->where('chats.id', $chat->id)
            )
            ->first();

        return $returnedChat;
    }

    /**
     * @param array $request
     * @return null|Chat
     */
    public function store(array $request): ?Chat
    {
        /** @var User $user */
        $user = auth()->user();

        /** @var Chat $chat */
        $chat = $user->chats()
            ->whereHas(
                'users',
                fn ($query) => $query
                    ->where('users.id', $request['user_id'])
            )
            ?->first();

        if (!$chat) {
            $chat = Chat::create();
            $chat->users()->sync([$request['user_id'], auth()->id()]);
        }

        return $chat;
    }

    /**
     * @param Chat $chat
     * @return LengthAwarePaginator
     */
    public function getUsersPaginate(Chat $chat): LengthAwarePaginator
    {
        return $chat->users()->paginate();
    }

    /**
     * @param Chat $chat
     * @return bool
     */
    public function delete(Chat $chat): bool
    {
        return $chat->delete();
    }
}
