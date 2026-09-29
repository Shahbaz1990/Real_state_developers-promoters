<?php
require_once __DIR__ . '/includes/config.php';

// Get search and price range from GET request
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$selectedPrice = isset($_GET['price']) ? intval($_GET['price']) : 650;

// Function to convert price to lakhs
function priceToLakhs(string $price): int {
    $digits = preg_replace('/\D/', '', $price);
    return intval($digits / 100000);
}

// Function to convert price to abbreviated format (e.g., ₹ 1.76 Cr or ₹ 71L)
function convertPriceToAbbrev(string $price): string {
    $digits = preg_replace('/\D/', '', $price);
    $priceInLakhs = intval($digits / 100000);
    return $priceInLakhs >= 100
        ? '₹ ' . ($priceInLakhs % 100 === 0 ? $priceInLakhs / 100 : number_format($priceInLakhs / 100, 2)) . ' Cr'
        : '₹ ' . $priceInLakhs . 'L';
}

// Sample properties data
$properties = [
    [
        'id' => 1,
        'title' => 'Kalpataru Vian',
        'city' => 'Mumbai',
        'state' => 'Maharashtra',
        'price' => '₹1,76,00,000',
        'lat' => 19.1366,
        'lng' => 72.8295,
        'beds' => 3,
        'baths' => 3,
        'area' => '1,420 sqft',
        'rating' => '4.8',
        'image' => 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=1200&q=80'
    ],
    [
        'id' => 2,
        'title' => 'Mahindra Rainforest',
        'city' => 'Mumbai',
        'state' => 'Maharashtra',
        'price' => '₹1,95,00,000',
        'lat' => 19.1511,
        'lng' => 72.9375,
        'beds' => 3,
        'baths' => 3,
        'area' => '1,580 sqft',
        'rating' => '4.7',
        'image' => 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=1200&q=80'
    ],
    [
        'id' => 3,
        'title' => 'Godrej Vanantara',
        'city' => 'Bangalore',
        'state' => 'Karnataka',
        'price' => '₹1,76,00,000',
        'lat' => 12.9081,
        'lng' => 77.5960,
        'beds' => 3,
        'baths' => 3,
        'area' => '1,650 sqft',
        'rating' => '4.9',
        'image' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=80'
    ],
    [
        'id' => 4,
        'title' => 'Prestige Lakeside Habitat',
        'city' => 'Bangalore',
        'state' => 'Karnataka',
        'price' => '₹2,45,00,000',
        'lat' => 12.9698,
        'lng' => 77.7499,
        'beds' => 4,
        'baths' => 4,
        'area' => '2,450 sqft',
        'rating' => '4.8',
        'image' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80'
    ],
    [
        'id' => 5,
        'title' => 'DLF The Crest',
        'city' => 'Gurgaon',
        'state' => 'Haryana',
        'price' => '₹4,20,00,000',
        'lat' => 28.4595,
        'lng' => 77.0266,
        'beds' => 4,
        'baths' => 5,
        'area' => '3,200 sqft',
        'rating' => '4.9',
        'image' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80'
    ],
    [
        'id' => 6,
        'title' => 'M3M Golf Estate',
        'city' => 'Gurgaon',
        'state' => 'Haryana',
        'price' => '₹5,80,00,000',
        'lat' => 28.4089,
        'lng' => 77.0484,
        'beds' => 5,
        'baths' => 5,
        'area' => '4,100 sqft',
        'rating' => '4.8',
        'image' => 'https://images.unsplash.com/photo-1600573472550-8090b5e0745e?auto=format&fit=crop&w=1200&q=80'
    ],
    [
        'id' => 7,
        'title' => 'Godrej River Greens',
        'city' => 'Pune',
        'state' => 'Maharashtra',
        'price' => '₹71,00,000',
        'lat' => 18.5204,
        'lng' => 73.8567,
        'beds' => 2,
        'baths' => 2,
        'area' => '980 sqft',
        'rating' => '4.6',
        'image' => 'https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?auto=format&fit=crop&w=1200&q=80'
    ],
    [
        'id' => 8,
        'title' => 'Kolte Patil Life Republic',
        'city' => 'Pune',
        'state' => 'Maharashtra',
        'price' => '₹89,00,000',
        'lat' => 18.5912,
        'lng' => 73.7389,
        'beds' => 3,
        'baths' => 3,
        'area' => '1,250 sqft',
        'rating' => '4.5',
        'image' => 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1200&q=80'
    ],
    [
        'id' => 9,
        'title' => 'Brigade Xanadu',
        'city' => 'Chennai',
        'state' => 'Tamil Nadu',
        'price' => '₹88,00,000',
        'lat' => 13.0827,
        'lng' => 80.2707,
        'beds' => 3,
        'baths' => 3,
        'area' => '2,018 sqft',
        'rating' => '4.5',
        'image' => 'https://images.unsplash.com/photo-1605146769289-440113cc3d00?auto=format&fit=crop&w=1200&q=80'
    ],
    [
        'id' => 10,
        'title' => 'Purva Somerset House',
        'city' => 'Chennai',
        'state' => 'Tamil Nadu',
        'price' => '₹1,45,00,000',
        'lat' => 13.0604,
        'lng' => 80.2496,
        'beds' => 4,
        'baths' => 4,
        'area' => '2,350 sqft',
        'rating' => '4.7',
        'image' => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb3?auto=format&fit=crop&w=1200&q=80'
    ],
    [
        'id' => 11,
        'title' => 'Aparna Zenon',
        'city' => 'Hyderabad',
        'state' => 'Telangana',
        'price' => '₹1,12,00,000',
        'lat' => 17.3850,
        'lng' => 78.4867,
        'beds' => 3,
        'baths' => 3,
        'area' => '1,540 sqft',
        'rating' => '4.6',
        'image' => 'https://images.unsplash.com/photo-1600585154084-4e5fe7c39198?auto=format&fit=crop&w=1200&q=80'
    ],
    [
        'id' => 12,
        'title' => 'ATS Knightsbridge',
        'city' => 'Noida',
        'state' => 'Uttar Pradesh',
        'price' => '₹6,50,00,000',
        'lat' => 28.5355,
        'lng' => 77.3910,
        'beds' => 5,
        'baths' => 5,
        'area' => '6,000 sqft',
        'rating' => '4.9',
        'image' => 'https://images.unsplash.com/photo-1600607687644-c7171b42498f?auto=format&fit=crop&w=1200&q=80'
    ]
];

