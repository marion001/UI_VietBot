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
// Khởi tạo biến để lưu output
$output = '';

function picovoice_version($noi_dung_tep, $ten_lop, $ten_phuong_thuc) {
    try {
        $dong = explode("\n", $noi_dung_tep);
        $trong_lop = $noi_dung_lop = $trong_phuong_thuc = $noi_dung_phuong_thuc = $gia_tri_return = false;
        foreach ($dong as $line) {
            $noi_dung_lop .= $line;
            if (strpos($line, "class {$ten_lop}(") !== false) {
                $trong_lop = true;
            }
            if ($trong_lop && strpos($line, "def {$ten_phuong_thuc}(") !== false) {
                $trong_phuong_thuc = true;
            }
            if ($trong_phuong_thuc) {
                $noi_dung_phuong_thuc .= $line;
                if (strpos($line, 'return ') !== false) {
                    $gia_tri_return = trim(trim(str_replace("'", "", explode('return ', $line)[1])));
                    break;
                }
            }
        }
        return $gia_tri_return;
    } catch (Exception $e) {
        return "Lỗi xử lý tệp.";
    }
}
function porcupine_version($file_path, $skip_count = 9) {
    try {
        $file = fopen($file_path, 'r');
        // Đọc và bỏ qua 9 ký tự đầu
        fread($file, $skip_count);
        // Đọc 15 ký tự tiếp theo
        $next_14_characters = fread($file, 5);
        fclose($file);
        return $next_14_characters;
    } catch (Exception $e) {
        return "File not found.";
    }
}

//Command
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commandd'])) {
	$commandnd = @$_POST['commandnd'];

	if (empty($commandnd)) {
            $output .= "$GET_current_USER@$HostName:$ ~> Hãy Nhập Lệnh Cần Thực Thi";
        }
else {
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream = ssh2_exec($connection, $commandnd);
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~ $ $commandnd\n";
$output .=  stream_get_contents($stream_out);
}
}

if (isset($_POST['auto_start'])) {
$CMD = "systemctl --user start vietbot.service";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream = ssh2_exec($connection, $CMD);
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~ $ $CMD\n";
$output .=  stream_get_contents($stream_out);
}

if (isset($_POST['auto_stop'])) {
$CMD = "systemctl --user stop vietbot.service";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream = ssh2_exec($connection, $CMD);
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~ $ $CMD\n";
$output .=  stream_get_contents($stream_out);
}

if (isset($_POST['auto_enable'])) {
$CMD = "systemctl --user enable vietbot.service";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream = ssh2_exec($connection, $CMD);
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~ $ $CMD\n";
$output .=  stream_get_contents($stream_out);
}

if (isset($_POST['auto_disable'])) {
$CMD = "systemctl --user disable vietbot.service";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream = ssh2_exec($connection, $CMD);
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~ $ $CMD\n";
$output .=  stream_get_contents($stream_out);
}

if (isset($_POST['auto_status'])) {
$CMD = "systemctl --user status vietbot.service";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream = ssh2_exec($connection, $CMD);
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~ $ $CMD\n";
$output .=  stream_get_contents($stream_out);
}

if (isset($_POST['auto_restart'])) {
$CMD = "systemctl --user restart vietbot.service";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream = ssh2_exec($connection, $CMD);
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~ $ $CMD\n";
$output .=  stream_get_contents($stream_out);
}


