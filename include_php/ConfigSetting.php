<?php
//Code By: Vũ Tuyển
//Facebook: https://www.facebook.com/TWFyaW9uMDAx
include "../Configuration.php";
	$FileConfigJson = "$DuognDanThuMucJson"."config.json";
	$FileVolumeJson = "$DuognDanThuMucJson"."volume_state.json";
	$json_volume_data = file_get_contents($FileVolumeJson);
    $json_config_data = file_get_contents($FileConfigJson);
	$data_volume = json_decode($json_volume_data);
	$data_config = json_decode($json_config_data, true);
	$ttsCompany = '';
	$ttsVoice = '';
//Khôi Phục File Config
// Đường dẫn đến thư mục "Backup_Config"
$backupDirz = "Backup_Config/";
$fileLists = glob($backupDirz . "*.json");
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['selectedFile']) && !empty($_GET['selectedFile'])) {
            $selectedFile = $_GET['selectedFile'];
            $configFile = $backupDirz . "../config.json";
            $fileContent = file_get_contents($selectedFile);
            file_put_contents($configFile, $fileContent);
			header("Location: ".$PHP_SELF);
            exit();
            //echo "Đã Khôi Phục File config.json Được Chọn Thành Công.";
        }}
	//END Khôi Phục File Config	
	//hotwork
	$hotwords = $data_config['smart_wakeup']['hotword'];
	$hotwords_get_langgg = $data_config['smart_wakeup']['hotword'][0]['lang'];
	
	if ($hotwords_get_langgg === 'eng') {
    $hotwords_get_lang = 'Tiếng Anh';
} elseif ($hotwords_get_langgg === 'vi') {
    $hotwords_get_lang = 'Tiếng Việt';
}

	
	//Lấy giá trị value trong file json
	$value_volume = $data_volume->volume;
	//lấy giá các trị wakeup_reply
	$GET_wakeupReply = $data_config['smart_wakeup']['wakeup_reply'];
	//lấy giá các trị value của STT
	$GET_STT = $data_config['smart_request']['stt']['type'];
	$GET_STT_GG_ASS_MODE = $data_config['smart_request']['stt']['stt_gg_ass_mode'];
	$GET_STT_Replace = $data_config['smart_request']['stt']['type'];
	$keywordsSTT = array(
    "stt_gg_free" => "Google Free",
    "stt_gg_cloud" => "Google Cloud",
    "stt_gg_ass" => "Google Assistant",
    "stt_fpt" => "FPT",
    "stt_viettel" => "Viettel"
);
// Thực hiện thay thế từng từ khóa
foreach ($keywordsSTT as $keywordSTT => $replacementSTT) {
    if (strpos($GET_STT_Replace, $keywordSTT) !== false) {
        $GET_STT_Replacee = str_replace($keywordSTT, $replacementSTT, $GET_STT_Replace);
    }}
	// In ra chuỗi đã được thay thế
	$GET_TimeOut_STT = $data_config['smart_request']['stt']['time_out'];
	$GET_Token_STTzz = $data_config['smart_request']['stt']['token'];
	
	if ($GET_Token_STTzz === null) {
  $GET_Token_STT = "Null";
} else {
  $GET_Token_STT = $GET_Token_STTzz;
}
	
    // Kiểm tra xem tệp google_stt.json có tồn tại hay không
	 $jsonFile = "$DuognDanThuMucJson/google_stt.json";
    if (file_exists($jsonFile)) {
		$jsonDataGcloudTTS = file_get_contents($jsonFile);
    } else {$jsonDataGcloudTTS = '';
	//echo "<h3><center color='red'>Lỗi! File: <b>/home/pi/vietbot_offline/src/google.json</b> Không Tồn Tại</center></h3><hr/>";
    }
	 $jsonFileGcloud = "$DuognDanThuMucJson/google_tts.json";
    if (file_exists($jsonFileGcloud)) {
		$jsonDataGcloudSTT = file_get_contents($jsonFileGcloud);
    } else {$jsonDataGcloudSTT = '';
	//echo "<h3><center color='red'>Lỗi! File: <b>/home/pi/vietbot_offline/src/google.json</b> Không Tồn Tại</center></h3><hr/>";
    }
	///
	//Lấy Giá Trị TTS
	$GET_TTS_Type = $data_config['smart_answer']['tts']['type'];
	$GET_TTS_Type_Replace = $data_config['smart_answer']['tts']['type'];
	// Mảng chứa từ khóa và giá trị thay thế tương ứng
	$keywordsTTS = array(
    "tts_gg_free" => "Google Free",
    "tts_gg_cloud" => "Google Cloud",
    "tts_fpt" => "FPT",
    "tts_viettel" => "Viettel",
    "tts_zalo" => "Zalo"
);
// Thực hiện thay thế từng từ khóa
foreach ($keywordsTTS as $keywordTTS => $replacementTTS) {
    if (strpos($GET_TTS_Type_Replace, $keywordTTS) !== false) {
        $GET_TTS_Type_Replacee = str_replace($keywordTTS, $replacementTTS, $GET_TTS_Type_Replace);
    }}
	// In ra chuỗi đã được thay thế
	$GET_TTS_Voice_Name = $data_config['smart_answer']['tts']['voice_name'];
	$GET_TTS_Token_Key = $data_config['smart_answer']['tts']['token'];
	//echo $GET_TTS_Token_Key;
	$GET_Speaker_Amixer_ID = $data_config['smart_config']['speaker']['amixer_id'];
	$GET_Port_Web_Interface = $data_config['smart_config']['web_interface']['port'];
	//$GET_HostName_Web_Interface = $data_config['smart_config']['web_interface']['hostname'];
	//my_user
	$MY_USER_NAME = $data_config['smart_config']['user_info']['name'];
	//console_ouput
	//$Get_Console_Ouput = $data_config['smart_config']['console_ouput'];
	
	
		if ($data_config['smart_config']['console_ouput'] === null) {
  $Get_Console_Ouput = "Null";
} else {
  $Get_Console_Ouput = $data_config['smart_config']['console_ouput'];
}
	
	//location
//	$Location_Longitude = $data_config['smart_config']['user_info']['lon'];
//	$Location_Latitude = $data_config['smart_config']['user_info']['lat'];
	//Address
	$Address_City = $data_config['smart_config']['user_info']['address']['province'];
	$Address_district = $data_config['smart_config']['user_info']['address']['district'];
	$Address_ward = $data_config['smart_config']['user_info']['address']['wards'];
	// smart_answer welcome
	$Welcome_Mode = $data_config['smart_answer']['sound']['welcome']['mode'];
	$Welcome_Path = $data_config['smart_answer']['sound']['welcome']['path'];
	$Welcome_Text = $data_config['smart_answer']['sound']['welcome']['text'];
	//address
	//Led
	$LED_TYPE = $data_config['smart_config']['led']['type'];
	$LED_NUMBER_LED = $data_config['smart_config']['led']['number_led'];
	$LED_EFFECT_MODE = $data_config['smart_config']['led']['effect_mode'];
	$LED_BRIGHTNESS = $data_config['smart_config']['led']['brightness'];
	$LED_WAKEUP_COLOR = $data_config['smart_config']['led']['wakeup_color'];
	$LED_MUTED_COLOR = $data_config['smart_config']['led']['muted_color'];
	$LED_LISTEN_EFFECT = $data_config['smart_config']['led']['listen_effect'];
	$LED_THINK_EFFECT = $data_config['smart_config']['led']['think_effect'];
	$LED_SPEAK_EFFECT = $data_config['smart_config']['led']['speak_effect'];
	//HOTWORD_ENGINE_KEY
	$HOTWORD_ENGINE_KEY = $data_config['smart_wakeup']['hotword_engine']['key'];
	$HOTWORD_ENGINE_TYPE = $data_config['smart_wakeup']['hotword_engine']['type'];
	// Tiếp tục hỏi khi trả lời xong
	$continuous_asking = $data_config['smart_request']['continuous_asking'];
	$Pre_Answer_Timeout = $data_config['smart_answer']['pre_answer_timeout'];
	$numberCharactersToSwitchMode = $data_config["smart_answer"]["number_characters_to_switch_mode"];
