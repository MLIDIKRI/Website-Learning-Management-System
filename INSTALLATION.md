# 📚 PANDUAN INSTALASI LMS

## Panduan Lengkap Setup Learning Management System

### PRASYARAT SISTEM

**Minimum Requirements:**

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau MariaDB 10.2+
- Apache atau Nginx server
- Minimal 100MB disk space
- Browser modern (Chrome, Firefox, Safari, Edge)

**Recommended:**

- PHP 8.0+
- MySQL 8.0+
- SSD storage
- 256MB+ RAM

---

## STEP 1: Install Server & Database

### Untuk XAMPP (Windows/Mac/Linux):

1. Download XAMPP dari https://www.apachefriends.org/
2. Install XAMPP
3. Buka XAMPP Control Panel
4. Start MySQL service
5. Buka phpMyAdmin di `http://localhost/phpmyadmin`

### Untuk WAMP (Windows):

1. Download WAMP dari http://www.wampserver.com/
2. Install WAMP
3. Klik icon WAMP di system tray, pilih "Start All Services"

### Untuk Linux (Ubuntu/Debian):

```bash
sudo apt-get update
sudo apt-get install apache2 mysql-server php php-mysql
sudo systemctl start apache2
sudo systemctl start mysql
```

---

## STEP 2: Buat Database

### Menggunakan phpMyAdmin:

1. Buka `http://localhost/phpmyadmin`
2. Login dengan user `root` (password kosong jika baru)
3. Klik menu "Databases"
4. Nama database: `lms_database`
5. Collation: `utf8mb4_unicode_ci`
6. Klik "Create"

### Menggunakan Command Line:

```bash
mysql -u root -p
CREATE DATABASE lms_database;
CREATE USER 'lms_user'@'localhost' IDENTIFIED BY 'lms_password';
GRANT ALL PRIVILEGES ON lms_database.* TO 'lms_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## STEP 3: Import Database Schema

### Metode 1: Menggunakan phpMyAdmin

1. Buka `http://localhost/phpmyadmin`
2. Pilih database `lms_database`
3. Klik tab "Import"
4. Pilih file `db_schema.sql`
5. Klik "Go"

### Metode 2: Menggunakan Command Line

```bash
mysql -u root -p lms_database < db_schema.sql
```

### Metode 3: Menggunakan MySQL Workbench

1. Buka MySQL Workbench
2. Buat koneksi atau buka koneksi existing
3. Klik menu "File" > "Open SQL Script"
4. Pilih file `db_schema.sql`
5. Klik "Execute"

---

## STEP 4: Setup File Aplikasi

### Step 4.1: Copy File ke Web Root

**Untuk XAMPP:**

```
Copy folder 'lms' ke:
C:\xampp\htdocs\lms  (Windows)
/Applications/XAMPP/htdocs/lms  (Mac)
/opt/lampp/htdocs/lms  (Linux)
```

**Untuk WAMP:**

```
Copy folder 'lms' ke:
C:\wamp64\www\lms  (atau C:\wamp\www jika WAMP 32-bit)
```

**Untuk Apache di Linux:**

```bash
sudo cp -r lms /var/www/html/
sudo chown -R www-data:www-data /var/www/html/lms
sudo chmod -R 755 /var/www/html/lms
```

### Step 4.2: Beri Permission untuk Upload Folder

```bash
# Windows/PowerShell (Run as Administrator):
# Skip, permission sudah otomatis

# Linux/Mac:
chmod 777 /path/to/lms/assets/uploads
chmod 777 /path/to/lms/assets/img
```

---

## STEP 5: Konfigurasi Database

Edit file: `includes/config.php`

```php
<?php
define('DB_HOST', 'localhost');      // Sesuaikan jika berbeda
define('DB_USER', 'root');           // Sesuaikan username
define('DB_PASS', '');               // Masukkan password (jika ada)
define('DB_NAME', 'lms_database');   // Nama database yang dibuat
?>
```

**Contoh konfigurasi alternatif:**

Jika menggunakan user khusus:

```php
define('DB_USER', 'lms_user');
define('DB_PASS', 'lms_password');
```

---

## STEP 6: Import Data Demo (Optional)

### Via phpMyAdmin:

1. Buka `http://localhost/phpmyadmin`
2. Pilih database `lms_database`
3. Klik tab "Import"
4. Pilih file `demo_data.sql`
5. Klik "Go"

### Via Command Line:

```bash
mysql -u root -p lms_database < demo_data.sql
```

---

## STEP 7: Akses Aplikasi

1. Buka browser
2. Ketik URL: `http://localhost/lms`
3. Atau: `http://localhost:8080/lms` (jika port berbeda)

**Halaman yang akan ditampilkan:**

- Halaman login dengan form username & password
- Demo account tersedia (jika sudah import demo data)

---

## AKUN DEMO

Setelah import `demo_data.sql`, gunakan akun berikut:

### Admin Account

```
Username: admin
Password: admin123
URL: http://localhost/lms/admin/dashboard.php
```

### Teacher Account

```
Username: teacher1
Password: teacher123
URL: http://localhost/lms/teacher/dashboard.php

Username: teacher2
Password: teacher123
```

### Student Account

```
Username: student1
Password: student123
URL: http://localhost/lms/student/dashboard.php

Username: student2, student3, student4
Password: teacher123 (semua sama)
```

---

## TROUBLESHOOTING

