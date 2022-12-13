<?php

namespace App\Models\Cabinet;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class CabinetSite.
 *
 * @package namespace App\Models\Cabinet;
 *
 * @property int $id
 * @property int $cabinet_id
 *
 * @property string $name
 * @property string $slug
 *
 * @property bool $is_default
 * @property bool $is_active
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleting_at
 * @property Carbon $deleted_at
 *
 * @property Cabinet $cabinet
 * @property CabinetDomain $domain
 * @property CabinetDomain $activeDomain
 */
class CabinetSite extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'is_default',
        'is_active',
        'deleting_at'
    ];

    protected $dates = [
        'deleting_at'
    ];

    protected $with = [
        'activeDomain',
        'domain',
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function cabinet(): BelongsTo
    {
        return $this->belongsTo(Cabinet::class);
    }

    public function domain(): HasOne
    {
        return $this->hasOne(CabinetDomain::class);
    }

    public function activeDomain(): HasOne
    {
        return $this->domain()->where('is_active', '=', true);
    }

    public function isDeleting(): bool
    {
        return (bool) $this->deleting_at;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }
}
