<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Template</title>
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    <style>
        /* Smooth transition for sidebar */
        aside {
            transition: transform 0.3s ease-in-out;
        }
    </style>
     <link rel="preconnect" href="https://fonts.bunny.net">
     <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

     <!-- Styles / Scripts -->
     @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
         @vite(['resources/css/app.css', 'resources/js/app.js'])
     @else

     @endif
     <meta name="csrf-token" content="{{ csrf_token() }}">

</head>

<body class="bg-gray-100">
    <!-- Wrapper -->
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 bg-gray-800 text-white flex flex-col">
            <div class="px-4 py-5 text-center text-lg font-bold border-b border-gray-700">
                Admin Panel
            </div>
            <nav class="flex-1 px-4 py-4 space-y-2">
                <a href="#" class="block px-3 py-2 rounded-md hover:bg-gray-700">
                    Dashboard
                </a>
                <a href="#" class="block px-3 py-2 rounded-md hover:bg-gray-700">
                    Cửa hàng
                </a>
                <a href="{{url('order')}}" class="block px-3 py-2 rounded-md hover:bg-gray-700">
                    Đơn hàng
                </a>
                <a href="{{url('product')}}" class="block px-3 py-2 rounded-md hover:bg-gray-700">
                    Sản phẩm
                </a>
                <a href="#" class="block px-3 py-2 rounded-md hover:bg-gray-700">
                    Settings
                </a>
            </nav>
            <div class="px-4 py-4 border-t border-gray-700">
                <button class="w-full px-3 py-2 bg-red-600 rounded-md hover:bg-red-700">
                    Logout
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-md px-4 py-3 flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <button id="toggleSidebar" class="text-gray-800 md:hidden">
                        ☰
                    </button>
                    <h1 class="text-lg font-semibold">Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <input type="text" placeholder="Search..." class="border rounded-md px-3 py-2 text-sm" />
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Add New
                    </button>
                </div>
            </header>

            <!-- Content -->
            @yield('content')
        </main>
    </div>

    <script>
        const toggleButton = document.querySelector('#toggleSidebar');
        const sidebar = document.querySelector('#sidebar');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });

        // Initialize sidebar state for small screens
        if (window.innerWidth < 768) {
            sidebar.classList.add('-translate-x-full');
        }

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('-translate-x-full');
            }
        });

        document.querySelectorAll('.toggle-details').forEach((header) => {
        header.addEventListener('click', () => {
            // Tìm phần tử .product-details liên quan
            const productDetails = header.nextElementSibling;

            // Kiểm tra trạng thái hiện tại
            if (productDetails.style.maxHeight) {
            // Nếu đang mở, đóng lại
            productDetails.style.maxHeight = null;
            } else {
            // Nếu đang đóng, mở ra
            productDetails.style.maxHeight = productDetails.scrollHeight + 'px';
            }
        });
        });

    </script>
    @stack('js')
</body>

</html>
