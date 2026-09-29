<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

// Database connection
$conn = new mysqli("localhost", "u424007618_Developers", "Tatitodevelopers@123", "u424007618_Developers");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

class AuthAPI {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function handleRequest() {
        $action = $_GET['action'] ?? '';
        
        switch($action) {
            case 'send-otp':
                $this->sendOTP();
                break;
            case 'verify-otp':
                $this->verifyOTP();
                break;
            case 'register':
                $this->register();
                break;
            case 'login':
                $this->login();
                break;
            case 'check-email':
                $this->checkEmail();
                break;
            case 'logout':
                $this->logout();
                break;
            case 'get-user':
                $this->getUser();
                break;
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    }
    
    private function checkEmail() {
        $input = json_decode(file_get_contents('php://input'), true);
        $email = $input['email'] ?? '';
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Valid email required']);
            return;
        }
        
        $stmt = $this->conn->prepare("SELECT id, email_verified, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $verified = false;
        $hasPassword = false;
        
        if ($row = $result->fetch_assoc()) {
            $verified = $row['email_verified'] == 1;
            $hasPassword = !empty($row['password']);
        }
        $stmt->close();
        
        echo json_encode([
            'success' => true,
            'exists' => $exists,
            'verified' => $verified,
            'has_password' => $hasPassword,
            'message' => $exists ? 'Email found' : 'Email not found'
        ]);
    }
    
    private function sendOTP() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Clear any existing registration data
    if (isset($_SESSION['register_data'])) {
        unset($_SESSION['register_data']);
    }
    
    error_log("=== SEND OTP START ===");
    error_log("Full input: " . print_r($input, true));
    
    $email = isset($input['email']) ? trim($input['email']) : '';
    $isRegister = isset($input['is_register']) && $input['is_register'] === true;
    $fullname = isset($input['fullname']) ? trim($input['fullname']) : '';
    $mobile = isset($input['mobile']) ? trim($input['mobile']) : '';
    $password = isset($input['password']) ? trim($input['password']) : '';
    
    error_log("Parsed data - Email: $email, Password length: " . strlen($password) . ", Register: " . ($isRegister ? 'true' : 'false'));
    error_log("Fullname: $fullname, Mobile: $mobile");
    
    // Only allow OTP for registration
    if (!$isRegister) {
        echo json_encode([
            'success' => false, 
            'message' => 'OTP is only required for registration.',
            'code' => 'LOGIN_WITH_PASSWORD'
        ]);
        return;
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Valid email required']);
        return;
    }
    
    if (empty($fullname)) {
        echo json_encode(['success' => false, 'message' => 'Full name is required']);
        return;
    }
    
    if (empty($password)) {
        error_log("ERROR: Password is empty");
        echo json_encode(['success' => false, 'message' => 'Password is required']);
        return;
    }
    
    if (strlen($password) < 6) {
        error_log("ERROR: Password too short - Length: " . strlen($password));
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        return;
    }
    
