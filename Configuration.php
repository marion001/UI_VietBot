<?php
//Code By: Vũ Tuyển
//Facebook: https://www.facebook.com/TWFyaW9uMDAx
//Bỏ qua hiển thị lỗi trên màn hình nếu có
//Mail: vietbotsmartspeaker@gmail.com
@error_reporting(0);
@date_default_timezone_set('Asia/Ho_Chi_Minh');
// Khởi động session
session_start();
$session_expiration = 86400; //Cài đặt thời gian sống của session cookie thành 1 ngày 1 ngày (1 giờ = 3600 giây)
session_set_cookie_params($session_expiration);

$SESSION_ID_Name = "Marion001"; //để nguyên
$HostName = gethostname();
$PHP_SELF = $_SERVER['PHP_SELF'];
$GET_current_USER = get_current_user();
$serverIP = $_SERVER['SERVER_ADDR'];

//Nhóm Facebook và GITHUB, UI VietBot, Version
$FacebookGroup = "https://www.facebook.com/groups/1082404859211900";
$GitHub_VietBot_OFF = "https://github.com/phanmemkhoinghiep/vietbot_offline";
$UI_VietBot = "https://github.com/marion001/UI_VietBot";
$Vietbot_Version = "https://raw.githubusercontent.com/phanmemkhoinghiep/vietbot_offline/beta/src/version.json";
$UI_Version = "https://raw.githubusercontent.com/marion001/UI_VietBot/main/version.json";

//Mật Khẩu Đăng Nhập Các Chức Năng Web UI
//$Pass_Login_UI = "21232f297a57a5a743894a0e4a801fc3"; //Mã hóa md5 = admin
//Mật Khẩu Đăng Nhập Trình Quản Lý File Manager
$Pass_Login_File_Manager = "admin"; // admin

//Cấu Hình Send Mail
//App Passwords: https://myaccount.google.com/apppasswords
//Tài Khoản Gmail
$Mail_Gmail = "dmlldGJvdHNtYXJ0c3BlYWtlckBnbWFpbC5jb20="; //Giữ Nguyên  B64
//Key App Passwords (Thay Cho Mật Khẩu) Của Gmail
$Mail_APP_Passwords = "Y2J1aWZjemJwZGx0enRpZg==";  //Giữ Nguyên  B64
$Mail_Host = "smtp.gmail.com";  //Giữ Nguyên
$Mail_Port = 587;  //Giữ Nguyên
$Mail_SMTPSecure = "tls";  //Giữ Nguyên


//upload MP3 Media Player 
$Upload_Max_Size = "300"; //MB
//Key youtube mã hóa base64
$apiKeyYoutube = "QUl6YVN5RFBva1R2eUI3WEdhQ3JwQnB0U25xd0RET3JaOW9WNnJR"; // Thay YOUR_YOUTUBE_API_KEY bằng khóa API YouTube của bạn

//ĐƯờng Dẫn VietBot Chính
$Path_Vietbot_src = "/home/pi/vietbot_offline";

//Đường dẫn nhánh để hết mặc định
$DuognDanThuMucJson = $Path_Vietbot_src.'/src';
$DuognDanUI_HTML = $Path_Vietbot_src.'/html';
$PathResources = $Path_Vietbot_src.'/resources';
$directorySound = $Path_Vietbot_src.'/src/sound/default/';
$Lib_Hotword = $Path_Vietbot_src.'/resources/picovoice/lib';

//SSH Tải Khoản, Mật Khẩu Đăng Nhập SSH (Bắt Buộc Phải Nhập Để Dùng Các Lệnh Hệ Thống)
$SSH_TaiKhoan = "pi"; //Tài Khoản Đăng Nhập pi SSH Của Bạn
$SSH_MatKhau = "vietbot"; //Mật Khẩu Đăng Nhập pi SSH Của Bạn
$SSH_Port = "22"; //Mặc Định: "22"

//Thông Báo Lỗi Khi Kết Nối SSH
$E_rror = "<center><h1>Đăng Nhập SSH Thất Bại, Kiểm Tra Lại Tài Khoản Hoặc Mật Khẩu</h1></center>";
$E_rror_HOST = "<center><h1>Không thể kết nối tới máy chủ SSH, Kiểm Tra lại ip</h1></center>";

