<?php
// ===== PROPERTY RECOMMENDATIONS =====
$propertyRecommendations = [];
if ($project && !empty($project['city'])) {
    $rec_query = "SELECT id, project_name, slug, address, city, price, image, category_type 
                  FROM trending_projects 
                  WHERE status = 'Active' 
                  AND city = '" . mysqli_real_escape_string($conn, $project['city']) . "' 
                  AND id != " . (int)$project['id'] . " 
                  ORDER BY RAND() 
                  LIMIT 10";
    $rec_result = mysqli_query($conn, $rec_query);
    while ($row = mysqli_fetch_assoc($rec_result)) {
        $propertyRecommendations[] = $row;
    }
}
?>

<!-- ===== PROPERTY RECOMMENDATIONS ===== -->
<?php if (!empty($propertyRecommendations)): ?>
<div class="card-panel mb-4">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
        <h4 style="font-size:1.1rem; font-weight:700; color:var(--ink); margin:0;">
            <i class="bi bi-star-fill" style="color:#f5a623; margin-right:0.5rem;"></i>
            Property Recommendations
        </h4>
        <a href="#" style="font-size:0.8rem; color:var(--accent, #f5a623); text-decoration:none; font-weight:600;">
            View All <i class="bi bi-chevron-right" style="font-size:0.7rem;"></i>
        </a>
    </div>

    <div class="rec-slider-wrapper" style="position:relative; overflow:hidden; border-radius:0.75rem;">
        <div class="rec-slider" id="recSlider" style="display:flex; transition:transform 0.5s ease-in-out; gap:0;">
            <?php foreach ($propertyRecommendations as $rec): 
                // Image handling with placeholder fallback
                $rec_image = '';
                if (!empty($rec['image'])) {
                    $fullImagePath = "../backend/uploads/trending-projects/" . $rec['image'];
                    if (file_exists($fullImagePath) && is_file($fullImagePath)) {
                        $rec_image = $fullImagePath;
                    } else {
                        $altImagePath = "backend/uploads/trending-projects/" . $rec['image'];
                        if (file_exists($altImagePath) && is_file($altImagePath)) {
                            $rec_image = $altImagePath;
                        } else {
                            $rec_image = 'https://placehold.co/400x300/f5a623/ffffff?text=' . urlencode(substr($rec['project_name'], 0, 20));
                        }
                    }
                } else {
                    $rec_image = 'https://placehold.co/400x300/f5a623/ffffff?text=' . urlencode(substr($rec['project_name'], 0, 20));
                }
                
                $short_name = strlen($rec['project_name']) > 25 ? substr($rec['project_name'], 0, 25) . '...' : $rec['project_name'];
                $short_address = strlen($rec['address']) > 30 ? substr($rec['address'], 0, 30) . '...' : $rec['address'];
            ?>
            <div class="rec-slide" style="flex:0 0 100%; min-width:100%; background:var(--card-bg); border:1px solid var(--line, #e0e0e0); border-radius:0.75rem; overflow:hidden; transition:all 0.3s ease; cursor:pointer;" 
                 onclick="window.location.href='property-listing/<?php echo urlencode($rec['slug']); ?>/'">
                <div style="position:relative; height:180px; background:#f0f0f0; overflow:hidden;">
                    <img src="<?php echo htmlspecialchars($rec_image); ?>" 
                         alt="<?php echo htmlspecialchars($rec['project_name']); ?>" 
                         style="width:100%; height:100%; object-fit:cover; transition:transform 0.3s ease;"
                         onerror="this.style.display='none'; this.parentElement.style.background='var(--bg-soft)'; this.parentElement.innerHTML='<div style=\'display:flex; align-items:center; justify-content:center; height:100%; font-size:2.5rem; color:var(--muted);\'><i class=\'bi bi-building\'></i></div>'">
                    <span style="position:absolute; top:0.75rem; left:0.75rem; background:rgba(0,0,0,0.7); color:#fff; padding:0.15rem 0.6rem; border-radius:1rem; font-size:0.55rem; font-weight:600; backdrop-filter:blur(4px);">
                        <?php 
                        $cat_label = !empty($rec['category_type']) ? ucfirst($rec['category_type']) : 'Property';
                        echo htmlspecialchars($cat_label);
                        ?>
                    </span>
                    <?php if (!empty($rec['price']) && $rec['price'] != '0'): ?>
                    <span style="position:absolute; bottom:0.75rem; right:0.75rem; background:rgba(0,0,0,0.8); color:#fff; padding:0.3rem 0.7rem; border-radius:0.5rem; font-size:0.7rem; font-weight:700; backdrop-filter:blur(4px);">
                        <?php echo htmlspecialchars($rec['price']); ?>
                    </span>
                    <?php endif; ?>
                </div>
                <div class="rec-body" style="padding:0.9rem 1rem 1.1rem;">
                    <h3 style="font-size:1.02rem; font-weight:700; margin-bottom:0.25rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:var(--ink);">
                        <?php echo htmlspecialchars($short_name); ?>
                    </h3>
                    <div style="color:var(--muted); font-size:0.86rem; margin-bottom:0.5rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($short_address); ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($propertyRecommendations) > 1): ?>
        <button class="rec-slider-btn prev" style="position:absolute; top:50%; left:0.25rem; transform:translateY(-50%); background:rgba(255,255,255,0.9); border:none; border-radius:50%; width:34px; height:34px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.15); z-index:2; transition:all 0.3s ease; color:var(--ink); border:1px solid var(--line);" onclick="slideRec(-1)">
            <i class="bi bi-chevron-left" style="font-size:1rem;"></i>
        </button>
        <button class="rec-slider-btn next" style="position:absolute; top:50%; right:0.25rem; transform:translateY(-50%); background:rgba(255,255,255,0.9); border:none; border-radius:50%; width:34px; height:34px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.15); z-index:2; transition:all 0.3s ease; color:var(--ink); border:1px solid var(--line);" onclick="slideRec(1)">
            <i class="bi bi-chevron-right" style="font-size:1rem;"></i>
        </button>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- ===== HOME LOANS | LOANS ===== -->
<?php
// First try to get real data from database
$loans_query = "SELECT id, project_name, slug, address, city, price, image, category_type, 
                amenities 
                FROM trending_projects 
                WHERE status = 'Active' 
                AND category_type = 'home_services'
                AND (amenities LIKE '%loan%' OR amenities LIKE '%finance%' OR amenities LIKE '%bank%' OR amenities LIKE '%mortgage%')
                ORDER BY created_at DESC 
                LIMIT 10";
$loans_result = mysqli_query($conn, $loans_query);
$loans = [];
while ($row = mysqli_fetch_assoc($loans_result)) {
    $loans[] = $row;
}

// If no real data found, use dummy data
if (empty($loans)) {
    $loans = [
        [
            'id' => 1, 
            'project_name' => 'SBI Home Loans', 
            'slug' => 'sbi-home-loans', 
            'address' => 'Pan India', 
            'city' => 'Mumbai', 
            'price' => '8.5% p.a.', 
            'image' => '', 
            'category_type' => 'home_services', 
            'amenities' => 'loan, finance'
        ],
        [
            'id' => 2, 
            'project_name' => 'HDFC Home Loans', 
            'slug' => 'hdfc-home-loans', 
            'address' => 'Pan India', 
            'city' => 'Delhi', 
            'price' => '8.7% p.a.', 
            'image' => '', 
            'category_type' => 'home_services', 
            'amenities' => 'loan, finance'
        ],
        [
            'id' => 3, 
            'project_name' => 'Axis Bank Home Loans', 
            'slug' => 'axis-bank-home-loans', 
            'address' => 'Pan India', 
            'city' => 'Bangalore', 
            'price' => '8.6% p.a.', 
            'image' => '', 
            'category_type' => 'home_services', 
            'amenities' => 'loan, bank'
        ],
        [
            'id' => 4, 
            'project_name' => 'LIC Housing Finance', 
            'slug' => 'lic-housing-finance', 
            'address' => 'Pan India', 
            'city' => 'Hyderabad', 
            'price' => '8.4% p.a.', 
            'image' => '', 
            'category_type' => 'home_services', 
            'amenities' => 'loan, finance'
        ],
        [
            'id' => 5, 
            'project_name' => 'Bajaj Housing Finance', 
            'slug' => 'bajaj-housing-finance', 
            'address' => 'Pan India', 
            'city' => 'Pune', 
            'price' => '8.9% p.a.', 
            'image' => '', 
            'category_type' => 'home_services', 
            'amenities' => 'loan, finance'
        ],
        [
            'id' => 6, 
            'project_name' => 'ICICI Home Loans', 
            'slug' => 'icici-home-loans', 
            'address' => 'Pan India', 
            'city' => 'Chennai', 
            'price' => '8.8% p.a.', 
            'image' => '', 
            'category_type' => 'home_services', 
            'amenities' => 'loan, bank'
        ],
        [
            'id' => 7, 
            'project_name' => 'Kotak Mahindra Home Loans', 
            'slug' => 'kotak-mahindra-home-loans', 
            'address' => 'Pan India', 
            'city' => 'Ahmedabad', 
            'price' => '8.3% p.a.', 
            'image' => '', 
            'category_type' => 'home_services', 
            'amenities' => 'loan, finance'
        ],
        [
            'id' => 8, 
            'project_name' => 'PNB Housing Finance', 
            'slug' => 'pnb-housing-finance', 
            'address' => 'Pan India', 
            'city' => 'Kolkata', 
            'price' => '8.2% p.a.', 
            'image' => '', 
            'category_type' => 'home_services', 
            'amenities' => 'loan, bank'
        ]
    ];
}
?>

