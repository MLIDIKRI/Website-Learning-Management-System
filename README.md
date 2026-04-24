<<<<<<< HEAD
# 📚 Learning Management System (LMS)

Sistem Manajemen Pembelajaran Lengkap dengan fitur untuk Guru dan Siswa.

## 🎯 Fitur Utama

### Fitur Admin

- ✅ Kelola Pengguna (Guru & Siswa)
- ✅ Kelola Kelas
- ✅ Melihat Laporan
- ✅ Activity Logs
- ✅ Pengaturan Sistem

### Fitur Guru

- ✅ Membuat dan Mengelola Kelas
- ✅ Mengelola Siswa di Kelas
- ✅ Membuat Tugas dan Pengumuman
- ✅ Menilai Pekerjaan Siswa
- ✅ Mengelola Materi Pembelajaran
- ✅ Memimpin Diskusi Kelas
- ✅ Mencatat Kehadiran Siswa
- ✅ Melihat Nilai Siswa

### Fitur Siswa

- ✅ Melihat Kelas yang Diambil
- ✅ Melihat dan Mengerjakan Tugas
- ✅ Mengumpulkan Pekerjaan
- ✅ Melihat Nilai dan Feedback
- ✅ Membaca Pengumuman
- ✅ Melihat Materi Pembelajaran
- ✅ Berpartisipasi dalam Diskusi
- ✅ Melihat Kehadiran

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, Bootstrap 5
- **JavaScript**: Vanilla JS

## 📋 Requirement

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web Server (Apache, Nginx)
- Browser Modern

## ⚙️ Instalasi

### Step 1: Setup Database

1. Buka phpMyAdmin atau MySQL Command Line
2. Buat database baru:

```sql
CREATE DATABASE lms_database;
```

3. Import file `db_schema.sql`:

```bash
mysql -u root -p lms_database < db_schema.sql
```

Atau copy-paste isi file `db_schema.sql` ke phpMyAdmin

### Step 2: Konfigurasi Database

Edit file `includes/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Ganti dengan password MySQL Anda
define('DB_NAME', 'lms_database');
```

### Step 3: Setup Server

1. Copy folder `lms` ke direktori web root (htdocs untuk XAMPP, www untuk WAMP)
2. Buka browser dan akses: `http://localhost/lms`
3. Atau jika menggunakan port khusus: `http://localhost:8080/lms`

### Step 4: Import Demo Data

1. Buka phpMyAdmin
2. Pilih database `lms_database`
3. Import file `demo_data.sql` untuk menambahkan akun demo

## 👥 Akun Demo

Setelah import demo data:

- 123456/ admin@lms.com
- 123456/ teacher1@lms.com
- 123456/ student1@lms.com

## 📁 Struktur File

```
lms/
├── index.php                 # Halaman Login
├── logout.php               # Logout
├── db_schema.sql            # Schema Database
├── demo_data.sql            # Data Demo
├── includes/
│   ├── config.php           # Konfigurasi Database
│   ├── header.php           # Header Template
│   └── footer.php           # Footer Template
├── assets/
│   ├── css/
│   │   └── style.css        # CSS Custom
│   ├── js/
│   │   └── script.js        # JavaScript Custom
│   ├── img/                 # Folder Gambar
│   └── uploads/             # Folder File Upload
├── admin/                   # Module Admin
│   ├── dashboard.php
│   ├── users.php
│   ├── classes.php
│   ├── reports.php
│   ├── logs.php
│   └── settings.php
├── teacher/                 # Module Guru
│   ├── dashboard.php
│   ├── classes.php
│   ├── class_detail.php
│   ├── assignments.php
│   ├── announcements.php
│   ├── discussions.php
│   ├── attendance.php
│   ├── grades.php
│   └── profile.php
└── student/                 # Module Siswa
    ├── dashboard.php
    ├── classes.php
    ├── view_class.php
    ├── assignments.php
    ├── submissions.php
    ├── grades.php
    ├── materials.php
    ├── announcements.php
    └── profile.php
```

## 🔒 Keamanan

- Password di-hash dengan bcrypt
- Session management untuk autentikasi
- Input sanitization untuk mencegah SQL Injection
- CSRF protection (dapat ditambahkan)

## 📝 Penggunaan

### Untuk Admin

1. Login dengan akun admin
2. Kelola semua pengguna di menu "Kelola Pengguna"
3. Lihat statistik kelas dan aktivitas di dashboard
4. Generate laporan di menu "Laporan"

### Untuk Guru

1. Login dengan akun guru
2. Buat kelas baru di menu "Kelas"
3. Tambah siswa ke kelas
4. Buat tugas dan posting pengumuman
5. Nilai pekerjaan siswa
6. Kelola materi pembelajaran

### Untuk Siswa

1. Login dengan akun siswa
2. Lihat kelas yang tersedia
3. Kerjakan dan kumpulkan tugas
4. Lihat nilai dan feedback dari guru
5. Baca pengumuman dan materi

## 🚀 Fitur yang Dapat Dikembangkan

- [ ] Video conference untuk pembelajaran online
- [ ] Sistem notifikasi real-time
- [ ] Export data ke PDF/Excel
- [ ] Mobile app version
- [ ] Analytics dashboard yang lebih detail
- [ ] Integration dengan email server
- [ ] Two-factor authentication
- [ ] Rubric-based grading
- [ ] Student portfolio
- [ ] Parent access portal

## 🐛 Troubleshooting

### Database Connection Failed

- Pastikan MySQL server running
- Cek konfigurasi di `includes/config.php`
- Pastikan database sudah dibuat

### File Upload Error

- Cek permission folder `assets/uploads`
- Pastikan folder writable (chmod 777)
- Cek maksimal file size di php.ini

### Login Gagal

- Pastikan import demo data
- Clear browser cache/cookies
- Cek username dan password

## 📞 Support

Untuk pertanyaan atau laporan bug, silahkan hubungi developer.

## 📄 Lisensi

Open Source - Bebas digunakan dan dimodifikasi

---

**Dibuat dengan ❤️ untuk Pendidikan**
=======
>>>>>>> 64f3689a6a1a057dc598d2a8c8e071ca3f8d94e8
