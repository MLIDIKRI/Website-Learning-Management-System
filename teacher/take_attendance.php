<?php
require '../includes/config.php';
$page_title = 'Absen Siswa';

if (get_user_role() != 'teacher') {
    redirect('index.php');
}

$teacher_id = $_SESSION['user_id'];
$class_id = (int)$_GET['class_id'];

// Check if class belongs to teacher
$class_query = $conn->query("SELECT * FROM classes WHERE id = $class_id AND teacher_id = $teacher_id");
if ($class_query->num_rows == 0) {
    redirect('teacher/attendance.php');
}

$class = $class_query->fetch_assoc();

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_attendance'])) {
    $attendance_date = $conn->real_escape_string($_POST['attendance_date']);

    // Get all students in class
    $students = $conn->query("SELECT student_id FROM class_members WHERE class_id = $class_id");

    while ($student = $students->fetch_assoc()) {
        $student_id = $student['student_id'];
        $status = sanitize($_POST["status_$student_id"]);

        // Check if attendance already exists
        $check = $conn->query("SELECT id FROM attendance WHERE class_id = $class_id AND student_id = $student_id AND attendance_date = '$attendance_date'");

        if ($check->num_rows > 0) {
            $query = "UPDATE attendance SET status = '$status' WHERE class_id = $class_id AND student_id = $student_id AND attendance_date = '$attendance_date'";
        } else {
            $query = "INSERT INTO attendance (class_id, student_id, attendance_date, status) 
                     VALUES ($class_id, $student_id, '$attendance_date', '$status')";
        }

        $conn->query($query);
    }

    alert('Absensi berhasil disimpan!', 'success');
    redirect('teacher/attendance.php');
}

// Get all students in class
$students_query = "SELECT u.* FROM users u 
                  JOIN class_members cm ON u.id = cm.student_id 
                  WHERE cm.class_id = $class_id ORDER BY u.full_name";
$students = $conn->query($students_query);

// Get today's attendance if exists
$today = date('Y-m-d');
$today_attendance = $conn->query("SELECT student_id, status FROM attendance WHERE class_id = $class_id AND attendance_date = '$today'");
$attendance_map = [];
while ($att = $today_attendance->fetch_assoc()) {
    $attendance_map[$att['student_id']] = $att['status'];
}

require '../includes/header.php';
?>

<!-- Sidebar -->
<div class="col-md-3 mb-4">
    <div class="sidebar">
        <h5><i class="fas fa-chalkboard-user"></i> Teacher Panel</h5>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="classes.php"><i class="fas fa-book"></i> Kelas</a></li>
            <li><a href="assignments.php"><i class="fas fa-tasks"></i> Tugas</a></li>
            <li><a href="announcements.php"><i class="fas fa-bullhorn"></i> Pengumuman</a></li>
            <li><a href="discussions.php"><i class="fas fa-comments"></i> Diskusi</a></li>
            <li><a href="attendance.php" class="active"><i class="fas fa-list-check"></i> Kehadiran</a></li>
            <li><a href="grades.php"><i class="fas fa-chart-bar"></i> Nilai</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profil</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="col-md-9">
    <div class="mb-4">
        <a href="attendance.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <h2 class="mb-2"><i class="fas fa-clipboard-list"></i> Absen Siswa</h2>
    <p class="text-muted mb-4">Kelas: <?php echo $class['name']; ?></p>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-list-check"></i> Catat Kehadiran
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-4">
                    <label for="attendance_date" class="form-label">Tanggal Kehadiran</label>
                    <input type="date" class="form-control" id="attendance_date" name="attendance_date"
                        value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th style="text-align: center;">Hadir</th>
                                <th style="text-align: center;">Terlambat</th>
                                <th style="text-align: center;">Absen</th>
                                <th style="text-align: center;">Izin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($student = $students->fetch_assoc()):
                                $current_status = $attendance_map[$student['id']] ?? 'absent';
                            ?>
                                <tr>
                                    <td>
                                        <strong><?php echo $student['full_name']; ?></strong><br>
                                        <small class="text-muted">@<?php echo $student['username']; ?></small>
                                    </td>
                                    <td style="text-align: center;">
                                        <input type="radio" name="status_<?php echo $student['id']; ?>"
                                            value="present" <?php echo $current_status == 'present' ? 'checked' : ''; ?>>
                                    </td>
                                    <td style="text-align: center;">
                                        <input type="radio" name="status_<?php echo $student['id']; ?>"
                                            value="late" <?php echo $current_status == 'late' ? 'checked' : ''; ?>>
                                    </td>
                                    <td style="text-align: center;">
                                        <input type="radio" name="status_<?php echo $student['id']; ?>"
                                            value="absent" <?php echo $current_status == 'absent' ? 'checked' : ''; ?>>
                                    </td>
                                    <td style="text-align: center;">
                                        <input type="radio" name="status_<?php echo $student['id']; ?>"
                                            value="excused" <?php echo $current_status == 'excused' ? 'checked' : ''; ?>>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <button type="submit" name="save_attendance" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Absensi
                </button>
            </form>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>