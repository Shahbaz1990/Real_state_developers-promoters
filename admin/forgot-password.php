<?php
require_once __DIR__ . '/includes/security.php';
session_start_secure();

require_once __DIR__ . '/config.php';

$msg = "";
$msg_type = "";
$code_sent = false;

if(isset($_POST['send_code'])){

    if(!verify_csrf()){
        $msg = "Your session expired. Please try again.";
        $msg_type = "danger";
    } else {
        $email = trim($_POST['email']);

        // Throttle code generation: max 5 per account+IP per 10 minutes
        $throttleKey = md5('pwreset_' . strtolower($email) . '/' . ($_SERVER['REMOTE_ADDR'] ?? ''));
        if(throttle_failures($throttleKey) >= 5){
            $msg = "Too many reset requests. Please wait a while and try again.";
            $msg_type = "danger";
        } else {
            $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
            if(!$stmt){
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result->num_rows > 0){

                $user = $result->fetch_assoc();

                // Generate a 6-digit numeric code, valid for 10 minutes
                $code    = str_pad((string) random_int(0, 999999), 6, "0", STR_PAD_LEFT);
                $expires = date("Y-m-d H:i:s", strtotime("+10 minutes"));

                $update = $conn->prepare("UPDATE users SET reset_token=?, reset_expires=? WHERE id=?");
                $update->bind_param("ssi", $code, $expires, $user['id']);
                $update->execute();
                $update->close();
                throttle_record_failure($throttleKey);

                // Track this reset attempt in the session (no token in the URL this time)
                $_SESSION['reset_user_id']  = $user['id'];
                $_SESSION['reset_verified'] = false;
                unset($_SESSION['reset_attempts']);

                // ---------------------------------------------------------------
                // Send the code by email. PHP's mail() rarely works out of the box
                // on XAMPP/localhost without extra SMTP setup (e.g. PHPMailer +
                // Gmail SMTP).
                // ---------------------------------------------------------------
                $subject = "Your Password Reset Code";
                $body    = "Your password reset code is: " . $code . "\n\nThis code expires in 10 minutes.\n\nIf you didn't request this, you can ignore this email.";
                $headers = "From: no-reply@" . $_SERVER['HTTP_HOST'];

                @mail($email, $subject, $body, $headers);

            $code_sent = true;
            $msg = "If this email exists in our system, a 6-digit code has been sent.";
            $msg_type = "success";

        }else{
            // Same message shown whether or not the email exists, so people can't
            // use this form to check which emails are registered.
            $msg = "If this email exists in our system, a 6-digit code has been sent.";
            $msg_type = "success";
        }

        $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/adminlte.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    </head>
<body>

    <div class="d-flex align-items-center justify-content-center vh-100 bg-light">

        <div class="col-md-4">

        <div class="card shadow">

        <div class="card-header bg-primary text-white text-center">
        <h4>Forgot Password</h4>
        </div>

        <div class="card-body">

        <?php if($msg!=""){ ?>
        <div class="alert alert-<?= $msg_type=='success' ? 'success' : 'danger' ?>">
        <?= $msg ?>
        </div>
        <?php } ?>

        <?php if($code_sent){ ?>

        <div class="text-center mb-3">
        <a href="reset-password.php" class="btn btn-primary w-100">Enter Code &rarr;</a>
        </div>

        <?php } else { ?>

        <p class="text-muted">Enter the email address associated with your account and we'll send you a 6-digit code to reset your password.</p>

        <form method="post">

        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">

        <div class="mb-3">
        <label>Email Address</label>
        <input
        type="email"
        name="email"
        class="form-control"
        required>
        </div>

        <button
        name="send_code"
        class="btn btn-primary w-100">
        Send Reset Code
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