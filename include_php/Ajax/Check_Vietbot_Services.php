<?php
include "../../Configuration.php";
$VUTUYEN = "vietbot_offline/src/start.py";

// Hàm để kiểm tra xem một quy trình có đang chạy hay không
function isProcessRunning($processName) {
    exec("ps aux | grep '$processName'", $output);
    foreach ($output as $line) {
        if (strpos($line, $processName) !== false) {
            return true;
        }
    }
    return false;
}

// Hàm để gửi yêu cầu cURL và xử lý kết quả
function sendCurlRequest($url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => 0
    ));
    $response = curl_exec($ch);
    if(curl_errno($ch)) {
        return array(
            'message' => 'Lỗi kết nối tới API Vietbot, mã lỗi:' . curl_error($ch),
            'status' => 'offline'
        );
    } else {
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code == 200) {
            return array(
                'message' => 'Kết nối tới API Vietbot thành công',
                'status' => 'online',
                'data' => json_decode($response)
            );
        } else {
            return array(
                'message' => "Lỗi kết nối tới API Vietbot, mã lỗi: $http_code",
                'status' => 'offline'
            );
        }
    }
    curl_close($ch);
}

// Kiểm tra trạng thái của Vietbot
$result['services'] = array();
if (isProcessRunning($VUTUYEN)) {
    $result['services']['message'] = "Vietbot đang chạy chế độ Auto";
    $result['services']['status'] = "online";
} else {
    if (isProcessRunning('python3 start.py')) {
        $result['services']['message'] = "Vietbot đang chạy ở chế độ thủ công terminal: python3 start.py";
        $result['services']['status'] = "online";
    } else {
        $result['services']['message'] = "Vietbot không được khởi chạy";
        $result['services']['status'] = "offline";
    }
}

// Gửi yêu cầu cURL đến API Vietbot
$api_url = 'http://' . $serverIP . ':' . $Port_Vietbot . '/';
$api_response = sendCurlRequest($api_url);
$result['api'] = $api_response;
$result['api'] = $api_response;

// Gửi yêu cầu cURL để lấy trạng thái trò chuyện
$conversation_url = 'http://' . $serverIP . ':' . $Port_Vietbot . '/?data=conversation_state';
$conversation_response = sendCurlRequest($conversation_url);
$result['conversation_state'] = $conversation_response;

// Gửi yêu cầu cURL để lấy trạng thái phản hồi
$wakeup_reply_state_url = 'http://' . $serverIP . ':' . $Port_Vietbot . '/?data=wakeup_reply_state';
$wakeup_reply_state_response = sendCurlRequest($wakeup_reply_state_url);
$result['wakeup_reply_state'] = $wakeup_reply_state_response;

// In ra kết quả
echo json_encode($result);
?>
