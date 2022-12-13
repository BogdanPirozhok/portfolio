<?php

namespace App\Models\Cabinet;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class CabinetDomain.
 *
 * @package namespace App\Models\Cabinet;
 *
 * @property int $id
 * @property int $type_id
 * @property int $cabinet_site_id
 *
 * @property string|null $cloudflare_id
 * @property string $hostname
 *
 * @property array $dns_records
 * @property array $dns_errors
 *
 * @property bool $is_active
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $confirmed_at
 * @property Carbon $last_activation_attempt_at
 * @property Carbon $deleted_at
 *
 * @property Cabinet $cabinet
 * @property CabinetSite $site
 */
class CabinetDomain extends Model
{
    use SoftDeletes;

    final const ROOT_DOMAIN_TYPE = 1;
    final const SUB_DOMAIN_TYPE = 2;

    final const DOMAIN_TYPES = [
        self::ROOT_DOMAIN_TYPE,
        self::SUB_DOMAIN_TYPE
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type_id',
        'cloudflare_id',
        'hostname',
        'dns_records',
        'dns_errors',
        'is_active',
        'confirmed_at',
        'last_activation_attempt_at'
    ];

    protected $dates = [
        'confirmed_at',
        'last_activation_attempt_at',
    ];

    protected $casts = [
        'dns_records' => 'array',
        'dns_errors' => 'array',
        'type_id' => 'integer',
        'is_active' => 'boolean'
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(CabinetSite::class, 'cabinet_site_id', 'id');
    }

    public function isRootDomain(): bool
    {
        return $this->type_id === self::ROOT_DOMAIN_TYPE;
    }

    public function isConfirmed(): bool
    {
        return (bool) $this->confirmed_at;
    }

    public function isFailed(): bool
    {
        return (bool) $this->dns_errors;
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }
}
