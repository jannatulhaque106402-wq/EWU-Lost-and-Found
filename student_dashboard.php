<?php

session_start();

if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["role"] != "Student"
) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Student Dashboard</title>

</head>

<body>

<h2>EWU Lost & Found</h2>

<h3>
    Welcome,
    <?php echo $_SESSION["name"]; ?>
</h3>

<hr>

<a href="add_item.php">
    Report Lost / Found Item
</a>

<br><br>

<a href="view_items.php">
    View Lost & Found Items
</a>

<br><br>

<a href="add_claim.php">
    Submit Claim
</a>

<br><br>

<a href="logout.php">
    Logout
</a>

</body>

</html>