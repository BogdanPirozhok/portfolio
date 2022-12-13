<?php

use Illuminate\Support\Collection;

function int_to_str_convert(Collection $items): Collection
{
    return $items->map(function ($item) {
        return (string) $item;
    });
}

function to_json(Collection $items, bool $isSingleQuotes = false): string
{
    $json = $items->toJson();

    if ($isSingleQuotes) {
        $json = str_replace('"', "'", $json);
    }

    return $json;
}
