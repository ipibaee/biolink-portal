# Modern Luxury Bio-Link & Systems Portal

Portal bio-link interaktif bernuansa abu-abu modern (Zinc/Slate) dengan fitur Dark/Light Mode, katalog showcase aplikasi (Modal interaktif), dan Secret Admin Panel untuk manajemen konten (CRUD Profile, Link, & Aplikasi).

---

## 🚀 Fitur Unggulan

1. **Desain Modern Gray (Zinc/Slate Aesthetic)**
   - **Desktop**: Tampilan terpusat (*centered app container*) yang elegan dengan efek bayangan ambient.
   - **Mobile**: Lebar penuh (*responsive edge-to-edge*), sangat nyaman diakses dari smartphone.
   - **Tema Terang & Gelap**: Dark Mode dominan warna abu-abu netral mewah (bukan hitam pekat pekat), lengkap dengan toggle Switcher (Matahari / Bulan).

2. **Katalog Aplikasi Interaktif (Showcase Modal)**
   - Kartu khusus *"Aplikasi & Sistem Saya"* di halaman depan.
   - Saat diklik, membuka pop-up Modal yang berisi daftar sistem/aplikasi yang kamu miliki, lengkap dengan kategori, badge status (*Live/Dev*), deskripsi, dan tombol buka langsung.
   - Dilengkapi fitur pencarian (*instant search bar*).

3. **Secret Admin Entrance (`/admin`)**
   - **Tanpa Tombol Publik**: Pengunjung halaman utama tidak akan melihat tautan/tombol ke halaman admin sama sekali.
   - **Proteksi Password**: Login aman berbasis session.
   - **CRUD Lengkap**:
     - Edit Profil (Nama, Gelar, Lokasi, Status, Foto Avatar, Bio).
     - Kelola Link Utama (Tambah, Edit, Hapus, Urutkan Link).
     - Kelola Katalog Aplikasi (Tambah, Edit, Hapus sistem web/aplikasi).
     - Ganti Password Admin.

4. **Zero-Configuration Database Engine**
   - Otomatis menggunakan **SQLite** (bawaan PHP 8.x). Jika server tidak memiliki ekstensi SQLite, sistem otomatis berpindah ke **JSON storage** tanpa error.
   - Sangat ringan, tanpa perlu membuat database MySQL manual di aaPanel!

---

## 🔑 Informasi Login Awal Admin
- **URL Akses**: `http://domain-kamu/admin` (atau `localhost/admin`)
- **Default Password**: `admin123`
- *(Disarankan langsung ganti password di tab Keamanan setelah login pertama kali)*

---

## 📂 Struktur File
```text
biolink-portal/
├── index.php             # Halaman utama publik (PHP dinamis)
├── preview.html          # File preview statis (bisa langsung dibuka di browser lokal)
├── db.php                # Database Engine (Auto SQLite / JSON fallback)
├── .gitignore            # Mengabaikan database lokal agar tidak konflik saat git pull
├── admin/
│   ├── index.php         # Dashboard admin (CRUD Profil, Links, Apps, Keamanan)
│   ├── login.php         # Form login admin
│   ├── logout.php        # Script keluar sesi
│   └── auth.php          # Session middleware proteksi
└── data/
    ├── .htaccess         # Proteksi keamanan database dari download langsung
    └── .gitkeep
```

---

## 🛠️ Langkah-Langkah Push ke GitHub

Buka terminal (PowerShell atau Git Bash) di folder projek ini:

```bash
cd "C:\Users\muama\.gemini\antigravity\scratch\biolink-portal"

# 1. Inisialisasi Git
git init

# 2. Tambahkan semua file
git add .

# 3. Commit pertama
git commit -m "feat: initial commit modern biolink portal"

# 4. Ganti branch utama ke main
git branch -M main

# 5. Hubungkan ke repository GitHub kamu (Ganti URL dengan repo GitHub kamu)
git remote add origin https://github.com/USERNAME_KAMU/NAMA_REPO_KAMU.git

# 6. Push ke GitHub
git push -u origin main
```

---

## 🌐 Cara Pasang & Auto-Deploy di aaPanel

### 1. Tambah Website di aaPanel
1. Buka dashboard aaPanel kamu.
2. Masuk ke menu **Website** > klik tombol **Add Site**.
3. Masukkan domain kamu (atau IP server / subdomain lokal).
4. Pilih versi PHP: **PHP 8.0 / 8.1 / 8.2 / 8.3**.
5. Klik **Submit**.

### 2. Tarik Kode dari GitHub ke Folder Web aaPanel
Buka **Terminal** di server aaPanel (bisa lewat menu *Terminal* di aaPanel):
```bash
# Masuk ke direktori webroot
cd /www/wwwroot/domain-kamu

# Hapus file default bawaan aaPanel (seperti 404.html, index.html)
rm -rf * .htaccess

# Clone repository kamu langsung ke folder ini
git clone https://github.com/USERNAME_KAMU/NAMA_REPO_KAMU.git .

# Berikan izin akses folder data agar PHP bisa menyimpan SQLite/JSON
chmod -R 775 data/
chown -R www:www /www/wwwroot/domain-kamu
```
Selesai! Sekarang buka domain kamu di browser, website langsung aktif!

---

### 3. Setting Auto-Deploy di aaPanel (Opsi Paling Simpel)
Agar setiap kali kamu `git push` dari komputer kamu, server aaPanel otomatis menarik perubahannya:

1. Di aaPanel, masuk ke menu **Cron**.
2. Buat Scheduled Task baru:
   - **Type of Task**: *Shell Script*
   - **Name of Task**: `Auto Deploy BioLink`
   - **Period**: Setiap **N Minutes** (misal: *2 Minutes* atau *5 Minutes*)
   - **Script Content**:
     ```bash
     cd /www/wwwroot/domain-kamu
     git pull origin main
     chown -R www:www /www/wwwroot/domain-kamu/data
     ```
3. Klik **Add Task**.

Setiap kali kamu selesai coding dan push ke GitHub, dalam waktu 2 menit server aaPanel lokal kamu akan langsung ter-update secara otomatis tanpa perlu sentuh server lagi!
