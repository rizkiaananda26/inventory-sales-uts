@extends('layouts.app')

@section('title', 'Kelola Pengguna')
@section('page-title', 'Manajemen Pengguna &amp; Hak Akses')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Info -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-bold text-slate-800">Daftar Akun Pengguna Sistem</h1>
            <p class="text-xs text-slate-500">Kelola kredensial login dan pembagian role Admin/Owner serta Staff.</p>
        </div>
        <div>
            <a href="{{ route('users.create') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-md shadow-emerald-600/20 transition">
                <i class="fa-solid fa-user-plus"></i>
                <span>Tambah Pengguna Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
        <form action="{{ route('users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-7">
                <label for="search" class="block text-xs font-semibold text-slate-600 mb-1">Cari Nama / Email</label>
                <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Nama atau email pengguna..."
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div class="sm:col-span-3">
                <label for="role" class="block text-xs font-semibold text-slate-600 mb-1">Role</label>
                <select name="role" id="role" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ $role == 'admin' ? 'selected' : '' }}>Admin / Owner</option>
                    <option value="staff" {{ $role == 'staff' ? 'selected' : '' }}>Staff Gudang</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center space-x-2">
                <button type="submit" class="w-full px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition">
                    Filter
                </button>
                @if($search || $role)
                    <a href="{{ route('users.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-medium transition" title="Reset filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="px-5 py-3.5">Nama Lengkap</th>
                        <th class="px-5 py-3.5">Email Akun</th>
                        <th class="px-4 py-3.5 text-center">Role / Hak Akses</th>
                        <th class="px-4 py-3.5">Terdaftar Sejak</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-bold text-slate-800 text-sm flex items-center space-x-2">
                                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs">
                                    {{ substr($u->name, 0, 1) }}
                                </div>
                                <span>{{ $u->name }}</span>
                                @if($u->id === auth()->id())
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-semibold">(Anda)</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-600 font-mono">
                                {{ $u->email }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if($u->role === 'admin')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">
                                        <i class="fa-solid fa-shield-halved mr-1"></i> Admin / Owner
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-user mr-1"></i> Staff
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-xs text-slate-500">
                                {{ $u->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1">
                                <a href="{{ route('users.edit', $u) }}" class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-medium transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $u) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium transition">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400 text-sm">
                                Tidak ada akun yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-slate-200/80 bg-slate-50/50">
            {{ $users->links() }}
        </div>
    </div>

</div>
@endsection
