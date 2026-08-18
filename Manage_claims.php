<?php

session_start();
include "db_conn.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] != "Admin") {
    header("Location: login.php");
    exit();
}

$actionMessage = "";

// Handle Approve/Reject Claim
if (isset($_GET["action"]) && isset($_GET["claim_id"])) {

    $claim_id = $_GET["claim_id"];
    $action = $_GET["action"]; // "approve" or "reject"
    $admin_id = $_SESSION["user_id"];

    if ($action == "approve") {

        $newStatus = "Approved";

        // Get item_id linked to this claim
        $getItemSql = "SELECT item_id FROM Claim WHERE claim_id = ?";
        $getItemStmt = $conn->prepare($getItemSql);
        $getItemStmt->bind_param("i", $claim_id);
        $getItemStmt->execute();
        $itemRow = $getItemStmt->get_result()->fetch_assoc();
        $item_id = $itemRow["item_id"];

        // Update claim status
        $updateClaimSql = "UPDATE Claim SET status = ?, verified_by = ? WHERE claim_id = ?";
        $updateClaimStmt = $conn->prepare($updateClaimSql);
        $updateClaimStmt->bind_param("sii", $newStatus, $admin_id, $claim_id);
        $updateClaimStmt->execute();

        // Update item status to Claimed
        $updateItemSql = "UPDATE Item SET status = 'Claimed' WHERE item_id = ?";
        $updateItemStmt = $conn->prepare($updateItemSql);
        $updateItemStmt->bind_param("i", $item_id);
        $updateItemStmt->execute();

        $actionMessage = "Claim approved and item marked as Claimed.";

    } elseif ($action == "reject") {

        $newStatus = "Rejected";

        $updateClaimSql = "UPDATE Claim SET status = ?, verified_by = ? WHERE claim_id = ?";
        $updateClaimStmt = $conn->prepare($updateClaimSql);
        $updateClaimStmt->bind_param("sii", $newStatus, $admin_id, $claim_id);
        $updateClaimStmt->execute();

        $actionMessage = "Claim rejected.";
    }
}

// Fetch all claims with item title and claimant name
$sql = "SELECT Claim.claim_id, Claim.claim_date, Claim.status, Claim.verification_notes,
               Item.item_id, Item.title AS item_title,
               User.name AS claimant_name, User.email AS claimant_email
        FROM Claim
        JOIN Item ON Claim.item_id = Item.item_id
        JOIN User ON Claim.claimant_id = User.user_id
        ORDER BY Claim.claim_id DESC";

$claims = $conn->query($sql);

function claimBadgeClass($status) {
    switch ($status) {
        case "Approved": return "badge-approved";
        case "Rejected": return "badge-rejected";
        default:         return "badge-pending";
    }
}

$pageTitle = "Manage Claims";
$pageFile  = "manage_claims.php";
include 'header.php';
?>

    <h1>EWU Lost &amp; Found</h1>
    <h2 class="subtitle">Manage Claims</h2>

    <?php if ($actionMessage) { ?>
        <div class="msg-success"><?php echo htmlspecialchars($actionMessage); ?></div>
    <?php } ?>

    <table>
        <thead>
            <tr>
                <th>Claim ID</th>
                <th>Item</th>
                <th>Claimant</th>
                <th>Claim Date</th>
                <th>Status</th>
                <th>Notes</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($claim = $claims->fetch_assoc()): ?>
            <tr>
                <td><?= $claim["claim_id"] ?></td>
                <td><?= htmlspecialchars($claim["item_title"]) ?></td>
                <td><?= htmlspecialchars($claim["claimant_name"]) ?> (<?= htmlspecialchars($claim["claimant_email"]) ?>)</td>
                <td><?= $claim["claim_date"] ?></td>
                <td>
                    <span class="badge <?php echo claimBadgeClass($claim['status']); ?>">
                        <?= htmlspecialchars($claim["status"]) ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($claim["verification_notes"]) ?></td>
                <td>
                    <?php if ($claim["status"] == "Pending"): ?>
                        <a class="action-link" href="manage_claims.php?action=approve&claim_id=<?= $claim['claim_id'] ?>"
                           onclick="return confirm('Approve this claim?');">
                            Approve
                        </a>
                        |
                        <a class="action-link" href="manage_claims.php?action=reject&claim_id=<?= $claim['claim_id'] ?>"
                           onclick="return confirm('Reject this claim?');">
                            Reject
                        </a>
                    <?php else: ?>
                        <?= htmlspecialchars($claim["status"]) ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <p style="margin-top:18px;">
        <a href="admin_dashboard.php" class="link-arrow">Back to Dashboard</a>
    </p>

<?php include 'footer.php'; ?>