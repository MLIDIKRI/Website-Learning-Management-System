<?php
require 'includes/config.php';

if (!is_logged_in()) {
    redirect('index.php');
}

$role = get_user_role();

// Admin punya logs page, selain itu kembali ke dashboard role masing-masing.
if ($role === 'admin') {
    redirect('admin/logs.php');
} else {
    redirect("$role/dashboard.php");
}
