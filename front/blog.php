<?php
require_once __DIR__ . '/includes/config.php';

// Fetch all active blogs
$blogsQuery = mysqli_query($conn, "
    SELECT *
    FROM blogs
    WHERE status='Active'
    ORDER BY blog_order ASC, created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Blog — <?php echo htmlspecialchars($site['name']); ?></title>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
<!-- Site styles -->
<link rel="stylesheet" href="assets/css/style.css">

<style>
  /* ============================================
     DARK MODE SUPPORT - Blog Page
     ============================================ */
  
  /* Hero Section */
  body.dark-mode .blog-hero {
    background: linear-gradient(135deg, #0a0f1a 0%, #0d1a2e 150%);
    border-bottom: 1px solid #30363d;
  }
  body.dark-mode .blog-hero h1 {
    color: #f0f6fc;
  }
  body.dark-mode .blog-hero p {
    color: #8b949e;
  }
  body.dark-mode .blog-hero .eyebrow {
    color: #8b949e;
  }

  /* Blog Cards - Dark Mode */
  body.dark-mode .blog-card {
    background: #161b22;
    border-color: #30363d;
    box-shadow: 0 8px 24px rgba(0,0,0,0.3);
  }
  body.dark-mode .blog-card:hover {
    border-color: #c9a84c;
    box-shadow: 0 12px 36px rgba(201, 168, 76, 0.1);
  }
  body.dark-mode .blog-card h3 {
    color: #f0f6fc;
  }
  body.dark-mode .blog-card p.excerpt {
    color: #8b949e;
  }
  body.dark-mode .blog-card .author-name {
    color: #f0f6fc;
  }
  body.dark-mode .blog-card .post-meta {
    color: #8b949e;
  }
  body.dark-mode .blog-card .action-btn {
    background: #21262d;
    border-color: #30363d;
    color: #8b949e;
  }
  body.dark-mode .blog-card .action-btn:hover {
    background: #30363d;
    color: #f0f6fc;
  }
  body.dark-mode .blog-card .action-btn.liked {
    background: rgba(224, 36, 94, 0.2);
    border-color: #e0245e;
    color: #e0245e;
  }
  body.dark-mode .blog-card .share-btn {
    background: #21262d;
    border-color: #30363d;
    color: #8b949e;
  }
  body.dark-mode .blog-card .share-btn:hover {
    background: #c9a84c;
    border-color: #c9a84c;
    color: #0a0f1a;
  }
  body.dark-mode .blog-card .bookmark-icon {
    color: #8b949e;
  }
  body.dark-mode .blog-card .bookmark-icon:hover {
    color: #c9a84c;
  }
  body.dark-mode .blog-card .bookmark-icon.bookmarked {
    color: #c9a84c;
  }
  body.dark-mode .blog-card .avatar {
    background: linear-gradient(135deg, #c9a84c, #f0b429);
    color: #0a0f1a;
  }

  /* Blog Grid Container - Dark Mode */
  body.dark-mode .blog-grid {
    background: transparent;
  }

  /* Section - Dark Mode */
  body.dark-mode .section {
    background: #0d1117;
  }

  /* Body - Dark Mode */
  body.dark-mode {
    background-color: #0d1117;
  }

  /* ============================================
     ORIGINAL STYLES (Light Mode)
     ============================================ */
  .blog-hero{
    background:linear-gradient(135deg,#12213a 0%,#1f6feb 150%);
    color:#fff;
    padding:3.5rem 0 3rem;
    text-align:center;
    transition: background 0.3s ease, border-color 0.3s ease;
  }
  .blog-hero .eyebrow{ 
    color:#cfe0ff; 
    transition: color 0.3s ease;
  }
  .blog-hero h1{
    font-size:2.3rem;
    font-weight:800;
    margin:.5rem 0 .75rem;
    color:#fff;
    transition: color 0.3s ease;
  }
  .blog-hero p{
    color:#d7e0ee;
    max-width:560px;
    margin:0 auto;
    font-size:1rem;
    transition: color 0.3s ease;
  }

  .section {
    padding: 3rem 0;
    transition: background 0.3s ease;
  }

  /* Blog Grid */
  .blog-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
  }

  /* Blog Card - Light Mode */
  .blog-card {
    display: flex;
    flex-direction: row;
    text-decoration: none;
    color: inherit;
    background: #ffffff;
    border-radius: 1.25rem;
    overflow: hidden;
    border: 1px solid #e7e9ee;
    box-shadow: 0 4px 16px rgba(18,33,58,0.06);
    transition: all 0.3s ease;
    padding: 1.5rem;
    gap: 1.5rem;
  }

  .blog-card:hover {
    border-color: #c9a84c;
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(18,33,58,0.1);
    text-decoration: none;
    color: inherit;
  }

  /* Left Column (Content) */
  .blog-card .body {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  /* Header: Avatar, Author, Date, Bookmark */
  .blog-card .card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
  }
  .blog-card .author-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  .blog-card .avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7b61ff, #6a5acd);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 0.9rem;
    flex-shrink: 0;
  }
  .blog-card .author-details {
    font-size: 0.85rem;
    display: flex;
    flex-direction: column;
  }
  .blog-card .author-name { 
    color: #12213a; 
    font-weight: 600; 
    transition: color 0.3s ease;
  }
  .blog-card .post-meta { 
    color: #6b7686; 
    font-size: 0.8rem;
    transition: color 0.3s ease;
  }

  .blog-card .card-actions-top {
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  .blog-card .bookmark-icon { 
    color: #6b7686; 
    cursor: pointer; 
    font-size: 1.3rem; 
    transition: color 0.2s ease; 
  }
  .blog-card .bookmark-icon:hover { 
    color: #c9a84c; 
  }
  .blog-card .bookmark-icon.bookmarked { 
    color: #c9a84c; 
  }

  /* Content */
  .blog-card h3 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #12213a;
    margin-bottom: 0.75rem;
    line-height: 1.3;
    font-family: 'Fraunces', serif;
    transition: color 0.3s ease;
  }
  .blog-card p.excerpt {
    color: #6b7686;
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 1.5rem;
    flex-grow: 1;
    transition: color 0.3s ease;
  }

  /* Bottom Actions (Like, Comment, Share) */
  .blog-card .card-actions-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
  }
  .blog-card .action-buttons {
    display: flex;
    gap: 0.75rem;
  }
  .blog-card .action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: #f7f8fa;
    color: #6b7686;
    border: 1px solid #e7e9ee;
    padding: 0.4rem 0.8rem;
    border-radius: 999px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .blog-card .action-btn:hover {
    background: #e7e9ee;
    color: #12213a;
  }
  .blog-card .action-btn.liked {
    background: #ffe3ea;
    border-color: #e0245e;
    color: #e0245e;
  }
  .blog-card .action-btn i { 
    font-size: 1rem; 
  }

  .blog-card .share-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #f7f8fa;
    border: 1px solid #e7e9ee;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7686;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .blog-card .share-btn:hover {
    background: #c9a84c;
    color: #fff;
    border-color: #c9a84c;
  }

  /* Right Column (Image) */
  .blog-card .thumb {
    flex: 0 0 45%;
    background-size: cover;
    background-position: center;
    border-radius: 1rem;
    min-height: 250px;
  }

  /* ============================================
     SHARE MODAL STYLES
     ============================================ */
  .share-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  .share-modal-overlay.active {
    display: flex;
    opacity: 1;
  }
  .share-modal {
    background: #ffffff;
    border-radius: 1.25rem;
    width: 90%;
    max-width: 420px;
    padding: 1.8rem 2rem 2rem;
    position: relative;
    transform: translateY(10px);
    transition: transform 0.3s ease;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
  }
  .share-modal-overlay.active .share-modal {
    transform: translateY(0);
  }
  .share-modal .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.2rem;
  }
  .share-modal .modal-header h5 {
    font-weight: 700;
    font-size: 1.1rem;
    margin: 0;
    color: #12213a;
  }
  .share-modal .close-modal {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #6b7686;
    cursor: pointer;
    transition: color 0.2s;
  }
  .share-modal .close-modal:hover {
    color: #12213a;
  }
  .share-modal p {
    color: #6b7686;
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
  }
  .share-modal .social-icons {
    display: flex;
    justify-content: center;
    gap: 1.2rem;
    margin-bottom: 1.5rem;
  }
  .share-modal .social-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 1px solid #e7e9ee;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    text-decoration: none;
    transition: all 0.2s ease;
    background: #fff;
  }
  .share-modal .social-icon.whatsapp {
    color: #25D366;
    border-color: #25D366;
  }
  .share-modal .social-icon.whatsapp:hover {
    background: #25D366;
    color: #fff;
  }
  .share-modal .social-icon.facebook {
    color: #1877F2;
    border-color: #1877F2;
  }
  .share-modal .social-icon.facebook:hover {
    background: #1877F2;
    color: #fff;
  }
  .share-modal .social-icon.twitter {
    color: #000;
    border-color: #000;
  }
  .share-modal .social-icon.twitter:hover {
    background: #000;
    color: #fff;
  }
  .share-modal .social-icon.email {
    color: #EA4335;
    border-color: #EA4335;
  }
  .share-modal .social-icon.email:hover {
    background: #EA4335;
    color: #fff;
  }
  .share-modal .copy-box {
    display: flex;
    align-items: center;
    border: 1px solid #e7e9ee;
    border-radius: 0.6rem;
    overflow: hidden;
    background: #f8f9fa;
  }
  .share-modal .copy-box input {
    flex: 1;
    border: none;
    padding: 0.6rem 1rem;
    font-size: 0.85rem;
    background: transparent;
    outline: none;
    color: #12213a;
  }
  .share-modal .copy-box button {
    background: #c9a84c;
    color: #fff;
    border: none;
    padding: 0.6rem 1.2rem;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: background 0.2s;
    white-space: nowrap;
  }
  .share-modal .copy-box button:hover {
    background: #b8973e;
  }
  
  /* Dark Mode Share Modal */
  body.dark-mode .share-modal {
    background: #161b22;
    border: 1px solid #30363d;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
  }
  body.dark-mode .share-modal .modal-header h5 {
    color: #f0f6fc;
  }
  body.dark-mode .share-modal .close-modal {
    color: #8b949e;
  }
  body.dark-mode .share-modal .close-modal:hover {
    color: #f0f6fc;
  }
  body.dark-mode .share-modal p {
    color: #8b949e;
  }
  body.dark-mode .share-modal .social-icon {
    background: #21262d;
    border-color: #30363d;
  }
  body.dark-mode .share-modal .social-icon.twitter {
    color: #f0f6fc;
    border-color: #f0f6fc;
  }
  body.dark-mode .share-modal .social-icon.twitter:hover {
    background: #f0f6fc;
    color: #161b22;
  }
  body.dark-mode .share-modal .copy-box {
    background: #21262d;
    border-color: #30363d;
  }
  body.dark-mode .share-modal .copy-box input {
    color: #f0f6fc;
  }
  body.dark-mode .share-modal .copy-box button {
    background: #c9a84c;
    color: #0a0f1a;
  }
  body.dark-mode .share-modal .copy-box button:hover {
    background: #f0b429;
  }

  /* ============================================
     RESPONSIVE STYLES
     ============================================ */
  @media (max-width: 991px) {
    .blog-card {
      flex-direction: column-reverse;
      padding: 1.2rem;
      gap: 1rem;
    }
    .blog-card .thumb {
      min-height: 200px;
      width: 100%;
      flex: none;
      border-radius: 0.8rem;
    }
    .blog-card h3 {
      font-size: 1.4rem;
    }
  }

  @media (max-width: 575.98px) {
    .blog-hero {
      padding: 2.5rem 0 2rem;
    }
    .blog-hero h1 {
      font-size: 1.8rem;
    }
    .blog-hero p {
      font-size: 0.9rem;
    }
    .blog-card {
      padding: 1rem;
      border-radius: 1rem;
    }
    .blog-card .thumb {
      min-height: 160px;
    }
    .blog-card h3 {
      font-size: 1.2rem;
    }
    .blog-card p.excerpt {
      font-size: 0.9rem;
    }
    .blog-card .card-header {
      flex-wrap: wrap;
      gap: 0.5rem;
    }
    .blog-card .card-actions-bottom {
      flex-wrap: wrap;
      gap: 0.5rem;
    }
    .blog-card .action-btn {
      font-size: 0.7rem;
      padding: 0.3rem 0.6rem;
    }
    .blog-card .action-btn i {
      font-size: 0.85rem;
    }
    .blog-card .avatar {
      width: 32px;
      height: 32px;
      font-size: 0.75rem;
    }
    .blog-card .author-details {
      font-size: 0.75rem;
    }
    
    .share-modal {
      padding: 1.5rem 1.2rem;
      width: 95%;
    }
    .share-modal .social-icons {
      gap: 0.8rem;
    }
    .share-modal .social-icon {
      width: 42px;
      height: 42px;
      font-size: 1.1rem;
    }
  }

  @media (max-width: 400px) {
    .blog-card .thumb {
      min-height: 130px;
    }
    .blog-card h3 {
      font-size: 1rem;
    }
    .blog-card .action-buttons {
      gap: 0.4rem;
    }
    .blog-card .action-btn {
      font-size: 0.65rem;
      padding: 0.2rem 0.5rem;
    }
  }