<div class="card-panel mb-4">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
        <h4 style="font-size:1.1rem; font-weight:700; color:var(--ink); margin:0;">
            <i class="bi bi-bank" style="color:#f5a623; margin-right:0.5rem;"></i>
            Home Loans | Loans
        </h4>
        <a href="#" style="font-size:0.8rem; color:var(--accent, #f5a623); text-decoration:none; font-weight:600;">
            View All <i class="bi bi-chevron-right" style="font-size:0.7rem;"></i>
        </a>
    </div>

    <div class="loan-slider-wrapper" style="position:relative; overflow:hidden; border-radius:0.75rem;">
        <div class="loan-slider" id="loanSlider" style="display:flex; transition:transform 0.5s ease-in-out; gap:0;">
            <?php foreach ($loans as $loan): 
                // Image handling with placeholder fallback
                $loan_image = '';
                if (!empty($loan['image'])) {
                    $fullImagePath = "../backend/uploads/trending-projects/" . $loan['image'];
                    if (file_exists($fullImagePath) && is_file($fullImagePath)) {
                        $loan_image = $fullImagePath;
                    } else {
                        $altImagePath = "backend/uploads/trending-projects/" . $loan['image'];
                        if (file_exists($altImagePath) && is_file($altImagePath)) {
                            $loan_image = $altImagePath;
                        } else {
                            $loan_image = 'https://placehold.co/400x300/1a73e8/ffffff?text=' . urlencode(substr($loan['project_name'], 0, 15));
                        }
                    }
                } else {
                    // Bank-specific placeholder colors based on name
                    $bankColors = [
                        'sbi' => '1a73e8',
                        'hdfc' => 'e65100',
                        'axis' => 'd32f2f',
                        'lic' => '388e3c',
                        'bajaj' => 'e65100',
                        'icici' => 'd32f2f',
                        'kotak' => '1565c0',
                        'pnb' => '4a148c'
                    ];
                    $bankKey = strtolower(substr($loan['project_name'], 0, 4));
                    $color = isset($bankColors[$bankKey]) ? $bankColors[$bankKey] : '1a73e8';
                    $loan_image = 'https://placehold.co/400x300/' . $color . '/ffffff?text=' . urlencode(substr($loan['project_name'], 0, 15));
                }
                
                $short_name = strlen($loan['project_name']) > 25 ? substr($loan['project_name'], 0, 25) . '...' : $loan['project_name'];
                $short_address = strlen($loan['address']) > 30 ? substr($loan['address'], 0, 30) . '...' : $loan['address'];
            ?>
            <div class="loan-slide" style="flex:0 0 100%; min-width:100%; background:var(--card-bg); border:1px solid var(--line, #e0e0e0); border-radius:0.75rem; overflow:hidden; transition:all 0.3s ease; cursor:pointer;" 
                 onclick="window.location.href='property-listing/<?php echo urlencode($loan['slug']); ?>/'">
                <div style="position:relative; height:180px; background:#f0f0f0; overflow:hidden;">
                    <img src="<?php echo htmlspecialchars($loan_image); ?>" 
                         alt="<?php echo htmlspecialchars($loan['project_name']); ?>" 
                         style="width:100%; height:100%; object-fit:cover; transition:transform 0.3s ease;"
                         onerror="this.style.display='none'; this.parentElement.style.background='var(--bg-soft)'; this.parentElement.innerHTML='<div style=\'display:flex; align-items:center; justify-content:center; height:100%; font-size:2.5rem; color:var(--muted);\'><i class=\'bi bi-bank\'></i></div>'">
                    <?php if (!empty($loan['price']) && $loan['price'] != '0'): ?>
                    <span style="position:absolute; top:0.75rem; left:0.75rem; background:rgba(26, 115, 232, 0.9); color:#fff; padding:0.15rem 0.6rem; border-radius:1rem; font-size:0.55rem; font-weight:600; backdrop-filter:blur(4px);">
                        <?php echo htmlspecialchars($loan['price']); ?>
                    </span>
                    <?php endif; ?>
                </div>
                <div class="loan-body" style="padding:0.9rem 1rem 1.1rem;">
                    <h3 style="font-size:1.02rem; font-weight:700; margin-bottom:0.25rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:var(--ink);">
                        <?php echo htmlspecialchars($short_name); ?>
                    </h3>
                    <div style="color:var(--muted); font-size:0.86rem; margin-bottom:0.5rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($short_address); ?>
                    </div>
                    <?php if (!empty($loan['city']) && $loan['city'] != '0'): ?>
                    <div style="color:var(--muted); font-size:0.8rem;">
                        <i class="bi bi-building"></i> <?php echo htmlspecialchars($loan['city']); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($loans) > 1): ?>
        <button class="loan-slider-btn prev" style="position:absolute; top:50%; left:0.25rem; transform:translateY(-50%); background:rgba(255,255,255,0.9); border:none; border-radius:50%; width:34px; height:34px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.15); z-index:2; transition:all 0.3s ease; color:var(--ink); border:1px solid var(--line);" onclick="slideLoan(-1)">
            <i class="bi bi-chevron-left" style="font-size:1rem;"></i>
        </button>
        <button class="loan-slider-btn next" style="position:absolute; top:50%; right:0.25rem; transform:translateY(-50%); background:rgba(255,255,255,0.9); border:none; border-radius:50%; width:34px; height:34px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.15); z-index:2; transition:all 0.3s ease; color:var(--ink); border:1px solid var(--line);" onclick="slideLoan(1)">
            <i class="bi bi-chevron-right" style="font-size:1rem;"></i>
        </button>
        <?php endif; ?>
    </div>
</div>

<!-- ===== INTERIORS ===== -->
<?php if (!$isInteriorProject): ?>
<?php
$interiors_query = "SELECT id, project_name, slug, address, city, price, image, category_type, 
                    amenities 
                    FROM trending_projects 
                    WHERE status = 'Active' 
                    AND category_type = 'home_services'
                    AND (amenities LIKE '%interior%' OR amenities LIKE '%furniture%' OR amenities LIKE '%design%' OR amenities LIKE '%decoration%')
                    ORDER BY created_at DESC 
                    LIMIT 10";
$interiors_result = mysqli_query($conn, $interiors_query);
$interiors = [];
while ($row = mysqli_fetch_assoc($interiors_result)) {
    $interiors[] = $row;
}
?>

