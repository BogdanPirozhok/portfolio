<?php

namespace App\Interfaces;

interface CommandServiceInterface
{
    public static function getDictionary(string $lang): array;

    public function run(): void;
}