//Thay ĐỔi Ngôn Ngữ hotword
if (isset($_POST['language_hotword_submit'])) {
    $selectedLanguage = $_POST['language_hotword'];
//	echo $selectedLanguage;
 $jsonContent = file_get_contents($FileConfigJson);
    $jsonData = json_decode($jsonContent, true);
    // Xóa tất cả hotword hiện tại
    $jsonData['smart_wakeup']['hotword'] = [];
    // Lấy danh sách tên tệp trong thư mục tương ứng
    $folderPath = '/'.$DuognDanThuMucJson.'/hotword/' . $selectedLanguage . '/';
	 $fileList = glob($folderPath . '*.ppn');
    $fileList = array_diff($fileList, array('.', '..')); // Loại bỏ các tệp . và ..
    // Thêm hotword mới từ danh sách tên tệp
    foreach ($fileList as $filePath) {
		$fileName = pathinfo($filePath, PATHINFO_FILENAME);
        $jsonData['smart_wakeup']['hotword'][] = [
            "type" => "porcupine",
            "value" => null,
            "lang" => $selectedLanguage,
            "file_name" => $fileName.".ppn",
            "sensitive" => 0.1,
            "say_reply" => false,
            "command" => null,
            "active" => true
        ];
    }
    // Lưu lại các thay đổi vào tệp json.php
    file_put_contents($FileConfigJson, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
if ($selectedLanguage === "vi") {
	$hotword_lib_language = "porcupine_params_vn.pv";
} elseif ($selectedLanguage === "eng") {
  $hotword_lib_language = "porcupine_params.pv";
}
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die('Không thể kết nối tới máy chủ.');}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die('Đăng nhập không thành công.');}
$stream2 = ssh2_exec($connection, "sudo cp /home/pi/$hotword_lib_language /home/pi/.local/lib/python3.9/site-packages/pvporcupine/lib/common/porcupine_params.pv");
stream_set_blocking($stream2, true);
$stream_out2 = ssh2_fetch_stream($stream2, SSH2_STREAM_STDIO);
stream_get_contents($stream_out2);
header("Location: $PHP_SELF");
}
if(isset($_POST['config_setting'])) {
		//Lưu google.json STT
        $editedData = $_POST['edited_data_textarea'];
        // Kiểm tra nếu không có dữ liệu JSON
        if (empty($editedData)) {
            $editedData = '{}'; // Gán giá trị mặc định là JSON rỗng
        }
        // Kiểm tra lỗi cú pháp JSON
        if (json_decode($editedData) === null && json_last_error() !== JSON_ERROR_NONE) {
        echo "<br/><br/><br/><br/><br/><br/><br/><center><h1>Lỗi Ghi Dữ Liệu, Cấu Trúc json STT Google Cloud bạn nhập không hợp lệ<br/></h1><a href='$PHP_SELF'><h3>Nhấn Vào Đây Để Quay Lại</h3></a></center> ";
        exit();
		} else {
            // Lưu dữ liệu JSON vào tệp
            file_put_contents("$jsonFile", $editedData);
          //  echo "<script>Swal.fire('Thành công', 'Lưu thành công!', 'success');</script>";
        }
	//end lưu google.json
	
		//Lưu google.json TTS
        $editedData = $_POST['edited_data_textarea_tts_gcloud'];
        // Kiểm tra nếu không có dữ liệu JSON
        if (empty($editedData)) {
            $editedData = '{}'; // Gán giá trị mặc định là JSON rỗng
        }
        // Kiểm tra lỗi cú pháp JSON
       // if (json_decode($editedData) === null && json_last_error() !== JSON_ERROR_NONE) {
        if (json_decode($editedData) === null) {
        echo "<br/><br/><br/><br/><br/><br/><br/><center><h1>Lỗi Ghi Dữ Liệu, Cấu Trúc json TTS Google Cloud bạn nhập không hợp lệ<br/></h1><a href='$PHP_SELF'><h3>Nhấn Vào Đây Để Quay Lại</h3></a></center> ";
        exit();
		} else {
            // Lưu dữ liệu JSON vào tệp
            file_put_contents("$jsonFileGcloud", $editedData);
          //  echo "<script>Swal.fire('Thành công', 'Lưu thành công!', 'success');</script>";
        }
	//end lưu google.json
	
	
	//Backup Config
$backupDir = __DIR__ . '/Backup_Config/';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}
$backups = glob($backupDir . '*.json');
$numBackups = count($backups);
if ($numBackups >= $Limit_Config_Backup) {
    // Sắp xếp các tệp sao lưu theo thứ tự tăng dần về thời gian
    usort($backups, function ($a, $b) {
        return filemtime($a) - filemtime($b);
    });
    // Xóa các tệp sao lưu cũ nhất trừ tệp config_default.json và số lượng tệp cần giữ lại
    $keepBackups = ['config_default.json'];
    $numToDelete = $numBackups - $Limit_Config_Backup;
    $backupsToDelete = array_slice($backups, 0, $numToDelete);
    foreach ($backupsToDelete as $backup) {
        if (!in_array(basename($backup), $keepBackups)) {
            unlink($backup);
        }
    }
}
$backupFile = $backupDir . 'backup_config_' . date('d-m-Y_H:i:s') . '.json';
copy($FileConfigJson, $backupFile);
chmod($backupFile, 0777);
	// echo "Đã sao chép thành công tệp tin config.json sang $backupFile";
	//END Backup Config

	//hotwork
    // Lấy dữ liệu từ form
    $selectedFileName = $_POST['file_name'];
	if (strcasecmp(@$_POST['command'], "") === 0) {$commandHW = null;
    } else {$commandHW = @$_POST['command'];}
   $selectedSensitive = floatval($_POST['sensitive']);
    $selectedActive = isset($_POST['active']) ? true : false;
    $selectedSayReply = isset($_POST['say_reply']) ? true : false;
    // Đọc dữ liệu từ file config.json
    // Tìm và cập nhật thông tin của hotword được chọn
    foreach ($data_config['smart_wakeup']['hotword'] as &$hotword) {
        if ($hotword['file_name'] === $selectedFileName) {
            $hotword['sensitive'] = $selectedSensitive;
            $hotword['active'] = $selectedActive;
            $hotword['command'] = $commandHW;
            $hotword['say_reply'] = $selectedSayReply;
            break;
        }
    }
	// Lưu lại dữ liệu vào file config.json
	//Hỏi liên tục\
	 $data_config['smart_request']['continuous_asking'] = ($_POST['continuous_asking'] === 'true');
	//end hỏi liên tục
	
		//Chờ xử Lý Dữ Liệu
    $preAnswerList = $_POST["pre_answer"];
    $numberCharactersToSwitchMode = $_POST["number_characters_to_switch_mode"];
	
    foreach ($preAnswerList as $index => $preAnswer) {
        $value = $preAnswer["value"];
        if (empty($value)) {
            unset($preAnswerList[$index]);
        }
    }
    // Giới hạn số lượng pre_answer
    $preAnswerList = array_slice($preAnswerList, 0, $Limit_Pre_Answer);
    $data_config["smart_answer"]["pre_answer"] = array_values($preAnswerList);
    $data_config["smart_answer"]["number_characters_to_switch_mode"] = intval($numberCharactersToSwitchMode);
	//End Chờ xử lý dữ liệu
    // Lấy giá trị từ input
    $Volume_Value = @$_POST['volume_value'];
	$wakeup_reply = @$_POST['wakeup_reply'];
	$STT_Type = @$_POST['stt_type'];
	$STT_GG_Ass_Mode = @$_POST['stt_gg_ass_mode'];
    $STT_TimeOut = @$_POST['stt_time_out'];
	if (strcasecmp(@$_POST['token_stt'], "Null") === 0) {$STT_Token = null;
    } else {$STT_Token = @$_POST['token_stt'];}
	$TTS_Company = @$_POST['tts_company'];
	$TTS_Voice = @$_POST['tts_voice'];
	//$TTS_Token_Key = @$_POST['token_key_tts'];
	if (strcasecmp(@$_POST['token_key_tts'], "") === 0) {$TTS_Token_Key = null;
    } else {$TTS_Token_Key = @$_POST['token_key_tts'];}
	$GET_CARD_Speaker_Amixer_ID = @$_POST['input_number_card_number'];
	$Port_Input_Number = @$_POST['port_input_number'];
	//$HostName_Input = @$_POST['hostname_input'];
	//Led Config
	if (strcasecmp(@$_POST['led_chonkieu'], "Null") === 0) {$TTS_LED_Type_CheckINPUT = null;
    } else {$TTS_LED_Type_CheckINPUT = @$_POST['led_chonkieu'];}
	$Led_Number_Led = @$_POST['number_led'];
	$Led_Effect_Mode = @$_POST['effect_mode'];
	$Led_Brightness = @$_POST['brightness'];
	$Led_Wakeup_Color = @$_POST['wakeup_color'];
	$Led_Muted_Color = @$_POST['muted_color'];
	$Led_Listen_Effect = @$_POST['listen_effect'];
	$Led_Think_Effect = @$_POST['think_effect'];
	$Led_Speak_Effect = @$_POST['speak_effect'];
	//$Hotword_Engine_Type_Input = @$_POST['hotword_engine_type'];
	$Hotword_Engine_Key_Input = @$_POST['hotword_engine_key'];
	$buttonData = $_POST['button'];
	//echo "$TTS_Token_Key";
	$My_User_Name = @$_POST['my_user_name_input'];
	//Pre_Answer_Timeout
	$Pre_Answer_Timeoutttt = @$_POST['pre_answer_timeout'];

	// Thay đổi giá trị "TTS_Voice" thành null trong mảng
	 if (strcasecmp($TTS_Voice, "null") === 0) {$TTS_Voice_CheckINPUT = null;
    } else {$TTS_Voice_CheckINPUT = $TTS_Voice;}
	//VOLUME
	$data_volume->volume = intval($Volume_Value);
	$new_json_data_volume = json_encode($data_volume, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	file_put_contents($FileVolumeJson, $new_json_data_volume);
	//Kết thúc lưu giá trị volume
	// Cập nhật giá trị trong mảng Config.Json
	$newWakeupReply = [];
        foreach ($_POST['wakeup_reply'] as $value) {
            $newWakeupReply[] = array('value' => $value);
        }
	//stt
	$data_config['smart_request']['stt']['type'] = $STT_Type;
	$data_config['smart_request']['stt']['stt_gg_ass_mode'] = $STT_GG_Ass_Mode;
	$data_config['smart_request']['stt']['time_out'] = intval($STT_TimeOut);;
	$data_config['smart_request']['stt']['token'] = $STT_Token;
	//Pre_Answer_Timeoutttt
	$data_config['smart_answer']['pre_answer_timeout'] = intval($Pre_Answer_Timeoutttt);
	//tts
	$data_config['smart_answer']['tts']['type'] = $TTS_Company;
	$data_config['smart_answer']['tts']['token'] = $TTS_Token_Key;
	$data_config['smart_answer']['tts']['voice_name'] = $TTS_Voice_CheckINPUT;
	//console_ouput
	if (strcasecmp(@$_POST['console_ouput'], "Null") === 0) {$console_ouputrepl = null;
    } else {$console_ouputrepl = @$_POST['console_ouput'];}
	$data_config['smart_config']['console_ouput'] = $console_ouputrepl;
	//speaker, card id
	$data_config['smart_config']['speaker']['amixer_id'] = intval($GET_CARD_Speaker_Amixer_ID);
	//web_interface
	$data_config['smart_config']['web_interface']['port'] = intval($Port_Input_Number);
	//$data_config['smart_config']['web_interface']['hostname'] = $HostName_Input;
	//my_user
	$data_config['smart_config']['user_info']['name'] = $My_User_Name;
	//Address
	$data_config['smart_config']['user_info']['address']['province'] = @$_POST['city'];
	$data_config['smart_config']['user_info']['address']['district'] = @$_POST['district'];
	$data_config['smart_config']['user_info']['address']['wards'] = @$_POST['ward'];
	//Welcome Mode
	$data_config['smart_answer']['sound']['welcome']['mode'] = @$_POST['mode_options'];
	$data_config['smart_answer']['sound']['welcome']['path'] = @$_POST['mode_path'];
	//welcome Đọc Văn Bản Hoặc IP
	$welcome_text_ip = $_POST['mode_text']."".$_POST['welcome_ip'];
	$data_config['smart_answer']['sound']['welcome']['text'] = $welcome_text_ip; //welcome_ip
	//LED
	$data_config['smart_config']['led']['type'] = $TTS_LED_Type_CheckINPUT;
	$data_config['smart_config']['led']['number_led'] = intval($Led_Number_Led);
	$data_config['smart_config']['led']['effect_mode'] = intval($Led_Effect_Mode);
	$data_config['smart_config']['led']['brightness'] = intval($Led_Brightness);
	$data_config['smart_config']['led']['wakeup_color'] = $Led_Wakeup_Color;
	$data_config['smart_config']['led']['muted_color'] = $Led_Muted_Color;
	$data_config['smart_config']['led']['listen_effect'] = intval($Led_Listen_Effect);
	$data_config['smart_config']['led']['think_effect'] = intval($Led_Think_Effect);
	$data_config['smart_config']['led']['speak_effect'] = intval($Led_Speak_Effect);
	//Hotword_Engine_Key_Input
	$data_config['smart_wakeup']['hotword_engine']['key'] = $Hotword_Engine_Key_Input;
    $data_config['smart_wakeup']['wakeup_reply'] = $newWakeupReply;
	// Cập nhật dữ liệu của từng button từ dữ liệu gửi lên
    foreach ($buttonData as $buttonName => $buttonAttributes) {
        if (isset($data_config['smart_config']['button'][$buttonName])) {
            $data_config['smart_config']['button'][$buttonName]['gpio'] = intval($buttonAttributes['gpio']);
            $data_config['smart_config']['button'][$buttonName]['pulled_high'] = isset($buttonAttributes['pulled_high']);
            $data_config['smart_config']['button'][$buttonName]['active'] = isset($buttonAttributes['active']);
            }}
    $new_json_data_config = json_encode($data_config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    file_put_contents($FileConfigJson, $new_json_data_config);
header("Location: $PHP_SELF");
exit;
}
	//restart vietbot
if (isset($_POST['restart_vietbot'])) {
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die('Không thể kết nối tới máy chủ SSH');}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die('Đăng nhập SSH thất bại');}
$stream = ssh2_exec($connection, 'systemctl --user restart vietbot');
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
stream_get_contents($stream_out);
header("Location: $PHP_SELF");
exit;
}
	//Chmod sét full quyền
if (isset($_POST['set_full_quyen'])) {
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die('Không thể kết nối tới máy chủ.');}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die('Đăng nhập không thành công.');}
$stream1 = ssh2_exec($connection, 'sudo chmod -R 0777 /var/www/html/');
$stream2 = ssh2_exec($connection, 'sudo chmod -R 0777 /home/pi/vietbot_offline/src/');
stream_set_blocking($stream1, true); stream_set_blocking($stream2, true);
$stream_out1 = ssh2_fetch_stream($stream1, SSH2_STREAM_STDIO); $stream_out2 = ssh2_fetch_stream($stream2, SSH2_STREAM_STDIO);
stream_get_contents($stream_out1); stream_get_contents($stream_out2);
header("Location: $PHP_SELF"); exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<!-- Code By: Vũ Tuyển
Facebook: https://www.facebook.com/TWFyaW9uMDAx -->
    <title><?php echo $MYUSERNAME; ?>, Cấu Hình Config</title>
	    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <link rel="shortcut icon" href="../assets/img/VietBot128.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
 <link rel="stylesheet" href="../assets/css/4.5.2_css_bootstrap.min.css">
    <style>
	body {
  background-color:#dbe0c9;
}
.slider {
  width:200px;
}
.slider-value {
  display:inline-block;
  width:40px;
  text-align:center;
}
.hidden-input {
  display:none;
}
::-webkit-scrollbar {
  width:5px;
}
::-webkit-scrollbar-track {
  -webkit-box-shadow:inset 0 0 6px rgba(0,0,0,0.3);
  -webkit-border-radius:10px;
  border-radius:10px;
}
::-webkit-scrollbar-thumb {
  -webkit-border-radius:10px;
  border-radius:10px;
  background:rgb(251,255,7);
  -webkit-box-shadow:inset 0 0 6px rgba(0,0,0,0.5);
}
.popup-container {
  display:none;
  position:fixed;
  top:0;
  left:0;
  width:100%;
  height:100%;
  background-color:rgba(0,0,0,0.5);
  z-index:9999;
}
.popup-container.show {
  display:flex;
  align-items:center;
  justify-content:center;
}
#popupContent {
  background-color:white;
  padding:20px;
  border:1px solid gray;
  border-radius:5px;
}
a {
  text-decoration:none;
}

#loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    display: none;
}

#loading-icon {
    width: 50px;
    height: 50px;
    position: absolute;
    top: 42%;
    left: 50%;
    transform: translate(-50%, -50%);
}
#loading-message {
	   position: absolute;
    color: White;
	  top: 60%;
    left: 50%;
	  transform: translate(-50%, -50%);
}
</style>
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
</head>
<body>
<form method="post" id="my-form" onsubmit="return validateInputs();" action="<?php echo $PHP_SELF ?>"> 
<?php
// Thư mục cần kiểm tra
$directories = array(
    "$DuognDanUI_HTML",
    "$DuognDanThuMucJson"
);
function checkPermissions($path, &$hasPermissionIssue) {
    $files = scandir($path);
    foreach ($files as $file) {
		// bỏ qua thư mục tts_saved check quyền
        if ($file === '.' || $file === '..' || $file === 'tts_saved' || $file === '__pycache__') {continue;}
        $filePath = $path . '/' . $file;
        $permissions = fileperms($filePath);
        if ($permissions !== false && ($permissions & 0777) !== 0777) {
            if (!$hasPermissionIssue) {
                echo "<br/><center><h3 class='text-danger'>Một Số File,Thư Mục Trong <b>$path</b> Không Có Quyền Can Thiệp.<h3><br/>";
			echo " <button type='submit' name='set_full_quyen' class='btn btn-success'>Cấp Quyền Cho File, Thư Mục</button></center><hr/>";
                $hasPermissionIssue = true;
				//exit();
			}	
            break;}
        if (is_dir($filePath)) {
            checkPermissions($filePath, $hasPermissionIssue);
        }}}
