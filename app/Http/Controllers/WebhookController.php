<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Lắng nghe và xử lý các sự kiện liên quan đến đơn hàng.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handleWebhook(Request $request)
    {
        $event = $request->input('event');

        if (in_array($event, ['order.created', 'order.updated'])) {
            $data = $request->input('data');

            // Xử lý sự kiện
            if ($event === 'order.created') {
                $this->handleOrderCreated($data);
            } elseif ($event === 'order.updated') {
                $this->handleOrderUpdated($data);
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Xử lý sự kiện tạo đơn hàng.
     *
     * @param array $data
     * @return void
     */
    private function handleOrderCreated(array $data)
    {
        // Logic xử lý khi đơn hàng được tạo
        Log::info('Order created:', $data);
    }

    /**
     * Xử lý sự kiện cập nhật đơn hàng.
     *
     * @param array $data
     * @return void
     */
    private function handleOrderUpdated(array $data)
    {
        // Logic xử lý khi đơn hàng được cập nhật
        Log::info('Order updated:', $data);
    }
}

