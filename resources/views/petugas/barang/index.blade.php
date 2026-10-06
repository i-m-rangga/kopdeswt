@extends('layouts.petugas')

@section('title', 'Data Barang')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Data Barang</h2>
        <p class="text-gray-600">Kelola data barang sembako</p>
    </div>
    <a href="{{ route('petugas.barang.create') }}"
       class="btn-primary inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        <i class="fas fa-plus mr-2"></i> Tambah Barang
    </a>
</div>

<!-- Search & Filter Form -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('petugas.barang.index') }}" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px] max-w-md">
            <label for="search" class="sr-only">Cari barang</label>
            <input type="text" name="search" id="search"
                   value="{{ $search }}"
                   placeholder="Cari nama barang..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="min-w-[180px]">
            <label for="kategori_id" class="sr-only">Filter kategori</label>
            <select name="kategori_id" id="kategori_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $kategori)
                    <option value="{{ $kategori->id_kategori }}" {{ $kategoriId == $kategori->id_kategori ? 'selected' : '' }}>
                        {{ $kategori->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center">
            <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" name="stok_rendah" value="1" {{ $stokRendah ? 'checked' : '' }}
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Stok Rendah Saja</span>
            </label>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-search mr-1"></i> Filter
        </button>
        @if($search || $kategoriId || $stokRendah)
            <a href="{{ route('petugas.barang.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Reset
            </a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    @if($barangs->isEmpty())
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-box text-4xl mb-2"></i>
            <p>Tidak ada data barang</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Min</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($barangs as $barang)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-medium">{{ $barang->nama_barang }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $barang->kategori->nama_kategori ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $barang->satuan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-gray-900">Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center {{ $barang->isStokRendah() ? 'font-bold text-red-600' : 'text-gray-900' }}">
                            {{ $barang->stok }} {{ $barang->satuan }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-gray-500">{{ $barang->stok_minimum }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($barang->isStokRendah())
                                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full">Stok Rendah</span>
                            @else
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Normal</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('petugas.barang.show', $barang) }}" class="text-blue-600 hover:text-blue-900" title="Detail"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('petugas.barang.edit', $barang) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="{{ route('petugas.barang.destroy', $barang) }}" class="inline" onsubmit="return confirm('Yakin hapus barang ini?')">
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
            {{ $barangs->links() }}
        </div>
    @endif
</div>
@endsection