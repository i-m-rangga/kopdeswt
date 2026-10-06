# Sistem Informasi Manajemen Koperasi Desa Merah Putih

Aplikasi web Laravel untuk unit usaha penjualan sembako koperasi desa. Dibangun dengan Laravel 12, Blade + Tailwind CSS, dan MySQL/MariaDB.

---

## 📋 Daftar Fitur

### 1. **Autentikasi & Otorisasi**
- Login/Logout dengan username & password
- Middleware role-based access control (RBAC)
- 3 Role: `kasir`, `petugas_persediaan`, `pengurus`
- Redirect otomatis ke dashboard sesuai role setelah login
- Proteksi route dengan middleware `role:role1,role2`
- Validasi status user (aktif/nonaktif)

### 2. **Master Data (CRUD Lengkap)**

| Modul | Role Akses | Fitur |
|-------|------------|-------|
| **Kategori Barang** | Pengurus, Petugas | Tambah, Tampil, Edit, Hapus, Detail, Cari, Pagination |
| **Barang** | Pengurus, Petugas | CRUD + filter kategori, pencarian, stok rendah, pagination |
| **Pemasok** | Pengurus saja | CRUD + pencarian kontak/alamat, pagination |

**Validasi**: Form Request (Server-side), Unique checks, Foreign key constraints

### 3. **Transaksi Penjualan (Kasir)**
- Pilih barang dari dropdown (hanya stok > 0)
- Keranjang dinamis: tambah/hapus baris, auto-fill satuan & harga
- Hitung subtotal & grand total real-time (JavaScript)
- Metode pembayaran: **Tunai** (input uang + hitung kembalian) / **Non-Tunai** (auto = total)
- Validasi: stok cukup, uang tunai ≥ total
- Simpan: `transaksi_penjualan` + `detail_transaksi` + `pembayaran` (1:1)
- **Otomatis**: Kurangi `barang.stok` + catat `mutasi_persediaan` (jenis: keluar)

### 4. **Penerimaan Barang (Petugas Persediaan)**
- Pilih pemasok, tanggal
- Form dinamis: tambah multiple barang + jumlah + harga beli
- Validasi: minimal 1 item, jumlah ≥ 1, harga ≥ 0
- Simpan: `penerimaan_barang` + `detail_penerimaan`
- **Otomatis**: Tambah `barang.stok` + catat `mutasi_persediaan` (jenis: masuk)

### 5. **Dashboard**

| Role | Fitur |
|------|-------|
| **Kasir** | Quick actions: Transaksi Baru, Riwayat, Lihat Data Barang |
| **Petugas** | Quick actions: Barang Masuk, Data Barang, Stok Rendah |
| **Pengurus** | **3 KPI**: Penjualan Hari Ini, Transaksi Hari Ini, Barang Stok Rendah<br>**2 Grafik Chart.js**: Penjualan 30 Hari (Line dual-axis), Top 10 Produk Terlaris (Horizontal Bar)<br>**Tabel**: Barang Stok Rendah (detail kekurangan)<br>Quick links ke Laporan & Audit Trail |

### 6. **Laporan (Pengurus) - Export PDF & Excel**

| Laporan | Filter | Export |
|---------|--------|--------|
| **Penjualan** | Tanggal, Barang, Pencarian nama, Sorting (6 opsi) | PDF (Landscape) + Excel |
| **Persediaan (Mutasi Stok)** | Tanggal, Barang | PDF (Landscape) + Excel |

**PDF**: `barryvdh/laravel-dompdf` - Header kop, summary cards, tabel berwarna
**Excel**: `maatwebsite/excel` - Auto-size, header bold, grand total row

### 7. **Audit Trail / Activity Log (Pengurus)**
- Tabel `activity_logs` (id_log, id_pengguna, aktivitas, modul, data_diproses JSON, waktu)
- Trait `LogsActivity` otomatis catat: **create, update, delete**
- Modul tercatat: Barang, Pemasok, Penerimaan Barang, Transaksi Penjualan
- Data JSON: `{old: {...}, new: {...}}` (hanya field yang berubah saat update)
- Halaman riwayat: Filter pengguna, modul, aktivitas, tanggal
- Detail modal AJAX: Perbandingan Old vs New (JSON pretty print)

---

## 🗄️ Struktur Database (14 Tabel)

