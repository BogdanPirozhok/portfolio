<?php

namespace App\Models\Common;

use App\Contracts\Notificationable;
use App\Enums\NotificationEnum;
use App\Models\Post\Post;
use App\Models\User\User;
use App\Scopes\WithoutDeletedUserScope;
use App\Traits\HasTranslations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer id
 * @property integer user_id
 * @property ?integer initial_user_id
 * @property ?integer post_id
 * @property ?integer comment_id
 *
 * @property bool is_read
 *
 * @property ?string slug
 * @property ?string title
 * @property ?string text
 * @property ?string link
 *
 * @property ?string notice_title
 * @property ?string notice_text
 * @property array notice_links
 *
 * @property ?User user
 * @property ?User recipient
 * @property ?Post post
 *
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Notification extends Model implements Notificationable
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'user_id',
        'initial_user_id',
        'post_id',
        'comment_id',
        'title',
        'text',
        'is_read',
        'slug',
        'link',
    ];

    public array $translatable = [
        'title',
        'text',
    ];

    protected $casts = [
        'is_read' => 'boolean'
    ];

    protected $with = [
        'post',
        'user',
        'user.profile',
        'user.profile.file',
        'user.profile.country',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new WithoutDeletedUserScope);
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initial_user_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function isSystemNotice(): bool
    {
        return $this->slug === NotificationEnum::TYPE_SYSTEM;
    }

    public function getNoticeTitleAttribute(): ?string
    {
        if ($this->isSystemNotice()) {
            return $this->title;
        }

        if ($this->relationLoaded('user')) {
            return $this->user->profile->full_name;
        }

        return null;
    }

    public function getNoticeTextAttribute(): ?string
    {
        if ($this->isSystemNotice()) {
            return $this->text;
        }

        $lang = app()->getLocale() ?? 'ru';

        if ($this->relationLoaded('post') && $this->relationLoaded('user')) {
            $text = NotificationEnum::TYPES[$this->slug]['text'][$lang][$this->user->profile->gender] ?? '';

            $text .= match ($this->slug) {
                NotificationEnum::TYPE_NEW_COMMENT,
                NotificationEnum::TYPE_COMMENT_ANSWER,
                NotificationEnum::TYPE_POSITIVE_RATING_POST,
                NotificationEnum::TYPE_NEGATIVE_RATING_POST,
                NotificationEnum::TYPE_POSITIVE_RATING_COMMENT,
                NotificationEnum::TYPE_NEGATIVE_RATING_COMMENT => ' "' . $this->post->title . '"',
                default => ''
            };

            return trim($text);
        }

        return null;
    }

    public function getNoticeLinksAttribute(): array
    {
        if ($this->isSystemNotice()) {
            return [
                NotificationEnum::LINK_TYPE_OTHER => $this->link
            ];
        }

        $linksPattern = NotificationEnum::TYPES[$this->slug]['links'];
        $links = [];

        if ($this->relationLoaded('user') && $this->relationLoaded('post')) {
            foreach ($linksPattern as $link) {
                switch ($link) {
                    case NotificationEnum::LINK_TYPE_PROFILE:
                        $links[$link] = '/profile/'. $this->user->id;
                        break;
                    case NotificationEnum::LINK_TYPE_COMMENT:
                        // $links[NotificationEnum::LINK_TYPE_OTHER] = '/article/'. $this->post->id .'/#comment';
                        $links[NotificationEnum::LINK_TYPE_OTHER] = '/article/'. $this->post->id;
                        break;
                    case NotificationEnum::LINK_TYPE_POST:
                        $links[NotificationEnum::LINK_TYPE_OTHER] = '/article/'. $this->post->id;
                        break;
                }
            }
        }

        return $links;
    }

    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    public function getNoticeTitle(): string
    {
        return $this->notice_title;
    }

    public function getNoticeText(): string
    {
        return $this->notice_text;
    }

    public function getNoticeLink(): ?string
    {
        return $this->notice_links[NotificationEnum::LINK_TYPE_OTHER]
            ?? $this->notice_links[NotificationEnum::LINK_TYPE_PROFILE]
            ?? null;
    }

    public function getNoticeType(): string
    {
        return Notificationable::COMMON_NOTICE_TYPE;
    }
}
