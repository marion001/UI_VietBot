<?php
//Code By: Vũ Tuyển
//Facebook: https://www.facebook.com/TWFyaW9uMDAx
//Bỏ qua hiển thị lỗi trên màn hình nếu có
@error_reporting(0);
@date_default_timezone_set('Asia/Ho_Chi_Minh');

//Nhóm Facebook và GITHUB VietBot
$FacebookGroup = "https://www.facebook.com/groups/1082404859211900";
$GitHub_VietBot_OFF = "https://github.com/phanmemkhoinghiep/vietbot_offline";

//Mật Khẩu Đăng Nhập Phần Quản Lý File
$Pass_Login = "admin";

//Đường dẫn tới thư mục chứa các file json: /home/pi/vietbot_offline/src/
//Dấu "/" phải có ở đầu và ở cuối
$DuognDanThuMucJson = "/home/pi/vietbot_offline/src/"; //Để Mặc Định
$DuognDanUI_HTML = "/var/www/html/"; 				   //Để Mặc Định

//SSH Tải Khoản, Mật Khẩu Đăng Nhập SSH (Bắt Buộc Phải Nhập Để Dùng Các Lệnh Hệ Thống)
$SSH_TaiKhoan = "pi"; //Tài Khoản Đăng Nhập pi SSH Của Bạn
$SSH_MatKhau = "21041997"; //Nật Khẩu Đăng Nhập pi SSH Của Bạn
$SSH_Port = "22"; //Mặc Định: "22"

$E_rror = "Đăng Nhập SSH Thất Bại, Kiểm Tra Lại Tài Khoản Hoặc Mật Khẩu";
$E_rror_HOST = "Không thể kết nối tới máy chủ SSH";

//Giới hạn ngày kỷ niệm: 10 giá trị
$Limit_NgayKyNiem = "15"; 

//Giới Hạn Số Lượng Báo, Tin Tức: 3 giá trị
$Limit_BaoTinTuc = "5"; 

//Giới hạn số lượng Danh Bạ Người Gửi Tele
$Limit_Telegram = "3"; 

//Giới hạn Phản Hồi Khi Được Đánh Thức
$Limit_Wakeup_Reply = "7";

//giới hạn số lượng file config cần backup
//các file backup config.json nằm trong đường dẫn: /var/www/html/include_php/Backup_Config
$Limit_Config_Backup = "10";

//Limit Radio Đài Báo
$Limit_Radio = "10";

//Limit Nội Dung Thông Báo Chờ
$Limit_Pre_Answer = "3";

//Đọc vài dữ liệu của config.json
$jsonSKILL = file_get_contents("$DuognDanThuMucJson"."skill.json");
$Data_Json_Skill = json_decode($jsonSKILL);


$jsonDatazXZ = file_get_contents("$DuognDanThuMucJson"."config.json");
$dataVTGET = json_decode($jsonDatazXZ);


$HOST_NAME_CHATBOT = $dataVTGET->smart_config->web_interface->hostname;
$PORT_CHATBOT = $dataVTGET->smart_config->web_interface->port;
$MYUSERNAME = $dataVTGET->smart_config->user_info->name;

$wards_Lang = $dataVTGET->smart_config->user_info->address->wards; //Xã
$wards_Huyen = $dataVTGET->smart_config->user_info->address->district;
$wards_Tinh = $dataVTGET->smart_config->user_info->address->province;
$wards_Duong = $dataVTGET->smart_config->user_info->address->street;

$apiKeyWeather = $Data_Json_Skill->weather->openweathermap_key;

$HostName = gethostname();
$PHP_SELF = $_SERVER['PHP_SELF'];
$GET_current_USER = get_current_user();
$serverIP = $_SERVER['SERVER_ADDR'];

?>