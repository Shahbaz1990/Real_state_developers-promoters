<?php
/**
 * GET /backend/api/locality-insights.php?locality=Andheri%20West
 *
 * Computes real stats (from trending_projects only — not external market
 * data) for whatever locality substring is passed in: listing count, price
 * range, median price, and the spread of bedroom configurations on offer.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../../config/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
cors_headers();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$locality = isset($_GET['locality']) ? trim($_GET['locality']) : '';

if ($locality === '') {
    echo json_encode(['success' => true, 'data' => null]);
    exit;
}

// Convert a price string like "₹1,76,00,000" or "1.76 Cr" into whole rupees
function priceToRupees($price) {
    $price = trim($price);
    if (stripos($price, 'cr') !== false) {
        $num = (float) preg_replace('/[^0-9.]/', '', $price);
        return (int) round($num * 10000000);
    }
    if (stripos($price, 'lakh') !== false || stripos($price, 'l') !== false) {
        $num = (float) preg_replace('/[^0-9.]/', '', $price);
        return (int) round($num * 100000);
    }
    $digits = preg_replace('/\D/', '', $price);
    return $digits === '' ? 0 : (int) $digits;
}

function formatRupees($amount) {
    if ($amount >= 10000000) return '₹' . round($amount / 10000000, 2) . ' Cr';
    if ($amount >= 100000)   return '₹' . round($amount / 100000, 1) . ' L';
    return '₹' . number_format($amount);
}

$stmt = $conn->prepare("SELECT price, bedrooms FROM trending_projects WHERE status='Active' AND address LIKE ?");
$like = '%' . $locality . '%';
$stmt->bind_param('s', $like);
$stmt->execute();
$res = $stmt->get_result();

$prices = [];
$bedroomSet = [];
while ($row = $res->fetch_assoc()) {
    $rupees = priceToRupees($row['price']);
    if ($rupees > 0) $prices[] = $rupees;
    if (!empty($row['bedrooms'])) $bedroomSet[(int) $row['bedrooms']] = true;
}
$stmt->close();

if (empty($prices)) {
    echo json_encode(['success' => true, 'data' => null]);
    exit;
}

sort($prices);
$count = count($prices);
$median = $count % 2 === 0
    ? ($prices[$count / 2 - 1] + $prices[$count / 2]) / 2
    : $prices[($count - 1) / 2];

$bedrooms = array_keys($bedroomSet);
sort($bedrooms);
$bedroomLabels = array_map(function ($n) { return $n . ' BHK'; }, $bedrooms);

echo json_encode([
    'success' => true,
    'data' => [
        'locality'       => $locality,
        'listing_count'  => $count,
        'min_price'      => formatRupees(min($prices)),
        'max_price'      => formatRupees(max($prices)),
        'median_price'   => formatRupees($median),
        'bedroom_types'  => $bedroomLabels,
    ],
]);
