<?php

session_start();

include "db_conn.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Get user role
$userRole = isset($_SESSION["role"]) ? strtolower(trim($_SESSION["role"])) : "";
$isAdmin = ($userRole === "admin");

// Get all reported items
$sql = "SELECT 
            Item.item_id,
            Item.report_type,
            Item.title,
            Item.description,
            Item.location,
            Item.date_occurred,
            Item.date_reported,
            Item.status,
            Category.category_name,
            User.name AS reported_by_name
        FROM Item
        JOIN Category 
            ON Item.category_id = Category.category_id
        JOIN User 
            ON Item.reported_by = User.user_id
        ORDER BY Item.date_reported DESC";

$result = $conn->query($sql);

// Map DB status text to badge color class
function statusBadgeClass($status) {
    switch ($status) {
        case "Approved":
            return "badge-approved";

        case "Claimed":
            return "badge-approved";

        case "Rejected":
            return "badge-rejected";

        default:
            return "badge-pending";
    }
}

$pageTitle = "View Items";
$pageFile  = "view_items.php";

include "header.php";

?>

<h1>EWU Lost &amp; Found</h1>
<h2 class="subtitle">All Reported Items</h2>

<div class="table-wrap">
    <table>

        <thead>
            <tr>
                <th>Type</th>
                <th>Title</th>
                <th>Category</th>
                <th>Location</th>
                <th>Date Occurred</th>
                <th>Date Reported</th>
                <th>Status</th>
                <th>Reported By</th>

                <?php if ($isAdmin): ?>
                    <th>Description</th>
                <?php endif; ?>

            </tr>
        </thead>

        <tbody>

            <?php if ($result && $result->num_rows > 0): ?>

                <?php while ($row = $result->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars($row["report_type"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["title"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["category_name"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["location"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["date_occurred"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["date_reported"]); ?>
                        </td>

                        <td>
                            <span class="badge <?php echo statusBadgeClass($row["status"]); ?>">
                                <?php echo htmlspecialchars($row["status"]); ?>
                            </span>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["reported_by_name"]); ?>
                        </td>

                        <?php if ($isAdmin): ?>

                            <td class="desc-col">
                                <?php echo htmlspecialchars($row["description"]); ?>
                            </td>

                        <?php endif; ?>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="<?php echo $isAdmin ? 9 : 8; ?>">
                        No reported items found.
                    </td>
                </tr>

            <?php endif; ?>

        </tbody>

    </table>
</div>

<p style="margin-top:18px;">

    <a href="<?php echo $isAdmin ? 'admin_dashboard.php' : 'student_dashboard.php'; ?>" 
       class="link-arrow">
        Back to Dashboard
    </a>

</p>

<?php include "footer.php"; ?>
