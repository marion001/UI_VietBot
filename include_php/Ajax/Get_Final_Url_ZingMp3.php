<?php

function getFinalUrl($url) {
    $ch = curl_init($url);
    // Thiết lập cURL để trả về dữ liệu thay vì hiển thị nó
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Thiết lập cURL để theo dõi tất cả các chuyển hướng
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    // Thiết lập thời gian chờ
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    // Tắt chế độ DNS lookup (chỉ sử dụng IPv4)
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    // Tắt SSL verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // Thực hiện yêu cầu cURL và lấy thông tin về chuyển hướng
    curl_exec($ch);
    $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    // Đóng kết nối cURL
    curl_close($ch);
    return $finalUrl;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['url'])) {
    $url = $_GET['url'];
    $finalUrl = getFinalUrl($url);
    echo json_encode(['finalUrl' => $finalUrl]);
} else {
    echo json_encode(['error' => 'Invalid request']);
}

?>
