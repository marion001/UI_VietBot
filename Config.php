
<?php
#Code By: Vũ Tuyển
#Designed by: BootstrapMade
#Facebook: https://www.facebook.com/TWFyaW9uMDAx
include 'Configuration.php';
?>
<?php
if ($Config['web_interface']['login_authentication']['active']){
session_start();
// Kiểm tra xem người dùng đã đăng nhập chưa và thời gian đăng nhập
if (!isset($_SESSION['user_login']) ||
    (isset($_SESSION['user_login']['login_time']) && (time() - $_SESSION['user_login']['login_time'] > 43200))) {
    
    // Nếu chưa đăng nhập hoặc đã quá 12 tiếng, hủy session và chuyển hướng đến trang đăng nhập
    session_unset();
    session_destroy();
    header('Location: Login.php');
    exit;
}
// Cập nhật lại thời gian đăng nhập để kéo dài thời gian session
//$_SESSION['user_login']['login_time'] = time();
}
?>
<?php
#Thay đổi ngôn ngữ gọi hotword
if (isset($_POST['change_hotword_language'])) {
	
// Kết nối tới máy chủ SSH
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {
    die("Không thể kết nối tới máy chủ SSH");
}
// Xác thực SSH
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {
    die("Xác thực SSH không thành công.");
}
	
	$Lang = null;
    // Lấy giá trị từ form
    $SELECT_CHANGE_HOTWORD_LANG = $_POST['select_change_hotword_language'];
	
	if ($SELECT_CHANGE_HOTWORD_LANG === 'vi'){
		$Lang = 'vi';
		$Dir_Hotword_Path = $VietBot_Offline_Path.'src/hotword/'.$Lang;
	}elseif ($SELECT_CHANGE_HOTWORD_LANG === 'eng'){
		$Lang = 'eng';
		$Dir_Hotword_Path = $VietBot_Offline_Path.'src/hotword/'.$Lang;
	}elseif ($SELECT_CHANGE_HOTWORD_LANG === 'default'){
		$Lang = 'default';
		$Dir_Hotword_Path = $VietBot_Offline_Path.'src/hotword/'.$Lang;
	}
	
    // Kiểm tra nếu giá trị lang là 'vi'
    if ($SELECT_CHANGE_HOTWORD_LANG === 'vi' || $SELECT_CHANGE_HOTWORD_LANG === 'eng') {
        $hotwords = [];
        // Duyệt qua các file trong thư mục
        $files = glob($Dir_Hotword_Path.'/*.ppn');
        // Nếu có file .ppn trong thư mục
        if ($files) {
            foreach ($files as $file) {
                // Sử dụng explode để tách tên file từ đường dẫn
                $fileNameArray = explode('/', $file);
                $fileNameWithExtension = end($fileNameArray);

                // Thêm thông tin vào mảng hotword
                $hotwords[] = [
                    "type" => "porcupine",
                    "custom_skill" => false,
                    "file_name" => $fileNameWithExtension,
                    "lang" => $Lang,
                    "sensitive" => 0.5,
                    "say_reply" => false,
                    "command" => null,
                    "active" => true,
                    "value" => null
                ];
            }
            // Lưu dữ liệu vào cấu hình
            $Config['smart_wakeup']['hotword'] = $hotwords;
		}
    }elseif ($SELECT_CHANGE_HOTWORD_LANG === 'default'){
		$directory = $VietBot_Offline_Path.'src/hotword/default';
		if (!is_dir($directory)) {
			mkdir($directory, 0777, true);
			chmod($directory, 0777);
		}
		
// Đường dẫn nguồn và đích trên máy chủ từ xa
$sourceDir = '/home/pi/.local/lib/python3.9/site-packages/pvporcupine/resources/keyword_files/raspberry-pi';
$destDir = '/home/pi/vietbot_offline/html/vietbot/vietbot_offline/src/hotword/default';


// Kiểm tra xem thư mục đích có tồn tại trên máy chủ từ xa không, nếu không thì tạo nó
$checkDestDirCmd = "mkdir -p $destDir";  // Lệnh tạo thư mục đích nếu chưa tồn tại
$stream = ssh2_exec($connection, $checkDestDirCmd);
stream_set_blocking($stream, true);  // Chờ khi lệnh được thực thi

// Lấy danh sách các tệp .ppn từ thư mục nguồn
$cmd = "find $sourceDir -type f -name '*.ppn'";  // Lệnh tìm tất cả các tệp .ppn trong thư mục nguồn
$stream = ssh2_exec($connection, $cmd);
stream_set_blocking($stream, true);
$output = stream_get_contents($stream);  // Lấy danh sách các tệp .ppn

// Nếu có tệp .ppn, sao chép chúng vào thư mục đích
if ($output) {
    $files = explode("\n", trim($output));  // Chuyển đổi kết quả thành mảng tệp
    foreach ($files as $file) {
        $fileName = basename($file);  // Lấy tên tệp
        $destFile = $destDir . '/' . $fileName;  // Đường dẫn đích

        // Lệnh sao chép tệp từ nguồn sang đích
        $copyCmd = "cp $file $destFile";
        $stream = ssh2_exec($connection, $copyCmd);
        stream_set_blocking($stream, true);  // Chờ khi lệnh được thực thi

        echo "Đã sao chép tệp: $fileName\n";
    }
} else {
    echo "Không tìm thấy tệp .ppn nào trong thư mục nguồn.\n";
}


#Tiến Hành thêm Hotword vào config
        $hotwords = [];
        // Duyệt qua các file trong thư mục
        $files = glob($Dir_Hotword_Path.'/*.ppn');
        // Nếu có file .ppn trong thư mục
        if ($files) {
            foreach ($files as $file) {
                // Sử dụng explode để tách tên file từ đường dẫn
                $fileNameArray = explode('/', $file);
                $fileNameWithExtension = end($fileNameArray);

                // Thêm thông tin vào mảng hotword
                $hotwords[] = [
                    "type" => "porcupine",
                    "custom_skill" => false,
                    "file_name" => $fileNameWithExtension,
                    "lang" => $Lang,
                    "sensitive" => 0.5,
                    "say_reply" => false,
                    "command" => null,
                    "active" => true,
                    "value" => null
                ];
            }
            // Lưu dữ liệu vào cấu hình
            $Config['smart_wakeup']['hotword'] = $hotwords;
		}


// Đóng kết nối SSH
ssh2_exec($connection, 'exit');




		#exit();
		
	}
	
	
$result_ConfigJson = file_put_contents($Config_filePath, json_encode($Config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
if ($result_ConfigJson !== false) {
    $messages[] = "Cấu hình Hotword đã được lưu thành công!";
} else {
    $messages[] = "Đã xảy ra lỗi khi lưu cấu hình Hotword";
}
}


#Lưu lại các giá trị Config.json
if (isset($_POST['all_config_save'])) {
#Cập nhật cấu hình SSH Server:
$Config['web_interface']['ssh_server']['ssh_port'] = intval($_POST['ssh_port']);
$Config['web_interface']['ssh_server']['ssh_username'] = $_POST['ssh_username'];
$Config['web_interface']['ssh_server']['ssh_password'] = $_POST['ssh_password'];

#Cập Nhật Port API
$Config['smart_config']['web_interface']['port'] = intval($_POST['api_port']);

#Cập Nhật ID Mic, Microphone
$Config['smart_config']['mic']['id'] = intval($_POST['mic_id']);

#Cập Nhật ID Sound out, Âm Thanh Out
$Config['smart_config']['speaker']['system']['amixer_id'] = intval($_POST['amixer_speaker_system_id']);
$Config['smart_config']['speaker']['system']['amixer_id_name'] = $_POST['amixer_speaker_system_id_name'];

#Cập Nhật Âm THanh Khi Khởi ĐỘng
$Config['smart_answer']['sound']['welcome']['mode'] = $_POST['sound_welcome_mode'];
$Config['smart_answer']['sound']['welcome']['text'] = $_POST['welcome_text'];
$Config['smart_answer']['sound']['welcome']['path'] = $_POST['welcome_audio_files'];
$Config['smart_answer']['startup_state_speaking'] = isset($_POST['startup_state_speaking']) ? true : false;

#Cập Nhật ÂM THanh Phản Hồi
$Config['smart_answer']['sound']['default']['start'] = $_POST['default_sound_start'];
$Config['smart_answer']['sound']['default']['finish'] = $_POST['default_sound_finish'];
$Config['smart_answer']['sound']['default']['volume_change'] = $_POST['default_sound_volume_change'];

#Cập Nhật Key Picovoice
$Config['smart_wakeup']['hotword_engine']['key'] = $_POST['hotword_engine_key'];
$Config['smart_wakeup']['hotword_engine']['type'] = $_POST['hotword_type'];

#Cập Nhật Home Asisstant
$Config['smart_skill']['hass']['display_full_state'] = isset($_POST['display_full_state']) ? true : false;
$Config['smart_skill']['hass']['url'] = $_POST['hass_internal_url'];
$Config['smart_skill']['hass']['token'] = $_POST['hass_long_token'];
$Config['smart_skill']['hass']['error_answer'] = $_POST['hass_error_answer'];

#Cập Nhật Open Weather Map
$Config['smart_skill']['weather']['openweathermap_key'] = $_POST['key_open_weather_map'];
$Config['smart_skill']['weather']['error_answer'] = $_POST['open_weather_map_error_answer'];

#Cập Nhật Cache, Bộ Nhớ Đệm
$Config['smart_skill']['cache_compare_result'] = intval($_POST['cache_compare_result']);


#Cập Nhật Logs Hệ Thống
$data_logging_type = $_POST['log_display_style'];
if ($data_logging_type === 'null') {$Config['smart_config']['logging_type'] = null;
} else {$Config['smart_config']['logging_type'] = $data_logging_type;}


#CẬP NHẬT cẤU hình BUTTON NÚT NHẤN
foreach ($_POST['button'] as $buttonName => $buttonData) {
	$Config['smart_config']['button'][$buttonName]['gpio'] = intval($buttonData['gpio']);
	$Config['smart_config']['button'][$buttonName]['pulled_high'] = isset($buttonData['pulled_high']) ? (bool)$buttonData['pulled_high'] : false;
	$Config['smart_config']['button'][$buttonName]['active'] = isset($buttonData['active']) ? (bool)$buttonData['active'] : false;}


#Cập Nhật Giá Trị wakeup_reply
if (!isset($Config['smart_wakeup']['wakeup_reply'])) {
	$Config['smart_wakeup']['wakeup_reply'] = [];
	}
foreach ($_POST['wakeup_reply'] as $index => $value) {
	$Config['smart_wakeup']['wakeup_reply'][$index]['value'] = $value;
	}


#Cập Nhật Giá Trị Câu Phản hồi trước pre_answer
if (!isset($Config['smart_answer']['pre_answer'])) {
	$Config['smart_answer']['pre_answer'] = [];
	}
foreach ($_POST['pre_answer'] as $index => $value) {
	$Config['smart_answer']['pre_answer'][$index]['value'] = $value;
	}

#Cập Nhật thời gian chờ phản hồi tối đa
$Config['smart_answer']['pre_answer_timeout'] = intval($_POST['pre_answer_timeout']);


#Cập Nhật Các Giá Trị Hotword
$hotwords = $_POST['hotword'] ?? [];
// Xử lý từng phần tử trong danh sách hotwords
foreach ($hotwords as $index => $hotword) {
    // Kiểm tra và chuyển đổi giá trị cho các trường boolean
    $Config['smart_wakeup']['hotword'][$index]['custom_skill'] = isset($hotword['custom_skill']) ? true : false;
    $Config['smart_wakeup']['hotword'][$index]['active'] = isset($hotword['active']) ? true : false;
    $Config['smart_wakeup']['hotword'][$index]['say_reply'] = isset($hotword['say_reply']) ? true : false;

    $Config['smart_wakeup']['hotword'][$index]['command'] = !empty($hotword['command']) ? $hotword['command'] : null;
    $Config['smart_wakeup']['hotword'][$index]['value'] = !empty($hotword['value']) ? $hotword['value'] : null;
    $Config['smart_wakeup']['hotword'][$index]['file_name'] = !empty($hotword['file_name']) ? $hotword['file_name'] : null;
    $Config['smart_wakeup']['hotword'][$index]['lang'] = !empty($hotword['lang']) ? $hotword['lang'] : null;
    $Config['smart_wakeup']['hotword'][$index]['type'] = !empty($hotword['type']) ? $hotword['type'] : 'porcupine';

    // Kiểm tra và chuyển đổi các trường khác nếu cần thiết
    $Config['smart_wakeup']['hotword'][$index]['sensitive'] = is_numeric($hotword['sensitive']) ? floatval($hotword['sensitive']) : 0.5;
}





$result_ConfigJson = file_put_contents($Config_filePath, json_encode($Config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
if ($result_ConfigJson !== false) {
    $messages[] = "Cấu hình đã được lưu thành công!";
} else {
    $messages[] = "Đã xảy ra lỗi khi lưu cấu hình";
}
}


?>
<!DOCTYPE html>
<html lang="vi">
<?php
include 'html_head.php';
?>

<body>
<!-- ======= Header ======= -->
<?php
include 'html_header_bar.php'; 
?>
<!-- End Header -->

  <!-- ======= Sidebar ======= -->
<?php
include 'html_sidebar.php';
?>
<!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Cấu Hình Config</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item" onclick="loading('show')"><a href="index.php">Trang Chủ</a></li>
          <li class="breadcrumb-item active">config.json</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
<form class="row g-3 needs-validation" id="hotwordForm" enctype="multipart/form-data" novalidate method="POST" action="">
	    <section class="section">
        <div class="row">
		<div class="col-lg-12">
		
		
		  <div class="card accordion" id="accordion_button_ssh">
		<div class="card-body">
			  <h5 class="card-title accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_button_ssh" aria-expanded="false" aria-controls="collapse_button_ssh">
                 Cấu Hình Kết Nối SSH Server <font color="red"> (Bắt Buộc)</font>:</h5>
				 <div id="collapse_button_ssh" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#collapse_button_ssh">
                <div class="row mb-3">
                  <label for="ssh_port" class="col-sm-3 col-form-label">Cổng kết nối:</label>
                  <div class="col-sm-9">
                      <input required class="form-control border-success" type="number" name="ssh_port" id="ssh_port" placeholder="<?php echo $Config['web_interface']['ssh_server']['ssh_port']; ?>" value="<?php echo $Config['web_interface']['ssh_server']['ssh_port']; ?>">
                 <div class="invalid-feedback">Cần nhập cổng kết nối tới máy chủ SSH</div>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="ssh_username" class="col-sm-3 col-form-label">Tên đăng nhập:</label>
                  <div class="col-sm-9">
                      <input required class="form-control border-success" type="text" name="ssh_username" id="ssh_username" placeholder="<?php echo $Config['web_interface']['ssh_server']['ssh_username']; ?>" value="<?php echo $Config['web_interface']['ssh_server']['ssh_username']; ?>">
                 <div class="invalid-feedback">Cần nhập tên đăng nhập của máy chủ SSH</div>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="ssh_password" class="col-sm-3 col-form-label">Mật khẩu:</label>
                  <div class="col-sm-9">
                      <input required class="form-control border-success" type="text" name="ssh_password" id="ssh_password" placeholder="<?php echo $Config['web_interface']['ssh_server']['ssh_password']; ?>" value="<?php echo $Config['web_interface']['ssh_server']['ssh_password']; ?>">
                 <div class="invalid-feedback">Cần nhập mật khẩu của máy chủ SSH</div>
                  </div>
                </div>
				<center><button disabled type="button" class="btn btn-success rounded-pill" onclick="checkSSHConnection()">Kiểm tra kết nối SSH</button></center>
                </div>
                </div>
                </div>


<div class="card accordion" id="accordion_button_setting_API">
<div class="card-body">
<h5 class="card-title accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_button_setting_API" aria-expanded="false" aria-controls="collapse_button_setting_API">
Cấu Hình API:</h5>
<div id="collapse_button_setting_API" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#collapse_button_setting_API">
                <div class="row mb-3">
                  <label for="api_port" class="col-sm-3 col-form-label">Port API:</label>
                  <div class="col-sm-9">
				   <div class="input-group mb-3">
                    <input required type="number" class="form-control border-success" name="api_port" id="api_port" max="9999" placeholder="<?php echo htmlspecialchars($Config['smart_config']['web_interface']['port']) ?>" value="<?php echo htmlspecialchars($Config['smart_config']['web_interface']['port']) ?>">
					<div class="invalid-feedback">Cần nhập cổng Port dành cho API, Tối Đa 9999!</div>
					<button disabled class="btn btn-success border-success" type="button" title="<?php echo $Protocol.$serverIp.':'.$Config['smart_config']['web_interface']['port']; ?>"><a title="<?php echo $Protocol.$serverIp.':'.$Config['smart_config']['web_interface']['port']; ?>" style="text-decoration: none; color: inherit;" href="<?php echo $Protocol.$serverIp.':'.$$Config['smart_config']['web_interface']['port']; ?>" target="_bank">Kiểm Tra</a></button>
				  </div>
				  </div>
                </div>
</div>
</div>
</div>


<div class="card accordion" id="accordion_button_volume_setting">
<div class="card-body">
<h5 class="card-title accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_button_volume_setting" aria-expanded="false" aria-controls="collapse_button_volume_setting">
Cấu Hình Loa - Audio Out, Mic - Microphone:
</h5>
<div id="collapse_button_volume_setting" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#collapse_button_volume_setting">

<div class="card">
<div class="card-body">
<h5 class="card-title" title="Âm Lượng (Volume)/Audio Out">Cài Đặt Mic, Microphone &nbsp;<i class="bi bi-question-circle-fill" onclick="show_message('Cần nhập ID của Microphone hiện tại đang dùng')"></i> &nbsp;:</h5>

                <div class="row mb-3">
                  <label for="mic_id" class="col-sm-3 col-form-label">ID Mic:</label>
                  <div class="col-sm-9">
				  <div class="input-group mb-3">
                      <input required class="form-control border-success" type="number" name="mic_id" id="mic_id" placeholder="<?php echo $Config['smart_config']['mic']['id']; ?>" value="<?php echo $Config['smart_config']['mic']['id']; ?>">
                 	     
				 <div class="invalid-feedback">Cần nhập ID của Mic, Microphone!</div>
				  <button disabled class="btn btn-success border-success" type="button" onclick="scan_audio_devices('scan_mic')">Tìm Kiếm</button>
  
                  </div>
                </div>
                </div>

</div></div>
<div class="card">
<div class="card-body">
<h5 class="card-title" title="Âm Lượng (Volume)/Audio Out">Âm Lượng (Volume)/Audio Out &nbsp;<i class="bi bi-question-circle-fill" onclick="show_message('Cần nhập ID của thiết bị âm thanh đầu ra trong amixer và Âm Lượng khi khởi chạy Bot lần đầu')"></i> &nbsp;:</h5>
                <div class="row mb-3">
                  <label for="amixer_speaker_system_id" class="col-sm-3 col-form-label" title="Âm lượng khi chạy lần đầu tiên">ID Loa (Audio Out): <i class="bi bi-question-circle-fill" onclick="show_message('Cần nhập đúng ID của thiết bị âm thanh đầu ra trong amixer')"></i> :</label>
                  <div class="col-sm-9">
                      <input required class="form-control border-success" type="number" name="amixer_speaker_system_id" id="amixer_speaker_system_id" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="ID của thiết bị âm thanh đầu ra trong amixer" placeholder="<?php echo $Config['smart_config']['speaker']['system']['amixer_id']; ?>" value="<?php echo $Config['smart_config']['speaker']['system']['amixer_id']; ?>">
					<div class="invalid-feedback">Cần nhập âm lượng khi khởi động!</div>
					</div>
                  </div>
				  <!--
                <div class="row mb-3">
                  <label for="amixer_speaker_system_id_name" class="col-sm-3 col-form-label" title="Tên Định Danh Của Thiết Bị">ID Name (Audio Out): <i class="bi bi-question-circle-fill" onclick="show_message('Tên Định Danh Của Thiết Bị, Có Thể Nhập Gì Cũng Được')"></i> :</label>
                  <div class="col-sm-9">
                      <input class="form-control border-success" name="amixer_speaker_system_id_name" id="amixer_speaker_system_id_name" placeholder="<?php echo $Config['smart_config']['speaker']['system']['amixer_id_name']; ?>" value="<?php echo $Config['smart_config']['speaker']['system']['amixer_id_name']; ?>">
					</div>
                  </div>
				  -->
<!--
                <div class="row mb-3">
                  <label for="bot_volume" class="col-sm-3 col-form-label" title="Âm lượng khi chạy lần đầu tiên">Âm lượng <i class="bi bi-question-circle-fill" onclick="show_message('Đặt mức âm lượng mặc định khi bắt đầu khởi chạy chương trình')"></i> :</label>
                  <div class="col-sm-9">
                      <input disabled class="form-control border-success" step="1" min="0" max="100" type="number" name="bot_volume" id="bot_volume" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Âm lượng khi chạy lần đầu tiên" placeholder="" value="">
					<div class="invalid-feedback">Cần nhập âm lượng khi khởi động!</div>
					</div>
                  </div>
				  -->
</div>
</div>

<div class="card">
<div class="card-body">
<h5 class="card-title" title="Âm Thanh Khi Khởi Động Chương Trình Thành Công">Âm Thanh Khi Khởi Động <i class="bi bi-question-circle-fill" onclick="show_message('Lựa Chọn Âm Thanh Hoặc Thông Báo Khi Khởi Động Chương Trình Thành Công')"></i> &nbsp;:</h5>

<div class="row mb-3">
<label for="sound_welcome_mode" class="col-sm-3 col-form-label">Chọn Chế Độ:</label>
<div class="col-sm-9">
<select name="sound_welcome_mode" id="sound_welcome_mode" class="form-select border-success" aria-label="Default select example">
<option value="text" <?php echo $Config['smart_answer']['sound']['welcome']['mode'] === 'text' ? 'selected' : ''; ?>>text (Dùng Văn Bản)</option>
<option value="path" <?php echo $Config['smart_answer']['sound']['welcome']['mode'] === 'path' ? 'selected' : ''; ?>>path (Dùng Tệp Âm Thanh)</option>
</select>
</div>
</div>

<div class="row mb-3">
<label for="welcome_text" class="col-sm-3 col-form-label">Văn Bản:</label>
<div class="col-sm-9">
<input required class="form-control border-success" type="text" name="welcome_text" id="welcome_text" placeholder="<?php echo $Config['smart_answer']['sound']['welcome']['text']; ?>" value="<?php echo $Config['smart_answer']['sound']['welcome']['text']; ?>">
<div class="invalid-feedback">Cần nhập Văn Bản thông báo khi chương trình khởi động</div>
</div>
</div>

<div class="row mb-3">
<label for="welcome_audio_files" class="col-sm-3 col-form-label">Tệp Âm Thanh:</label>
<div class="col-sm-9">
<div class="input-group mb-3">
<?php
$directory_welcome_sound = $VietBot_Offline_Path.'src/sound/welcome';
$audioFiles_welcome = array_filter(scandir($directory_welcome_sound), function ($file_welcome_sound) use ($directory_welcome_sound) {
    $filePath = $directory_welcome_sound . '/' . $file_welcome_sound;
    return is_file($filePath) && preg_match('/\.(mp3|wav|ogg)$/i', $file_welcome_sound);
});
echo '<select class="form-select border-success" name="welcome_audio_files" id="welcome_audio_files">';
if (empty($audioFiles_welcome)) {
    echo '<option value="">Không có tệp âm thanh nào</option>';
} else {
    foreach ($audioFiles_welcome as $file_welcome_sound) {
        echo '<option value="sound/welcome/' . htmlspecialchars($file_welcome_sound) . '" ' 
    . (htmlspecialchars($Config['smart_answer']['sound']['welcome']['path']) === 'sound/welcome/' . htmlspecialchars($file_welcome_sound) ? 'selected' : '') 
    . '>' . htmlspecialchars($file_welcome_sound) . '</option>';
 }
}
echo '</select>';
?>

</div>
</div>
</div>

<div class="row mb-3">
<label class="col-sm-3 col-form-label">Đọc Thông Tin Khi Khởi Động <i class="bi bi-question-circle-fill" onclick="show_message('Đọc Thông Tin Khi Chương Trình ĐƯợc Khởi Động Thành Công Như: địa chỉ ip, v..v...')"></i> :</label>
<div class="col-sm-9">
<div class="form-switch">
<input class="form-check-input" type="checkbox" name="startup_state_speaking" id="startup_state_speaking" <?php echo $Config['smart_answer']['startup_state_speaking'] ? 'checked' : ''; ?>>
</div>
</div>
</div>

</div></div>


<div class="card">
<div class="card-body">
<h5 class="card-title" title="Âm Thanh Khi Được Đánh Thức">Âm Thanh Phản Hồi <i class="bi bi-question-circle-fill" onclick="show_message('Lựa Chọn Âm Thanh Phản Hồi Khi Được Đánh Thức Thành Công')"></i> &nbsp;:</h5>
<div class="row mb-3">
<label for="default_sound_start" class="col-sm-3 col-form-label">Khi Được Đánh Thức:</label>
<div class="col-sm-9">
<div class="input-group mb-3">
<?php
$directory_default_sound = $VietBot_Offline_Path.'src/sound/default';
$audioFiles_default_sound = array_filter(scandir($directory_default_sound), function ($default_sound_start) use ($directory_default_sound) {
    $filePath_default_sound_start = $directory_default_sound . '/' . $default_sound_start;
    return is_file($filePath_default_sound_start) && preg_match('/\.(mp3|wav|ogg)$/i', $default_sound_start);
});
echo '<select class="form-select border-success" name="default_sound_start" id="default_sound_start">';
if (empty($audioFiles_default_sound)) {
    echo '<option value="">Không có tệp âm thanh nào</option>';
} else {
    foreach ($audioFiles_default_sound as $default_sound_start) {
        echo '<option value="sound/default/' . htmlspecialchars($default_sound_start) . '" ' 
    . (htmlspecialchars($Config['smart_answer']['sound']['default']['start']) === 'sound/default/' . htmlspecialchars($default_sound_start) ? 'selected' : '') 
    . '>' . htmlspecialchars($default_sound_start) . '</option>';
 }
}
echo '</select>';
?>

</div>
</div>
</div>

<div class="row mb-3">
<label for="default_sound_finish" class="col-sm-3 col-form-label">Khi Kết Thúc:</label>
<div class="col-sm-9">
<div class="input-group mb-3">
<?php
echo '<select class="form-select border-success" name="default_sound_finish" id="default_sound_finish">';
if (empty($audioFiles_default_sound)) {
    echo '<option value="">Không có tệp âm thanh nào</option>';
} else {
    foreach ($audioFiles_default_sound as $default_sound_finish) {
        echo '<option value="sound/default/' . htmlspecialchars($default_sound_finish) . '" ' 
    . (htmlspecialchars($Config['smart_answer']['sound']['default']['finish']) === 'sound/default/' . htmlspecialchars($default_sound_finish) ? 'selected' : '') 
    . '>' . htmlspecialchars($default_sound_finish) . '</option>';
 }
}
echo '</select>';
?>

</div>
</div>
</div>

<div class="row mb-3">
<label for="default_sound_volume_change" class="col-sm-3 col-form-label" title="Âm thanh báo khi âm lượng được thay đổi">Volume Change:</label>
<div class="col-sm-9">
<div class="input-group mb-3">
<?php
echo '<select class="form-select border-success" name="default_sound_volume_change" id="default_sound_volume_change">';
if (empty($audioFiles_default_sound)) {
    echo '<option value="">Không có tệp âm thanh nào</option>';
} else {
    foreach ($audioFiles_default_sound as $default_sound_volume_change) {
        echo '<option value="sound/default/' . htmlspecialchars($default_sound_volume_change) . '" ' 
    . (htmlspecialchars($Config['smart_answer']['sound']['default']['volume_change']) === 'sound/default/' . htmlspecialchars($default_sound_volume_change) ? 'selected' : '') 
    . '>' . htmlspecialchars($default_sound_volume_change) . '</option>';
 }
}
echo '</select>';
?>

</div>
</div>
</div>
</div></div>





</div>
</div>
</div>


<div class="card accordion" id="accordion_button_hotword_engine">
<div class="card-body">
<h5 class="card-title accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_button_hotword_engine" aria-expanded="false" aria-controls="collapse_button_hotword_engine">
Cấu Hình Hotword Engine/Picovoice :</h5>
           
<div id="collapse_button_hotword_engine" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordion_button_hotword_engine" style="">

<div class="card">
<div class="card-body">
<h5 class="card-title" title="Key Picovoice">Picovoice <i class="bi bi-question-circle-fill" onclick="show_message('Đăng ký, lấy key: <a href=\'https://console.picovoice.ai\' target=\'_bank\'>https://console.picovoice.ai</a>')"></i> :</h5>
         <div class="row mb-3">
                  <label for="hotword_engine_key" class="col-sm-3 col-form-label">Token Key:</label>
                  <div class="col-sm-9">
				  <div class="input-group mb-3">
                      <input required class="form-control border-success" type="text" name="hotword_engine_key" id="hotword_engine_key" placeholder="<?php echo $Config['smart_wakeup']['hotword_engine']['key']; ?>" value="<?php echo $Config['smart_wakeup']['hotword_engine']['key']; ?>">
                 <div class="invalid-feedback">Cần nhập key Picovoice để gọi Hotword!</div>
				  <button disabled class="btn btn-success border-success" type="button" onclick="test_key_Picovoice()">Kiểm Tra</button>
                  </div>
                  </div>
                </div>
				
                <div class="row mb-3">
                  <label for="hotword_type" class="col-sm-3 col-form-label">Kiểu Loại:</label>
                  <div class="col-sm-9">
                      <input readonly class="form-control border-danger" type="text" name="hotword_type" id="hotword_type" placeholder="<?php echo $Config['smart_wakeup']['hotword_engine']['type']; ?>" value="<?php echo $Config['smart_wakeup']['hotword_engine']['type']; ?>">
                  </div>
                </div>
				
            </div>
          </div>



<div class="card">
<div class="card-body">
<h5 class="card-title">Hotword <i class="bi bi-question-circle-fill" onclick="show_message('Danh sách file thư viện Porcupine: <a href=\'https://github.com/Picovoice/porcupine/tree/master/lib/common\' target=\'_bank\'>Github</a><br/>Mẫu các từ khóa đánh thức: <a href=\'https://github.com/Picovoice/porcupine/tree/master/resources\' target=\'_bank\'>Github</a>')"></i> :</h5>
<!--
     
     <div class="form-floating mb-3">			
<select name="select_hotword_lang" id="select_hotword_lang" class="form-select border-success" aria-label="Default select example">
<option value="vi" <?php echo $Config['smart_config']['smart_wakeup']['hotword']['lang'] === 'vi' ? 'selected' : ''; ?>>Tiếng việt</option>
<option value="eng" <?php echo $Config['smart_config']['smart_wakeup']['hotword']['lang'] === 'eng' ? 'selected' : ''; ?>>Tiếng anh</option>
</select>
<label for="select_hotword_lang">Chọn ngôn ngữ để gọi, đánh thức Bot:</label>
</div>
			
<table class="table table-bordered border-primary">
                <thead>
                  <tr>
                    <th scope="col" colspan="5">  <h5 class="card-title"><center><font color=red>Cài đặt nâng cao Hotword:</font></center></h5></th>
                  </tr>
				   <tr>
                    <th scope="col" colspan="8"><center>
					<button type="button" class="btn btn-primary rounded-pill" onclick="loadConfigHotword('vi')" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Cài đặt Hotword Tiếng Việt">Tiếng Việt</button>
					<button type="button" class="btn btn-primary rounded-pill" onclick="loadConfigHotword('eng')" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Cài đặt Hotword Tiếng Anh">Tiếng Anh</button>
					<button type="button" class="btn btn-warning rounded-pill" onclick="reload_hotword_config()" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Tự động tìm scan các file Hotword có trong thư mục eng và vi để cấu hình trong Config.json">Scan Và Ghi Mới</button>
				
					</center>
					<span id="language_hotwordd" value=""></span>
					</tr>
					</thead> 
                
                  <thead id="results_body_hotword1">
                  </thead>
				   
                <tbody id="results_body_hotword">
               

                </tbody>
              </table>
-->





<?php
// Kiểm tra nếu key 'hotword' tồn tại
if (!isset($Config['smart_wakeup']['hotword'])) {
    echo  'Dữ liệu config.json không chứa cấu hình "hotword", Hãy kiểm tra lại';
}
else {
$hotwords = $Config['smart_wakeup']['hotword'];
$hasEng_mess = false;
$hasVi_mess = false;
$hasDefault_mess = false;
$HOT_WORD_LANG = null;
// Duyệt qua danh sách hotword
foreach ($hotwords as $hotword_check_lang) {
    if ($hotword_check_lang['lang'] === 'eng') {
        $hasEng_mess = true;
		$HOT_WORD_LANG = 'eng';
    } elseif ($hotword_check_lang['lang'] === 'vi') {
        $hasVi_mess = true;
		$HOT_WORD_LANG = 'vi';
    } elseif ($hotword_check_lang['lang'] === 'default') {
        $hasDefault_mess = true;
		$HOT_WORD_LANG = 'default';
    }
}
// Kiểm tra trạng thái và đưa ra thông báo
if ($hasEng_mess && $hasVi_mess) {
    $Hotword_lang_Mess = "<p class='text-danger'>Lỗi cấu hình Ngôn ngữ Hotword, bạn đang cấu hình sử dụng lẫn lộn các ngôn ngữ Hotword</p>";
} elseif ($hasEng_mess) {
    $Hotword_lang_Mess = "<p class='text-success'>Bạn đang sử dụng ngôn ngữ: Tiếng Anh.</p>";
} elseif ($hasVi_mess) {
    $Hotword_lang_Mess = "<p class='text-success'>Bạn đang sử dụng ngôn ngữ: Tiếng Việt.</p>";
}elseif ($hasDefault_mess) {
    $Hotword_lang_Mess = "<p class='text-success'>Bạn đang sử dụng ngôn ngữ: Mặc Định (Tiếng Anh)</p>";
}else {
    $Hotword_lang_Mess = "<p class='text-danger'>Không xác định được ngôn ngữ Hotword nào đang được sử dụng.</p>";
}
?>
<!--
<div class="input-group">
<div class="form-floating mb-3">
<select name="select_change_hotword_language" id="select_hotword_lang" class="form-select border-success" aria-label="Default select example">
<option value="vi" <?php echo $HOT_WORD_LANG === 'vi' ? 'selected' : ''; ?>>Tiếng Việt</option>
<option value="eng" <?php echo $HOT_WORD_LANG === 'eng' ? 'selected' : ''; ?>>Tiếng Anh</option>
<option value="default" <?php echo $HOT_WORD_LANG === 'default' ? 'selected' : ''; ?>>Mặc Định (Tiếng Anh)</option>
</select>
<label class="text-primary" for="select_hotword_lang">Thay Đổi Ngôn Ngữ Gọi Hotword:</label>
</div>
<button class="btn btn-success border-success" type="submit" name="change_hotword_language">Thay Đổi</button>
</div>
-->

<table class="table table-bordered border-primary">
           <thead>
<tr><th scope="col" colspan="6">  <h5 class="card-title"><center><?php echo $Hotword_lang_Mess; ?></center></h5></th></tr>
                <tr>
                    <!-- <th style="text-align: center; vertical-align: middle;">Type</th> -->
					<th style="text-align: center; vertical-align: middle;">Tên Hotword</th>
                   <!-- <th style="text-align: center; vertical-align: middle;">Language</th> -->
                    <th style="text-align: center; vertical-align: middle;">Độ Nhạy</th>
                    <th style="text-align: center; vertical-align: middle;">Command</th>
					<th style="text-align: center; vertical-align: middle;">Câu Phản Hồi</th>
					<th style="text-align: center; vertical-align: middle;">Custom Skill</th>
                    <th style="text-align: center; vertical-align: middle;">Kích Hoạt</th>
                    <!--<th>Value</th> -->
                </tr>
            </thead>

            <tbody>
                <?php foreach ($hotwords as $index => $hotword): ?>
                    <tr>
                        <td hidden><input type="text" name="hotword[<?= $index ?>][type]" value="<?= htmlspecialchars($hotword['type']) ?>"></td>
<td>					<input readonly type="text" class="form-control border-danger" name="hotword[<?= $index ?>][file_name]" value="<?= htmlspecialchars($hotword['file_name']) ?>"></td>
                       <td hidden><input type="text" class="form-control border-success" name="hotword[<?= $index ?>][lang]" value="<?= htmlspecialchars($hotword['lang']) ?>"></td> 
                        <td><input type="number" class="form-control border-success" title="Độ nhạy từ 0.1 -> 1" min="0.1" max="1" step="0.1" name="hotword[<?= $index ?>][sensitive]" value="<?= htmlspecialchars($hotword['sensitive']) ?>"></td>
                        <td><input type="text" class="form-control border-success" name="hotword[<?= $index ?>][command]" value="<?= $hotword['command'] !== null ? htmlspecialchars($hotword['command']) : '' ?>"></td>
						<td style="text-align: center; vertical-align: middle;"><div class="form-switch"><input type="checkbox" class="form-check-input" name="hotword[<?= $index ?>][say_reply]" value="1" <?= $hotword['say_reply'] ? 'checked' : '' ?>></div></td>
						<td style="text-align: center; vertical-align: middle;"><div class="form-switch"><input class="form-check-input" type="checkbox" name="hotword[<?= $index ?>][custom_skill]" value="1" <?= $hotword['custom_skill'] ? 'checked' : '' ?>></div></td>
                        <td style="text-align: center; vertical-align: middle;"><div class="form-switch"><input type="checkbox" class="form-check-input" name="hotword[<?= $index ?>][active]" value="1" <?= $hotword['active'] ? 'checked' : '' ?>></div></td>
                        <td hidden><input type="text" name="hotword[<?= $index ?>][value]" value="<?= $hotword['value'] !== null ? htmlspecialchars($hotword['value']) : '' ?>"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
  </table>
  <?php
}
?>
            </div>
          </div>

							
</div>
</div>
</div>


<div class="card accordion" id="accordion_button_setting_homeassistant">
<div class="card-body">
<h5 class="card-title accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_button_setting_homeassistant" aria-expanded="false" aria-controls="collapse_button_setting_homeassistant">
Cấu Hình Home Assistant:</h5>
<div id="collapse_button_setting_homeassistant" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#collapse_button_setting_homeassistant">

                <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Display Full State <i class="bi bi-question-circle-fill" onclick="show_message('Display Full State: Câu trả lời có thêm chi tiết về thiết bị (nếu được kích hoạt)')"></i> :</label>
                  <div class="col-sm-9">
					<div class="form-switch">
                      <input class="form-check-input" type="checkbox" name="display_full_state" id="display_full_state" <?php echo $Config['smart_skill']['hass']['display_full_state'] ? 'checked' : ''; ?>>
                      
                    </div>
                  </div>
                </div>

			        <div class="row mb-3">
                  <label for="hass_long_token" class="col-sm-3 col-form-label" title="Mã token của nhà thông minh Home Assistant">Mã Token:</label>
                 <div class="col-sm-9">
                      <input required class="form-control border-success" type="text" name="hass_long_token" id="hass_long_token" title="Mã token của nhà thông minh Home Assistant" placeholder="<?php echo htmlspecialchars($Config['smart_skill']['hass']['token']) ?>" value="<?php echo htmlspecialchars($Config['smart_skill']['hass']['token']) ?>">
						<div class="invalid-feedback">Cần nhập mã Token của nhà thông minh!</div>
					</div>
                </div>
				
                <div class="row mb-3">
                  <label for="hass_internal_url" class="col-sm-3 col-form-label" title="Địa chỉ url nội bộ">URL Home Assistant:</label>
                 <div class="col-sm-9">
				 <div class="input-group mb-3">
                      <input required class="form-control border-success" type="text" name="hass_internal_url" id="hass_internal_url" placeholder="<?php echo htmlspecialchars($Config['smart_skill']['hass']['url']) ?>" title="Địa chỉ url nội bộ" value="<?php echo htmlspecialchars($Config['smart_skill']['hass']['url']) ?>">
						<div class="invalid-feedback">Cần nhập URL Home Assistant!</div>
						<button disabled class="btn btn-success border-success" type="button" onclick="CheckConnectionHomeAssistant('hass_internal_url')">Kiểm Tra</button>
					</div>
					</div>
                </div>
				
			        <div class="row mb-3">
                  <label for="hass_error_answer" class="col-sm-3 col-form-label" title="Thông Báo Lỗi Home Assistant">Thông Báo Lỗi:</label>
                 <div class="col-sm-9">
                      <input required class="form-control border-success" type="text" name="hass_error_answer" id="hass_error_answer" title="Thông Báo Lỗi Home Assistant" placeholder="<?php echo htmlspecialchars($Config['smart_skill']['hass']['error_answer']) ?>" value="<?php echo htmlspecialchars($Config['smart_skill']['hass']['error_answer']) ?>">
						<div class="invalid-feedback">Cần Nhập Thông Báo Khi Lỗi Home Assistant</div>
					</div>
                </div>
</div>
</div>
</div>


<div class="card accordion" id="accordion_button_OpenWeatherMap">
<div class="card-body">
<h5 class="card-title accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_button_OpenWeatherMap" aria-expanded="false" aria-controls="collapse_button_OpenWeatherMap">
Open Weather Map:</h5>
<div id="collapse_button_OpenWeatherMap" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#collapse_button_OpenWeatherMap">
			        <div class="row mb-3">
                  <label for="key_open_weather_map" class="col-sm-3 col-form-label" title="Mã token của nhà thông minh Home Assistant">KEY/Token: <i class="bi bi-question-circle-fill" onclick="show_message(' - Trang Chủ: https://openweathermap.org/')"></i></label>
                 <div class="col-sm-9">
				 <div class="input-group mb-3">
                      <input required class="form-control border-success" type="text" name="key_open_weather_map" id="key_open_weather_map" title="Mã token của Open Weather Map" placeholder="<?php echo htmlspecialchars($Config['smart_skill']['weather']['openweathermap_key']) ?>" value="<?php echo htmlspecialchars($Config['smart_skill']['weather']['openweathermap_key']) ?>">
						<div class="invalid-feedback">Cần nhập KEY/Token của Open Weather Map!</div>
						<button disabled class="btn btn-success border-success" type="button" onclick="CheckConnectionHomeAssistant('hass_internal_url')">Kiểm Tra</button>
					</div>
					</div>
                </div>

			        <div class="row mb-3">
                  <label for="open_weather_map_error_answer" class="col-sm-3 col-form-label" title="Cần nhập thông báo nếu lỗi Open Weather Map">Thông Báo Lỗi:</label>
                 <div class="col-sm-9">
                      <input required class="form-control border-success" type="text" name="open_weather_map_error_answer" id="open_weather_map_error_answer" title="Thông báo nếu lỗi Open Weather Map" placeholder="<?php echo htmlspecialchars($Config['smart_skill']['weather']['error_answer']) ?>" value="<?php echo htmlspecialchars($Config['smart_skill']['weather']['error_answer']) ?>">
						<div class="invalid-feedback">Cần nhập thông báo nếu lỗi Open Weather Map</div>
					</div>
                </div>
</div>
</div>
</div>


                <div class="card accordion" id="accordion_button_setting_bton">
               <div class="card-body">
<h5 class="card-title accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_button_setting_bton" aria-expanded="false" aria-controls="collapse_button_setting_bton">
                 Cấu Hình Nút Nhấn:</h5>
                  <div id="collapse_button_setting_bton" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordion_button_setting_bton" style="">

<table class="table table-bordered border-primary">
                <thead>
                  <tr>
                    <th scope="col"><center><font color=blue>Nút Nhấn</font></center></th>
                    <th scope="col"><center><font color=blue>GPIO</font></center></th>
					<th scope="col"><center><font color=blue>Kéo mức thấp</font></center></th>
                    <th scope="col"><center><font color=blue>Kích hoạt</font></center></th>

                  </tr>
                </thead>
                <tbody>
<?php
    foreach ($Config['smart_config']['button'] as $buttonName => $buttonData) {
		echo '<tr>';
        echo '<th scope="row" style="text-align: center; vertical-align: middle;"><center>' . $buttonName . ':</center></th>';
        echo '<td style="text-align: center; vertical-align: middle;"><!-- GPIO --><input required type="number" style="width: 90px;" class="form-control border-success" min="1" step="1" max="30" name="button[' . $buttonName . '][gpio]" value="' . $buttonData['gpio'] . '" placeholder="' . $buttonData['gpio'] . '"></center><div class="invalid-feedback">Cần nhập đúng Chân GPIO cho nút nhấn, tối đa 30</div></td>';
		echo '<td style="text-align: center; vertical-align: middle;"><!-- Pulled High --><div class="form-switch"><input type="checkbox" class="form-check-input" name="button[' . $buttonName . '][pulled_high]"' . ($buttonData['pulled_high'] ? ' checked' : '') . '></div></td>';
		echo '<td style="text-align: center; vertical-align: middle;"><!-- Active nhấn nhả --> <div class="form-switch"><center><input type="checkbox" class="form-check-input" name="button[' . $buttonName . '][active]"' . ($buttonData['active'] ? ' checked' : '') . '></div></td>';
		echo '</tr>';
	}
?>
</tbody></table>
</div>
</div>
</div>


<div class="card accordion" id="accordion_button_Assistant_Select">
<div class="card-body">
<h5 class="card-title accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_button_Assistant_Select" aria-expanded="false" aria-controls="collapse_button_Assistant_Select">
Thết Lập Các Giá Trị Phản Hồi</h5>
<div id="collapse_button_Assistant_Select" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#collapse_button_Assistant_Select">

<div class="row mb-3">
<label for="pre_answer_timeout" class="col-sm-3 col-form-label" title="Thời Gian Chờ Phản Hồi Tối Đa (giây)">Thời Gian Chờ (giây): <i class="bi bi-question-circle-fill" onclick="show_message('Thời Gian Chờ Phản Hồi Tối Đa')"></i> :</label>
<div class="col-sm-9">
<input required class="form-control border-success" type="number" min="3" max="20" step="1" name="pre_answer_timeout" id="pre_answer_timeout" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Thời Gian Chờ Phản Hồi Tối Đa (giây)" placeholder="<?php echo $Config['smart_answer']['pre_answer_timeout']; ?>" value="<?php echo $Config['smart_answer']['pre_answer_timeout']; ?>">
<div class="invalid-feedback">Cần nhập thời gian chờ phản hồi tối đa (giây)!</div>
</div>
</div>

<div class="row mb-3">
<label for="pre_answer_timeout" class="col-sm-3 col-form-label" title="Câu Phản Hồi Trước">Câu Phản Hồi Trước <i class="bi bi-question-circle-fill" onclick="show_message('Câu Phản Hồi Trước')"></i> :</label>
<div class="col-sm-9">
<?php
if (!empty($Config['smart_answer']['pre_answer'])) {
    foreach ($Config['smart_answer']['pre_answer'] as $index => $reply) {
        $value = htmlspecialchars($reply['value']);

echo '
<div class="row mb-3">
<label for="pre_answer_' . $index . '" class="col-sm-3 col-form-label" title="Câu phản hồi trước">Phản hồi ' . ($index + 1) . '</label>
<div class="col-sm-9">
<input class="form-control border-success" type="text" name="pre_answer[' . $index . ']" id="pre_answer_' . $index . '" placeholder="' . $value . '" value="' . $value . '">
</div>
</div>
';
		
    }
} else {
    echo 'Không có dữ liệu để hiển thị.';
}
?>
</div>
</div>

</div>
</div>
</div>





<div class="card accordion" id="accordion_button_wakeup_reply">
<div class="card-body">
<h5 class="card-title accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_button_wakeup_reply" aria-expanded="false" aria-controls="collapse_button_wakeup_reply">
Câu Phản Hồi Khi Được Đánh Thức:</h5>
<div id="collapse_button_wakeup_reply" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#collapse_button_wakeup_reply">
<?php
// Kiểm tra và hiển thị các giá trị trong thẻ <input>
if (!empty($Config['smart_wakeup']['wakeup_reply'])) {
    foreach ($Config['smart_wakeup']['wakeup_reply'] as $index => $reply) {
		 $value = htmlspecialchars($reply['value']);
echo '
<div class="row mb-3">
<label for="open_weather_map_error_answer" class="col-sm-3 col-form-label" title="Câu phản hồi khi được đánh thức">Câu phản hồi ' . ($index + 1) . ':</label>
<div class="col-sm-9">
<input class="form-control border-success" type="text" id="wakeup_reply_' . $index . '" name="wakeup_reply[' . $index . ']" value="' . $value . '">
<div class="invalid-feedback">Cần nhập đầy đủ dữ liệu câu phản hồi khi được đánh thức</div>
</div>
</div>
';	
    }
} else {
    echo 'Không có dữ liệu để hiển thị cho: Câu Phản Hồi khi Được Đánh Thức';
}
?>

</div>
</div>
</div>










<div class="card accordion" id="accordion_button_cache_vietbot">
<div class="card-body">
<h5 class="card-title accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_button_cache_vietbot" aria-expanded="false" aria-controls="collapse_button_cache_vietbot">
Cache, Bộ Nhớ Đệm:</h5>
<div id="collapse_button_cache_vietbot" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#collapse_button_cache_vietbot">
                <div class="row mb-3">
                  <label for="cache_compare_result" class="col-sm-3 col-form-label" title="Mức kết quả so sánh bộ đệm">Mức so sánh bộ đệm <i class="bi bi-question-circle-fill" onclick="show_message('Đặt mức kết quả so sánh bộ đệm từ 0 -> 100')"></i> :</label>
                  <div class="col-sm-9">
                      <input required class="form-control border-success" step="1" min="0" max="100" type="number" name="cache_compare_result" id="cache_compare_result" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Mức kết quả so sánh bộ đệm" placeholder="<?php echo htmlspecialchars($Config['smart_skill']['cache_compare_result']) ?>" value="<?php echo htmlspecialchars($Config['smart_skill']['cache_compare_result']) ?>">
					<div class="invalid-feedback">Cần nhập ức kết quả so sánh bộ đệm!</div>
					</div>
                  </div>
</div>
</div>
</div>


<div class="card accordion" id="accordion_button_logs_vietbot">
<div class="card-body">
<h5 class="card-title accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_button_logs_vietbot" aria-expanded="false" aria-controls="collapse_button_logs_vietbot">
Logs Hệ Thống:</h5>
<div id="collapse_button_logs_vietbot" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#collapse_button_logs_vietbot">
				<div class="row mb-3">
                  <label for="log_display_style" class="col-sm-3 col-form-label">Kiểu hiển thị Logs:</label>
                  <div class="col-sm-9">
                   <select name="log_display_style" id="log_display_style" class="form-select border-success" aria-label="Default select example">
				   <option value="null" <?php echo $Config['smart_config']['logging_type'] === null ? 'selected' : ''; ?>>null (Không hiển thị)</option>
                      <option value="console" <?php echo $Config['smart_config']['logging_type'] === 'console' ? 'selected' : ''; ?>>console (Hiển thị log ra bảng điều khiển đầu cuối)</option>
                      <option value="web" <?php echo $Config['smart_config']['logging_type'] === 'web' ? 'selected' : ''; ?>>web (Hiển thị log ra API, Web UI)</option>
					   <option value="both" <?php echo $Config['smart_config']['logging_type'] === 'both' ? 'selected' : ''; ?>>both (Hiển thị log ra tất cả các đường)</option>
					</select>
                  </div>
                </div>
</div>
</div>
</div>








<center>

<button type="submit" name="all_config_save" class="btn btn-primary rounded-pill">Lưu Cài Đặt Config</button>

</center>		
		
		</div>
		</div>
		</section>
</form>
</main>


  <!-- ======= Footer ======= -->
<?php
include 'html_footer.php';
?>
<!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Nghe thử file âm thanh 
<audio id="audioPlayer" style="display: none;" controls></audio>-->

  <!-- Template Main JS File -->
<?php
include 'html_js.php';
?>

</body>
</html>
