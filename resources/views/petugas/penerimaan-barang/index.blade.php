@extends('layouts.petugas')

@section('title', 'Penerimaan Barang')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Penerimaan Barang</h2>
        <p class="text-gray-600">Kelola barang masuk dari pemasok</p>
    </div>
    <a href="{{ route('petugas.penerimaan-barang.create') }}"
       class="btn-primary inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
        <i class="fas fa-plus mr-2"></i> Tambah Penerimaan
    </a>
</div>

<!-- Search & Filter Form -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('petugas.penerimaan-barang.index') }}" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px] max-w-md">
            <label for="search" class="sr-only">Cari pemasok</label>
            <input type="text" name="search" id="search"
                   value="{{ $search }}"
                   placeholder="Cari nama pemasok..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="min-w-[180px]">
            <label for="pemasok_id" class="sr-only">Filter pemasok</label>
            <select name="pemasok_id" id="pemasok_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Pemasok</option>
                @foreach($pemasoks as $pemasok)
                    <option value="{{ $pemasok->id_pemasok }}" {{ $pemasokId == $pemasok->id_pemasok ? 'selected' : '' }}>
                        {{ $pemasok->nama_pemasok }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[150px]">
            <label for="tanggal_awal" class="sr-only">Tanggal awal</label>
            <input type="date" name="tanggal_awal" id="tanggal_awal"
                   value="{{ $tanggalAwal }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="min-w-[150px]">
            <label for="tanggal_akhir" class="sr-only">Tanggal akhir</label>
            <input type="date" name="tanggal_akhir" id="tanggal_akhir"
                   value="{{ $tanggalAkhir }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-search mr-1"></i> Filter
        </button>
        @if($search || $pemasokId || $tanggalAwal || $tanggalAkhir)
            <a href="{{ route('petugas.penerimaan-barang.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Reset
            </a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    @if($penerimaans->isEmpty())
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-truck-loading text-4xl mb-2"></i>
            <p>Tidak ada data penerimaan barang</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Penerimaan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemasok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Item</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Nilai</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($penerimaans as $penerimaan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-mono">{{ $penerimaan->id_penerimaan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $penerimaan->tanggal->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ $penerimaan->pemasok->nama_pemasok }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $penerimaan->petugas->nama }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-gray-900">{{ $penerimaan->detailPenerimaan->count() }} item</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-gray-900">
                            Rp {{ number_format($penerimaan->detailPenerimaan->sum(fn($d) => $d->jumlah * $d->harga_beli), 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('petugas.penerimaan-barang.show', $penerimaan) }}"
                               class="text-blue-600 hover:text-blue-900" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-gray-200">
            {{ $penerimaans->links() }}
        </div>
    @endif
</div>
@endsection