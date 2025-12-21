<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class DriveService
{
    /**
     * @return array{id: string, name: string, mime_type: string|null, size: int|null, checksum: string|null}
     */
    public function upload(string $localPath, string $name, ?string $mimeType, ?string $parentId = null): array
    {
        $metadata = [
            'name' => $name,
            'parents' => [$this->resolveParentId($parentId)],
        ];

        $url = $this->withSupportsAllDrives(
            $this->uploadBaseUrl().'/files?uploadType=multipart&fields=id,name,mimeType,size,md5Checksum'
        );

        $response = retry(
            $this->retryTimes(),
            function () use ($localPath, $name, $mimeType, $metadata, $url) {
                $fileStream = fopen($localPath, 'rb');

                if ($fileStream === false) {
                    throw new RuntimeException('Unable to open the document for upload.');
                }

                try {
                    return Http::withToken($this->accessToken())
                        ->acceptJson()
                        ->timeout($this->timeoutSeconds())
                        ->withOptions(['verify' => $this->sslVerify()])
                        ->attach(
                            'metadata',
                            json_encode($metadata, JSON_UNESCAPED_SLASHES),
                            'metadata.json',
                            ['Content-Type' => 'application/json; charset=UTF-8']
                        )
                        ->attach(
                            'file',
                            $fileStream,
                            $name,
                            ['Content-Type' => $mimeType ?: 'application/octet-stream']
                        )
                        ->post($url)
                        ->throw();
                } finally {
                    fclose($fileStream);
                }
            },
            $this->retrySleepMs()
        );

        return $this->normalizeFileResponse($response->json());
    }

    /**
     * @return array{id: string, name: string}
     */
    public function rename(string $fileId, string $name): array
    {
        $payload = [
            'name' => $name,
        ];

        $response = $this->request('patch', $this->withSupportsAllDrives(
            $this->driveBaseUrl().'/files/'.$fileId.'?fields=id,name'
        ), [
            'json' => $payload,
        ]);

        if (! isset($response['id'])) {
            throw new RuntimeException('Unable to rename the Drive file.');
        }

        return [
            'id' => $response['id'],
            'name' => $response['name'] ?? $name,
        ];
    }

    public function move(string $fileId, ?string $fromParentId, ?string $toParentId): void
    {
        $from = $this->resolveParentId($fromParentId);
        $to = $this->resolveParentId($toParentId);

        $this->request('patch', $this->withSupportsAllDrives(
            $this->driveBaseUrl().'/files/'.$fileId.'?addParents='.$to.'&removeParents='.$from.'&fields=id,parents'
        ), [
            'json' => new \stdClass,
        ]);
    }

    /**
     * @return array{id: string, name: string, mime_type: string|null, size: int|null, checksum: string|null}
     */
    public function copy(string $fileId, string $name, ?string $parentId = null): array
    {
        $payload = [
            'name' => $name,
            'parents' => [$this->resolveParentId($parentId)],
        ];

        $response = $this->request('post', $this->withSupportsAllDrives(
            $this->driveBaseUrl().'/files/'.$fileId.'/copy?fields=id,name,mimeType,size,md5Checksum'
        ), [
            'json' => $payload,
        ]);

        return $this->normalizeFileResponse($response);
    }

    /**
     * @return array{id: string, name: string}
     */
    public function createFolder(string $name, ?string $parentId = null): array
    {
        $payload = [
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => [$this->resolveParentId($parentId)],
        ];

        $response = $this->request('post', $this->withSupportsAllDrives(
            $this->driveBaseUrl().'/files?fields=id,name'
        ), [
            'json' => $payload,
        ]);

        if (! isset($response['id'])) {
            throw new RuntimeException('Unable to create Drive folder.');
        }

        return [
            'id' => $response['id'],
            'name' => $response['name'] ?? $name,
        ];
    }

    public function trash(string $fileId, ?string $fromParentId = null): void
    {
        $trashId = $this->trashFolderId();

        $this->move($fileId, $fromParentId, $trashId);
    }

    public function delete(string $fileId): void
    {
        $this->request('delete', $this->withSupportsAllDrives($this->driveBaseUrl().'/files/'.$fileId));
    }

    private function request(string $method, string $url, array $options = []): array
    {
        $response = retry(
            $this->retryTimes(),
            function () use ($method, $url, $options) {
                return Http::withToken($this->accessToken())
                    ->acceptJson()
                    ->timeout($this->timeoutSeconds())
                    ->withOptions(['verify' => $this->sslVerify()])
                    ->send(strtoupper($method), $url, $options)
                    ->throw();
            },
            $this->retrySleepMs()
        );

        return $response->json() ?? [];
    }

    private function normalizeFileResponse(array $payload): array
    {
        if (! isset($payload['id'])) {
            throw new RuntimeException('Drive response missing file id.');
        }

        return [
            'id' => $payload['id'],
            'name' => $payload['name'] ?? $payload['id'],
            'mime_type' => $payload['mimeType'] ?? null,
            'size' => isset($payload['size']) ? (int) $payload['size'] : null,
            'checksum' => $payload['md5Checksum'] ?? null,
        ];
    }

    private function accessToken(): string
    {
        return Cache::remember('drive.service-account.token', now()->addSeconds(3500), function () {
            $serviceAccount = $this->serviceAccount();
            $tokenUri = config('drive.token_uri');

            if (! $tokenUri) {
                throw new RuntimeException('Google Drive token URI is not configured.');
            }

            $now = time();
            $payload = [
                'iss' => $serviceAccount['client_email'],
                'scope' => 'https://www.googleapis.com/auth/drive',
                'aud' => $tokenUri,
                'exp' => $now + 3600,
                'iat' => $now,
            ];

            $impersonateUser = config('drive.impersonate_user');
            if (is_string($impersonateUser) && $impersonateUser !== '') {
                $payload['sub'] = $impersonateUser;
            }

            $jwt = $this->encodeJwt($payload, $serviceAccount['private_key']);

            $response = Http::asForm()
                ->timeout($this->timeoutSeconds())
                ->withOptions(['verify' => $this->sslVerify()])
                ->post($tokenUri, [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ])
                ->throw();

            $token = $response->json('access_token');

            if (! $token) {
                throw new RuntimeException('Unable to retrieve Google Drive access token.');
            }

            return $token;
        });
    }

    /**
     * @return array{client_email: string, private_key: string}
     */
    private function serviceAccount(): array
    {
        $credentialsPath = config('drive.credentials_path');

        if (is_string($credentialsPath) && $credentialsPath !== '') {
            $resolvedPath = $credentialsPath;

            if (! is_file($resolvedPath)) {
                $resolvedPath = base_path($credentialsPath);
            }

            if (! is_file($resolvedPath)) {
                throw new RuntimeException('Google Drive credentials file does not exist.');
            }

            $contents = file_get_contents($resolvedPath);

            if ($contents === false) {
                throw new RuntimeException('Unable to read Google Drive credentials file.');
            }

            $payload = json_decode($contents, true);

            if (! is_array($payload)) {
                throw new RuntimeException('Google Drive credentials file is invalid JSON.');
            }

            $email = $payload['client_email'] ?? null;
            $privateKey = $payload['private_key'] ?? null;

            if (! is_string($email) || $email === '' || ! is_string($privateKey) || $privateKey === '') {
                throw new RuntimeException('Google Drive credentials file is missing required fields.');
            }

            return [
                'client_email' => $email,
                'private_key' => $privateKey,
            ];
        }

        $serviceAccount = config('drive.service_account', []);
        $email = $serviceAccount['client_email'] ?? null;
        $privateKey = $serviceAccount['private_key'] ?? null;

        if (! is_string($email) || $email === '' || ! is_string($privateKey) || $privateKey === '') {
            throw new RuntimeException('Google Drive service account credentials are not configured.');
        }

        return [
            'client_email' => $email,
            'private_key' => $privateKey,
        ];
    }

    private function encodeJwt(array $payload, string $privateKey): string
    {
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT',
        ];

        $segments = [
            $this->base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES)),
            $this->base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES)),
        ];

        $signature = $this->sign(implode('.', $segments), $privateKey);
        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    private function sign(string $data, string $privateKey): string
    {
        $key = openssl_pkey_get_private($privateKey);

        if ($key === false) {
            throw new RuntimeException('Invalid Google Drive private key.');
        }

        try {
            $signature = '';
            $signed = openssl_sign($data, $signature, $key, OPENSSL_ALGO_SHA256);

            if (! $signed) {
                throw new RuntimeException('Unable to sign Google Drive JWT.');
            }

            return $signature;
        } finally {
            openssl_free_key($key);
        }
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function resolveParentId(?string $parentId): string
    {
        if (is_string($parentId) && $parentId !== '') {
            return $parentId;
        }

        return $this->rootFolderId();
    }

    private function rootFolderId(): string
    {
        $rootFolderId = config('drive.root_folder_id');

        if (! is_string($rootFolderId) || $rootFolderId === '') {
            throw new RuntimeException('Google Drive root folder id is not configured.');
        }

        return $rootFolderId;
    }

    private function trashFolderId(): string
    {
        $trashFolderId = config('drive.trash_folder_id');

        if (! is_string($trashFolderId) || $trashFolderId === '') {
            throw new RuntimeException('Google Drive trash folder id is not configured.');
        }

        return $trashFolderId;
    }

    private function retryTimes(): int
    {
        return (int) config('drive.retry_times', 3);
    }

    private function retrySleepMs(): int
    {
        return (int) config('drive.retry_sleep_ms', 250);
    }

    private function timeoutSeconds(): int
    {
        return (int) config('drive.timeout_seconds', 30);
    }

    private function driveBaseUrl(): string
    {
        return rtrim((string) config('drive.drive_api_url', ''), '/');
    }

    private function uploadBaseUrl(): string
    {
        return rtrim((string) config('drive.upload_api_url', ''), '/');
    }

    private function sslVerify(): bool
    {
        $value = config('drive.verify_ssl', true);

        if (is_string($value)) {
            return ! in_array(strtolower($value), ['0', 'false', 'off', 'no'], true);
        }

        return (bool) $value;
    }

    private function withSupportsAllDrives(string $url): string
    {
        if (! $this->supportsAllDrives()) {
            return $url;
        }

        if (str_contains($url, 'supportsAllDrives=')) {
            return $url;
        }

        return $url.(str_contains($url, '?') ? '&' : '?').'supportsAllDrives=true';
    }

    private function supportsAllDrives(): bool
    {
        $value = config('drive.supports_all_drives', true);

        if (is_string($value)) {
            return ! in_array(strtolower($value), ['0', 'false', 'off', 'no'], true);
        }

        return (bool) $value;
    }
}
