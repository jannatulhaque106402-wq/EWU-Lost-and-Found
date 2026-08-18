<?php

session_start();
include "db_conn.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] != "Admin") {
    header("Location: login.php");
    exit();
}

$actionMessage = "";
$actionError = "";

// Handle Delete User
if (isset($_GET["delete"])) {

    $user_id = $_GET["delete"];

    // Prevent admin from deleting themselves
    if ($user_id == $_SESSION["user_id"]) {
        $actionError = "You cannot delete your own account while logged in.";
    } else {

        // Check if user has reported items (block delete if so, to protect FK integrity)
        $checkSql = "SELECT COUNT(*) AS total FROM Item WHERE reported_by = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("i", $user_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result()->fetch_assoc();

        if ($checkResult["total"] > 0) {
            $actionError = "Cannot delete this user — they have existing item reports linked to their account.";
        } else {
            $deleteSql = "DELETE FROM User WHERE user_id = ?";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->bind_param("i", $user_id);

            if ($deleteStmt->execute()) {
                $actionMessage = "User deleted successfully!";
            } else {
                $actionError = "Failed to delete user: " . $conn->error;
            }
        }
    }
}

// Handle Role Change (promote/demote Admin <-> Student)
if (isset($_GET["toggle_role"])) {

    $user_id = $_GET["toggle_role"];

    $getRoleSql = "SELECT role FROM User WHERE user_id = ?";
    $getRoleStmt = $conn->prepare($getRoleSql);
    $getRoleStmt->bind_param("i", $user_id);
    $getRoleStmt->execute();
    $currentRole = $getRoleStmt->get_result()->fetch_assoc()["role"];

    $newRole = ($currentRole == "Admin") ? "Student" : "Admin";

    $updateSql = "UPDATE User SET role = ? WHERE user_id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("si", $newRole, $user_id);

    if ($updateStmt->execute()) {
        $actionMessage = "User role updated to $newRole.";
    } else {
        $actionError = "Failed to update role: " . $conn->error;
    }
}

// Fetch all users
$users = $conn->query("SELECT * FROM User ORDER BY user_id");

$pageTitle = "Manage Users";
$pageFile  = "manage_users.php";
include 'header.php';
?>

    <h1>EWU Lost &amp; Found</h1>
    <h2 class="subtitle">Manage Users</h2>

    <?php if ($actionMessage) { ?>
        <div class="msg-success"><?php echo htmlspecialchars($actionMessage); ?></div>
    <?php } ?>
    <?php if ($actionError) { ?>
        <div class="msg-error"><?php echo htmlspecialchars($actionError); ?></div>
    <?php } ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Department</th>
                <th>Registered On</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $users->fetch_assoc()): ?>
            <tr>
                <td><?= $user["user_id"] ?></td>
                <td><?= htmlspecialchars($user["name"]) ?></td>
                <td><?= htmlspecialchars($user["email"]) ?></td>
                <td><?= htmlspecialchars($user["phone"]) ?></td>
                <td><?= htmlspecialchars($user["role"]) ?></td>
                <td><?= htmlspecialchars($user["department"]) ?></td>
                <td><?= $user["registration_date"] ?></td>
                <td>
                    <a class="action-link" href="manage_users.php?toggle_role=<?= $user['user_id'] ?>"
                       onclick="return confirm('Change this user\'s role?');">
                        Make <?= $user["role"] == "Admin" ? "Student" : "Admin" ?>
                    </a>
                    |
                    <a class="action-link" href="manage_users.php?delete=<?= $user['user_id'] ?>"
                       onclick="return confirm('Are you sure you want to delete this user?');">
                        Delete
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <p style="margin-top:18px;">
        <a href="admin_dashboard.php" class="link-arrow">Back to Dashboard</a>
    </p>

<?php include 'footer.php'; ?>