    // Check if email exists and is verified
    $stmt = $this->conn->prepare("SELECT id, email_verified FROM users WHERE username = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $userData = $result->fetch_assoc();
    $stmt->close();
    
    if ($exists && $userData['email_verified'] == 1) {
        echo json_encode([
            'success' => false, 
            'message' => 'Email already registered and verified. Please login.',
            'code' => 'ALREADY_VERIFIED'
        ]);
        return;
    }
    
    // Generate OTP
    $otp = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
    $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    error_log("Password hashed successfully");
    
    // Store OTP in session
    $_SESSION['otp_data'] = [
        'email' => $email,
        'otp' => $otp,
        'expires' => $expires,
        'is_register' => true
    ];
    
    // Store registration data in session
    $_SESSION['register_data'] = [
        'email' => $email,
        'fullname' => $fullname,
        'mobile' => $mobile,
        'password' => $hashedPassword
    ];
    error_log("Session data stored");
    
    // Store or update OTP in database
    if ($exists) {
        // Update existing user
        $stmt = $this->conn->prepare("UPDATE users SET fullname = ?, mobile_number = ?, otp_code = ?, otp_expires = ?, password = ? WHERE username = ?");
        $stmt->bind_param("sssss", $fullname, $mobile, $otp, $expires, $hashedPassword, $email);
    } else {
        // Create new user
        $stmt = $this->conn->prepare("INSERT INTO users (username, fullname, mobile_number, otp_code, otp_expires, email_verified, password, created_at) VALUES (?, ?, ?, ?, ?, 0, ?, NOW())");
        $stmt->bind_param("ssssss", $email, $fullname, $mobile, $otp, $expires, $hashedPassword);
    }
    
    if ($stmt->execute()) {
        error_log("OTP and password saved to database for: $email");
        error_log("=== SEND OTP SUCCESS ===");
        
        // Try to send email
        $sent = $this->sendOTPEmail($email, $otp, true);
        
        echo json_encode([
            'success' => true, 
            'message' => $sent ? 'OTP sent to your email' : 'OTP generated (email sending disabled)',
            'is_register' => true,
            'otp' => $otp // Remove in production
        ]);
    } else {
        error_log("Database error: " . $this->conn->error);
        error_log("=== SEND OTP FAILED ===");
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $this->conn->error]);
    }
    $stmt->close();
}
    
    private function sendOTPEmail($email, $otp, $isRegister = true) {
        $subject = "Your OTP for Tatito Developers - Registration";
        
        $message = "<html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; background: #f7f8fa; margin: 0; padding: 20px; }
                .container { max-width: 500px; margin: 0 auto; padding: 30px; background: #ffffff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { text-align: center; padding-bottom: 20px; border-bottom: 2px solid #1f6feb; }
                .header h2 { color: #12213a; margin: 0; }
                .header p { color: #6b7686; margin: 5px 0 0; }
                .otp-box { background: #f0f4ff; padding: 20px; text-align: center; font-size: 36px; letter-spacing: 15px; font-weight: bold; margin: 25px 0; border-radius: 8px; color: #1f6feb; }
                .footer { text-align: center; color: #6b7686; font-size: 12px; margin-top: 20px; border-top: 1px solid #e7e9ee; padding-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Tatito Developers</h2>
                    <p>Registration Verification</p>
                </div>
                <p>Hello,</p>
                <p>Your OTP for registration is:</p>
                <div class='otp-box'>{$otp}</div>
                <p>This OTP is valid for <strong>10 minutes</strong>.</p>
                <p>If you didn't request this, please ignore this email.</p>
                <div class='footer'>
                    <p>&copy; 2026 Tatito Developers. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Tatito Developers <noreply@tatitodevelopersandpromoters.com>" . "\r\n";
        $headers .= "Reply-To: support@tatitodevelopersandpromoters.com" . "\r\n";
        
        try {
            return mail($email, $subject, $message, $headers);
        } catch (Exception $e) {
            return false;
        }
    }
    
    private function verifyOTP() {
        $input = json_decode(file_get_contents('php://input'), true);
        $email = isset($input['email']) ? trim($input['email']) : '';
        $otp = isset($input['otp']) ? trim($input['otp']) : '';
        
        if (empty($email) || empty($otp)) {
            echo json_encode(['success' => false, 'message' => 'Email and OTP required']);
            return;
        }
        
        // Check session OTP first
        $sessionValid = false;
        if (isset($_SESSION['otp_data']) && $_SESSION['otp_data']['email'] === $email) {
            $otpData = $_SESSION['otp_data'];
            if (time() <= strtotime($otpData['expires']) && $otpData['otp'] === $otp) {
                $sessionValid = true;
            }
        }
        
        // Check database OTP
        $stmt = $this->conn->prepare("SELECT id, fullname, otp_code, otp_expires, email_verified, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            return;
        }
        
        if ($user['email_verified'] == 1) {
            // Already verified - just login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $user['fullname'];
            
            echo json_encode([
                'success' => true, 
                'message' => 'Email already verified. Logged in successfully.',
                'already_verified' => true,
                'user' => [
                    'id' => $user['id'],
                    'fullname' => $user['fullname'],
                    'email' => $email
                ]
            ]);
            return;
        }
        
        $now = date('Y-m-d H:i:s');
        $dbValid = ($user['otp_code'] == $otp && strtotime($user['otp_expires']) > strtotime($now));
        
        if ($sessionValid || $dbValid) {
            // Verify the user
            $stmt = $this->conn->prepare("UPDATE users SET email_verified = 1 WHERE id = ?");
            $stmt->bind_param("i", $user['id']);
            
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $user['fullname'];
                unset($_SESSION['otp_data']);
                
                // Check if we have registration data with password
                if (isset($_SESSION['register_data']) && $_SESSION['register_data']['email'] === $email) {
                    // Password is already stored in register_data and in database
                    error_log("OTP verified. Password already stored for: $email");
                }
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Email verified successfully! Please complete registration.',
                    'already_verified' => false,
                    'user' => [
                        'id' => $user['id'],
                        'fullname' => $user['fullname'],
                        'email' => $email
                    ]
                ]);
            } else {
                error_log("Verify OTP update error: " . $this->conn->error);
                echo json_encode(['success' => false, 'message' => 'Verification failed']);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired OTP']);
        }
    }
    
    private function register() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $fullname = isset($input['fullname']) ? trim($input['fullname']) : '';
        $email = isset($input['email']) ? trim($input['email']) : '';
        $mobile = isset($input['mobile']) ? trim($input['mobile']) : '';
        $password = isset($input['password']) ? trim($input['password']) : '';
        
        error_log("Registration completion - Email: $email, Fullname: $fullname");
        
        if (empty($fullname) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'All fields required']);
            return;
        }
        
        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
            return;
        }
        
        // Check if user exists
        $stmt = $this->conn->prepare("SELECT id, email_verified, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found. Please request OTP first.']);
            return;
        }
        
        if ($user['email_verified'] == 1) {
            echo json_encode([
                'success' => false, 
                'message' => 'Email already registered and verified. Please login.',
                'code' => 'ALREADY_VERIFIED'
            ]);
            return;
        }
        
        // Check if password already exists in database
        if (!empty($user['password'])) {
            error_log("Password already exists for user: $email");
            // User already has password, just verify email
            $stmt = $this->conn->prepare("UPDATE users SET email_verified = 1 WHERE id = ?");
            $stmt->bind_param("i", $user['id']);
            
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $fullname;
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Registration complete! Welcome to Tatito Developers!',
                    'user' => [
                        'id' => $user['id'],
                        'fullname' => $fullname,
                        'email' => $email
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to complete registration']);
            }
            $stmt->close();
            return;
        }
        
        // Hash the password if not already stored
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        error_log("Hashing password for: $email");
        
        // Update user with password and complete registration
        $stmt = $this->conn->prepare("UPDATE users SET fullname = ?, mobile_number = ?, password = ?, email_verified = 1 WHERE id = ?");
        $stmt->bind_param("sssi", $fullname, $mobile, $hashedPassword, $user['id']);
        
        if ($stmt->execute()) {
            error_log("Registration complete for: $email - User ID: " . $user['id']);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $fullname;
            
            // Clear registration data
            unset($_SESSION['register_data']);
            unset($_SESSION['otp_data']);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Registration complete! Welcome to Tatito Developers!',
                'user' => [
                    'id' => $user['id'],
                    'fullname' => $fullname,
                    'email' => $email
                ]
            ]);
        } else {
            error_log("Registration update failed: " . $this->conn->error);
            echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $this->conn->error]);
        }
        $stmt->close();
    }
    
    private function login() {
        $input = json_decode(file_get_contents('php://input'), true);
        $email = isset($input['email']) ? trim($input['email']) : '';
        $password = isset($input['password']) ? trim($input['password']) : '';
        
        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email and password required']);
            return;
        }
        
        $stmt = $this->conn->prepare("SELECT id, fullname, password, email_verified, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            return;
        }
        
        if ($user['email_verified'] == 0) {
            echo json_encode([
                'success' => false, 
                'message' => 'Please verify your email first. Please register.',
                'code' => 'NOT_VERIFIED'
            ]);
            return;
        }
        
        // Check if password exists
        if (empty($user['password'])) {
            error_log("Password missing for user: $email");
            echo json_encode([
                'success' => false, 
                'message' => 'Account setup incomplete. Please register again.',
                'code' => 'PASSWORD_MISSING'
            ]);
            return;
        }
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            error_log("Login successful for: $email");
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $user['fullname'];
            $_SESSION['user_role'] = $user['role'] ?? 'user';
            
            // Update last login
            $updateStmt = $this->conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->bind_param("i", $user['id']);
            $updateStmt->execute();
            $updateStmt->close();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'fullname' => $user['fullname'],
                    'email' => $email,
                    'role' => $user['role'] ?? 'user'
                ]
            ]);
        } else {
            error_log("Login failed for: $email - Invalid password");
            echo json_encode(['success' => false, 'message' => 'Invalid password']);
        }
    }
    
    private function logout() {
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    }
    
    private function getUser() {
        if (isset($_SESSION['user_id'])) {
            $stmt = $this->conn->prepare("SELECT id, fullname, username as email, mobile_number, profile_image, role, email_verified FROM users WHERE id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($user = $result->fetch_assoc()) {
                echo json_encode([
                    'success' => true,
                    'user' => $user
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'User not found']);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
        }
    }
}

// Initialize and handle request
$auth = new AuthAPI($conn);
$auth->handleRequest();
?>