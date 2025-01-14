<?php

namespace App\Http\Controllers;

use App\Facades\TikTokShop;
use App\Models\Shop;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
    function index()  {
        $shop = Shop::find(1);
        $orders = TikTokShop::getOrderList($shop->cipher, $shop->token);
        // dd($orders);
        // dd(itemCount($orders['orders'][2]['line_items']) );

        return view('admin.order', ['orders'=>$orders]);
    }

    function getShippingDocument(Request $request) {
        $shop = Shop::find(1);
        $label = TikTokShop::getShippingDocument($request->packages, $shop->token, $shop->cipher);
        dd($label);
    }

    function shippingPackage(Request $request)  {
        $shop = Shop::find(1);
        $data = TikTokShop::shippingPacekage($request->packages, $shop->token, $shop->cipher);
        // $data = TikTokShop::getShippingDocument($request->packages, $shop->token, $shop->cipher);
        dd($data);
    }
}