</style>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<!-- ============ BLOG HERO ============ -->
<section class="blog-hero">
  <div class="container">
    <span class="eyebrow">Real Estate &amp; Insights</span>
    <h1>Latest News & Articles</h1>
    <p>Explore expert articles, industry updates, investment opportunities, and practical guides designed to keep you ahead in the ever-changing real estate market.</p>
  </div>
</section>

<!-- ============ BLOG CONTENT ============ -->
<section class="section">
  <div class="container">

    <!-- ====== GRID CONTAINER ====== -->
    <div class="blog-grid" id="blogContainer">

    <?php if (mysqli_num_rows($blogsQuery) > 0): ?>
      <?php while($row=mysqli_fetch_assoc($blogsQuery)){ ?>

      <a href="blog-details.php?slug=<?php echo $row['slug']; ?>" class="blog-card">

          <!-- Left Side: Content -->
          <div class="body">

              <!-- Top Header: Avatar, Badge, Bookmark -->
              <div class="card-header">
                  <div class="author-info">
                      <div class="avatar">
                          <?php echo strtoupper(substr($row['author'], 0, 1)); ?>
                      </div>
                      <div class="author-details">
                          <span class="author-name"><?php echo htmlspecialchars($row['author']); ?></span>
                          <span class="post-meta">
                              <?php echo ucfirst(htmlspecialchars($row['category'])); ?> &bull; <?php echo date("F j, Y", strtotime($row['created_at'])); ?>
                          </span>
                      </div>
                  </div>

                  <div class="card-actions-top">
                      <!-- Bookmark Icon (Saved to localStorage) -->
                      <i class="bi bi-bookmark bookmark-icon" data-id="<?php echo $row['id']; ?>" onclick="event.preventDefault(); toggleBookmark(this);"></i>
                  </div>
              </div>

              <!-- Title & Excerpt -->
              <h3>
                  <?php echo htmlspecialchars($row['title']); ?>
              </h3>
              <p class="excerpt">
                  <?php echo htmlspecialchars($row['excerpt']); ?>
              </p>

              <!-- Bottom Actions (Like, Comment, Share) -->
              <div class="card-actions-bottom">
                  <div class="action-buttons">
                      <!-- Like Button -->
                      <span class="action-btn like-btn" data-id="<?php echo $row['id']; ?>" onclick="event.preventDefault(); toggleLike(this);">
                          <i class="bi bi-heart"></i> <span class="like-count">0</span>
                      </span>
                      <!-- Comment Button -->
                      <span class="action-btn" onclick="event.preventDefault();">
                          <i class="bi bi-chat"></i> <span class="comment-count">0</span>
                      </span>
                  </div>
                  <!-- Share Button -->
                  <div class="share-btn" onclick="event.preventDefault(); openShareModal('<?php echo $row['slug']; ?>', '<?php echo htmlspecialchars(addslashes($row['title'])); ?>');">
                      <i class="bi bi-share"></i>
                  </div>
              </div>

          </div>

          <!-- Right Side: Image -->
          <?php 
            $imagePath = '../backend/uploads/blogs/' . $row['image'];
            // Check if image exists, use placeholder if not
            if (!file_exists($imagePath) || empty($row['image'])) {
                $imagePath = 'assets/images/blog-placeholder.jpg';
            }
          ?>
          <div class="thumb"
              style="background-image:url('<?php echo $imagePath; ?>');">
          </div>

      </a>

      <?php } ?>
    <?php else: ?>
      <!-- No blogs found -->
      <div class="text-center py-5" style="grid-column: 1 / -1;">
        <i class="bi bi-newspaper" style="font-size: 3rem; color: #6b7686;"></i>
        <h4 class="mt-3" style="color: #6b7686;">No blog posts yet</h4>
        <p style="color: #8a93a3;">Check back soon for new articles and insights.</p>
      </div>
    <?php endif; ?>

    </div>

  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

