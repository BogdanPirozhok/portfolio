<?php

namespace App\Services\Cabinet;

use App\Exceptions\Common\RegularException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class DomainApi
 *
 * @package App\Services\Cabinet
 */
class DomainApi
{
    protected array $cloudflareConfig;

    public function __construct()
    {
        $this->cloudflareConfig = config('cloudflare', []);
    }

    /**
     * @throws Throwable
     */
    private function request(string $method, string $path, ?array $properties = null): array
    {
        $request = Http::withHeaders([
            'X-Auth-Email' => $this->cloudflareConfig['account_email'],
            'X-Auth-Key' => $this->cloudflareConfig['auth_key'],
        ]);

        try {
            $path = $this->cloudflareConfig['api_endpoint'] . '/' . $path;

            $response = match ($method) {
                'post' => $request->post($path, $properties),
                'get' => $request->get($path, $properties),
                'put' => $request->put($path, $properties),
                'patch' => $request->patch($path, $properties),
                'delete' => $request->delete($path, $properties),
                default => null,
            };

            if ($response) {
                if ($response->successful()) {
                    $responseData = $response->json();

                    if (isset($responseData['result']) && isset($responseData['success']) && $responseData['success']) {
                        return $responseData['result'];
                    }
                }
                throw new RegularException($response->body() ?? lang('cabinet.domain.failed_request'));
            }
            throw new RegularException(lang('cabinet.domain.failed_request'));
        } catch (Throwable $e) {
            $message = json_decode($e->getMessage());

            Log::error($e);
            if ($message) {
                if (is_countable($message->errors) ? count($message->errors) : 0) {
                    $error = $message->errors[0];

                    throw match ($error->code) {
                        1099, 1061, 1224 => new RegularException(lang('cabinet.domain.exceptions.' . $error->code)),
                        default => new RegularException($error->message),
                    };
                }
            }

            throw new RegularException($e);
        }
    }

    /**
     * @throws Throwable
     */
    public function addRootDomain(string $hostname): array
    {
        return $this->request(
            'post',
            'zones',
            ['name' => $hostname]
        );
    }

    /**
     * @throws Throwable
     */
    public function addSubDomain(string $hostname): array
    {
        return $this->request(
            'post',
            'zones/' . $this->cloudflareConfig['ssl_host_zone_id'] . '/custom_hostnames',
            [
                'hostname' => $hostname,
                'ssl' => [
                    'method' => 'http',
                    'type' => 'dv',
                    'settings' => [
                        'http2' => 'on',
                        'min_tls_version' => '1.0',
                        'tls_1_3' => 'off',
                        'early_hints' => 'on'
                    ],
                    'bundle_method' => 'ubiquitous',
                    'wildcard' => false
                ]
            ]
        );
    }

    /**
     * @throws Throwable
     */
    public function activateRootDomain(string $domainId): array
    {
        return $this->request(
            'put',
            'zones/' . $domainId . '/activation_check'
        );
    }

    /**
     * @throws Throwable
     */
    public function checkSubDomain(string $domainId): array
    {
        return $this->request(
            'get',
            'zones/' . $this->cloudflareConfig['ssl_host_zone_id'] . '/custom_hostnames/' . $domainId
        );
    }

    /**
     * @throws Throwable
     */
    public function deleteRootDomain(string $domainId): array
    {
        return $this->request(
            'delete',
            'zones/' . $domainId
        );
    }

    /**
     * @throws Throwable
     */
    public function deleteSubDomain(string $domainId): array
    {
        return $this->request(
            'delete',
            'zones/' . $this->cloudflareConfig['ssl_host_zone_id'] . '/custom_hostnames/' . $domainId
        );
    }

    /**
     * @throws Throwable
     */
    public function setRecord(string $domainId, array $properties): array
    {
        return $this->request(
            'post',
            'zones/' . $domainId . '/dns_records',
            $properties
        );
    }

    /**
     * @throws Throwable
     */
    public function checkRecords(string $domainId): array
    {
        return $this->request(
            'get',
            'zones/' . $domainId
        );
    }

    /**
     * @throws Throwable
     */
    public function setUseHTTPS(string $domainId): array
    {
        return $this->request(
            'patch',
            'zones/' . $domainId . '/settings/always_use_https',
            ['value' => 'on']
        );
    }
}
