<?php
require_once __DIR__ . '/includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (strlen($username) < 3 || strlen($password) < 6) {
        $_SESSION['error_msg'] = "Username must be at least 3 characters and password at least 6 characters.";
    } else {
        $stmt = $pdo->prepare("SELECT count(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['error_msg'] = "Username already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            if ($stmt->execute([$username, $hashed])) {
                $_SESSION['success_msg'] = "Registration successful! Please login.";
                header("Location: /sweet_shop/login.php");
                exit;
            } else {
                $_SESSION['error_msg'] = "Registration failed. Please try again.";
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="form-container">
    <h2 style="text-align: center; margin-bottom: 1.5rem;">Register</h2>
    <form action="/sweet_shop/register.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
    </form>
    <p style="text-align: center; margin-top: 1rem;">
        Already have an account? <a href="/sweet_shop/login.php" style="color: var(--accent-color); font-weight: 600;">Login</a>
    </p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