<!-- ============ SHARE MODAL ============ -->
<div class="share-modal-overlay" id="shareModalOverlay">
  <div class="share-modal">
    <div class="modal-header">
      <h5>Share Property</h5>
      <button class="close-modal" onclick="closeShareModal()"><i class="bi bi-x-lg"></i></button>
    </div>
    <p>Share this property with your friends and family</p>
    
    <div class="social-icons">
      <a href="#" class="social-icon whatsapp" id="waShare" target="_blank"><i class="bi bi-whatsapp"></i></a>
      <a href="#" class="social-icon facebook" id="fbShare" target="_blank"><i class="bi bi-facebook"></i></a>
      <a href="#" class="social-icon twitter" id="twShare" target="_blank"><i class="bi bi-twitter-x"></i></a>
      <a href="#" class="social-icon email" id="emailShare" target="_blank"><i class="bi bi-envelope"></i></a>
    </div>
    
    <div class="copy-box">
      <input type="text" id="shareLinkInput" readonly value="">
      <button onclick="copyShareLink()"><i class="bi bi-files me-1"></i> Copy</button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- ============ INTERACTIVE SCRIPTS ============ -->
<script>
    // --- Like Functionality ---
    function toggleLike(btn) {
        const countSpan = btn.querySelector('.like-count');
        let currentCount = parseInt(countSpan.innerText) || 0;
        
        if(btn.classList.contains('liked')) {
            btn.classList.remove('liked');
            btn.querySelector('i').className = 'bi bi-heart';
            currentCount--;
        } else {
            btn.classList.add('liked');
            btn.querySelector('i').className = 'bi bi-heart-fill';
            currentCount++;
        }
        countSpan.innerText = currentCount;
    }

    // --- Bookmark Functionality (Saved to Browser) ---
    function toggleBookmark(icon) {
        const postId = icon.getAttribute('data-id');
        
        // Toggle the bookmark class
        icon.classList.toggle('bookmarked');
        
        // Toggle the icon between bookmark and bookmark-fill
        if (icon.classList.contains('bookmarked')) {
            icon.className = 'bi bi-bookmark-fill bookmark-icon';
            localStorage.setItem('bookmarked_' + postId, 'true');
        } else {
            icon.className = 'bi bi-bookmark bookmark-icon';
            localStorage.removeItem('bookmarked_' + postId);
        }
    }

    // --- Share Modal Functionality ---
    let currentShareUrl = '';
    let currentShareTitle = '';

    function openShareModal(slug, title) {
        // Construct full URL
        const baseUrl = window.location.origin + '/blog-details.php?slug=';
        currentShareUrl = baseUrl + slug;
        currentShareTitle = title;

        // Set input value
        document.getElementById('shareLinkInput').value = currentShareUrl;

        // Set social media links
        document.getElementById('waShare').href = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(title + ' - ' + currentShareUrl);
        document.getElementById('fbShare').href = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(currentShareUrl);
        document.getElementById('twShare').href = 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(title) + '&url=' + encodeURIComponent(currentShareUrl);
        document.getElementById('emailShare').href = 'mailto:?subject=' + encodeURIComponent(title) + '&body=Check out this article: ' + encodeURIComponent(currentShareUrl);

        // Show modal
        const overlay = document.getElementById('shareModalOverlay');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeShareModal() {
        const overlay = document.getElementById('shareModalOverlay');
        overlay.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    function copyShareLink() {
        const input = document.getElementById('shareLinkInput');
        input.select();
        input.setSelectionRange(0, 99999);
        
        try {
            navigator.clipboard.writeText(input.value).then(() => {
                alert('Link copied to clipboard!');
            });
        } catch (err) {
            document.execCommand('copy');
            alert('Link copied to clipboard!');
        }
    }

    // Close modal when clicking on backdrop
    document.getElementById('shareModalOverlay').addEventListener('click', function(e) {
        if (e.target === this) {
            closeShareModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeShareModal();
        }
    });

    // ============ Load saved states from localStorage ============
    document.addEventListener('DOMContentLoaded', function() {
        // Restore bookmark states
        document.querySelectorAll('.bookmark-icon').forEach(function(icon) {
            const postId = icon.getAttribute('data-id');
            if (localStorage.getItem('bookmarked_' + postId) === 'true') {
                icon.className = 'bi bi-bookmark-fill bookmark-icon';
                icon.classList.add('bookmarked');
            }
        });
    });

    // ============ Dark Mode ============
    (function(){
        function handleThemeChange() {
            const isDark = document.body.classList.contains('dark-mode');
        }

        document.addEventListener('themeChanged', function(e) {
            handleThemeChange();
        });

        handleThemeChange();

        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    handleThemeChange();
                }
            });
        });
        observer.observe(document.body, { attributes: true });
    })();
</script>

</body>
</html>