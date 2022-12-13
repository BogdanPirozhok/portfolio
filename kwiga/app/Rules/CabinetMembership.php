<?php

namespace App\Rules;

use App\Models\Cabinet\Cabinet;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class CabinetMembership
 * @package App\Rules
 *
 * Check if model (or models) are membership of cabinet
 */
class CabinetMembership implements Rule
{
    protected ?Collection $notMembership = null;
    protected string $attribute;

    /**
     * Create a new rule instance.
     * CabinetMembership constructor.
     *
     * @param Cabinet $cabinet
     * @param string $table
     * @param string $key
     *
     * @return void
     */
    public function __construct(protected Cabinet $cabinet, protected string $table, protected string $key = 'id')
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  int|string|array $value
     */
    public function passes($attribute, $value): bool
    {
        $modelIds = Arr::wrap($value);
        $membership = DB::table($this->table)
            ->whereIn($this->key, $modelIds)
            ->where('cabinet_id', $this->cabinet->id)
            ->get();
        $this->notMembership = collect($modelIds)->diff($membership->pluck($this->key));

        return !$this->notMembership->count();
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        $notMembershipIds = $this->notMembership;
        $cabinetId = $this->cabinet->id;

        return "Some entities of {$this->table} are not membership of the cabinet #{$cabinetId}: {$notMembershipIds}";
    }
}
