<?php
// Include tệp Configuration.php
include "../../Configuration.php";

// Include thư viện Net_SSH2.php
include("../../assets/lib_php/Net/SSH2.php");

// Khởi tạo kết nối SSH
$connection = ssh2_connect($serverIP, $SSH_Port);

// Kiểm tra kết nối
if (!$connection) {
    die("Không thể kết nối đến máy chủ SSH.");
}

// Xác thực với tên người dùng và mật khẩu SSH
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {
    die("Xác thực SSH không thành công.");
}

// Thực thi lệnh SSH và lấy kết quả
$stream = ssh2_exec($connection, 'amixer scontrols | grep "Output"');

// Kiểm tra luồng SSH
if (!$stream) {
    die("Không thể khởi tạo luồng SSH.");
}

// Thiết lập luồng đọc và lấy kết quả
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = stream_get_contents($stream_out);

// Phân tách các dòng
$lines = explode("\n", $output);

// Mảng để lưu giá trị của mỗi điều khiển âm thanh
$values = array();

// Biến đếm giá trị
$valueCount = 0;

// Duyệt qua từng dòng
foreach ($lines as $line) {
    // Kiểm tra nếu dòng không rỗng
    if (!empty($line)) {
        // Tìm vị trí của dấu '' đầu tiên
        $startPos = strpos($line, "'") + 1;
        // Tìm vị trí của dấu '' thứ hai
        $endPos = strpos($line, "'", $startPos);
        // Trích xuất giá trị trong dấu ''
        $controlValue = substr($line, $startPos, $endPos - $startPos);
        // Lưu giá trị tương ứng và tăng biến đếm giá trị
        $values[] = array(
            'amixer_scontrols_name' => $controlValue,
            'amixer_scontrols_id' => $valueCount++
        );
    }
}

// Chuyển đổi mảng thành JSON
$jsonData = json_encode($values, JSON_PRETTY_PRINT);

// Hiển thị dữ liệu JSON
echo $jsonData;
?>
