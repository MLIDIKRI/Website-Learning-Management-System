-- Demo Data untuk LMS

-- Insert Demo Users
INSERT INTO users (username, email, password, full_name, phone, role, status) VALUES
('admin', 'admin@lms.com', '$2y$10$OIVGcKl4lHFrL2QjTq6mEev7J2rXfJJa5BjHSPGJ.w2MTb9y8j5bm', 'Administrator', '082123456789', 'admin', 'active'),
('teacher1', 'teacher1@lms.com', '$2y$10$OIVGcKl4lHFrL2QjTq6mEev7J2rXfJJa5BjHSPGJ.w2MTb9y8j5bm', 'Budi Santoso', '082234567890', 'teacher', 'active'),
('teacher2', 'teacher2@lms.com', '$2y$10$OIVGcKl4lHFrL2QjTq6mEev7J2rXfJJa5BjHSPGJ.w2MTb9y8j5bm', 'Siti Nurhaliza', '082345678901', 'teacher', 'active'),
('student1', 'student1@lms.com', '$2y$10$OIVGcKl4lHFrL2QjTq6mEev7J2rXfJJa5BjHSPGJ.w2MTb9y8j5bm', 'Ahmad Riza', '081111111111', 'student', 'active'),
('student2', 'student2@lms.com', '$2y$10$OIVGcKl4lHFrL2QjTq6mEev7J2rXfJJa5BjHSPGJ.w2MTb9y8j5bm', 'Rini Wijaya', '081222222222', 'student', 'active'),
('student3', 'student3@lms.com', '$2y$10$OIVGcKl4lHFrL2QjTq6mEev7J2rXfJJa5BjHSPGJ.w2MTb9y8j5bm', 'Doni Pratama', '081333333333', 'student', 'active'),
('student4', 'student4@lms.com', '$2y$10$OIVGcKl4lHFrL2QjTq6mEev7J2rXfJJa5BjHSPGJ.w2MTb9y8j5bm', 'Eka Putri', '081444444444', 'student', 'active');

-- Password untuk semua demo user adalah "123456" (tanpa tanda kutip)

-- Insert Demo Classes
INSERT INTO classes (code, name, description, teacher_id, status) VALUES
('MTH101', 'Matematika Dasar', 'Kelas matematika untuk pemula yang membahas aljabar, geometri, dan trigonometri dasar.', 1, 'active'),
('ENG102', 'Bahasa Inggris Umum', 'Kelas grammar, speaking, listening, reading, dan writing untuk tingkat menengah.', 2, 'active'),
('SCI103', 'Sains Fisika', 'Pembelajaran tentang mekanika, energi, gelombang, dan optika.', 1, 'active');

-- Insert Class Members
INSERT INTO class_members (class_id, student_id) VALUES
(1, 4), (1, 5), (1, 6), (1, 7),
(2, 4), (2, 5), (2, 6),
(3, 5), (3, 6), (3, 7);

-- Insert Demo Announcements
INSERT INTO announcements (class_id, title, content, created_by, priority) VALUES
(1, 'Selamat Datang di Kelas Matematika', 'Halo semua! Selamat datang di kelas Matematika Dasar. Mari kita belajar bersama dan saling membantu.', 1, 'high'),
(1, 'Jadwal Kelas dan Waktu Konsultasi', 'Kelas diadakan setiap Senin dan Rabu pukul 10:00-12:00. Waktu konsultasi Jumat 14:00-16:00.', 1, 'normal'),
(2, 'Assignment Minggu Pertama', 'Silahkan selesaikan latihan writing di modul pembelajaran. Deadline Jumat minggu ini.', 2, 'normal'),
(3, 'Persiapan Quiz Fisika', 'Quiz akan diadakan minggu depan. Pelajari materi tentang gaya dan gerakan.', 1, 'high');

-- Insert Demo Materials
INSERT INTO materials (class_id, title, description, created_by) VALUES
(1, 'Buku Ajar Matematika Dasar', 'Materi lengkap tentang aljabar linear dan operasi dasar.', 1),
(1, 'Slide Presentasi Trigonometri', 'Penjelasan visual tentang fungsi trigonometri dan aplikasinya.', 1),
(2, 'Grammar Guide English', 'Panduan lengkap tenses, preposisi, dan struktur kalimat.', 2),
(3, 'Physics Lecture Notes', 'Catatan kuliah tentang hukum Newton dan aplikasinya.', 1);

