<?php

session_start();

include "db_conn.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $item_id = $_POST["item_id"];
    $claimant_id = $_SESSION["user_id"];
    $claim_reason = trim($_POST["claim_reason"]);

    if (empty($item_id) || empty($claim_reason)) {
        $message = "Please select an item and explain why it's yours.";
    } else {

        $sql = "INSERT INTO Claim
                (item_id, claimant_id, claim_reason, claim_date, status)
                VALUES (?, ?, ?, CURDATE(), 'Pending')";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "iis",
            $item_id,
            $claimant_id,
            $claim_reason
        );

        if ($stmt->execute()) {
            $message = "Claim submitted successfully! An admin will verify it.";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

$items = $conn->query("SELECT item_id, title, report_type FROM Item WHERE status = 'Approved' OR status = 'Pending' ORDER BY date_reported DESC");

$pageTitle = "Submit Claim";
$pageFile  = "add_claim.php";
include 'header.php';
?>

    <h1>EWU Lost &amp; Found</h1>
    <h2 class="subtitle">Submit a Claim</h2>

    <?php if ($message) { ?>
        <div class="msg-success"><?php echo htmlspecialchars($message); ?></div>
    <?php } ?>

    <form method="POST">

        <label for="item_id">Select Item</label>
        <select id="item_id" name="item_id" required>
            <option value="">-- Select Item --</option>
            <?php while ($row = $items->fetch_assoc()) { ?>
                <option value="<?php echo $row['item_id']; ?>">
                    <?php echo htmlspecialchars($row['title']) . " (" . $row['report_type'] . ")"; ?>
                </option>
            <?php } ?>
        </select>

        <label for="claim_reason">Why is this item yours? (Proof / Description)</label>
        <textarea id="claim_reason" name="claim_reason" rows="4" required
                  style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:5px; font-size:14px; font-family:inherit;"></textarea>

        <button type="submit">Submit Claim</button>

    </form>

    <p style="margin-top:18px;">
        <a href="student_dashboard.php" class="link-arrow">Back to Dashboard</a>
    </p>

<?php include 'footer.php'; ?>
