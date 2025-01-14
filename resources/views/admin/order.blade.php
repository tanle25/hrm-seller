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
        <div class="p-4 flex">
            <form id="shipping-form" action="{{url('shipping-package')}}" method="POST">
                @csrf
                <button class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
                    Xác nhận đơn hàng
                </button>
            </form>
            <form id="print-form" action="{{url('get-shipping-document')}}" method="POST">
                @csrf
                <button class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
                    In đơn hàng
                </button>
            </form>
        </div>

        <div class="w-full space-y-4 p-4">
            <!-- Header -->
            <div class="flex items-center px-4 py-3 bg-gray-50 rounded-lg">
                <div class="w-10"></div>
                <div class="flex-1">Mặt Hàng</div>
                <div class="w-52">Mã Đơn Hàng</div>
                <div class="w-40">Mã Vận Đơn</div>
                <div class="w-32">Doanh Thu</div>
                <div class="w-56">Nguồn</div>
                <div class="w-40">Tạo Lúc</div>
                <div class="w-40">Quá Hạn</div>
            </div>

            <!-- Card 1 -->
            @foreach ($orders['orders'] as $index => $order)
                {{-- <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="flex items-center">
                        <div class="w-10">1</div>

                        @foreach (itemCount($order['line_items']) as $item)

                            <div class="flex-1">
                                @foreach ($item['skus'] as $sku)
                                    <div class="flex-1">
                                        <div class="items-center gap-3">
                                            <img src="{{ $sku['sku_image'] }}" class="w-12 h-12 object-cover rounded"
                                                alt="Product">
                                            <div>
                                                <div class="font-medium">{{ $item['product_name'] }}</div>
                                                <div class="text-sm text-gray-500">{{ $item['product_id'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                        <div class=" w-52 flex items-center gap-2">
                            <span>{{ $order['id'] }}</span>
                            <button class="hover:bg-gray-100 p-1 rounded">
                                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                        </div>

                        <div class="w-40">
                            <div>J&T Express</div>
                            <button
                                class="px-3 py-1 mt-1 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                                Chuẩn bị hàng
                            </button>
                        </div>

                        <div class="w-32 text-green-600">450,000</div>

                        <div class=" w-56 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>TRINH BA HAO ...47N4CVGE2E</span>
                        </div>
                        <div class="w-40 flex items-center gap-2">
                            <span>18:00 - 12/1/2024</span>
                        </div>
                        <div class="w-40 flex items-center gap-2">

                            <span>18:00 - 15/1/2024</span>
                        </div>
                    </div>
                </div> --}}
                @include('admin.components.order.order-item-single',['index'=>$index, 'order'=>$order])
            @endforeach

            <!-- Card 2 -->


            <!-- Thêm Card 3 tương tự -->

        </div>

    </section>

@endsection

@push('js')


<script>
   function toggleDetails(orderId) {
  const details = document.getElementById(`details-${orderId}`);
  if (details.classList.contains("hidden")) {
    details.classList.remove("hidden");
  } else {
    details.classList.add("hidden");
  }
}

</script>


@endpush
