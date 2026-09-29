<?php
// ============================================================
// FAQ, REVIEWS & TESTIMONIALS - Complete HTML with Feedback Popup
// This file contains all FAQ and reviews-related HTML
// ============================================================
?>

<!-- ============================================================
     REVIEWS & TESTIMONIALS SECTION
     ============================================================ -->
<div id="reviews" class="card-panel" style="width:100%; position:relative; padding:30px 20px; background:var(--card-bg, #fff); border-radius:12px; box-shadow:0 2px 20px rgba(0,0,0,0.06);">
    <!-- Heading with Rating Summary -->
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:25px; flex-wrap:wrap; gap:15px;">
        <div style="display:flex; align-items:center; gap:15px; flex-wrap:wrap;">
            <h3 style="font-size:1.5rem; margin:0; font-weight:700; color:var(--ink, #12213a);">Reviews & Testimonials</h3>
        </div>
        <div style="display:flex; align-items:center; gap:15px; flex-wrap:wrap;">
            <button onclick="openFeedbackPopup()" style="background:var(--primary, #c9a84c); color:#fff; border:none; border-radius:2rem; padding:0.4rem 1.4rem; font-size:0.8rem; font-weight:600; cursor:pointer; transition:all 0.2s ease; display:flex; align-items:center; gap:0.4rem; white-space:nowrap;">
                <i class="bi bi-pencil-square" style="font-size:0.9rem;"></i> Write a Review
            </button>
        </div>
        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <span style="font-size:1.6rem; margin:0; font-weight:700; color:var(--ink, #12213a);">4.8</span>
            <div style="display:flex; align-items:center; gap:3px;">
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:1.1rem;"></i>
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:1.1rem;"></i>
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:1.1rem;"></i>
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:1.1rem;"></i>
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:1.1rem;"></i>
            </div>
        </div>
    </div>

    <!-- Testimonials Grid - 4 columns x 1 row -->
    <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:20px;">
        <!-- Review 1 -->
        <div class="testimonial-card" style="background:var(--bg-soft, #f8f9fa); border-radius:12px; padding:20px; border:1px solid var(--line, #e7e9ee); transition:all 0.3s ease; display:flex; flex-direction:column;">
            <div style="display:flex; gap:2px; margin-bottom:12px;">
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:0.9rem;"></i>
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:0.9rem;"></i>
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:0.9rem;"></i>
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:0.9rem;"></i>
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:0.9rem;"></i>
            </div>
            <p style="font-size:0.9rem; line-height:1.6; color:var(--ink, #12213a); margin:0 0 15px 0; flex:1;">"One of the best decisions we made. The community is vibrant and amenities are world-class."</p>
            <div style="display:flex; align-items:center; gap:12px; border-top:1px solid var(--line, #e7e9ee); padding-top:14px;">
                <div style="width:40px; height:40px; border-radius:50%; background:var(--primary, #c9a84c); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:0.85rem; flex-shrink:0;">AR</div>
                <div>
                    <div style="font-weight:600; font-size:0.9rem; color:var(--ink, #12213a);">Ananya Reddy</div>
                    <div style="font-size:0.75rem; color:var(--muted, #6b7280);">Homeowner, 3 BHK</div>
                </div>
            </div>
        </div>

        <!-- Review 2 -->
        <div class="testimonial-card" style="background:var(--bg-soft, #f8f9fa); border-radius:12px; padding:20px; border:1px solid var(--line, #e7e9ee); transition:all 0.3s ease; display:flex; flex-direction:column;">
            <div style="display:flex; gap:2px; margin-bottom:12px;">
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:0.9rem;"></i>
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:0.9rem;"></i>
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:0.9rem;"></i>
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:0.9rem;"></i>
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:0.9rem;"></i>
            </div>
            <p style="font-size:0.9rem; line-height:1.6; color:var(--ink, #12213a); margin:0 0 15px 0; flex:1;">"Great investment opportunity. Property value has appreciated significantly."</p>
            <div style="display:flex; align-items:center; gap:12px; border-top:1px solid var(--line, #e7e9ee); padding-top:14px;">
                <div style="width:40px; height:40px; border-radius:50%; background:#4a90d9; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:0.85rem; flex-shrink:0;">VP</div>
                <div>
                    <div style="font-weight:600; font-size:0.9rem; color:var(--ink, #12213a);">Vikram Patel</div>
                    <div style="font-size:0.75rem; color:var(--muted, #6b7280);">Investor</div>
                </div>
            </div>
        </div>

        <!-- Review 3 -->
        <div class="testimonial-card" style="background:var(--bg-soft, #f8f9fa); border-radius:12px; padding:20px; border:1px solid var(--line, #e7e9ee); transition:all 0.3s ease; display:flex; flex-direction:column;">
            <div style="display:flex; gap:2px; margin-bottom:12px;">
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:0.9rem;"></i>
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:0.9rem;"></i>
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:0.9rem;"></i>
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:0.9rem;"></i>
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:0.9rem;"></i>
            </div>
            <p style="font-size:0.9rem; line-height:1.6; color:var(--ink, #12213a); margin:0 0 15px 0; flex:1;">"The construction quality is excellent and the team is very responsive."</p>
            <div style="display:flex; align-items:center; gap:12px; border-top:1px solid var(--line, #e7e9ee); padding-top:14px;">
                <div style="width:40px; height:40px; border-radius:50%; background:#e67e22; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:0.85rem; flex-shrink:0;">MK</div>
                <div>
                    <div style="font-weight:600; font-size:0.9rem; color:var(--ink, #12213a);">Meera Krishnan</div>
                    <div style="font-size:0.75rem; color:var(--muted, #6b7280);">Homeowner, 2 BHK</div>
                </div>
            </div>
        </div>

        <!-- Review 4 -->
        <div class="testimonial-card" style="background:var(--bg-soft, #f8f9fa); border-radius:12px; padding:20px; border:1px solid var(--line, #e7e9ee); transition:all 0.3s ease; display:flex; flex-direction:column;">
            <div style="display:flex; gap:2px; margin-bottom:12px;">
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:0.9rem;"></i>
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:0.9rem;"></i>
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:0.9rem;"></i>
                <i class="bi bi-star-fill" style="color:#f5a623; font-size:0.9rem;"></i>
                <i class="bi bi-star-half" style="color:#f5a623; font-size:0.9rem;"></i>
            </div>
            <p style="font-size:0.9rem; line-height:1.6; color:var(--ink, #12213a); margin:0 0 15px 0; flex:1;">"Good value for money with excellent connectivity to the city."</p>
            <div style="display:flex; align-items:center; gap:12px; border-top:1px solid var(--line, #e7e9ee); padding-top:14px;">
                <div style="width:40px; height:40px; border-radius:50%; background:#27ae60; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:0.85rem; flex-shrink:0;">RN</div>
                <div>
                    <div style="font-weight:600; font-size:0.9rem; color:var(--ink, #12213a);">Rahul Nair</div>
                    <div style="font-size:0.75rem; color:var(--muted, #6b7280);">Investor</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     FEEDBACK / REVIEW POPUP
     ============================================================ -->
