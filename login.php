<?php

session_start();

include "db_conn.php";

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    // Validate EWU student email format
    // Pattern: anything before @, then exactly @std.ewubd.edu
    if (!preg_match("/^[a-zA-Z0-9._-]+@std\.ewubd\.edu$/", $email)) {

        $errorMessage = "Please use your EWU student email (format: ****-*-**-***@std.ewubd.edu).";

    } else {

        $sql = "SELECT *
                FROM User
                WHERE email = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();

            if (
                password_verify(
                    $password,
                    $user["password"]
                )
            ) {

                $_SESSION["user_id"]
                    = $user["user_id"];

                $_SESSION["name"]
                    = $user["name"];

                $_SESSION["role"]
                    = $user["role"];


                if ($user["role"] == "Admin") {

                    header(
                        "Location: admin_dashboard.php"
                    );

                } else {

                    header(
                        "Location: student_dashboard.php"
                    );
                }

                exit();

            } else {

                $errorMessage = "Invalid password!";
            }

        } else {

            $errorMessage = "User not found!";
        }
    }
}

$pageTitle = "EWU Lost & Found — login.php";
$pageFile  = "login.php";
include 'header.php';
?>

    <h1>EWU Lost &amp; Found Management System</h1>
    <h2 class="subtitle">Login</h2>

    <?php if (!empty($errorMessage)): ?>
        <div class="msg-error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <form method="POST">

        <label for="email">Email</label>
        <input type="email"
               id="email"
               name="email"
               placeholder="#### - # - ## - ###@std.ewubd.edu"
               pattern="[a-zA-Z0-9._-]+@std\.ewubd\.edu"
               title="Please use your EWU student email (e.g., ****-*-**-***@std.ewubd.edu)"
               required>

        <label for="password">Password</label>
        <input type="password"
               id="password"
               name="password"
               required>

        <button type="submit">Login</button>

    </form>

    <p style="margin-top:18px;">
        <a href="register.php" class="link-arrow">Create Student Account</a>
    </p>
<?php include 'footer.php'; ?>

