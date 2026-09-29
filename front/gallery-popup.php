<?php
// ============================================================
// GALLERY POPUP - Complete CSS, HTML, and JavaScript
// This file contains all gallery-related code
// ============================================================
?>

<!-- ============================================================
     GALLERY POPUP CSS
     ============================================================ -->
<style>
/* ============================================
   ENHANCED GALLERY POPUP STYLES
   ============================================ */
.gallery-popup-content {
    background: #0a0a0f !important;
    border: none !important;
    border-radius: 0 !important;
}

.gallery-close-btn {
    position: fixed !important;
    top: 15px;
    right: 20px;
    z-index: 1060;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.gallery-close-btn:hover {
    opacity: 1;
}

/* TOP BAR */
.gallery-top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 24px;
    background: rgba(10, 10, 15, 0.95);
    border-bottom: 1px solid rgba(255,255,255,0.06);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1050;
    flex-wrap: wrap;
    gap: 8px;
}

.gallery-categories {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    flex: 1;
}

.gallery-top-actions {
    display: flex;
    gap: 6px;
    flex-shrink: 0;
}

.gallery-cat-btn {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.6);
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    letter-spacing: 0.02em;
    white-space: nowrap;
}

.gallery-cat-btn .cat-count {
    color: rgba(255,255,255,0.3);
    font-size: 0.65rem;
}

.gallery-cat-btn:hover {
    background: rgba(255,255,255,0.12);
    color: #fff;
    border-color: rgba(255,255,255,0.15);
}

.gallery-cat-btn.active {
    background: #c9a84c;
    color: #fff;
    border-color: #c9a84c;
}

.gallery-cat-btn.active .cat-count {
    color: rgba(255,255,255,0.7);
}

.brochure-btn {
    background: rgba(201, 168, 76, 0.15) !important;
    border-color: rgba(201, 168, 76, 0.25) !important;
    color: #c9a84c !important;
}

.brochure-btn:hover {
    background: rgba(201, 168, 76, 0.25) !important;
}

.request-btn {
    background: rgba(255,255,255,0.06) !important;
    border-color: rgba(255,255,255,0.08) !important;
    color: rgba(255,255,255,0.6) !important;
}

.request-btn:hover {
    background: rgba(255,255,255,0.12) !important;
    color: #fff !important;
}

.gallery-cat-btn i {
    margin-right: 4px;
    font-size: 0.7rem;
}

/* LAYOUT */
.gallery-popup-layout {
    display: flex;
    height: 100vh;
    width: 100%;
    background: #0a0a0f;
    padding-top: 56px;
}

.gallery-main-col {
    flex: 1;
    height: calc(100vh - 56px);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    background: #0a0a0f;
    padding: 10px 20px 20px 20px;
}

/* Feedback - Top of Big Image */
.gallery-feedback-top {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 16px;
    background: rgba(0,0,0,0.5);
    border-radius: 30px;
    backdrop-filter: blur(10px);
    margin-bottom: 12px;
    border: 1px solid rgba(255,255,255,0.06);
    z-index: 5;
    flex-wrap: wrap;
    justify-content: center;
}

.gallery-feedback-top .feedback-label {
    color: rgba(255,255,255,0.7);
    font-size: 0.75rem;
    font-weight: 500;
}

.gallery-feedback-top .feedback-btn {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.6);
    padding: 3px 12px;
    border-radius: 16px;
    font-size: 0.7rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 4px;
}

.gallery-feedback-top .feedback-btn i {
    font-size: 0.7rem;
}

.gallery-feedback-top .feedback-btn:hover {
    background: rgba(255,255,255,0.12);
    color: #fff;
}

.gallery-feedback-top .feedback-btn.yes-btn:hover {
    background: rgba(34, 197, 94, 0.2);
    border-color: #22c55e;
    color: #22c55e;
}

.gallery-feedback-top .feedback-btn.no-btn:hover {
    background: rgba(239, 68, 68, 0.2);
    border-color: #ef4444;
    color: #ef4444;
}

