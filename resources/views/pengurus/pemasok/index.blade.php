@extends('layouts.pengurus')

@section('title', 'Data Pemasok')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Data Pemasok</h2>
        <p class="text-gray-600">Kelola data pemasok barang</p>
    </div>
    <a href="{{ route('pengurus.pemasok.create') }}"
       class="btn-primary inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        <i class="fas fa-plus mr-2"></i> Tambah Pemasok
    </a>
</div>

<!-- Search Form -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('pengurus.pemasok.index') }}" class="flex gap-4">
        <div class="flex-1 max-w-md">
            <label for="search" class="sr-only">Cari pemasok</label>
            <input type="text" name="search" id="search"
                   value="{{ $search }}"
                   placeholder="Cari nama pemasok atau kontak..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-search mr-1"></i> Cari
        </button>
        @if($search)
            <a href="{{ route('pengurus.pemasok.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Reset
            </a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    @if($pemasoks->isEmpty())
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-truck text-4xl mb-2"></i>
            <p>Tidak ada data pemasok</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pemasok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerimaan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($pemasoks as $pemasok)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-medium">{{ $pemasok->nama_pemasok }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $pemasok->kontak ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 max-w-xs truncate">{{ $pemasok->alamat ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $pemasok->penerimaan_barang_count ?? 0 }} kali</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $pemasok->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('pengurus.pemasok.show', $pemasok) }}" class="text-blue-600 hover:text-blue-900" title="Detail"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('pengurus.pemasok.edit', $pemasok) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="{{ route('pengurus.pemasok.destroy', $pemasok) }}" class="inline" onsubmit="return confirm('Yakin hapus pemasok ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-gray-200">
            {{ $pemasoks->links() }}
        </div>
    @endif
</div>
@endsection