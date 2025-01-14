<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Shop;
use App\Facades\TikTokShop;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\TikTokSignatureService;


class HomeController extends Controller
{
    //
    function index(Request $request)
    {
        $authCode = $request->code;
        try {
            $shopInfo = TikTokShop::getShopData($authCode);
            // dd($shopInfo);
            Shop::updateOrCreate(
                ['shopid' => $shopInfo->shopId], // Điều kiện để kiểm tra bản ghi tồn tại
                [
                    'authcode' => $shopInfo->authCode,
                    'token' => $shopInfo->token,
                    'shopname' => $shopInfo->shopName,
                    'shopcode' => $shopInfo->shopCode,
                    'cipher' => $shopInfo->cipher,
                ]
            );
            return view('admin.home');
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            dd($e->getMessage());
        }

    }

    function getShopInfo() {
        $shop = Shop::find(1);
        $shopInfo = TikTokShop::getShopData($shop->authcode);
        dd($shopInfo);
    }

    function searchProduct()  {
        $products = TikTokShop::searchProduct(1);
        // dd($products);

        return view('admin.products', ['products'=>$products]);
    }

    function updateProduct(Request $request)  {
        $data = $request->all();
        $shop = Shop::find(1);
        // dd($data);
        TikTokShop::updatePrice($data['productId'], $data['skus'],$shop->cipher, $shop->token)->updateInventory($data['productId'], $data['skus'],$shop->cipher, $shop->token);
        return response($data);
    }
}