.gallery-feedback-top .feedback-btn.skip-btn:hover {
    background: rgba(255,255,255,0.12);
    color: #fff;
}

.gallery-feedback-top .feedback-btn.feedback-active {
    background: rgba(201, 168, 76, 0.2);
    border-color: #c9a84c;
    color: #c9a84c;
}

.gallery-main-image-wrapper {
    position: relative;
    width: 100%;
    height: calc(100% - 60px);
    display: flex;
    align-items: center;
    justify-content: center;
}

.gallery-main-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    border-radius: 4px;
    transition: transform 0.3s ease;
    transform-origin: center center;
}

/* Navigation Arrows */
.gallery-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 10;
    backdrop-filter: blur(8px);
}

.gallery-nav-btn:hover {
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.4);
    transform: translateY(-50%) scale(1.05);
}

.gallery-nav-btn i {
    font-size: 1.5rem;
}

.gallery-prev-btn {
    left: 20px;
}

.gallery-next-btn {
    right: 20px;
}

/* Zoom Indicator */
.zoom-indicator {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0,0,0,0.7);
    color: #fff;
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 0.7rem;
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.1);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 5;
    pointer-events: none;
}

.zoom-indicator.show {
    opacity: 1;
}

/* Sidebar */
.gallery-sidebar {
    width: 320px;
    min-width: 320px;
    height: calc(100vh - 56px);
    background: #12121a;
    padding: 16px 18px 20px 18px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
    border-left: 1px solid rgba(255,255,255,0.06);
}

/* Photo Info */
.gallery-photo-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    flex-shrink: 0;
}

.photo-counter {
    color: rgba(255,255,255,0.7);
    font-size: 0.8rem;
    font-weight: 500;
}

.photo-title {
    color: #fff;
    font-size: 0.85rem;
    font-weight: 600;
}

/* Gallery Controls - Zoom & Share */
.gallery-controls {
    display: flex;
    gap: 6px;
    padding: 8px 0 10px 0;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    flex-wrap: wrap;
    flex-shrink: 0;
}

.gallery-control-btn {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.6);
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.gallery-control-btn:hover {
    background: rgba(255,255,255,0.12);
    color: #fff;
    border-color: rgba(255,255,255,0.15);
}

.gallery-control-btn:active {
    transform: scale(0.95);
}

/* Thumbnails */
.gallery-thumbnails-wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
}

.gallery-thumbnails-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 4px 0 10px 0;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    flex-shrink: 0;
}

.thumbnails-label {
    color: rgba(255,255,255,0.6);
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.thumbnails-toggle {
    background: none;
    border: none;
    color: rgba(255,255,255,0.4);
    font-size: 0.7rem;
    cursor: pointer;
    transition: color 0.3s ease;
    display: flex;
    align-items: center;
    gap: 4px;
}

.thumbnails-toggle:hover {
    color: rgba(255,255,255,0.7);
}

.gallery-thumbnails {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px;
    padding: 10px 0 4px 0;
    overflow-y: auto;
    flex: 1;
    align-content: start;
}

.gallery-thumbnails.hidden {
    display: none;
}

.gallery-thumb {
    aspect-ratio: 1;
    border-radius: 4px;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    position: relative;
}

.gallery-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.gallery-thumb:hover {
    border-color: rgba(255,255,255,0.3);
    transform: scale(1.03);
}

.gallery-thumb.active {
    border-color: #c9a84c;
    box-shadow: 0 0 20px rgba(201, 168, 76, 0.2);
}

.gallery-thumb .thumb-badge {
    position: absolute;
    bottom: 4px;
    right: 4px;
    background: rgba(0,0,0,0.7);
    color: #fff;
    font-size: 0.5rem;
    padding: 1px 6px;
    border-radius: 10px;
    backdrop-filter: blur(4px);
}

/* Fullscreen mode */
.gallery-popup-content:-webkit-full-screen {
    background: #0a0a0f !important;
}

.gallery-popup-content:-moz-full-screen {
    background: #0a0a0f !important;
}

.gallery-popup-content:fullscreen {
    background: #0a0a0f !important;
}

/* Scrollbar styling */
.gallery-sidebar::-webkit-scrollbar,
.gallery-thumbnails::-webkit-scrollbar {
    width: 4px;
}

.gallery-sidebar::-webkit-scrollbar-track,
.gallery-thumbnails::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.05);
}

