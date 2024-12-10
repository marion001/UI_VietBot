<?php
#Code By: Vũ Tuyển
#Designed by: BootstrapMade
#Facebook: https://www.facebook.com/TWFyaW9uMDAx

#tăng giới hạn bộ nhớ cho PHP
//ini_set('memory_limit', '512M');
ini_set('memory_limit', '1G');
ini_set('upload_max_filesize', '300M');
ini_set('post_max_size', '300M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$Title_HTML = "Vietbot Assistant";
$Title_HEAD_BAR = "Vietbot";
//đường dẫn path Vietbot python
$VietBot_Offline_Path = "/home/pi/vietbot_offline/"; #/home/pi/vietbot_offline/

//Đường dẫn ui html
$HTML_Vietbot_Offline = getcwd();

$Logo_File = "assets/img/logo.png";
#$Avata_File = "assets/img/no_avata.jpg";
// Lấy đường dẫn đầy đủ tới tệp PHP hiện tại
//$current_file_path = __FILE__;

// Lấy đường dẫn thư mục chứa tệp PHP
$directory_path = dirname(__FILE__);

$HostName = gethostname();

#Lấy tên người dùng hệ thống ví dụ: pi
$GET_current_USER = get_current_user();

#echo $GET_current_USER;

//Lấy địa chỉ IP của máy chủ
$serverIp = $_SERVER['SERVER_ADDR'];

//Lấy địa chỉ IP của người dùng khi truy cập
$userIp = $_SERVER['REMOTE_ADDR'];

//Lấy giao thức (http hoặc https)
$Protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

//Lấy tên miền (ví dụ: 192.168.14.113)
$Domain = $_SERVER['HTTP_HOST'];

//Lấy đường dẫn tới file hiện tại (ví dụ: /html/includes/php_ajax/Media_Player_Search.php)
$Path = $_SERVER['REQUEST_URI'];

//Kết hợp thành URL đầy đủ
$Current_URL = $Protocol . $Domain . $Path;

//Đường dẫn đến tệp JSON
$Config_filePath = $VietBot_Offline_Path.'src/config.json';

//địa chỉ URL Repo Github Vietbot Program, địa chỉ này sẽ dùng cho cập nhật, không được chỉnh sửa
#https://github.com/phanmemkhoinghiep/vietbot_offline
$Github_Repo_Vietbot_Program = "https://github.com/phanmemkhoinghiep/vietbot_offline";

//Địa Chỉ URL Repo Github Interface
$Github_Repo_Vietbot_Interface = "https://github.com/marion001/UI_VietBot";


//Danh sách các file, thư mục cần loại trừ không yêu cầu cấp quyền chmod 0777
$excluded_items_chmod = ['.', '..', '__pycache__', 'mp3', 'tts_saved', 'robotx.txt'];

//Khởi tạo biến để lưu dữ liệu config.json
$Config = null; 


#Đọc nội dung file Config
if (file_exists($Config_filePath)) {
    $Config = json_decode(file_get_contents($Config_filePath), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo 'Có lỗi xảy ra khi giải mã JSON: ' . json_last_error_msg();
		// Đặt dữ liệu thành null nếu có lỗi json
        $Config = null;
    }
} else {
    echo '<br/><br/><center><h1>Tệp JSON Vietbot không tồn tại ở đường dẫn: ' . $Config_filePath.'</h1><center>';
	//Đặt dữ liệu thành null nếu tệp không tồn tại
    $Config = null;
	exit();
}


$Download_Path = "Backup_Upgrade/Download";
$Extract_Path = "Backup_Upgrade/Extract";


//Thông tin kết nối SSH
#sudo apt-get install php-ssh2
#$ssh_host = $Config['ssh_server']['ssh_host'];
$ssh_host = $serverIp;
$ssh_port = $Config['web_interface']['ssh_server']['ssh_port'];
$ssh_user = $Config['web_interface']['ssh_server']['ssh_username'];
$ssh_password = $Config['web_interface']['ssh_server']['ssh_password'];


// Tìm tất cả các tệp có tên bắt đầu bằng 'avata_user'
$files = glob('assets/img/avata_user.*');
// Kiểm tra xem có tệp nào không
if (count($files) > 0) {
    foreach ($files as $file_path) {
        // Lấy tên tệp bao gồm phần mở rộng
        $file_name = basename($file_path);
        // Hiển thị tên tệp
        $Avata_File = "assets/img/".htmlspecialchars($file_name);
    }
} else {
    $Avata_File = "assets/img/no-face.png";
}



?>