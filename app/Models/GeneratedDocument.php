<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedDocument extends Model
{
    protected $fillable = [
        'user_id',
        'resume_text',
        'cover_letter_text',
        'status',
        'viewed'
    ];
}
