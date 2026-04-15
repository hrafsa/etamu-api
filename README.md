# E-TAMU – Admin Web & Public API

Aplikasi E-TAMU menyediakan:
- Dashboard Web (khusus Admin) untuk mengelola kategori, sub kategori, pengguna, dan pengajuan.
- RESTful JSON API (khusus User) untuk registrasi, login, membuat dan melihat pengajuan.

Admin TIDAK bisa register via API. User TIDAK bisa login ke halaman web admin. Semua akses API tanpa token akan selalu mengembalikan JSON (bukan HTML / halaman login).

## Teknologi Utama
- Laravel 11 + PHP 8.2+
- Laravel Sanctum (Token based auth)
- SQLite (default, bisa ganti MySQL/PostgreSQL)
- Vite + Tailwind (untuk UI admin)

## Fitur Inti
- Autentikasi token (Bearer) untuk API user
- Throttling & brute force mitigation (login rate limit + random delay)
- Role & Status User (active / inactive)
- CRUD Pengajuan (User hanya melihat miliknya sendiri)
- Upload dokumen (wajib, pdf/jpg/jpeg/png, max 2MB)
- Kategori & Sub Kategori untuk referensi pengajuan
- Pagination dengan pilihan per_page (5,10,25,50)
- Konsisten response JSON: `status`, `message`, `data` (+meta pagination)
- Validasi relasi kategori ↔ sub kategori
- Token rotation (token lama dihapus saat login ulang)

## Persyaratan
| Komponen | Versi Minimum |
|----------|---------------|
| PHP      | 8.2           |
| Composer | 2.x           |
| Node     | 18+ (untuk asset build) |

## Instalasi (Development)
```cmd
:: Clone repository
git clone <URL-REPO> etamu
cd etamu

:: Install dependency PHP
composer install

:: Install dependency front-end (opsional untuk jalankan UI admin dev)
npm install

:: Salin file environment
copy .env.example .env

:: Generate application key
php artisan key:generate

:: (Opsional) Set SANCTUM_STATEFUL_DOMAINS bila perlu
:: SANCTUM_STATEFUL_DOMAINS=etamu.test

:: Migrasi & seeding (buat struktur database & data awal jika disediakan)
php artisan migrate --seed

:: Jalankan server dev
php artisan serve

:: (Opsional) Jalankan vite untuk asset
npm run dev
```

SQLite default: file `database/database.sqlite` sudah ada. Pastikan file writable.

## Konfigurasi Penting
- `.env` contoh minimal:
```
APP_NAME=E-TAMU
APP_ENV=local
APP_KEY=base64:...
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite

SANCTUM_STATEFUL_DOMAINS=
SESSION_DOMAIN=
```
Biarkan `SANCTUM_STATEFUL_DOMAINS` kosong untuk memaksa API selalu stateless (menghindari cookie login web mempengaruhi hasil API).

## Role & Akses
| Role  | Media Akses | Bisa Register | Akses Web Admin | Akses API | Catatan |
|-------|-------------|---------------|-----------------|----------|--------|
| admin | Web         | Tidak         | Ya              | (Diblok saat login API) | Admin dikelola manual / seeding |
| user  | API / Mobile| Ya (POST /api/register)| Tidak    | Ya       | Hanya melihat pengajuan miliknya |

