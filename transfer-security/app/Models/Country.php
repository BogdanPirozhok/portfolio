<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property integer id
 * @property string name
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Country extends Model
{
    use HasFactory, HasTranslations;

    /**
     * @var array|string[]
     */
    public array $translatable = ['name'];

    /**
     * @param $value
     * @return string
     */
    public function getNameAttribute($value): string
    {
        if ($json = json_decode($value)) {
            return $json->{app()->getLocale()};
        }
        return $value;
    }
}