// Kiểm tra từng thư mục
foreach ($directories as $directory) {
    $hasPermissionIssue = false;
    checkPermissions($directory, $hasPermissionIssue);
}
?>
<h5> Thông Tin Người Dùng:</h5><div class="row g-3 d-flex justify-content-center"><div class="col-auto"> 
<table class="table align-middle">
<tbody><tr>
<th scope="row">Tên Người Dùng:</th><td colspan="3">
<input type="text" class="form-control" name="my_user_name_input" value="<?php echo $MY_USER_NAME; ?>" placeholder="Nhập Tên Người Dùng Của Bạn" maxlength="10" required></td></tr>
 <tr><th scope="row">Địa Chỉ:</th>
<td><select class="custom-select" id="city" name="city"><option name="city" value="<?php echo $Address_City; ?>" selected><?php echo $Address_City; ?></option></select></td>
<td><select class="custom-select" id="district" name="district"><option name="district" value="<?php echo $Address_district; ?>" selected><?php echo $Address_district; ?></option></select></td>
<td><select class="custom-select" id="ward" name="ward"><option name="ward" value="<?php echo $Address_ward; ?>" selected><?php echo $Address_ward; ?></option></select></td>
</tr>
</tbody></table>
</div></div><hr/>
<!--END thông tin người dùng -->
	<!-- mục  Volume --> 
<h5> Sound Card/Volume:  <i class="bi bi-info-circle-fill" onclick="togglePopupVOLUME()" title="Nhấn Để Tìm Hiểu Thêm"></i></h5> <center>   
<div id="popupContainerVOLUME" class="popup-container" onclick="hidePopupVOLUME()">
<div id="popupContent" onclick="preventEventPropagationVOLUME(event)">
<center><b>Sound Card/Volume</b></center>
- <b>Card ID:</b> Số id của card âm thanh trên hệ thống nhận được<br/>
- <b>Âm Lượng:</b> Kéo để thay đổi mức âm lượng của loa</b><br/></div></div>
<div class="row justify-content-center"><div class="col-auto">
<table class="table table-responsive table-striped table-bordered align-middle"><tr>
<th scope="col"><center>Card ID:</center></th>
<th scope="col"><center>Âm lượng:</center></th></tr><tr>
<td><input type="number" class="form-control" title="Từ 0 Đến 3" title="Từ 0 Đến 3" name="input_number_card_number" size="28" value="<?php echo $GET_Speaker_Amixer_ID; ?>"  min="0" max="3" required></td>
<td><input type="range" name="volume_value" min="10" max="100" step="1" value="<?php echo $value_volume; ?>" class="slider" oninput="updateSliderValue(this.value)">
<span id="slider-value" class="slider-value"><?php echo $value_volume; ?>%</span></div> </td></tr></table></div></div></center><hr/>
<!-- Kết Thúc  Volume --> 
<!-- mục  Web Interface --> 
<h5>Web Interface: <i class="bi bi-info-circle-fill" onclick="togglePopupWeb()" title="Nhấn Để Tìm Hiểu Thêm"></i></h5> 
<div class="row g-3 d-flex justify-content-center"><div class="col-auto"> 
<table class="table table-responsive align-middle"><tbody><tr>
<th scope="row">Port:</th><td>
<input type="number" class="form-control" name="port_input_number" placeholder="5000" value="<?php echo $GET_Port_Web_Interface; ?>" min="0" max="9999" pattern="\d{4}" title="Vui lòng nhập 4 chữ số" required></td>
</tr></tbody></table></div></div>
<div id="popupContainerWeb" class="popup-container" onclick="hidePopupWeb()">
<div id="popupContent" onclick="preventEventPropagationWeb(event)">
<p><center><b>Web Interface</b></center><br/>
- <b>Port:</b> cổng port của web server, chatbot<br/>
- <b>Host Name:</b> <a href="http://<?php echo $HostName; ?>" target="_bank"><?php echo $HostName; ?></a><br/>
- <b>Ví Dụ:</b> <a href="http://<?php echo $HostName; ?>:<?php echo $GET_Port_Web_Interface; ?>" target="bank">http://<?php echo $HostName; ?>:<?php echo $GET_Port_Web_Interface; ?></a>
<br/></div></div><hr/>
<!-- Kết Thúc  Interface -->  

	<!-- mục  Hotword Engine --> 
<h5>Hotword Engine KEY: <i class="bi bi-info-circle-fill" onclick="togglePopup()" title="Nhấn Để Tìm Hiểu Thêm"></i></h5>
<div class="row g-3 d-flex justify-content-center"><div class="col-auto"> 
<table class="table table-responsive align-middle">
<tbody><tr><th scope="row">Token:</th>
<td><input type="text" placeholder="Nhập Key Của Bạn" title="Nhập Key Picovoice" class="form-control" style="width: 290px;" name="hotword_engine_key"  value="<?php echo $HOTWORD_ENGINE_KEY ?>" required></td>
</tr></tbody></table></div></div>
<div id="popupContainer" class="popup-container" onclick="hidePopup()">
<div id="popupContent" onclick="preventEventPropagation(event)">
<p><center><b>Hotword Engine</b></center><br/>
- Link Đăng Ký KEY API <a href="https://console.picovoice.ai/" target="_bank">Picovoice</a></p></div></div><hr/>
<!-- Kết Thúc Hotword Engine -->  
<!-- Trò Chuyện Liên Tục -->
<h5>Continuous Asking: <i class="bi bi-info-circle-fill" onclick="togglePopupTCLT()" title="Nhấn Để Tìm Hiểu Thêm"></i></h5>

<div id="popupContainerTCLT" class="popup-container" onclick="hidePopupTCLT()">
<div id="popupContent" onclick="preventEventPropagationTCLT(event)">
<p><center><b>Continuous Asking/Hỏi Liên Tục:</b></center><br/>
-  Bật để hỏi tiếp sau khi bot trả lời hoặc thực hiện xong 1 hành động nào đó và ngược lại
<br/></div></div>

<div class="row g-3 d-flex justify-content-center"><div class="col-auto">
			<div class="custom-control custom-switch mt-3" title="Bật để hỏi tiếp sau khi bot trả lời hoặc thực hiện xong 1 hành động nào đó và ngược lại">
                <input type="hidden" name="continuous_asking" value="false">
                <input type="checkbox" name="continuous_asking" class="custom-control-input" id="continuous-asking-toggle" value="true" <?php echo ($continuous_asking) ? 'checked' : ''; ?>>
                <label class="custom-control-label" for="continuous-asking-toggle"></label>
            </div></div></div>
<hr/>
<!-- END Trò Chuyện Liên Tục -->
<!--Bắt Đầu STT Speak To Text -->  
<h5> Speech to Text Engine: <i class="bi bi-info-circle-fill" onclick="togglePopupSTT()" title="Nhấn Để Tìm Hiểu Thêm"></i></h5><center>
<div id="popupContainerSTT" class="popup-container" onclick="hidePopupSTT()">
<div id="popupContent" onclick="preventEventPropagationSTT(event)">
<center><b>Cấu Hình STT</b></center><br/>
- Chuyển giọng nói thành văn bản</div></div>
<center><b>Bạn Đang Dùng STT: <font color="red"><?php echo $GET_STT_Replacee; ?></font></b></center>
<label><input type="radio" name="stt_type" title="Chuyển Giọng Nói Thành Văn Bản Server Google Cloud" value="stt_gg_cloud" <?php if ($GET_STT === 'stt_gg_cloud') echo 'checked'; ?> required  onchange="toggleTokenInput(this)">
Google Cloud</label><label>
<input type="radio" name="stt_type" title="Chuyển Giọng Nói Thành Văn Bản Server Google Assistant" value="stt_gg_ass" <?php if ($GET_STT === 'stt_gg_ass') echo 'checked'; ?> required  onchange="toggleTokenInput(this)">
Google Assistant</label><label>
<input type="radio" name="stt_type" title="Chuyển Giọng Nói Thành Văn Bản Server Google Free" value="stt_gg_free" <?php if ($GET_STT === 'stt_gg_free') echo 'checked'; ?> required  onchange="toggleTokenInput(this)">
Google Free</label><label>
<input type="radio" name="stt_type" title="Chuyển Giọng Nói Thành Văn Bản Server FPT" value="stt_fpt" <?php if ($GET_STT === 'stt_fpt') echo 'checked'; ?> required onchange="toggleTokenInput(this)">
FPT</label><label>
<input type="radio" name="stt_type" title="Chuyển Giọng Nói Thành Văn Bản Server Viettel" value="stt_viettel" <?php if ($GET_STT === 'stt_viettel') echo 'checked'; ?> required onchange="toggleTokenInput(this)">
Viettel</label><br/>
<div id="tokenInputContainer" style="display: none;">
<div class="row g-3 d-flex justify-content-center"><div class="col-auto">
<table class="table table-responsive align-middle"><tbody>
<tr><th scope="row">Token:</th>
<td> <input type="text" class="form-control" title="Nhập, Thay Đổi Token" name="token_stt" id="tokenInput" value="<?php echo $GET_Token_STT; ?>" required placeholder="Nhập Token STT" required></td></tr>
</tbody></table></div>
</div>
</div>

<div id="otherDivgcloud" style="display: none;">
<div class="row g-3 d-flex justify-content-center"><div class="col-auto">
   <textarea id="jsonTextareaGoogleCloud" class="form-control" name="edited_data_textarea" rows="10" cols="50"><?php echo $jsonDataGcloudTTS; ?></textarea><br/>
   <p onclick="clearTextareajsg()" class="btn btn-danger">Xóa Nội Dung</p>
   
</div>
</div>
</div>

<div id="otherDiv" style="display: none;">
<div class="row g-3 d-flex justify-content-center"><div class="col-auto">
<table class="table table-bordered"><thead><tr>
<th scope="col" title="Mặc định">Default</th>
<th scope="col" title="Thủ Công">Manual</th></tr></thead>
<tbody><tr>
<td><center><input type="radio" name="stt_gg_ass_mode" title="Google Assistatn Mode default" value="default" <?php if ($GET_STT_GG_ASS_MODE === 'default') echo 'checked'; ?>></center></td>
<td><center><input type="radio" name="stt_gg_ass_mode" title="Google Assistatn Mode manual" value="manual" <?php if ($GET_STT_GG_ASS_MODE === 'manual') echo 'checked'; ?>></center></td>
</tr></tbody></table></div></div></div>
<br/><label for="volume">Thời Gian Chờ:</label>
<input type="range" name="stt_time_out" title="Thời Gian Chờ" min="3000" max="8000" step="100" value="<?php echo $GET_TimeOut_STT; ?>" class="slider" oninput="updateSliderValueSTT(this.value)">
<span id="slider-stt" class="slider-stt"><?php echo $GET_TimeOut_STT,"ms"; ?> </span><br/>(1000 = 1 Giây)</center><hr/>
<!--Kết thúc STT Speak To Text --> 
<!--Text to Speech Engine --> 
<h5>Text to Speech Engine: <i class="bi bi-info-circle-fill" onclick="togglePopupTTS()" title="Nhấn Để Tìm Hiểu Thêm"></i></h5>		  
<center><div id="popupContainerTTS" class="popup-container" onclick="hidePopupTTS()">
<div id="popupContent" onclick="preventEventPropagationTTS(event)">
<center><b>Cấu Hình TTS</b></center><br/>
- Chuyển văn bản thành giọng nói
</div></div>
<center><b>Bạn Đang Dùng TTS: </b><b><font color="red"><?php echo $GET_TTS_Type_Replacee; ?></font></b></center>
<label><input type="radio" onclick="disableRadio()" name="tts_company" value="tts_gg_cloud" <?php if ($GET_TTS_Type === 'tts_gg_cloud') echo 'checked'; ?> onchange="showTokenInputTTS(this)" required>
Google Cloud</label><label>
<input type="radio" onclick="disableRadio()" name="tts_company" value="tts_gg_free" <?php if ($GET_TTS_Type === 'tts_gg_free') echo 'checked'; ?> onchange="showTokenInputTTS(this)" required>
Google Free</label><label>
<input type="radio" onclick="disableRadio()" name="tts_company" value="tts_fpt" <?php if ($GET_TTS_Type === 'tts_fpt') echo 'checked'; ?> onchange="showTokenInputTTS(this)" required>
FPT</label><label>
<input type="radio" onclick="disableRadio()" name="tts_company" value="tts_viettel" <?php if ($GET_TTS_Type === 'tts_viettel') echo 'checked'; ?> onchange="showTokenInputTTS(this)" required>
Viettel</label><label>
<input type="radio" onclick="disableRadio()" name="tts_company" value="tts_zalo" <?php if ($GET_TTS_Type === 'tts_zalo') echo 'checked'; ?> onchange="showTokenInputTTS(this)" required>
Zalo</label>
<div id="tokenInputContainerTTS" style="display: none;"><div class="row g-3 d-flex justify-content-center">
<div class="col-auto"><table class="table table-responsive align-middle">
<tbody><tr><th scope="row">Token:</th>
<td><input type="text" class="form-control" title="Nhập, Thay Đổi Token" id="tokenKeyTTS" value="<?php echo $GET_TTS_Token_Key; ?>" placeholder="Nhập Token TTS" name="token_key_tts"></td></tr>
</tbody></table></div></div></div>


<div id="tokenInputContainerTTSGGCLOUD" style="display: none;">
<div class="row g-3 d-flex justify-content-center"><div class="col-auto">
   <textarea id="jsonTextareaGoogleCloudTTS" class="form-control" name="edited_data_textarea_tts_gcloud" rows="10" cols="50"><?php echo $jsonDataGcloudSTT; ?></textarea><br/>
   <p onclick="clearTextareajsgCLOUD()" class="btn btn-danger">Xóa Nội Dung</p>
   
