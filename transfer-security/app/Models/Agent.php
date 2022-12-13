<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property integer id
 * @property integer user_id
 * @property string username
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @property Country|Collection|Country[] countries
 * @property User user
 */
class Agent extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'username',
    ];

    /**
     *
     * @return BelongsToMany
     */
    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(
            Country::class,
            'agent_countries',
            'agent_id',
            'country_id'
        )->withTimestamps();
    }

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class);
    }
}
