# 🚀 QUICK START GUIDE

Salin perintah di bawah ini untuk setup cepat LMS.

## 1️⃣ SETUP DATABASE (5 menit)

### Via Command Line:

```bash
# Buat database
mysql -u root -p -e "CREATE DATABASE lms_database;"

# Import schema
mysql -u root -p lms_database < db_schema.sql

# Import demo data (optional)
mysql -u root -p lms_database < demo_data.sql
```

### Atau ambil 2 langkah via phpMyAdmin:

1. Create database: `lms_database`
2. Import file: `db_schema.sql` dan `demo_data.sql`

---

## 2️⃣ SETUP APLIKASI (2 menit)

1. Copy folder `lms` ke `htdocs` (XAMPP) atau `www` (WAMP)
2. Edit `includes/config.php` - sesuaikan DB credentials

---

## 3️⃣ RUN (1 menit)

Buka browser: **`http://localhost/lms`**

Login dengan:

- **Admin**: admin / admin123
- **Guru**: teacher1 / teacher123
- **Siswa**: student1 / student123

---

## ✅ DONE!

Sekarang Anda dapat:

### 👨‍💼 Sebagai Admin

- Kelola user, kelas, lihat laporan

### 👨‍🏫 Sebagai Guru

- Buat kelas
- Post tugas & pengumuman
- Nilai pekerjaan siswa
- Catat kehadiran

### 👨‍🎓 Sebagai Siswa

- Lihat kelas & materi
- Kerjakan & kumpulkan tugas
- Lihat nilai & feedback
- Baca pengumuman

---

## 📚 DOKUMENTASI LENGKAP

- **README.md** - Feature overview & user guide
- **INSTALLATION.md** - Panduan install step-by-step
- **CHECKLIST.md** - Daftar fitur yang diimplementasikan
- **db_schema.sql** - Database structure
- **demo_data.sql** - Sample data

---

## 🔧 TROUBLESHOOT

**Database Connection Error?**

```bash
# Pastikan MySQL running:
mysql -u root -p
# Jika berhasil, ketik: exit
```

**Page Not Found?**

- Check URL: `http://localhost/lms` (bukan `/lms/index.php`)
- Pastikan folder `lms` di htdocs/www

**Permission Error?**

```bash
chmod 777 /path/to/lms/assets/uploads
chmod 777 /path/to/lms/assets/img
```

---

## 🎯 NEXT STEPS

1. **Change Default Passwords**
   - Login as admin, ubah password

2. **Create Teachers**
   - Admin → Users → Add User → Role: Teacher

3. **Teachers Create Classes**
   - Login sebagai teacher
   - Klik "Buat Kelas Baru"

4. **Add Students to Class**
   - Teacher → Kelas → Tambah Siswa

5. **Start Teaching!**
   - Post pengumuman, buat tugas, upload materi

---

**Happy Learning! 📚✨**

Pertanyaan? Cek **README.md** atau **INSTALLATION.md**
