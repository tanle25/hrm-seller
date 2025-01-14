<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TikTokSignatureService
{
    private const EXCLUDE_KEYS = ['sign', 'access_token'];


    public static function generateSignature(array $queryParams, string $appSecret, string $apiPath, ?array $body = null, string $contentType = 'application/json'): string
    {
        $excludeKeys = ['sign', 'access_token'];
        $filteredParams = array_filter($queryParams, function ($key) use ($excludeKeys) {
            return !in_array($key, $excludeKeys);
        }, ARRAY_FILTER_USE_KEY);
        ksort($filteredParams);
        $paramString = '';
        foreach ($filteredParams as $key => $value) {
            $paramString .= $key . $value;
        }
        $baseString = $apiPath . $paramString;

        if ($contentType !== 'multipart/form-data' && !empty($body)) {
            $baseString .= json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        $signString = $appSecret . $baseString . $appSecret;

        $signature = hash_hmac('sha256', $signString, $appSecret);

        return $signature;
    }

    public static function sign(
        string $path,
        array $queryParams = [],
        array $body = [],
        string $appSecret,
        string $contentType = 'application/json'
    ): string
    {
        $signString = '';

        // Step 1: Extract query parameters và sort theo alphabet
        $filteredParams = collect($queryParams)
            ->except(self::EXCLUDE_KEYS)
            ->sortKeys();

        // Step 2: Nối parameters theo format {key}{value}
        $paramString = '';
        foreach ($filteredParams as $key => $value) {
            $paramString .= $key . $value;
        }

        // Step 3: Thêm request path
        if (!Str::startsWith($path, '/')) {
            $path = '/' . $path;
        }
        $signString = $path . $paramString;

        // Step 4: Thêm request body nếu không phải multipart/form-data
        if ($contentType !== 'multipart/form-data' && !empty($body)) {
            $bodyJson = json_encode($body);
            if ($bodyJson) {
                $signString .= $bodyJson;
            }
        }

        // Step 5: Wrap chuỗi với app_secret
        $signString = $appSecret . $signString . $appSecret;

        // Step 6: Mã hóa chuỗi bằng HMAC-SHA256
        return hash_hmac('sha256', $signString, $appSecret);
    }
}
