<?php

namespace App\Enums;

enum GenerationStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';

    public function getIcon(): string{
        return match($this){
            self::Pending => '<div class="loader"></div>',
            self::Processing => '<div class="loader"></div>',
            self::Completed => '<i>✔</i>',
            self::Failed => '<i class="error">❗</i>',
        };
    }
}