```sql
1. kategori_barang       (id_kategori, nama_kategori)
2. barang                (id_barang, id_kategori, nama_barang, satuan, harga, stok, stok_minimum)
3. pemasok               (id_pemasok, nama_pemasok, kontak, alamat)
4. pengguna              (id_pengguna, nama, username, password, role, status)
5. penerimaan_barang     (id_penerimaan, tanggal, id_pemasok, id_petugas)
6. detail_penerimaan     (id_detail, id_penerimaan, id_barang, jumlah, harga_beli)
7. transaksi_penjualan   (id_transaksi, tanggal, id_kasir, total_transaksi)
8. detail_transaksi      (id_detail, id_transaksi, id_barang, jumlah, harga_satuan, subtotal)
9. pembayaran            (id_pembayaran, id_transaksi[UNIQUE], total_bayar, metode, status, tanggal_bayar)
10. mutasi_persediaan    (id_mutasi, id_barang, tanggal, jenis_mutasi[masuk/keluar], jumlah, stok_akhir, referensi)
11. activity_logs        (id_log, id_pengguna, aktivitas, modul, data_diproses, waktu)
12. password_reset_tokens
13. cache, jobs (Laravel default)
```

**Aturan Penting:**
- `barang.stok` = stok real-time (single source of truth)
- Setiap barang masuk/keluar → **WAJIB** insert ke `mutasi_persediaan`
- `pembayaran` 1:1 dengan `transaksi_penjualan` (UNIQUE FK)

---

## 🚀 Instalasi

### Prasyarat
- PHP ≥ 8.2
- Composer
- MySQL/MariaDB / SQLite
- Node.js & NPM (untuk Vite/Tailwind build)

### Langkah Instalasi

```bash
# 1. Clone / extract project
cd sistem-informasi-manajemen-koperasi-desa-merah-putih

# 2. Install dependencies PHP
composer install

# 3. Install dependencies JS (untuk Tailwind/Vite)
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate app key
php artisan key:generate

# 6. Konfigurasi database di .env
# SQLite (default, paling mudah):
# DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite
# touch database/database.sqlite

# Atau MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=koperasi_merah_putih
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Jalankan migrasi + seeder (buat tabel + akun demo)
php artisan migrate --seed

# 8. Build assets (CSS/JS)
npm run build
# Atau development mode:
# npm run dev

# 9. Jalankan server
php artisan serve
```

### Akses Aplikasi
Buka browser: `http://localhost:8000`

---

## 👤 Akun Demo (Dari Seeder)

Semua password: **`password`**

| Role | Username | Nama | Akses |
|------|----------|------|-------|
| **Kasir** | `budi.kasir` | Budi Santoso | `/kasir/*` - Transaksi penjualan, riwayat |
| **Petugas Persediaan** | `sari.gudang` | Sari Wulandari | `/petugas/*` - Kategori, Barang, Penerimaan Barang |
| **Pengurus** | `ahmad.pengurus` | Ahmad Fauzi | `/pengurus/*` - Semua master, Laporan, Dashboard KPI, Audit Trail |

---

## 🔐 Role & Permission Matrix

| Fitur | Kasir | Petugas Persediaan | Pengurus |
|-------|-------|-------------------|----------|
| Login/Logout | ✅ | ✅ | ✅ |
| Dashboard | ✅ | ✅ | ✅ (KPI + Grafik) |
| Kategori Barang (CRUD) | ❌ | ✅ | ✅ |
| Barang (CRUD) | ❌ (read-only via link) | ✅ | ✅ |
| Pemasok (CRUD) | ❌ | ❌ | ✅ |
| Penerimaan Barang | ❌ | ✅ | ❌ |
| Transaksi Penjualan | ✅ | ❌ | ❌ |
| Laporan Penjualan | ❌ | ❌ | ✅ |
| Laporan Persediaan | ❌ | ❌ | ✅ |
| Audit Trail | ❌ | ❌ | ✅ |

---

## 🛠️ Tech Stack

- **Backend**: Laravel 12.x
- **Frontend**: Blade Templates + Tailwind CSS 3.x + Chart.js 4.x
- **Database**: MySQL/MariaDB / SQLite
- **Export PDF**: `barryvdh/laravel-dompdf` ^3.1
- **Export Excel**: `maatwebsite/excel` ^3.1
- **Auth**: Laravel Session Guard + Custom Provider (`pengguna` table)
- **Validation**: Form Request Classes
- **Architecture**: Resource Controllers, Traits, Eloquent Relationships

---

