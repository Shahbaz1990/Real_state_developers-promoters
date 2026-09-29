<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/config.php';

$isAjax = (isset($_POST['ajax']) && $_POST['ajax'] === '1')
    || (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

if (!isset($_SESSION['user_id'])) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please log in to change your password.']);
        exit;
    }
    header('Location: /front/index.php');
    exit;
}

$pageTitle = 'Change Password';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = trim($_POST['current_password'] ?? '');
    $newPass = trim($_POST['new_password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');

    if ($current === '') {
        $error = 'Please enter your current password.';
    } elseif (strlen($newPass) < 6) {
        $error = 'New password must be at least 6 characters long.';
    } elseif ($newPass !== $confirm) {
        $error = 'New password and confirm password do not match.';
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();

        $isMatch = false;
        if ($row && !empty($row['password'])) {
            if (password_verify($current, $row['password'])) {
                $isMatch = true;
            } elseif ($row['password'] === md5($current)) {
                $isMatch = true;
            } elseif ($row['password'] === $current) {
                $isMatch = true;
            }
        }

        if (!$isMatch) {
            $error = 'Your current password is incorrect.';
        } else {
            if ($current === $newPass) {
                $error = 'New password must be different from the current password.';
            } else {
                $hash = password_hash($newPass, PASSWORD_DEFAULT);
                $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $upd->bind_param("si", $hash, $_SESSION['user_id']);
                if ($upd->execute()) {
                    $message = 'Password changed successfully.';
                } else {
                    $error = 'Could not change your password. Please try again.';
                }
                $upd->close();
            }
        }
    }
}

if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => ($error === ''),
        'message' => ($error !== '') ? $error : $message,
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle); ?> - Tatito Developers</title>
<link rel="icon" type="image/png" href="<?php echo $site['favicon']; ?>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/account-pages.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="page-shell">
    <div class="container" style="max-width: 640px;">
        <div class="act-card">
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2"><i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" class="vstack gap-3">
                    <div>
                        <label class="form-label">Current Password</label>
                        <div class="input-group">
                            <input type="password" name="current_password" id="currentPass" class="form-control" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePass('currentPass', this)"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password" name="new_password" id="newPass" class="form-control" required minlength="6">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePass('newPass', this)"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" name="confirm_password" id="confirmPass" class="form-control" required minlength="6">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePass('confirmPass', this)"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gold"><i class="bi bi-shield-check"></i> Update Password</button>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
function togglePass(id, btn) {
    var input = document.getElementById(id);
    var isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    btn.innerHTML = isPass ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>