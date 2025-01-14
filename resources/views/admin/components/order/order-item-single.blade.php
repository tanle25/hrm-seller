<div class="bg-white rounded-lg shadow-sm p-4">
    <div class="flex items-center gap-4">
      <!-- Sản phẩm chính -->
      <div>
        <input checked id="checked-checkbox" name="packages[]" form="shipping-form" type="checkbox" value="{{ $order['packages'][0]['id'] }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
      </div>
      <div>
        <img
          src="{{ $order['line_items'][0]['sku_image'] }}"
          alt="Sản phẩm"
          class="w-12 h-12 rounded shadow"
        />
      </div>

      <!-- Thông tin chính -->
      <div>
        <p class="font-medium text-gray-900">{{ $order['line_items'][0]['product_name'] }}</p>
        <p class="text-gray-500 text-sm">ID: {{ $order['line_items'][0]['id'] }}</p>
      </div>

      <!-- Chi tiết khác -->
      <div class="ml-auto flex items-center gap-4">
        <p class="text-gray-700 text-sm">{{ $order['shipping_provider'] }}</p>
        <button class="px-3 py-1 text-sm bg-gray-100 rounded border">
          Chuẩn bị hàng
        </button>
        <p class="text-green-600 font-semibold">
          {{ number_format($order['line_items'][0]['sale_price'], 0, ',', '.') }}₫
        </p>
      </div>
    </div>

    <!-- Phần mở rộng -->
    @if (count($order['line_items']) > 1)
      <div class="mt-4">
        <button
          class="text-blue-500 text-sm font-medium flex items-center gap-2"
          onclick="toggleDetails('{{ $order['id'] }}')"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M19 9l-7 7-7-7"
            />
          </svg>
          Hiển thị thêm SKU
        </button>

        <div
          id="details-{{ $order['id'] }}"
          class="hidden mt-2 space-y-2 border-t ease-in-out border-gray-200 pt-2"
        >
          @foreach ($order['line_items'] as $index => $item)
            @if ($index > 0)
              <div class="flex items-center gap-3">
                <img
                  src="{{ $item['sku_image'] }}"
                  alt="{{ $item['product_name'] }}"
                  class="w-10 h-10 rounded shadow"
                />
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ $item['product_name'] }}</p>
                  <p class="text-xs text-gray-500">ID: {{ $item['id'] }}</p>
                  <p class="text-xs text-green-600">
                    {{ number_format($item['sale_price'], 0, ',', '.') }}₫
                  </p>
                </div>
              </div>
            @endif
          @endforeach
        </div>
      </div>
    @endif
  </div>