</div>
</div>
</div>


<br><b>Giọng Đọc:</b><br/><label>
<input type="radio" id="myRadio1" title="Nữ miền Bắc" name="tts_voice" value="female_northern_voice" <?php if ($GET_TTS_Voice_Name === 'female_northern_voice') echo 'checked'; ?> required>Nữ miền Bắc</label><label>
<input type="radio" id="myRadio2" title="Nam Miền Bắc" name="tts_voice" value="male_northern_voice" <?php if ($GET_TTS_Voice_Name === 'male_northern_voice') echo 'checked'; ?> required>Nam Miền Bắc</label><label>


<input type="radio" id="myRadio3" title="Nữ Miền Trung"  name="tts_voice" value="female_middle_voice" <?php if ($GET_TTS_Voice_Name === 'female_middle_voice') echo 'checked'; ?> required>Nữ Miền Trung</label><label>
<input type="radio" id="myRadio4" title="Nam Miền Trung"  name="tts_voice" value="male_middle_voice" <?php if ($GET_TTS_Voice_Name === 'male_middle_voice') echo 'checked'; ?> required>Nam Miền Trung</label><label>


<input type="radio" id="myRadio5"  title="Nữ Miền Nam" name="tts_voice" value="female_southern_voice" <?php if ($GET_TTS_Voice_Name === 'female_southern_voice') echo 'checked'; ?> required>Nữ Miền Nam</label><label>
<input type="radio" id="myRadio6" title="Viettel Nam Miền Nam" id="myRadio2" name="tts_voice" value="male_southern_voice" <?php if ($GET_TTS_Voice_Name === 'male_southern_voice') echo 'checked'; ?> required>Nam Miền Nam</label><label>

<input type="radio" id="myRadio7" name="tts_voice" value="null" <?php if ($GET_TTS_Voice_Name === null) echo 'checked'; ?> required>Mặc Định</label></center><hr/>
<!-- -->
<h5>Console Ouput:</h5> 
<div class="row g-3 d-flex justify-content-center"><div class="col-auto"> 
<table class="table">
 <thead>
     <tr>
      <th scope="col" colspan="3"><center>Đầu Ra Bảng Điều Khiển</center></th>
    </tr>
    <tr>
      <th scope="col"><center>Không</center></th>
      <th scope="col"><center>Đầy Đủ</center></th>
      <th scope="col"><center>Xem Tức Thời</center></th>
    </tr>
  </thead>
   <tbody>
    <tr>
      <td><center><input type="radio" name="console_ouput" value="Null" <?php if ($Get_Console_Ouput === 'Null') echo 'checked'; ?> required></center></td>
      <td><center><input type="radio" name="console_ouput" value="full" <?php if ($Get_Console_Ouput === 'full') echo 'checked'; ?> required></center></td>
      <td><center><input type="radio" name="console_ouput" value="watching" <?php if ($Get_Console_Ouput === 'watching') echo 'checked'; ?> required></center></td>
    </tr>
</tbody>
</table>
</div>
</div>
<hr/>


<?php
//value Thông báo IP
 $dataexplo = explode(", | địa chỉ ai pi của mình là:", $Welcome_Text);
    if (count($dataexplo) > 0) {
        $Welcome_Text_ip = trim($dataexplo[0]);
    } else {
	   $Welcome_Text_ip = "Lỗi Lấy Dữ Liệu";
    }
?>
<h5 title="Thông Báo Chào Mừng Khi Thiết Bị Khởi Động Xong">Thông Báo Khởi Động:</h5>
<div class="row g-3 d-flex justify-content-center"><div class="col-auto">
  <table class="table"><thead><tr>
      <th scope="col"><center>Đọc Văn Bản</center></th>
      <th scope="col"><center>Dùng File Âm Thanh</center></th></tr></thead><tbody><tr>
      <td><center><input type="radio" name="mode_options" value="text" <?php if ($Welcome_Mode === 'text') echo 'checked'; ?> onchange="toggleElementsWEL(this)"></center></td>
      <td><center><input type="radio" name="mode_options" value="path" <?php if ($Welcome_Mode === 'path') echo 'checked'; ?> onchange="toggleElementsWEL(this)"></center></td>
</tr><tr><td> 
<center><input type="text" name="mode_text" value="<?php echo $Welcome_Text_ip; ?>" id="text-input" class="form-control" placeholder="Nhập văn bản, lời chào"></center></td>
<td>
<center>
  <?php
  // Tìm kiếm các file .mp3 và .wav trong thư mục "welcome"
  $folderPath = "$DuognDanThuMucJson/sound/welcome/";
  $files = glob($folderPath . '*.mp3');
  $files = array_merge($files, glob($folderPath . '*.wav'));
  if (!empty($files)) {
    // Hiển thị dropdown list với danh sách các file
  //  echo '<select id="path-dropdown" class="custom-select" style="display: none;">';
    echo '<select id="path-dropdown" name="mode_path" class="custom-select">';
    echo "<option value='$Welcome_Path'>".basename($Welcome_Path)."</option>";
    foreach ($files as $file) {
      echo '<option value="sound/welcome/' . basename($file) . '">' . basename($file) . '</option>';
    }
    echo '</select>';
  } else {
    echo "Không tìm thấy file mp3 và wav trong thư mục 'welcome'.";
  }
  ?>

  </center></td></tr>
 <!-- <tr id="text-inputt">
  <td><b>Đọc địa chỉ ip:</b> <input type="checkbox"  name="welcome_ip" value=", | địa chỉ ai pi của mình là: <?php //echo $serverIP; ?>" <?php /* if ($Welcome_Text === $Welcome_Text_ip.', | địa chỉ ai pi của mình là: '.$serverIP) echo 'checked'; */ ?>>
</td></tr> -->
  
  </tbody></table></div></div><hr/>
  
  
  
  
  
  
<!-- mục  Chọn Kiểu LED --> 
<h5>Chọn Kiểu LED: <i class="bi bi-info-circle-fill" onclick="togglePopupLED()" title="Nhấn Để Tìm Hiểu Thêm"></i></h5>
<div class="form-check form-switch d-flex justify-content-center">   <div class="col-auto">
<div id="toggleIcon" onclick="toggleDivz()">
    <i id="upIconz" class="bi bi-arrow-up-circle-fill" title="Nhấn Để Ẩn Cài Đặt LED" style="display: none;"></i>
    <i id="downIconz" class="bi bi-arrow-down-circle-fill" title="Nhấn Để Hiển Thị Cài Đặt LED"></i>
</div></div></div> 
<div id="popupContainerLED" class="popup-container" onclick="hidePopupLED()">
<div id="popupContent" onclick="preventEventPropagationLED(event)">
<center><b>Cấu Hình Đèn LED</b></center><br/>
- Chọn phần cứng của bạn và cấu hình các mục chức năng cho phép nhập bên dưới<br/><br/></div></div>
<div id="myDivz" style="display: none;"><center>
 <label><input type="radio" name="led_chonkieu" value="Vietbot AIO Board V2.0" <?php if ($LED_TYPE === 'Vietbot AIO Board V2.0') echo 'checked'; ?> onclick="handleLedChange()" required>
Vietbot AIO Board V2.0</label>
<label><input type="radio" name="led_chonkieu" value="ReSpeaker 2-Mics Pi HAT" <?php if ($LED_TYPE === 'ReSpeaker 2-Mics Pi HAT') echo 'checked'; ?> onclick="handleLedChange()" required>
Mic2HAT</label>
<label><input type="radio" name="led_chonkieu"  value="ReSpeaker 4-Mics Pi HAT" <?php if ($LED_TYPE === 'ReSpeaker 4-Mics Pi HAT') echo 'checked'; ?> onclick="handleLedChange()" required>
Mic4HAT</label>
<label><input type="radio" name="led_chonkieu"  value="APA102" <?php if ($LED_TYPE === 'APA102') echo 'checked'; ?> onclick="handleLedChange()" required>
APA102</label>
<label><input type="radio" name="led_chonkieu"  value="ReSpeaker Mic Array v2.0" <?php if ($LED_TYPE === 'ReSpeaker Mic Array v2.0') echo 'checked'; ?> onclick="handleLedChange()" required>
Respeaker USB</label>
<label><input type="radio" name="led_chonkieu"  value="WS2812" <?php if ($LED_TYPE === 'WS2812') echo 'checked'; ?> onclick="handleLedChange()">
WS2812</label>
<label><input type="radio" name="led_chonkieu"  value="Null" <?php if ($LED_TYPE === null) echo 'checked'; ?> onclick="handleLedChange()" required>
None (Không Dùng)</label></center>
<div class="row justify-content-center"><div class="col-auto">
<table class="table table-bordered">
<tr><th scope="row">Số led:</th>
<td colspan="2"><input type="text"  value="<?php echo $LED_NUMBER_LED; ?>" id="number_led_mode_input" name="number_led" class="disabled-input form-control"></td></tr>
<tr><th scope="row">Chế độ hiệu ứng:</th>
<td colspan="2"><input type="text"  value="<?php echo $LED_EFFECT_MODE; ?>" id="effect_mode_input" name="effect_mode" class="disabled-input form-control"></td></tr>
<tr><th scope="row">Độ sáng:</th>
<td colspan="2"><input type="text"  value="<?php echo $LED_BRIGHTNESS; ?>" id="brightness_mode_input" name="brightness" class="disabled-input form-control" ></td></tr>
<tr><th scope="row">Hiệu ứng nghe:</th>
<td colspan="2"><input type="text"  value="<?php echo $LED_LISTEN_EFFECT; ?>" id="listen_effect_mode_input" name="listen_effect" class="disabled-input form-control"></td></tr>
<tr><th scope="row">Hiệu ứng chờ trả lời</th>
<td colspan="2"><input type="text"  value="<?php echo $LED_THINK_EFFECT; ?>" id="think_effect_mode_input" name="think_effect" class="disabled-input form-control"></td></tr>
<tr><th scope="row">Hiệu ứng khi nói:</th>
<td colspan="2"><input type="text"   value="<?php echo $LED_SPEAK_EFFECT; ?>" id="speak_effect_mode_input" name="speak_effect" class="disabled-input form-control"></td></tr>
<tr><th scope="row">Màu khi được đánh thức:</th>
<td><input type="text"  id="wakeup_color_mode_input" title="Nhập Mã Màu" placeholder="03254b" value="<?php echo $LED_WAKEUP_COLOR; ?>"  name="wakeup_color" maxlength="6" class="disabled-input form-control"></td>
<td><input type="color" id="color_pickerwakeup_color" title="Nhấn Để Hiển Thị Bảng Mã Màu" class="disabled-input form-control-color" onchange="updateColorValueWakeUp_Color()"></td></tr>
<tr><th scope="row">Màu khi tắt tiếng:</th>
<td><input type="text"  value="<?php echo $LED_MUTED_COLOR; ?>" title="Nhập Mã Màu" placeholder="FF3333"  id="muted_color_mode_input" name="muted_color" maxlength="6" class="disabled-input form-control"></td>
<td><input type="color" title="Nhấn Để Hiển Thị Bảng Mã Màu" id="color_pickermuted_color" class="disabled-input form-control-color hidden-inputLED" onchange="updateColorValueMuted_Color()"></td></tr>
</table></div></div></div><hr/>
  <!-- end chọn kiểu led -->
	<!--Button -->
<h5>Cấu Hình Nút Nhấn: <i class="bi bi-info-circle-fill" onclick="togglePopupGPIO()" title="Nhấn Để Tìm Hiểu Thêm"></i></h5>
<div class="form-check form-switch d-flex justify-content-center"><div id="toggleIcon" onclick="toggleDivzx()">
    <i id="upIconzx" title="Nhấn Để Đóng Cấu Hình Cài Đặt Nút Nhấn" class="bi bi-arrow-up-circle-fill" style="display: none;"></i>
    <i id="downIconzx" title="Nhấn Để Mở Cấu Hình Cài Đặt Nút Nhấn" class="bi bi-arrow-down-circle-fill" ></i></div></div>
<div id="popupContainerGPIO" class="popup-container" onclick="hidePopupGPIO()">
<div id="popupContent" onclick="preventEventPropagationGPIO(event)">
<p><center><b>Cấu Hình Nút Nhấn GPIO</b></center><br/>
- <b>GPIO:</b> Chọn Chân GPIO để cài đặt chức năng cho nút bấm<br/>
- <b>Kéo Mức Cao:</b> Tích Để Kéo Nút Nhấn Lên Mức Cao (3.3V), Bỏ Tích Kéo Xuống Mức Thấp GND<br/>
- <b>Kích Hoạt:</b> Tích để kích hoạt chức năng của nút nhấn, bỏ tích nút nhấn sẽ bị vô hiệu
<br/></div></div>
<!--  
 <div id="input-divv" style="display: none;"> -->