<div class="feedback-overlay" id="feedbackOverlay">
    <div class="feedback-popup">
        <button class="feedback-popup-close" onclick="closeFeedbackPopup()">✕</button>
        
        <div class="feedback-popup-header">
            <h3 style="margin:0; font-size:1.3rem; font-weight:700; color:var(--ink);">Write a Review</h3>
            <p style="margin:0.3rem 0 0; font-size:0.85rem; color:var(--muted);">Share your experience with this project</p>
        </div>
        
        <div class="feedback-popup-body">
            <form id="feedbackForm">
                <input type="hidden" name="project_id" value="<?php echo $project['id'] ?? 0; ?>">
                
                <!-- Rating Stars -->
                <div class="form-group" style="margin-bottom:1.2rem;">
                    <label style="font-weight:600; font-size:0.9rem; display:block; margin-bottom:0.4rem; color:var(--ink);">Your Rating <span style="color:#e74c3c;">*</span></label>
                    <div class="rating-stars" id="ratingStars">
                        <i class="bi bi-star" data-value="1" onclick="setRating(1)"></i>
                        <i class="bi bi-star" data-value="2" onclick="setRating(2)"></i>
                        <i class="bi bi-star" data-value="3" onclick="setRating(3)"></i>
                        <i class="bi bi-star" data-value="4" onclick="setRating(4)"></i>
                        <i class="bi bi-star" data-value="5" onclick="setRating(5)"></i>
                    </div>
                    <input type="hidden" name="rating" id="selectedRating" value="5">
                    <span style="font-size:0.75rem; color:var(--muted);" id="ratingLabel">Excellent</span>
                </div>
                
                <!-- Name -->
                <div class="form-group" style="margin-bottom:1rem;">
                    <label style="font-weight:600; font-size:0.9rem; display:block; margin-bottom:0.3rem; color:var(--ink);">Your Name <span style="color:#e74c3c;">*</span></label>
                    <input type="text" name="user_name" id="feedbackName" placeholder="Enter your full name" required style="width:100%; padding:0.6rem 0.9rem; border:1px solid var(--line); border-radius:0.6rem; font-size:0.9rem; transition:border-color 0.3s ease; background:var(--bg-soft); color:var(--ink);">
                </div>
                
                <!-- Email -->
                <div class="form-group" style="margin-bottom:1rem;">
                    <label style="font-weight:600; font-size:0.9rem; display:block; margin-bottom:0.3rem; color:var(--ink);">Email Address</label>
                    <input type="email" name="user_email" id="feedbackEmail" placeholder="Enter your email address" style="width:100%; padding:0.6rem 0.9rem; border:1px solid var(--line); border-radius:0.6rem; font-size:0.9rem; transition:border-color 0.3s ease; background:var(--bg-soft); color:var(--ink);">
                </div>
                
                <!-- Category -->
                <div class="form-group" style="margin-bottom:1rem;">
                    <label style="font-weight:600; font-size:0.9rem; display:block; margin-bottom:0.3rem; color:var(--ink);">Category <span style="color:#e74c3c;">*</span></label>
                    <select name="category" id="feedbackCategory" required style="width:100%; padding:0.6rem 0.9rem; border:1px solid var(--line); border-radius:0.6rem; font-size:0.9rem; background:var(--bg-soft); color:var(--ink); cursor:pointer;">
                        <option value="">Select your category</option>
                        <option value="Homeowner">Homeowner</option>
                        <option value="Investor">Investor</option>
                        <option value="Dealer">Dealer</option>
                        <option value="Agent">Real Estate Agent</option>
                        <option value="Builder">Builder/Developer</option>
                        <option value="Visitor">Site Visitor</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <!-- Review Text -->
                <div class="form-group" style="margin-bottom:1.2rem;">
                    <label style="font-weight:600; font-size:0.9rem; display:block; margin-bottom:0.3rem; color:var(--ink);">Your Review <span style="color:#e74c3c;">*</span></label>
                    <textarea name="review_text" id="feedbackReview" rows="4" placeholder="Share your experience with this project..." required style="width:100%; padding:0.6rem 0.9rem; border:1px solid var(--line); border-radius:0.6rem; font-size:0.9rem; resize:vertical; transition:border-color 0.3s ease; background:var(--bg-soft); color:var(--ink); min-height:100px;"></textarea>
                </div>
                
                <!-- Submit Button -->
                <div id="feedbackMessage"></div>
                <button type="submit" class="feedback-submit-btn" style="background:var(--primary,#c9a84c); color:#fff; border:none; border-radius:0.6rem; padding:0.75rem; font-weight:700; font-size:1rem; width:100%; cursor:pointer; transition:all 0.3s ease; display:flex; align-items:center; justify-content:center; gap:0.5rem;">
                    <i class="bi bi-send"></i> Submit Review
                </button>
            </form>
        </div>
    </div>
