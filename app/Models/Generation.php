<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Generation extends Model
{
    protected $fillable = [
        'application_id',
        'status',
        'resume_text',
        'cover_letter_text',
        'viewed',
        'additional_instructions'
    ];

    public function missingSkills(): HasMany
    {
        return $this->hasMany(MissingSkill::class);
    }
}
