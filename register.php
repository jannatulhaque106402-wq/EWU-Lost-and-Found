<?php

include "db_conn.php";

$errorMessage = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $phone = trim($_POST["phone"]);
    $department = trim($_POST["department"]);

    // Server-side check: mandatory fields (phone excluded)
    if (
        empty($name) ||
        empty($email) ||
        empty($password) ||
        empty($department)
    ) {
        $errorMessage = "Name, Email, Password, and Department are required.";

    } elseif (!preg_match("/^[a-zA-Z0-9._-]+@std\.ewubd\.edu$/", $email)) {

        $errorMessage = "Please use your EWU student email (format: 2020-1-60-100@std.ewubd.edu).";

    } else {

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $sql = "INSERT INTO User
                (name, email, password, phone,
                 role, department, registration_date)
                VALUES (?, ?, ?, ?, 'Student', ?, CURDATE())";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sssss",
            $name,
            $email,
            $hashedPassword,
            $phone,
            $department
        );

        if ($stmt->execute()) {

            $successMessage = "Registration successful! You can now log in.";

        } else {

            $errorMessage = "Registration failed: " . $conn->error;
        }
    }
}

$pageTitle = "Student Registration";
$pageFile  = "register.php";
include 'header.php';
?>

    <h1>EWU Lost &amp; Found</h1>
    <h2 class="subtitle">Student Registration</h2>

    <?php if (!empty($errorMessage)): ?>
        <div class="msg-error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <?php if (!empty($successMessage)): ?>
        <div class="msg-success">
            <?php echo htmlspecialchars($successMessage); ?>
            <br>
            <a href="login.php" class="link-arrow">Login</a>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label for="name">Name</label>
        <input type="text"
               id="name"
               name="name"
               required>

        <label for="email">Email</label>
        <input type="email"
               id="email"
               name="email"
               placeholder="####-#-##-##@std.ewubd.edu"
               pattern="[a-zA-Z0-9._-]+@std\.ewubd\.edu"
               title="Please use your EWU student email (e.g., ####-#-##-###@std.ewubd.edu)"
               required>

        <label for="password">Password</label>
        <input type="password"
               id="password"
               name="password"
               required>

        <label for="phone">Phone (optional)</label>
        <input type="text"
               id="phone"
               name="phone">

        <label for="department">Department</label>
        <input type="text"
               id="department"
               name="department"
               required>

        <button type="submit">Register</button>

    </form>

    <p style="margin-top:18px;">
        <a href="login.php" class="link-arrow">Already have an account? Login</a>
    </p>

<?php include 'footer.php'; ?>
