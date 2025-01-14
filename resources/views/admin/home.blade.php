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
        <h2 class="text-lg font-semibold mb-4">Recent Orders</h2>
        <div class="bg-white rounded-md shadow-md overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="text-left px-4 py-2">Order ID</th>
                        <th class="text-left px-4 py-2">Customer</th>
                        <th class="text-left px-4 py-2">Total</th>
                        <th class="text-left px-4 py-2">Status</th>
                        <th class="text-left px-4 py-2">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-4 py-2">#12345</td>
                        <td class="px-4 py-2">John Doe</td>
                        <td class="px-4 py-2">$120.00</td>
                        <td class="px-4 py-2">
                            <span class="text-green-600">Completed</span>
                        </td>
                        <td class="px-4 py-2">2025-01-09</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-4 py-2">#12346</td>
                        <td class="px-4 py-2">Jane Smith</td>
                        <td class="px-4 py-2">$75.00</td>
                        <td class="px-4 py-2">
                            <span class="text-yellow-600">Pending</span>
                        </td>
                        <td class="px-4 py-2">2025-01-08</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2">#12347</td>
                        <td class="px-4 py-2">Bob Johnson</td>
                        <td class="px-4 py-2">$210.00</td>
                        <td class="px-4 py-2">
                            <span class="text-red-600">Cancelled</span>
                        </td>
                        <td class="px-4 py-2">2025-01-07</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