.gallery-sidebar::-webkit-scrollbar-thumb,
.gallery-thumbnails::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.15);
    border-radius: 2px;
}

.gallery-sidebar::-webkit-scrollbar-thumb:hover,
.gallery-thumbnails::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.25);
}

/* Responsive */
@media (max-width: 991.98px) {
    .gallery-popup-layout {
        flex-direction: column;
        height: 100vh;
        padding-top: 70px;
    }
    
    .gallery-main-col {
        height: 45vh;
        padding: 8px 10px 10px 10px;
    }
    
    .gallery-main-image-wrapper {
        height: calc(100% - 50px);
    }
    
    .gallery-sidebar {
        width: 100%;
        min-width: unset;
        height: 55vh;
        border-left: none;
        border-top: 1px solid rgba(255,255,255,0.06);
        padding: 12px 14px;
    }
    
    .gallery-nav-btn {
        width: 36px;
        height: 36px;
    }
    
    .gallery-nav-btn i {
        font-size: 1rem;
    }
    
    .gallery-prev-btn {
        left: 10px;
    }
    
    .gallery-next-btn {
        right: 10px;
    }
    
    .gallery-thumbnails {
        grid-template-columns: repeat(6, 1fr);
    }
    
    .gallery-top-bar {
        padding: 8px 14px;
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }
    
    .gallery-top-actions {
        width: 100%;
        justify-content: flex-start;
    }
    
    .gallery-feedback-top {
        padding: 5px 12px;
        gap: 6px;
        margin-bottom: 8px;
    }
    
    .gallery-feedback-top .feedback-btn {
        font-size: 0.65rem;
        padding: 2px 10px;
    }
    
    .gallery-controls {
        gap: 4px;
        padding: 6px 0 8px 0;
    }
    
    .gallery-control-btn {
        width: 32px;
        height: 32px;
        font-size: 0.8rem;
    }
}

@media (max-width: 575.98px) {
    .gallery-main-col {
        height: 35vh;
    }
    
    .gallery-sidebar {
        height: 65vh;
        padding: 10px;
    }
    
    .gallery-categories {
        gap: 4px;
    }
    
    .gallery-cat-btn {
        font-size: 0.6rem;
        padding: 4px 8px;
    }
    
    .gallery-thumbnails {
        grid-template-columns: repeat(4, 1fr);
        gap: 4px;
    }
    
    .gallery-top-bar {
        padding: 6px 10px;
    }
    
    .gallery-feedback-top {
        padding: 4px 10px;
        gap: 4px;
    }
    
    .gallery-feedback-top .feedback-label {
        font-size: 0.65rem;
    }
    
    .gallery-feedback-top .feedback-btn {
        font-size: 0.6rem;
        padding: 2px 8px;
    }
    
    .gallery-control-btn {
        width: 28px;
        height: 28px;
        font-size: 0.7rem;
    }
    
    .gallery-controls {
        gap: 3px;
        padding: 4px 0 6px 0;
    }
}
</style>

<!-- ============================================================
     GALLERY POPUP HTML
     ============================================================ -->