<div id="myDivzx" style="display: none;">
<div class="row justify-content-center">
<div class="col-auto">
<table class="table table-responsive table-striped table-bordered align-middle">
<tr><th scope="col">Nút Nhấn</th>
<th scope="col">GPIO</th>
<th scope="col" title="Tích Để Kéo Nút Nhấn Lên Mức Cao (3.3V), Bỏ Tích Kéo Xuống Mức Thấp GND">Kéo Mức Cao</th>
<th scope="col" title="Tích Vào Để Kích Hoạt Chức Năng Của Nút Nhấn, Bỏ Tích Nút Nhấn Sẽ Bị Vô Hiệu">Kích Hoạt</th></tr>
<?php
    foreach ($data_config['smart_config']['button'] as $buttonName => $buttonData) {
		echo '<tr>';
        echo '<th scope="row">' . $buttonName . ':</th>';
        echo '<td><!-- GPIO --><input type="number" class="form-control" style="width: 70px;" min="3" max="26" title="Cấu Hình Chân Chức Năng Của GPIO" style="width: 40px;" name="button[' . $buttonName . '][gpio]" value="' . $buttonData['gpio'] . '" placeholder="' . $buttonData['gpio'] . '"></td>';
        echo '<td><!-- Pulled High --><input type="checkbox" title="Tích Để Kéo Nút Nhấn Lên Mức Cao (3.3V), Bỏ Tích Kéo Xuống Mức Thấp GND" name="button[' . $buttonName . '][pulled_high]"' . ($buttonData['pulled_high'] ? ' checked' : '') . '></td>';
        echo '<td><!-- Active --><input type="checkbox" title="Tích Vào Để Kích Hoạt Chức Năng Của Nút Nhấn, Bỏ Tích Nút Nhấn Sẽ Bị Vô Hiệu" name="button[' . $buttonName . '][active]"' . ($buttonData['active'] ? ' checked' : '') . '></td></tr>';
	}
?>
</table></div></div></div><hr/>
<!-- END mục  Chọn Kiểu LED --> 
	<!--HOT WORK --> 
<h5>HotWord: <i class="bi bi-info-circle-fill" onclick="togglePopuphw()" title="Nhấn Để Tìm Hiểu Thêm"></i></h5>
<div class="form-check form-switch d-flex justify-content-center"> 
<div id="toggleIcon" onclick="toggleDivzxchw()">
<i id="upIconzxchw" title="Nhấn Để Mở Cấu Hình Cài Đặt HotWord" class="bi bi-arrow-up-circle-fill" style="display: none;"></i>
<i id="downIconzxchw" title="Nhấn Để Đóng Cấu Hình Cài Đặt HotWord" class="bi bi-arrow-down-circle-fill" ></i></div></div>  
<div id="popupContainerhw" class="popup-container" onclick="hidePopuphw()"><div id="popupContent" onclick="preventEventPropagationhw(event)">
<p><center><b>HotWord</b></center><br/>
- <b>Tên Hotword:</b> Chọn tên hotword cần chỉnh sửa giá trị<br/>
- <b>Độ Nhạy:</b> chỉnh dộ nhạy của HotWord khi được gọi từ 0.1 đến 1<br/>
- <b>Kích Hoạt:</b> Tích để kích hoạt Hotword, bỏ tích Hotword đó sẽ bị vô hiệu<br/>
- <b>Phản Hồi Của Bot Khi Được Đánh Thức:</b> Tích để kích hoạt, bỏ tích sẽ bị vô hiệu.<br/>
- <b>Hotwork File:</b> <a href="https://github.com/Picovoice/porcupine/tree/master/resources" target="_bank">Picovoice HotWord File</a><br/>
- <b>Thư Viện Hotwork:</b> <a href="https://github.com/Picovoice/porcupine/tree/master/lib/common" target="_bank">Picovoice HotWord Lib</a>
<br/></div></div>
<div id="myDivzxchw" style="display: none;">
<div class="row justify-content-center"><div class="col-auto">
<table style="border-color:black;" class="table table-responsive table-bordered align-middle">
<thead><tr> <th scope="col" colspan="4"><center class="text-success">Cài Đặt Hotword</center></th> </tr>
<tr><th scope="col"><label for="" class="form-label">Tên Hotword</label></th>
<th scope="col"><label for="" title="Độ Nhạy Sensitive" class="form-label">Độ Nhạy</label></th>
<th scope="col"><label for="" title="Tích Để Bật/Tắt Hotword" class="form-label">Kích Hoạt</label></th>
<th scope="col"><label for="" title="Bật/Tắt Phản Hồi Của Bot Khi Được Đánh Thức" class="form-label"><center>Phản Hồi Lại</center></label></th>
<tbody><tr><td><div>
<select id="file_name" name="file_name" class="custom-select" onchange="showSensitiveInput(this.value)">
<option value="">Chọn Hotword</option>
<?php foreach ($data_config['smart_wakeup']['hotword'] as $hotword): ?>
<option value="<?php echo $hotword['file_name']; ?>"><?php echo substr($hotword['file_name'], 0, strpos($hotword['file_name'], "_")); ?></option>
<?php endforeach; ?>
</select></div></td><td><div>
<input type="number" id="sensitive" name="sensitive" title="Chỉ Được Nhập Số Từ 0.1 Đến 1" placeholder="0.1 -> 1" class="form-control" step="0.1" min="0" max="1">
</div></td><td><div>
<center><input type="checkbox" id="active" name="active" title="Tích vào để kích hoạt" class="form-check-input"></center>
</div></td><td><div>
<center><input type="checkbox" id="say_reply" name="say_reply" title="Tích vào để kích hoạt" class="form-check-input"></center></div></td><tr>
<th scope="row"><center class="input-group-text">Kèm câu lệnh:</center></th><td colspan="3"><div>
<center><input type="text" id="command" name="command" placeholder="Nhập Lệnh Vào Đây" title="Nhập Lệnh Của Bạn" class="form-control"></center></div></td></tr>
</tr></tbody> </tr></thead></table> 
</div>
<div class="col-auto">
<table style="border-color:black;" class="table table-sm table-bordered table-responsive align-middle">
<thead><tr>
<th colspan="2"><center class="text-success">Thay Đổi Ngôn Ngữ Hotword <i class="bi bi-info-circle-fill" onclick="togglePopuphwlang()" title="Nhấn Để Tìm Hiểu Thêm"></i></center></th>
</tr></thead><tbody><tr> 
<td  scope="col" colspan="2"><center><font color="red">Bạn Đang Dùng: <b><?php echo $hotwords_get_lang; ?></b></font></center></td>
<tr><tr><td><center><b>Tiếng Việt</b></center></td><td><center><b>Tiếng Anh</b></center></td>
</tr><tr><td> <center><input type="radio" name="language_hotword" id="language_hotwordddd" value="vi"></center></td>
<td><center><input type="radio" name="language_hotword" id="language_hotwordddd1" value="eng"></center></td>
</tr><tr><th><center><button type="submit" name="language_hotword_submit" class="btn btn-success">Lưu Cài Đặt</button></th> 
<th><p onclick="uncheckRadiolanguage_hotwordddd()" class="btn btn-danger">Bỏ Chọn</p></th></center></th></tr></tbody></table></div></div></div>
<div id="popupContainerhwlang" class="popup-container" onclick="hidePopuphwlang()"><div id="popupContent" onclick="preventEventPropagationhwlang(event)">
<p><center><b>Thay Đổi Ngôn Ngữ Gọi Hotword</b></center><br/>
- <b>1: </b> 2 file thư viện <a href="https://github.com/Picovoice/porcupine/blob/master/lib/common/porcupine_params.pv" target="_bank">tiếng anh</a> 
	<b>"porcupine_params.pv"</b> và <a href="https://github.com/Picovoice/porcupine/blob/master/lib/common/porcupine_params_vn.pv" target="_bank">tiếng việt</a> 
	<b>"porcupine_params_vn.pv"</b><br/>phải nằm cùng trong đường dẫn sau: "/home/pi"<br/>
- <b>2: </b>các file thư viện hotword, file hotword, thư viện picovoice phải cùng phiên bản.<br/>
- <i>Khi thay đổi ngôn ngữ bạn sẽ cần phải cấu hình lại các Hotword ở mục <b>Cài Đặt Hotword</b></i>
</div></div>
<hr/>
<!--END HOT WORK --> 
<h5>Thông Báo/Thời Gian Chờ:</h5>
<div class="form-check form-switch d-flex justify-content-center"> 
<div id="toggleIcon" onclick="toggleDivzxcans()">
<i id="upIconzxcans" title="Nhấn Để Mở Cấu Hình Cài Đặt Wake Up Reply" class="bi bi-arrow-up-circle-fill" style="display: none;"></i>
<i id="downIconzxcans" title="Nhấn Để Đóng Cấu Hình Cài Đặt Wake Up Reply" class="bi bi-arrow-down-circle-fill" ></i>
</div>
</div>


 <div id="myDivzxcans" style="display: none;"> 
<div class="row justify-content-center"><div class="col-auto">
<table class="table table-bordered">
  <tbody><tr>
      <th scope="row" title="Pre Answer Timeout">Thời Gian Chờ (giây):</th>
      <td><input class="form-control" name="pre_answer_timeout" title="Từ 1 -> 15 (giây)" value="<?php echo $Pre_Answer_Timeout; ?>" placeholder="8" type="number" min="3" max="15" step="1"></td>
</tr><tr>
	  <th scope="row" title="Number Characters To Switch Mode">Tự Động Chuyển Sang Playback Nếu Số Ký Tự Trong Câu Trả Lời Vượt Quá:</th>
	  <td><input class="form-control" type="number" min="200" max="1000" step="10" title="Từ 200 đến 1000" placeholder="300" name="number_characters_to_switch_mode" value="<?php echo $numberCharactersToSwitchMode ?>"></td>
    </tr>
<?php
    // Hiển thị các pre_answer hiện tại
    foreach ($data_config["smart_answer"]["pre_answer"] as $index => $preAnswer) {
        $value = $preAnswer["value"];
		$indexup = $index + 1;
		echo "<tr><th scope='row'>Nội Dung Thông Báo $indexup:</th>";
        echo '<td><input class="form-control" placeholder="' . $value . '" type="text" name="pre_answer[' . $index . '][value]" value="' . $value . '"></td>';
        echo "</tr>";
    }
    if (count($data_config["smart_answer"]["pre_answer"]) < $Limit_Pre_Answer) {
        echo "<tr><th scope='row'>Nhập Mới Thông Báo:</th>";
        echo '<td><input class="form-control" type="text" name="pre_answer[' . count($data_config["smart_answer"]["pre_answer"]) . '][value]" placeholder="Nhập Mới Thông Báo Chờ"></td>';
        echo "</tr>";
    }
    ?>
</tbody></table></div></div></div>
	<hr/>
	
	<!-- mục  Wake Up Reply --> 
	<!-- <div id="additionalDiv" class="hidden">  -->
<h5>Phản Hồi Của Bot Khi Đánh Thức: <i class="bi bi-info-circle-fill" onclick="togglePopupWAKEUP()" title="Nhấn Để Tìm Hiểu Thêm"></i></h5>
<div class="form-check form-switch d-flex justify-content-center"> 
<div id="toggleIcon" onclick="toggleDivzxc()">
<i id="upIconzxc" title="Nhấn Để Mở Cấu Hình Cài Đặt Wake Up Reply" class="bi bi-arrow-up-circle-fill" style="display: none;"></i>
<i id="downIconzxc" title="Nhấn Để Đóng Cấu Hình Cài Đặt Wake Up Reply" class="bi bi-arrow-down-circle-fill" ></i></div></div>

<div id="popupContainerWAKEUP" class="popup-container" onclick="hidePopupWAKEUP()">
<div id="popupContent" onclick="preventEventPropagationWAKEUP(event)">
<p><center><b>Wake Up Reply</b></center><br/>
<b><center>Phản Hồi Của Bot Khi Đánh Thức</center></b>
- <i><b>Yêu Cầu:</b> Tích Vào <b>"Phản Hồi Của Bot Khi Đánh Thức"</b> Trong Mục Hotword Để Kích Hoạt Theo Từng Hotword<br/></i>
- <i><b>Chức Năng:</b> Trả lời khi được gọi hoặc đánh thức<br/></i>
- <i><b>Câu Trả Lời:</b> tự động chọn ngẫu nhiên 1 trong các câu trả lời để phản hồi lại.<br/></i>
<i><center>Nếu Wake Up Reply không hiển thị để chỉnh sửa tức là nội dung trong <b>config.json</b> không phù hợp, vượt quá <?php echo $Limit_Wakeup_Reply; ?> giá trị</center></i>
<br/></div></div>
 <div id="myDivzxc" style="display: none;"> 
<?php
// Kiểm tra số lượng giá trị và hiển thị kết quả
if (count($GET_wakeupReply) > $Limit_Wakeup_Reply) {
    echo "<center><h5> Wake Up Reply Không Được Hiển Thị Do <b>config.json<b/> Không Phù Hợp, Vượt Quá $Limit_Wakeup_Reply Giá Trị</h5></center>";
	    foreach ($GET_wakeupReply as $index => $reply) {
        echo '<div style="display: none;"><input type="hidden" name="wakeup_reply[]" id="input' . ($index + 1) . '" value="' . $reply['value'] . '" placeholder="' . $reply['value'] . '" class="form-control" aria-label="Username" aria-describedby="basic-addon1" required>
			</div>';
    }
} 
else {
    foreach ($GET_wakeupReply as $index => $reply) {
        echo '<div class="input-group mb-3 d-flex justify-content-center"><div class="input-group-prepend">
			<span class="input-group-text" id="basic-addon1">Câu Trả Lời ' . ($index + 1) . ':</span></div>  <div class="col-md-6">
            <div class="form-outline"> <input type="text" name="wakeup_reply[]" id="input' . ($index + 1) . '" value="' . $reply['value'] . '" placeholder="' . $reply['value'] . '" class="form-control" aria-label="Username" aria-describedby="basic-addon1" required></div>         </div>
			</div>';
    }
    }
