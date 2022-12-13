<?php

namespace App\Models\Chat;

use App\Contracts\Notificationable;
use App\Enums\NotificationEnum;
use App\Models\User\User;
use App\Scopes\WithoutDeletedUserScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class ChatMessage
 * @package App\Models\ChatMessage
 *
 * @property int id
 * @property int user_id
 * @property int chat_id
 *
 * @property string text
 *
 * @property Carbon sent_at
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 *
 * @property ?Chat chat
 * @property ?User user
 * @property null|Collection|ChatReadMessages[] reads
 * @method static find(int $messageId)
 */

class ChatMessage extends Model implements Notificationable
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chat_id',
        'text',
        'sent_at',
    ];

    protected $with = [
        'user',
    ];

    protected $dates = [
        'sent_at',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new WithoutDeletedUserScope);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)
            ->with([
                'profile',
                'profile.file',
                'profile.country',
            ]);
    }

    /**
     * @return BelongsTo
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * @return HasMany
     */
    public function reads(): HasMany
    {
        return $this->hasMany(ChatReadMessages::class);
    }

    /**
     * @return bool
     */
    public function isRead(): bool
    {
        return (bool) $this->reads
            ->where('user_id', auth()->id())
            ->first();
    }

    public function getRecipient(): ?User
    {
        if ($this->relationLoaded('chat')) {
            return $this->chat
                ->users()
                ->where('users.id', '!=', $this->user_id)
                ->first();
        }

        return null;
    }

    public function getNoticeTitle(): string
    {
        return $this->user->profile->full_name;
    }

    public function getNoticeText(): string
    {
        return Str::limit($this->text, 120);
    }

    public function getNoticeLink(): ?string
    {
        return '/chat/' . $this->chat_id;
    }

    public function getNoticeType(): string
    {
        return Notificationable::CHAT_MESSAGE_TYPE;
    }
}
