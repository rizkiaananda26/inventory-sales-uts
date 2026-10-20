<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Inventory & Sales System</title>
    <!-- Tailwind CSS CDN for instant, zero-build beautiful responsive UI -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome for crisp icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white !important; color: black !important; }
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased min-h-screen flex flex-col md:flex-row">

    <!-- Mobile Header -->
    <div class="md:hidden bg-slate-900 text-white px-4 py-3 flex items-center justify-between no-print shadow">
        <div class="flex items-center space-x-2">
            <i class="fa-solid fa-boxes-stacked text-emerald-400 text-xl"></i>
            <span class="font-bold text-lg tracking-wide">UTS Inventory</span>
        </div>
        <button id="mobileMenuBtn" class="text-slate-300 hover:text-white focus:outline-none p-1">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="w-full md:w-64 bg-slate-900 text-slate-200 flex-shrink-0 flex flex-col justify-between hidden md:flex no-print shadow-xl transition-all duration-300 z-30">
        <div>
            <!-- Brand Logo -->
            <div class="px-6 py-5 border-b border-slate-800 flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                    <i class="fa-solid fa-boxes-stacked text-lg"></i>
                </div>
                <div>
                    <h1 class="font-bold text-white text-base leading-tight">UTS Inventory</h1>
                    <p class="text-xs text-slate-400">Sales & Stock System</p>
                </div>
            </div>

            <!-- Role Badge & User Info Card -->
            <div class="px-5 py-4 border-b border-slate-800/80 bg-slate-800/30">
                <div class="flex items-center justify-between">
                    <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Role Akun</span>
                    @if(auth()->user()->isAdmin())
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                            <i class="fa-solid fa-shield-halved mr-1"></i> Admin / Owner
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            <i class="fa-solid fa-user-tag mr-1"></i> Staff Gudang
                        </span>
                    @endif
                </div>
                <div class="mt-2 text-sm font-medium text-white truncate">
                    {{ auth()->user()->name }}
                </div>
                <div class="text-xs text-slate-400 truncate">
                    {{ auth()->user()->email }}
                </div>
            </div>

            <!-- Menu Links -->
            <nav class="px-3 py-4 space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-gauge-high w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>

                <div class="pt-3 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Transaksi Stok</div>

                <a href="{{ route('purchases.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('purchases.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-truck-ramp-box w-5 text-center"></i>
                    <span>Pembelian (Stok Masuk)</span>
                </a>

                <a href="{{ route('sales.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('sales.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-cash-register w-5 text-center"></i>
                    <span>Penjualan (Stok Keluar)</span>
                </a>

                <div class="pt-3 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Master Data</div>

                <a href="{{ route('products.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('products.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-box-archive w-5 text-center"></i>
                    <span>Produk</span>
                </a>

                <a href="{{ route('categories.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('categories.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-tags w-5 text-center"></i>
                    <span>Kategori</span>
                </a>

                <a href="{{ route('suppliers.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('suppliers.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-handshake w-5 text-center"></i>
                    <span>Pemasok / Supplier</span>
                </a>

                @if(auth()->user()->isAdmin())
                    <div class="pt-3 pb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Administrasi</div>

                    <a href="{{ route('users.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('users.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-users-gear w-5 text-center"></i>
                        <span>Kelola Pengguna</span>
                    </a>
                @endif
            </nav>
        </div>

        <!-- Sidebar Footer / Logout -->
        <div class="p-4 border-t border-slate-800">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2.5 rounded-lg text-sm font-medium bg-red-600/20 text-red-300 hover:bg-red-600 hover:text-white transition">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Keluar (Logout)</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen overflow-x-hidden">
        <!-- Top App Bar -->
        <header class="bg-white border-b border-slate-200 px-6 py-3.5 flex items-center justify-between no-print shadow-sm">
            <div class="flex items-center space-x-3">
                <h2 class="text-xl font-bold text-slate-800">@yield('page-title', 'Sistem Manajemen Inventaris')</h2>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right hidden sm:block">
                    <div class="text-xs text-slate-400">{{ now()->translatedFormat('l, d F Y') }}</div>
                    <div class="text-sm font-semibold text-slate-700">{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</div>
                </div>
                <div class="w-9 h-9 rounded-full bg-slate-200 border border-slate-300 flex items-center justify-center text-slate-700 font-bold">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>
        </header>

        <!-- Page Body -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
            <!-- Global Flash Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-start space-x-3 text-emerald-800 shadow-sm no-print">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-lg mt-0.5"></i>
                    <div class="flex-1 text-sm font-medium">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 flex items-start space-x-3 text-red-800 shadow-sm no-print">
                    <i class="fa-solid fa-triangle-exclamation text-red-500 text-lg mt-0.5"></i>
                    <div class="flex-1 text-sm font-medium">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 shadow-sm no-print">
                    <div class="flex items-center space-x-2 font-semibold text-sm mb-2 text-amber-800">
                        <i class="fa-solid fa-circle-xmark text-amber-500 text-base"></i>
                        <span>Mohon periksa kembali formulir input Anda:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1 pl-4 text-amber-800">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 px-6 py-4 text-center text-xs text-slate-400 no-print">
            UTS Pemrograman Web 2 &bull; Inventory &amp; Sales Management System &bull; Laravel 12 &amp; MySQL
        </footer>
    </div>

    <!-- Toggle Mobile Menu Script -->
    <script>
        const btn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        if (btn && sidebar) {
            btn.addEventListener('click', () => {
                sidebar.classList.toggle('hidden');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
