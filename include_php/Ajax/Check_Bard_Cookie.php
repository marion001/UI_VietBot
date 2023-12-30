<?php
include "../../Configuration.php";
include("../../assets/lib_php/Net/SSH2.php");
// Lấy giá trị cookie từ URL
$cookie1PSID = $_GET['Cookie_1PSID'] ?? '';
$cookie1PSIDTS = $_GET['Cookie_1PSIDTS'] ?? '';
$cookie1PSIDCC = $_GET['Cookie_1PSIDCC'] ?? '';

// Kiểm tra nếu có tất cả các giá trị cookie
if ($cookie1PSID && $cookie1PSIDTS && $cookie1PSIDCC) {
    // Tạo lệnh Python với các giá trị cookie
    $pythonCommand = "python3 1.py {$cookie1PSID} {$cookie1PSIDTS} {$cookie1PSIDCC}";

$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {
    die("Không thể kết nối đến máy chủ SSH.");
}

if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {
    die("Xác thực SSH không thành công.");
}

// Sử dụng $encodedKey thay vì $accessKey trong lệnh SSH
$stream = ssh2_exec($connection, $pythonCommand);

if (!$stream) {
    die("Không thể khởi tạo luồng SSH.");
}

stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output =  stream_get_contents($stream_out);

echo $output;

} else {
    // Thông báo nếu bị thiếu giá trị cookie
    echo "Thiếu giá trị cookie đầu vào. Vui lòng cung cấp đủ giá trị Cookie_1PSID, Cookie_1PSIDTS và Cookie_1PSIDCC.";
}
?>
