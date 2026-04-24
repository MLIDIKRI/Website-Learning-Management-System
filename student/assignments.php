<?php
require '../includes/config.php';
$page_title = 'Tugas';

if (get_user_role() != 'student') {
    redirect('index.php');
}

$student_id = $_SESSION['user_id'];

// Get all assignments from student's classes
$assignments_query = "SELECT a.*, c.name as class_name FROM assignments a 
                     JOIN classes c ON a.class_id = c.id 
                     JOIN class_members cm ON c.id = cm.class_id 
                     WHERE cm.student_id = $student_id 
                     ORDER BY a.due_date ASC";
$assignments = $conn->query($assignments_query);

require '../includes/header.php';
?>

<!-- Sidebar -->
<div class="col-md-3 mb-4">
    <div class="sidebar">
        <h5><i class="fas fa-graduation-cap"></i> Student Panel</h5>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="classes.php"><i class="fas fa-book"></i> Kelas Saya</a></li>
            <li><a href="assignments.php" class="active"><i class="fas fa-tasks"></i> Tugas</a></li>
            <li><a href="submissions.php"><i class="fas fa-file-upload"></i> Pengumpulan</a></li>
            <li><a href="grades.php"><i class="fas fa-chart-bar"></i> Nilai</a></li>
            <li><a href="materials.php"><i class="fas fa-book-open"></i> Materi</a></li>
            <li><a href="announcements.php"><i class="fas fa-bullhorn"></i> Pengumuman</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profil</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="col-md-9">
    <h2 class="mb-4"><i class="fas fa-tasks"></i> Tugas Saya</h2>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-list"></i> Daftar Tugas
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kelas</th>
                            <th>Judul Tugas</th>
                            <th>Tenggat Waktu</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($assign = $assignments->fetch_assoc()):
                            $submitted = $conn->query("SELECT id FROM assignment_submissions WHERE assignment_id = {$assign['id']} AND student_id = $student_id")->num_rows;
                            $now = new DateTime();
                            $due = new DateTime($assign['due_date']);
                            $is_late = $now > $due;

                            if ($is_late) {
                                $status = $submitted ? 'Terlambat' : 'Belum Dikumpul';
                                $status_badge = 'danger';
                            } else {
                                $status = $submitted ? 'Sudah Dikumpul' : 'Pending';
                                $status_badge = $submitted ? 'success' : 'warning';
                            }
                        ?>
                            <tr>
                                <td><?php echo $assign['class_name']; ?></td>
                                <td><?php echo $assign['title']; ?></td>
                                <td><?php echo date('d M Y H:i', strtotime($assign['due_date'])); ?></td>
                                <td><span class="badge badge-<?php echo $status_badge; ?>"><?php echo $status; ?></span></td>
                                <td>
                                    <a href="view_assignment.php?id=<?php echo $assign['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>