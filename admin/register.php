<?php
require_once __DIR__ . '/includes/security.php';
session_start_secure();

require_once __DIR__ . '/config.php';

$msg = "";
$error = "";

if(isset($_POST['register'])){

    if(!verify_csrf()){
        $error = "Your session expired. Please try again.";
    } else {
        $fullname         = trim($_POST['fullname']);
        $username         = trim($_POST['username']);
        $mobile_number    = trim($_POST['mobile_number']);
        $password         = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);
        $role              = 'user'; // fixed server-side, never taken from form input

        if(empty($fullname) || empty($username) || empty($mobile_number) || empty($password) || empty($confirm_password)){
            $error = "All fields are required.";
        }
        elseif(!preg_match('/^[0-9]{10}$/', $mobile_number)){
            $error = "Please enter a valid 10-digit mobile number.";
        }
        elseif($password !== $confirm_password){
            $error = "Password and Confirm Password do not match.";
        }
        else{

            $check = $conn->prepare("SELECT id FROM users WHERE username=?");
            $check->bind_param("s",$username);
            $check->execute();
            $result = $check->get_result();

            if($result->num_rows>0){

                $error="Username already exists.";

            }else{

                $hash = password_hash($password,PASSWORD_DEFAULT);

                $stmt = $conn->prepare("INSERT INTO users(fullname,username,mobile_number,password,role) VALUES(?,?,?,?,?)");
                $stmt->bind_param("sssss",$fullname,$username,$mobile_number,$hash,$role);

                if($stmt->execute()){

                    $_SESSION['success']="Registration Successful. Please Login.";

                    header("Location: login.php");
                    exit();

                }else{

                    $error="Registration Failed.";

                }

            }

        }
    }

}
?>

<!-- HTML form below is UNCHANGED — no role field shown to the user -->

<!doctype html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Register</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

body{
    background:#e9ecef;
}

.register-box{
    width:380px;
    margin:70px auto;
}

.logo{
    text-align:center;
    font-size:50px;
    margin-bottom:20px;
    color:#444;
}

.card{
    border-radius:4px;
}

.form-control{
    height:46px;
}

.input-group-text{
    background:#fff;
}

</style>

</head>

<body>

<div class="register-box">

<div class="logo">
<b>Admin</b>LTE
</div>

<div class="card shadow-sm">

<div class="card-body">

<p class="text-center mb-4">
Register a new membership
</p>

<?php if($error!=""){ ?>

<div class="alert alert-danger">
<?php echo $error; ?>
</div>

<?php } ?>

<form method="post">

<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">

<div class="input-group mb-3">

<input
type="text"
name="fullname"
class="form-control"
placeholder="Full Name"
required>

<span class="input-group-text">
<i class="bi bi-person"></i>
</span>

</div>

<div class="input-group mb-3">

<input
type="text"
name="username"
class="form-control"
placeholder="Username"
required>

<span class="input-group-text">
<i class="bi bi-envelope"></i>
</span>

</div>

<div class="input-group mb-3">

<input
type="text"
name="mobile_number"
class="form-control"
placeholder="Mobile Number"
maxlength="10"
pattern="[0-9]{10}"
required>

<span class="input-group-text">
<i class="bi bi-telephone"></i>
</span>

</div>

<div class="input-group mb-3">

<input
type="password"
name="password"
class="form-control"
placeholder="Password"
required>

<span class="input-group-text">
<i class="bi bi-lock-fill"></i>
</span>

</div>

<div class="input-group mb-3">

<input
type="password"
name="confirm_password"
class="form-control"
placeholder="Confirm Password"
required>

<span class="input-group-text">
<i class="bi bi-lock-fill"></i>
</span>

</div>

<div class="row mb-3">

<div class="col-8">

<div class="form-check">

<input class="form-check-input" type="checkbox" required>

<label class="form-check-label">
I agree to the <a href="#">terms</a>
</label>

</div>

</div>

<div class="col-4">

<button class="btn btn-primary w-100" name="register">
Register
</button>

</div>

</div>

</form>

<div class="text-center">

<p>- OR -</p>

<a href="#" class="btn btn-primary w-100 mb-2">
<i class="bi bi-facebook me-2"></i>
Sign up using Facebook
</a>

<a href="#" class="btn btn-danger w-100">
<i class="bi bi-google me-2"></i>
Sign up using Google
</a>

</div>

<hr>

<a href="login.php">
I already have a membership
</a>

</div>

</div>

</div>

</body>

</html>