<div class="modal fade" id="enhancedGalleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content gallery-popup-content">
            <!-- Close Button -->
            <button type="button" class="btn-close btn-close-white gallery-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
            
            <!-- TOP BAR: Categories Left, Brochure & Request More Photos Right -->
            <div class="gallery-top-bar">
                <div class="gallery-categories">
                    <button class="gallery-cat-btn active" data-category="all" onclick="filterGallery('all')">All</button>
                    <button class="gallery-cat-btn" data-category="outdoors" onclick="filterGallery('outdoors')">Outdoors <span class="cat-count">(2)</span></button>
                    <button class="gallery-cat-btn" data-category="facilities" onclick="filterGallery('facilities')">Facilities <span class="cat-count">(6)</span></button>
                    <button class="gallery-cat-btn" data-category="construction" onclick="filterGallery('construction')">Construction <span class="cat-count">(3)</span></button>
                    <button class="gallery-cat-btn" data-category="masterplan" onclick="filterGallery('masterplan')">Masterplan <span class="cat-count">(1)</span></button>
                    <button class="gallery-cat-btn" data-category="videos" onclick="filterGallery('videos')">Videos <span class="cat-count">(2)</span></button>
                </div>
                <div class="gallery-top-actions">
                    <button class="gallery-cat-btn brochure-btn" onclick="filterGallery('brochure')">
                        <i class="bi bi-file-pdf"></i> Brochure
                    </button>
                    <button class="gallery-cat-btn request-btn" onclick="filterGallery('request')">
                        <i class="bi bi-plus-circle"></i> Request More Photos
                    </button>
                </div>
            </div>
            
            <div class="gallery-popup-layout">
                <!-- LEFT SIDE: MAIN IMAGE -->
                <div class="gallery-main-col">
                    <!-- Helpful Feedback - Top of Big Image -->
                    <div class="gallery-feedback-top">
                        <span class="feedback-label">Is this photo helpful?</span>
                        <button class="feedback-btn yes-btn" onclick="feedbackHelpful('yes')">
                            <i class="bi bi-hand-thumbs-up"></i> Yes
                        </button>
                        <button class="feedback-btn no-btn" onclick="feedbackHelpful('no')">
                            <i class="bi bi-hand-thumbs-down"></i> No
                        </button>
                        <button class="feedback-btn skip-btn" onclick="feedbackHelpful('skip')">Skip</button>
                    </div>
                    
                    <div class="gallery-main-image-wrapper">
                        <img id="galleryMainImage" src="" alt="Gallery Image" class="gallery-main-image">
                        
                        <!-- Navigation Arrows -->
                        <button class="gallery-nav-btn gallery-prev-btn" onclick="navigateGallery('prev')">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button class="gallery-nav-btn gallery-next-btn" onclick="navigateGallery('next')">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                        
                        <!-- Zoom Indicator -->
                        <div class="zoom-indicator" id="zoomIndicator">100%</div>
                    </div>
                </div>
                
                <!-- RIGHT SIDE: SIDEBAR -->
                <div class="gallery-sidebar">
                    <!-- Photo Counter & Title -->
                    <div class="gallery-photo-info">
                        <span class="photo-counter" id="photoCounter">1 of 4</span>
                        <span class="photo-title" id="photoTitle">Elevation</span>
                    </div>
                    
                    <!-- Zoom & Share Controls -->
                    <div class="gallery-controls">
                        <button class="gallery-control-btn" onclick="zoomImage('in')" title="Zoom In">
                            <i class="bi bi-zoom-in"></i>
                        </button>
                        <button class="gallery-control-btn" onclick="zoomImage('out')" title="Zoom Out">
                            <i class="bi bi-zoom-out"></i>
                        </button>
                        <button class="gallery-control-btn" onclick="resetZoom()" title="Reset Zoom">
                            <i class="bi bi-arrows-expand"></i>
                        </button>
                        <button class="gallery-control-btn" onclick="toggleFullscreen()" title="Full Screen">
                            <i class="bi bi-fullscreen"></i>
                        </button>
                        <button class="gallery-control-btn" onclick="shareGalleryImage()" title="Share Image">
                            <i class="bi bi-share-fill"></i>
                        </button>
                    </div>
                    
                    <!-- Thumbnail Navigation -->
                    <div class="gallery-thumbnails-wrapper">
                        <div class="gallery-thumbnails-header">
                            <span class="thumbnails-label" id="currentCategoryLabel">ALL IMAGES</span>
                            <button class="thumbnails-toggle" onclick="toggleThumbnails()">
                                <i class="bi bi-eye"></i> Hide
                            </button>
                        </div>
                        <div class="gallery-thumbnails" id="galleryThumbnails">
                            <!-- Thumbnails will be populated dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     GALLERY POPUP JAVASCRIPT
     ============================================================ -->
