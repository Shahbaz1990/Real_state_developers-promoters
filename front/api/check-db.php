<?php
require_once __DIR__ . '/../../backend/config.php';

if ($conn) {
    echo "Database connection successful!";
} else {
    echo "Database connection failed!";
}
?>