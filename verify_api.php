<?php
// Script kiểm tra nhanh các API sau khi sửa lỗi
$base = "http://localhost:8000/api/v1";

function test($url) {
    echo "Testing $url... ";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Fake a minimal token if needed, or just check for 401 vs 500
    // Since we just want to check SQL errors (500), we don't necessarily need a valid user for some GETs if public
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "Status: $code\n";
    if ($code == 500) {
        echo "ERROR: " . substr($resp, 0, 200) . "\n";
    }
    curl_close($ch);
}

// Test inventory (the one that reported SQL error)
test($base . "/inventory");
test($base . "/products");
test($base . "/customers");
test($base . "/import-receipts");
test($base . "/production-orders");
