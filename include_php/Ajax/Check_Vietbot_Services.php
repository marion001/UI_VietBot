<?php
include "../../Configuration.php";
$VUTUYEN = "vietbot_offline/src/start.py";

// Chạy lệnh "ps aux" và lấy output
exec("ps aux", $output);

// Khởi tạo một mảng để lưu kết quả
$result = array();

// Lặp qua từng dòng output để kiểm tra tiến trình
$processRunning = false;
foreach ($output as $line) {
    if (strpos($line, $VUTUYEN) !== false) {
        // Nếu dòng nào chứa $VUTUYEN, đặt cờ là true và thoát khỏi vòng lặp
        $processRunning = true;
        break;
    }
}

// Kiểm tra cờ để xác định xem tiến trình có đang chạy hay không
if ($processRunning) {
    $result['services']['message'] = "Vietbot đang chạy";
    $result['services']['status'] = "online";
} else {
    $result['services']['message'] = "Vietbot không được khởi chạy";
    $result['services']['status'] = "offline";
}

// Chuyển đổi mảng thành JSON
$json_result = json_encode($result);

// Sử dụng cURL để gửi yêu cầu GET đến URL http://192.168.14.110:5000/
$ch = curl_init('http://' . $serverIP . ':' . $Port_Vietbot . '/');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
// Thực hiện yêu cầu và lấy kết quả
$response = curl_exec($ch);
// Kiểm tra nếu có lỗi trong quá trình gửi yêu cầu
if(curl_errno($ch)) {
    $error_message = 'Lỗi kết nối tới API Vietbot, mã lỗi: ' . curl_error($ch);
    //echo 'Error: ' . $error_message;
    
    // Gán thông báo lỗi vào mảng $result['api']
    $result['api']['message'] = $error_message;
    $result['api']['status'] = "offline";
} else {
    // Lấy mã HTTP trả về từ yêu cầu
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Kiểm tra nếu mã HTTP là 200 (OK)
    if ($http_code == 200) {
        $result['api']['message'] = "Kết nối tới API Vietbot thành công";
        $result['api']['status'] = "online";
    } else {
        $error_message = "Lỗi kết nối tới API Vietbot, mã lỗi: $http_code";
        //echo 'Error: ' . $error_message;
        
        // Gán thông báo lỗi vào mảng $result['api']
        $result['api']['message'] = $error_message;
        $result['api']['status'] = "offline";
    }
}
// Đóng kết nối cURL
curl_close($ch);

// Chuyển đổi mảng thành JSON và in ra
echo json_encode($result);
?>
