<?php
/**
 * Sub-top navigation bar for Seller Dashboard pages
 */
if (!isset($lastVisitedTime)) {
    $lastVisitedTime = '';
    
    // Fetch last visited timestamp from backend session or database
    if (!empty($_SESSION['last_visited'])) {
        $lastVisitedTime = $_SESSION['last_visited'];
    } elseif (!empty($_SESSION['last_login'])) {
        $lastVisitedTime = date('h:i A | d M, Y', strtotime($_SESSION['last_login']));
    } elseif (isset($conn) && isset($_SESSION['user_id'])) {
        $uid = (int) $_SESSION['user_id'];
        $lvQuery = $conn->query("SELECT last_login FROM users WHERE id = $uid LIMIT 1");
        if ($lvQuery && $lvRow = $lvQuery->fetch_assoc()) {
            if (!empty($lvRow['last_login'])) {
                $lastVisitedTime = date('h:i A | d M, Y', strtotime($lvRow['last_login']));
            }
        }
    }
}

// Fallback if no prior visit record
if (empty($lastVisitedTime)) {
    $lastVisitedTime = date('h:i A | d M, Y');
}
?>
<div class="seller-subnav-bar">
    <div class="seller-last-visited">
        <span>Last Visited: <?php echo htmlspecialchars($lastVisitedTime); ?></span>
    </div>
    <div class="seller-subnav-actions">
        <a href="javascript:void(0)" class="seller-subnav-link">Buy Our Services</a>
        <span class="seller-subnav-divider">|</span>
        <a href="javascript:void(0)" class="seller-subnav-link">Post a Property</a>
        <span class="seller-subnav-divider">|</span>
        <a href="javascript:void(0)" class="seller-subnav-link">Customer Service</a>
    </div>
</div>
