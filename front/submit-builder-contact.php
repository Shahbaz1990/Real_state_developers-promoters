<?php
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $page_id = isset($_POST['page_id']) ? (int) $_POST['page_id'] : 0;
    $page_title = isset($_POST['page_title']) ? trim($_POST['page_title']) : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
    $is_dealer = isset($_POST['is_dealer']) ? trim($_POST['is_dealer']) : 'no';
    $buy_timeline = isset($_POST['buy_timeline']) ? trim($_POST['buy_timeline']) : 'more_than_6';
    $interested_home_loan = isset($_POST['interested_home_loan']) ? 'yes' : 'no';
    $interested_site_visits = isset($_POST['interested_site_visits']) ? 'yes' : 'no';
    $terms_agree = isset($_POST['terms_agree']) ? 'yes' : 'no';
    
    // Validate
    if (empty($name) || empty($contact) || strlen(preg_replace('/[^0-9]/', '', $contact)) < 10) {
        $response['message'] = 'Please enter a valid 10-digit contact number.';
        echo json_encode($response);
        exit;
    }
    
    if ($terms_agree !== 'yes') {
        $response['message'] = 'Please agree to the Terms & Conditions and Privacy Policy.';
        echo json_encode($response);
        exit;
    }
    
    // Save to database (create table if needed)
    $tableCheck = $conn->query("SHOW TABLES LIKE 'builder_contact_enquiries'");
    if (!$tableCheck || $tableCheck->num_rows == 0) {
        $createTable = "CREATE TABLE IF NOT EXISTS builder_contact_enquiries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            page_id INT,
            page_title VARCHAR(255),
            name VARCHAR(100),
            contact VARCHAR(20),
            is_dealer VARCHAR(10),
            buy_timeline VARCHAR(50),
            interested_home_loan VARCHAR(10),
            interested_site_visits VARCHAR(10),
            terms_agree VARCHAR(10),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->query($createTable);
    }
    
    $stmt = $conn->prepare("INSERT INTO builder_contact_enquiries 
        (page_id, page_title, name, contact, is_dealer, buy_timeline, interested_home_loan, interested_site_visits, terms_agree) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param("issssssss", $page_id, $page_title, $name, $contact, $is_dealer, $buy_timeline, $interested_home_loan, $interested_site_visits, $terms_agree);
        
        if ($stmt->execute()) {
            // Get seller contact from owner_phone in trending_projects
            $seller_contact = '';
            if ($page_id > 0) {
                $projStmt = $conn->prepare("SELECT owner_phone FROM trending_projects WHERE id = ?");
                if ($projStmt) {
                    $projStmt->bind_param("i", $page_id);
                    $projStmt->execute();
                    $projResult = $projStmt->get_result();
                    if ($projRow = $projResult->fetch_assoc()) {
                        if (!empty($projRow['owner_phone'])) {
                            $seller_contact = $projRow['owner_phone'];
                        }
                    }
                    $projStmt->close();
                }
            }
            if (empty($seller_contact)) {
                $seller_contact = '+91 98480 22338'; // Verified builder support desk
            }
            
            $_SESSION['form_filled'] = true;
            $response['status'] = 'success';
            $response['message'] = 'Your request has been submitted successfully!';
            $response['seller_contact'] = $seller_contact;
        } else {
            $response['message'] = 'Failed to submit enquiry. Please try again.';
        }
        $stmt->close();
    } else {
        $response['message'] = 'Database error. Please try again later.';
    }
}

echo json_encode($response);
exit;