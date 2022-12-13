<?php

namespace App\Models\Common;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer id
 * @property string type
 * @property string link
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 */

class File extends Model
{
    use HasFactory;

    const FILE_TYPE_IMAGE = 1;
    const FILE_TYPE_VIDEO = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['type', 'link'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
