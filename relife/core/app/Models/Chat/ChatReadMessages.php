<?php

namespace App\Models\Chat;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ChatReadMessages
 * @package App\Models\ChatReadMessages
 *
 * @property int id
 * @property int user_id
 * @property int chat_message_id
 *
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */

class ChatReadMessages extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_message_id',
        'user_id'
    ];
}
