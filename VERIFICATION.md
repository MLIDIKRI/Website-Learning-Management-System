# ✅ INSTALLATION & VERIFICATION CHECKLIST

Gunakan checklist ini untuk memastikan semua files sudah ter-copy dengan benar.

---

## 📋 FILE VERIFICATION CHECKLIST

### ✅ Documentation Files (8 files)

```
☐ README.md
☐ QUICKSTART.md
☐ INSTALLATION.md
☐ CHECKLIST.md
☐ PROJECT_SUMMARY.md
☐ DEVELOPERS.md
☐ API_INTEGRATION.md
☐ INDEX.md (master guide)
☐ VERIFICATION.md (file ini)
```

### ✅ Root Level Files (3 files)

```
☐ index.php (Login page)
☐ logout.php (Logout handler)
☐ db_schema.sql (Database creation)
☐ demo_data.sql (Sample data)
```

### ✅ Includes Folder (3 files)

```
includes/
├─ ☐ config.php (Database config)
├─ ☐ header.php (Navigation template)
└─ ☐ footer.php (Footer template)
```

### ✅ Assets Folder (4 items)

```
assets/
├─ css/
│  └─ ☐ style.css (Custom styling)
├─ js/
│  └─ ☐ script.js (JavaScript utilities)
├─ ☐ img/ (Image folder)
└─ ☐ uploads/ (File uploads folder)
```

### ✅ Admin Module (7 files)

```
admin/
├─ ☐ dashboard.php
├─ ☐ users.php
├─ ☐ edit_user.php
├─ ☐ classes.php
├─ ☐ reports.php
├─ ☐ logs.php
└─ ☐ settings.php
```

### ✅ Teacher Module (11 files)

```
teacher/
├─ ☐ dashboard.php
├─ ☐ classes.php
├─ ☐ class_detail.php
├─ ☐ assignments.php
├─ ☐ view_submissions.php
├─ ☐ announcements.php
├─ ☐ discussions.php
├─ ☐ attendance.php
├─ ☐ take_attendance.php
├─ ☐ grades.php
└─ ☐ profile.php
```

### ✅ Student Module (11 files)

```
student/
├─ ☐ dashboard.php
├─ ☐ classes.php
├─ ☐ view_class.php
├─ ☐ assignments.php
├─ ☐ view_assignment.php
├─ ☐ submissions.php
├─ ☐ view_submission.php
├─ ☐ grades.php
├─ ☐ materials.php
├─ ☐ announcements.php
└─ ☐ profile.php
```

**Total Files**: 8 docs + 4 root + 3 includes + 4 assets + 7 admin + 11 teacher + 11 student = **48+ files**

---

## 📂 FOLDER STRUCTURE VERIFICATION

Pastikan folder berikut ada:

```
lms/
├─ ☐ admin/
├─ ☐ teacher/
├─ ☐ student/
├─ ☐ includes/
├─ ☐ assets/
│  ├─ ☐ css/
│  ├─ ☐ js/
│  ├─ ☐ img/
│  └─ ☐ uploads/
└─ ☐ Root level files
```

---

## 🗄️ DATABASE SETUP CHECKLIST

### Step 1: Persiapan

```
☐ MySQL Server sudah installed
☐ MySQL Server sudah running
☐ PHP 7.4+ sudah terinstall
☐ Web server (Apache/Nginx) sudah running
☐ phpMyAdmin atau MySQL client tersedia
☐ db_schema.sql file sudah ada
☐ demo_data.sql file sudah ada
```

### Step 2: Database Creation

```
☐ Database "lms_database" sudah dibuat
☐ Database collation: utf8mb4_unicode_ci
☐ db_schema.sql sudah dirun
☐ Error saat import? Check INSTALLATION.md
```

### Step 3: Demo Data

```
☐ demo_data.sql sudah dirun
☐ Sample users sudah ada (admin, teacher1, student1, dst)
☐ Sample classes sudah ada (3 classes)
☐ Sample assignments sudah ada
☐ Sample submissions sudah ada
```

### Verifikasi Database

```sql
-- Run these queries to verify

-- Check database exists
SHOW DATABASES LIKE 'lms_database';

-- Check tables created (should be 14)
USE lms_database;
SHOW TABLES;

-- Check users created (should have at least 7)
SELECT COUNT(*) FROM users;

-- Check demo admin user
SELECT * FROM users WHERE username='admin' AND role=0;

-- Check classes created (should have 3)
SELECT COUNT(*) FROM classes;

-- All should return data without errors
```

---

## ⚙️ CONFIGURATION CHECKLIST

### includes/config.php

```
☐ Database server: localhost
☐ Database name: lms_database
☐ Database user: root (or your username)
☐ Database password: correct password
☐ Connection successful (test by opening any page)
```