### 1. "Cannot Connect to Database"

**Solusi:**

- Pastikan MySQL service sudah running
- Cek username dan password di `config.php`
- Cek apakah database `lms_database` sudah dibuat
- Restart MySQL service

```bash
# Windows
net stop MySQL80
net start MySQL80

# Linux
sudo systemctl restart mysql
```

### 2. "Database Connection Failed"

**Kemungkinan penyebab:**

- Port MySQL tidak standar (default 3306)
- Password atau username salah
- Database belum dibuat

**Solusi:**
Edit `config.php`:

```php
// Jika port berbeda (contoh: 3307)
$conn = new mysqli('localhost:3307', 'root', '', 'lms_database');
```

### 3. "404 Not Found"

**Solusi:**

- Pastikan folder `lms` di `htdocs` atau `www`
- Cek URL: `http://localhost/lms` bukan `http://localhost`
- Pastikan Apache/Nginx sudah running

### 4. "Permission Denied" untuk Upload

**Solusi:**

```bash
# Linux/Mac
chmod 777 assets/uploads
chmod 777 assets/img

# Windows:
# Klik kanan folder > Properties > Security > Edit > Full Control
```

### 5. "Blank Page" atau "Fatal Error"

**Solusi:**

- Cek PHP version: `php -v`
- Cek error log di console browser (F12 > Console)
- Enable error display di `php.ini`:
  ```ini
  display_errors = On
  error_reporting = E_ALL
  ```
- Restart web server

### 6. Login Terus Redirect

**Solusi:**

- Clear browser cache: Ctrl+Shift+Delete
- Clear cookies
- Pastikan session folder writable
- Cek apakah demo data sudah diimport

### 7. File Upload Gagal

**Solusi:**

- Cek folder `assets/uploads` permission (chmod 777)
- Cek `upload_max_filesize` di `php.ini`
- Cek `post_max_size` di `php.ini`

```ini
upload_max_filesize = 50M
post_max_size = 50M
max_input_time = 300
max_execution_time = 300
```

---

## KONFIGURASI LANJUTAN

### Mengubah Session Timeout

Edit `includes/config.php`:

```php
define('SESSION_TIMEOUT', 1800); // 30 menit (dalam detik)
```

### Mengubah Ukuran File Upload

Edit `includes/config.php`:

```php
define('MAX_FILE_SIZE', 100 * 1024 * 1024); // 100MB
```

### Mengubah Warna Tema

Edit `assets/css/style.css`:

```css
/* Ubah dari ungu #667eea ke warna lain */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### Setup Email Notification (Future Feature)

```php
// Di config.php tambahkan:
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_USER', 'your-email@gmail.com');
define('MAIL_PASS', 'your-app-password');
define('MAIL_FROM', 'lms@yourschool.com');
```

---

## MAINTENANCE

### Backup Database

```bash
# Backup semua data
mysqldump -u root -p lms_database > backup_lms_2026.sql

# Restore dari backup
mysql -u root -p lms_database < backup_lms_2026.sql
```

### Regular Tasks

- **Weekly**: Check disk space
- **Monthly**: Backup database
- **Quarterly**: Update PHP/MySQL
- **As needed**: Clear old session files

### Clear Session Files

```bash
# Linux/Mac
rm /tmp/php_sessions/*

# Windows PowerShell (as Admin)
Remove-Item C:\xampp\tmp\session\*
```

---

## TESTING CHECKLIST

Setelah install, test fitur berikut:

**Admin:**

- [ ] Login sebagai admin
- [ ] Create user (teacher & student)
- [ ] Lihat dashboard stats
- [ ] Akses menu reports dan logs

**Teacher:**

- [ ] Login sebagai teacher
- [ ] Create class baru
- [ ] Add students ke class
- [ ] Create assignment
- [ ] Post announcement
- [ ] Grade student work
- [ ] Record attendance

**Student:**

- [ ] Login sebagai student
- [ ] View enrolled classes
- [ ] View assignments
- [ ] Submit assignment
- [ ] View grades
- [ ] View announcements

---

## NEXT STEPS

Setelah successful install:

1. **Setup Admin User**
   - Ganti password admin default
   - Update admin profile

2. **Create Teacher Accounts**
   - Buat akun guru melalui admin panel
   - Berikan credentials ke guru

3. **Create Classes**
   - Guru membuat kelas
   - Guru menambah siswa

4. **Start Teaching**
   - Upload materi
   - Create assignments
   - Post announcements
   - Grade student work

---

## SUPPORT & DOCUMENTATION

- **README.md** - Fitur overview dan user guide
- **CHECKLIST.md** - Daftar fitur yang sudah diimplementasikan
- **Database Schema** - Di `db_schema.sql`
- **Code Comments** - Di setiap file PHP

---

## SECURITY RECOMMENDATIONS

1. **Change default passwords**
   - Ganti password admin
   - Ganti password database (jika shared server)

2. **Enable HTTPS**
   - Gunakan SSL certificate
   - Redirect HTTP ke HTTPS

3. **Restrict Access**
   - Disable phpMyAdmin di production
   - Restrict admin folder dengan .htaccess

4. **Regular Updates**
   - Backup database regularly
   - Update PHP & MySQL
   - Monitor log files

---

**Selamat menggunakan LMS!** 📚✨

Untuk pertanyaan atau masalah, silahkan cek bagian TROUBLESHOOTING atau README.md