?>
</div><hr/>
<!-- </div> -->
<!--Kết Thúc mục  Wake Up Reply --> 		
<center>
<input type="submit" class="btn btn-success" name="config_setting" value="Lưu Cấu Hình">  <a href="<?php echo $PHP_SELF ?>"><button type="button" class="btn btn-danger">Hủy Bỏ/Làm Mới</button></a>
 <button type="submit" name="restart_vietbot" class="btn btn-warning">Khởi Động Lại VietBot</button></center></form><hr/>    
<div id="loading-overlay"><img id="loading-icon" src="../assets/img/Loading.gif" alt="Loading...">
<div id="loading-message">- Đang Thực Hiện<br/>- Bạn Cần Restart Lại VietBot Để Áp Dụng Dữ Liệu Mới</div>
</div><center><h5>Khôi Phục File config.json: <i class="bi bi-info-circle-fill" onclick="togglePopupConfigRecovery()" title="Nhấn Để Tìm Hiểu Thêm"></i></h5></center>
<div class="form-check form-switch d-flex justify-content-center"> 
<div id="toggleIcon" onclick="toggleDivConfigRecovery()">
<i id="upIconConfigRecovery" title="Nhấn Để Mở Cấu Hình Cài Đặt Wake Up Reply" class="bi bi-arrow-up-circle-fill" style="display: none;"></i>
<i id="downIconConfigRecovery" title="Nhấn Để Đóng Cấu Hình Cài Đặt Wake Up Reply" class="bi bi-arrow-down-circle-fill" ></i></div></div>
 <div id="popupContainerConfigRecovery" class="popup-container" onclick="hidePopupConfigRecovery()">
<div id="popupContent" onclick="preventEventPropagationConfigRecovery(event)">
<p><center><b>Khôi Phục File Config</b></center><br/>
<b>-</b><i> Hệ thống sẽ tự đông sao lưu file <b>config.json</b> mỗi khi bạn lưu cấu hình mới, tối đa là <?php echo $Limit_Config_Backup; ?></i><br/>
<b>-</b><i> Hãy chọn 1 trong <?php echo $Limit_Config_Backup; ?> file config.json cấu hình trước đó để khôi phục lại</i><br/>
<b>-</b><i> File: config_default.json là file mặc định trên github</i><br/></div></div>
  <div id="myDivConfigRecovery" style="display: none;">
  <div class="form-check form-switch d-flex justify-content-center"> 
<?php
// Kiểm tra xem có file nào trong thư mục hay không
if (count($fileLists) > 0) {
    // Tạo dropdown list để hiển thị các file
    echo '<form method="get"><div class="input-group">';
    echo '<select class="custom-select" id="inputGroupSelect04" name="selectedFile">';
    echo '<option value="">Chọn file backup config</option>'; // Thêm lựa chọn "Chọn file"
    foreach ($fileLists as $file) {
        $fileName = basename($file);
        echo '<option value="' . $file . '">' . $fileName . '</option>';
    }
    echo '</select><div class="input-group-append">';
    echo '<input type="submit" class="btn btn-warning" title="Khôi Phục Lại File config.json trước đó đã sao lưu" value="Khôi Phục/Recovery">';
    echo ' </div></div></form>';
}
 else {
    echo "Không tìm thấy file backup config trong thư mục.";
}
?></div></div>
	<script src="../assets/js/bootstrap.js"></script>
	<script src="../assets/js/jquery.min.js"></script>
	<script src="../assets/js/axios_0.21.1.min.js"></script>
	   <script src="../assets/js/jquery-3.6.1.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
  <script>
       function showSensitiveInput(file_name) {
            var sensitiveInput = document.getElementById('sensitive');
            var sensitiveLabel = document.querySelector('label[for="sensitive"]');
            var activeInput = document.getElementById('active');
            var sayReplyInput = document.getElementById('say_reply');
             var commandInput = document.getElementById('command'); // Trường input command
            var sayReplyLabel = document.querySelector('label[for="say_reply"]');
            if (file_name !== '') {
                sensitiveInput.classList.remove('hidden');
                sensitiveInput.value = getSensitiveValue(file_name);
                activeInput.classList.remove('hidden');
                activeInput.checked = getActiveValue(file_name);
                sayReplyInput.classList.remove('hidden');
                sayReplyInput.checked = getSayReplyValue(file_name);
			         // Hiển thị dữ liệu command
      commandInput.classList.remove('hidden');
      commandInput.value = getCommandValue(file_name);
            } else {
                sensitiveInput.classList.add('hidden');
                sensitiveInput.value = '';
                activeInput.classList.add('hidden');
                activeInput.checked = false;
                sayReplyInput.classList.add('hidden');
                sayReplyInput.checked = false;
			    // Ẩn trường input command khi không có dữ liệu
      commandInput.classList.add('hidden');
      commandInput.value = '';
            }
        }
        function getSensitiveValue(file_name) {
            var sensitiveData = <?php echo json_encode($data_config['smart_wakeup']['hotword']); ?>;
            for (var i = 0; i < sensitiveData.length; i++) {
                if (sensitiveData[i]['file_name'] === file_name) {
                    return sensitiveData[i]['sensitive'];
                }
            }
            return '';
        }
        function getActiveValue(file_name) {
            var activeData = <?php echo json_encode($data_config['smart_wakeup']['hotword']); ?>;
            for (var i = 0; i < activeData.length; i++) {
                if (activeData[i]['file_name'] === file_name) {
                    return activeData[i]['active'];
                }
            }
            return false;
        }
        function getSayReplyValue(file_name) {
            var sayReplyData = <?php echo json_encode($data_config['smart_wakeup']['hotword']); ?>;
            for (var i = 0; i < sayReplyData.length; i++) {
                if (sayReplyData[i]['file_name'] === file_name) {
                    return sayReplyData[i]['say_reply'];
                }
            }
            return false;
        }
		
		function getCommandValue(file_name) {
    var commandData = <?php echo json_encode($data_config['smart_wakeup']['hotword']); ?>;
    for (var i = 0; i < commandData.length; i++) {
      if (commandData[i]['file_name'] === file_name) {
        return commandData[i]['command'];
      }
    }
    return '';
  }
  //ẩn hiện thẻ input của Wake Up
    $(document).ready(function() {
      $('#toggleButton').click(function() {
        $('.hidden-input').toggle();
      });
    });
	/*
	//ẩn hiện thẻ div mục Wake Up
	    function toggleInput() {
      var inputContainer = document.getElementById("inputContainer");
      inputContainer.style.display = (inputContainer.style.display === "none") ? "block" : "none";
    }
	*/
	//ẩn hiện cấu hình nút nhấn
	/*
    function toggleInputVisibilityy() {
      var inputDivv = document.getElementById("input-divv");
      var switchStatee = document.getElementById("switchh").checked;
      inputDivv.style.display = switchStatee ? "block" : "none";
    }	
	*/
	//Ẩn hiện thẻ input LED
    function toggleInputVisibility() {
      var inputDiv = document.getElementById("input-div");
      var switchState = document.getElementById("switch").checked;
      inputDiv.style.display = switchState ? "block" : "none";
    }
	
	/*
	//ẩn hiện cấu hình hotword_engine
    function toggleInputVisibilityyy() {
      var inputDivvv = document.getElementById("input-divvv");
      var switchStateee = document.getElementById("switchhh").checked;
      inputDivvv.style.display = switchStateee ? "block" : "none";
    }	
	*/
	//nút radio và xử lý sự kiện để vô hiệu hóa các nút radio khác khi một nút radio được chọn:
    const ttsCompanyRadios = document.querySelectorAll('input[name="tts_company"]');
    const ttsVoiceRadios = document.querySelectorAll('input[name="tts_voice"]');
    ttsCompanyRadios.forEach(companyRadio => {
        companyRadio.addEventListener('change', function () {
            if (companyRadio.value === 'tts_viettel') {
                ttsVoiceRadios.forEach(voiceRadio => {
                    if (voiceRadio.value === 'null_tuyen') { //tts Google Free
                        voiceRadio.disabled = true;
                    } else {
                        voiceRadio.disabled = false;
                    }
                });
            }
			 else if (companyRadio.value === 'tts_gg_free') {
                ttsVoiceRadios.forEach(voiceRadio => {
                    if (voiceRadio.value === 'female_northern_voice' || voiceRadio.value === 'female_southern_voice' || voiceRadio.value === 'female_middle_voice' || voiceRadio.value === 'male_northern_voice' || voiceRadio.value === 'male_southern_voice' || voiceRadio.value === 'male_middle_voice') {
                        voiceRadio.disabled = true;
                    } else {
                        voiceRadio.disabled = false;
                    }
                });
            }
			else if (companyRadio.value === 'tts_zalo') {
                ttsVoiceRadios.forEach(voiceRadio => {
                    if (voiceRadio.value === 'null_tuyen') { //tts Google Free
                        voiceRadio.disabled = true;
                    } else {
                        voiceRadio.disabled = false;
                    }
                });
            }
			
			else if (companyRadio.value === 'tts_fpt') {
                ttsVoiceRadios.forEach(voiceRadio => {
                    if (voiceRadio.value === 'null_tuyen') { //tts Google Free
                        voiceRadio.disabled = true;
                    } else {
                        voiceRadio.disabled = false;
                    }
                });
            }
			
			else if (companyRadio.value === 'tts_gg_cloud') {
                ttsVoiceRadios.forEach(voiceRadio => {
                    if (voiceRadio.value === 'male_middle_voice' || voiceRadio.value === 'female_middle_voice' || voiceRadio.value === 'male_southern_voice' || voiceRadio.value === 'female_southern_voice' || voiceRadio.value === 'null') {
                        voiceRadio.disabled = true;
                    } else {
                        voiceRadio.disabled = false;
                    }
                });
            }
        });
    });
	
	//Xóa Cheker radio
	    function disableRadio() {
     //Cheker radio 1
       document.getElementById("myRadio1").disabled = true;
	  document.getElementById("myRadio1").checked = false;
     //Cheker radio 2
       document.getElementById("myRadio2").disabled = true;
	  document.getElementById("myRadio2").checked = false;
     //Cheker radio 3
       document.getElementById("myRadio3").disabled = true;
	  document.getElementById("myRadio3").checked = false;
     //Cheker radio 4
       document.getElementById("myRadio4").disabled = true;
	  document.getElementById("myRadio4").checked = false;
     //Cheker radio 5
       document.getElementById("myRadio5").disabled = true;
	  document.getElementById("myRadio5").checked = false;
	       //Cheker radio 6
       document.getElementById("myRadio6").disabled = true;
	  document.getElementById("myRadio6").checked = false;
	  	       //Cheker radio 7
       document.getElementById("myRadio7").disabled = true;
	  document.getElementById("myRadio7").checked = false;
    }
	/////////
	//value tts viettell, zalo
function showTokenInputTTS(radio) {
  var tokenInputContainerTTS = document.getElementById("tokenInputContainerTTS");
  var tokenInputContainerTTSGGCLOUD = document.getElementById("tokenInputContainerTTSGGCLOUD");
 // var otherDivgcloudTTS = document.getElementById("otherDivgcloudTTS");
  if (radio.value === "tts_zalo" || radio.value === "tts_viettel" || radio.value === "tts_fpt") {
    tokenInputContainerTTS.style.display = "block";
    tokenInputContainerTTSGGCLOUD.style.display = "none";
  //  otherDivgcloudTTS.style.display = "none";
  } else if (radio.value === "tts_gg_cloud") {
    tokenInputContainerTTS.style.display = "none";
    tokenInputContainerTTSGGCLOUD.style.display = "block";
  //  otherDivgcloudTTS.style.display = "block";
  }
else if (radio.value === "tts_gg_free") {
    tokenInputContainerTTS.style.display = "none";
    tokenInputContainerTTSGGCLOUD.style.display = "none";
  // tự đông đánh dấu checked khi tích vào tts_gg_free
    var targetRadio = document.getElementById("myRadio7"); 
    targetRadio.checked = true;
  }
  else {
    tokenInputContainerTTS.style.display = "none";
    tokenInputContainerTTSGGCLOUD.style.display = "none";
  }
}
	///////
	        function updateSliderValue(value) {
            document.getElementById('slider-value').innerHTML = value + '%';
        }
		        function updateSliderValueSTT(value) {
            document.getElementById('slider-stt').innerHTML = value + 'ms';
        }
		/*
		//Ghi giá trị vào input host name khi nhấn button
        function ghiGiaTriHostName() {
            document.getElementById("hostname_input_element").value = "<?php echo $HostName; ?>";
        }
		*/
