# Sistem Informasi Manajemen Koperasi Desa Merah Putih

Aplikasi web Laravel untuk unit usaha penjualan sembako koperasi desa.
Scope HANYA: data barang, pemasok, barang masuk, transaksi penjualan,
pembayaran, dan persediaan. JANGAN tambahkan fitur simpan-pinjam,
penggajian, apotek, atau pengadaan otomatis — semua itu di luar scope.

## Pengguna & role (minimal 2 role wajib, kita pakai 3)
- kasir — mencatat transaksi penjualan & pembayaran
- petugas_persediaan — mencatat barang masuk, memantau stok
- pengurus — memantau dashboard, laporan, dan audit trail

## Skema database (sudah final, jangan diubah strukturnya tanpa diskusi)
Tabel: kategori_barang, barang, pemasok, pengguna, penerimaan_barang,
detail_penerimaan, transaksi_penjualan, detail_transaksi, pembayaran,
mutasi_persediaan.
Relasi detail ada di file koperasi_merah_putih.sql pada folder database/.
Catatan penting:
- `barang.stok` = stok terkini (bukan tabel persediaan terpisah)
- `mutasi_persediaan` = kartu stok/ledger, setiap barang masuk atau
  transaksi penjualan WAJIB menambah satu baris di sini
- `pembayaran` 1:1 dengan `transaksi_penjualan`

## Fitur wajib (dari ketentuan Tugas Akhir)
1. Login/logout dengan middleware role
2. Minimal 3 modul master data: barang, kategori_barang, pemasok
   (tambah, tampil, edit, hapus, cari, detail, validasi form)
3. Modul transaksi penjualan: pilih barang → hitung subtotal & total →
   simpan ke transaksi_penjualan + detail_transaksi + pembayaran →
   otomatis tulis baris ke mutasi_persediaan dan kurangi barang.stok
4. Dashboard: 3 kartu KPI (penjualan hari ini, transaksi hari ini,
   barang stok rendah), 2 grafik (penjualan bulanan, produk terlaris),
   1 tabel info (barang stok rendah) — semua dihitung live dari DB
5. Minimal 2 laporan (mis. laporan penjualan & laporan persediaan)
   dengan filter periode, pencarian, pengurutan, total, export PDF/Excel
6. Audit trail: tabel activity_logs, catat tiap aksi tambah/edit/hapus
   (siapa, modul, aktivitas, waktu)

## Gaya kode
- Laravel terbaru, Eloquent ORM (jangan raw query kecuali agregasi laporan)
- Blade + Tailwind untuk tampilan (atau sesuaikan jika sudah ada starter kit)
- Validasi pakai Form Request, bukan validasi manual di controller
- Penamaan tabel & kolom HARUS persis sama dengan skema di atas