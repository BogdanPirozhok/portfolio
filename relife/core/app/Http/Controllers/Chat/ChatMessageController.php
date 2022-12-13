<?php

namespace App\Http\Controllers\Chat;

use App\Contracts\Notificationable;
use App\Exceptions\AvailableException;
use App\Exceptions\RegularException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\ChatMessageStoreRequest;
use App\Http\Resources\Chat\ChatMessageResource;
use App\Http\Resources\User\UserSimpleResource;
use App\Models\Chat\Chat;
use App\Models\Chat\ChatMessage;
use App\Models\User\User;
use App\Services\Chat\ChatMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ChatMessageController extends Controller
{
    /**
     * @param ChatMessageService $chatMessageService
     */
    public function __construct(
        protected ChatMessageService $chatMessageService
    ) {
    }

    /**
     * @param Chat $chat
     * @return AnonymousResourceCollection
     */
    public function index(Chat $chat): AnonymousResourceCollection
    {
        return ChatMessageResource::collection(
            $this->chatMessageService->getMessages($chat)
        );
    }

    /**
     * @param Chat $chat
     * @param ChatMessageStoreRequest $request
     * @return ChatMessageResource
     * @throws AvailableException
     */
    public function store(Chat $chat, ChatMessageStoreRequest $request): ChatMessageResource
    {
        check_available_action($chat);

        return ChatMessageResource::make(
            $this->chatMessageService->store(
                $chat,
                $request->validated()
            )
        );
    }

    /**
     * @param ChatMessage $chatMessage
     * @return JsonResponse
     */
    public function destroy(ChatMessage $chatMessage): JsonResponse
    {
        $result = $this->chatMessageService->delete($chatMessage);

        return response()->json([
            'success' => $result
        ]);
    }

    /**
     * @param Chat $chat
     * @return UserSimpleResource
     */
    public function setRead(Chat $chat): UserSimpleResource
    {
        return UserSimpleResource::make(
            $this->chatMessageService->setRead($chat)
        );
    }

    /**
     * @throws RegularException
     */
    public function checkNewMessages(): JsonResponse
    {
        try {
            /** @var User $user */
            $user = auth()->user();
            $data = $user->getUnreadNotifications();

            return response()->json([
                'all_count_unread_notices' => $data['sum_all_count'],
                'has_unread_message' => !empty($data[Notificationable::CHAT_MESSAGE_TYPE]),
            ]);
        } catch (\Exception $exception) {
            throw new RegularException($exception->getMessage(), 500);
        }
    }
}
