<?php
require_once 'auth_check.php';

// Folder where the logo gets saved
$upload_dir = __DIR__ . '/uploads/logos/';
if(!is_dir($upload_dir)){
    mkdir($upload_dir, 0755, true);
}

// Pull any flash message set by a previous redirect, then clear it
$msg = $_SESSION['flash_msg'] ?? "";
unset($_SESSION['flash_msg']);

// Get existing settings
$settings = [
    'id'                      => null,
    'logo'                    => '',
    'site_title'              => 'Tatito Developers',
    'header_bg_color'         => '#141d4a',
    'nav_link_color'          => '#eef1fb',
    'login_btn_text'          => 'Login',
    'login_btn_text_color'    => '#ffffff',
    'login_btn_bg_color'      => 'transparent',
    'register_btn_text'       => 'Register',
    'register_btn_text_color' => '#12213a',
    'register_btn_bg_color'   => '#f0b429',
    'list_property_btn_text'  => 'List Property',
    'list_property_btn_bg_color' => '#f0b429',
];

// Check if header_settings table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'header_settings'");
if($tableCheck && $tableCheck->num_rows > 0) {
    $existingResult = $conn->query("SELECT * FROM header_settings ORDER BY id DESC LIMIT 1");
    if($existingResult && $existingResult->num_rows > 0){
        $settings = array_merge($settings, $existingResult->fetch_assoc());
    }
}

// Check if columns exist, if not, add them
$columnsToCheck = [
    'register_btn_text' => "ALTER TABLE header_settings ADD COLUMN register_btn_text VARCHAR(50) DEFAULT 'Register'",
    'register_btn_text_color' => "ALTER TABLE header_settings ADD COLUMN register_btn_text_color VARCHAR(20) DEFAULT '#12213a'",
    'register_btn_bg_color' => "ALTER TABLE header_settings ADD COLUMN register_btn_bg_color VARCHAR(20) DEFAULT '#f0b429'",
    'list_property_btn_text' => "ALTER TABLE header_settings ADD COLUMN list_property_btn_text VARCHAR(50) DEFAULT 'List Property'",
    'list_property_btn_bg_color' => "ALTER TABLE header_settings ADD COLUMN list_property_btn_bg_color VARCHAR(20) DEFAULT '#f0b429'",
];

foreach($columnsToCheck as $col => $alterQuery) {
    $check = $conn->query("SHOW COLUMNS FROM header_settings LIKE '$col'");
    if($check && $check->num_rows == 0) {
        $conn->query($alterQuery);
    }
}