if (isset($_POST['config_auto'])) {
// Đường dẫn đến file service
$serviceFilePath = "{$VietBot_Offline_Path}resource/vietbot.service";

// Nội dung của file service với biến
$serviceContent = <<<EOD
[Unit]
Description=VBot_Offline

[Service]
# Khởi chạy ứng dụng Python VBot_Offline
ExecStart=/usr/bin/python3.9 {$VBot_Offline}Start.py
WorkingDirectory=$VBot_Offline

# Ghi log ra các file log sau khi ứng dụng khởi chạy
#StandardOutput=append:{$VBot_Offline}resource/log/service_log.log
#StandardError=append:{$VBot_Offline}resource/log/service_error.log

# Tự động khởi động lại service nếu bị lỗi
Restart=always

[Install]
WantedBy=default.target
EOD;

// Tạo hoặc ghi đè file service
file_put_contents($serviceFilePath, $serviceContent);
$CMD1 = "cp {$VietBot_Offline_Path}resource/vietbot.service /home/$GET_current_USER/.config/systemd/user/vietbot.service";
$CMD2 = "sudo chmod 0777 {$VietBot_Offline_Path}resource/vietbot.service";
$CMD3 = "ln -s /home/$GET_current_USER/.config/systemd/user/vietbot.service /home/$GET_current_USER/.config/systemd/user/default.target.wants/vietbot.service";
$CMD4 = "sudo systemctl daemon-reload";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream1 = ssh2_exec($connection, $CMD1);
$stream2 = ssh2_exec($connection, $CMD2);
$stream3 = ssh2_exec($connection, $CMD3);
$stream4 = ssh2_exec($connection, $CMD4);
stream_set_blocking($stream1, true); 
stream_set_blocking($stream2, true); 
stream_set_blocking($stream3, true); 
stream_set_blocking($stream4, true); 
$stream_out1 = ssh2_fetch_stream($stream1, SSH2_STREAM_STDIO); 
$stream_out2 = ssh2_fetch_stream($stream2, SSH2_STREAM_STDIO); 
$stream_out3 = ssh2_fetch_stream($stream3, SSH2_STREAM_STDIO); 
$stream_out4 = ssh2_fetch_stream($stream4, SSH2_STREAM_STDIO); 
$output = "$GET_current_USER@$HostName:~ $ \n$serviceContent\n\n";
$output .= "$GET_current_USER@$HostName:~ $ $CMD1\n";
$output .= stream_get_contents($stream_out1);
$output .= "$GET_current_USER@$HostName:~ $ $CMD2\n";
$output .= stream_get_contents($stream_out2);
$output .= "$GET_current_USER@$HostName:~ $ $CMD3\n";
$output .= stream_get_contents($stream_out3);
$output .= "$GET_current_USER@$HostName:~ $ $CMD4\n";
$output .= stream_get_contents($stream_out4);
}


if (isset($_POST['apache_restart'])) {
$CMD = "sudo systemctl restart apache2.service";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream = ssh2_exec($connection, $CMD);
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~ $ $CMD\n";
$output .=  stream_get_contents($stream_out);
}

if (isset($_POST['reboot_os'])) {
$CMD = "sudo reboot";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream = ssh2_exec($connection, $CMD);
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~ $ $CMD\n";
$output .=  stream_get_contents($stream_out);
}

if (isset($_POST['chmod_vietbot'])) {
$CMD1 = "sudo chmod -R 0777 $VietBot_Offline_Path";
#$CMD2 = "sudo chmod -R 0777 $HTML_Vietbot_Offline";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream1 = ssh2_exec($connection, $CMD1);
#$stream2 = ssh2_exec($connection, $CMD2);
stream_set_blocking($stream1, true); 
#stream_set_blocking($stream2, true); 
$stream_out1 = ssh2_fetch_stream($stream1, SSH2_STREAM_STDIO); 
#$stream_out2 = ssh2_fetch_stream($stream2, SSH2_STREAM_STDIO); 
$output = "$GET_current_USER@$HostName:~ $ $CMD1\n";
#$output .= "$GET_current_USER@$HostName:~ $ $CMD2\n";
$output .= stream_get_contents($stream_out1); 
#$output .= stream_get_contents($stream_out2); 
}

if (isset($_POST['owner_vietbot'])) {
$CMD1 = "sudo chown -R $GET_current_USER:$GET_current_USER $VietBot_Offline_Path";
#$CMD2 = "sudo chown -R $GET_current_USER:$GET_current_USER $HTML_Vietbot_Offline";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream1 = ssh2_exec($connection, $CMD1);
#$stream2 = ssh2_exec($connection, $CMD2);
stream_set_blocking($stream1, true); 
#stream_set_blocking($stream2, true); 
$stream_out1 = ssh2_fetch_stream($stream1, SSH2_STREAM_STDIO); 
#$stream_out2 = ssh2_fetch_stream($stream2, SSH2_STREAM_STDIO); 
$output = "$GET_current_USER@$HostName:~ $ $CMD1\n";
#$output .= "$GET_current_USER@$HostName:~ $ $CMD2\n";
$output .= stream_get_contents($stream_out1); 
#$output .= stream_get_contents($stream_out2); 
}

if (isset($_POST['ifconfig_os'])) {
$CMD = "ifconfig";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream = ssh2_exec($connection, $CMD);
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~ $ $CMD\n";
$output .=  stream_get_contents($stream_out);
}

