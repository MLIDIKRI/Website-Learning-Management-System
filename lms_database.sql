-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 24, 2026 at 05:58 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lms_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `priority` enum('low','normal','high') DEFAULT 'normal',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `class_id`, `title`, `content`, `created_by`, `priority`, `created_at`, `updated_at`) VALUES
(1, 1, 'Selamat Datang di Kelas Matematika', 'Halo semua! Selamat datang di kelas Matematika Dasar. Mari kita belajar bersama dan saling membantu.', 1, 'high', '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(2, 1, 'Jadwal Kelas dan Waktu Konsultasi', 'Kelas diadakan setiap Senin dan Rabu pukul 10:00-12:00. Waktu konsultasi Jumat 14:00-16:00.', 1, 'normal', '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(3, 2, 'Assignment Minggu Pertama', 'Silahkan selesaikan latihan writing di modul pembelajaran. Deadline Jumat minggu ini.', 2, 'normal', '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(4, 3, 'Persiapan Quiz Fisika', 'Quiz akan diadakan minggu depan. Pelajari materi tentang gaya dan gerakan.', 1, 'high', '2026-03-09 08:09:02', '2026-03-09 08:09:02');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `instructions` text DEFAULT NULL,
  `due_date` datetime NOT NULL,
  `max_score` decimal(5,2) DEFAULT 100.00,
  `created_by` int(11) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `class_id`, `title`, `description`, `instructions`, `due_date`, `max_score`, `created_by`, `file_path`, `created_at`, `updated_at`) VALUES
(1, 1, 'Latihan Aljabar Linear', 'Selesaikan soal-soal tentang sistem persamaan linear.', 'Kerjakan semua soal dengan langkah yang jelas. Boleh menggunakan kalkulator.', '2026-03-16 15:09:02', 100.00, 1, NULL, '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(2, 1, 'Quiz Trigonometri', 'Tes kemampuan Anda tentang fungsi trigonometri.', 'Jawab semua pertanyaan. Tidak boleh membuka buku atau catatan.', '2026-03-23 15:09:02', 50.00, 1, NULL, '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(3, 2, 'Essay Writing Task', 'Tulis essay tentang hobi Anda dalam bahasa Inggris.', 'Minimal 300 kata, spasi ganda, Times New Roman 12pt.', '2026-03-14 15:09:02', 100.00, 2, NULL, '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(4, 3, 'Physics Problem Set', 'Selesaikan soal-soal aplikasi hukum Newton.', 'Gunakan rumus yang benar dan tunjukkan semua langkah.', '2026-03-19 15:09:02', 100.00, 1, NULL, '2026-03-09 08:09:02', '2026-03-09 08:09:02');

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submissions`
--

CREATE TABLE `assignment_submissions` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `submission_text` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `graded_by` int(11) DEFAULT NULL,
  `graded_at` datetime DEFAULT NULL,
  `status` enum('submitted','graded','late') DEFAULT 'submitted',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignment_submissions`
--

INSERT INTO `assignment_submissions` (`id`, `assignment_id`, `student_id`, `submission_text`, `file_path`, `submitted_at`, `score`, `feedback`, `graded_by`, `graded_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 'Jawaban siswa untuk soal aljabar', NULL, '2026-03-07 15:09:02', 85.00, 'Bagus! Jawabannya benar. Hati-hati dengan langkah ke-3.', 1, '2026-03-09 15:09:02', 'graded', '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(2, 1, 5, 'Jawaban siswa untuk soal aljabar', NULL, '2026-03-07 15:09:02', 92.00, 'Sempurna! Semua langkah benar dan rapi.', 1, '2026-03-09 15:09:02', 'graded', '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(3, 3, 4, 'Essay tentang hobi membaca buku dan menonton film', NULL, '2026-03-09 09:17:53', NULL, NULL, NULL, NULL, 'submitted', '2026-03-09 08:09:02', '2026-03-09 08:17:53'),
(4, 4, 6, 'Solusi soal fisika tentang gaya', NULL, '2026-03-06 15:09:02', 78.00, 'Cukup baik, tapi ada kesalahan di perhitungan akhir.', 1, '2026-03-09 15:09:02', 'graded', '2026-03-09 08:09:02', '2026-03-09 08:09:02');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','late','excused') DEFAULT 'absent',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `class_id`, `student_id`, `attendance_date`, `status`, `created_at`) VALUES
(1, 1, 4, '2026-03-04', 'present', '2026-03-09 08:09:02'),
(2, 1, 4, '2026-03-06', 'present', '2026-03-09 08:09:02'),
(3, 1, 4, '2026-03-08', 'present', '2026-03-09 08:09:02'),
(4, 1, 5, '2026-03-04', 'absent', '2026-03-09 08:09:02'),
(5, 1, 5, '2026-03-06', 'present', '2026-03-09 08:09:02'),
(6, 1, 5, '2026-03-08', 'late', '2026-03-09 08:09:02'),
(7, 1, 6, '2026-03-04', 'present', '2026-03-09 08:09:02'),
(8, 1, 6, '2026-03-06', 'present', '2026-03-09 08:09:02'),
(9, 1, 6, '2026-03-08', 'present', '2026-03-09 08:09:02'),
(10, 2, 4, '2026-03-09', 'present', '2026-03-09 08:19:14'),
(11, 2, 5, '2026-03-09', 'absent', '2026-03-09 08:19:14'),
(12, 2, 6, '2026-03-09', 'absent', '2026-03-09 08:19:14'),
(13, 2, 4, '2026-04-09', 'present', '2026-04-01 09:06:06'),
(14, 2, 5, '2026-04-09', 'absent', '2026-04-01 09:06:06'),
(15, 2, 6, '2026-04-09', 'absent', '2026-04-01 09:06:06'),
(16, 2, 4, '2026-04-01', 'present', '2026-04-01 09:06:42'),
(17, 2, 5, '2026-04-01', 'present', '2026-04-01 09:06:42'),
(18, 2, 6, '2026-04-01', 'absent', '2026-04-01 09:06:42');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `code`, `name`, `description`, `teacher_id`, `class_image`, `status`, `created_at`, `updated_at`) VALUES
(1, 'MTH101', 'Matematika Dasar', 'Kelas matematika untuk pemula yang membahas aljabar, geometri, dan trigonometri dasar.', 1, NULL, 'active', '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(2, 'ENG102', 'Bahasa Inggris Umum', 'Kelas grammar, speaking, listening, reading, dan writing untuk tingkat menengah.', 2, NULL, 'active', '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(3, 'SCI103', 'Sains Fisika', 'Pembelajaran tentang mekanika, energi, gelombang, dan optika.', 1, NULL, 'active', '2026-03-09 08:09:02', '2026-03-09 08:09:02');

-- --------------------------------------------------------

--
-- Table structure for table `class_members`
--

CREATE TABLE `class_members` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_members`
--

INSERT INTO `class_members` (`id`, `class_id`, `student_id`, `enrollment_date`) VALUES
(1, 1, 4, '2026-03-09 08:09:02'),
(2, 1, 5, '2026-03-09 08:09:02'),
(3, 1, 6, '2026-03-09 08:09:02'),
(5, 2, 4, '2026-03-09 08:09:02'),
(6, 2, 5, '2026-03-09 08:09:02'),
(7, 2, 6, '2026-03-09 08:09:02'),
(8, 3, 5, '2026-03-09 08:09:02'),
(9, 3, 6, '2026-03-09 08:09:02');

-- --------------------------------------------------------

--
-- Table structure for table `discussions`
--

CREATE TABLE `discussions` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discussions`
--

INSERT INTO `discussions` (`id`, `class_id`, `title`, `content`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'Bagaimana cara cepat memahami trigonometri?', 'Saya kesulitan memahami fungsi sin, cos, dan tan. Apakah ada tips atau trik untuk menghapalnya?', 4, '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(2, 1, 'Aplikasi praktis dari persamaan linear', 'Saya ingin tahu aplikasi nyata dari sistem persamaan linear dalam kehidupan sehari-hari.', 5, '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(3, 2, 'Tips pronunciation bahasa Inggris', 'Bagaimana cara yang tepat untuk melafalkan bunyi /th/ dan /v/ dalam bahasa Inggris?', 6, '2026-03-09 08:09:02', '2026-03-09 08:09:02');

-- --------------------------------------------------------

--
-- Table structure for table `discussion_comments`
--

CREATE TABLE `discussion_comments` (
  `id` int(11) NOT NULL,
  `discussion_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discussion_comments`
--

INSERT INTO `discussion_comments` (`id`, `discussion_id`, `created_by`, `content`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Coba gunakan mnemonic SOHCAHTOA untuk mengingat sin, cos, tan. S=sin, O=opposite, H=hypotenuse, dst.', '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(2, 1, 2, 'Atau coba visualisasi dengan lingkaran unit. Akan lebih mudah dipahami.', '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(3, 2, 2, 'Sistem persamaan linear sering digunakan dalam perencanaan bisnis, manajemen proyek, dan analisis data.', '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(4, 3, 1, 'Posisikan lidah di antara gigi untuk /th/ dan letakkan bibir bawah di belakang gigi atas untuk /v/.', '2026-03-09 08:09:02', '2026-03-09 08:09:02');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `total_score` decimal(5,2) DEFAULT NULL,
  `grade_letter` varchar(2) DEFAULT NULL,
  `final_status` enum('pass','fail','incomplete') DEFAULT 'incomplete',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `class_id`, `student_id`, `total_score`, `grade_letter`, `final_status`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 82.50, 'B', 'pass', '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(2, 1, 5, 88.75, 'A', 'pass', '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(3, 1, 6, 75.25, 'C', 'pass', '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(5, 2, 4, 80.00, 'B', 'pass', '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(6, 2, 5, 85.50, 'A', 'pass', '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(7, 2, 6, 72.25, 'D', 'pass', '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(8, 3, 5, 90.00, 'A', 'pass', '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(9, 3, 6, 78.50, 'C', 'pass', '2026-03-09 08:09:02', '2026-03-09 08:09:02');

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`id`, `class_id`, `title`, `description`, `file_path`, `file_type`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'Buku Ajar Matematika Dasar', 'Materi lengkap tentang aljabar linear dan operasi dasar.', NULL, NULL, 1, '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(2, 1, 'Slide Presentasi Trigonometri', 'Penjelasan visual tentang fungsi trigonometri dan aplikasinya.', NULL, NULL, 1, '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(3, 2, 'Grammar Guide English', 'Panduan lengkap tenses, preposisi, dan struktur kalimat.', NULL, NULL, 2, '2026-03-09 08:09:02', '2026-03-09 08:09:02'),
(4, 3, 'Physics Lecture Notes', 'Catatan kuliah tentang hukum Newton dan aplikasinya.', NULL, NULL, 1, '2026-03-09 08:09:02', '2026-03-09 08:09:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(300) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `role` enum('admin','teacher','student') NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `role`, `avatar`, `bio`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@lms.com', '$2y$10$Vlu7BqWCYZyfdXput7/AIe5j7lZgjCuQ.v4tL/PXSTxM6eo9K5tdu', 'Administrator', '082123456789', 'admin', NULL, NULL, 'active', '2026-03-09 08:09:02', '2026-03-09 08:13:49'),
(2, 'teacher1', 'teacher1@lms.com', '$2y$10$Vlu7BqWCYZyfdXput7/AIe5j7lZgjCuQ.v4tL/PXSTxM6eo9K5tdu', 'Budi Santoso', '082234567890', 'teacher', NULL, NULL, 'active', '2026-03-09 08:09:02', '2026-03-09 08:13:49'),
(3, 'teacher2', 'teacher2@lms.com', '$2y$10$Vlu7BqWCYZyfdXput7/AIe5j7lZgjCuQ.v4tL/PXSTxM6eo9K5tdu', 'Siti Nurhaliza', '082345678901', 'teacher', NULL, NULL, 'active', '2026-03-09 08:09:02', '2026-03-09 08:13:49'),
(4, 'student1', 'student1@lms.com', '$2y$10$Vlu7BqWCYZyfdXput7/AIe5j7lZgjCuQ.v4tL/PXSTxM6eo9K5tdu', 'Ahmad Riza', '081111111111', 'student', NULL, NULL, 'active', '2026-03-09 08:09:02', '2026-03-09 08:13:49'),
(5, 'student2', 'student2@lms.com', '$2y$10$Vlu7BqWCYZyfdXput7/AIe5j7lZgjCuQ.v4tL/PXSTxM6eo9K5tdu', 'Rini Wijaya', '081222222222', 'student', NULL, NULL, 'active', '2026-03-09 08:09:02', '2026-03-09 08:13:49'),
(6, 'student3', 'student3@lms.com', '$2y$10$Vlu7BqWCYZyfdXput7/AIe5j7lZgjCuQ.v4tL/PXSTxM6eo9K5tdu', 'Doni Pratama', '081333333333', 'student', NULL, NULL, 'active', '2026-03-09 08:09:02', '2026-03-09 08:13:49'),
(8, 'cinta@lms.com', 'dragon@gmail.com', '$2y$10$SXQpVXU/JFOJxIEIUIa9xeA88rCgTW4JFnc9qnFcLAm.CirP//Ul2', 'Dragon ball gt', '088973285467', 'student', NULL, NULL, 'active', '2026-04-01 09:00:42', '2026-04-01 09:26:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `graded_by` (`graded_by`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`class_id`,`student_id`,`attendance_date`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `class_members`
--
ALTER TABLE `class_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_class_student` (`class_id`,`student_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `discussions`
--
ALTER TABLE `discussions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `discussion_comments`
--
ALTER TABLE `discussion_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discussion_id` (`discussion_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_grades` (`class_id`,`student_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `class_members`
--
ALTER TABLE `class_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `discussions`
--
ALTER TABLE `discussions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `discussion_comments`
--
ALTER TABLE `discussion_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `announcements_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD CONSTRAINT `assignment_submissions_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignment_submissions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignment_submissions_ibfk_3` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_members`
--
ALTER TABLE `class_members`
  ADD CONSTRAINT `class_members_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_members_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discussions`
--
ALTER TABLE `discussions`
  ADD CONSTRAINT `discussions_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discussions_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discussion_comments`
--
ALTER TABLE `discussion_comments`
  ADD CONSTRAINT `discussion_comments_ibfk_1` FOREIGN KEY (`discussion_id`) REFERENCES `discussions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discussion_comments_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `materials_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
