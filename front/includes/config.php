<?php
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');
/**
 * Tatito Developers And Promoters — shared site configuration & sample data
 * Swap the arrays below for DB queries later; every include reads from here
 * so the header, homepage sections, and footer stay in sync.
 */

// ---------------------------------------------------------------
// Database connection
// ---------------------------------------------------------------
$db_host = "localhost";
$db_user = "u424007618_Developers";
$db_pass = "Tatitodevelopers@123";
$db_name = "u424007618_Developers";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$site = [
    'name'      => 'Tatito Developers And Promoters',
    'tagline'   => 'Find Your Dream Home, Faster.',
    'phone'     => '1800 120 0000',
    'favicon'   => 'assets/images/favicon.png',
];

$cities = [
    ['name' => 'Mumbai',      'listings' => '19.8K', 'trend' => 'up'],
    ['name' => 'Bangalore',   'listings' => '9.3K',  'trend' => 'flat'],
    ['name' => 'Gurgaon',     'listings' => '20.0K', 'trend' => 'down'],
    ['name' => 'Pune',        'listings' => '8.3K',  'trend' => 'flat'],
    ['name' => 'Noida',       'listings' => '6.8K',  'trend' => 'down'],
    ['name' => 'Hyderabad',   'listings' => '9.2K',  'trend' => 'flat'],
    ['name' => 'Thane',       'listings' => '8.5K',  'trend' => 'down'],
    ['name' => 'Delhi',       'listings' => '6.1K',  'trend' => 'up'],
];

// Signature "Price Pulse" ticker data — quarter-on-quarter asking price per sq.ft
$pricePulse = [
    ['city' => 'Mumbai',    'rate' => '₹38.6K', 'change' => '+2.4%', 'dir' => 'up'],
    ['city' => 'Gurgaon',   'rate' => '₹14.9K', 'change' => '-1.7%', 'dir' => 'down'],
    ['city' => 'Bangalore', 'rate' => '₹12.1K', 'change' => '+0.4%', 'dir' => 'up'],
    ['city' => 'Pune',      'rate' => '₹13.0K', 'change' => '+0.4%', 'dir' => 'up'],
    ['city' => 'Noida',     'rate' => '₹11.8K', 'change' => '-4.9%', 'dir' => 'down'],
    ['city' => 'Thane',     'rate' => '₹15.3K', 'change' => '-2.8%', 'dir' => 'down'],
    ['city' => 'Delhi',     'rate' => '₹18.4K', 'change' => '+3.1%', 'dir' => 'up'],
    ['city' => 'Hyderabad', 'rate' => '₹9.3K',  'change' => '0.0%',  'dir' => 'flat'],
];

$projects = [
    ['name' => 'Kalpataru Vian',   'locality' => 'Andheri West, Mumbai',   'price' => 'Price on request', 'tag' => 'New Launch',      'img' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=80'],
    ['name' => 'Mahindra Rainforest', 'locality' => 'Bhandup West, Mumbai', 'price' => '₹1.76 Cr onwards', 'tag' => 'Under Construction', 'img' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=600&q=80'],
    ['name' => 'Godrej Vanantara', 'locality' => 'Bannerghatta, Bangalore', 'price' => '₹1.49 Cr onwards', 'tag' => 'New Launch',      'img' => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=600&q=80'],
    ['name' => 'Conscient Parq',   'locality' => 'Sector 80, Gurgaon',      'price' => '₹3.40 Cr onwards', 'tag' => 'Ready to Move',   'img' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=600&q=80'],
    ['name' => 'Shapoorji Joyville', 'locality' => 'Hinjewadi, Pune',       'price' => '₹89.0 L onwards',  'tag' => 'Under Construction', 'img' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=600&q=80'],
    ['name' => 'M3M Jacob & Co',   'locality' => 'Sector 97, Noida',        'price' => '₹2.50 Cr onwards', 'tag' => 'New Launch',      'img' => 'https://images.unsplash.com/photo-1613977257363-707ba9348227?w=600&q=80'],
];

$developers = [
    ['name' => 'Brigade',      'projects' => 150, 'years' => 33],
    ['name' => 'DLF',          'projects' => 112, 'years' => 70],
    ['name' => 'Puravankara',  'projects' => 105, 'years' => 44],
    ['name' => 'Rustomjee',    'projects' => 68,  'years' => 23],
    ['name' => 'M3M',          'projects' => 59,  'years' => 12],
    ['name' => 'Emaar',        'projects' => 57,  'years' => 14],
];

$stats = [
    ['value' => '2M+',   'label' => 'Buyers served'],
    ['value' => '₹8.4B', 'label' => 'Homes sold'],
    ['value' => '1M+',   'label' => 'Active listings'],
    ['value' => '4.7',   'label' => 'Avg. review score'],
];

// Primary nav — built from the `menus` and `submenus` tables.
// Structure: [ 'Buy' => [ ['name' => 'Apartments', 'link' => 'apartments.php'], ... ], ... ]
// A menu with no active submenus becomes an empty array => rendered as a plain link.
$navMenu = [];

$menuResult = $conn->query("SELECT id, menu_name FROM menus WHERE status='Active' ORDER BY menu_order ASC");

if ($menuResult) {
    while ($menu = $menuResult->fetch_assoc()) {

        $menuId   = $menu['id'];
        $menuName = $menu['menu_name'];

        $navMenu[$menuName] = [];

        $subStmt = $conn->prepare("SELECT submenu_name, submenu_link FROM submenus WHERE menu_id=? AND status='Active' ORDER BY submenu_order ASC");
        $subStmt->bind_param("i", $menuId);
        $subStmt->execute();
        $subResult = $subStmt->get_result();

        while ($sub = $subResult->fetch_assoc()) {
            $navMenu[$menuName][] = [
                'name' => $sub['submenu_name'],
                'link' => $sub['submenu_link'] ?: '#',
            ];
        }

        $subStmt->close();
    }

}