<?php

session_start();
include "db_conn.php";

// Only Admin can access this page
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "Admin") {
    header("Location: login.php");
    exit();
}

$actionMessage = "";
$actionError = "";

// Handle Add Category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_category"])) {

    $category_name = trim($_POST["category_name"]);

    if (empty($category_name)) {
        $actionError = "Category name cannot be empty.";
    } else {
        $sql = "INSERT INTO Category (category_name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $category_name);

        if ($stmt->execute()) {
            $actionMessage = "Category added successfully!";
        } else {
            $actionError = "Failed to add category: " . $conn->error;
        }
    }
}

// Handle Delete Category
if (isset($_GET["delete"])) {

    $category_id = $_GET["delete"];

    // Check if any items are using this category before deleting
    $checkSql = "SELECT COUNT(*) AS total FROM Item WHERE category_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $category_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result()->fetch_assoc();

    if ($checkResult["total"] > 0) {
        $actionError = "Cannot delete this category — it is currently used by one or more items.";
    } else {
        $deleteSql = "DELETE FROM Category WHERE category_id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $category_id);

        if ($deleteStmt->execute()) {
            $actionMessage = "Category deleted successfully!";
        } else {
            $actionError = "Failed to delete category: " . $conn->error;
        }
    }
}

// Fetch all categories
$categories = $conn->query("SELECT * FROM Category ORDER BY category_id");

$pageTitle = "Manage Categories";
$pageFile  = "manage_categories.php";
include 'header.php';
?>

    <h1>EWU Lost &amp; Found</h1>
    <h2 class="subtitle">Manage Categories</h2>

    <?php if ($actionMessage) { ?>
        <div class="msg-success"><?php echo htmlspecialchars($actionMessage); ?></div>
    <?php } ?>
    <?php if ($actionError) { ?>
        <div class="msg-error"><?php echo htmlspecialchars($actionError); ?></div>
    <?php } ?>

    <form method="POST">
        <label for="category_name">New Category Name</label>
        <div class="inline-row">
            <div>
                <input type="text" id="category_name" name="category_name" required>
            </div>
            <div>
                <button type="submit" name="add_category">Add Category</button>
            </div>
        </div>
    </form>

    <table style="margin-top:24px;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($cat = $categories->fetch_assoc()): ?>
            <tr>
                <td><?= $cat["category_id"] ?></td>
                <td><?= htmlspecialchars($cat["category_name"]) ?></td>
                <td>
                    <a class="action-link" href="manage_categories.php?delete=<?= $cat['category_id'] ?>"
                       onclick="return confirm('Are you sure you want to delete this category?');">
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