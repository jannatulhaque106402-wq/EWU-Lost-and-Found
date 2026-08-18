<?php

session_start();

if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["role"] != "Student"
) {
    header("Location: login.php");
    exit();
}

$pageTitle = "Student Dashboard";
$pageFile  = "student_dashboard.php";
include 'header.php';
?>

    <h1>EWU Lost &amp; Found</h1>

    <hr>

    <ul class="menu-list">
        <li><a href="add_item.php">Report Lost / Found Item</a></li>
        <li><a href="view_items.php">View Lost &amp; Found Items</a></li>
        <li><a href="add_claim.php">Submit Claim</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>

<?php include 'footer.php'; ?>
Nusrat
<?php

session_start();

include "db_conn.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $report_type = $_POST["report_type"];
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $category_id = $_POST["category_id"];
    $location = trim($_POST["location"]);
    $date_occurred = $_POST["date_occurred"];
    $reported_by = $_SESSION["user_id"];

    if (empty($report_type) || empty($title) || empty($category_id) || empty($location) || empty($date_occurred)) {
        $message = "Please fill in all required fields.";
    } else {

        $sql = "INSERT INTO Item
                (report_type, title, description, category_id, location, date_occurred, date_reported, status, reported_by)
                VALUES (?, ?, ?, ?, ?, ?, CURDATE(), 'Pending', ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sssissi",
            $report_type,
            $title,
            $description,
            $category_id,
            $location,
            $date_occurred,
            $reported_by
        );

        if ($stmt->execute()) {
            $message = "Item reported successfully! Status: Pending review.";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

$categories = $conn->query("SELECT category_id, category_name FROM Category");

$pageTitle = "Report Item";
$pageFile  = "add_item.php";
include 'header.php';
?>

    <h1>EWU Lost &amp; Found</h1>
    <h2 class="subtitle">Report Lost / Found Item</h2>

    <?php if ($message) { ?>
        <div class="msg-success"><?php echo htmlspecialchars($message); ?></div>
    <?php } ?>

    <form method="POST">

        <label for="report_type">Report Type</label>
        <select id="report_type" name="report_type" required>
            <option value="">-- Select --</option>
            <option value="Lost">Lost</option>
            <option value="Found">Found</option>
        </select>

        <label for="title">Title</label>
        <input type="text" id="title" name="title" placeholder="Black wallet" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"
                  style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:5px; font-size:14px; font-family:inherit;"></textarea>

        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <option value="">-- Select Category --</option>
            <?php while ($row = $categories->fetch_assoc()) { ?>
                <option value="<?php echo $row['category_id']; ?>">
                    <?php echo htmlspecialchars($row['category_name']); ?>
                </option>
            <?php } ?>
        </select>

        <label for="location">Location</label>
        <input type="text" id="location" name="location" placeholder="Library, 2nd floor" required>

        <div class="inline-row">
            <div>
                <label for="date_occurred">Date Occurred</label>
                <input type="date" id="date_occurred" name="date_occurred" required>
            </div>
            <div>
                <button type="submit">Submit Report</button>
            </div>
        </div>

    </form>

    <p style="margin-top:18px;">
        <a href="student_dashboard.php" class="link-arrow">Back to Dashboard</a>
    </p>

<?php include 'footer.php'; ?>