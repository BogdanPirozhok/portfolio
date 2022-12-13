<?php

namespace App\Enums\Cabinet;

enum CabinetEmailSettingsLangType: string
{
    case RECIPIENT = 'recipient'; // recipient's language
    case SPECIFIC = 'specific'; // specific language
}
