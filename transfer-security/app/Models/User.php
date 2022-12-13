<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property integer id
 * @property string username
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @property Agent|Collection|Agent[] agents
 */
class User extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
    ];

    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class);
    }
}
