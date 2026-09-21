<?php
/**
 * Practical 10: Secure Login Processing with password_verify() and RBAC
 * Course: ITUE203 - Web Development Frameworks | CHARUSAT
 */

require_once __DIR__ . '/db.php';

session_start();

$error = $_GET['error'] ?? '';
$msg = $_GET['msg'] ?? '';

// Check if already authenticated
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember_me']);

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $conn = getDB();
        $stmt = $conn->prepare("SELECT id, name, email, password_hash, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            // Verify Password using password_verify()
            if (password_verify($password, $user['password_hash'])) {
                // Prevent Session Fixation attacks: regenerate session ID
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['last_activity'] = time();

                // Update last_login timestamp
                $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $updateStmt->bind_param("i", $user['id']);
                $updateStmt->execute();
                $updateStmt->close();

                // Optional Secure Remember-Me Token
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $tokenHash = hash('sha256', $token);
                    $tokenStmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                    $tokenStmt->bind_param("si", $tokenHash, $user['id']);
                    $tokenStmt->execute();
                    $tokenStmt->close();

                    setcookie('studenthub_remember', $user['id'] . ':' . $token, [
                        'expires'  => time() + (86400 * 30), // 30 days
                        'path'     => '/',
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
                }

                // Role-based redirection
                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: student_dashboard.php");
                }
                exit;
            } else {
                $error = "Invalid password. Please try again.";
            }
        } else {
            $error = "No account found registered with this email address.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Practical 10 - Secure User Login | CHARUSAT</title>
  <style>
    body { font-family: system-ui, sans-serif; background: #f8fafc; color: #0f172a; padding: 2rem 1rem; }
    .card { max-width: 480px; margin: 2rem auto; background: white; padding: 2.5rem; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.06); }
    h1 { color: #1e3a8a; text-align: center; margin-bottom: 0.5rem; font-size: 1.8rem; }
    .subtitle { text-align: center; color: #64748b; font-size: 0.9rem; margin-bottom: 1.5rem; }
    .alert-error { background: #fee2e2; color: #991b1b; padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.9rem; border: 1px solid #fecaca; }
    .alert-info { background: #e0f2fe; color: #0369a1; padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.9rem; border: 1px solid #bae6fd; }
    .form-group { margin-bottom: 1.2rem; }
    label { display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.35rem; }
    .form-control { width: 100%; padding: 0.7rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; }
    .btn { width: 100%; background: #1e3a8a; color: white; padding: 0.8rem; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 1rem; }
    .btn:hover { background: #1d4ed8; }
    .credentials-box { margin-top: 1.5rem; padding: 1rem; background: #f1f5f9; border-radius: 6px; font-size: 0.8rem; color: #475569; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Portal Authentication</h1>
    <p class="subtitle">Practical 10: Secure Sessions &bull; RBAC &bull; Timeout Protection</p>

    <?php if (!empty($error)): ?>
      <div class="alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($msg)): ?>
      <div class="alert-info"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
      <div class="form-group">
        <label for="email">Student / Admin Email</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="user@charusat.edu.in" required />
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control" required />
      </div>

      <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
        <input type="checkbox" id="remember_me" name="remember_me" />
        <label for="remember_me" style="font-weight: normal; margin-bottom: 0;">Remember me on this browser</label>
      </div>

      <button type="submit" class="btn">Sign In</button>
    </form>

    <div class="credentials-box">
      <strong>Demo Test Accounts:</strong><br />
      &bull; <strong>Student:</strong> <code>alice@charusat.edu.in</code> | Password: <code>Student@2026!</code><br />
      &bull; <strong>Admin:</strong> <code>admin@charusat.edu.in</code> | Password: <code>Admin@2026!</code>
    </div>
  </div>
</body>
</html>
