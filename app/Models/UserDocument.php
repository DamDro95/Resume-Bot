<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
    protected $fillable = [
        'user_id',
        'documentType',
        'filename',
        'path',
    ];

    public function getPath(): string{
        return $this->path . '/' . $this->filename;
    }

}