if (isset($_POST['lscpu_os'])) {
$CMD = "lscpu";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream = ssh2_exec($connection, $CMD);
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~ $ $CMD\n";
$output .=  stream_get_contents($stream_out);
}

if (isset($_POST['hostnamectl_os'])) {
$CMD = "hostnamectl";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream = ssh2_exec($connection, $CMD);
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~ $ $CMD\n";
$output .=  stream_get_contents($stream_out);
}

if (isset($_POST['kiem_tra_bo_nho'])) {
$CMD = "df -hm";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream = ssh2_exec($connection, $CMD);
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~ $ $CMD\n";
$output .=  stream_get_contents($stream_out);
}

if (isset($_POST['kiem_tra_dung_luong'])) {
$CMD = "free -mh";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream = ssh2_exec($connection, $CMD);
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~ $ $CMD\n";
$output .=  stream_get_contents($stream_out);
}

if (isset($_POST['pvporcupine_info'])) {
$CMD = "pip show pvporcupine";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream = ssh2_exec($connection, $CMD);
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~ $ $CMD\n";
$output .=  stream_get_contents($stream_out);
}

if (isset($_POST['picovoice_info'])) {
$CMD = "pip show picovoice";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream = ssh2_exec($connection, $CMD);
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~ $ $CMD\n";
$output .=  stream_get_contents($stream_out);
}

if (isset($_POST['pip_show_all_lib'])) {
$CMD = "pip list";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream = ssh2_exec($connection, $CMD);
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~ $ $CMD\n";
$output .=  stream_get_contents($stream_out);
}


//check_version_picovoice_porcupine
if (isset($_POST['check_version_picovoice_porcupine'])) {
$remotePath = "/home/$GET_current_USER/.local/lib/python3.9/site-packages/";
$pattern = '/^pvporcupine-(\d+\.\d+\.\d+)\.dist-info$/m';
// Thực hiện lệnh ls để lấy danh sách thư mục
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream = ssh2_exec($connection, "ls $remotePath");
stream_set_blocking($stream, true);
$outputhh = stream_get_contents($stream);
fclose($stream);
$output .= "$GET_current_USER@$HostName:~ssh$:\n";
// Kiểm tra xem có thư mục nào khớp với biểu thức chính quy không
if (preg_match($pattern, $outputhh, $matches)) {
    $foundVersion = $matches[1];
    $output .= "Phiên bản Picovoice: $foundVersion\n";
} else {
    //echo "Không tìm thấy thư mục pvporcupine-X.X.X.dist-info.";
$path_picovoice = "/home/$GET_current_USER/.local/lib/python3.9/site-packages/picovoice/_picovoice.py";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream = ssh2_exec($connection, "cat $path_picovoice");
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output =  stream_get_contents($stream_out);
//echo $output;
$text_picovoice_version = picovoice_version($output, 'Picovoice', 'version');
$firstThreeCharspicovoice_version = substr($text_picovoice_version, 0, 3);
$output .= "Phiên bản Picovoice: $text_picovoice_version\n";
}

//Kiểm tra phiên bản porcupine hiện tại
 if ($Config['smart_config']['smart_wakeup']['hotword']['lang'] == 'vi') {
    $porcupine_check = $Config['smart_config']['smart_wakeup']['hotword']['library']['vi']['modelFilePath'];
} elseif ($Config['smart_config']['smart_wakeup']['hotword']['lang'] == 'eng') {
    $porcupine_check = $Config['smart_config']['smart_wakeup']['hotword']['library']['eng']['modelFilePath'];
}

$file_path = $VBot_Offline.'resource/picovoice/library/'.$porcupine_check;
$text_porcupine_version = porcupine_version($file_path);
$output .= "Phiên bản Porcupine: $text_porcupine_version";
}


if (isset($_POST['install_picovoice'])) {
$versions_picovoice_install = $_POST['versions_picovoice_install'];
if (empty($versions_picovoice_install)) {
    $output = "Picovoice:> Hãy chọn phiên bản picovoice cần cài đặt\n";
} else {
$CMD = "pip install picovoice==$versions_picovoice_install";
$connection = ssh2_connect($ssh_host, $ssh_port);
if (!$connection) {die("Không thể kết nối tới máy chủ SSH");}
if (!ssh2_auth_password($connection, $ssh_user, $ssh_password)) {die("Xác thực SSH không thành công.");}
$stream = ssh2_exec($connection, $CMD);
stream_set_blocking($stream, true); 
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO); 
$output = "$GET_current_USER@$HostName:~$ pip install picovoice==$versions_picovoice_install\n";
$output .= stream_get_contents($stream_out);
}
}