//Giới hạn file backup Firmware src Vietbot tar.gz (Khi Cập Nhật Firmware)
$maxBackupFiles = "10";

//Giới hạn file backup UI  Vietbot tar.gz (Khi Cập Nhật Firmware)
$maxBackupFilesUI = "5";

//Giới hạn ngày kỷ niệm: 10 giá trị
$Limit_NgayKyNiem = "15"; 

//Giới Hạn Số Lượng Báo, Tin Tức: 3 giá trị
$Limit_BaoTinTuc = "5"; 

//Giới hạn số lượng Danh Bạ Người Gửi Tele
$Limit_Telegram = "3"; 

//Giới hạn Phản Hồi Khi Được Đánh Thức
$Limit_Wakeup_Reply = "7";
//Cài Văn Bản Mặc Định Nếu Biến $Limit_Wakeup_Reply bị xóa hết
$Limit_Wakeup_Reply_Default_Response = "Dạ";

//giới hạn số lượng file config cần backup (Khi Nội Dụng config.json bị thay đổi ở dao diện)
$Limit_Config_Backup = "10";

//giới hạn số lượng file config cần backup (Khi Nội Dụng config.json bị thay đổi ở dao diện)
$Limit_Skill_Backup = "10";

//Limit Radio Đài Báo
$Limit_Radio = "10";

//Limit Nội Dung Thông Báo Chờ
$Limit_Pre_Answer = "3";

//Thời gian đếm ngược tải lại trang khi update UI và Vietbot
$Page_Load_Time_Countdown = "6"; //Giây

//thời gian đếm ngược đọc log màn hình 1000 = 1 giây
$Log_Load_Time_Countdown = "2000"; //2000 = 2Giây

//thời gian chờ time out media player api
$Time_Out_MediaPlayer_API = "4000"; //4000 bằng 4 giây

///////////////////////////////////////////////////////////////////////////////
//API webui Config Setting
$API_Messenger_Disabled = "Thao tác thất bại, API WEB UI chưa được bật";
$allowedCommands_ALL = "all"; //"all" Biến cho phép chạy tất cả các lệnh
// Danh sách chỉ cho phép chạy các lệnh an toàn khi bỏ chữ "all" bên trên
$allowedCommands = "ls,dir,touch,reboot,uname"; //Tester
$apiKey = 'vietbot'; //api key, user cần mã hóa api key này dạng md5 3f406f61a2b5053b53cda80e0320a60b


///////////////////////////////////////////////////////////////////////////////
$Data_Json_Skill = json_decode(file_get_contents("$DuognDanThuMucJson"."/skill.json"));

$dataVTGET = json_decode(file_get_contents("$DuognDanThuMucJson"."/config.json"));

$dataVersionUI = json_decode(file_get_contents("$DuognDanUI_HTML"."/version.json"));

$dataVersionVietbot = json_decode(file_get_contents("$DuognDanThuMucJson"."/version.json"));

$action_json = json_decode(file_get_contents("$DuognDanThuMucJson"."/action.json"));

$object_json = json_decode(file_get_contents("$DuognDanThuMucJson"."/object.json"));


$PORT_CHATBOT = $dataVTGET->smart_config->web_interface->port;
$MYUSERNAME = $dataVTGET->smart_config->user_info->name;
$Web_UI_Login = $dataVTGET->smart_config->block_updates->web_ui_login;
$Web_UI_Enable_Api = $dataVTGET->smart_config->block_updates->enable_api;

//Vị Trí, Địa Chỉ
$wards_Lang = $dataVTGET->smart_config->user_info->address->wards; 
$wards_Huyen = $dataVTGET->smart_config->user_info->address->district;
$wards_Tinh = $dataVTGET->smart_config->user_info->address->province;

//Lấy Dữ Liệu Config Chặn Cập Nhật
$block_updates_vietbot_program = $dataVTGET->smart_config->block_updates->vietbot_program;
$block_updates_web_ui = $dataVTGET->smart_config->block_updates->web_ui;
//lấy dữ liệu config kiểm tra trạng thái hiển thị log hiện tại
$check_current_log_status = $dataVTGET->smart_config->logging_type;

$apiKeyWeather = $Data_Json_Skill->weather->openweathermap_key;
?>
