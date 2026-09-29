<?php
require_once 'auth_check.php';

// Page name comes from the placeholder links (e.g. coming-soon.php?p=Members)
$placeholder = ucwords(htmlspecialchars($_GET['p'] ?? 'Page', ENT_QUOTES, 'UTF-8'));
?>
<!DOCTYPE html>
<html>
    <head>
    <title><?php echo $placeholder; ?> — Coming Soon</title>
    <link rel="stylesheet" href="css/adminlte.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    </head>
    <style>
    .coming-soon-wrap{
        display:flex;
        flex-direction:column;
        align-items:center;
        justify-content:center;
        min-height:60vh;
        text-align:center;
    }
    .coming-soon-icon{
        width:96px;
        height:96px;
        border-radius:50%;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:2.5rem;
        background:linear-gradient(135deg,#5c4cc9,#765bc8);
        color:#fff;
        box-shadow:0 12px 30px rgba(92,76,201,.35);
        margin-bottom:1.5rem;
    }
    .coming-soon-title{ font-weight:800; color:#12213a; }
    .coming-soon-desc{ max-width:460px; color:#6b7686; }
    </style>
<body>
    <div class="app-wrapper">
    <?php include 'nav.php'; include 'sidebar.php'; ?>
      <main class="app-main">
        <div class="app-content-header"><div class="container-fluid"><div class="row"><div class="col-sm-6"><h1 class="mb-0 fs-3"><?php echo $placeholder; ?></h1></div><div class="col-sm-6"><nav aria-label="breadcrumb"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="dashboard.php">Home</a></li><li class="breadcrumb-item active" aria-current="page"><?php echo $placeholder; ?></li></ol></nav></div></div></div></div>
        <div class="app-content">
          <div class="container-fluid coming-soon-wrap">
            <div class="coming-soon-icon"><i class="bi bi-rocket-takeoff"></i></div>
            <h3 class="coming-soon-title mb-2"><?php echo $placeholder; ?></h3>
            <p class="coming-soon-desc me-2 mb-4">This section is under construction. We'll have it ready soon — check back shortly!</p>
            <a href="dashboard.php" class="btn btn-primary"><i class="bi bi-speedometer2 me-1"></i> Back to Dashboard</a>
          </div>
        </div>
      </main>
    </div>
    <?php include __DIR__ . '/includes/ui_footer.php'; ?>
</body>
</html>