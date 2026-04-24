<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'job_title',
        'job_description',
        'additional_instructions'
    ];
}
