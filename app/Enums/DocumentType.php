<?php

namespace App\Enums;

enum DocumentType: string
{
    case Resume = 'resume';
    case CoverLetter = 'cover_letter';

    public function path(): string{
        return match($this){
            DocumentType::Resume => 'resume',
            DocumentType::CoverLetter => 'cover-letter',
        };
    }
}