### Optional Configuration (edit if needed)

```
☐ SITE_NAME = 'LMS'
☐ Session timeout = 3600 seconds
☐ Upload directory = assets/uploads/
☐ Max file size = 5MB (adjust if needed)
```

---

## 🐛 COMMON SETUP ISSUES & VERIFICATION

### Issue: Database Connection Error

**Checklist:**

```
☐ MySQL is running?
  $ mysql -u root -p

☐ Database created?
  mysql> SHOW DATABASES LIKE 'lms_database';

☐ Credentials in config.php correct?
  Check: $servername, $username, $password

☐ UTF-8 charset set?
  Check: mysqli->set_charset('utf8')
```

**Fix**: Edit includes/config.php dan ubah credentials.

---

### Issue: Tables Not Created

**Checklist:**

```
☐ db_schema.sql file exists?

☐ File sudah diimport?
  via phpMyAdmin atau MySQL command

☐ No errors saat import?
  Check import log

☐ Can query tables?
  mysql> USE lms_database; SHOW TABLES;
```

**Fix**: Re-run db_schema.sql, check for duplicate table names.

---

### Issue: Demo Users Not Found

**Checklist:**

```
☐ demo_data.sql file exists?

☐ demo_data.sql sudah dirun setelah db_schema.sql?
  (Order penting!)

☐ MySQL errors saat import?
  Check error messages

☐ Can query users?
  mysql> SELECT * FROM users;
```

**Fix**: Re-run demo_data.sql, ensure db_schema.sql ran first.

---

### Issue: Page Shows Error

**Checklist:**

```
☐ PHP 7.4+ installed?
  $ php -v

☐ Web server running?
  Apache/Nginx accessible

☐ File permissions correct?
  chmod 755 for files, 777 for uploads

☐ includes/config.php accessible?
  Check file path

☐ Error reporting enabled?
  Check php.ini for error_reporting
```

**Fix**: Check PHP error log atau enable display_errors.

---

### Issue: Can't Login

**Checklist:**

```
☐ Database connection works?
  Test other pages first

☐ Demo users created?
  SELECT * FROM users;

☐ User permissions in DB correct?
  Check role field (0=admin, 1=teacher, 2=student)

☐ Session starting properly?
  Check for session_start() in index.php

☐ Password correct?
  Default: admin/admin123
```

**Fix**: Check browser console, verify credentials in database.

---

## 🔒 SECURITY CHECKLIST

### Before Going Live

```
☐ Changed all demo passwords
  ☐ admin password changed
  ☐ teacher1 password changed
  ☐ student accounts password changed

☐ includes/config.php not accessible via browser

☐ uploads/ folder has write permission only (755)

☐ Database backup sudah dibuat

☐ Error reporting disabled in production
  (error_reporting = 0)

☐ HTTPS enabled (SSL certificate)

☐ Database user has limited privileges (not root)

☐ Regular security updates planned
```

---

## 📊 FUNCTIONALITY VERIFICATION

### Feature Testing Checklist

#### Authentication

```
☐ Can login dengan admin account
☐ Can login dengan teacher account
☐ Can login dengan student account
☐ Logout works properly
☐ Invalid credentials rejected
☐ Session timeout works
```

#### Admin Module

```
☐ Admin dashboard shows statistics
☐ Can view all users
☐ Can create new user
☐ Can edit user
☐ Can delete user
☐ Can view all classes
☐ Can generate reports
```

#### Teacher Module

```
☐ Teacher dashboard shows their classes
☐ Can create new class
☐ Can add students to class
☐ Can remove students from class
☐ Can create assignment
☐ Can view student submissions
☐ Can grade submissions
☐ Can post announcement
☐ Can view discussions
☐ Can record attendance
☐ Can view grades
```

#### Student Module

```
☐ Student dashboard shows enrolled classes
☐ Can view class details
☐ Can see all assignments
☐ Can submit assignment
☐ Can view submission history
☐ Can view grades
☐ Can download materials
☐ Can read announcements
```

#### Database

```
☐ All 14 tables created
☐ Can insert records
☐ Can update records
☐ Can delete records
☐ Foreign keys working
☐ Data integrity maintained
```

#### UI/UX

```
☐ Navigation bar visible on all pages
☐ Sidebar working properly
☐ Forms submit without errors
☐ Modal dialogs working
☐ Responsive design (test on mobile)
☐ CSS styling loaded
☐ Icons displaying
☐ Alert messages showing
```

---

## 🚀 DEPLOYMENT VERIFICATION

### Pre-Deployment

```
☐ All files copied to server
☐ Folder permissions set correctly
☐ Database configured for production
☐ Error reporting disabled
☐ Debug mode off
☐ HTTPS configured
☐ Backup strategy in place
```

### Post-Deployment

