@extends('layouts.kasir')

@section('title', 'Transaksi Penjualan Baru')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Transaksi Penjualan Baru</h2>
    <p class="text-gray-600">Pilih barang, masukkan jumlah, dan proses pembayaran</p>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <form method="POST" action="{{ route('kasir.transaksi-penjualan.store') }}" id="transaksiForm" class="space-y-6">
        @csrf

        <!-- Header Info -->
        <fieldset class="border border-gray-200 rounded-lg p-4">
            <legend class="text-lg font-medium text-gray-900 px-2">Informasi Transaksi</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
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

        <!-- Cart Items -->
        <fieldset class="border border-gray-200 rounded-lg p-4">
            <div class="flex justify-between items-center mb-4">
                <legend class="text-lg font-medium text-gray-900">Keranjang Belanja</legend>
                <button type="button" id="addItemBtn"
                        class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                    <i class="fas fa-plus mr-2"></i> Tambah Barang
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="cartTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/5">Barang (Stok)</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty <span class="text-red-500">*</span></th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="cartBody">
                        <!-- Rows added dynamically -->
                    </tbody>
                </table>
            </div>

            @error('items')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <!-- Total Summary -->
            <div class="mt-4 bg-gray-50 rounded-lg p-4" id="summaryCard" style="display: none;">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="text-sm text-gray-500">Total Item</label>
                        <p class="text-2xl font-bold text-gray-900" id="totalItems">0</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Total Qty</label>
                        <p class="text-2xl font-bold text-gray-900" id="totalQty">0</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Subtotal</label>
                        <p class="text-2xl font-bold text-gray-900" id="subtotalDisplay">Rp 0</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Total</label>
                        <p class="text-2xl font-bold text-green-600" id="grandTotalDisplay">Rp 0</p>
                    </div>
                </div>
            </div>
        </fieldset>

        <!-- Payment Section -->
        <fieldset class="border border-gray-200 rounded-lg p-4 bg-green-50" id="paymentSection" style="display: none;">
            <legend class="text-lg font-medium text-gray-900 px-2">Pembayaran</legend>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label for="metode_pembayaran" class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran <span class="text-red-500">*</span></label>
                    <select name="metode_pembayaran" id="metode_pembayaran" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Metode</option>
                        <option value="tunai">Tunai</option>
                        <option value="non-tunai">Non-Tunai (Transfer/Qris)</option>
                    </select>
                    @error('metode_pembayaran')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="tunaiFields" style="display: none;">
                    <label for="total_bayar" class="block text-sm font-medium text-gray-700 mb-1">Uang Diterima <span class="text-red-500">*</span></label>
                    <input type="number" name="total_bayar" id="total_bayar"
                           required min="0" step="100"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Masukkan uang diterima">
                    @error('total_bayar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="nonTunaiFields" style="display: none;">
                    <label for="total_bayar" class="block text-sm font-medium text-gray-700 mb-1">Total Bayar <span class="text-red-500">*</span></label>
                    <input type="number" name="total_bayar" id="total_bayar_nontunai"
                           required min="0" step="100" readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100"
                           placeholder="Otomatis = Total Transaksi">
                    <input type="hidden" name="total_bayar" id="total_bayar">
                    @error('total_bayar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="kembalianField" style="display: none;" class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kembalian</label>
                    <div class="bg-white border border-gray-300 rounded-lg px-4 py-3">
                        <p class="text-3xl font-bold text-gray-900" id="kembalianDisplay">Rp 0</p>
                        <p class="text-sm text-gray-500">Kembalian akan dihitung otomatis</p>
                    </div>
                </div>
            </div>
        </fieldset>

        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
            <a href="{{ route('kasir.transaksi-penjualan.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Batal
            </a>
            <button type="submit" id="submitBtn" disabled
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition opacity-50 cursor-not-allowed text-lg">
                <i class="fas fa-shopping-cart mr-2"></i> Proses Transaksi
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
        'stok' => (int) $b->stok,
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

    // Format number input dengan separator ribuan
    function formatNumberInput(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        if (value) {
            input.value = new Intl.NumberFormat('id-ID').format(value);
        }
    }

    // Update subtotal untuk satu row
    function updateRowSubtotal(row) {
        const qtyInput = row.querySelector('.qty-input');
        const hargaInput = row.querySelector('.harga-input');
        const qty = parseInt(qtyInput.value.replace(/[^0-9]/g, '')) || 0;
        const harga = parseFloat(hargaInput.value.replace(/[^0-9]/g, '')) || 0;
        const subtotal = qty * harga;
        row.querySelector('.subtotal-cell').textContent = formatRupiah(subtotal);
        row.querySelector('.subtotal-hidden').value = subtotal;
        return { qty, harga, subtotal };
    }

    // Update all totals
    function updateTotals() {
        let totalItems = 0;
        let totalQty = 0;
        let subtotal = 0;

        document.querySelectorAll('#cartBody tr').forEach(row => {
            const { qty, subtotal: rowSubtotal } = updateRowSubtotal(row);
            totalItems++;
            totalQty += qty;
            subtotal += rowSubtotal;
        });

        document.getElementById('totalItems').textContent = totalItems;
        document.getElementById('totalQty').textContent = totalQty;
        document.getElementById('subtotalDisplay').textContent = formatRupiah(subtotal);
        document.getElementById('grandTotalDisplay').textContent = formatRupiah(subtotal);

        // Update hidden total_bayar for non-tunai
        document.getElementById('total_bayar_nontunai').value = subtotal;
        document.getElementById('total_bayar').value = subtotal;

        // Show/hide summary & payment section
        const hasItems = totalItems > 0;
        document.getElementById('summaryCard').style.display = hasItems ? 'block' : 'none';
        document.getElementById('paymentSection').style.display = hasItems ? 'block' : 'none';

        // Update kembalian if tunai
        updateKembalian();

        // Enable/disable submit
        const metodePembayaran = document.getElementById('metode_pembayaran').value;
        const totalBayar = parseFloat(document.getElementById('total_bayar').value) || 0;
        const canSubmit = hasItems && metodePembayaran && (
            (metodePembayaran === 'tunai' && totalBayar >= subtotal) ||
            (metodePembayaran === 'non-tunai')
        );
        document.getElementById('submitBtn').disabled = !canSubmit;
        document.getElementById('submitBtn').classList.toggle('opacity-50', !canSubmit);
        document.getElementById('submitBtn').classList.toggle('cursor-not-allowed', !canSubmit);
    }

    // Update kembalian display
    function updateKembalian() {
        const metodePembayaran = document.getElementById('metode_pembayaran').value;
        const subtotal = parseFloat(document.getElementById('subtotalDisplay').textContent.replace(/[^0-9.-]/g, '')) || 0;

        if (metodePembayaran === 'tunai') {
            document.getElementById('tunaiFields').style.display = 'block';
            document.getElementById('nonTunaiFields').style.display = 'none';
            document.getElementById('kembalianField').style.display = 'block';

            const totalBayar = parseFloat(document.getElementById('total_bayar').value.replace(/[^0-9]/g, '')) || 0;
            const kembalian = totalBayar - subtotal;
            document.getElementById('kembalianDisplay').textContent = formatRupiah(Math.max(0, kembalian));
            document.getElementById('kembalianDisplay').className = kembalian >= 0 ? 'text-3xl font-bold text-green-600' : 'text-3xl font-bold text-red-600';
        } else if (metodePembayaran === 'non-tunai') {
            document.getElementById('tunaiFields').style.display = 'none';
            document.getElementById('nonTunaiFields').style.display = 'block';
            document.getElementById('kembalianField').style.display = 'none';
            document.getElementById('total_bayar_nontunai').value = subtotal;
            document.getElementById('total_bayar').value = subtotal;
        } else {
            document.getElementById('tunaiFields').style.display = 'none';
            document.getElementById('nonTunaiFields').style.display = 'none';
            document.getElementById('kembalianField').style.display = 'none';
        }
        updateTotals(); // Re-check submit button
    }

    // Create new cart row
    function createRow(data = {}) {
        rowIndex++;
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        row.dataset.index = rowIndex;

        const barangOptions = barangData.map(b => {
            const stokInfo = b.stok > 0 ? ` (Stok: ${b.stok} ${b.satuan})` : ` <span class="text-red-500">(Habis)</span>`;
            return `<option value="${b.id}" ${data.id_barang == b.id ? 'selected' : ''} data-stok="${b.stok}" data-harga="${b.harga}" data-satuan="${b.satuan}">${b.nama}${stokInfo}</option>`;
        }).join('');

        row.innerHTML = `
            <td class="px-4 py-2">
                <select name="items[${rowIndex}][id_barang]" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 barang-select"
                        data-satuan="${data.satuan || ''}" data-harga="${data.harga_satuan || ''}" data-stok="${data.stok || ''}">
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
                <input type="text" name="items[${rowIndex}][harga_satuan]" value="${data.harga_satuan ? formatRupiah(data.harga_satuan) : ''}"
                       required
                       class="w-28 px-3 py-2 border border-gray-300 rounded-lg text-right focus:outline-none focus:ring-2 focus:ring-blue-500 harga-input"
                       readonly>
                <input type="hidden" name="items[${rowIndex}][harga_satuan]" class="harga-hidden" value="${data.harga_satuan || ''}">
            </td>
            <td class="px-4 py-2 text-center">
                <input type="number" name="items[${rowIndex}][jumlah]" value="${data.jumlah || ''}"
                       required min="1" step="1" max="${data.stok || 999}"
                       class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-center focus:outline-none focus:ring-2 focus:ring-blue-500 qty-input">
                @error('items.*.jumlah')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </td>
            <td class="px-4 py-2 text-right subtotal-cell font-medium text-gray-900">
                ${data.jumlah && data.harga_satuan ? formatRupiah(data.jumlah * data.harga_satuan) : 'Rp 0'}
                <input type="hidden" name="items[${rowIndex}][subtotal]" class="subtotal-hidden" value="${data.jumlah && data.harga_satuan ? data.jumlah * data.harga_satuan : 0}">
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
    document.getElementById('addItemBtn').addEventListener('click', () => {
        const row = createRow();
        document.getElementById('cartBody').appendChild(row);
        updateTotals();
    });

    document.getElementById('cartBody').addEventListener('input', (e) => {
        if (e.target.matches('.qty-input')) {
            // Validate max stok
            const row = e.target.closest('tr');
            const select = row.querySelector('.barang-select');
            const selectedOption = select.options[select.selectedIndex];
            const maxStok = parseInt(selectedOption.dataset.stok) || 999;
            const qty = parseInt(e.target.value) || 0;
            if (qty > maxStok) {
                e.target.value = maxStok;
            }
            updateTotals();
        }
    });

    document.getElementById('cartBody').addEventListener('change', (e) => {
        if (e.target.matches('.barang-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const barangId = parseInt(e.target.value);
            const barang = barangData.find(b => b.id === barangId);
            const row = e.target.closest('tr');

            if (barang) {
                row.querySelector('.satuan-cell').textContent = barang.satuan;
                row.querySelector('.harga-input').value = formatRupiah(barang.harga);
                row.querySelector('.harga-hidden').value = barang.harga;
                row.querySelector('.qty-input').max = barang.stok;
                row.querySelector('.qty-input').value = 1;
            } else {
                row.querySelector('.satuan-cell').textContent = '-';
                row.querySelector('.harga-input').value = '';
                row.querySelector('.harga-hidden').value = '';
                row.querySelector('.qty-input').max = 999;
                row.querySelector('.qty-input').value = '';
            }
            updateTotals();
        }
    });

    document.getElementById('cartBody').addEventListener('click', (e) => {
        if (e.target.closest('.delete-row')) {
            e.target.closest('tr').remove();
            updateTotals();
        }
    });

    // Metode pembayaran change
    document.getElementById('metode_pembayaran').addEventListener('change', updateKembalian);

    // Tunai input - format number & update kembalian
    document.getElementById('total_bayar').addEventListener('input', (e) => {
        formatNumberInput(e.target);
        updateKembalian();
    });

    // Add first row on load
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('addItemBtn').click();
    });
</script>
@endsection