## Format Response Standar
```json
{
  "status": true,
  "message": "Deskripsi singkat",
  "data": { 
      ... 
  }
}
```
Error umum:
```json
{
  "status": false,
  "message": "Validation failed",
  "errors": { 
      "field": [
          "Pesan error..."
      ] 
  }
}

## Autentikasi
Gunakan Bearer Token dari header `Authorization`:
```
Authorization: Bearer <token>
```
Token didapat setelah sukses register atau login.

## Rate Limit
- Login: 5 percobaan / 60 detik per kombinasi (email+IP) & IP.
- Jika limit tercapai: HTTP 429 + waktu tunggu detik tersisa.

## Endpoint API
Semua URL diawali `/api`.

### 1. Register User
POST `/api/register`
Body (JSON):
```json
{
  "name": "User Satu",
  "email": "user1@example.com",
  "phone": "08123456789",
  "password": "password123",
  "password_confirmation": "password123"
}
```
Respon 201 (contoh ringkas):
```json
{
  "status": true,
  "message": "User registered successfully",
  "data": {
    "user": {
        "id":1,
        "name":"User Satu", 
        "email":"user1@example.com", 
        "phone":"08123456789", 
        "role":"user", 
        "status":true
    },
    "token": "<bearer-token>"
  }
}
```

### 2. Login User
POST `/api/login`
```json
{ 
    "email":"user1@example.com", 
    "password":"password123"
}
```
Respon 200 -> token baru (token lama dihapus).

### 3. Logout
POST `/api/logout` (Butuh token)
Respon 200: token dihapus.

### 4. Profile Saat Ini
GET `/api/profile` (Butuh token)

### 5. Daftar Kategori (dengan sub kategori)
GET `/api/categories`
Parameter opsional:
- `include_sub=false` untuk tidak memuat sub kategori.

Respon contoh:
```json
{
  "status": true,
  "message": "Daftar kategori",
  "data": [
    { 
        "id":1, 
        "name":"AKD", 
        "sub_categories":[
            {
                "id":1,
                "nama":"Komisi A"
            }
        ] 
    }
  ]
}
```

### 6. Sub Kategori per Kategori
GET `/api/categories/{id}/sub-categories`

### 7. List Pengajuan User
GET `/api/pengajuan?per_page=10&page=1`
- `per_page` hanya: 5,10,25,50 (default 10)

Respon struktur pagination mengikuti Laravel paginator + data sudah berupa resource:
```json
{
  "status": true,
  "message": "Daftar pengajuan",
  "data": {
    "current_page": 1,
    "data": [ 
        { 
            "nomor_pengajuan": "PGJ-20251002-ABCD", 
            ...
        } 
    ],
    "first_page_url": "...",
    "last_page": 1,
    "per_page": 10,
    "total": 3,
    "links": [ 
        {
            "url": "...", 
            "label": "1", 
            "active": true 
        } 
    ]
  }
}
```

### 8. Detail Pengajuan
GET `/api/pengajuan/{nomor_pengajuan}`
Respon 404 jika tidak ditemukan / bukan milik user.

### 9. Buat Pengajuan (Upload Dokumen Wajib)
POST `/api/pengajuan`
Kirim sebagai `multipart/form-data` (JANGAN raw JSON). Field:
| Nama Field | Tipe | Keterangan |
|------------|------|-----------|
| nama_instansi | text | required |
| atas_nama | text | required |
| jumlah_peserta | integer|min 1 |
| phone | text | required |
| email | email | required |
| kategori | integer | id kategori valid |
| sub_kategori | integer | id sub kategori valid (harus sesuai kategori) |
| tanggal_kunjungan | date (YYYY-MM-DD) | >= hari ini |
| waktu_kunjungan | time (HH:MM) | 24h format |
| tujuan | text | required |
| dokumen | file | pdf/jpg/jpeg/png, max 2048 KB |

Contoh curl (Linux/Mac sintaks; di Windows Git Bash serupa):
```bash
curl -X POST http://localhost:8000/api/pengajuan \
  -H "Authorization: Bearer <token>" \
  -H "Accept: application/json" \
  -F nama_instansi="PT Maju" \
  -F atas_nama="Budi" \
  -F jumlah_peserta=20 \
  -F phone="081234567890" \
  -F email="budi@example.com" \
  -F kategori=1 \
  -F sub_kategori=1 \
  -F tanggal_kunjungan=2025-10-10 \
  -F waktu_kunjungan=09:30 \
  -F tujuan="Kunjungan kerja" \
  -F dokumen=@/path/file.pdf