<div class="card-panel mb-4">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
        <h4 style="font-size:1.1rem; font-weight:700; color:var(--ink); margin:0;">
            <i class="bi bi-palette-fill" style="color:#f5a623; margin-right:0.5rem;"></i>
            Interiors
        </h4>
        <a href="#" style="font-size:0.8rem; color:var(--accent, #f5a623); text-decoration:none; font-weight:600;">
            View All <i class="bi bi-chevron-right" style="font-size:0.7rem;"></i>
        </a>
    </div>

    <?php if (!empty($interiors)): ?>
    <div class="interior-slider-wrapper" style="position:relative; overflow:hidden; border-radius:0.75rem;">
        <div class="interior-slider" id="interiorSlider" style="display:flex; transition:transform 0.5s ease-in-out; gap:0;">
            <?php foreach ($interiors as $interior): 
                // Image handling with placeholder fallback
                $interior_image = '';
                if (!empty($interior['image'])) {
                    $fullImagePath = "../backend/uploads/trending-projects/" . $interior['image'];
                    if (file_exists($fullImagePath) && is_file($fullImagePath)) {
                        $interior_image = $fullImagePath;
                    } else {
                        $altImagePath = "backend/uploads/trending-projects/" . $interior['image'];
                        if (file_exists($altImagePath) && is_file($altImagePath)) {
                            $interior_image = $altImagePath;
                        } else {
                            $interior_image = 'https://placehold.co/400x300/e67e22/ffffff?text=' . urlencode(substr($interior['project_name'], 0, 15));
                        }
                    }
                } else {
                    $interior_image = 'https://placehold.co/400x300/e67e22/ffffff?text=' . urlencode(substr($interior['project_name'], 0, 15));
                }
                
                $short_name = strlen($interior['project_name']) > 25 ? substr($interior['project_name'], 0, 25) . '...' : $interior['project_name'];
                $short_address = strlen($interior['address']) > 30 ? substr($interior['address'], 0, 30) . '...' : $interior['address'];
            ?>
            <div class="interior-slide" style="flex:0 0 100%; min-width:100%; background:var(--card-bg); border:1px solid var(--line, #e0e0e0); border-radius:0.75rem; overflow:hidden; transition:all 0.3s ease; cursor:pointer;" 
                 onclick="window.location.href='property-listing/<?php echo urlencode($interior['slug']); ?>/'">
                <div style="position:relative; height:180px; background:#f0f0f0; overflow:hidden;">
                    <img src="<?php echo htmlspecialchars($interior_image); ?>" 
                         alt="<?php echo htmlspecialchars($interior['project_name']); ?>" 
                         style="width:100%; height:100%; object-fit:cover; transition:transform 0.3s ease;"
                         onerror="this.style.display='none'; this.parentElement.style.background='var(--bg-soft)'; this.parentElement.innerHTML='<div style=\'display:flex; align-items:center; justify-content:center; height:100%; font-size:2.5rem; color:var(--muted);\'><i class=\'bi bi-palette-fill\'></i></div>'">
                    <?php if (!empty($interior['price']) && $interior['price'] != '0'): ?>
                    <span style="position:absolute; top:0.75rem; left:0.75rem; background:rgba(230, 126, 34, 0.9); color:#fff; padding:0.15rem 0.6rem; border-radius:1rem; font-size:0.55rem; font-weight:600; backdrop-filter:blur(4px);">
                        <?php echo htmlspecialchars($interior['price']); ?>
                    </span>
                    <?php endif; ?>
                </div>
                <div class="interior-body" style="padding:0.9rem 1rem 1.1rem;">
                    <h3 style="font-size:1.02rem; font-weight:700; margin-bottom:0.25rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:var(--ink);">
                        <?php echo htmlspecialchars($short_name); ?>
                    </h3>
                    <div style="color:var(--muted); font-size:0.86rem; margin-bottom:0.5rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($short_address); ?>
                    </div>
                    <?php if (!empty($interior['city']) && $interior['city'] != '0'): ?>
                    <div style="color:var(--muted); font-size:0.8rem;">
                        <i class="bi bi-building"></i> <?php echo htmlspecialchars($interior['city']); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($interiors) > 1): ?>
        <button class="interior-slider-btn prev" style="position:absolute; top:50%; left:0.25rem; transform:translateY(-50%); background:rgba(255,255,255,0.9); border:none; border-radius:50%; width:34px; height:34px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.15); z-index:2; transition:all 0.3s ease; color:var(--ink); border:1px solid var(--line);" onclick="slideInterior(-1)">
            <i class="bi bi-chevron-left" style="font-size:1rem;"></i>
        </button>
        <button class="interior-slider-btn next" style="position:absolute; top:50%; right:0.25rem; transform:translateY(-50%); background:rgba(255,255,255,0.9); border:none; border-radius:50%; width:34px; height:34px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.15); z-index:2; transition:all 0.3s ease; color:var(--ink); border:1px solid var(--line);" onclick="slideInterior(1)">
            <i class="bi bi-chevron-right" style="font-size:1rem;"></i>
        </button>
        <?php endif; ?>
    </div>
        <?php else: ?>
    <div style="text-align:center; padding:1.5rem 1rem; color:var(--muted);">
        <i class="bi bi-palette-fill" style="font-size:1.5rem; display:block; margin-bottom:0.5rem; opacity:0.3;"></i>
        <span style="font-size:0.85rem;">No interior services available at the moment.</span>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- ===== LOCAL SERVICES ===== -->
<?php
$localServiceKeywords = ['plumbing', 'electrical', 'paint', 'cleaning', 'pest', 'ac', 'carpentry', 'handyman'];
$isLocalServiceProject = false;

if ($project && !empty($project['category_type'])) {
    $catType = strtolower(trim($project['category_type']));

    if ($catType === 'local_services') {
        // Direct match — this IS a local service listing
        $isLocalServiceProject = true;
    } elseif ($catType === 'home_services' && !empty($project['amenities'])) {
        // Fallback: some listings use 'home_services' + amenity keywords
        foreach ($localServiceKeywords as $kw) {
            if (stripos($project['amenities'], $kw) !== false) {
                $isLocalServiceProject = true;
                break;
            }
        }
    }
}
?>
<?php if (!$isLocalServiceProject): ?>
<?php
$local_service_query = "SELECT id, project_name, slug, address, city, price, image, category_type, 
                        amenities 
                        FROM trending_projects 
                        WHERE status = 'Active' 
                        AND category_type = 'home_services'
                        AND (amenities LIKE '%plumbing%' OR amenities LIKE '%electrical%' OR amenities LIKE '%paint%' 
                        OR amenities LIKE '%cleaning%' OR amenities LIKE '%pest%' OR amenities LIKE '%ac%' 
                        OR amenities LIKE '%carpentry%' OR amenities LIKE '%handyman%')
                        AND slug != '" . mysqli_real_escape_string($conn, $project['slug']) . "'
                        ORDER BY created_at DESC 
                        LIMIT 10";
$local_service_result = mysqli_query($conn, $local_service_query);
$local_services = [];
while ($row = mysqli_fetch_assoc($local_service_result)) {
    $local_services[] = $row;
}
?>

