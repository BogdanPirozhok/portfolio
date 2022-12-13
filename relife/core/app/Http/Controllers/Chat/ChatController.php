<?php

namespace App\Http\Controllers\Chat;

use App\Exceptions\AvailableException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\ChatRequest;
use App\Http\Resources\Chat\ChatResource;
use App\Http\Resources\User\UserSimpleResource;
use App\Models\Chat\Chat;
use App\Services\Chat\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ChatController extends Controller
{
    /**
     * @param ChatService $chatService
     */
    public function __construct(
        protected ChatService $chatService
    ) {
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $response = $this->chatService->index();

        return ChatResource::collection($response);
    }

    /**
     * @param Chat $chat
     * @return UserSimpleResource
     */
    public function show(Chat $chat): UserSimpleResource
    {
        $response = $this->chatService->show($chat);

        if (!$response) {
            abort(404, __('main.404'));
        }

        return UserSimpleResource::make($response);
    }

    /**
     * @param ChatRequest $request
     * @return ChatResource
     * @throws AvailableException
     */
    public function store(ChatRequest $request): ChatResource
    {
        check_available_user($request->input('user_id'));

        $response = $this->chatService->store($request->validated());

        if (!$response) {
            abort(404, __('main.404'));
        }

        return ChatResource::make($response);
    }

    /**
     * @param Chat $chat
     * @return JsonResponse
     */
    public function destroy(Chat $chat): JsonResponse
    {
        $result = $this->chatService->delete($chat);

        return response()->json([
            'success' => $result
        ]);
    }


    /**
     * @param Chat $chat
     * @return AnonymousResourceCollection
     */
    public function getUsers(Chat $chat): AnonymousResourceCollection
    {
        return UserSimpleResource::collection(
            $this->chatService->getUsersPaginate($chat)
        );
    }
}