<script>
// ===== GALLERY DATA =====
var galleryData = {
    items: [],
    currentIndex: 0,
    currentCategory: 'all',
    filteredItems: [],
    thumbnailsVisible: true
};

// ===== ZOOM CONTROLS =====
var currentZoom = 1;
var ZOOM_STEP = 0.25;
var MAX_ZOOM = 3;
var MIN_ZOOM = 0.5;
var zoomIndicatorTimer = null;

function zoomImage(direction) {
    var img = document.getElementById('galleryMainImage');
    if (!img) return;
    
    if (direction === 'in') {
        currentZoom = Math.min(currentZoom + ZOOM_STEP, MAX_ZOOM);
    } else if (direction === 'out') {
        currentZoom = Math.max(currentZoom - ZOOM_STEP, MIN_ZOOM);
    }
    
    img.style.transform = 'scale(' + currentZoom + ')';
    showZoomIndicator(currentZoom);
}

function resetZoom() {
    currentZoom = 1;
    var img = document.getElementById('galleryMainImage');
    if (img) {
        img.style.transform = 'scale(1)';
    }
    showZoomIndicator(1);
}

function showZoomIndicator(level) {
    var indicator = document.getElementById('zoomIndicator');
    if (!indicator) return;
    
    var percentage = Math.round(level * 100);
    indicator.textContent = percentage + '%';
    indicator.classList.add('show');
    
    clearTimeout(zoomIndicatorTimer);
    zoomIndicatorTimer = setTimeout(function() {
        indicator.classList.remove('show');
    }, 1500);
}

// ===== FULLSCREEN CONTROLS =====
function toggleFullscreen() {
    var modalContent = document.querySelector('.gallery-popup-content');
    if (!modalContent) return;
    
    if (!document.fullscreenElement) {
        if (modalContent.requestFullscreen) {
            modalContent.requestFullscreen();
        } else if (modalContent.webkitRequestFullscreen) {
            modalContent.webkitRequestFullscreen();
        } else if (modalContent.msRequestFullscreen) {
            modalContent.msRequestFullscreen();
        }
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }
}

// Update fullscreen button icon when fullscreen changes
document.addEventListener('fullscreenchange', updateFullscreenIcon);
document.addEventListener('webkitfullscreenchange', updateFullscreenIcon);
document.addEventListener('mozfullscreenchange', updateFullscreenIcon);
document.addEventListener('MSFullscreenChange', updateFullscreenIcon);

function updateFullscreenIcon() {
    var btn = document.querySelector('.gallery-control-btn .bi-fullscreen');
    if (!btn) return;
    
    var isFullscreen = document.fullscreenElement || 
                         document.webkitFullscreenElement || 
                         document.mozFullScreenElement || 
                         document.msFullscreenElement;
    
    if (isFullscreen) {
        btn.className = 'bi bi-fullscreen-exit';
    } else {
        btn.className = 'bi bi-fullscreen';
    }
}

// ===== SHARE GALLERY IMAGE =====
function shareGalleryImage() {
    var img = document.getElementById('galleryMainImage');
    if (!img) return;
    
    var title = document.getElementById('photoTitle')?.textContent || 'Gallery Image';
    var url = window.location.href;
    
    if (navigator.share) {
        fetch(img.src)
            .then(function(response) { return response.blob(); })
            .then(function(blob) {
                var file = new File([blob], 'gallery-image.jpg', { type: 'image/jpeg' });
                navigator.share({
                    title: title,
                    text: 'Check out this image: ' + title,
                    files: [file]
                }).catch(function() {});
            })
            .catch(function() {
                navigator.share({
                    title: title,
                    text: 'Check out this image: ' + title,
                    url: url
                }).catch(function() {});
            });
    } else {
        var imageUrl = img.src;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(imageUrl).then(function() {
                showZoomIndicatorMessage('Image URL copied to clipboard!');
            }).catch(function() {
                window.open(imageUrl, '_blank');
            });
        } else {
            window.open(imageUrl, '_blank');
        }
    }
}