## 📁 Struktur Project Penting

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/LoginController.php
│   │   ├── Kasir/TransaksiPenjualanController.php
│   │   ├── Petugas/
│   │   │   ├── KategoriBarangController.php
│   │   │   ├── BarangController.php
│   │   │   └── PenerimaanBarangController.php
│   │   └── Pengurus/
│   │       ├── DashboardController.php
│   │       ├── KategoriBarangController.php
│   │       ├── BarangController.php
│   │       ├── PemasokController.php
│   │       ├── Laporan/LaporanController.php
│   │       └── ActivityLogController.php
│   ├── Requests/ (Form Requests per modul)
│   └── Middleware/RoleMiddleware.php
├── Models/ (10 models + ActivityLog)
│   ├── Barang.php (trait LogsActivity)
│   ├── Pemasok.php (trait LogsActivity)
│   ├── PenerimaanBarang.php (trait LogsActivity)
│   └── TransaksiPenjualan.php (trait LogsActivity)
└── Traits/LogsActivity.php

database/
├── migrations/ (14 files)
└── seeders/DatabaseSeeder.php (3 akun demo)

resources/views/
├── layouts/ (kasir, petugas, pengurus)
├── auth/login.blade.php
├── kasir/ (dashboard, transaksi-penjualan)
├── petugas/ (dashboard, kategori-barang, barang, penerimaan-barang)
└── pengurus/ (dashboard, kategori-barang, barang, pemasok, laporan, activity-log)

routes/web.php (semua route dengan middleware role)
```

---

## 🧪 Testing Checklist

### Login & Role Redirect
- [ ] Login `budi.kasir` → redirect ke `/kasir/dashboard`
- [ ] Login `sari.gudang` → redirect ke `/petugas/dashboard`
- [ ] Login `ahmad.pengurus` → redirect ke `/pengurus/dashboard`
- [ ] User nonaktif → tidak bisa login
- [ ] Kasir akses `/petugas/*` → 403 Forbidden
- [ ] Petugas akses `/pengurus/*` → 403 Forbidden

### Master Data (Petugas & Pengurus)
- [ ] CRUD Kategori Barang (create, read, update, delete, search)
- [ ] CRUD Barang (create, read, update, delete, search, filter kategori, stok rendah)
- [ ] CRUD Pemasok (pengurus only)

### Transaksi (Kasir)
- [ ] Buat transaksi baru: pilih barang, input qty, hitung total
- [ ] Bayar tunai: input uang, hitung kembalian
- [ ] Bayar non-tunai: auto total
- [ ] Validasi stok tidak minus
- [ ] Stok terkurang otomatis setelah simpan
- [ ] Mutasi persediaan (keluar) tercatat

### Penerimaan (Petugas)
- [ ] Buat penerimaan: pilih pemasok, tambah multiple barang
- [ ] Stok bertambah otomatis
- [ ] Mutasi persediaan (masuk) tercatat

### Dashboard Pengurus
- [ ] 3 KPI tampil benar (live query)
- [ ] Grafik penjualan 30 hari render (Chart.js)
- [ ] Grafik produk terlaris render
- [ ] Tabel stok rendah tampil

### Laporan (Pengurus)
- [ ] Filter tanggal, barang, search, sort
- [ ] Export PDF (download)
- [ ] Export Excel (download)

### Audit Trail (Pengurus)
- [ ] Filter pengguna, modul, aktivitas, tanggal
- [ ] Klik "Lihat Detail" → modal tampil Old vs New JSON
- [ ] Log otomatis saat create/update/delete di 4 modul

---

## 📝 Catatan Pengembangan

### Menambah Modul Baru dengan Audit Trail
1. Buat model → `use LogsActivity`
2. Implement `getLogModul()` return string nama modul
3. Implement `getLogData()` return `['new' => ..., 'old' => ...]`
4. Trait otomatis handle created/updated/deleted events

### Query Stok Real-time
```php
// Cek stok cukup sebelum transaksi
$barang = Barang::find($id);
if ($barang->stok < $jumlah) { throw ... }

// Update stok (atomic)
$barang->decrement('stok', $jumlah); // atau increment

// Mutasi otomatis via model methods
MutasiPersediaan::catatBarangKeluar($id, $jumlah, $stokBaru, $referensi, $tanggal);
MutasiPersediaan::catatBarangMasuk($id, $jumlah, $stokBaru, $referensi, $tanggal);
```

### Export PDF Landscape
```php
$pdf = Pdf::loadView('view.name', compact('data'));
$pdf->setPaper('A4', 'landscape');
return $pdf->download('filename.pdf');
```

### Export Excel dengan Maatwebsite
```php
class MyExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    // implement methods...
}
return Excel::download(new MyExport($data), 'file.xlsx');
```

---

## 📄 Lisensi

Proyek Tugas Akhir / Pendidikan. Bebas digunakan untuk pembelajaran.

---

**Dibuat dengan** ❤️ **menggunakan Laravel 12 + Tailwind CSS**