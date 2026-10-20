<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory & Sales System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full">
        <!-- Logo and App Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-emerald-500 text-white shadow-xl shadow-emerald-500/30 mb-4">
                <i class="fa-solid fa-boxes-stacked text-3xl"></i>
            </div>
            <h1 class="text-2xl font-black text-white tracking-wide">Inventory &amp; Sales System</h1>
            <p class="text-sm text-slate-400 mt-1">UTS Pemrograman Web 2 &bull; Laravel &amp; MySQL</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-6 sm:p-8 border border-slate-100">
            <h2 class="text-xl font-bold text-slate-800 mb-1">Masuk ke Sistem</h2>
            <p class="text-xs text-slate-500 mb-6">Gunakan kredensial akun Admin atau Staff Anda.</p>

            @if(session('success'))
                <div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-xs font-medium flex items-center space-x-2">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-xs">
                    @foreach($errors->all() as $err)
                        <div class="flex items-center space-x-1">
                            <i class="fa-solid fa-circle-xmark text-red-500"></i>
                            <span>{{ $err }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-envelope text-sm"></i>
                        </div>
                        <input type="email" name="email" id="email" required value="{{ old('email') }}"
                            class="w-full pl-10 pr-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition"
                            placeholder="nama@inventory.test">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1 uppercase tracking-wider">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        <input type="password" name="password" id="password" required
                            class="w-full pl-10 pr-3 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition"
                            placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2 text-xs text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                        <span>Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-lg shadow-emerald-600/30 transition duration-150 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Masuk</span>
                </button>
            </form>

            <!-- Quick Login Helper for Assessment / Demo -->
            <div class="mt-6 pt-6 border-t border-slate-200">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 text-center mb-2.5">
                    <i class="fa-solid fa-bolt text-amber-500 mr-1"></i> Akun Demo untuk Dosen / Penguji
                </p>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="fillAccount('admin@inventory.test', 'password')"
                        class="p-2.5 rounded-lg border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/50 text-left transition group">
                        <div class="text-xs font-bold text-slate-800 group-hover:text-indigo-600">Admin / Owner</div>
                        <div class="text-[10px] text-slate-500">Akses Penuh Master &amp; Transaksi</div>
                    </button>
                    <button type="button" onclick="fillAccount('staff@inventory.test', 'password')"
                        class="p-2.5 rounded-lg border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/50 text-left transition group">
                        <div class="text-xs font-bold text-slate-800 group-hover:text-emerald-600">Staff Gudang</div>
                        <div class="text-[10px] text-slate-500">Buat Pembelian &amp; Penjualan</div>
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center text-xs text-slate-400">
            Sistem UTS Web Programming 2 &bull; Keamanan Session &amp; Password BCRYPT
        </div>
    </div>

    <script>
        function fillAccount(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