</div>

<style>
/* ===== FEEDBACK POPUP STYLES ===== */
.feedback-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: 99999;
    display: none;
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(6px);
    padding: 20px;
}

.feedback-overlay.active {
    display: flex;
}

.feedback-popup {
    background: var(--card-bg, #fff);
    border-radius: 1rem;
    max-width: 520px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    box-shadow: 0 25px 70px rgba(0, 0, 0, 0.3);
    animation: feedbackSlideIn 0.35s ease;
}

@keyframes feedbackSlideIn {
    from {
        transform: translateY(30px) scale(0.96);
        opacity: 0;
    }
    to {
        transform: translateY(0) scale(1);
        opacity: 1;
    }
}

.feedback-popup-close {
    position: sticky;
    top: 0;
    right: 0;
    float: right;
    background: var(--card-bg, #fff);
    border: none;
    font-size: 1.3rem;
    padding: 0.7rem 1rem;
    cursor: pointer;
    z-index: 10;
    color: var(--ink, #12213a);
    border-radius: 0 1rem 0 0;
    transition: background 0.2s ease;
    line-height: 1;
}

.feedback-popup-close:hover {
    background: var(--bg-soft, #f7f8fa);
}

body.dark-mode .feedback-popup-close {
    background: var(--dm-bg-secondary, #161b22);
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode .feedback-popup-close:hover {
    background: var(--dm-bg-tertiary, #1c2333);
}

.feedback-popup-header {
    padding: 0.5rem 1.8rem 0.8rem 1.8rem;
    border-bottom: 1px solid var(--line, #e7e9ee);
}

.feedback-popup-body {
    padding: 1.5rem 1.8rem 1.8rem;
}

/* Rating Stars */
.rating-stars {
    display: flex;
    gap: 0.4rem;
    font-size: 1.8rem;
    cursor: pointer;
}

.rating-stars i {
    color: #d1d5db;
    transition: all 0.2s ease;
}

.rating-stars i.active {
    color: #f5a623;
}

.rating-stars i:hover {
    transform: scale(1.15);
}

#ratingLabel {
    display: block;
    margin-top: 0.3rem;
    font-weight: 600;
}

/* Form inputs dark mode */
body.dark-mode .feedback-popup {
    background: var(--dm-bg-secondary, #161b22);
}

body.dark-mode .feedback-popup-header {
    border-color: var(--dm-border, #30363d);
}

body.dark-mode .feedback-popup-header h3 {
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode .feedback-popup-header p {
    color: var(--dm-text-secondary, #8b949e);
}

body.dark-mode #feedbackName,
body.dark-mode #feedbackEmail,
body.dark-mode #feedbackCategory,
body.dark-mode #feedbackReview {
    background: var(--dm-bg-tertiary, #1c2333);
    border-color: var(--dm-border, #30363d);
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode #feedbackName:focus,
body.dark-mode #feedbackEmail:focus,
body.dark-mode #feedbackCategory:focus,
body.dark-mode #feedbackReview:focus {
    border-color: var(--primary, #c9a84c);
    box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.12);
}

body.dark-mode #feedbackName::placeholder,
body.dark-mode #feedbackEmail::placeholder,
body.dark-mode #feedbackReview::placeholder {
    color: var(--dm-text-muted, #6b7686);
}

body.dark-mode .rating-stars i {
    color: var(--dm-border, #30363d);
}

body.dark-mode .rating-stars i.active {
    color: #f5a623;
}

/* Testimonial card hover effect */
.testimonial-card:hover {
    border-color: var(--primary, #c9a84c) !important;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}

body.dark-mode .testimonial-card {
    background: var(--dm-bg-secondary, #161b22) !important;
    border-color: var(--dm-border, #30363d) !important;
}

body.dark-mode .testimonial-card p {
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode .testimonial-card .name {
    color: var(--dm-text-primary, #f0f6fc);
}

/* Responsive */
@media (max-width: 991.98px) {
    #reviews > div:last-child {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

@media (max-width: 575.98px) {
    .feedback-popup {
        max-width: 100%;
        max-height: 100vh;
        border-radius: 0;
        margin: 0;
        height: 100vh;
    }
    
    .feedback-popup-header {
        padding: 0.5rem 1rem 0.8rem 1rem;
    }
    
    .feedback-popup-body {
        padding: 1rem 1rem 1.5rem;
    }
    
    .rating-stars {
        font-size: 1.5rem;
    }
    
    #reviews > div:last-child {
        grid-template-columns: 1fr !important;
        gap: 15px !important;
    }
    
    #reviews {
        padding: 20px 15px !important;
    }
    
    #reviews > div:first-child {
        flex-direction: column !important;
        align-items: flex-start !important;
    }
}
</style>

<script>
// ===== FEEDBACK POPUP FUNCTIONS =====
var selectedRating = 5;

function openFeedbackPopup() {
    document.getElementById('feedbackOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeFeedbackPopup() {
    document.getElementById('feedbackOverlay').classList.remove('active');
    document.body.style.overflow = '';
    // Reset form
    document.getElementById('feedbackForm').reset();
    selectedRating = 5;
    setRating(5);
    document.getElementById('feedbackMessage').innerHTML = '';
}

function setRating(value) {
    selectedRating = value;
    document.getElementById('selectedRating').value = value;
    
    var stars = document.querySelectorAll('#ratingStars i');
    stars.forEach(function(star) {
        var starValue = parseInt(star.dataset.value);
        if (starValue <= value) {
            star.className = 'bi bi-star-fill active';
        } else {
            star.className = 'bi bi-star';
        }
    });
    
    var labels = {
        1: 'Terrible',
        2: 'Poor',
        3: 'Average',
        4: 'Good',
        5: 'Excellent'
    };
    document.getElementById('ratingLabel').textContent = labels[value] || 'Excellent';
}

// Click on overlay to close
document.addEventListener('DOMContentLoaded', function() {
    var overlay = document.getElementById('feedbackOverlay');
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeFeedbackPopup();
            }
        });
    }
});

// Escape key to close
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFeedbackPopup();
    }
});

// ===== FEEDBACK FORM SUBMISSION =====
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('feedbackForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(form);
            var messageDiv = document.getElementById('feedbackMessage');
            var submitBtn = form.querySelector('.feedback-submit-btn');
            
            // Validate
            var name = document.getElementById('feedbackName').value.trim();
            var review = document.getElementById('feedbackReview').value.trim();
            var category = document.getElementById('feedbackCategory').value;
            
            if (!name) {
                messageDiv.innerHTML = '<div class="alert alert-danger" style="padding:0.5rem 0.8rem; border-radius:0.4rem; font-size:0.85rem; background:#fdeef0; color:#b6435a; margin-bottom:0.8rem;">Please enter your name.</div>';
                document.getElementById('feedbackName').focus();
                return;
            }
            
            if (!category) {
                messageDiv.innerHTML = '<div class="alert alert-danger" style="padding:0.5rem 0.8rem; border-radius:0.4rem; font-size:0.85rem; background:#fdeef0; color:#b6435a; margin-bottom:0.8rem;">Please select a category.</div>';
                document.getElementById('feedbackCategory').focus();
                return;
            }
            
            if (!review || review.length < 10) {
                messageDiv.innerHTML = '<div class="alert alert-danger" style="padding:0.5rem 0.8rem; border-radius:0.4rem; font-size:0.85rem; background:#fdeef0; color:#b6435a; margin-bottom:0.8rem;">Please write a review (minimum 10 characters).</div>';
                document.getElementById('feedbackReview').focus();
                return;
            }
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Submitting...';
            
            fetch('<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>/submit-review.php', {
                method: 'POST',
                body: formData
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.status === 'success') {
                    messageDiv.innerHTML = '<div class="alert alert-success" style="padding:0.5rem 0.8rem; border-radius:0.4rem; font-size:0.85rem; background:#e6f6ec; color:#1a8f4c; margin-bottom:0.8rem;">' + data.message + '</div>';
                    form.reset();
                    setRating(5);
                    setTimeout(function() {
                        closeFeedbackPopup();
                    }, 3000);
                } else {
                    messageDiv.innerHTML = '<div class="alert alert-danger" style="padding:0.5rem 0.8rem; border-radius:0.4rem; font-size:0.85rem; background:#fdeef0; color:#b6435a; margin-bottom:0.8rem;">' + data.message + '</div>';
                }
            })
            .catch(function(error) {
                messageDiv.innerHTML = '<div class="alert alert-danger" style="padding:0.5rem 0.8rem; border-radius:0.4rem; font-size:0.85rem; background:#fdeef0; color:#b6435a; margin-bottom:0.8rem;">Something went wrong. Please try again.</div>';
            })
            .finally(function() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-send"></i> Submit Review';
            });
        });
    }
});
</script>