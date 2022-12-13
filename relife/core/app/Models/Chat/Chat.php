<?php

namespace App\Models\Chat;

use App\Contracts\Available;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

/**
 * Class Chat
 * @package App\Models\Chat
 *
 * @property int id
 *
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 *
 * @property null|Collection|User[] users
 * @property null|Collection|ChatMessage[] messages
 * @method static create()
 */

class Chat extends Model implements Available
{
    use HasFactory, HasEagerLimit;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'chat_users',
            'chat_id',
            'user_id'
        )->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)
            ->orderBy('sent_at', 'desc');
    }

    public function getOwnerIds(): array
    {
        return $this->users->pluck('id')->toArray();
    }

    public function getAvailableMessage(): string
    {
        return __('errors.not_available_in_chat');
    }
}