// Filter properties based on search and price range
$selectedPrice = isset($_GET['price']) ? intval($_GET['price']) : 650;

$filtered = array_filter($properties, function($p) use ($search, $selectedPrice) {

    $matchesSearch = $search === '' ||
        stripos($p['city'], $search) !== false ||
        stripos($p['title'], $search) !== false;

    $priceLakhs = priceToLakhs($p['price']);

    return $matchesSearch && $priceLakhs <= $selectedPrice;
});

// Encode filtered properties for JavaScript
$filteredPropertiesJson = json_encode(array_values($filtered));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Properties</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/property-search.css">
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <!-- HERO SECTION -->
    <section class="hero-section py-4">
        <div class="">
            <div class="text-center mb-6">
                <h2 class="display-6 fw-bold">Find Your Dream Property</h2>
                <p>Discover the perfect home from our curated collection of properties</p>
            </div>

            <div class="search-card">
                <form method="GET" class="">
                    <!-- Search Input -->
                    <div class="input-group input-group-lg mb-4">
                        <span class="input-group-text bg-white border-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text"
                               name="search"
                               class="form-control search-input"
                               placeholder="Search by city, locality or project"
                               value="<?= htmlspecialchars($search) ?>">
                        <button class="search-btn" type="submit">Search</button>
                    </div>

                    <!-- Filters -->
                    <div class="row g-4 align-items-end">
                        <!-- Price Range -->
                        <div class="col-lg-4">
                            <div class="filter-label fw-semibold mb-2">Price Range</div>
                            <div class="price-range-card">
                                <div class="d-flex gap-2 mb-3">
                                    <input type="text" class="form-control min-box" value="Min" readonly>
                                    <input type="number"
                                           id="priceValue"
                                           class="form-control max-box"
                                           value="<?= $selectedPrice * 100000 ?>"
                                           readonly>
                                </div>
                                <input type="range"
                                       id="priceMin"
                                       name="price"
                                       class="price-slider"
                                       min="0"
                                       max="650"
                                       step="10"
                                       value="<?= $selectedPrice ?>">
                                <div class="d-flex justify-content-between mt-2">
                                    <span class="price-limit">₹ 0</span>
                                    <span class="price-limit" id="priceLabel"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="col-lg-4">
                            <div class="filter-label">Location</div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <select class="form-select" name="region">
                                        <option>Select Region</option>
                                        <option>North</option>
                                        <option>South</option>
                                        <option>East</option>
                                        <option>West</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <select class="form-select" name="city">
                                        <option>Select City</option>
                                        <option>Pune</option>
                                        <option>Mumbai</option>
                                        <?php foreach (json_decode($uniqueCities) as $city): ?>
                                            <option value="<?= htmlspecialchars($city) ?>"><?= htmlspecialchars($city) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Property Type -->
                        <div class="col-lg-4">
                            <div class="filter-label">Property Type</div>
                            <select class="form-select" name="property_type">
                                <option>All Types</option>
                                <option>Apartment</option>
                                <option>Villa</option>
                                <option>Plot</option>
                            </select>
                        </div>

                        <!-- Listing Type -->
                        <div class="col-lg-4">
                            <div class="filter-label">Listing Type</div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn bg-light">For Sale</button>
                                <button type="button" class="btn btn-outline">For Rent</button>
                            </div>
                        </div>

                        <!-- Rooms -->
                        <div class="col-lg-4">
                            <div class="filter-label">Rooms</div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <select class="form-select" name="bedrooms">
                                        <option>Bedrooms</option>
                                        <option>1</option>
                                        <option>2</option>
                                        <option>3</option>
                                        <option>4+</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <select class="form-select" name="bathrooms">
                                        <option>Bathrooms</option>
                                        <option>1</option>
                                        <option>2</option>
                                        <option>3</option>
                                        <option>4+</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="mt-4">
                    <a href="properties.php" class="clear-btn text-decoration-none d-inline-flex align-items-center gap-2">
                        <i class="bi bi-funnel"></i>
                        Clear all
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- RESULTS SECTION -->
    <section class="py-5">
        <div class="properties-container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <h2 class="fw-bold mb-0"><?= count($filtered) ?> Properties Found</h2>
                <div class="view-toggle">
                    <button class="view-btn active" id="listBtn">
                        <i class="bi bi-grid me-1"></i> List
                    </button>
                    <button class="view-btn" id="mapBtn">
                        <i class="bi bi-map me-1"></i> Map
                    </button>
                </div>
            </div>

            <!-- LIST VIEW -->
            <div id="listView">
                <div class="row g-4">
                    <?php foreach ($filtered as $property): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="property-card">
                                <div class="property-image">
                                    <img src="<?= $property['image'] ?>" alt="<?= htmlspecialchars($property['title']) ?>">
                                    <span class="sale-badge">For Sale</span>
                                </div>
                                <div class="property-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h3 class="property-title mb-0"><?= htmlspecialchars($property['title']) ?></h3>
                                        <div class="rating">
                                            <i class="bi bi-star-fill"></i> <?= htmlspecialchars($property['rating']) ?>
                                        </div>
                                    </div>
                                    <div class="property-location">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        <?= htmlspecialchars($property['state']) ?>, <?= htmlspecialchars($property['city']) ?>
                                    </div>
                                    <div class="property-meta">
                                        <span><i class="bi bi-door-open me-1"></i> <?= $property['beds'] ?> Beds</span>
                                        <span><i class="bi bi-droplet me-1"></i> <?= $property['baths'] ?> Baths</span>
                                        <span><i class="bi bi-aspect-ratio me-1"></i> <?= $property['area'] ?></span>
                                    </div>
                                    <div class="amenities">
                                        <span class="amenity">Parking</span>
                                        <span class="amenity">Lift</span>
                                        <span class="amenity">24×7 Security</span>
                                        <span class="amenity">Power Backup</span>
                                    </div>
                                    <div class="property-footer">
                                        <div class="property-price"><?= $property['price'] ?></div>
                                        <a href="project-details.php?id=<?= $property['id'] ?>"
                                         class=" btn btn-light  px-4"><i class="bi bi-hand-thumbs-up"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- MAP VIEW -->
            <div id="mapView" class="d-none">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="map-sidebar">
                            <div class="text-muted mb-3"><?= count($filtered) ?> properties on map</div>
                            <?php foreach ($filtered as $property): ?>
                                <div class="map-item">
                                    <div class="d-flex gap-3">
                                        <img src="<?= $property['image'] ?>" alt="<?= htmlspecialchars($property['title']) ?>">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1"><?= htmlspecialchars($property['title']) ?></h6>
                                            <div class="text-muted small mb-2">
                                                <i class="bi bi-geo-alt me-1"></i>
                                                <?= htmlspecialchars($property['city']) ?>, <?= htmlspecialchars($property['state']) ?>
                                            </div>
                                            <div class="text-primary fw-bold fs-5">
                                                <?= convertPriceToAbbrev($property['price']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div id="map"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <a href="#" class="floating-chat text-decoration-none">
        <i class="bi bi-chat-square"></i>
    </a>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // --- DOM Elements ---
        const listBtn = document.getElementById('listBtn');
        const mapBtn = document.getElementById('mapBtn');
        const listView = document.getElementById('listView');
        const mapView = document.getElementById('mapView');
        const filteredProperties = <?php echo $filteredPropertiesJson; ?>;
        let mapInitialized = false;
        let map;

        // --- Price Slider Logic ---
        const priceInput = document.getElementById("priceMin");
        const priceValue = document.getElementById("priceValue");
        const priceLabel = document.getElementById("priceLabel");

        function indianFormat(num) {
            return Number(num).toLocaleString("en-IN");
        }

        function updatePriceLabel() {
            const value = parseInt(priceInput.value);
            const amount = value * 100000;
            if (priceValue) priceValue.value = amount;
            if (priceLabel) priceLabel.innerHTML = "₹ " + indianFormat(amount);
        }

        priceInput.addEventListener("input", updatePriceLabel);
        priceInput.addEventListener("change", () => priceInput.form.submit());
        updatePriceLabel(); // Initialize

        // --- View Toggle Logic ---
        listBtn.addEventListener('click', () => {
            listBtn.classList.add('active');
            mapBtn.classList.remove('active');
            listView.classList.remove('d-none');
            mapView.classList.add('d-none');
        });

        mapBtn.addEventListener('click', () => {
            mapBtn.classList.add('active');
            listBtn.classList.remove('active');
            mapView.classList.remove('d-none');
            listView.classList.add('d-none');

            if (!mapInitialized) {
                initMap();
                mapInitialized = true;
            }
            setTimeout(() => map?.invalidateSize(), 300);
        });

        // --- Map Initialization ---
        function initMap() {
            const centerLat = filteredProperties.length > 0 ? filteredProperties[0].lat : 20.5937;
            const centerLng = filteredProperties.length > 0 ? filteredProperties[0].lng : 78.9629;
            map = L.map('map').setView([centerLat, centerLng], 5);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const markers = [];
            filteredProperties.forEach(property => {
                const marker = L.marker([property.lat, property.lng]).addTo(map);
                marker.bindPopup(`
                    <div style="width:220px">
                        <img src="${property.image}" style="width:100%;height:120px;object-fit:cover;border-radius:12px;margin-bottom:10px">
                        <h6 style="margin:0 0 6px;font-weight:700">${property.title}</h6>
                        <div style="color:#64748B;font-size:13px;margin-bottom:8px">${property.city}, ${property.state}</div>
                        <div style="font-size:18px;font-weight:800;color:#0B192C">${property.price}</div>
                    </div>
                `);
                markers.push(marker);
            });

            if (markers.length > 0) {
                const bounds = L.featureGroup(markers).getBounds();
                map.fitBounds(bounds.pad(0.15));
            }
        }
    </script>
</body>
</html>