function showZoomIndicatorMessage(message) {
    var indicator = document.getElementById('zoomIndicator');
    if (!indicator) return;
    
    indicator.textContent = message;
    indicator.classList.add('show');
    
    clearTimeout(zoomIndicatorTimer);
    zoomIndicatorTimer = setTimeout(function() {
        indicator.classList.remove('show');
    }, 2000);
}

// ===== GALLERY FUNCTIONS =====
function openEnhancedGallery(index) {
    var modalEl = document.getElementById('enhancedGalleryModal');
    var modal = new bootstrap.Modal(modalEl);
    galleryData.currentIndex = index || 0;
    updateGalleryImage();
    renderThumbnails();
    modal.show();
    resetZoom();
}

function updateGalleryImage() {
    var items = galleryData.filteredItems;
    if (items.length === 0) return;
    var item = items[galleryData.currentIndex] || items[0];
    if (!item) return;
    
    var mainImg = document.getElementById('galleryMainImage');
    mainImg.src = item.image;
    mainImg.alt = item.title;
    
    document.getElementById('photoCounter').textContent = 
        (galleryData.currentIndex + 1) + ' of ' + items.length;
    
    document.getElementById('photoTitle').textContent = item.title;
    
    var thumbs = document.querySelectorAll('.gallery-thumb');
    thumbs.forEach(function(thumb, idx) {
        thumb.classList.toggle('active', idx === galleryData.currentIndex);
    });
    
    var prevBtn = document.querySelector('.gallery-prev-btn');
    var nextBtn = document.querySelector('.gallery-next-btn');
    if (prevBtn) prevBtn.style.display = galleryData.currentIndex === 0 ? 'none' : 'flex';
    if (nextBtn) nextBtn.style.display = galleryData.currentIndex === items.length - 1 ? 'none' : 'flex';
}

function navigateGallery(direction) {
    var items = galleryData.filteredItems;
    if (items.length === 0) return;
    if (direction === 'prev') {
        galleryData.currentIndex = Math.max(0, galleryData.currentIndex - 1);
    } else {
        galleryData.currentIndex = Math.min(items.length - 1, galleryData.currentIndex + 1);
    }
    resetZoom();
    updateGalleryImage();
}

function filterGallery(category) {
    var btns = document.querySelectorAll('.gallery-cat-btn');
    btns.forEach(function(btn) {
        btn.classList.toggle('active', btn.dataset.category === category);
    });
    
    galleryData.currentCategory = category;
    
    if (category === 'all') {
        galleryData.filteredItems = galleryData.items;
    } else {
        galleryData.filteredItems = galleryData.items.filter(function(item) {
            return item.category === category;
        });
    }
    
    if (galleryData.filteredItems.length === 0) {
        galleryData.filteredItems = galleryData.items;
    }
    
    galleryData.currentIndex = 0;
    resetZoom();
    updateGalleryImage();
    renderThumbnails();
}