// Save settings
if(isset($_POST['save'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: header-settings.php");
        exit;
    }

    $site_title              = $_POST['site_title'] ?? 'Tatito Developers';
    $header_bg_color         = $_POST['header_bg_color'] ?? '#141d4a';
    $nav_link_color          = $_POST['nav_link_color'] ?? '#eef1fb';
    $login_btn_text          = $_POST['login_btn_text'] ?? 'Login';
    $login_btn_text_color    = $_POST['login_btn_text_color'] ?? '#ffffff';
    $login_btn_bg_color      = isset($_POST['login_btn_bg_transparent']) 
        ? 'transparent' 
        : ($_POST['login_btn_bg_color'] ?? 'transparent');
    $register_btn_text       = $_POST['register_btn_text'] ?? 'Register';
    $register_btn_text_color = $_POST['register_btn_text_color'] ?? '#12213a';
    $register_btn_bg_color   = $_POST['register_btn_bg_color'] ?? '#f0b429';
    $list_property_btn_text  = $_POST['list_property_btn_text'] ?? 'List Property';
    $list_property_btn_bg_color = $_POST['list_property_btn_bg_color'] ?? '#f0b429';

    $logo_name = $settings['logo'] ?? '';

    // Handle logo upload
    if(isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK){
        $uploaded = secure_upload('logo', $upload_dir, 'logo_', ADMIN_ALLOWED_IMAGE_MIMES, ADMIN_ALLOWED_IMAGE_EXTS);
        if($uploaded === false){
            set_flash('error', "Invalid logo image. Allowed: jpg, jpeg, png, webp, gif.");
            header("Location: header-settings.php");
            exit;
        }
        $logo_name = $uploaded;

        // Remove old logo file
        if(!empty($settings['logo']) && file_exists($upload_dir . $settings['logo'])){
            unlink($upload_dir . $settings['logo']);
        }
    }

    // Check if table exists before inserting/updating
    $tableExists = $conn->query("SHOW TABLES LIKE 'header_settings'");
    if($tableExists && $tableExists->num_rows == 0) {
        // Create table if it doesn't exist
        $createTable = "
            CREATE TABLE IF NOT EXISTS header_settings (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                logo VARCHAR(255) DEFAULT NULL,
                site_title VARCHAR(255) DEFAULT 'Tatito Developers',
                header_bg_color VARCHAR(20) DEFAULT '#141d4a',
                nav_link_color VARCHAR(20) DEFAULT '#eef1fb',
                login_btn_text VARCHAR(50) DEFAULT 'Login',
                login_btn_text_color VARCHAR(20) DEFAULT '#ffffff',
                login_btn_bg_color VARCHAR(20) DEFAULT 'transparent',
                register_btn_text VARCHAR(50) DEFAULT 'Register',
                register_btn_text_color VARCHAR(20) DEFAULT '#12213a',
                register_btn_bg_color VARCHAR(20) DEFAULT '#f0b429',
                list_property_btn_text VARCHAR(50) DEFAULT 'List Property',
                list_property_btn_bg_color VARCHAR(20) DEFAULT '#f0b429',
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
        $conn->query($createTable);
    }

    if(empty($settings['id'])){
        // Insert new record
        $stmt = $conn->prepare("INSERT INTO header_settings 
            (logo, site_title, header_bg_color, nav_link_color, 
             login_btn_text, login_btn_text_color, login_btn_bg_color,
             register_btn_text, register_btn_text_color, register_btn_bg_color,
             list_property_btn_text, list_property_btn_bg_color)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param(
            "ssssssssssss",
            $logo_name, $site_title, $header_bg_color, $nav_link_color,
            $login_btn_text, $login_btn_text_color, $login_btn_bg_color,
            $register_btn_text, $register_btn_text_color, $register_btn_bg_color,
            $list_property_btn_text, $list_property_btn_bg_color
        );
    } else {
        // Update existing record
        $stmt = $conn->prepare("UPDATE header_settings SET 
            logo=?, site_title=?, header_bg_color=?, nav_link_color=?,
            login_btn_text=?, login_btn_text_color=?, login_btn_bg_color=?,
            register_btn_text=?, register_btn_text_color=?, register_btn_bg_color=?,
            list_property_btn_text=?, list_property_btn_bg_color=?
            WHERE id=?");
        $stmt->bind_param(
            "ssssssssssssi",
            $logo_name, $site_title, $header_bg_color, $nav_link_color,
            $login_btn_text, $login_btn_text_color, $login_btn_bg_color,
            $register_btn_text, $register_btn_text_color, $register_btn_bg_color,
            $list_property_btn_text, $list_property_btn_bg_color,
            $settings['id']
        );
    }

    if($stmt->execute()){
        set_flash('success', "Header Settings Updated Successfully");
    }else{
        set_flash('error', "Something went wrong. Please try again. (" . $conn->error . ")");
    }
    $stmt->close();

    header("Location: header-settings.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Header Settings</title>
    <link rel="stylesheet" href="css/adminlte.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root{
            --sy-navy-1: #0c1230;
            --sy-navy-2: #141d4a;
            --sy-navy-3: #1c2b63;
            --sy-gold: #f0b429;
            --sy-gold-dark: #d99b00;
            --sy-line: #e7e9ee;
        }
        .color-field{ display:flex; align-items:center; gap:.6rem; }
        .color-field input[type="color"]{ width:48px; height:38px; padding:2px; }
        .color-field.is-disabled input[type="color"]{ opacity:.45; pointer-events:none; }
        .transparent-toggle{ font-size:.8rem; color:#6c757d; display:flex; align-items:center; gap:.35rem; margin-top:.4rem; }

        /* Preview styles - Updated to match new header */
        .header-preview-shell{
            border-radius:.6rem;
            overflow:hidden;
            box-shadow:0 10px 30px rgba(18,33,58,.12);
            font-family:'Inter', sans-serif;
        }
        .hp-wrap{ display:flex; align-items:stretch; flex-wrap:wrap; }
        .hp-brand{
            background: var(--sy-navy-1);
            width:100px;
            flex-shrink:0;
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            padding:.6rem .4rem;
            text-align:center;
        }
        .hp-brand .ring{
            width:44px; height:44px; border-radius:50%;
            border:2px solid rgba(255,255,255,.35);
            background:rgba(255,255,255,.06);
            display:flex; align-items:center; justify-content:center;
            overflow:hidden;
        }
        .hp-brand .ring img{ width:78%; height:78%; object-fit:contain; }
        .hp-brand .ring .placeholder-logo{
            width:100%; height:100%; display:flex; align-items:center; justify-content:center;
            color:#fff; font-size:1.1rem;
        }
        .hp-brand .name{
            color:#fff; font-family:'Poppins', sans-serif; font-weight:700;
            font-size:.6rem; letter-spacing:.04em; text-transform:uppercase;
            margin-top:.35rem; line-height:1.2;
        }
        .hp-right{ flex:1; min-width:0; display:flex; flex-direction:column; }
        
        /* Topbar */
        .hp-topbar{
            color:rgba(255,255,255,.78);
            padding:.3rem .75rem;
            display:flex;
            align-items:center;
            gap:.6rem;
            overflow:hidden;
        }
        .hp-ticker{ flex:1; min-width:0; display:flex; gap:0; overflow:hidden; white-space:nowrap; }
        .hp-ticker-item{
            display:inline-flex; align-items:center; gap:.3rem;
            padding:0 .8rem; font-size:.72rem; color:rgba(255,255,255,.85);
            border-right:1px solid rgba(255,255,255,.15); flex-shrink:0;
        }
        .hp-ticker-item .city{ font-weight:700; color:#fff; }
        .hp-ticker-item .up{ color:#3ddc84; font-weight:600; }
        .hp-topbar-tools{
            display:flex; align-items:center; gap:.4rem; flex-shrink:0;
        }
        .hp-theme-toggle{
            width:28px; height:28px; border-radius:50%;
            border:1.5px solid rgba(255,255,255,.28);
            display:inline-flex; align-items:center; justify-content:center;
            color:#fff; font-size:.7rem; background:transparent;
        }
        .hp-wishlist{
            flex-shrink:0; width:28px; height:28px; border-radius:50%;
            border:1.5px solid rgba(255,255,255,.28);
            display:inline-flex; align-items:center; justify-content:center;
            color:#fff; font-size:.75rem; background:transparent;
        }
        .hp-font-size{
            display:flex; align-items:center;
            background:rgba(255,255,255,.08);
            border:1px solid rgba(255,255,255,.22);
            border-radius:999px; overflow:hidden;
        }
        .hp-font-size button{
            background:transparent; border:none; color:rgba(255,255,255,.85);
            font-weight:700; padding:.2rem .45rem; font-size:.6rem; line-height:1.4;
        }
        .hp-font-size button:not(:last-child){ border-right:1px solid rgba(255,255,255,.15); }
        .hp-lang-toggle{
            display:flex; align-items:center; gap:.25rem;
            background:rgba(255,255,255,.08);
            border:1px solid rgba(255,255,255,.22);
            border-radius:999px; color:#fff; font-size:.6rem; font-weight:600;
            padding:.22rem .6rem; white-space:nowrap;
        }
        .hp-lang-toggle i.bi-globe2{ font-size:.7rem; }

        /* Mainbar */
        .hp-mainbar{ padding:.55rem .9rem; }
        .hp-mainbar-inner{ display:flex; align-items:center; gap:.75rem; flex-wrap:wrap; }
        .hp-nav{ display:flex; align-items:center; gap:.15rem; flex-wrap:wrap; }
        .hp-nav a{
            text-decoration:none; font-size:.86rem; font-weight:500;
            padding:.4rem .7rem; border-radius:.3rem; white-space:nowrap;
        }
        .hp-nav a:hover{ color: var(--sy-gold); }
        .hp-actions{ display:flex; align-items:center; gap:.55rem; margin-left:auto; flex-wrap:wrap; }
        .hp-city{
            height:38px; display:flex; align-items:center; gap:.4rem;
            padding:0 .85rem; background:rgba(255,255,255,.08);
            border:1px solid rgba(255,255,255,.20); color:#fff; font-size:.82rem;
            border-radius:.3rem; white-space:nowrap;
        }
        .hp-city i{ color:#fff; }
        .hp-list-property{
            height:36px; display:inline-flex; align-items:center; gap:.3rem;
            padding:0 .9rem; border-radius:2rem; font-size:.8rem; font-weight:700;
            border:none; text-decoration:none;
        }
        .hp-login{
            height:36px; display:inline-flex; align-items:center; gap:.3rem;
            padding:0 .9rem; border-radius:2rem; font-size:.8rem; font-weight:600;
            border:1.5px solid; text-decoration:none;
        }
        .hp-register{
            height:36px; display:inline-flex; align-items:center; gap:.3rem;
            padding:0 .9rem; border-radius:2rem; font-size:.8rem; font-weight:700;
            border:none; text-decoration:none;
        }
        .preview-note{
            font-size:.78rem; color:#6c757d; margin:.5rem 0 1.5rem;
        }
        /* Dark mode preview support */
        .header-preview-shell.dark-preview .hp-topbar,
        .header-preview-shell.dark-preview .hp-mainbar {
            background: #161b22 !important;
        }
        .header-preview-shell.dark-preview .hp-brand {
            background: #0d1117 !important;
        }
        .header-preview-shell.dark-preview .hp-nav a {
            color: #8b949e !important;
        }
        .header-preview-shell.dark-preview .hp-nav a:hover {
            color: var(--sy-gold) !important;
        }
    </style>
</head>
<body>

<div class="app-wrapper">
    <?php include 'nav.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main class="app-main">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-11">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4>Header Settings</h4>
                        </div>
                        <div class="card-body">
                            <!-- Live preview -->
                            <label class="fw-bold mb-2">Preview</label>
                            <div class="header-preview-shell mb-2" id="previewShell">
                                <div class="hp-wrap">
                                    <!-- Brand Panel -->
                                    <div class="hp-brand" id="previewBrand" style="background:<?= htmlspecialchars($settings['header_bg_color'] ?? '#141d4a') ?>;">
                                        <span class="ring">
                                            <?php if(!empty($settings['logo'])){ ?>
                                                <img id="previewLogo" src="uploads/logos/<?= htmlspecialchars($settings['logo']) ?>" alt="logo">
                                            <?php }else{ ?>
                                                <span id="previewLogo" class="placeholder-logo"><i class="bi bi-building"></i></span>
                                            <?php } ?>
                                        </span>
                                        <span class="name" id="previewSiteTitleBrand"><?= htmlspecialchars($settings['site_title'] ?? 'Tatito Developers') ?></span>
                                    </div>

                                    <div class="hp-right">
                                        <!-- Topbar -->
                                        <div class="hp-topbar" id="previewTopbar" style="background:<?= htmlspecialchars($settings['header_bg_color'] ?? '#141d4a') ?>;">
                                            <div class="hp-ticker">
                                                <div class="hp-ticker-item"><span class="city">Hyderabad</span><span>₹15.3K/sqft</span><span class="up"><i class="bi bi-arrow-up-short"></i>+2.4%</span></div>
                                                <div class="hp-ticker-item"><span class="city">Kolkata</span><span>₹15.3K/sqft</span><span class="up"><i class="bi bi-arrow-up-short"></i>+2.4%</span></div>
                                                <div class="hp-ticker-item"><span class="city">Mumbai</span><span>₹15.3K/sqft</span><span class="up"><i class="bi bi-arrow-up-short"></i>+2.4%</span></div>
                                            </div>
                                            <div class="hp-topbar-tools">
                                                <button class="hp-theme-toggle"><i class="bi bi-moon-fill"></i></button>
                                                <div class="hp-font-size">
                                                    <button>A-</button>
                                                    <button class="active">A</button>
                                                    <button>A+</button>
                                                </div>
                                                <span class="hp-lang-toggle"><i class="bi bi-globe2"></i> EN</span>
                                                <span class="hp-wishlist"><i class="bi bi-heart-fill"></i></span>
                                            </div>
                                        </div>

                                        <!-- Mainbar -->
                                        <div class="hp-mainbar" id="previewMainbar" style="background:<?= htmlspecialchars($settings['header_bg_color'] ?? '#141d4a') ?>;">
                                            <div class="hp-mainbar-inner">
                                                <nav class="hp-nav" id="previewNav" style="--nav-link-color: <?= htmlspecialchars($settings['nav_link_color'] ?? '#eef1fb') ?>;">
                                                    <a href="#">Buy</a>
                                                    <a href="#">Rent</a>
                                                    <a href="#">Interiors</a>
                                                    <a href="#">Vendors</a>
                                                    <a href="#">Services</a>
                                                    <a href="#">Finance</a>
                                                    <a href="#">Blog</a>
                                                    <a href="#">Hotels</a>
                                                </nav>
                                                <div class="hp-actions">
                                                    <span class="hp-city"><i class="bi bi-geo-alt"></i> Search</span>
                                                    <a href="#" class="hp-list-property" id="previewListPropertyBtn"
                                                       style="background:<?= htmlspecialchars($settings['list_property_btn_bg_color'] ?? '#f0b429') ?>; color:<?= htmlspecialchars($settings['list_property_btn_text_color'] ?? '#12213a') ?>;">
                                                        <i class="bi bi-plus-circle"></i> <span id="previewListPropertyText"><?= htmlspecialchars($settings['list_property_btn_text'] ?? 'List Property') ?></span>
                                                    </a>
                                                    <a href="#" class="hp-login" id="previewLoginBtn"
                                                       style="border-color:<?= htmlspecialchars($settings['login_btn_text_color'] ?? '#ffffff') ?>; color:<?= htmlspecialchars($settings['login_btn_text_color'] ?? '#ffffff') ?>; background:<?= htmlspecialchars($settings['login_btn_bg_color'] ?? 'transparent') ?>;">
                                                        <i class="bi bi-box-arrow-in-right"></i> <span id="previewLoginText"><?= htmlspecialchars($settings['login_btn_text'] ?? 'Login') ?></span>
                                                    </a>
                                                    <a href="#" class="hp-register" id="previewRegisterBtn"
                                                       style="background:<?= htmlspecialchars($settings['register_btn_bg_color'] ?? '#f0b429') ?>; color:<?= htmlspecialchars($settings['register_btn_text_color'] ?? '#12213a') ?>;">
                                                        <i class="bi bi-person-plus"></i> <span id="previewRegisterText"><?= htmlspecialchars($settings['register_btn_text'] ?? 'Register') ?></span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form method="post" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                
                                <div class="mb-3">
                                    <label>Site Title</label>
                                    <input type="text" name="site_title" id="site_title" class="form-control" 
                                           value="<?= htmlspecialchars($settings['site_title'] ?? 'Tatito Developers') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label>Logo</label>
                                    <input type="file" name="logo" class="form-control" accept="image/*">
                                    <?php if(!empty($settings['logo'])){ ?>
                                        <small class="text-muted">Leave empty to keep the current logo.</small>
                                    <?php } ?>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Header Background Color</label>
                                        <div class="color-field">
                                            <input type="color" name="header_bg_color" id="header_bg_color" 
                                                   value="<?= htmlspecialchars($settings['header_bg_color'] ?? '#141d4a') ?>">
                                            <span id="header_bg_color_hex" class="text-muted"><?= htmlspecialchars($settings['header_bg_color'] ?? '#141d4a') ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>Nav Link Color</label>
                                        <div class="color-field">
                                            <input type="color" name="nav_link_color" id="nav_link_color" 
                                                   value="<?= htmlspecialchars($settings['nav_link_color'] ?? '#eef1fb') ?>">
                                            <span id="nav_link_color_hex" class="text-muted"><?= htmlspecialchars($settings['nav_link_color'] ?? '#eef1fb') ?></span>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- Login Button Settings -->
                                <label class="fw-bold mb-2">Login Button</label>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label>Button Text</label>
                                        <input type="text" name="login_btn_text" id="login_btn_text" class="form-control" 
                                               value="<?= htmlspecialchars($settings['login_btn_text'] ?? 'Login') ?>" required>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Text / Border Color</label>
                                        <div class="color-field">
                                            <input type="color" name="login_btn_text_color" id="login_btn_text_color" 
                                                   value="<?= htmlspecialchars($settings['login_btn_text_color'] ?? '#ffffff') ?>">
                                            <span id="login_btn_text_color_hex" class="text-muted"><?= htmlspecialchars($settings['login_btn_text_color'] ?? '#ffffff') ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Background Color</label>
                                        <div class="color-field <?= ($settings['login_btn_bg_color'] ?? '') === 'transparent' ? 'is-disabled' : '' ?>" id="login_bg_color_field">
                                            <input type="color" name="login_btn_bg_color" id="login_btn_bg_color" 
                                                   value="<?= htmlspecialchars(($settings['login_btn_bg_color'] ?? '') === 'transparent' ? '#141d4a' : ($settings['login_btn_bg_color'] ?? '#141d4a')) ?>">
                                            <span id="login_btn_bg_color_hex" class="text-muted"><?= htmlspecialchars($settings['login_btn_bg_color'] ?? 'transparent') ?></span>
                                        </div>
                                        <label class="transparent-toggle">
                                            <input type="checkbox" id="login_btn_bg_transparent" name="login_btn_bg_transparent" value="1" <?= ($settings['login_btn_bg_color'] ?? '') === 'transparent' ? 'checked' : '' ?>>
                                            Transparent
                                        </label>
                                    </div>
                                </div>

                                <hr>

                                <!-- Register Button Settings -->
                                <label class="fw-bold mb-2">Register Button</label>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label>Button Text</label>
                                        <input type="text" name="register_btn_text" id="register_btn_text" class="form-control" 
                                               value="<?= htmlspecialchars($settings['register_btn_text'] ?? 'Register') ?>" required>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Text Color</label>
                                        <div class="color-field">
                                            <input type="color" name="register_btn_text_color" id="register_btn_text_color" 
                                                   value="<?= htmlspecialchars($settings['register_btn_text_color'] ?? '#12213a') ?>">
                                            <span id="register_btn_text_color_hex" class="text-muted"><?= htmlspecialchars($settings['register_btn_text_color'] ?? '#12213a') ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label>Background Color</label>
                                        <div class="color-field">
                                            <input type="color" name="register_btn_bg_color" id="register_btn_bg_color" 
                                                   value="<?= htmlspecialchars($settings['register_btn_bg_color'] ?? '#f0b429') ?>">
                                            <span id="register_btn_bg_color_hex" class="text-muted"><?= htmlspecialchars($settings['register_btn_bg_color'] ?? '#f0b429') ?></span>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- List Property Button Settings (NEW) -->
                                <label class="fw-bold mb-2">List Property Button</label>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Button Text</label>
                                        <input type="text" name="list_property_btn_text" id="list_property_btn_text" class="form-control" 
                                               value="<?= htmlspecialchars($settings['list_property_btn_text'] ?? 'List Property') ?>" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>Background Color</label>
                                        <div class="color-field">
                                            <input type="color" name="list_property_btn_bg_color" id="list_property_btn_bg_color" 
                                                   value="<?= htmlspecialchars($settings['list_property_btn_bg_color'] ?? '#f0b429') ?>">
                                            <span id="list_property_btn_bg_color_hex" class="text-muted"><?= htmlspecialchars($settings['list_property_btn_bg_color'] ?? '#f0b429') ?></span>
                                        </div>
                                    </div>
                                </div>

                                <button name="save" class="btn btn-primary mt-2">Save Header Settings</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/includes/ui_footer.php'; ?>

<script>
// Live preview update
function bindColor(inputId, hexId, onChange){
    const input = document.getElementById(inputId);
    const hexLabel = document.getElementById(hexId);
    if(input && hexLabel) {
        input.addEventListener('input', function(){
            hexLabel.textContent = this.value;
            if(onChange) onChange(this.value);
        });
    }
}

const previewTopbar = document.getElementById('previewTopbar');
const previewMainbar = document.getElementById('previewMainbar');
const previewBrand = document.getElementById('previewBrand');
const previewNav = document.getElementById('previewNav');
const previewLoginBtn = document.getElementById('previewLoginBtn');
const previewRegisterBtn = document.getElementById('previewRegisterBtn');
const previewListPropertyBtn = document.getElementById('previewListPropertyBtn');
const previewSiteTitleBrand = document.getElementById('previewSiteTitleBrand');
const previewLoginText = document.getElementById('previewLoginText');
const previewRegisterText = document.getElementById('previewRegisterText');
const previewListPropertyText = document.getElementById('previewListPropertyText');

bindColor('header_bg_color', 'header_bg_color_hex', function(val){
    if(previewTopbar) previewTopbar.style.background = val;
    if(previewMainbar) previewMainbar.style.background = val;
    if(previewBrand) previewBrand.style.background = val;
});
bindColor('nav_link_color', 'nav_link_color_hex', function(val){
    if(previewNav) previewNav.style.setProperty('--nav-link-color', val);
});
bindColor('login_btn_text_color', 'login_btn_text_color_hex', function(val){
    if(previewLoginBtn) {
        previewLoginBtn.style.color = val;
        previewLoginBtn.style.borderColor = val;
    }
});
bindColor('login_btn_bg_color', 'login_btn_bg_color_hex', function(val){
    if(previewLoginBtn) previewLoginBtn.style.background = val;
});
bindColor('register_btn_text_color', 'register_btn_text_color_hex', function(val){
    if(previewRegisterBtn) previewRegisterBtn.style.color = val;
});
bindColor('register_btn_bg_color', 'register_btn_bg_color_hex', function(val){
    if(previewRegisterBtn) previewRegisterBtn.style.background = val;
});
bindColor('list_property_btn_bg_color', 'list_property_btn_bg_color_hex', function(val){
    if(previewListPropertyBtn) previewListPropertyBtn.style.background = val;
});

// Transparent toggle
const transparentCheckbox = document.getElementById('login_btn_bg_transparent');
const loginBgField = document.getElementById('login_bg_color_field');
const loginBgHexLabel = document.getElementById('login_btn_bg_color_hex');
const loginBgInput = document.getElementById('login_btn_bg_color');

if(transparentCheckbox) {
    transparentCheckbox.addEventListener('change', function(){
        if(this.checked){
            if(previewLoginBtn) previewLoginBtn.style.background = 'transparent';
            if(loginBgHexLabel) loginBgHexLabel.textContent = 'transparent';
            if(loginBgField) loginBgField.classList.add('is-disabled');
        } else {
            if(previewLoginBtn && loginBgInput) previewLoginBtn.style.background = loginBgInput.value;
            if(loginBgHexLabel && loginBgInput) loginBgHexLabel.textContent = loginBgInput.value;
            if(loginBgField) loginBgField.classList.remove('is-disabled');
        }
    });
}

// Text inputs
const siteTitleInput = document.getElementById('site_title');
if(siteTitleInput) {
    siteTitleInput.addEventListener('input', function(){
        if(previewSiteTitleBrand) previewSiteTitleBrand.textContent = this.value;
    });
}

const loginTextInput = document.getElementById('login_btn_text');
if(loginTextInput) {
    loginTextInput.addEventListener('input', function(){
        if(previewLoginText) previewLoginText.textContent = this.value;
    });
}

const registerTextInput = document.getElementById('register_btn_text');
if(registerTextInput) {
    registerTextInput.addEventListener('input', function(){
        if(previewRegisterText) previewRegisterText.textContent = this.value;
    });
}

const listPropertyTextInput = document.getElementById('list_property_btn_text');
if(listPropertyTextInput) {
    listPropertyTextInput.addEventListener('input', function(){
        if(previewListPropertyText) previewListPropertyText.textContent = this.value;
    });
}
</script>
</body>
</html>