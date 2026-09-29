<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /front/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Tatito Developers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container py-5">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>!</h1>
        <p>Email: <?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></p>
        <a href="/front/api/auth.php?action=logout" class="btn btn-danger">Logout</a>
    </div>
    
    <script>
        async function logout() {
            await fetch('/front/api/auth.php?action=logout', {
                method: 'POST'
            });
            window.location.href = '/front/index.php';
        }
    </script>
    
    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>