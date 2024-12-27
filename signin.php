<?php

// Include database connection
require_once 'db.php'; // Replace with your actual database connection file

// Initialize error and success messages
$error = '';

// Check if the cookies for username and password are set
if (isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
    $username_or_email = $_COOKIE['username'];
    $password = $_COOKIE['password'];

    // Query to check if username or email exists
    $query = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $username_or_email, $username_or_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Start session and store user info
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];

            // Redirect user based on user type (admin or user)
            if ($user['user_type'] == 'admin' || $user['user_type'] == 'staff') {
                header("Location: orders.php"); // Redirect to admin dashboard
            } else {
                header("Location: index.php"); // Redirect to user dashboard
            }
            exit();
        } else {
            $error = "Invalid password. Please try again.";
        }
    } else {
        $error = "Username or email not found. Please try again.";
    }

    $stmt->close();
    $conn->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username_or_email = trim($_POST['username']);
    $password = trim($_POST['password']);
    $rememberMe = isset($_POST['rememberMe']); // Check if "Remember Me" is checked

    // Query to check if username or email exists
    $query = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $username_or_email, $username_or_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Start session and store user info
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];

            // If "Remember Me" is checked, store the username and password in cookies
            if ($rememberMe) {
                setcookie('username', $username_or_email, time() + (86400 * 30), "/"); // 30 days
                setcookie('password', $password, time() + (86400 * 30), "/"); // 30 days
            } else {
                // Clear cookies if "Remember Me" is not checked
                setcookie('username', "", time() - 3600, "/");
                setcookie('password', "", time() - 3600, "/");
            }

            // Redirect user based on user type (admin or user)
            if ($user['user_type'] == 'admin' || $user['user_type'] == 'staff') {
                header("Location: orders.php"); // Redirect to admin dashboard
            } else {
                header("Location: index.php"); // Redirect to user dashboard
            }
            exit();
        } else {
            $error = "Invalid password. Please try again.";
        }
    } else {
        $error = "Username or email not found. Please try again.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- HTML and form below -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Rizline</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./style.css">
</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-sm" style="width: 100%; max-width: 400px;">
            <h4 class="text-center mb-4">Sign In</h4>
            <!-- Show alert if there is an error -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <form action="signin.php" method="post" class="needs-validation" novalidate>
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" class="form-control" id="username" name="username" required
                        value="<?php echo isset($_COOKIE['username']) ? $_COOKIE['username'] : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required
                        value="<?php echo isset($_COOKIE['password']) ? $_COOKIE['password'] : ''; ?>">
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe" <?php echo isset($_COOKIE['username']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="rememberMe">Remember Me</label>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                <div class="text-center mt-3">
                    <small>Don't have an account? <a href="signup.php">Sign Up</a></small>
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
