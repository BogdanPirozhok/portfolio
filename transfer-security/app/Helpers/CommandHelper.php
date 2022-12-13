<?php

namespace App\Helpers;

use Telegram\Bot\Objects\Update;

class CommandHelper
{
    public static function isPrivateChat(Update $update): bool
    {
        return $update->getChat()?->type === 'private';
    }
}
