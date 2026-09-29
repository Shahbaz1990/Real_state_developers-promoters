<?php
require_once __DIR__ . '/includes/security.php';
session_start_secure();
require_once __DIR__ . '/config.php'; // safe even if already included elsewhere (require_once)
// Fetch logged-in user's details for the avatar/dropdown
$user = null;
if(isset($_SESSION['id'])){
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

$fullname = $user["fullname"] ?? ($_SESSION['fullname'] ?? 'Guest');
$role     = $user["role"] ?? 'admin'; // falls back to "admin" if there's no role column yet

// Avatar image: use the uploaded profile image if it exists on disk, otherwise fall back to the initial
$avatar_image = $user["profile_image"] ?? '';
$has_avatar_image = !empty($avatar_image) && file_exists($avatar_image);
?>
<style>
  .user-avatar-sm{
    width:34px; height:34px;
    border-radius:50%;
    background:#12213a;
    color:#fff;
    display:flex; align-items:center; justify-content:center;
    font-weight:700;
    font-size:.9rem;
    flex-shrink:0;
    overflow:hidden;
  }
  .user-avatar-sm img{
    width:100%;
    height:100%;
    object-fit:cover;
  }
  .user-info-inline{ line-height:1.15; }
  .user-info-inline .name{ font-weight:700; color:#12213a; font-size:.88rem; }
  .user-info-inline .role{ color:#6b7686; font-size:.78rem; text-transform:capitalize; }

  .app-header .nav-link.bell-link{ color:#12213a; position:relative; }
  .app-header .nav-link.bell-link:hover{ color:#1f6feb; }

  .user-dropdown-menu{
    min-width:220px;
    padding:.5rem;
    border-radius:.75rem;
    border:1px solid #e7e9ee;
    box-shadow:0 12px 30px rgba(18,33,58,.12);
  }
  .user-dropdown-menu .dropdown-item{
    display:flex;
    align-items:center;
    gap:.6rem;
    padding:.65rem .75rem;
    border-radius:.5rem;
    font-size:.9rem;
    color:#12213a;
  }
  .user-dropdown-menu .dropdown-item i{ font-size:1.05rem; color:#6b7686; }
  .user-dropdown-menu .dropdown-item:hover{
    background:#eaf1ff;
    color:#1f6feb;
  }
  .user-dropdown-menu .dropdown-item:hover i{ color:#1f6feb; }
  .user-dropdown-menu .dropdown-item.text-danger:hover{
    background:#fdeef0;
    color:#dc2626;
  }
  .user-dropdown-menu .dropdown-item.text-danger:hover i{ color:#dc2626; }

  .notif-dropdown-menu{
    min-width:300px;
    padding:0;
    border-radius:.75rem;
    border:1px solid #e7e9ee;
    box-shadow:0 12px 30px rgba(18,33,58,.12);
    overflow:hidden;
  }
  .notif-dropdown-menu .notif-header{
    background:#f7f8fa;
    padding:.85rem 1rem;
    font-weight:700;
    font-size:.92rem;
    color:#12213a;
    border-bottom:1px solid #e7e9ee;
  }
  .notif-dropdown-menu .notif-empty{
    padding:1.5rem 1rem;
    text-align:center;
    color:#6b7686;
    font-size:.88rem;
  }
  .notif-dropdown-menu .notif-item{
    display:block;
    padding:.75rem 1rem;
    border-bottom:1px solid #f2f3f5;
    font-size:.88rem;
    color:#12213a;
  }
  .notif-dropdown-menu .notif-item:hover{ background:#eaf1ff; color:#1f6feb; }
  .notif-dropdown-menu .notif-footer{
    text-align:center;
    padding:.75rem 1rem;
    border-top:1px solid #e7e9ee;
    font-size:.88rem;
    font-weight:600;
    color:#1f6feb;
  }
  .notif-dropdown-menu .notif-footer:hover{ background:#f7f8fa; }
</style>

<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#">
                    <i class="bi bi-list"></i>
                </a>
            </li>
        </ul>
        <ul class="navbar-nav ms-auto align-items-center gap-2">

            <li class="nav-item dropdown">
                <a class="nav-link bell-link" href="#" data-bs-toggle="dropdown" aria-label="Notifications">
                    <i class="bi bi-bell fs-5"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end notif-dropdown-menu">
                    <div class="notif-header">Notifications</div>

                    <!-- No notifications backend wired up yet — shown as an empty state for now.
                         Replace this with a real query/list once a notifications table exists. -->
                    <div class="notif-empty">No new notifications</div>

                    <a href="#notifications" class="notif-footer d-block text-decoration-none">
                        View All Notifications
                    </a>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown" href="#">
                    <span class="user-avatar-sm">
                        <?php if ($has_avatar_image): ?>
                            <img src="<?php echo htmlspecialchars($avatar_image); ?>" alt="<?php echo htmlspecialchars($fullname); ?>">
                        <?php else: ?>
                            <?php echo $user ? strtoupper(substr($fullname, 0, 1)) : '?'; ?>
                        <?php endif; ?>
                    </span>
                    <span class="user-info-inline d-none d-md-flex flex-column">
                        <span class="name"><?php echo htmlspecialchars(strtoupper($fullname)); ?></span>
                        <span class="role"><?php echo htmlspecialchars($role); ?></span>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end user-dropdown-menu">
                    <a class="dropdown-item" href="profile.php">
                        <i class="bi bi-person"></i> Manage Profile
                    </a>
                    <a class="dropdown-item text-danger" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </li>

        </ul>
    </div>
</nav>