function renderThumbnails() {
    var container = document.getElementById('galleryThumbnails');
    var items = galleryData.filteredItems;
    var label = document.getElementById('currentCategoryLabel');
    
    if (label) {
        var categoryMap = {
            'all': 'ALL IMAGES',
            'outdoors': 'OUTDOORS',
            'facilities': 'FACILITIES',
            'construction': 'CONSTRUCTION',
            'masterplan': 'MASTERPLAN',
            'videos': 'VIDEOS',
            'brochure': 'BROCHURE',
            'request': 'REQUEST MORE PHOTOS'
        };
        label.textContent = categoryMap[galleryData.currentCategory] || 'ALL IMAGES';
    }
    
    if (items.length === 0) {
        container.innerHTML = '<div style="color:rgba(255,255,255,0.4); text-align:center; padding:20px; font-size:0.8rem;">No images in this category</div>';
        return;
    }
    
    var html = '';
    items.forEach(function(item, index) {
        var activeClass = index === galleryData.currentIndex ? 'active' : '';
        var badge = '';
        if (item.isVideo) {
            badge = '<span class="thumb-badge"><i class="bi bi-play-fill"></i></span>';
        } else if (item.isBrochure) {
            badge = '<span class="thumb-badge"><i class="bi bi-file-pdf"></i></span>';
        }
        html += '<div class="gallery-thumb ' + activeClass + '" onclick="goToGalleryItem(' + index + ')">' +
                '<img src="' + item.image + '" alt="' + item.title + '">' +
                badge +
                '</div>';
    });
    container.innerHTML = html;
}

function goToGalleryItem(index) {
    galleryData.currentIndex = index;
    resetZoom();
    updateGalleryImage();
    renderThumbnails();
}

function toggleThumbnails() {
    var container = document.getElementById('galleryThumbnails');
    var toggleBtn = document.querySelector('.thumbnails-toggle');
    
    galleryData.thumbnailsVisible = !galleryData.thumbnailsVisible;
    container.classList.toggle('hidden', !galleryData.thumbnailsVisible);
    if (toggleBtn) {
        toggleBtn.innerHTML = galleryData.thumbnailsVisible 
            ? '<i class="bi bi-eye"></i> Hide' 
            : '<i class="bi bi-eye-slash"></i> Show';
    }
}

function feedbackHelpful(response) {
    var btns = document.querySelectorAll('.feedback-btn');
    btns.forEach(function(btn) {
        btn.classList.remove('feedback-active');
    });
    
    if (response === 'yes') {
        var yesBtn = document.querySelector('.feedback-btn.yes-btn');
        if (yesBtn) yesBtn.classList.add('feedback-active');
    } else if (response === 'no') {
        var noBtn = document.querySelector('.feedback-btn.no-btn');
        if (noBtn) noBtn.classList.add('feedback-active');
    }
}

// ===== KEYBOARD SHORTCUTS =====
document.addEventListener('keydown', function(e) {
    var modal = document.getElementById('enhancedGalleryModal');
    if (!modal || !modal.classList.contains('show')) return;
    
    if (e.key === 'ArrowLeft') navigateGallery('prev');
    if (e.key === 'ArrowRight') navigateGallery('next');
    if (e.key === '+' || e.key === '=') { e.preventDefault(); zoomImage('in'); }
    if (e.key === '-') { e.preventDefault(); zoomImage('out'); }
    if (e.key === '0') { e.preventDefault(); resetZoom(); }
    if (e.key === 'f' || e.key === 'F') { toggleFullscreen(); }
    if (e.key === 'Escape') {
        var bsModal = bootstrap.Modal.getInstance(modal);
        if (bsModal) bsModal.hide();
    }
});

// ===== INITIALIZE GALLERY WITH DATA =====
function initializeGallery(items) {
    galleryData.items = items;
    galleryData.filteredItems = items;
    galleryData.currentIndex = 0;
    resetZoom();
    if (document.getElementById('galleryThumbnails')) {
        renderThumbnails();
    }
}

// ===== MAKE FUNCTIONS GLOBALLY ACCESSIBLE =====
window.zoomImage = zoomImage;
window.resetZoom = resetZoom;
window.toggleFullscreen = toggleFullscreen;
window.shareGalleryImage = shareGalleryImage;
window.navigateGallery = navigateGallery;
window.filterGallery = filterGallery;
window.goToGalleryItem = goToGalleryItem;
window.openEnhancedGallery = openEnhancedGallery;
window.toggleThumbnails = toggleThumbnails;
window.feedbackHelpful = feedbackHelpful;
window.initializeGallery = initializeGallery;
</script>