//Script LED
	//Bảng Mã màu check wakeup_color
    function updateColorValueWakeUp_Color() {
      var colorInput = document.getElementById("wakeup_color_mode_input");
      var colorPicker = document.getElementById("color_pickerwakeup_color");
      var selectedColor = colorPicker.value;
      colorInput.value = selectedColor.replace("#", "");
    }
	//Bảng Mã màu check muted_color
	    function updateColorValueMuted_Color() {
      var colorInput = document.getElementById("muted_color_mode_input");
      var colorPicker = document.getElementById("color_pickermuted_color");
      var selectedColor = colorPicker.value;
      colorInput.value = selectedColor.replace("#", "");
    }
	//End
	// Xử lý các thay đổi loại đèn LED cụ thể
        function handleLedChange() {
            var selectedLed = document.querySelector('input[name="led_chonkieu"]:checked').value;
            var disabledInputs = document.getElementsByClassName("disabled-input");
            var DeleteText = document.getElementsByClassName("disabled-input");
			//
			var NumberModeLed = document.getElementById("number_led_mode_input");
            var EffectModeInput = document.getElementById("effect_mode_input");
            var BrightnessModeInput = document.getElementById("brightness_mode_input");
            var WakeupColorModeInput = document.getElementById("wakeup_color_mode_input");
            var MutedColorModeInput = document.getElementById("muted_color_mode_input");
            var ListenEffectModeInput = document.getElementById("listen_effect_mode_input");
            var ThinkEffectModeInput = document.getElementById("think_effect_mode_input");
            var SpeakEffectModeInput = document.getElementById("speak_effect_mode_input");
            // Vô hiệu hóa tất cả các đầu vào ban đầu
            for (var i = 0; i < disabledInputs.length; i++) {
                disabledInputs[i].disabled = true;
            }
            // Xử lý các thay đổi loại đèn LED cụ thể
			//ReSpeaker 2-Mics Pi HAT
            if (selectedLed === "ReSpeaker 2-Mics Pi HAT") {
                NumberModeLed.type = "text";
                NumberModeLed.value = "";
				EffectModeInput.type = "text";
				EffectModeInput.value = "";
				BrightnessModeInput.type = "text";
				BrightnessModeInput.value = "";
				WakeupColorModeInput.type = "text";
				WakeupColorModeInput.value = "";
				MutedColorModeInput.type = "text";
				MutedColorModeInput.value = "";
				ListenEffectModeInput.type = "text";
				ListenEffectModeInput.value = "";
				ThinkEffectModeInput.type = "text";
				ThinkEffectModeInput.value = "";
				SpeakEffectModeInput.type = "text";
				SpeakEffectModeInput.value = "";
            } 
			//ReSpeaker 4-Mics Pi HAT
			else if (selectedLed === "ReSpeaker 4-Mics Pi HAT") {
				disabledInputs["effect_mode_input"].disabled = false;
				disabledInputs["effect_mode_input"].required = true;
                //NumberModeLed.type = "text";
                NumberModeLed.value = "";
				EffectModeInput.type = "number";
				EffectModeInput.value = "<?php echo $LED_EFFECT_MODE; ?>";
				EffectModeInput.min = "1";
				EffectModeInput.max = "2";
				EffectModeInput.placeholder = "1->2"
				//BrightnessModeInput.type = "text";
				BrightnessModeInput.value = "";
				//WakeupColorModeInput.type = "text";
				WakeupColorModeInput.value = "";
				//MutedColorModeInput.type = "text";
				MutedColorModeInput.value = "";
				//ListenEffectModeInput.type = "text";
				ListenEffectModeInput.value = "";
				ListenEffectModeInput.placeholder = "";
				//ThinkEffectModeInput.type = "text";
				ThinkEffectModeInput.value = "";
				ThinkEffectModeInput.placeholder = "";
				//SpeakEffectModeInput.type = "text";
				SpeakEffectModeInput.value = "";
				SpeakEffectModeInput.placeholder = "";
			} 
			//APA102
			else if (selectedLed === "APA102") {
				disabledInputs["effect_mode_input"].disabled = false;
				disabledInputs["effect_mode_input"].required = true;
                //NumberModeLed.type = "text";
                NumberModeLed.value = "";
				EffectModeInput.type = "number";
				EffectModeInput.value = "<?php echo $LED_EFFECT_MODE; ?>";
				EffectModeInput.min = "1";
				EffectModeInput.max = "2";
				EffectModeInput.placeholder = "1->2"
				//BrightnessModeInput.type = "text";
				BrightnessModeInput.value = "";
				//WakeupColorModeInput.type = "text";
				WakeupColorModeInput.value = "";
				//MutedColorModeInput.type = "text";
				MutedColorModeInput.value = "";
				//ListenEffectModeInput.type = "text";
				ListenEffectModeInput.value = "";
				ListenEffectModeInput.placeholder = "";
				//ThinkEffectModeInput.type = "text";
				ThinkEffectModeInput.value = "";
				ThinkEffectModeInput.placeholder = "";
				//SpeakEffectModeInput.type = "text";
				SpeakEffectModeInput.value = "";
				SpeakEffectModeInput.placeholder = "";
			}
			//ReSpeaker Mic Array v2.0 | ReSpeaker USB
			else if (selectedLed === "ReSpeaker Mic Array v2.0") {
				disabledInputs["wakeup_color_mode_input"].disabled = false;
				disabledInputs["muted_color_mode_input"].disabled = false;
				disabledInputs["color_pickerwakeup_color"].disabled = false;
				disabledInputs["color_pickermuted_color"].disabled = false;
				disabledInputs["wakeup_color_mode_input"].required = true;
				disabledInputs["muted_color_mode_input"].required = true;
				disabledInputs["color_pickerwakeup_color"].required = true;
				disabledInputs["color_pickermuted_color"].required = true;
				NumberModeLed.value = "";
				NumberModeLed.placeholder = "";
				//EffectModeInput.type = "text";
				EffectModeInput.value = "";
				EffectModeInput.placeholder = ""
				BrightnessModeInput.value = "";
				BrightnessModeInput.placeholder = "";
				WakeupColorModeInput.type = "text"; 
				WakeupColorModeInput.value = "<?php echo $LED_WAKEUP_COLOR; ?>"; 
				WakeupColorModeInput.pattern = "[a-zA-Z0-9]*"; 
				MutedColorModeInput.type = "text"; 
				MutedColorModeInput.value = "<?php echo $LED_MUTED_COLOR; ?>"; 
				MutedColorModeInput.pattern = "[a-zA-Z0-9]*"; 
				ListenEffectModeInput.value = "";
				ListenEffectModeInput.placeholder = "";
				ThinkEffectModeInput.value = "";
				ThinkEffectModeInput.placeholder = "";
				SpeakEffectModeInput.value = "";
				SpeakEffectModeInput.placeholder = "";
			}
			//WS2812
			else if (selectedLed === "WS2812") {
					for (var i = 0; i < disabledInputs.length; i++) {
				disabledInputs[i].disabled = false;
			}
					for (var i = 0; i < disabledInputs.length; i++) {
				disabledInputs[i].required = true;
			}
			disabledInputs["effect_mode_input"].disabled = true;
			disabledInputs["effect_mode_input"].required = false;
				NumberModeLed.type = "number";
				NumberModeLed.value = "<?php echo $LED_NUMBER_LED; ?>";
				NumberModeLed.min = "0";
				NumberModeLed.placeholder = "16";
				EffectModeInput.type = "text";
				EffectModeInput.value = "<?php echo $LED_EFFECT_MODE; ?>";
				EffectModeInput.placeholder = ""
				BrightnessModeInput.type = "number";
				BrightnessModeInput.value = "<?php echo $LED_BRIGHTNESS; ?>";
				BrightnessModeInput.min = "0";
				BrightnessModeInput.placeholder = "150";
				WakeupColorModeInput.type = "text";
				WakeupColorModeInput.value = "<?php echo $LED_WAKEUP_COLOR; ?>";
				MutedColorModeInput.type = "text";
				MutedColorModeInput.value = "<?php echo $LED_MUTED_COLOR; ?>";
				//ListenEffectModeInput.value = "";
				ListenEffectModeInput.type = "number";
				ListenEffectModeInput.value = "<?php echo $LED_LISTEN_EFFECT; ?>";
				ListenEffectModeInput.min = "1";
				ListenEffectModeInput.max = "3";
				ListenEffectModeInput.placeholder = "1->3";
				//ThinkEffectModeInput.value = "";
				ThinkEffectModeInput.type = "number";
				ThinkEffectModeInput.value = "<?php echo $LED_THINK_EFFECT; ?>";
				ThinkEffectModeInput.min = "1";
				ThinkEffectModeInput.max = "3";
				ThinkEffectModeInput.placeholder = "1->3";
				//SpeakEffectModeInput.value = "";
				SpeakEffectModeInput.type = "number";
				SpeakEffectModeInput.value = "<?php echo $LED_SPEAK_EFFECT; ?>";
				SpeakEffectModeInput.min = "1";
				SpeakEffectModeInput.max = "3";
				SpeakEffectModeInput.placeholder = "1->3";
			}
			//None
			else if (selectedLed === "None") {
				//disabledInputs["effect_mode_input"].disabled = false;
                NumberModeLed.type = "text";
                NumberModeLed.value = "";
                NumberModeLed.placeholder = "";
				EffectModeInput.type = "text";
				EffectModeInput.value = "";
				EffectModeInput.placeholder = "";
				BrightnessModeInput.type = "text";
				BrightnessModeInput.value = "";
				BrightnessModeInput.placeholder = "";
				WakeupColorModeInput.type = "text";
				WakeupColorModeInput.value = "";
				WakeupColorModeInput.placeholder = "";
				MutedColorModeInput.type = "text";
				MutedColorModeInput.value = "";
				MutedColorModeInput.placeholder = "";
				ListenEffectModeInput.type = "text";
				ListenEffectModeInput.value = "";
				ListenEffectModeInput.placeholder = "";
				ThinkEffectModeInput.type = "text";
				ThinkEffectModeInput.value = "";
				ThinkEffectModeInput.placeholder = "";
				SpeakEffectModeInput.type = "text";
				SpeakEffectModeInput.value = "";
				SpeakEffectModeInput.placeholder = "";
			}
			//Vietbot AIO Board V2.0
			else if (selectedLed === "Vietbot AIO Board V2.0") {
				for (var i = 0; i < disabledInputs.length; i++) {
				disabledInputs[i].disabled = false;
			}
				// xóa text trong input
				for (var ii = 0; ii < DeleteText.length; ii++) {
				DeleteText[ii].value = "";
			}
				for (var i = 0; i < disabledInputs.length; i++) {
				disabledInputs[i].required = true;
			}
				NumberModeLed.type = "number";
				NumberModeLed.value = "<?php echo $LED_NUMBER_LED; ?>";
				NumberModeLed.placeholder = "16";
				EffectModeInput.type = "number";
				EffectModeInput.value = "<?php echo $LED_EFFECT_MODE; ?>";
				EffectModeInput.min = "1";
				EffectModeInput.max = "2";
				EffectModeInput.placeholder = "1->2"
				BrightnessModeInput.type = "number";
				BrightnessModeInput.value = "<?php echo $LED_BRIGHTNESS; ?>";
				BrightnessModeInput.placeholder = "150";
				BrightnessModeInput.min = "0";
				WakeupColorModeInput.type = "text"; 
				WakeupColorModeInput.value = "<?php echo $LED_WAKEUP_COLOR; ?>"; 
				WakeupColorModeInput.pattern = "[a-zA-Z0-9]*"; 
				MutedColorModeInput.type = "text"; 
				MutedColorModeInput.value = "<?php echo $LED_MUTED_COLOR; ?>"; 
				MutedColorModeInput.pattern = "[a-zA-Z0-9]*"; 
				//ListenEffectModeInput.value = "";
				ListenEffectModeInput.type = "number";
				ListenEffectModeInput.value = "<?php echo $LED_LISTEN_EFFECT; ?>";
				ListenEffectModeInput.min = "1";
				ListenEffectModeInput.max = "10";
				ListenEffectModeInput.placeholder = "1->10";
				//ThinkEffectModeInput.value = "";
				ThinkEffectModeInput.type = "number";
				ThinkEffectModeInput.value = "<?php echo $LED_THINK_EFFECT; ?>";
				ThinkEffectModeInput.min = "1";
				ThinkEffectModeInput.max = "10";
				ThinkEffectModeInput.placeholder = "1->10";
				//SpeakEffectModeInput.value = "";
				SpeakEffectModeInput.type = "number";
				SpeakEffectModeInput.value = "<?php echo $LED_SPEAK_EFFECT; ?>";
				SpeakEffectModeInput.min = "1";
				SpeakEffectModeInput.max = "10";
				SpeakEffectModeInput.placeholder = "1->10";
			}
			}
//End Led Script
//Kiểm tra các chân gpio không được giống nhau
function validateInputs() {
    var name1 = document.getElementsByName("button[down][gpio]")[0].value.trim();
    var name2 = document.getElementsByName("button[up][gpio]")[0].value.trim();
    var name3 = document.getElementsByName("button[wakeup][gpio]")[0].value.trim();
    var name4 = document.getElementsByName("button[mic][gpio]")[0].value.trim();
    if (name1 === name2 || name1 === name3 || name1 === name4 || name2 === name3 || name2 === name4 || name3 === name4) {
        alert("Cấu Hình Nút Nhấn:\n\nCác chân GPIO không được cấu hình giống nhau");
        return false;
    }
    return true;
}
    function showPosition() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(updateInputValues);
      } else {
        alert("Trình duyệt của bạn không hỗ trợ Geolocation.");
      }
    }
    function updateInputValues(position) {
      var latitude = position.coords.latitude;
      var longitude = position.coords.longitude;
      document.getElementById("latitudeInput").value = latitude;
      document.getElementById("longitudeInput").value = longitude;
    }