<div class="card-panel mb-4">
    
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
        <h4 style="font-size:1.1rem; font-weight:700; color:var(--ink); margin:0;">
            <i class="bi bi-tools" style="color:#f5a623; margin-right:0.5rem;"></i>
            Local Services
        </h4>
        <a href="#" style="font-size:0.8rem; color:var(--accent, #f5a623); text-decoration:none; font-weight:600;">
            View All <i class="bi bi-chevron-right" style="font-size:0.7rem;"></i>
        </a>
    </div>

    <?php if (!empty($local_services)): ?>
    <div class="service-slider-wrapper" style="position:relative; overflow:hidden; border-radius:0.75rem;">
        <div class="service-slider" id="serviceSlider" style="display:flex; transition:transform 0.5s ease-in-out; gap:0;">
            <?php foreach ($local_services as $service): 
                // Image handling with placeholder fallback
                $service_image = '';
                if (!empty($service['image'])) {
                    $fullImagePath = "../backend/uploads/trending-projects/" . $service['image'];
                    if (file_exists($fullImagePath) && is_file($fullImagePath)) {
                        $service_image = $fullImagePath;
                    } else {
                        $altImagePath = "backend/uploads/trending-projects/" . $service['image'];
                        if (file_exists($altImagePath) && is_file($altImagePath)) {
                            $service_image = $altImagePath;
                        } else {
                            $service_image = 'https://placehold.co/400x300/f5a623/ffffff?text=' . urlencode(substr($service['project_name'], 0, 20));
                        }
                    }
                } else {
                    $service_image = 'https://placehold.co/400x300/f5a623/ffffff?text=' . urlencode(substr($service['project_name'], 0, 20));
                }
                
                $short_name = strlen($service['project_name']) > 25 ? substr($service['project_name'], 0, 25) . '...' : $service['project_name'];
                $short_address = strlen($service['address']) > 30 ? substr($service['address'], 0, 30) . '...' : $service['address'];
            ?>
            <div class="service-slide" style="flex:0 0 100%; min-width:100%; background:var(--card-bg); border:1px solid var(--line, #e0e0e0); border-radius:0.75rem; overflow:hidden; transition:all 0.3s ease; cursor:pointer;" 
                 onclick="window.location.href='property-listing/<?php echo urlencode($service['slug']); ?>/'">
                <div style="position:relative; height:180px; background:#f0f0f0; overflow:hidden;">
                    <img src="<?php echo htmlspecialchars($service_image); ?>" 
                         alt="<?php echo htmlspecialchars($service['project_name']); ?>" 
                         style="width:100%; height:100%; object-fit:cover; transition:transform 0.3s ease;"
                         onerror="this.style.display='none'; this.parentElement.style.background='var(--bg-soft)'; this.parentElement.innerHTML='<div style=\'display:flex; align-items:center; justify-content:center; height:100%; font-size:2.5rem; color:var(--muted);\'><i class=\'bi bi-tools\'></i></div>'">
                    <?php if (!empty($service['price']) && $service['price'] != '0'): ?>
                    <span style="position:absolute; bottom:0.75rem; right:0.75rem; background:rgba(0,0,0,0.8); color:#fff; padding:0.3rem 0.7rem; border-radius:0.5rem; font-size:0.7rem; font-weight:700; backdrop-filter:blur(4px);">
                        <?php echo htmlspecialchars($service['price']); ?>
                    </span>
                    <?php endif; ?>
                </div>
                <div class="service-body" style="padding:0.9rem 1rem 1.1rem;">
                    <h3 style="font-size:1.02rem; font-weight:700; margin-bottom:0.25rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:var(--ink);">
                        <?php echo htmlspecialchars($short_name); ?>
                    </h3>
                    <div style="color:var(--muted); font-size:0.86rem; margin-bottom:0.5rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($short_address); ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($local_services) > 1): ?>
        <button class="service-slider-btn prev" style="position:absolute; top:50%; left:0.25rem; transform:translateY(-50%); background:rgba(255,255,255,0.9); border:none; border-radius:50%; width:34px; height:34px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.15); z-index:2; transition:all 0.3s ease; color:var(--ink); border:1px solid var(--line);" onclick="slideService(-1)">
            <i class="bi bi-chevron-left" style="font-size:1rem;"></i>
        </button>
        <button class="service-slider-btn next" style="position:absolute; top:50%; right:0.25rem; transform:translateY(-50%); background:rgba(255,255,255,0.9); border:none; border-radius:50%; width:34px; height:34px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.15); z-index:2; transition:all 0.3s ease; color:var(--ink); border:1px solid var(--line);" onclick="slideService(1)">
            <i class="bi bi-chevron-right" style="font-size:1rem;"></i>
        </button>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div style="text-align:center; padding:1.5rem 1rem; color:var(--muted);">
        <i class="bi bi-tools" style="font-size:1.5rem; display:block; margin-bottom:0.5rem; opacity:0.3;"></i>
        <span style="font-size:0.85rem;">No local services available at the moment.</span>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- ===== VENDORS ===== -->
<?php
$isVendorProject = false;
if ($project) {
    // Vendors table records have 'category' but no 'category_type' field
    if (isset($project['category']) && !isset($project['category_type'])) {
        $isVendorProject = true;
    }
    // Fallback: slug suffix used in vendor listing URLs
    if (!empty($project['slug']) && preg_match('/-vnd$/', $project['slug'])) {
        $isVendorProject = true;
    }
}
?>
<?php if (!$isVendorProject): ?>
<?php
$vendors_query = "SELECT id, name, slug, category, city, address, phone, 
                  experience_years, rating, starting_price, image, description 
                  FROM vendors 
                  WHERE status = 'Active' 
                  AND slug != '" . mysqli_real_escape_string($conn, $project['slug']) . "'
                  ORDER BY rating DESC, id ASC 
                  LIMIT 10";
$vendors_result = mysqli_query($conn, $vendors_query);
$vendors = [];
while ($row = mysqli_fetch_assoc($vendors_result)) {
    $vendors[] = $row;
}
?>

<div class="card-panel mb-4">

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
        <h4 style="font-size:1.1rem; font-weight:700; color:var(--ink); margin:0;">
            <i class="bi bi-shop" style="color:#f5a623; margin-right:0.5rem;"></i>
            Vendors
        </h4>
        <a href="#" style="font-size:0.8rem; color:var(--accent, #f5a623); text-decoration:none; font-weight:600;">
            View All <i class="bi bi-chevron-right" style="font-size:0.7rem;"></i>
        </a>
    </div>

    <?php if (!empty($vendors)): ?>
    <div class="vendor-slider-wrapper" style="position:relative; overflow:hidden; border-radius:0.75rem;">
        <div class="vendor-slider" id="vendorSlider" style="display:flex; transition:transform 0.5s ease-in-out; gap:0;">
            <?php foreach ($vendors as $vendor): 
                $vendor_image = '';
                if (!empty($vendor['image'])) {
                    $fullImagePath = "../backend/uploads/vendors/" . $vendor['image'];
                    if (file_exists($fullImagePath) && is_file($fullImagePath)) {
                        $vendor_image = $fullImagePath;
                    } else {
                        $altImagePath = "backend/uploads/vendors/" . $vendor['image'];
                        if (file_exists($altImagePath) && is_file($altImagePath)) {
                            $vendor_image = $altImagePath;
                        } else {
                            $vendor_image = 'https://placehold.co/400x300/2ecc71/ffffff?text=' . urlencode(substr($vendor['name'], 0, 20));
                        }
                    }
                } else {
                    $vendor_image = 'https://placehold.co/400x300/2ecc71/ffffff?text=' . urlencode(substr($vendor['name'], 0, 20));
                }

                $short_name = strlen($vendor['name']) > 25 ? substr($vendor['name'], 0, 25) . '...' : $vendor['name'];
                $short_address = strlen($vendor['address']) > 30 ? substr($vendor['address'], 0, 30) . '...' : $vendor['address'];
                $vendor_link = 'vendor-profile.php?slug=' . urlencode($vendor['slug']);
            ?>
            <div class="vendor-slide" style="flex:0 0 100%; min-width:100%; background:var(--card-bg); border:1px solid var(--line, #e0e0e0); border-radius:0.75rem; overflow:hidden; transition:all 0.3s ease; cursor:pointer;" 
                 onclick="window.location.href='<?php echo htmlspecialchars($vendor_link); ?>'">
                <div style="position:relative; height:180px; background:#f0f0f0; overflow:hidden;">
                    <img src="<?php echo htmlspecialchars($vendor_image); ?>" 
                         alt="<?php echo htmlspecialchars($vendor['name']); ?>" 
                         style="width:100%; height:100%; object-fit:cover; transition:transform 0.3s ease;"
                         onerror="this.style.display='none'; this.parentElement.style.background='var(--bg-soft)'; this.parentElement.innerHTML='<div style=\'display:flex; align-items:center; justify-content:center; height:100%; font-size:2.5rem; color:var(--muted);\'><i class=\'bi bi-shop\'></i></div>'">
                    <?php if (!empty($vendor['rating']) && $vendor['rating'] > 0): ?>
                    <span style="position:absolute; top:0.75rem; left:0.75rem; background:rgba(0,0,0,0.7); color:#fff; padding:0.15rem 0.6rem; border-radius:1rem; font-size:0.6rem; font-weight:700; backdrop-filter:blur(4px); display:flex; align-items:center; gap:0.2rem;">
                        <i class="bi bi-star-fill" style="color:#f5a623;"></i> <?php echo number_format($vendor['rating'], 1); ?>
                    </span>
                    <?php endif; ?>
                    <?php if (!empty($vendor['starting_price'])): ?>
                    <span style="position:absolute; bottom:0.75rem; right:0.75rem; background:rgba(0,0,0,0.8); color:#fff; padding:0.3rem 0.7rem; border-radius:0.5rem; font-size:0.7rem; font-weight:700; backdrop-filter:blur(4px);">
                        <?php echo htmlspecialchars($vendor['starting_price']); ?>
                    </span>
                    <?php endif; ?>
                </div>
                <div class="vendor-body" style="padding:0.9rem 1rem 1.1rem;">
                    <h3 style="font-size:1.02rem; font-weight:700; margin-bottom:0.25rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:var(--ink);">
                        <?php echo htmlspecialchars($short_name); ?>
                    </h3>
                    <div style="color:var(--muted); font-size:0.86rem; margin-bottom:0.5rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($short_address); ?>
                    </div>
                    <?php if (!empty($vendor['experience_years'])): ?>
                    <div style="color:var(--muted); font-size:0.75rem;">
                        <i class="bi bi-briefcase-fill"></i> <?php echo (int)$vendor['experience_years']; ?> yrs experience
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (count($vendors) > 1): ?>
        <button class="vendor-slider-btn prev" style="position:absolute; top:50%; left:0.25rem; transform:translateY(-50%); background:rgba(255,255,255,0.9); border:none; border-radius:50%; width:34px; height:34px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.15); z-index:2; transition:all 0.3s ease; color:var(--ink); border:1px solid var(--line);" onclick="slideVendor(-1)">
            <i class="bi bi-chevron-left" style="font-size:1rem;"></i>
        </button>
        <button class="vendor-slider-btn next" style="position:absolute; top:50%; right:0.25rem; transform:translateY(-50%); background:rgba(255,255,255,0.9); border:none; border-radius:50%; width:34px; height:34px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.15); z-index:2; transition:all 0.3s ease; color:var(--ink); border:1px solid var(--line);" onclick="slideVendor(1)">
            <i class="bi bi-chevron-right" style="font-size:1rem;"></i>
        </button>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div style="text-align:center; padding:1.5rem 1rem; color:var(--muted);">
        <i class="bi bi-shop" style="font-size:1.5rem; display:block; margin-bottom:0.5rem; opacity:0.3;"></i>
        <span style="font-size:0.85rem;">No vendors available at the moment.</span>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php
