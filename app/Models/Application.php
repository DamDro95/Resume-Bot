<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'job_title',
        'job_description',
    ];

    public function generations(): HasMany
    {
        return $this->hasMany(Generation::class);
    }
}