if (isset($_POST['install_porcupine'])) {
$destinationPath = $VBot_Offline.'resource/picovoice/library';
$versions_porcupine_install = $_POST['versions_porcupine_install'];
if (empty($versions_porcupine_install)) {
    $output .= "Porcupine:> Hãy chọn phiên bản Porcupine cần cài đặt\n";
} else {
$fileUrl = 'https://github.com/Picovoice/porcupine/archive/refs/tags/v'.$versions_porcupine_install.'.zip';
$fileContent = file_get_contents($fileUrl);
$filename = basename($fileUrl);
$destinationFile = $destinationPath . '/' . $filename;
file_put_contents($destinationFile, $fileContent);
chmod($destinationFile, 0777);
$output .= "Porcupine:> Phiên bản thư viện Porcupine (.pv) được cài đặt là: $versions_porcupine_install\n";

$fileNameZip = 'porcupine-'.$versions_porcupine_install.'/lib/common';
$zipFilePath = $destinationPath.'/v'.$versions_porcupine_install.'.zip'; // Đường dẫn đến file ZIP
$zip = new ZipArchive;
if ($zip->open($zipFilePath) === TRUE) {
    $fileNamesToCopy = ["$fileNameZip/porcupine_params.pv", "$fileNameZip/porcupine_params_vn.pv"];

    foreach ($fileNamesToCopy as $fileNameInZip) {
        // Kiểm tra xem file có tồn tại trong ZIP hay không
        $index = $zip->locateName($fileNameInZip);
        if ($index !== false) {
            // Đọc nội dung của file từ ZIP
            $fileContent = $zip->getFromIndex($index);
            // Đường dẫn đến thư mục đích
            $destinationFilee = $destinationPath . '/' . basename($fileNameInZip);
            // Ghi nội dung của file vào thư mục đích
            file_put_contents($destinationFilee, $fileContent);
            
            //$output .= 'Porcupine:> File '.basename($fileNameInZip).' đã được đưa vào thư mục lib có chứa tệp .pv | ';
        } else {
            $output .= 'Porcupine:> File '.basename($fileNameInZip). 'không tồn tại | ';
        }
    }
    $zip->close();
	shell_exec('rm ' . escapeshellarg($zipFilePath));
	$output .= 'Porcupine:> HÃY CHỌN LẠI NGÔN NGỮ HOTWORD VÀ LƯU CẤU HÌNH SAU ĐÓ KHỞI ĐỘNG LẠI VBot ĐỂ ÁP DỤNG.';
	
} else {
    $output .= 'Porcupine:> Lỗi không thể mở file thư viện Porcupine: v'.$versions_porcupine_install.'.zip \n';
}
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
      <h1>Dòng lệnh/Đầu cuối</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item" onclick="loading('show')"><a href="index.php">Trang chủ</a></li>
          <li class="breadcrumb-item active">Command/Terminal</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
<section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
			<form method="POST" action="">
<br/>
	<div class="row g-3 d-flex justify-content-center">
	<div class="col-auto">
<div class="btn-group">
<div class="dropdown">
          <button class="btn btn-danger dropdown-toggle rounded-pill" data-bs-toggle="dropdown" aria-expanded="false">
            VBot Auto Run
          </button>
          <ul class="dropdown-menu">
    <li><button onclick="loading('show')" class="dropdown-item text-danger" name="auto_start" type="submit" title="Chạy lại trương trình">Chạy</button></li>
    <li><button onclick="loading('show')" class="dropdown-item text-danger" name="auto_restart" type="submit" title="Tạm dừng trương trình đang chạy">Khởi động lại</button></li>
    <li><button onclick="loading('show')" class="dropdown-item text-danger" name="auto_stop" type="submit" title="Tạm dừng trương trình đang chạy">Dừng</button></li>
    <li><button onclick="loading('show')" class="dropdown-item text-danger" name="auto_status" type="submit" title="Tạm dừng trương trình đang chạy">Trạng thái</button></li>
    <li><button onclick="loading('show')" class="dropdown-item text-danger" name="auto_enable" type="submit" title="Tự động chạy trương trình khi hệ thống khởi động">Kích hoạt</button></li>
    <li><button onclick="loading('show')" class="dropdown-item text-danger" name="auto_disable" type="submit" title="Vô hiệu hóa trương trình, không cho tự động chạy">Vô hiệu</button></li>
    <li><button onclick="loading('show')" class="dropdown-item text-danger" name="config_auto" type="submit" title="Vô hiệu hóa trương trình, không cho tự động chạy">Cài đặt cấu hình Auto</button></li>
          </ul>
</div>
</div>

<div class="btn-group">
<div class="dropdown">
          <button class="btn btn-dark dropdown-toggle rounded-pill" data-bs-toggle="dropdown" aria-expanded="false">
            Hệ Thống
          </button>
          <ul class="dropdown-menu">
    <li><button onclick="loading('show')" class="dropdown-item text-danger" name="apache_restart" type="submit" title="Khởi động lại apache2">Restart Apache2</button></li>
    <li><button onclick="loading('show')" class="dropdown-item text-danger" name="reboot_os" type="submit" title="Khởi động lại hệ thống">Reboot OS</button></li>
    <li><button onclick="loading('show')" class="dropdown-item text-danger" name="chmod_vietbot" type="submit" title="Chmod VBot và UI HTML thành 0777">Chmod 0777</button></li>
    <li><button onclick="loading('show')" class="dropdown-item text-danger" name="owner_vietbot" type="submit" title="Thay đổi quyền sở hữu các file thành của người dùng SSH">Owner Change</button></li>
    <li><button onclick="loading('show')" class="dropdown-item text-danger" name="ifconfig_os" type="submit" title="Kiểm tra thông tin mạng">Thông tin mạng</button></li>
    <li><button onclick="loading('show')" class="dropdown-item text-danger" name="lscpu_os" type="submit" title="Kiểm tra thông CPU">Thông tin CPU</button></li>
    <li><button onclick="loading('show')" class="dropdown-item text-danger" name="hostnamectl_os" type="submit" title="Kiểm tra thông tin hệ điều hành">Thông tin OS</button></li>
    <li><button onclick="loading('show')" class="dropdown-item text-danger" name="kiem_tra_bo_nho" type="submit" title="Kiểm tra thông tin bộ nhớ">Thông tin bộ nhớ</button></li>
    <li><button onclick="loading('show')" class="dropdown-item text-danger" name="kiem_tra_dung_luong" type="submit" title="Kiểm tra thông tin dung lượng">Thông tin dung lượng</button></li>
          </ul>
</div>
</div>
<div class="btn-group">
<div class="dropdown">
          <button class="btn btn-success dropdown-toggle rounded-pill" data-bs-toggle="dropdown" aria-expanded="false">
            Thư Viện
          </button>
          <ul class="dropdown-menu">
  <li><button onclick="loading('show')" class="dropdown-item text-danger" name="pip_show_all_lib" type="submit" title="Liệt kê các thư viện đã cài bằng pip">pip show all lib</button></li>
		  <li><button onclick="loading('show')" class="dropdown-item text-danger" name="pvporcupine_info" type="submit" title="Kiểm tra thông tin thư viện pvporcupine">Thông tin pvporcupine</button></li>
		  <li><button onclick="loading('show')" class="dropdown-item text-danger" name="picovoice_info" type="submit" title="Kiểm tra thông tin thư viện picovoice">Thông tin picovoice</button></li>
  
		</ul>
</div>
</div>
</form>
<!-- 
<div class="btn-group">
<form method="POST" action="">
<button class="btn btn-primary rounded-pill" name="setting_apache2" type="button" title="Cấu hình apache2">Cấu hình apache2</button><br/>
<div class="input-group mb-3">
<input required="" class="form-control border-success" type="text" name="setting_apache2_path" id="setting_apache2_path" placeholder="/home/pi/VBot_Offline/html" title="Ví dụ: /home/pi/VBot_Offline/html">
<div class="invalid-feedback">Cần nhập đường dẫn path cần cấu hình apache2</div>
<button class="btn btn-success border-success" type="submit">Cấu hình</button>
</div>
</form>  
</div>
-->
</div>
</div>



	<hr/>
	
  <form method="POST" action="">
	<div class="row g-3 d-flex justify-content-center">
	<div class="col-auto">
	<div class="input-group"><span class="input-group-text text-success">Nâng/Hạ Cấp Picovoice</span>
    <select class="btn btn-success dropdown-toggle" data-toggle="dropdown" name="versions_picovoice_install">
	<option value="" selected>Chọn Phiên Bản</option>
 <?php
$url = 'https://pypi.org/rss/project/picovoice/releases.xml';
// Lấy nội dung từ RSS feed
$xml_content = file_get_contents($url);
// Kiểm tra xem có dữ liệu hay không
if ($xml_content) {
    // Tìm vị trí của các thẻ <item>
    $start_pos = strpos($xml_content, '<item>');
    $end_pos = strpos($xml_content, '</item>');
    // Tạo một mảng để lưu trữ các phiên bản
    $versions = [];
    // Lặp qua từng mục và thêm thông tin vào mảng
    while ($start_pos !== false && $end_pos !== false) {
        $item_xml = substr($xml_content, $start_pos, $end_pos - $start_pos + strlen('</item>'));
        // Trích xuất thông tin từ mỗi mục
        preg_match('/<title>(.*?)<\/title>/', $item_xml, $title_match);
        // Thêm phiên bản vào mảng
        $versions[] = $title_match[1];
        // Di chuyển đến mục tiếp theo
        $start_pos = strpos($xml_content, '<item>', $end_pos);
        $end_pos = strpos($xml_content, '</item>', $start_pos);
    }
    // Hiển thị dropdown list
    foreach ($versions as $version) {
        echo '<option value="' . $version . '">Picovoice: ' . $version . '</option>';
    }
} else {
    echo "<option value=''>Phiên bản: -----</option>";
}
?>
 </select></div> 
 
 </div><div class="col-auto"> <div class="input-group-append">
 <button class="btn btn-danger" onclick="loading('show')" name="install_picovoice" title="Cài đặt Picovoice" type="submit">Cài Đặt Picovoice</button>
 <button type='submit' onclick="loading('show')" name='check_version_picovoice_porcupine' class='btn btn-primary' title='Kiểm tra phiên bản Picovoice và Porcupine'>Kiểm tra phiên bản</button>
 </div> 
 </div> 
 </div>
 <br/>
 
 <div class="row g-3 d-flex justify-content-center">
 <div class="col-auto">
 <div class="input-group">
 <span class="input-group-text text-success">Thư Viện Porcupine (.pv)</span>
     <select class="btn btn-success dropdown-toggle" data-toggle="dropdown" id="inputGroupSelect04" name="versions_porcupine_install">
	<option value="" selected>Chọn Phiên Bản</option>
 <?php
 $uniqueVersions = [];
    foreach ($versions as $versionpv) {
    // Lấy 3 ký tự đầu tiên của chuỗi
    $versionFirstThreeChars = substr($versionpv, 0, 3);
    
    // Kiểm tra xem giá trị đã xuất hiện chưa
    if (!in_array($versionFirstThreeChars, $uniqueVersions)) {
        // Nếu chưa xuất hiện, thêm vào mảng và hiển thị
        $uniqueVersions[] = $versionFirstThreeChars;
        echo '<option value="' . $versionFirstThreeChars . '">Porcupine: ' . $versionFirstThreeChars . '</option>';
    }
   }
?>
 </select>
 </div>
 </div>
 <div class="col-auto"> <div class="input-group-append">
 <button class="btn btn-danger" onclick="loading('show')" name="install_porcupine" title="Cài đặt Porcupine" type="submit">Cài Đặt Porcupine</button>
 </div> 
 </div> 
 </div>
	</form>
	
	
	
	
	
		<hr/>
	<form method="POST" action="">
				
<div class="input-group mb-3">
<span class="input-group-text border-success" id="basic-addon1"><i class="bi bi-terminal-fill"></i></span>
  <input type="text" class="form-control border-success" name="commandnd" placeholder="Nhập dòng lệnh cần thực hiện">
    <button class="btn btn-success border-success" onclick="loading('show')" name="commandd" type="submit">Command</button>
</div>
  <div class="form-group">
    <textarea class="form-control border-success text-info bg-dark" id="textarea_log_command" rows="14"><?php echo $output; ?></textarea>
  </div>
	</form>
	</div>
	</div>
	</div>
	</div>
	</section>
</main>
<!-- End #main -->
<!-- ======= Footer ======= -->
<?php
include 'html_footer.php';
?>
<!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Nghe thử file âm thanh -->
<!-- <audio id="audioPlayer" style="display: none;" controls></audio> -->

  <!-- Template Main JS File -->
<?php
include 'html_js.php';
?>


</body>

</html>