```
☐ Site accessible at production URL
☐ Database connected properly
☐ Login working
☐ All features tested
☐ Backups scheduled
☐ Monitoring configured
☐ Logging enabled
```

---

## 📱 BROWSER COMPATIBILITY CHECK

Test sama dengan berbagai browser:

```
☐ Chrome/Chromium (latest)
☐ Firefox (latest)
☐ Safari (latest)
☐ Edge (latest)
☐ Mobile Safari (iOS)
☐ Chrome Mobile (Android)
```

**Expected Result**: Semua browser bisa akses tanpa permission issues.

---

## 🧪 PERFORMANCE VERIFICATION

```
☐ Page load time < 2 seconds
☐ Database queries < 500ms
☐ No console errors
☐ No warning messages
☐ Memory usage normal
☐ CPU usage normal
```

---

## 📝 DOCUMENTATION VERIFICATION

```
☐ README.md exists & readable
☐ QUICKSTART.md provides clear setup
☐ INSTALLATION.md complete
☐ DEVELOPERS.md has code examples
☐ API_INTEGRATION.md documented
☐ PROJECT_SUMMARY.md accurate
☐ INDEX.md navigable
☐ CHECKLIST.md helpful
```

---

## ✅ FINAL VERIFICATION PROCESS

Jalankan prosedur ini sebelum declare "siap pakai":

### 1. Complete Installation

```bash
# 1. Extract files
# 2. Create database
# 3. Import schema
# 4. Import demo data
# 5. Configure config.php
```

### 2. Verify Files

```bash
# Make sure all folders exist
ls -la /path/to/lms/
ls -la /path/to/lms/admin/
ls -la /path/to/lms/teacher/
ls -la /path/to/lms/student/
```

### 3. Test Database

```bash
# Login to MySQL
mysql -u root -p lms_database

# Check tables (should show 14)
SHOW TABLES;

# Check users (should show 7+)
SELECT * FROM users LIMIT 5;

# Exit
exit
```

### 4. Test Application

```
1. Open browser: http://localhost/lms
2. Should see login page
3. Login dengan admin/admin123
4. Should see admin dashboard
5. Navigate to other modules
6. Test creating/editing/deleting data
```

### 5. Verify Documentation

```
1. Open README.md in browser or editor
2. Read through main sections
3. Check for any missing info
4. Verify examples match actual code
```

### 6. Final Check

```bash
# All folders exist
# All PHP files accessible
# Database connected
# Demo data present
# No error messages
# Features working
# UI rendering correctly
```

---

## 🎯 NEXT STEPS AFTER VERIFICATION

### ✅ Everything Working?

1. ☐ Congratulations! System is ready to use.
2. ☐ Create real user accounts (admin, teachers, students)
3. ☐ Change all demo passwords
4. ☐ Start using for actual teaching/learning
5. ☐ Plan for backups & maintenance

### ⚠️ Something Not Working?

1. ☐ Check the Troubleshooting section above
2. ☐ Read relevant documentation file
3. ☐ Check error logs
4. ☐ Verify file permissions
5. ☐ Ensure dependencies installed
6. ☐ Ask for help (see INSTALLATION.md)

---

## 📞 ISSUE RESOLUTION MATRIX

| Issue                  | Check       | Solution              |
| ---------------------- | ----------- | --------------------- |
| Can't find LMS folder  | File copy   | Copy all files again  |
| MySQL won't connect    | config.php  | Update credentials    |
| Tables don't exist     | Database    | Re-run db_schema.sql  |
| Can't login            | Users       | Re-run demo_data.sql  |
| Page shows error       | Permissions | chmod 755 for files   |
| Can't upload files     | Folder      | chmod 777 uploads/    |
| CSS not loading        | File path   | Check assets/ folder  |
| JavaScript not working | Console     | Check browser console |
| Slow performance       | Database    | Add indexes           |
| Session lost           | PHP         | Check session timeout |

---

## 📋 SIGN-OFF CHECKLIST

Ketika semua item tercentang, sistem siap digunakan:

```
Production Status: ☐ READY FOR USE

✅ All files copied
✅ Database created
✅ Demo data loaded
✅ Configuration complete
✅ All features tested
✅ Security verified
✅ Documentation reviewed
✅ Performance acceptable
✅ Backup plan ready
✅ Training completed

Date Verified: ____________
Verified By: ______________
```

---

## 🎉 CONGRATULATIONS!

Jika semua checklist tercentang, LMS Anda siap digunakan!

**Sistem sudah:**

- ✅ Complete dengan 50+ features
- ✅ Secure dan validated
- ✅ Documented & supported
- ✅ Ready for production
- ✅ Fully operational

**Next: Baca [README.md](README.md) untuk user guide!**

---

**Version**: 1.0
**Last Updated**: 2026
**Status**: Complete & Ready
