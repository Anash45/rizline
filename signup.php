<?php
// Include database connection
require_once 'db.php'; // Replace with your actual database connection file

// Initialize variables for messages
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

    // Check if username or email already exists
    $query = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Username or email already exists. Please try again.";
    } else {
        // Insert new user into database
        $insert_query = "INSERT INTO users (username, email, password, user_type, reg_date) VALUES (?, ?, ?, 'user', NOW())";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param('sss', $username, $email, $hashed_password);

        if ($insert_stmt->execute()) {
            $success = "Account created successfully. You can now log in.";
        } else {
            $error = "Error creating account. Please try again.";
        }
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Rizline</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./style.css">
</head>

<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center ss-cont py-3">
    <div class="card p-4 shadow-sm" style="width: 100%; max-width: 400px;">
        <h4 class="text-center mb-4">Sign Up</h4>
        <!-- Show alerts -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <form action="signup.php" method="post" class="needs-validation" novalidate>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
            <div class="text-center mt-3">
                <small>Already have an account? <a href="signin.php">Sign In</a></small>
            </div>
        </form>
    </div>
</div>
<script defer src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script defer
        src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script defer src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script defer src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
<script defer src="script.js"></script>
</body>

</html>
