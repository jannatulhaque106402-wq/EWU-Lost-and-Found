<?php

session_start();

if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["role"] != "Admin"
) {
    header("Location: login.php");
    exit();
}

$pageTitle = "Admin Dashboard";
$pageFile  = "admin_dashboard.php";
include 'header.php';
?>

    <h1>EWU Lost &amp; Found</h1>
    <h2 class="subtitle">Admin Dashboard</h2>

    <hr>

    <ul class="menu-list">
        <li><a href="view_items.php">Manage Item Reports</a></li>
        <li><a href="manage_users.php">Manage Users</a></li>
        <li><a href="manage_categories.php">Manage Categories</a></li>
        <li><a href="manage_claims.php">Manage Claims</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>

<?php include 'footer.php'; ?>