<?php
// Fetch first active banner
$banner = [];

$sql = "SELECT * FROM banners
        WHERE status='Active'
        ORDER BY banner_order ASC
        LIMIT 1";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $banner = $result->fetch_assoc();
} else {
    // Default values if no banner found
    $banner = [
        'title' => 'India\'s Fastest-Growing Real Estate Platform',
        'subtitle' => 'Find your dream property today',
        'image' => 'assets/images/home-desktop-cover.jpg'
    ];
}

// Fallback so the page never notices if $hero isn't set upstream
if (!isset($hero) || !is_array($hero)) {
    $hero = [];
}
$hero['rating']  = $hero['rating']  ?? '4.7';
$hero['reviews'] = $hero['reviews'] ?? '18K reviews';

// Fixed tab set — plain text only, no icons/badges
$tabs = ['Buy', 'Rent', 'Commercial', 'PG/Co-living', 'Plots'];

$imagePath = "../backend/uploads/banners/" . $banner['image'];
?>
<style>
  .hero-banner{
    position:relative;
    background-image:url('<?php echo htmlspecialchars($imagePath); ?>');
    background-size:cover;
    background-position:center;
    padding:0;
    overflow:hidden;
  }
  
  .hero-banner .hero-image{
    width:100%;
    height:550px; 
    filter: brightness(60%);
    object-fit:cover;
  }
  .hero-banner .overlay{
    position:absolute;
    inset:0;
    background:linear-gradient(180deg, rgba(10,16,28,.55) 0%, rgba(10,16,28,.35) 55%, rgba(10,16,28,.65) 100%);
  }
  .hero-banner .hero-inner{ position:relative; z-index:2; }

  .rating-badge{
    display:inline-flex; align-items:center; gap:.4rem;
    background:rgba(255,255,255,.15);
    border:1px solid rgba(255,255,255,.35);
    color:#fff;
    border-radius:2rem;
    padding:.4rem 1rem;
    font-size:.85rem;
    backdrop-filter:blur(4px);
  }
  .rating-badge i{ color:#f5b942; }

  .hero-title{
    color:#fff;
    font-family:'Fraunces', serif;
    font-weight:600;
    font-size:clamp(1.8rem, 3.2vw, 2.6rem);
    margin-bottom:.75rem;
  }
  .hero-subtitle{
    color:#e7e9ee;
    font-size:1.05rem;
    margin-bottom:0;
  }
  .hero-subtitle .highlight{ color:#f5b942; font-weight:600; }
  .hero-subtitle .sep{
    display:inline-block; width:5px; height:5px; border-radius:50%;
    background:#e7e9ee; vertical-align:middle;
  }

  /* ============ Search panel — centered on the banner image ============ */
  .hero-panel-wrap{
    position:relative;
    z-index:3;
    margin-top:2.5rem;
  }
  .hero-panel{
    background:linear-gradient(135deg, rgba(76,55,196,.92) 0%, rgba(58,38,168,.92) 100%);
    border:1px solid rgba(255,255,255,.12);
    border-radius:1.1rem;
    box-shadow:0 20px 45px rgba(20,10,60,.35);
    padding:1.1rem 1.5rem 1.4rem;
    backdrop-filter:blur(6px);
  }

  .hero-tabs{
    display:flex;
    flex-wrap:wrap;
    gap:1.6rem;
    padding-bottom:0.1rem;
    margin-bottom:0.6rem;
  }
  .hero-tabs .tab-btn{
    background:transparent;
    border:none;
    border-bottom:2px solid transparent;
    color:rgba(255,255,255,.65);
    font-size:.95rem;
    font-weight:600;
    padding:.3rem .1rem;
    border-radius:0;
  }
  .hero-tabs .tab-btn:hover{ color:#fff; }
  .hero-tabs .tab-btn.active{
    background:transparent;
    color:#fff;
    border-bottom-color:#fff;
  }

  .hero-search .search-box{
    display:flex;
    align-items:center;
    gap:.75rem;
    flex-wrap:wrap;
  }
  .hero-search .search-input{
    flex:1 1 260px;
    display:flex;
    align-items:center;
    gap:.6rem;
    background:rgba(10,8,25,.55);
    border:1px solid rgba(255,255,255,.12);
    border-radius:2rem;
    padding:.85rem 1.3rem;
  }
  .hero-search .search-input i{ color:rgba(255,255,255,.55); }
  .hero-search .search-input input{
    border:none;
    background:transparent;
    outline:none;
    width:100%;
    font-size:.95rem;
    color:#fff;
  }
  .hero-search .search-input input::placeholder{ color:rgba(255,255,255,.5); }
  .hero-search .search-btn{
    background:linear-gradient(90deg, #7b3ff2 0%, #d6398a 100%);
    color:#fff;
    border:none;
    border-radius:2rem;
    padding:.85rem 2rem;
    font-weight:600;
    font-size:.95rem;
    flex:0 0 auto;
  }
  .hero-search .search-btn:hover{ filter:brightness(1.08); }

  @media (max-width: 767.98px){
    .hero-banner{ padding:3.5rem 0 3rem; }
    .hero-panel-wrap{ margin-top:1.5rem; }
    .hero-search .search-btn{ width:100%; }
  }
  .carousel-indicators{
    position:absolute;
    left:50%;
    bottom:8px;          
    transform:translateX(-50%);
    margin:0;
    display:flex;
    justify-content:center;
    align-items:center;
    gap:10px;
    z-index:20;
}

.carousel-indicators button{
    width:38px;
    height:4px;
    border:none;
    border-radius:50px;
    background:rgba(255,255,255,.45);
    opacity:1;
    margin:0;
    transition:.3s;
}

.carousel-indicators button.active {
    background-color: #fff;
    transform: scaleX(1.2);
}
</style>

<section class="hero-banner">

<?php
$carouselImages = [
    "assets/images/image1.avif",
    "assets/images/image2.avif",
    "assets/images/image3.avif"
];
?>

<div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">

    <!-- Slides -->
    <div class="carousel-inner">

        <?php foreach ($carouselImages as $index => $image): ?>
        <div class="carousel-item <?= $index === 0 ? 'active' : ''; ?>">

            <img src="<?= $image; ?>" class="hero-image d-block w-100" alt="Banner <?= $index + 1; ?>">

            <div class="carousel-overlay"></div>

            <div class="carousel-caption hero-content">

                <div class="container text-center">

                    <div class="rating-badge mx-auto mb-4">
                        <i class="bi bi-star-fill"></i>
                        <span>
                            <?= htmlspecialchars($hero['rating']); ?>
                            ·
                            <?= htmlspecialchars($hero['reviews']); ?>
                        </span>
                    </div>

                    <h1 class="hero-title">
                        <?= nl2br(htmlspecialchars($banner['title'])); ?>
                    </h1>

                    <p class="hero-subtitle">
                        <?= htmlspecialchars($banner['subtitle']); ?>
                    </p>

                    <div class="hero-panel-wrap mx-auto">

                        <div class="hero-panel">

                            <div class="hero-tabs">
                                <?php foreach ($tabs as $i => $label): ?>
                                    <button
                                        type="button"
                                        class="tab-btn <?= $i === 0 ? 'active' : ''; ?>"
                                        data-placeholder="<?= htmlspecialchars($label); ?> Property">
                                        <?= htmlspecialchars($label); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>

                            <div class="hero-search">
                                <div class="search-box">

                                    <div class="search-input">
                                        <i class="bi bi-search"></i>

                                        <input
                                            id="heroSearch"
                                            type="text"
                                            placeholder="Search city, locality, project, or builder...">
                                    </div>

                                    <button
                                        class="search-btn"
                                        onclick="searchProperty()">
                                        Search
                                    </button>

                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
        <?php endforeach; ?>

    </div>

    <!-- Previous -->
    <button class="carousel-control-prev"
            type="button"
            data-bs-target="#heroCarousel"
            data-bs-slide="prev">

        <span class="carousel-control-prev-icon"></span>

    </button>

    <!-- Next -->
    <button class="carousel-control-next"
            type="button"
            data-bs-target="#heroCarousel"
            data-bs-slide="next">

        <span class="carousel-control-next-icon"></span>

    </button>

    <!-- Indicators -->
    <div class="carousel-indicators">

        <?php foreach ($carouselImages as $index => $image): ?>

            <button
                type="button"
                data-bs-target="#heroCarousel"
                data-bs-slide-to="<?= $index; ?>"
                class="<?= $index === 0 ? 'active' : ''; ?>"
                aria-label="Slide <?= $index + 1; ?>">
            </button>

        <?php endforeach; ?>

    </div>

</div>

</section>
<script>
function searchProperty() {
    let keyword = document.getElementById("heroSearch").value.trim();

    if (keyword !== "") {
        window.location.href = "properties.php?search=" + encodeURIComponent(keyword);
    } else {
        window.location.href = "properties.php";
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const tabs = document.querySelectorAll(".tab-btn");
    const input = document.getElementById("heroSearch");

    tabs.forEach(tab => {
        tab.addEventListener("click", function () {
            tabs.forEach(btn => btn.classList.remove("active"));
            this.classList.add("active");
            input.placeholder = "Search " + this.dataset.placeholder;
        });
    });
});
</script>