```

## Validasi & Pesan Error Penting
| Field | Rule Utama | Catatan |
|-------|-----------|---------|
| password (register) | min:8 + confirmed | `password_confirmation` wajib |
| dokumen | mimes + max:2048 | wajib, tidak nullable |
| sub_kategori | exists + relasi kategori | Dicek manual pada `passedValidation()` |
| tanggal_kunjungan | after_or_equal:today | Tidak boleh tanggal lampau |

Contoh 422:
```json
{
  "status": false,
  "message": "Validation failed",
  "errors": { "email": ["The email field must be a valid email address."] }
}
```

## Kode Status HTTP yang Digunakan
| Kode | Situasi |
|------|---------|
| 200 | OK / Sukses umum |
| 201 | Resource berhasil dibuat (register, pengajuan baru) |
| 401 | Tidak diautentikasi (token hilang/invalid) |
| 403 | Terautentikasi tapi tidak berhak (akun inactive / role salah) |
| 404 | Tidak ditemukan / bukan milik user |
| 422 | Validasi input gagal |
| 429 | Terlalu banyak percobaan (throttle) |

## Upload Dokumen di Postman
1. Pilih method POST `/api/pengajuan`.
2. Tab Body -> pilih form-data.
3. Tambah semua field text sesuai tabel.
4. Field `dokumen`: ubah tipe ke File lalu pilih file lokal.
5. Header `Authorization: Bearer <token>` otomatis (bisa set di tab Auth Bearer Token atau manual).
6. Pastikan tidak mengirim raw JSON.

## Pagination Penjelasan
Field pada objek pagination (`data` di dalam response pengajuan):
- `current_page`, `last_page`, `per_page`, `total`: info umum.
- `data`: array pengajuan (sudah ditransformasi).
- `links`: daftar navigasi (tiap item: url, label, active).

Ubah jumlah item per halaman dengan `?per_page=25`. Jika nilai tidak valid -> fallback 10.

## Keamanan & Praktik Baik
- Token lama dihapus saat login ulang untuk mencegah penyalahgunaan.
- Rate limiting & random sleep mengurangi brute force.
- Jangan share token di log publik.
- Set `SANCTUM_STATEFUL_DOMAINS` kosong pada environment API-only agar cookie sesi tidak ikut.
- Pastikan selalu kirim header `Accept: application/json` (middleware sudah memaksa, tapi eksplisit aman).

## Contoh curl Ringkas
```bash
# Register
curl -X POST http://localhost:8000/api/register -H "Accept: application/json" -d '{"name":"U","email":"u@ex.com","phone":"081","password":"password123","password_confirmation":"password123"}' -H "Content-Type: application/json"

# Login
curl -X POST http://localhost:8000/api/login -H "Accept: application/json" -d '{"email":"u@ex.com","password":"password123"}' -H "Content-Type: application/json"

# List pengajuan (pakai token)
curl -H "Authorization: Bearer <token>" -H "Accept: application/json" http://localhost:8000/api/pengajuan
```

## Struktur Direktori (Singkat)
| Path | Deskripsi |
|------|-----------|
| `app/Http/Controllers/Api` | Controller API |
| `app/Http/Resources` | Resource transformer JSON |
| `app/Http/Requests/Api` | Validasi FormRequest API |
| `app/Support` | Helper (ApiResponse, ActivityLogger, dll) |
| `database/migrations` | Migrasi database |
| `routes/api.php` | Definisi route API |

## Penamaan Field
| Konteks | Nama | Catatan |
|---------|------|---------|
| User | phone | Menggantikan nomor_whatsapp |
| Pengajuan | nomor_pengajuan | Generated (format PGJ-YYYYMMDD-XXXX) |
| Relasi | kategori, sub_kategori | Input request; response nested objek |

## Roadmap (Opsional / Ide Lanjutan)
- Endpoint pencarian & filtering pengajuan
- Activity log API endpoint
- Status update pengajuan oleh admin via API
- Notifikasi email / webhook
- Internationalization (i18n)

## Troubleshooting
| Masalah | Penyebab Umum | Solusi |
|---------|---------------|--------|
| Akses API menampilkan HTML login | Cookie sesi ikut terkirim | Hapus cookie di Postman / kosongkan SANCTUM_STATEFUL_DOMAINS |
| 401 padahal sudah login | Token salah / expired (dihapus saat login ulang) | Login ulang & pakai token baru |
| Upload gagal (422 dokumen) | Salah tipe / >2MB | Pastikan ekstensi & ukuran sesuai |
| 429 login | Terlalu banyak percobaan | Tunggu sesuai detik di pesan |

## Lisensi
Proyek ini berbasis Laravel (MIT). Silakan modifikasi sesuai kebutuhan internal.