$isHotelOrHomestayProject = $project && in_array(($project['category_type'] ?? ''), ['hotel', 'homestay']);
?>
<?php if (!$isHotelOrHomestayProject): ?>
<!-- ===== HOTELS & HOMESTAYS ===== -->
<?php
$stay_query = "SELECT id, project_name, slug, address, city, price, image, category_type, 
                amenities, bedrooms, bathrooms 
                FROM trending_projects 
                WHERE status = 'Active' 
                AND category_type IN ('hotel', 'homestay') 
                ORDER BY created_at DESC 
                LIMIT 10";
$stay_result = mysqli_query($conn, $stay_query);
$stays = [];
while ($row = mysqli_fetch_assoc($stay_result)) {
    $stays[] = $row;
}
shuffle($stays);
$stays = array_slice($stays, 0, 8);
?>

<div class="card-panel mb-4">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
        <h4 style="font-size:1.1rem; font-weight:700; color:var(--ink); margin:0;">
            <i class="bi bi-building" style="color:#f5a623; margin-right:0.5rem;"></i>
            Hotels & Homestays
        </h4>
        <a href="#" style="font-size:0.8rem; color:var(--accent, #f5a623); text-decoration:none; font-weight:600;">
            View All <i class="bi bi-chevron-right" style="font-size:0.7rem;"></i>
        </a>
    </div>

    <?php if (!empty($stays)): ?>
    <div class="stay-slider-wrapper" style="position:relative; overflow:hidden; border-radius:0.75rem;">
        <div class="stay-slider" id="staySlider" style="display:flex; transition:transform 0.5s ease-in-out; gap:0;">
            <?php foreach ($stays as $item): 
                // Image handling with placeholder fallback
                $stay_image = '';
                if (!empty($item['image'])) {
                    $fullImagePath = "../backend/uploads/trending-projects/" . $item['image'];
                    if (file_exists($fullImagePath) && is_file($fullImagePath)) {
                        $stay_image = $fullImagePath;
                    } else {
                        $altImagePath = "backend/uploads/trending-projects/" . $item['image'];
                        if (file_exists($altImagePath) && is_file($altImagePath)) {
                            $stay_image = $altImagePath;
                        } else {
                            $stay_image = 'https://placehold.co/400x300/f5a623/ffffff?text=' . urlencode(substr($item['project_name'], 0, 25));
                        }
                    }
                } else {
                    $stay_image = 'https://placehold.co/400x300/f5a623/ffffff?text=' . urlencode(substr($item['project_name'], 0, 25));
                }
                
                $badge_label = ($item['category_type'] == 'hotel') ? 'Hotel' : 'Homestay';
                $badge_bg = ($item['category_type'] == 'hotel') ? '#3498db' : '#2ecc71';
                
                $short_name = strlen($item['project_name']) > 25 ? substr($item['project_name'], 0, 25) . '...' : $item['project_name'];
                $short_address = strlen($item['address']) > 30 ? substr($item['address'], 0, 30) . '...' : $item['address'];
            ?>
            <div class="stay-slide" style="flex:0 0 100%; min-width:100%; background:var(--card-bg); border:1px solid var(--line, #e0e0e0); border-radius:0.75rem; overflow:hidden; transition:all 0.3s ease; cursor:pointer;" 
                 onclick="window.location.href='property-listing/<?php echo urlencode($item['slug']); ?>/'">
                <div style="position:relative; height:180px; background:#f0f0f0; overflow:hidden;">
                    <img src="<?php echo htmlspecialchars($stay_image); ?>" 
                         alt="<?php echo htmlspecialchars($item['project_name']); ?>" 
                         style="width:100%; height:100%; object-fit:cover; transition:transform 0.3s ease;"
                         onerror="this.style.display='none'; this.parentElement.style.background='#f0f0f0'; this.parentElement.innerHTML='<div style=\'display:flex; align-items:center; justify-content:center; height:100%; color:#999; font-size:0.8rem; text-align:center; padding:0.5rem;\'><?php echo addslashes($item['project_name']); ?></div>'">
                    
                    <span style="position:absolute; top:0.75rem; left:0.75rem; background:<?php echo $badge_bg; ?>; color:#fff; padding:0.15rem 0.6rem; border-radius:1rem; font-size:0.55rem; font-weight:600; backdrop-filter:blur(4px); z-index:1;">
                        <?php echo $badge_label; ?>
                    </span>
                    
                    <?php if (!empty($item['price']) && $item['price'] != '0'): ?>
                    <span style="position:absolute; bottom:0.75rem; right:0.75rem; background:rgba(0,0,0,0.8); color:#fff; padding:0.3rem 0.7rem; border-radius:0.5rem; font-size:0.7rem; font-weight:700; backdrop-filter:blur(4px); z-index:1;">
                        <?php echo htmlspecialchars($item['price']); ?>
                    </span>
                    <?php endif; ?>
                </div>
                <div class="stay-body" style="padding:0.9rem 1rem 1.1rem;">
                    <h3 style="font-size:1.02rem; font-weight:700; margin-bottom:0.25rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:var(--ink);">
                        <?php echo htmlspecialchars($short_name); ?>
                    </h3>
                    <div style="color:var(--muted); font-size:0.86rem; margin-bottom:0.5rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($short_address); ?>
                    </div>
                    <div style="display:flex; align-items:center; gap:0.5rem; font-size:0.8rem; color:var(--muted);">
                        <?php if (!empty($item['bedrooms']) && $item['bedrooms'] > 0): ?>
                        <span style="display:flex; align-items:center; gap:0.2rem;">
                            <i class="bi bi-door-open"></i> <?php echo $item['bedrooms']; ?> BR
                        </span>
                        <?php endif; ?>
                        <?php if (!empty($item['bathrooms']) && $item['bathrooms'] > 0): ?>
                        <span style="display:flex; align-items:center; gap:0.2rem;">
                            <i class="bi bi-droplet"></i> <?php echo $item['bathrooms']; ?> Bath
                        </span>
                        <?php endif; ?>
                        <?php if (!empty($item['city'])): ?>
                        <span style="display:flex; align-items:center; gap:0.2rem; margin-left:auto;">
                            <i class="bi bi-geo"></i> <?php echo htmlspecialchars($item['city']); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($stays) > 1): ?>
        <button class="stay-slider-btn prev" style="position:absolute; top:50%; left:0.25rem; transform:translateY(-50%); background:rgba(255,255,255,0.9); border:none; border-radius:50%; width:34px; height:34px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.15); z-index:2; transition:all 0.3s ease; color:var(--ink); border:1px solid var(--line);" onclick="slideStay(-1)">
            <i class="bi bi-chevron-left" style="font-size:1rem;"></i>
        </button>
        <button class="stay-slider-btn next" style="position:absolute; top:50%; right:0.25rem; transform:translateY(-50%); background:rgba(255,255,255,0.9); border:none; border-radius:50%; width:34px; height:34px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.15); z-index:2; transition:all 0.3s ease; color:var(--ink); border:1px solid var(--line);" onclick="slideStay(1)">
            <i class="bi bi-chevron-right" style="font-size:1rem;"></i>
        </button>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div style="text-align:center; padding:2rem 1rem; color:var(--muted);">
        <i class="bi bi-building" style="font-size:2rem; display:block; margin-bottom:0.5rem; opacity:0.3;"></i>
        <span style="font-size:0.9rem;">No hotels or homestays available at the moment.</span>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>
