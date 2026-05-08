<?php
require_once __DIR__ . '/includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];
        
        $_SESSION['success_msg'] = "Welcome back, " . htmlspecialchars($username) . "!";
        header("Location: /sweet_shop/index.php");
        exit;
    } else {
        $_SESSION['error_msg'] = "Invalid username or password.";
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="form-container">
    <h2 style="text-align: center; margin-bottom: 1.5rem;">Login</h2>
    <form action="/sweet_shop/login.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
    </form>
    <p style="text-align: center; margin-top: 1rem;">
        Don't have an account? <a href="/sweet_shop/register.php" style="color: var(--accent-color); font-weight: 600;">Register</a>
    </p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

