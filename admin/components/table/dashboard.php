<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html>
    <head>
      <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/adminlte.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    </head>
    <style>
   .small-box{
    position:relative;
    overflow:hidden;
    min-height:160px;
    border-radius:.5rem;
}

.small-box>.inner{
    position:relative;
    z-index:10;
    padding:28px 18px;
}

.small-box h3{
    margin:0 0 8px;
    font:700 1.25rem/1.1 sans-serif;
}

.small-box p{margin:0;}

@media (max-width:991.98px){
    .small-box{min-height:145px;}
    .small-box>.inner{padding:22px 16px;}
}

@media (max-width:575.98px){
    .small-box{min-height:130px;}
    .small-box>.inner{padding:18px 15px;}
    .small-box h3{font-size:1.15rem;}
    .small-box p{font-size:.9rem;}
}

.small-box::before,
.small-box::after{
    content:"";
    position:absolute;
    inset:auto 0 0;
    height:55px;
}

.small-box::before{
    background:rgba(255,255,255,.14);
    clip-path:polygon(0 70%,15% 50%,35% 75%,50% 55%,65% 70%,85% 50%,100% 65%,100% 100%,0 100%);
}

.small-box::after{
    background:rgba(255,255,255,.22);
    clip-path:polygon(0 85%,15% 65%,35% 80%,50% 60%,65% 75%,85% 60%,100% 70%,100% 100%,0 100%);
}

.box-purple{background:linear-gradient(135deg,#5c4cc9,#765bc8);}
.box-blue{background:linear-gradient(135deg,#3fa1e4,#54b2ea);}
.box-pink{background:linear-gradient(135deg,#bd4f9e,#d64f8c);}
.box-orange{background:linear-gradient(135deg,#ff7c44,#ffa53d);}
    </style>
<body>
    <div class="app-wrapper">
    <?php include 'nav.php'; include 'sidebar.php'; ?>        
      <main class="app-main">
        <div class="app-content-header"><div class="container-fluid"><div class="row"><div class="col-sm-6"><h1 class="mb-0 fs-3">Small Box</h1></div><div class="col-sm-6"><nav aria-label="breadcrumb"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item active" aria-current="page">Small Box</li></ol></nav></div></div></div></div>
        <div class="app-content">
          <div class="container-fluid">
            <h5 class="mb-2">Welcome <?php echo $_SESSION['fullname']; ?> <span class="badge bg-secondary"><?php echo ucfirst($_SESSION['role'] ?? 'user'); ?></span></h5>
            <div class="row g-3">
              <div class="col-12 col-sm-6 col-xl-3"><div class="small-box box-purple text-white"><div class="inner"><h3>150</h3><p>New Orders</p></div></div></div>
              <div class="col-12 col-sm-6 col-xl-3"><div class="small-box box-blue text-white"><div class="inner"><h3>53<sup class="fs-8">%</sup></h3><p>Bounce Rate</p></div></div></div>
              <div class="col-12 col-sm-6 col-xl-3"><div class="small-box box-pink text-white"><div class="inner"><h3>44</h3><p>User Registrations</p></div></div></div>
              <div class="col-12 col-sm-6 col-xl-3"><div class="small-box box-orange text-white"><div class="inner"><h3>65</h3><p>Unique Visitors</p></div></div></div>
            </div>
          </div>
        </div>
      </main>
    </div>
    <script src="js/adminlte.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
