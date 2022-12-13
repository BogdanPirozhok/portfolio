<?php

namespace App\Contracts;

interface Available
{
    public function getOwnerIds(): array;

    public function getAvailableMessage(): string;
}
