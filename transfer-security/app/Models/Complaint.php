<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property integer id
 * @property string defendant_username
 * @property ?string complainant_username
 * @property ?string cause_text
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Complaint extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cause_text',
        'defendant_username',
        'complainant_username',
    ];
}
