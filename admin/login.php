<?php
require_once __DIR__ . '/includes/security.php';
session_start_secure();

require_once __DIR__ . '/config.php';

$error = "";

if(isset($_GET['timeout'])){
    $error = "Your session expired due to inactivity. Please log in again.";
}

if(isset($_GET['denied'])){
    $error = "You do not have permission to access the admin panel.";
}

if(isset($_GET['loggedout'])){
    set_flash('info', "You have been logged out.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Block the request before any work if the account is currently throttled
    if(login_throttle_blocked()){
        $error = "Too many failed login attempts. Please try again in a few minutes.";
    } elseif(!verify_csrf()){
        $error = "Your session expired. Please try again.";
    } else {

        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {

                // Admin panel is admin-only
                if (($row['role'] ?? 'user') !== 'admin') {
                    login_throttle_record_failure();
                    $error = "Invalid username or password.";
                } else {

                    session_regenerate_id(true);
                    login_throttle_reset();

                    $_SESSION['id'] = $row['id'];
                    $_SESSION['fullname'] = $row['fullname'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['role'] = $row['role'];
                    $_SESSION['last_activity'] = time();
                    csrf_token(); // fresh token for the new session

                    header("Location: dashboard.php");
                    exit();
                }

            } else {
                login_throttle_record_failure();
                $error = "Invalid username or password.";
            }

        } else {
            login_throttle_record_failure();
            $error = "Invalid username or password.";
        }
    }
}
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin Login</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
        <style>
body{
    background:#e9ecef;
    height:100vh;
}

.login-box{
    width:360px;
    margin:auto;
    padding-top:120px;
}

.logo{
    text-align:center;
    font-size:48px;
    margin-bottom:20px;
    color:#444;
}

.card{
    border-radius:3px;
}

.form-control{
    height:48px;
}

.input-group-text{
    background:#fff;
}

.btn-primary{
    height:38px;
}

.social-btn{
    width:100%;
}

</style>
    </head>
<body>

    <div class="login-box">

    <div class="logo">
    <b>Admin</b>Panel
    </div>

    <div class="card shadow-sm">

    <div class="card-body">

    <p class="text-center mb-4">
    Sign in to start your session
    </p>

    <?php if($error!=""){ ?>

    <div class="alert alert-danger">
    <?php echo htmlspecialchars($error); ?>
    </div>

    <?php } ?>

    <form method="post">

    <?php echo csrf_field(); ?>

    <div class="input-group mb-3">

    <input
    type="text"
    name="username"
    class="form-control"
    placeholder="Username"
    required
    autocomplete="username">

    <span class="input-group-text">
    <i class="bi bi-person"></i>
    </span>

    </div>

    <div class="input-group mb-3">

    <input
    type="password"
    name="password"
    class="form-control"
    placeholder="Password"
    required
    autocomplete="current-password">

    <span class="input-group-text">
    <i class="bi bi-lock-fill"></i>
    </span>

    </div>

    <div class="row mb-3">

    <div class="col-7">

    <div class="form-check">

    <input
    class="form-check-input"
    type="checkbox"
    id="remember">

    <label class="form-check-label" for="remember">
    Remember Me
    </label>

    </div>

    </div>

    <div class="col-5">

    <button class="btn btn-primary w-100" data-loading-text="Signing in…">
    Sign In
    </button>

    </div>

    </div>

    </form>

    <div class="text-center">

    <hr>

    <a href="forgot-password.php">I forgot my password</a>

    <br>

    <a href="register.php">Register a new membership</a>

    </div>

    </div>

    </div>

    </div>

<?php include __DIR__ . '/includes/ui_footer.php'; ?>

</body>

</html>