<!-- ===== AGENTS ===== -->
<?php
$agents_query = "SELECT id, name, slug, photo, specialization, designation, phone, email, 
                 city, locality, experience_years, listings_count, rating, reviews_count, status 
                 FROM agents 
                 WHERE status = 'Active' 
                 ORDER BY agent_order ASC 
                 LIMIT 8";
$agents_result = mysqli_query($conn, $agents_query);
$agents = [];
while ($row = mysqli_fetch_assoc($agents_result)) {
    $agents[] = $row;
}
?>

<div class="card-panel mb-4">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
        <h4 style="font-size:1.1rem; font-weight:700; color:var(--ink); margin:0;">
            <i class="bi bi-people-fill" style="color:#f5a623; margin-right:0.5rem;"></i>
            Agents
        </h4>
        <a href="#" style="font-size:0.8rem; color:var(--accent, #f5a623); text-decoration:none; font-weight:600;">
            View All <i class="bi bi-chevron-right" style="font-size:0.7rem;"></i>
        </a>
    </div>

    <?php if (!empty($agents)): ?>
    <div class="agent-slider-wrapper" style="position:relative; overflow:hidden; border-radius:0.75rem;">
        <div class="agent-slider" id="agentSlider" style="display:flex; transition:transform 0.5s ease-in-out; gap:0;">
            <?php foreach ($agents as $agent): 
                $nameParts = explode(' ', $agent['name']);
                $initials = '';
                foreach ($nameParts as $part) {
                    if (!empty($part)) {
                        $initials .= strtoupper(substr($part, 0, 1));
                    }
                }
                $initials = substr($initials, 0, 2);
                
                // Profile image handling
                $profile_img = '';
                if (!empty($agent['photo'])) {
                    $fullProfilePath = "../backend/uploads/agents/" . $agent['photo'];
                    if (file_exists($fullProfilePath) && is_file($fullProfilePath)) {
                        $profile_img = $fullProfilePath;
                    } else {
                        $altProfilePath = "backend/uploads/agents/" . $agent['photo'];
                        if (file_exists($altProfilePath) && is_file($altProfilePath)) {
                            $profile_img = $altProfilePath;
                        }
                    }
                }
                
                // Badge based on specialization
                $spec_colors = [
                    'buy' => '#c9a84c',
                    'rent' => '#3498db',
                    'vendor' => '#2ecc71',
                    'interior' => '#e67e22',
                    'local_services' => '#f5a623',
                    'hotels_homestays' => '#9b59b6',
                    'pg_hostels' => '#e74c3c'
                ];
                $role_color = $spec_colors[$agent['specialization']] ?? '#c9a84c';
                $role_label = !empty($agent['designation']) ? $agent['designation'] : ucfirst($agent['specialization']);
                $short_role = strlen($role_label) > 20 ? substr($role_label, 0, 20) . '...' : $role_label;
                
                $agent_link = !empty($agent['slug']) 
                    ? 'agent-profile.php?slug=' . urlencode($agent['slug']) 
                    : 'agent-profile.php?id=' . $agent['id'];
            ?>
            <div class="agent-slide" style="flex:0 0 100%; min-width:100%; background:var(--card-bg); border:1px solid var(--line, #e0e0e0); border-radius:0.75rem; overflow:hidden; transition:all 0.3s ease; cursor:pointer; text-align:center; padding:0.9rem 0.5rem;" 
                 onclick="window.location.href='<?php echo htmlspecialchars($agent_link); ?>'">
                <div style="position:relative; width:80px; height:80px; margin:0 auto 0.75rem;">
                    <?php if (!empty($profile_img)): ?>
                        <img src="<?php echo htmlspecialchars($profile_img); ?>" 
                             alt="<?php echo htmlspecialchars($agent['name']); ?>" 
                             style="width:80px; height:80px; border-radius:50%; object-fit:cover; border:3px solid var(--line);"
                             onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'width:80px; height:80px; border-radius:50%; background:var(--primary, #c9a84c); display:flex; align-items:center; justify-content:center; font-size:1.8rem; font-weight:700; color:#fff; border:3px solid var(--primary, #c9a84c);\'><?php echo $initials; ?></div>'">
                    <?php else: ?>
                        <div style="width:80px; height:80px; border-radius:50%; background:var(--primary, #c9a84c); display:flex; align-items:center; justify-content:center; font-size:1.8rem; font-weight:700; color:#fff; border:3px solid var(--primary, #c9a84c);">
                            <?php echo $initials; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($agent['rating']) && $agent['rating'] > 0): ?>
                    <span style="position:absolute; bottom:0; right:0; background:#f5a623; color:#fff; padding:0.1rem 0.4rem; border-radius:1rem; font-size:0.55rem; font-weight:700; border:2px solid var(--card-bg); display:flex; align-items:center; gap:0.1rem;">
                        <i class="bi bi-star-fill" style="font-size:0.45rem;"></i><?php echo number_format($agent['rating'], 1); ?>
                    </span>
                    <?php endif; ?>
                </div>
                <div style="font-weight:600; font-size:0.9rem; color:var(--ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    <?php echo htmlspecialchars($agent['name']); ?>
                </div>
                <div style="font-size:0.68rem; color:<?php echo $role_color; ?>; font-weight:600; margin-top:0.1rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    <?php echo htmlspecialchars($short_role); ?>
                </div>
                <?php if (!empty($agent['city'])): ?>
                <div style="font-size:0.65rem; color:var(--muted); margin-top:0.15rem;">
                    <i class="bi bi-geo-alt-fill" style="font-size:0.5rem;"></i>
                    <?php echo htmlspecialchars($agent['locality'] ? $agent['locality'] . ', ' . $agent['city'] : $agent['city']); ?>
                </div>
                <?php endif; ?>
                <div style="font-size:0.65rem; color:var(--muted); margin-top:0.1rem;">
                    <?php if (!empty($agent['experience_years'])): ?>
                        <i class="bi bi-briefcase-fill" style="font-size:0.5rem;"></i> <?php echo (int)$agent['experience_years']; ?> yrs
                    <?php endif; ?>
                    <?php if (!empty($agent['listings_count'])): ?>
                        &nbsp;•&nbsp;<i class="bi bi-houses-fill" style="font-size:0.5rem;"></i> <?php echo (int)$agent['listings_count']; ?> listings
                    <?php endif; ?>
                </div>
                <button style="margin-top:0.6rem; background:var(--primary, #c9a84c); color:#fff; border:none; border-radius:1.5rem; padding:0.25rem 1.2rem; font-size:0.65rem; font-weight:600; cursor:pointer; transition:all 0.3s ease;"
                        onmouseover="this.style.opacity='0.8'"
                        onmouseout="this.style.opacity='1'"
                        onclick="event.stopPropagation(); window.location.href='<?php echo htmlspecialchars($agent_link); ?>'">
                    View Profile
                </button>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($agents) > 1): ?>
        <button class="agent-slider-btn prev" style="position:absolute; top:50%; left:0.25rem; transform:translateY(-50%); background:rgba(255,255,255,0.9); border:none; border-radius:50%; width:34px; height:34px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.15); z-index:2; transition:all 0.3s ease; color:var(--ink); border:1px solid var(--line);" onclick="slideAgent(-1)">
            <i class="bi bi-chevron-left" style="font-size:1rem;"></i>
        </button>
        <button class="agent-slider-btn next" style="position:absolute; top:50%; right:0.25rem; transform:translateY(-50%); background:rgba(255,255,255,0.9); border:none; border-radius:50%; width:34px; height:34px; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.15); z-index:2; transition:all 0.3s ease; color:var(--ink); border:1px solid var(--line);" onclick="slideAgent(1)">
            <i class="bi bi-chevron-right" style="font-size:1rem;"></i>
        </button>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div style="text-align:center; padding:1.5rem 1rem; color:var(--muted);">
        <i class="bi bi-people-fill" style="font-size:1.5rem; display:block; margin-bottom:0.5rem; opacity:0.3;"></i>
        <span style="font-size:0.85rem;">No agents available at the moment.</span>
    </div>
    <?php endif; ?>
