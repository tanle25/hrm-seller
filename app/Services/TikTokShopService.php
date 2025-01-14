<?php

namespace App\Services;

use stdClass;
use Exception;
use App\Models\Shop;
use Illuminate\Support\Facades\Http;
use App\Services\TikTokSignatureService;

class TikTokShopService
{
    protected $baseUrl;
    protected $appKey;

    protected $appSecret;

    public function __construct()
    {
        $this->appSecret = config('tts.app_secret');
        $this->appKey = config('tts.app_key');
        $this->baseUrl = config('tts.base_url');
    }

    function getShopData($authCode)
    {

        $baseUrl = 'https://auth.tiktok-shops.com/api/v2/token/get';

        // Lấy token từ TikTok Shop API
        $tokenResponse = Http::get($baseUrl, [
            'app_key' => $this->appKey,
            'app_secret' => $this->appSecret,
            'auth_code' => $authCode,
            'grant_type' => 'authorized_code',
        ]);

        if ($tokenResponse->failed()) {
            throw new Exception('Failed to fetch token: ' . $tokenResponse->body());
        }

        $tokenData = $tokenResponse->json('data');
        $accessToken = $tokenData['access_token'] ?? null;

        if (!$accessToken) {
            throw new Exception('Access token not found in response.');
        }

        // Tạo chữ ký cho request tiếp theo
        $params = [
            'app_key' => $this->appKey,
            'timestamp' => time(),
        ];
        $apiPath = '/authorization/202309/shops';
        $signature = TikTokSignatureService::generateSignature($params, $this->appSecret, $apiPath, null);
        $params['sign'] = $signature;

        // Gọi API để lấy thông tin cửa hàng
        $shopResponse = Http::withHeaders([
            'x-tts-access-token' => $accessToken,
            'content-type' => 'application/json',
        ])->get('https://open-api.tiktokglobalshop.com' . $apiPath, $params);

        if ($shopResponse->failed()) {
            throw new Exception('Failed to fetch shop data: ' . $shopResponse->body());
        }

        $shopData = $shopResponse->json('data.shops')[0] ?? null;

        if (!$shopData) {
            throw new Exception('Shop data not found in response.');
        }

        // Chuẩn bị thông tin shop
        return (object)[
            'authCode' => $authCode,
            'token' => $accessToken,
            'shopName' => $tokenData['seller_name'],
            'shopId' => $shopData['id'],
            'shopCode' => $shopData['code'],
            'cipher' => $shopData['cipher'],
        ];
    }


    function searchProduct($shopId)
    {
        $shop = Shop::findOrFail($shopId); // Tìm hoặc trả lỗi nếu không tồn tại
    $apiPath = '/product/202312/products/search';
    $timestamp = time();

    // Thông số cho API
    $params = [
        'app_key' => $this->appKey,
        'timestamp' => $timestamp,
        'shop_cipher' => $shop->cipher,
        'page_size' => 100,
    ];
    $body = ['status' => 'ALL'];

    // Tạo chữ ký
    $signature = TikTokSignatureService::generateSignature(
        $params,
        $this->appSecret,
        $apiPath,
        $body,
        $timestamp
    );

    // URL API
    $url = sprintf(
        "https://open-api.tiktokglobalshop.com%s?%s&sign=%s",
        $apiPath,
        http_build_query($params),
        $signature
    );

    // Gửi yêu cầu cURL
    $response = Http::withHeaders([
        'x-tts-access-token' => $shop->token,
        'Content-Type' => 'application/json',
    ])->post($url, $body);

    // Kiểm tra phản hồi
    if ($response->failed()) {
        abort(500, 'API request failed: ' . $response->body());
    }
    // dd($response->body());
    $products = $response->json('data.products', []);

    // Lấy thông tin chi tiết từng sản phẩm
    foreach ($products as &$product) {
        $product = $this->getProduct($product['id'], $shop->cipher, $shop->token);
    }

    return $products;
    }

