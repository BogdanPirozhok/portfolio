<?php

/**
 * @param string $path
 * @param array $params
 * @return string
 */
function client_url(string $path = '/', array $params = []): string
{
    $separator = $path[0] === '/' ? '' : '/';
    $path = config('custom.client.app_url') . $separator . $path;

    if (count($params)) {
        foreach ($params as $key => &$value) {
            $value = $key . '=' . $value;
        }
        $path .= '?' . implode('&', $params);
    }

    return $path;
}

function storage_url(string $path = '/'): string
{
    $separator = $path[0] === '/' ? '' : '/';
    return config('custom.relife_storage_url') . $separator . $path;
}
