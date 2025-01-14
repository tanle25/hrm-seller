@extends('admin.master')
@section('content')
<section class="flex-1 p-4">
    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 bg-white rounded-md shadow-md">
            <h2 class="text-sm text-gray-500">Total Users</h2>
            <p class="text-xl font-semibold">1,234</p>
        </div>
        <div class="p-4 bg-white rounded-md shadow-md">
            <h2 class="text-sm text-gray-500">Total Sales</h2>
            <p class="text-xl font-semibold">$45,678</p>
        </div>
        <div class="p-4 bg-white rounded-md shadow-md">
            <h2 class="text-sm text-gray-500">Orders Pending</h2>
            <p class="text-xl font-semibold">56</p>
        </div>
    </div>

    <!-- Data Table -->
    <div class="mt-6">
        @foreach ($products as $product )

        <div class="border rounded-md shadow-md p-4 bg-white">
            <!-- Header sản phẩm -->
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <img src="{{ getImageUrls($product['main_images'])[0] }}" alt="Product Image" class="w-12 h-12 rounded-md" />
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold">{{ $product['title'] }}</h3>
                        <p class="text-sm text-gray-600">ID: {{ $product['id'] }}</p>
                    </div>
                </div>

                <div class=" flex justify-between w-[400px] ">
                    <div>
                        <p class="text-sm text-gray-600">{{ getTotalInventory($product['skus']) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">{!! getMinPrice($product['skus']) == getMaxPrice($product['skus'])
                            ? number_format(getMinPrice($product['skus']))
                            : number_format(getMinPrice($product['skus'])) . 'đ - đ' . number_format(getMaxPrice($product['skus'])) !!}
                            </p>
                    </div>
                    <div class=" w-30"></div>
                    {{-- <p class="text-sm text-gray-400">Đã đặt mức hoa hồng</p> --}}
                </div>
            </div>

            <!-- Tiêu đề có thể click -->
            <h4 class="toggle-details text-md p-1 rounded bg-gray-100 font-semibold cursor-pointer mt-4">
                Tổng {{count($product['skus'])}} mặt hàng
            </h4>

            <!-- Khu vực nội dung ẩn -->
            <div data-product-id="{{ $product['id'] }}"
                class="product-details bg-gray-50 rounded-md overflow-hidden transition-all duration-500 ease-in-out max-h-0">
                <!-- Nội dung mở rộng -->
                <div class="details-content flex justify-end my-2">

                    <div class=" w-[500px]">
                        <div class="flex justify-end mt-2 items-center">
                            <div class=" flex-1 mr-2">
                            </div>

                            <div class="w-32 mr-2">
                                <input type="number"
                                class="input-qty border rounded-md h-10  px-2 py-1 w-32  text-gray-700" />
                            </div>

                            <div class=" w-32 mr-2">
                                <input type="number"
                                class="input-price border rounded-md px-2 h-10  py-1 w-32  text-gray-700" />
                            </div>
                            <div class=" w-32">
                                <p class="text-sm font-medium"><button class=" btn-apply text-teal-600">Áp dụng</button></p>
                            </div>
                        </div>
                        <div class="product-inventory">
                            @foreach ( $product['skus'] as $sku )
                            <div class="flex justify-end mt-2 items-center" data-warehouseid="{{$sku['inventory'][0]['warehouse_id']}}" data-skuid="{{$sku['id']}}">
                                <div class=" flex-1 mr-2">
                                    <p class="text-sm font-medium">{{$sku['seller_sku']}}</p>
                                </div>

                                <div class="w-32 mr-2">
                                    <input type="number" name="qty" value="{{$sku['inventory'][0]['quantity']}}"
                                    class="sku-qty border rounded-md px-2 py-1 w-32 h-10  text-gray-700" />
                                </div>

                                <div class=" w-32 mr-2">
                                    <input type="number" name="price" value="{{$sku['price']['sale_price']}}"
                                    class="sku-price border rounded-md px-2 py-1 w-32 h-10  text-gray-700" />
                                </div>
                                <div class=" w-32"></div>
                            </div>
                            @endforeach

                        </div>
                        <div class="flex justify-end mt-3 w-[372px] pr-1 mr-2 items-center">
                            <button class="btn-save rounded-md bg-teal-600 py-1 px-3 border border-transparent text-center text-sm text-white transition-all shadow-sm hover:shadow focus:bg-teal-800 focus:shadow-none active:bg-teal-800 hover:bg-teal-800 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none" type="button"> Lưu </button>
                        </div>

                    </div>

                </div>
            </div>
        </div>
        @endforeach

    </div>
</section>
@endsection

@push('js')
<script>
   document.addEventListener("DOMContentLoaded", () => {
    // Xử lý nút "Áp dụng"
    const applyButtons = document.querySelectorAll(".btn-apply");
    applyButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
        const detailsContent = btn.closest(".details-content");

        // Lấy giá trị từ input-qty và input-price
        const qtyInput = detailsContent.querySelector(".input-qty");
        const priceInput = detailsContent.querySelector(".input-price");

        // Lấy giá trị nếu không phải NaN và lớn hơn hoặc bằng 0
        const qtyValue = qtyInput?.valueAsNumber;
        const priceValue = priceInput?.valueAsNumber;

        // Chỉ thực hiện nếu có ít nhất một giá trị hợp lệ
        if ((qtyValue || qtyValue === 0) || (priceValue > 0)) {
            // Cập nhật các ô sku-qty và sku-price
            const inventories = detailsContent.querySelectorAll(".product-inventory .flex");
            inventories.forEach((inventory) => {
                const skuQtyInput = inventory.querySelector(".sku-qty");
                const skuPriceInput = inventory.querySelector(".sku-price");

                // Cập nhật nếu có giá trị hợp lệ
                if (skuQtyInput && (qtyValue || qtyValue === 0)) {
                    skuQtyInput.value = qtyValue;
                }
                if (skuPriceInput && priceValue > 0) {
                    skuPriceInput.value = priceValue;
                }
            });

            console.log("Đã cập nhật hàng loạt giá trị SKU từ input chính!");
        } else {
            console.warn("Không tìm thấy giá trị hợp lệ để áp dụng.");
        }
    });
});


    // Xử lý nút "Lưu" cho mỗi sản phẩm
    const saveButtons = document.querySelectorAll(".btn-save");
    saveButtons.forEach((saveButton) => {
        saveButton.addEventListener("click", () => {
            const details = saveButton.closest(".product-details");
            const productId = details.dataset.productId; // Lấy product-id
            const inventories = details.querySelectorAll(".product-inventory .flex");

            const skus = Array.from(inventories).map((inventory) => {
                return {
                    skuId: inventory.dataset.skuid,
                    warehouseId: inventory.dataset.warehouseid,
                    qty: inventory.querySelector(".sku-qty")?.value || null,
                    price: inventory.querySelector(".sku-price")?.value || null,
                };
            });

            const data = {
                productId,
                skus,
            };

            console.log("Dữ liệu đã lưu:", data); // In ra dữ liệu
            axios.post('/api/save-product', data)
                .then(response => {
                    console.log(`Dữ liệu sản phẩm ${productId} được gửi thành công:`, response.data);
                })
                .catch(error => {
                    console.error(`Đã xảy ra lỗi khi gửi dữ liệu sản phẩm ${productId}:`, error);
                });
        });
    });
});


</script>
@endpush
