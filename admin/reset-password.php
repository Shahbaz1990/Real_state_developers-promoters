<?php
require_once __DIR__ . '/includes/security.php';
session_start_secure();

require_once __DIR__ . '/config.php';

$msg = "";
$msg_type = "";

// Allow starting over from a stale/failed session
if(isset($_GET['cancel'])){
    unset($_SESSION['reset_user_id'], $_SESSION['reset_verified'], $_SESSION['reset_attempts']);
    header("Location: forgot-password.php");
    exit;
}

$has_session = isset($_SESSION['reset_user_id']);
$is_verified = $has_session && !empty($_SESSION['reset_verified']);

// -----------------------------------------------------------------
// STEP 1: Verify the 6-digit code
// -----------------------------------------------------------------
if(isset($_POST['verify_code']) && $has_session && !$is_verified){

    if(!verify_csrf()){
        $msg = "Your session expired. Please try again.";
        $msg_type = "danger";
    } else {
        $entered = trim($_POST['code']);

    // basic brute-force protection: 5 attempts per requested code
    $_SESSION['reset_attempts'] = ($_SESSION['reset_attempts'] ?? 0) + 1;

    if($_SESSION['reset_attempts'] > 5){

        $msg = "Too many incorrect attempts. Please request a new code.";
        $msg_type = "danger";
        unset($_SESSION['reset_user_id'], $_SESSION['reset_verified'], $_SESSION['reset_attempts']);
        $has_session = false;

    }else{

        $stmt = $conn->prepare("SELECT reset_token, reset_expires FROM users WHERE id=?");
        $stmt->bind_param("i", $_SESSION['reset_user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if($user && strtotime($user['reset_expires']) < time()){
            $msg = "This code has expired. Please request a new one.";
            $msg_type = "danger";
        }elseif($user && hash_equals((string) $user['reset_token'], $entered)){
            $_SESSION['reset_verified'] = true;
            $is_verified = true;
            unset($_SESSION['reset_attempts']);
        }else{
            $msg = "Incorrect code. Please try again.";
            $msg_type = "danger";
        }
    }
    }
}

// -----------------------------------------------------------------
// STEP 2: Set the new password
// -----------------------------------------------------------------
if(isset($_POST['reset_password']) && $is_verified){

    if(!verify_csrf()){
        $msg = "Your session expired. Please try again.";
        $msg_type = "danger";
    } else {
        $password = $_POST['password'];
        $confirm  = $_POST['confirm_password'];

    if($password !== $confirm){
        $msg = "Passwords do not match.";
        $msg_type = "danger";
    }elseif(strlen($password) < 6){
        $msg = "Password must be at least 6 characters long.";
        $msg_type = "danger";
    }else{

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $update = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expires=NULL WHERE id=?");
        $update->bind_param("si", $hashed, $_SESSION['reset_user_id']);

        if($update->execute()){
            $msg = "Password reset successfully. You can now log in with your new password.";
            $msg_type = "success";
            unset($_SESSION['reset_user_id'], $_SESSION['reset_verified'], $_SESSION['reset_attempts']);
            $has_session = false;
            $is_verified = false;
        }else{
            $msg = "Something went wrong. Please try again.";
            $msg_type = "danger";
        }
        $update->close();
    }
    }
}

// -----------------------------------------------------------------
// STEP 2 finished
// -----------------------------------------------------------------
?>

<!DOCTYPE html>
<html>
    <head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/adminlte.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    </head>
<body>

    <div class="d-flex align-items-center justify-content-center vh-100 bg-light">

        <div class="col-md-4">

        <div class="card shadow">

        <div class="card-header bg-primary text-white text-center">
        <h4>Reset Password</h4>
        </div>

        <div class="card-body">

        <?php if($msg!=""){ ?>
        <div class="alert alert-<?= $msg_type=='success' ? 'success' : 'danger' ?>">
        <?= $msg ?>
        </div>
        <?php } ?>

        <?php if(!$has_session){ ?>

        <p class="text-muted text-center">No active reset request found.</p>
        <div class="text-center">
        <a href="forgot-password.php" class="btn btn-outline-primary">Request a New Code</a>
        </div>

        <?php }elseif(!$is_verified){ ?>

        <p class="text-muted">Enter the 6-digit code we sent to your email.</p>

        <form method="post">

        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">

        <div class="mb-3">
        <label>6-Digit Code</label>
        <input
        type="text"
        name="code"
        class="form-control"
        inputmode="numeric"
        pattern="[0-9]{6}"
        maxlength="6"
        autocomplete="one-time-code"
        required>
        </div>

        <button
        name="verify_code"
        class="btn btn-primary w-100">
        Verify Code
        </button>

        </form>

        <div class="text-center mt-3">
        <a href="reset-password.php?cancel=1">Request a new code</a>
        </div>

        <?php }else{ ?>

        <form method="post">

        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">

        <div class="mb-3">
        <label>New Password</label>
        <input
        type="password"
        name="password"
        class="form-control"
        minlength="6"
        required>
        </div>

        <div class="mb-3">
        <label>Confirm New Password</label>
        <input
        type="password"
        name="confirm_password"
        class="form-control"
        minlength="6"
        required>
        </div>

        <button
        name="reset_password"
        class="btn btn-primary w-100">
        Reset Password
        </button>

        </form>

        <?php } ?>

        <div class="text-center mt-3">
        <a href="login.php">Back to Login</a>
        </div>

        </div>

        </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>