</div>

<!-- ===== SLIDER JAVASCRIPT ===== -->
<script>
    // Property Recommendations Slider
    let recCurrent = 0;
    const recSlider = document.getElementById('recSlider');
    const recItems = document.querySelectorAll('.rec-slide');
    const recTotal = recItems.length;
    let recAutoInterval;

    function updateRecSlider() {
        if (!recSlider || recItems.length === 0) return;
        const slideWidth = recItems[0].offsetWidth;
        recSlider.style.transform = 'translateX(-' + (recCurrent * slideWidth) + 'px)';
    }

    function slideRec(direction) {
        clearInterval(recAutoInterval);
        const maxSlide = Math.max(0, recTotal - 1);
        recCurrent += direction;
        if (recCurrent > maxSlide) recCurrent = 0;
        if (recCurrent < 0) recCurrent = maxSlide;
        updateRecSlider();
        startRecAutoSlide();
    }

    function startRecAutoSlide() {
        clearInterval(recAutoInterval);
        if (recTotal > 1) {
            recAutoInterval = setInterval(function() {
                const maxSlide = Math.max(0, recTotal - 1);
                if (recCurrent >= maxSlide) { recCurrent = 0; } else { recCurrent++; }
                updateRecSlider();
            }, 4000);
        }
    }

    // Loans Slider
    let loanCurrent = 0;
    const loanSlider = document.getElementById('loanSlider');
    const loanItems = document.querySelectorAll('.loan-slide');
    const loanTotal = loanItems.length;
    let loanAutoInterval;

    function updateLoanSlider() {
        if (!loanSlider || loanItems.length === 0) return;
        const slideWidth = loanItems[0].offsetWidth;
        loanSlider.style.transform = 'translateX(-' + (loanCurrent * slideWidth) + 'px)';
    }

    function slideLoan(direction) {
        clearInterval(loanAutoInterval);
        const maxSlide = Math.max(0, loanTotal - 1);
        loanCurrent += direction;
        if (loanCurrent > maxSlide) loanCurrent = 0;
        if (loanCurrent < 0) loanCurrent = maxSlide;
        updateLoanSlider();
        startLoanAutoSlide();
    }

    function startLoanAutoSlide() {
        clearInterval(loanAutoInterval);
        if (loanTotal > 1) {
            loanAutoInterval = setInterval(function() {
                const maxSlide = Math.max(0, loanTotal - 1);
                if (loanCurrent >= maxSlide) { loanCurrent = 0; } else { loanCurrent++; }
                updateLoanSlider();
            }, 4000);
        }
    }

    // Interiors Slider
    let interiorCurrent = 0;
    const interiorSlider = document.getElementById('interiorSlider');
    const interiorItems = document.querySelectorAll('.interior-slide');
    const interiorTotal = interiorItems.length;
    let interiorAutoInterval;

    function updateInteriorSlider() {
        if (!interiorSlider || interiorItems.length === 0) return;
        const slideWidth = interiorItems[0].offsetWidth;
        interiorSlider.style.transform = 'translateX(-' + (interiorCurrent * slideWidth) + 'px)';
    }

    function slideInterior(direction) {
        clearInterval(interiorAutoInterval);
        const maxSlide = Math.max(0, interiorTotal - 1);
        interiorCurrent += direction;
        if (interiorCurrent > maxSlide) interiorCurrent = 0;
        if (interiorCurrent < 0) interiorCurrent = maxSlide;
        updateInteriorSlider();
        startInteriorAutoSlide();
    }

    function startInteriorAutoSlide() {
        clearInterval(interiorAutoInterval);
        if (interiorTotal > 1) {
            interiorAutoInterval = setInterval(function() {
                const maxSlide = Math.max(0, interiorTotal - 1);
                if (interiorCurrent >= maxSlide) { interiorCurrent = 0; } else { interiorCurrent++; }
                updateInteriorSlider();
            }, 4000);
        }
    }

    // Local Services Slider
    let serviceCurrent = 0;
    const serviceSlider = document.getElementById('serviceSlider');
    const serviceItems = document.querySelectorAll('.service-slide');
    const serviceTotal = serviceItems.length;
    let serviceAutoInterval;

    function updateServiceSlider() {
        if (!serviceSlider || serviceItems.length === 0) return;
        const slideWidth = serviceItems[0].offsetWidth;
        serviceSlider.style.transform = 'translateX(-' + (serviceCurrent * slideWidth) + 'px)';
    }

    function slideService(direction) {
        clearInterval(serviceAutoInterval);
        const maxSlide = Math.max(0, serviceTotal - 1);
        serviceCurrent += direction;
        if (serviceCurrent > maxSlide) serviceCurrent = 0;
        if (serviceCurrent < 0) serviceCurrent = maxSlide;
        updateServiceSlider();
        startServiceAutoSlide();
    }

    function startServiceAutoSlide() {
        clearInterval(serviceAutoInterval);
        if (serviceTotal > 1) {
            serviceAutoInterval = setInterval(function() {
                const maxSlide = Math.max(0, serviceTotal - 1);
                if (serviceCurrent >= maxSlide) { serviceCurrent = 0; } else { serviceCurrent++; }
                updateServiceSlider();
            }, 4000);
        }
    }

    // Vendors Slider
    let vendorCurrent = 0;
    const vendorSlider = document.getElementById('vendorSlider');
    const vendorItems = document.querySelectorAll('.vendor-slide');
    const vendorTotal = vendorItems.length;
    let vendorAutoInterval;

    function updateVendorSlider() {
        if (!vendorSlider || vendorItems.length === 0) return;
        const slideWidth = vendorItems[0].offsetWidth;
        vendorSlider.style.transform = 'translateX(-' + (vendorCurrent * slideWidth) + 'px)';
    }

    function slideVendor(direction) {
        clearInterval(vendorAutoInterval);
        const maxSlide = Math.max(0, vendorTotal - 1);
        vendorCurrent += direction;
        if (vendorCurrent > maxSlide) vendorCurrent = 0;
        if (vendorCurrent < 0) vendorCurrent = maxSlide;
        updateVendorSlider();
        startVendorAutoSlide();
    }

    function startVendorAutoSlide() {
        clearInterval(vendorAutoInterval);
        if (vendorTotal > 1) {
            vendorAutoInterval = setInterval(function() {
                const maxSlide = Math.max(0, vendorTotal - 1);
                if (vendorCurrent >= maxSlide) { vendorCurrent = 0; } else { vendorCurrent++; }
                updateVendorSlider();
            }, 4000);
        }
    }

    // Hotels & Homestays Slider
    let stayCurrent = 0;
    const staySlider = document.getElementById('staySlider');
    const stayItems = document.querySelectorAll('.stay-slide');
    const stayTotal = stayItems.length;
    let stayAutoInterval;

    function updateStaySlider() {
        if (!staySlider || stayItems.length === 0) return;
        const slideWidth = stayItems[0].offsetWidth;
        staySlider.style.transform = 'translateX(-' + (stayCurrent * slideWidth) + 'px)';
    }

    function slideStay(direction) {
        clearInterval(stayAutoInterval);
        const maxSlide = Math.max(0, stayTotal - 1);
        stayCurrent += direction;
        if (stayCurrent > maxSlide) stayCurrent = 0;
        if (stayCurrent < 0) stayCurrent = maxSlide;
        updateStaySlider();
        startStayAutoSlide();
    }

    function startStayAutoSlide() {
        clearInterval(stayAutoInterval);
        if (stayTotal > 1) {
            stayAutoInterval = setInterval(function() {
                const maxSlide = Math.max(0, stayTotal - 1);
                if (stayCurrent >= maxSlide) { stayCurrent = 0; } else { stayCurrent++; }
                updateStaySlider();
            }, 4000);
        }
    }

    // Agents Slider
    let agentCurrent = 0;
    const agentSlider = document.getElementById('agentSlider');
    const agentItems = document.querySelectorAll('.agent-slide');
    const agentTotal = agentItems.length;
    let agentAutoInterval;

    function updateAgentSlider() {
        if (!agentSlider || agentItems.length === 0) return;
        const slideWidth = agentItems[0].offsetWidth;
        agentSlider.style.transform = 'translateX(-' + (agentCurrent * slideWidth) + 'px)';
    }

    function slideAgent(direction) {
        clearInterval(agentAutoInterval);
        const maxSlide = Math.max(0, agentTotal - 1);
        agentCurrent += direction;
        if (agentCurrent > maxSlide) agentCurrent = 0;
        if (agentCurrent < 0) agentCurrent = maxSlide;
        updateAgentSlider();
        startAgentAutoSlide();
    }

    function startAgentAutoSlide() {
        clearInterval(agentAutoInterval);
        if (agentTotal > 1) {
            agentAutoInterval = setInterval(function() {
                const maxSlide = Math.max(0, agentTotal - 1);
                if (agentCurrent >= maxSlide) { agentCurrent = 0; } else { agentCurrent++; }
                updateAgentSlider();
            }, 4000);
        }
    }

    // Initialize all sliders
    function initAllSliders() {
        if (recTotal > 0) { updateRecSlider(); startRecAutoSlide(); if (recTotal <= 1) { document.querySelectorAll('.rec-slider-wrapper .rec-slider-btn').forEach(function(b) { b.style.display = 'none'; }); } }
        if (loanTotal > 0) { updateLoanSlider(); startLoanAutoSlide(); if (loanTotal <= 1) { document.querySelectorAll('.loan-slider-wrapper .loan-slider-btn').forEach(function(b) { b.style.display = 'none'; }); } }
        if (interiorTotal > 0) { updateInteriorSlider(); startInteriorAutoSlide(); if (interiorTotal <= 1) { document.querySelectorAll('.interior-slider-wrapper .interior-slider-btn').forEach(function(b) { b.style.display = 'none'; }); } }
        if (serviceTotal > 0) { updateServiceSlider(); startServiceAutoSlide(); if (serviceTotal <= 1) { document.querySelectorAll('.service-slider-wrapper .service-slider-btn').forEach(function(b) { b.style.display = 'none'; }); } }
        if (vendorTotal > 0) { updateVendorSlider(); startVendorAutoSlide(); if (vendorTotal <= 1) { document.querySelectorAll('.vendor-slider-wrapper .vendor-slider-btn').forEach(function(b) { b.style.display = 'none'; }); } }
        if (stayTotal > 0) { updateStaySlider(); startStayAutoSlide(); if (stayTotal <= 1) { document.querySelectorAll('.stay-slider-wrapper .stay-slider-btn').forEach(function(b) { b.style.display = 'none'; }); } }
        if (agentTotal > 0) { updateAgentSlider(); startAgentAutoSlide(); if (agentTotal <= 1) { document.querySelectorAll('.agent-slider-wrapper .agent-slider-btn').forEach(function(b) { b.style.display = 'none'; }); } }
    }

    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initAllSliders); } else { initAllSliders(); }

    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            if (recTotal > 0) { const maxSlide = Math.max(0, recTotal - 1); if (recCurrent > maxSlide) recCurrent = maxSlide; updateRecSlider(); }
            if (loanTotal > 0) { const maxSlide = Math.max(0, loanTotal - 1); if (loanCurrent > maxSlide) loanCurrent = maxSlide; updateLoanSlider(); }
            if (interiorTotal > 0) { const maxSlide = Math.max(0, interiorTotal - 1); if (interiorCurrent > maxSlide) interiorCurrent = maxSlide; updateInteriorSlider(); }
            if (serviceTotal > 0) { const maxSlide = Math.max(0, serviceTotal - 1); if (serviceCurrent > maxSlide) serviceCurrent = maxSlide; updateServiceSlider(); }
            if (vendorTotal > 0) { const maxSlide = Math.max(0, vendorTotal - 1); if (vendorCurrent > maxSlide) vendorCurrent = maxSlide; updateVendorSlider(); }
            if (stayTotal > 0) { const maxSlide = Math.max(0, stayTotal - 1); if (stayCurrent > maxSlide) stayCurrent = maxSlide; updateStaySlider(); }
            if (agentTotal > 0) { const maxSlide = Math.max(0, agentTotal - 1); if (agentCurrent > maxSlide) agentCurrent = maxSlide; updateAgentSlider(); }
        }, 250);
    });

    // Pause on hover for all sliders
    document.querySelectorAll('.rec-slider-wrapper, .loan-slider-wrapper, .interior-slider-wrapper, .service-slider-wrapper, .vendor-slider-wrapper, .stay-slider-wrapper, .agent-slider-wrapper').forEach(function(wrapper) {
        wrapper.addEventListener('mouseenter', function() {
            clearInterval(recAutoInterval); clearInterval(loanAutoInterval); clearInterval(interiorAutoInterval);
            clearInterval(serviceAutoInterval); clearInterval(vendorAutoInterval); clearInterval(stayAutoInterval); clearInterval(agentAutoInterval);
        });
        wrapper.addEventListener('mouseleave', function() {
            if (recTotal > 1) startRecAutoSlide();
            if (loanTotal > 1) startLoanAutoSlide();
            if (interiorTotal > 1) startInteriorAutoSlide();
            if (serviceTotal > 1) startServiceAutoSlide();
            if (vendorTotal > 1) startVendorAutoSlide();
            if (stayTotal > 1) startStayAutoSlide();
            if (agentTotal > 1) startAgentAutoSlide();
        });
    });

    window.slideRec = slideRec; window.slideLoan = slideLoan; window.slideInterior = slideInterior;
    window.slideService = slideService; window.slideVendor = slideVendor; window.slideStay = slideStay; window.slideAgent = slideAgent;