    private function getProduct($productId, $shopCipher, $token)
    {
        $apiPath = "/product/202309/products/$productId";
        // dd($token);
        $params = [
            'app_key' => $this->appKey,
            'timestamp' => time(),
            'shop_cipher' => $shopCipher
        ];
        $signature = TikTokSignatureService::sign($apiPath,$params,[],$this->appSecret);
        $params['sign'] = $signature;

        $shopResponse = Http::withHeaders([
            'x-tts-access-token' => $token,
            'content-type' => 'application/json',
        ])->get('https://open-api.tiktokglobalshop.com' . $apiPath, $params);

        if ($shopResponse->failed()) {
            throw new Exception('Failed to fetch shop data: ' . $shopResponse->body());
        }
        return $shopResponse->json('data');
    }


    function updatePrice($productId, $skus, $shopCipher, $token)
    {
        $apiPath = "/product/202309/products/$productId/prices/update";
        $timestamp = time();

        $params = [
            'app_key' => $this->appKey,
            'timestamp' => $timestamp,
            'shop_cipher' => $shopCipher
        ];

        $body = [
            'skus' => array_map(function ($sku) {
                return [
                    'id' => $sku['skuId'],
                    'price' => [
                        'amount' => $sku['price'],
                        'currency' => 'VND'
                    ]
                ];
            }, $skus)
        ];

        // Generate signature
        $signature = TikTokSignatureService::sign($apiPath, $params, $body, $this->appSecret);

        // Add signature to query params
        $queryString = http_build_query(array_merge($params, ['sign' => $signature]));

        // Construct full URL with query params
        $fullUrl = 'https://open-api.tiktokglobalshop.com' . $apiPath . '?' . $queryString;

        // Make request with body in the correct place
        $shopResponse = Http::withHeaders([
            'x-tts-access-token' => $token,
            'content-type' => 'application/json',
        ])->post($fullUrl, $body);  // Send body here, not params

        if ($shopResponse->failed()) {
            throw new Exception('Failed to fetch shop data: ' . $shopResponse->body());
        }
        return $this;
    }

    function updateInventory($productId, $skus, $shopCipher, $token)  {
        $apiPath = "/product/202309/products/$productId/inventory/update";
        $timestamp = time();

        $params = [
            'app_key' => $this->appKey,
            'timestamp' => $timestamp,
            'shop_cipher' => $shopCipher
        ];
        // dd($skus);
        $body = [
            'skus' => array_map(function ($sku) {
                return [
                    'id' => $sku['skuId'],
                    'inventory' => [
                        [
                            'quantity' => intval($sku['qty']),
                            'warehouse_id' => $sku['warehouseId']
                        ]
                    ]
                ];
            }, $skus)
        ];
        // dd($body);
        // Generate signature
        $signature = TikTokSignatureService::sign($apiPath, $params, $body, $this->appSecret);

        // Add signature to query params
        $queryString = http_build_query(array_merge($params, ['sign' => $signature]));

        // Construct full URL with query params
        $fullUrl = 'https://open-api.tiktokglobalshop.com' . $apiPath . '?' . $queryString;

        // Make request with body in the correct place
        $shopResponse = Http::withHeaders([
            'x-tts-access-token' => $token,
            'content-type' => 'application/json',
        ])->post($fullUrl, $body);  // Send body here, not params

        if ($shopResponse->failed()) {
            throw new Exception('Failed to fetch shop data: ' . $shopResponse->body());
        }
        return $this;
    }

    function getOrderList($shopCipher, $token) {
        $apiPath = "/order/202309/orders/search";
        $timestamp = time();

        $params = [
            'app_key' => $this->appKey,
            'timestamp' => $timestamp,
            'shop_cipher' => $shopCipher,
            'page_size' =>20,
            'sort_order'=> 'ASC',
            'sort_field' => 'create_time'
        ];

        $body = [
            "shipping_type" => "TIKTOK",
            // "is_buyer_request_cancel" => false,
            // "order_status" => "UNPAID",
        ];
        // ksort($params);
        // Generate signature
        // dd($params);
        $signature = TikTokSignatureService::sign($apiPath, $params, $body, $this->appSecret);

        // Add signature to query params
        $queryString = http_build_query(array_merge($params, ['sign' => $signature]));

        // Construct full URL with query params
        $fullUrl = 'https://open-api.tiktokglobalshop.com' . $apiPath . '?' . $queryString;

        // Make request with body in the correct place
        $shopResponse = Http::withHeaders([
            'x-tts-access-token' => $token,
            'content-type' => 'application/json',
        ])->post($fullUrl, $body);  // Send body here, not params

        if ($shopResponse->failed()) {
            throw new Exception('Failed to fetch shop data: ' . $shopResponse->body());
        }
        // dd(json_decode($shopResponse->body(), JSON_PRETTY_PRINT) );
        return $shopResponse->json('data');
    }