-- Insert Demo Assignments
INSERT INTO assignments (class_id, title, description, instructions, due_date, max_score, created_by) VALUES
(1, 'Latihan Aljabar Linear', 'Selesaikan soal-soal tentang sistem persamaan linear.', 'Kerjakan semua soal dengan langkah yang jelas. Boleh menggunakan kalkulator.', DATE_ADD(NOW(), INTERVAL 7 DAY), 100, 1),
(1, 'Quiz Trigonometri', 'Tes kemampuan Anda tentang fungsi trigonometri.', 'Jawab semua pertanyaan. Tidak boleh membuka buku atau catatan.', DATE_ADD(NOW(), INTERVAL 14 DAY), 50, 1),
(2, 'Essay Writing Task', 'Tulis essay tentang hobi Anda dalam bahasa Inggris.', 'Minimal 300 kata, spasi ganda, Times New Roman 12pt.', DATE_ADD(NOW(), INTERVAL 5 DAY), 100, 2),
(3, 'Physics Problem Set', 'Selesaikan soal-soal aplikasi hukum Newton.', 'Gunakan rumus yang benar dan tunjukkan semua langkah.', DATE_ADD(NOW(), INTERVAL 10 DAY), 100, 1);

-- Insert Demo Assignment Submissions
INSERT INTO assignment_submissions (assignment_id, student_id, submission_text, submitted_at, score, feedback, graded_by, graded_at, status) VALUES
(1, 4, 'Jawaban siswa untuk soal aljabar', DATE_SUB(NOW(), INTERVAL 2 DAY), 85, 'Bagus! Jawabannya benar. Hati-hati dengan langkah ke-3.', 1, NOW(), 'graded'),
(1, 5, 'Jawaban siswa untuk soal aljabar', DATE_SUB(NOW(), INTERVAL 2 DAY), 92, 'Sempurna! Semua langkah benar dan rapi.', 1, NOW(), 'graded'),
(3, 4, 'Essay tentang hobi membaca buku dan menonton film', DATE_SUB(NOW(), INTERVAL 1 DAY), NULL, NULL, NULL, NULL, 'submitted'),
(4, 6, 'Solusi soal fisika tentang gaya', DATE_SUB(NOW(), INTERVAL 3 DAY), 78, 'Cukup baik, tapi ada kesalahan di perhitungan akhir.', 1, NOW(), 'graded');

-- Insert Demo Attendance
INSERT INTO attendance (class_id, student_id, attendance_date, status) VALUES
(1, 4, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'present'),
(1, 4, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'present'),
(1, 4, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'present'),
(1, 5, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'absent'),
(1, 5, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'present'),
(1, 5, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'late'),
(1, 6, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'present'),
(1, 6, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'present'),
(1, 6, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'present');

-- Insert Demo Grades
INSERT INTO grades (class_id, student_id, total_score, grade_letter, final_status) VALUES
(1, 4, 82.50, 'B', 'pass'),
(1, 5, 88.75, 'A', 'pass'),
(1, 6, 75.25, 'C', 'pass'),
(1, 7, 68.50, 'D', 'pass'),
(2, 4, 80.00, 'B', 'pass'),
(2, 5, 85.50, 'A', 'pass'),
(2, 6, 72.25, 'D', 'pass'),
(3, 5, 90.00, 'A', 'pass'),
(3, 6, 78.50, 'C', 'pass'),
(3, 7, 82.00, 'B', 'pass');

-- Insert Demo Discussions
INSERT INTO discussions (class_id, title, content, created_by) VALUES
(1, 'Bagaimana cara cepat memahami trigonometri?', 'Saya kesulitan memahami fungsi sin, cos, dan tan. Apakah ada tips atau trik untuk menghapalnya?', 4),
(1, 'Aplikasi praktis dari persamaan linear', 'Saya ingin tahu aplikasi nyata dari sistem persamaan linear dalam kehidupan sehari-hari.', 5),
(2, 'Tips pronunciation bahasa Inggris', 'Bagaimana cara yang tepat untuk melafalkan bunyi /th/ dan /v/ dalam bahasa Inggris?', 6);

-- Insert Demo Discussion Comments
INSERT INTO discussion_comments (discussion_id, created_by, content) VALUES
(1, 1, 'Coba gunakan mnemonic SOHCAHTOA untuk mengingat sin, cos, tan. S=sin, O=opposite, H=hypotenuse, dst.'),
(1, 2, 'Atau coba visualisasi dengan lingkaran unit. Akan lebih mudah dipahami.'),
(2, 2, 'Sistem persamaan linear sering digunakan dalam perencanaan bisnis, manajemen proyek, dan analisis data.'),
(3, 1, 'Posisikan lidah di antara gigi untuk /th/ dan letakkan bibir bawah di belakang gigi atas untuk /v/.');