// CHo phép nhập khi ấn vào checkbox
	    function toggleInputtt(checkbox) {
      var input = document.getElementById("myInput");
      if (checkbox.checked) {
        input.disabled = false;
      } else {
        input.disabled = true;
        input.value = "123";
      }
    }
	
	function toggleTokenInput(radio) {
  var tokenInputContainer = document.getElementById("tokenInputContainer");
  var tokenInput = document.getElementById("tokenInput");
  var otherDiv = document.getElementById("otherDiv");
  var otherDivgcloud = document.getElementById("otherDivgcloud");

  if (radio.value === "stt_fpt" || radio.value === "stt_viettel") {
    tokenInputContainer.style.display = "block";
    otherDiv.style.display = "none";
	otherDivgcloud.style.display = "none";
    tokenInput.value = "<?php echo $GET_Token_STT; ?>";
  } else if (radio.value === "stt_gg_ass") {
    tokenInputContainer.style.display = "none";
    otherDiv.style.display = "block";
	otherDivgcloud.style.display = "none";
    tokenInput.value = "Null";
  }else if (radio.value === "stt_gg_cloud") {
    tokenInputContainer.style.display = "none";
    otherDiv.style.display = "none";
    otherDivgcloud.style.display = "block";
    tokenInput.value = "Null";
  }else if (radio.value === "stt_gg_free") {
    tokenInputContainer.style.display = "none";
    otherDiv.style.display = "none";
	otherDivgcloud.style.display = "none";
    tokenInput.value = "Null";
  } else {
    tokenInputContainer.style.display = "none";
    otherDiv.style.display = "none";
	otherDivgcloud.style.display = "none";
    tokenInput.value = "Null";
  }
 
  
  
  
}

	//togglePopup
	 function hidePopup() {
      var popupContainer = document.getElementById("popupContainer");
      popupContainer.classList.remove("show");
    }
	//Hotword Engine Key
    function togglePopup() {
      var popupContainer = document.getElementById("popupContainer");
      popupContainer.classList.toggle("show");
    }
    function preventEventPropagation(event) {
      event.stopPropagation();
    }
// togglePopupWeb web
    function togglePopupWeb() {
      var popupContainer = document.getElementById("popupContainerWeb");
      popupContainer.classList.toggle("show");
    }
    function hidePopupWeb() {
      var popupContainer = document.getElementById("popupContainerWeb");
      popupContainer.classList.remove("show");
    }
    function preventEventPropagationWeb(event) {
      event.stopPropagation();
    }
// togglePopupGPIO GPIO
    function togglePopupGPIO() {
      var popupContainer = document.getElementById("popupContainerGPIO");
      popupContainer.classList.toggle("show");
    }
    function hidePopupGPIO() {
      var popupContainer = document.getElementById("popupContainerGPIO");
      popupContainer.classList.remove("show");
    }
    function preventEventPropagationGPIO(event) {
      event.stopPropagation();
    }
// togglePopupLED LED
    function togglePopupLED() {
      var popupContainer = document.getElementById("popupContainerLED");
      popupContainer.classList.toggle("show");
    }
    function hidePopupLED() {
      var popupContainer = document.getElementById("popupContainerLED");
      popupContainer.classList.remove("show");
    }
    function preventEventPropagationLED(event) {
      event.stopPropagation();
    }
// togglePopupTTS TTS
    function togglePopupTTS() {
      var popupContainer = document.getElementById("popupContainerTTS");
      popupContainer.classList.toggle("show");
    }
    function hidePopupTTS() {
      var popupContainer = document.getElementById("popupContainerTTS");
      popupContainer.classList.remove("show");
    }
    function preventEventPropagationTTS(event) {
      event.stopPropagation();
    }
// togglePopupVOLUME VOLUME
    function togglePopupVOLUME() {
      var popupContainer = document.getElementById("popupContainerVOLUME");
      popupContainer.classList.toggle("show");
    }
    function hidePopupVOLUME() {
      var popupContainer = document.getElementById("popupContainerVOLUME");
      popupContainer.classList.remove("show");
    }
    function preventEventPropagationVOLUME(event) {
      event.stopPropagation();
    }
// togglePopupSTT STT
    function togglePopupSTT() {
      var popupContainer = document.getElementById("popupContainerSTT");
      popupContainer.classList.toggle("show");
    }
    function hidePopupSTT() {
      var popupContainer = document.getElementById("popupContainerSTT");
      popupContainer.classList.remove("show");
    }
    function preventEventPropagationSTT(event) {
      event.stopPropagation();
    }
// togglePopupWAKEUP WAKEUP
    function togglePopupWAKEUP() {
      var popupContainer = document.getElementById("popupContainerWAKEUP");
      popupContainer.classList.toggle("show");
    }
    function hidePopupWAKEUP() {
      var popupContainer = document.getElementById("popupContainerWAKEUP");
      popupContainer.classList.remove("show");
    }
    function preventEventPropagationWAKEUP(event) {
      event.stopPropagation();
    }
	
// togglePopupWAKEUP WAKEUP
    function togglePopupTCLT() {
      var popupContainer = document.getElementById("popupContainerTCLT");
      popupContainer.classList.toggle("show");
    }
    function hidePopupTCLT() {
      var popupContainer = document.getElementById("popupContainerTCLT");
      popupContainer.classList.remove("show");
    }
    function preventEventPropagationTCLT(event) {
      event.stopPropagation();
    }
	
	// togglePopupConfig File Recovery
    function togglePopupConfigRecovery() {
      var popupContainer = document.getElementById("popupContainerConfigRecovery");
      popupContainer.classList.toggle("show");
    }
    function hidePopupConfigRecovery() {
      var popupContainer = document.getElementById("popupContainerConfigRecovery");
      popupContainer.classList.remove("show");
    }
    function preventEventPropagationConfigRecovery(event) {
      event.stopPropagation();
    }	
// togglePopupHotWork HotWork
    function togglePopuphw() {
      var popupContainer = document.getElementById("popupContainerhw");
      popupContainer.classList.toggle("show");
    }
    function hidePopuphw() {
      var popupContainer = document.getElementById("popupContainerhw");
      popupContainer.classList.remove("show");
    }
    function preventEventPropagationhw(event) {
      event.stopPropagation();
    }
// togglePopuphwlang
    function togglePopuphwlang() {
      var popupContainer = document.getElementById("popupContainerhwlang");
      popupContainer.classList.toggle("show");
    }
    function hidePopuphwlang() {
      var popupContainer = document.getElementById("popupContainerhwlang");
      popupContainer.classList.remove("show");
    }
    function preventEventPropagationhwlang(event) {
      event.stopPropagation();
    }
//ẩn hiện cấu hình nút nhấn
    function toggleDivz() {
      var div = document.getElementById("myDivz");
      var upIcon = document.getElementById("upIconz");
      var downIcon = document.getElementById("downIconz");

      if (div.style.display === "none") {
        div.style.display = "block";
		upIcon.style.display = "inline-block";
        downIcon.style.display = "none";

      } else {
        div.style.display = "none";
		upIcon.style.display = "none";
        downIcon.style.display = "inline-block";
      }
    }
	// ẩn hiện cấu hình led
    function toggleDivzx() {
      var div = document.getElementById("myDivzx");
      var upIcon = document.getElementById("upIconzx");
      var downIcon = document.getElementById("downIconzx");
      if (div.style.display === "none") {
        div.style.display = "block";
		upIcon.style.display = "inline-block";
        downIcon.style.display = "none";
      } else {
        div.style.display = "none";
		upIcon.style.display = "none";
        downIcon.style.display = "inline-block";
      }
    }
		// ẩn hiện cấu hotword
    function toggleDivzxchw() {
      var div = document.getElementById("myDivzxchw");
      var upIcon = document.getElementById("upIconzxchw");
      var downIcon = document.getElementById("downIconzxchw");
      if (div.style.display === "none") {
        div.style.display = "block";
		upIcon.style.display = "inline-block";
        downIcon.style.display = "none";
      } else {
        div.style.display = "none";
		upIcon.style.display = "none";
        downIcon.style.display = "inline-block";
      }
    }
	
	
	
		// ẩn hiện cấu hình Config File Recovery
    function toggleDivConfigRecovery() {
      var div = document.getElementById("myDivConfigRecovery");
      var upIcon = document.getElementById("upIconConfigRecovery");
      var downIcon = document.getElementById("downIconConfigRecovery");
      if (div.style.display === "none") {
        div.style.display = "block";
		upIcon.style.display = "inline-block";
        downIcon.style.display = "none";
      } else {
        div.style.display = "none";
		upIcon.style.display = "none";
        downIcon.style.display = "inline-block";
      }
    }
	// aanrh iện wake up reply
    function toggleDivzxc() {
      var div = document.getElementById("myDivzxc");
      var upIcon = document.getElementById("upIconzxc");
      var downIcon = document.getElementById("downIconzxc");
      if (div.style.display === "none") {
        div.style.display = "block";
		upIcon.style.display = "inline-block";
        downIcon.style.display = "none";
      } else {
        div.style.display = "none";
		upIcon.style.display = "none";
        downIcon.style.display = "inline-block";
      }
    }
	
	
	// aanrh iện pre_answer 
    function toggleDivzxcans() {
      var div = document.getElementById("myDivzxcans");
      var upIcon = document.getElementById("upIconzxcans");
      var downIcon = document.getElementById("downIconzxcans");
      if (div.style.display === "none") {
        div.style.display = "block";
		upIcon.style.display = "inline-block";
        downIcon.style.display = "none";
      } else {
        div.style.display = "none";
		upIcon.style.display = "none";
        downIcon.style.display = "inline-block";
      }
    }
	//icon Loading
$(document).ready(function() {
    $('#my-form').on('submit', function() {
        // Hiển thị biểu tượng loading
        $('#loading-overlay').show();

        // Vô hiệu hóa nút gửi
        $('#submit-btn').attr('disabled', true);
    });
});
//button xóa check vào radio thay đổi ngôn ngữ hw
	function uncheckRadiolanguage_hotwordddd() {
  var radio = document.getElementById("language_hotwordddd");
  radio.checked = false;
    var radio1 = document.getElementById("language_hotwordddd1");
  radio1.checked = false;
}

    // Lắng nghe sự kiện thay đổi của checkbox Welcome
    var checkboxes = document.getElementsByName("options");
    checkboxes.forEach(function(checkbox) {
      checkbox.addEventListener("change", function() {
        toggleElementsWEL(this);
      });
    });
    function toggleElementsWEL(element) {
      var textInput = document.getElementById("text-input");
    //  var textInputt = document.getElementById("text-inputt");
      var pathDropdown = document.getElementById("path-dropdown");

      if (element.value === "text") {
        textInput.style.display = "block";
     //   textInputt.style.display = "block";
        pathDropdown.style.display = "none";
      } else if (element.value === "path") {
        textInput.style.display = "none";
      //  textInputt.style.display = "none";
        pathDropdown.style.display = "block";
      }
    }
	
  </script>
    <script>
	var citis = document.getElementById("city");
var districts = document.getElementById("district");
var wards = document.getElementById("ward");
var Parameter = {
  url: "../assets/json/Data_DiaGioiHanhChinhVN.json", 
  method: "GET", 
  responseType: "application/json", 
};
var promise = axios(Parameter);
promise.then(function (result) {
  renderCity(result.data);
});

function renderCity(data) {
  for (const x of data) {
	var opt = document.createElement('option');
	 opt.value = x.Name;
	 opt.text = x.Name;
	 opt.setAttribute('data-id', x.Id);
	 citis.options.add(opt);
  }
  citis.onchange = function () {
    district.length = 1;
    ward.length = 1;
    if(this.options[this.selectedIndex].dataset.id != ""){
      const result = data.filter(n => n.Id === this.options[this.selectedIndex].dataset.id);

      for (const k of result[0].Districts) {
		var opt = document.createElement('option');
		 opt.value = k.Name;
		 opt.text = k.Name;
		 opt.setAttribute('data-id', k.Id);
		 district.options.add(opt);
      }
    }
  };
  district.onchange = function () {
    ward.length = 1;
    const dataCity = data.filter((n) => n.Id === citis.options[citis.selectedIndex].dataset.id);
    if (this.options[this.selectedIndex].dataset.id != "") {
      const dataWards = dataCity[0].Districts.filter(n => n.Id === this.options[this.selectedIndex].dataset.id)[0].Wards;

      for (const w of dataWards) {
		var opt = document.createElement('option');
		 opt.value = w.Name;
		 opt.text = w.Name;
		 opt.setAttribute('data-id', w.Id);
		 wards.options.add(opt);
      }
    }
  };
}
//Xóa Nội Dung Trong Thẻ Textarea
        function clearTextareajsg() {
            document.getElementById('jsonTextareaGoogleCloud').value = '';
        }
		        function clearTextareajsgCLOUD() {
            document.getElementById('jsonTextareaGoogleCloudTTS').value = '';
        }
/*
//Đọc IP Ra Thông Báo
        function updateTextip() {
            var checkbox = document.getElementById("myCheckboxip");
            var docIp = "<?php echo $serverIP; ?>";
            var text = "" + (checkbox.checked ? docIp : "");
            document.getElementById("myTextip").textContent = text;
        }
		*/
	</script>
	  
</body>
</html>