    function getShippingDocument($packages, $token, $shopCipher) {
        $package = $packages[0];
        $apiPath = "/fulfillment/202309/packages/$package/shipping_documents";
        $timestamp = time();

        $params = [
            'app_key' => $this->appKey,
            'timestamp' => $timestamp,
            'shop_cipher' => $shopCipher,
            'document_type' => 'SHIPPING_LABEL_AND_PACKING_SLIP',
            'document_size' => 'A6'
        ];

        $body = [
            // "shipping_type" => "TIKTOK",
            // "is_buyer_request_cancel" => false,
            // "order_status" => "UNPAID",
        ];
        // ksort($params);
        // Generate signature
        // dd($params);
        // $signature = TikTokSignatureService::sign($apiPath, $params, $body, $this->appSecret);

        // Add signature to query params
        $signature = TikTokSignatureService::sign($apiPath,$params,[],$this->appSecret);
        $params['sign'] = $signature;

        $shopResponse = Http::withHeaders([
            'x-tts-access-token' => $token,
            'content-type' => 'application/json',
        ])->get('https://open-api.tiktokglobalshop.com' . $apiPath, $params);

        if ($shopResponse->failed()) {
            throw new Exception('Failed to fetch shop data: ' . $shopResponse->body());
        }
        return $shopResponse->json();
    }

    function shippingPacekage($packages, $token, $shopCipher) {
        $package = $packages[0];
        $apiPath = "/fulfillment/202309/packages/$package/ship";
        $timestamp = time();

        $params = [
            'app_key' => $this->appKey,
            'timestamp' => $timestamp,
            'shop_cipher' => $shopCipher
        ];

        $body = [
            'handover_method' => 'DROP_OFF',
        ];
        // Add signature to query params
        $signature = TikTokSignatureService::sign($apiPath, $params, $body, $this->appSecret);

        // Add signature to query params
        $queryString = http_build_query(array_merge($params, ['sign' => $signature]));

        // Construct full URL with query params
        $fullUrl = 'https://open-api.tiktokglobalshop.com' . $apiPath . '?' . $queryString;

        // Make request with body in the correct place
        $shopResponse = Http::withHeaders([
            'x-tts-access-token' => $token,
            'content-type' => 'application/json',
        ])->post($fullUrl, $body);  // Send body here, not params

        if ($shopResponse->failed()) {
            throw new Exception('Failed to fetch shop data: ' . $shopResponse->body());
        }
        return $shopResponse->json();
    }

    function getPackageDetail($packages, $token, $shopCipher) {
        $package = $packages[0];
        $apiPath = "/fulfillment/202309/packages/$package";
        $timestamp = time();

        $params = [
            'app_key' => $this->appKey,
            'timestamp' => $timestamp,
            'shop_cipher' => $shopCipher,
        ];

        $body = [

        ];

        // Add signature to query params
        $signature = TikTokSignatureService::sign($apiPath,$params,$body,$this->appSecret);
        $params['sign'] = $signature;

        $shopResponse = Http::withHeaders([
            'x-tts-access-token' => $token,
            'content-type' => 'application/json',
        ])->get('https://open-api.tiktokglobalshop.com' . $apiPath, $params);

        if ($shopResponse->failed()) {
            throw new Exception('Failed to fetch shop data: ' . $shopResponse->body());
        }
        return $shopResponse->json();
    }
}
