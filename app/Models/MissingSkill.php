<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissingSkill extends Model
{
    protected $fillable = [
        'generated_document_id',
        'skill_name',
        'user_response',
    ];

    public function generatedDocument(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
