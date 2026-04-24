<?php
require '../includes/config.php';
$page_title = 'Reports';

if (get_user_role() != 'admin') {
    redirect('index.php');
}

require '../includes/header.php';
?>

<!-- Sidebar -->
<div class="col-md-3 mb-4">
    <div class="sidebar">
        <h5><i class="fas fa-cog"></i> Admin Panel</h5>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Kelola Pengguna</a></li>
            <li><a href="classes.php"><i class="fas fa-book"></i> Kelola Kelas</a></li>
            <li><a href="reports.php" class="active"><i class="fas fa-chart-bar"></i> Laporan</a></li>
            <li><a href="logs.php"><i class="fas fa-history"></i> Activity Logs</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Pengaturan</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="col-md-9">
    <h2 class="mb-4"><i class="fas fa-chart-bar"></i> Laporan</h2>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-graduation-cap"></i> Laporan Nilai Siswa
                </div>
                <div class="card-body">
                    <p>Download laporan lengkap nilai semua siswa dari semua kelas.</p>
                    <a href="#" class="btn btn-primary"><i class="fas fa-download"></i> Download Excel</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-users-class"></i> Laporan Kehadiran
                </div>
                <div class="card-body">
                    <p>Download laporan kehadiran siswa per kelas.</p>
                    <a href="#" class="btn btn-primary"><i class="fas fa-download"></i> Download Excel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-tasks"></i> Laporan Tugas
                </div>
                <div class="card-body">
                    <p>Download laporan pengumpulan tugas siswa.</p>
                    <a href="#" class="btn btn-primary"><i class="fas fa-download"></i> Download Excel</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-book"></i> Laporan Aktivitas
                </div>
                <div class="card-body">
                    <p>Download laporan aktivitas pengguna dalam sistem.</p>
                    <a href="#" class="btn btn-primary"><i class="fas fa-download"></i> Download Excel</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>