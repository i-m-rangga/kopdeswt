@extends('layouts.petugas')

@section('title', 'Tambah Penerimaan Barang')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Tambah Penerimaan Barang</h2>
    <p class="text-gray-600">Catat barang masuk dari pemasok</p>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <form method="POST" action="{{ route('petugas.penerimaan-barang.store') }}" id="penerimaanForm" class="space-y-6">
        @csrf

        <!-- Header Info -->
        <fieldset class="border border-gray-200 rounded-lg p-4">
            <legend class="text-lg font-medium text-gray-900 px-2">Informasi Penerimaan</legend>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label for="id_pemasok" class="block text-sm font-medium text-gray-700 mb-1">Pemasok <span class="text-red-500">*</span></label>
                    <select name="id_pemasok" id="id_pemasok" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Pemasok</option>
                        @foreach($pemasoks as $pemasok)
                            <option value="{{ $pemasok->id_pemasok }}">{{ $pemasok->nama_pemasok }}</option>
                        @endforeach
                    </select>
                    @error('id_pemasok')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" id="tanggal"
                           value="{{ old('tanggal', now()->toDateString()) }}"
                           required max="{{ now()->toDateString() }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('tanggal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </fieldset>

        <!-- Items Table -->
        <fieldset class="border border-gray-200 rounded-lg p-4">
            <div class="flex justify-between items-center mb-4">
                <legend class="text-lg font-medium text-gray-900">Daftar Barang</legend>
                <button type="button" id="addRowBtn"
                        class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                    <i class="fas fa-plus mr-2"></i> Tambah Barang
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="itemsTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">Barang</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah <span class="text-red-500">*</span></th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Beli <span class="text-red-500">*</span></th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <!-- Rows added dynamically -->
                    </tbody>
                </table>
            </div>

            @error('items')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="mt-4 text-right" id="totalRow" style="display: none;">
                <span class="text-lg font-semibold text-gray-900">
                    Total: <span id="grandTotal">Rp 0</span>
                </span>
            </div>
        </fieldset>

        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
            <a href="{{ route('petugas.penerimaan-barang.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Batal
            </a>
            <button type="submit" id="submitBtn" disabled
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition opacity-50 cursor-not-allowed">
                <i class="fas fa-save mr-2"></i> Simpan Penerimaan
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Data barang dari server
    const barangData = @json($barangs->map(fn($b) => [
        'id' => $b->id_barang,
        'nama' => $b->nama_barang,
        'satuan' => $b->satuan,
        'harga' => (float) $b->harga,
    ])->values());

    let rowIndex = 0;

    // Format rupiah
    function formatRupiah(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }

    // Parse rupiah string to number
    function parseRupiah(str) {
        return parseFloat(str.replace(/[^0-9.-]/g, '')) || 0;
    }

    // Update subtotal untuk satu row
    function updateSubtotal(row) {
        const jumlah = parseInt(row.querySelector('[name$="[jumlah]"]').value) || 0;
        const harga = parseFloat(row.querySelector('[name$="[harga_beli]"]').value) || 0;
        const subtotal = jumlah * harga;
        row.querySelector('.subtotal-cell').textContent = formatRupiah(subtotal);
        return subtotal;
    }

    // Update grand total
    function updateGrandTotal() {
        let total = 0;
        document.querySelectorAll('#itemsBody tr').forEach(row => {
            total += updateSubtotal(row);
        });
        document.getElementById('grandTotal').textContent = formatRupiah(total);
        document.getElementById('totalRow').style.display = total > 0 ? 'block' : 'none';
        document.getElementById('submitBtn').disabled = total === 0;
        document.getElementById('submitBtn').classList.toggle('opacity-50', total === 0);
        document.getElementById('submitBtn').classList.toggle('cursor-not-allowed', total === 0);
    }

    // Create new row
    function createRow(data = {}) {
        rowIndex++;
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        row.dataset.index = rowIndex;

        const barangOptions = barangData.map(b =>
            `<option value="${b.id}" ${data.id_barang == b.id ? 'selected' : ''}>${b.nama}</option>`
        ).join('');

        row.innerHTML = `
            <td class="px-4 py-2">
                <select name="items[${rowIndex}][id_barang]" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 barang-select"
                        data-satuan="${data.satuan || ''}">
                    <option value="">Pilih Barang</option>
                    ${barangOptions}
                </select>
                @error('items.*.id_barang')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </td>
            <td class="px-4 py-2 whitespace-nowrap text-gray-600 satuan-cell">
                ${data.satuan || '-'}
            </td>
            <td class="px-4 py-2 text-right">
                <input type="number" name="items[${rowIndex}][jumlah]" value="${data.jumlah || ''}"
                       required min="1" step="1"
                       class="w-24 px-3 py-2 border border-gray-300 rounded-lg text-right focus:outline-none focus:ring-2 focus:ring-blue-500 qty-input">
                @error('items.*.jumlah')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </td>
            <td class="px-4 py-2 text-right">
                <input type="number" name="items[${rowIndex}][harga_beli]" value="${data.harga_beli || ''}"
                       required min="0" step="0.01"
                       class="w-32 px-3 py-2 border border-gray-300 rounded-lg text-right focus:outline-none focus:ring-2 focus:ring-blue-500 harga-input">
                @error('items.*.harga_beli')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </td>
            <td class="px-4 py-2 text-right subtotal-cell font-medium text-gray-900">
                ${data.jumlah && data.harga_beli ? formatRupiah(data.jumlah * data.harga_beli) : 'Rp 0'}
            </td>
            <td class="px-4 py-2 text-center">
                <button type="button" class="text-red-600 hover:text-red-900 delete-row" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        return row;
    }

    // Event delegation
    document.getElementById('addRowBtn').addEventListener('click', () => {
        const row = createRow();
        document.getElementById('itemsBody').appendChild(row);
        updateGrandTotal();
    });

    document.getElementById('itemsBody').addEventListener('input', (e) => {
        if (e.target.matches('.qty-input, .harga-input')) {
            updateGrandTotal();
        }
    });

    document.getElementById('itemsBody').addEventListener('change', (e) => {
        if (e.target.matches('.barang-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const satuan = selectedOption.textContent.includes(' - ') ? '' : '';
            // Find barang data
            const barangId = parseInt(e.target.value);
            const barang = barangData.find(b => b.id === barangId);
            if (barang) {
                e.target.closest('tr').querySelector('.satuan-cell').textContent = barang.satuan;
                // Auto-fill harga beli with harga jual as suggestion
                e.target.closest('tr').querySelector('.harga-input').value = barang.harga;
            } else {
                e.target.closest('tr').querySelector('.satuan-cell').textContent = '-';
            }
            updateGrandTotal();
        }
    });

    document.getElementById('itemsBody').addEventListener('click', (e) => {
        if (e.target.closest('.delete-row')) {
            e.target.closest('tr').remove();
            updateGrandTotal();
        }
    });

    // Add first row on load
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('addRowBtn').click();
    });
</script>
@endsection