</script>

<style>
    .rec-slide:hover, .loan-slide:hover, .interior-slide:hover, .service-slide:hover, .vendor-slide:hover, .stay-slide:hover, .agent-slide:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        border-color: #f5a623;
    }
    .rec-slide:hover img, .loan-slide:hover img, .interior-slide:hover img, .service-slide:hover img, .vendor-slide:hover img, .stay-slide:hover img {
        transform: scale(1.05);
    }
    .rec-slider-btn:hover, .loan-slider-btn:hover, .interior-slider-btn:hover, .service-slider-btn:hover, .vendor-slider-btn:hover, .stay-slider-btn:hover, .agent-slider-btn:hover {
        background: #fff !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
    }
    
    @media (prefers-color-scheme: dark) {
        .rec-slider-btn, .loan-slider-btn, .interior-slider-btn, .service-slider-btn, .vendor-slider-btn, .stay-slider-btn, .agent-slider-btn {
            background: rgba(30,30,40,0.9) !important;
            color: #fff !important;
        }
        .rec-slider-btn:hover, .loan-slider-btn:hover, .interior-slider-btn:hover, .service-slider-btn:hover, .vendor-slider-btn:hover, .stay-slider-btn:hover, .agent-slider-btn:hover {
            background: rgba(50,50,60,0.95) !important;
        }
    }
    
    @media (max-width: 480px) {
        .rec-slider-btn, .loan-slider-btn, .interior-slider-btn, .service-slider-btn, .vendor-slider-btn, .stay-slider-btn, .agent-slider-btn {
            width: 28px !important;
            height: 28px !important;
        }
        .rec-slider-btn i, .loan-slider-btn i, .interior-slider-btn i, .service-slider-btn i, .vendor-slider-btn i, .stay-slider-btn i, .agent-slider-btn i {
            font-size: 0.8rem !important;
        }
        .agent-slide {
            padding: 0.6rem 0.3rem !important;
        }
        .agent-slide div[style*="width:80px"] {
            width: 60px !important;
            height: 60px !important;
        }
        .agent-slide div[style*="width:80px"] img {
            width: 60px !important;
            height: 60px !important;
        }
    }
</style>

<!-- ===== REPORT INAPPROPRIATE CONTENT BUTTON ===== -->
<button class="card-panel w-100" onclick="openReportPopup()" style="border:1px solid var(--line); border-radius:1rem; padding:1rem; background:var(--card-bg); cursor:pointer; transition:all 0.3s ease; text-align:center; border-color:#e74c3c;">
    <div style="display:flex; align-items:center; justify-content:center; gap:0.75rem;">
        <i class="bi bi-flag-fill" style="color:#e74c3c; font-size:1.2rem;"></i>
        <span style="font-weight:600; color:var(--ink);">Report Inappropriate Content</span>
    </div>
    <p style="font-size:0.7rem; color:var(--muted); margin:0.3rem 0 0;">Click to report if you find this content inappropriate</p>
</button>