<?php
// Code By: Vũ Tuyển
// Facebook: https://www.facebook.com/TWFyaW9uMDAx
include "../Configuration.php";
?>

<?php
	//Chmod sét full quyền
if (isset($_POST['set_full_quyen'])) {
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die($E_rror_HOST);}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die($E_rror);}
$stream1 = ssh2_exec($connection, "sudo chmod -R 0777 $Path_Vietbot_src");
$stream2 = ssh2_exec($connection, "sudo chown -R pi:pi $Path_Vietbot_src");
stream_set_blocking($stream1, true); 
stream_set_blocking($stream2, true); 
$stream_out1 = ssh2_fetch_stream($stream1, SSH2_STREAM_STDIO); 
$stream_out2 = ssh2_fetch_stream($stream2, SSH2_STREAM_STDIO); 
stream_get_contents($stream_out1); 
stream_get_contents($stream_out2); 
header("Location: $PHP_SELF"); exit;
}
// Đường dẫn đến thư mục "Backup_Config"
$backupDirz = "Backup_Skill/";
$fileLists = glob($backupDirz . "*.json");
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['selectedFile']) && !empty($_GET['selectedFile'])) {
            $selectedFile = $_GET['selectedFile'];
            $skillFile = $DuognDanThuMucJson . "/skill.json";
            $fileContent = file_get_contents($selectedFile);
            file_put_contents($skillFile, $fileContent);
			header("Location: ".$PHP_SELF);
            exit();
            //echo "Đã Khôi Phục File config.json Được Chọn Thành Công.";
        }}
	//END Khôi Phục File skill	

//////////////////////////Khôi Phục Gốc Skill.Json
if (isset($_POST['restore_skill_json'])) {
$sourceFile = $DuognDanUI_HTML.'/assets/json/skill.json';
$destinationFile = $DuognDanThuMucJson.'/skill.json';
// Kiểm tra xem tệp nguồn tồn tại
if (file_exists($sourceFile)) {
	shell_exec("rm $destinationFile");
    // Thực hiện sao chép bằng lệnh cp
    $command = "cp $sourceFile $destinationFile";
    $output = shell_exec($command);
	shell_exec("chmod 0777 $destinationFile");
    // Kiểm tra kết quả
    if ($output === null) {
        echo "<center>Khôi Phục Gốc <b>skill.json</b> thành công!</center>";
    } else {
        echo "<center>Đã xảy ra lỗi khi khôi phục gốc <b>skill.json</b> : $output</center>";
    }
} else {
    echo "<center>Tệp gốc <b>skill.json</b> không tồn tại!</center>";
}
header("Location: $PHP_SELF"); exit;
}
?>

<?php	
if (isset($Web_UI_Login) && $Web_UI_Login === true) {
	if (!isset($_SESSION['root_id'])) {
		echo "<br/><center><h1>Có Vẻ Như Bạn Chưa Đăng Nhập!<br/><br>
		- Nếu Bạn Đã Đăng Nhập, Hãy Nhấn Vào Nút Dưới<br/><br/><a href='$PHP_SELF'><button type='button' class='btn btn-danger'>Tải Lại</button></a></h1>
		</center>";
		exit();
}
	include "Skill_.php";
	
	} else {
	   
	   include "Skill_.php";
	   
	   
	}
?>	
	
