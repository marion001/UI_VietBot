<?php
/**
 * File: secure_execute.php
 * Description: Example code to execute shell commands securely through an API using cURL in PHP.
 * Author: Vũ Tuyển
 * Facebook: https://www.facebook.com/TWFyaW9uMDAx
 * Version: 1.0
 */
include "Configuration.php";
include "./include_php/INFO_OS.php";
$version = "1.0";
$information = array(
        'api_version' => $version,
        'github_vietbot_offline' => $GitHub_VietBot_OFF,
        'ui_vietbot' => $UI_VietBot,
        'author' => $MYUSERNAME
   
);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array(
        'message' => 'Phương pháp truy vấn không được phép.',
        'http_response_code' => 405,
		'output_api' => null,
		'information' => $information
		));
    exit();
}
$input = file_get_contents('php://input');
$data = json_decode($input, true);
if (!isset($data['command']) || !isset($data['api_key'])) {
    http_response_code(400); // Bad Request
    echo json_encode(array(
		'message' => 'Command or API key not provided.',
		'http_response_code' => 400,
		'output_api' => null,
		'information' => $information
		));
    exit();
}
$command = $data['command'];
$providedApiKey = $data['api_key'];
// Kiểm tra xác thực API key
if ($providedApiKey !== md5($apiKey)) {
    http_response_code(401); // Unauthorized
    echo json_encode(array(
		'message' => 'Xác thực lỗi! Vui lòng kiểm tra lại key api.',
		'http_response_code' => 401,
		'output_api' => null,
		'information' => $information
		));
    exit();
}
// Thực hiện kiểm tra lệnh an toàn trước khi thực thi
if (!isSafeCommand($command)) {
    http_response_code(403); // Forbidden
    echo json_encode(array(
		'message' => 'Dữ liệu được gửi tới api đã bị chặn không được phép thực thi.',
		'http_response_code' => 403,
		'allowed_commands' => $allowedCommands,
		'output_api' => null,
		'information' => $information
		));
    exit();
}

if ($command === "reboot") {
    if (isset($data['api_key']) && $data['api_key'] === md5($apiKey)) {
        // exec("sudo reboot");
        $rebootResult = "TEST Reboot command executed successfully.";
        echo json_encode(array(
            'message' => $rebootResult,
            'http_response_code' => 200,
            'output_api' => null,
            'information' => $information
        ));
        exit();
    } else {
		unauthorized();
    }
}


if ($command === "restart_vietbot") {
    if (isset($data['api_key']) && $data['api_key'] === md5($apiKey)) {
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die($E_rror_HOST);}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die($E_rror);}
$stream = ssh2_exec($connection, 'systemctl --user restart vietbot');
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output =  stream_get_contents($stream_out);
        echo json_encode(array(
            'message' => 'successfully',
            'http_response_code' => 200,
            'output_api' => $output,
            'information' => $information
        ));
        exit();
    } else {
		unauthorized();
    }
}

if ($command === "info") {
    if (isset($data['api_key']) && $data['api_key'] === md5($apiKey)) {
        echo json_encode(array(
            'message' => 'successfully',
            'http_response_code' => 200,
            'output_api' => null,
			'info_vietbot' => array(
				'vietbot_version' => array(
					'current_version' => 'dsdsd',
					'new_version' => 'dsdsd',
				),
				'vietbot_update' => true, //hoặc false tùy theo người viết code =))
			),
            'info_os' => array(
				'host_name' =>  gethostname(),
				'uname_a' =>  php_uname(),
				'kernel_version' => php_uname('v'),
				'machine_type' => php_uname('m'),
				'os_version' => php_uname('r'),
				'server_name' => $_SERVER['SERVER_NAME'],
				'client_ip' => get_client_ip(),
				'php_version' => phpversion(),
				'used_cpu_capacity' => $cpuload."%",
				'cpu_count' => rtrim($cpu_count, "\n"),
				'uptime' => $ut[0]." Ngày, " .$ut[1].":".$ut[2]."'",
				'disk' => array(
						'disk_total' => $disktotal."GB",
						'disk_used' => $diskused."GB",
						'disk_free' => $diskfree."GB"
				),
				'ram' => array(
						'ram_total' => $memtotal."GB",
						'ram_used' => $memused."GB",
						'ram_free' => $memavailable."GB"
				)
			),
            'information' => $information
        ));
        exit();
    } else {
		unauthorized();
    }
}

// Thực thi lệnh shell sử dụng shell_exec
//$output = shell_exec($command);

//SSH2
$connection = ssh2_connect($serverIP, $SSH_Port); // Thay 'hostname' bằng địa chỉ IP hoặc tên miền của máy chủ SSH
if (ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) { // Thay 'username' và 'password' bằng thông tin đăng nhập SSH
    $stream = ssh2_exec($connection, $command); // Thực thi lệnh SSH
    stream_set_blocking($stream, true);
    $output = stream_get_contents($stream); // Lấy kết quả từ luồng kết nối
	$messageee = "successfully";
	$http_response_code = 200;
    fclose($stream);
} else {
    // Xử lý lỗi xác thực SSH
    $output = null;
    $messageee = "Kết nối tới ssh thất bại.";
	$http_response_code = 401;
}

//$output = str_replace("\n", "", $output);
// Thiết lập header cho response
header('Content-Type: application/json');
// Trả về kết quả từ lệnh shell
echo json_encode(array(
	'message' => $messageee,
	'http_response_code' => $http_response_code,
	'output_api' => $output,
	'information' => $information
	));

// Kiểm tra xem chuỗi lệnh có chứa từ khóa trong danh sách an toàn không
function isSafeCommand($command) {
    global $allowedCommands_ALL, $allowedCommands;
    if ($allowedCommands_ALL === "all") {
        return true; // Cho phép chạy tất cả các lệnh nếu có chữ all trong biến file Configuration.php
    } else {
        // Danh sách các lệnh cho phép
        $safeCommands = explode(',', $allowedCommands);
        // Kiểm tra xem lệnh có nằm trong danh sách cho phép không
        foreach ($safeCommands as $safeCommand) {
            if (strpos($command, $safeCommand) !== false) {
                return true;
            }
        }
        return false;
    }
}
//thông báo xác thực key nếu thất bại
function unauthorized(){
        http_response_code(401); // Unauthorized
        echo json_encode(array(
            'message' => 'Xác thực lỗi! Vui lòng kiểm tra lại key api.',
            'http_response_code' => 401,
            'output_api' => null,
            'information' => $information
        ));
        exit();
}
?>
