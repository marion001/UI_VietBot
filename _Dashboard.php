
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

$messages = [];
$Version_Vietbot_WebUI = null;
$Version_Vietbot_WebUI_filePath = $HTML_Vietbot_Offline.'/version.json';
#echo $Version_Vietbot_WebUI_filePath;
#Đọc nội dung file version.json Vietbot
if (file_exists($Version_Vietbot_WebUI_filePath)) {
    $Version_Vietbot_WebUI = json_decode(file_get_contents($Version_Vietbot_WebUI_filePath), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $messages[] = 'Có lỗi xảy ra khi giải mã JSON: ' . json_last_error_msg();
        $Version_Vietbot_WebUI = null; // Đặt dữ liệu thành null nếu có lỗi
    }
} else {
    $messages[] = 'Tệp JSON không tồn tại tại đường dẫn: ' . $Version_Vietbot_WebUI_filePath;
    $Version_Vietbot_WebUI = null; // Đặt dữ liệu thành null nếu tệp không tồn tại
}



#Giới hạn file backup
$Limit_Backup_Files = $Config['web_interface']['backup_upgrade']['web_interface']['backup']['limit_backup_files'];
#ĐƯờng dẫn lưu file backup WEBUI Vietbot
$Backup_Dir_Save_Interface_Vietbot = $Config['web_interface']['backup_upgrade']['web_interface']['backup']['backup_path'];

// Các thư mục cần kiểm tra và tạo Download_Path và  Extract_Path
$directoriessss = [$Download_Path, $Extract_Path, $Backup_Dir_Save_Interface_Vietbot];


#Xóa tất cả các file, thư mục và thư mục con
function deleteDirectory($dir) {
	global $messages;
    // Kiểm tra xem thư mục có tồn tại không
    if (!is_dir($dir)) {
		$messages[] = "<font color=red>Thư mục $dir không tồn tại để xóa dữ liệu</font>";
        return false;
    }
    // Mở thư mục
    $files = scandir($dir);
    foreach ($files as $file) {
        // Bỏ qua các thư mục hiện tại (.) và thư mục cha (..)
        if ($file != '.' && $file != '..') {
            //$filePath = $dir . '/' . $file;
			$filePath = rtrim($dir, '/') . '/' . $file; // loại bỏ dấu / ở cuối
            // Nếu là thư mục, gọi đệ quy
            if (is_dir($filePath)) {
                deleteDirectory($filePath);
            } else {
                // Xóa tệp
                unlink($filePath);
                $messages[] = "<font color=red>- Đã xóa tệp: </font> <font color=blue>$filePath</font>";
            }
        }
    }
    // Cuối cùng, xóa thư mục
    rmdir($dir);
    $messages[] = "<font color=red>- Đã xóa thư mục: </font> <font color=blue>$dir</font>";
    return true;
}

#hàm coppy file, thư mục, có lựa chọn giữ lại tệp , thư mục không cho sao chép
function copyFiles($source, $destination, $keepList = []) {
    global $messages;
    // Kiểm tra xem thư mục nguồn có tồn tại không
    if (!is_dir($source)) {
        $messages[] = "<font color=red>- Thư mục nguồn '$source' không tồn tại</font>";
        return false;
    }
    // Tạo thư mục đích nếu chưa tồn tại
    if (!is_dir($destination)) {
        mkdir($destination, 0777, true);
    }
    // Mở thư mục nguồn
    $dir = opendir($source);
    while (($file = readdir($dir)) !== false) {
        // Bỏ qua các thư mục hiện tại (.) và thư mục cha (..)
        if ($file != '.' && $file != '..') {
            // Đường dẫn đầy đủ của tệp hoặc thư mục
            $srcPath = rtrim($source, '/') . '/' . $file;
            $destPath = rtrim($destination, '/') . '/' . $file;
            // Bỏ qua nếu file hoặc thư mục nằm trong danh sách cần giữ lại
            if (in_array($file, $keepList)) {
                $messages[] = "<font color=orange>- Bỏ qua tệp/thư mục: </font><font color=blue><b>$file</b></font>";
                continue;
            }
            // Nếu là thư mục, gọi đệ quy
            if (is_dir($srcPath)) {
                copyFiles($srcPath, $destPath, $keepList);
            } else {
                // Sao chép tệp
                if (copy($srcPath, $destPath)) {
                    $messages[] = "<font color=blue>- Đã sao chép tệp: </font><font color=blue><b>" . basename($srcPath) . "</b></font>";
                } else {
                    $messages[] = "<font color=red>- Không thể sao chép tệp <b>'$srcPath'</b> đến <b>'$destPath'</b></font>";
                }
            }
        }
    }
    closedir($dir);
    return true; 
}

#Tạo Thư mục
function createDirectory($directory) {
	global $messages;
    if (!is_dir($directory)) {
        if (mkdir($directory, 0777, true)) {
			chmod($directory, 0777);
            $messages[] = "<font color=green>- Thư mục '$directory' đã được tạo thành công và quyền truy cập đã được đặt là 0777</font>";
        } else {
            $messages[] = "<font color=red>- Không thể tạo thư mục '$directory'.</font>";
        }
    }
}


#Giải nén tệp .tar.gz
function extractTarGz($tarFilePath, $extractTo) {
	global $messages;
    // Kiểm tra xem tệp .tar.gz có tồn tại không
    if (!file_exists($tarFilePath)) {
		$messages[] = "<font color=red>- Tệp Sao Lưu: '$tarFilePath' không tồn tại</font>";
		// Tệp không tồn tại
        return false;
    }
    // Thực hiện lệnh giải nén
    $command = "tar -xzf " . escapeshellarg($tarFilePath) . " -C " . escapeshellarg($extractTo);
    exec($command, $output, $returnVar);
    // Kiểm tra kết quả
    if ($returnVar === 0) {
		// Giải nén thành công
        return true;
    } else {
		// Giải nén thất bại
        return false;
    }
}


#Tải xuống repo git, không dùng lệnh git clone
function downloadGitRepoAsNamedZip($repoUrl, $destinationDir) {
	global $messages;
	$messages[] = "<font color=green>- Đang tiến hành tải xuống bản cập nhật...</font>";
    // Lấy tên repository từ URL
    $repoName = basename(parse_url($repoUrl, PHP_URL_PATH));
    $zipFile = $destinationDir . "/" . $repoName . ".zip";
    // URL tải file ZIP từ GitHub
	// Hoặc thay 'main' bằng nhánh mong muốn
    $zipUrl = rtrim($repoUrl, '/') . "/archive/refs/heads/main.zip";
    // Tạo thư mục đích nếu chưa tồn tại
    if (!is_dir($destinationDir)) {
        mkdir($destinationDir, 0777, true);
    }
    // Tải tệp ZIP về và lưu với tên mới
    file_put_contents($zipFile, fopen($zipUrl, 'r'));
	chmod($zipFile, 0777);
    // Giải nén tệp ZIP
    $zip = new ZipArchive;
	$messages[] = "<font color=green>- Tải xuống thành công, đang tiến hành giải nén dữ liệu...</font>";
    if ($zip->open($zipFile) === TRUE) {
		//Tên  Thư mục giải nén sẽ có dạng repo-main
        $extractedFolder = $destinationDir . "/" . $repoName . "-main";
        $zip->extractTo($destinationDir);
        $zip->close();
        // Xóa tệp ZIP sau khi giải nén
        unlink($zipFile);
        // Đặt quyền cho thư mục đã giải nén
        chmod($extractedFolder, 0777);
		//$messages[] = "Giải nén dữ liệu thành công, tiến hành nâng cấp...";
        return $extractedFolder;
    } else {
        $messages[] = "Có Lỗi Xảy Ra, không thể giải nén được giữ liệu đã tải xuống, đã dừng tiến trình";
        return null;
    }
}




#function Tạo bản sao lưu
function backup_data($Exclude_Files_Folder, $Exclude_File_Format){

global $Config, $messages, $HTML_Vietbot_Offline, $Limit_Backup_Files, $Backup_Dir_Save_Interface_Vietbot, $Version_Vietbot_WebUI;

// Kiểm tra nếu thư mục chưa tồn tại
if (!is_dir($Backup_Dir_Save_Interface_Vietbot)) {
    // Tạo thư mục với quyền 0777
    if (mkdir($Backup_Dir_Save_Interface_Vietbot, 0777, true)) {
        $messages[] = "<font color=blue>- Thư mục đã được tạo: <b>$Backup_Dir_Save_Interface_Vietbot</b></font>";
        chmod($Backup_Dir_Save_Interface_Vietbot, 0777);
    } else {
        $messages[] = "<font color=red>- Lỗi, Không thể tạo thư mục: <b>$Backup_Dir_Save_Interface_Vietbot</b></font>";
		return null;
    }
}

//Chuyển dấu / thành dấu - ở file Version.json
$Version_Vietbot_Program_releaseDate = str_replace([' ', '*', '/'], ['_', '_', '-'], $Version_Vietbot_WebUI['ui_version']['latest']);

// Đường dẫn file backup

# Tên file Backup
$Backup_File_Name = $Backup_Dir_Save_Interface_Vietbot . '/Vietbot_Interface_' . date('dmY_His') . '_'.$Version_Vietbot_Program_releaseDate.'.tar.gz';

// Tạo lệnh để nén thư mục
$tarCommand = "tar -czvf " . escapeshellarg($Backup_File_Name) . " -C " . escapeshellarg($HTML_Vietbot_Offline.'/');
// Thêm các tùy chọn bỏ qua cho từng file trong mảng
foreach ($Exclude_Files_Folder as $item) {
    $tarCommand .= " --exclude=" . escapeshellarg($item);
}
// Thêm các đuôi file cần loại bỏ vào lệnh tar
foreach ($Exclude_File_Format as $ext) {
    // Tạo một lệnh exclude cho đuôi file
    $tarCommand .= " --exclude=*" . escapeshellarg($ext);
}

// Thêm tên thư mục cần nén (dùng dấu chấm để nén toàn bộ nội dung thư mục)
$tarCommand .= " . --warning=all 2>&1";
// Thực thi lệnh tar
exec($tarCommand, $output, $returnCode);
// Kiểm tra kết quả
if ($returnCode === 0) {
    chmod($Backup_File_Name, 0777); // Đặt quyền cho file backup
    $messages[] = "<font color=green>- Tạo bản sao lưu chương trình Vietbot thành công:</font> <font color=blue><a title='Tải Xuống file backup: ".basename($Backup_File_Name)."' onclick=\"downloadFile('".$HTML_Vietbot_Offline."/".$Backup_File_Name."')\">".basename($Backup_File_Name)."</a></font> <a title='Tải Xuống file backup: ".basename($Backup_File_Name)."' onclick=\"downloadFile('".$HTML_Vietbot_Offline."/".$Backup_File_Name."')\"><font color=green>Tải Xuống</font></a>";
    // Hiển thị các file và thư mục đã nén
    $messages[] = "<br/>- Các file và thư mục đã được sao lưu và đóng gói vào tệp <b>".basename($Backup_File_Name)."</b>";
    foreach ($output as $line) {
        $messages[] = "<font color=blue>".$line."</font>";
    }
    // Kiểm tra và hiển thị các file và thư mục bị bỏ qua
    $messages[] = "<br/><font color=red><b>- Các file và thư mục không được sao lưu:</b></font>";
    foreach ($Exclude_Files_Folder as $item) {
        $messages[] = "<font color=red>- Thư mục: <b>".$item."</b> không được sao lưu</font>";
    }
    foreach ($Exclude_File_Format as $ext) {
        $messages[] = "<font color=red>- Tệp có phần mở rộng: <b>'$ext'</b> không được sao lưu</font>";
    }
    // Xóa các file cũ nếu số lượng tệp tin sao lưu vượt quá giới hạn
    $Backup_File_Names = glob($Backup_Dir_Save_Interface_Vietbot . '/*.tar.gz');
    $numBackupFiles = count($Backup_File_Names);
    if ($numBackupFiles > $Limit_Backup_Files) {
        // Sắp xếp tệp tin sao lưu theo thời gian tăng dần
        usort($Backup_File_Names, function ($a, $b) {
            return filemtime($a) - filemtime($b);
        });
        // Xóa các tệp tin cũ nhất cho đến khi số lượng tệp tin sao lưu đạt đến giới hạn
        $filesToDelete = array_slice($Backup_File_Names, 0, $numBackupFiles - $Limit_Backup_Files);
        foreach ($filesToDelete as $file) {
            unlink($file);
			$messages[] = "<br/>- Số lượng tệp tin sao lưu trên hệ thống vượt quá giới hạn là: <b>$Limit_Backup_Files</b>, đã xóa file cũ nhất: <font color=red><b>".basename($file)."</b></font>";
        }
    }
	return $Backup_File_Name;
} else {
    $messages[] = '<br/></font color=red>- Lỗi khi nén thư mục. Mã lỗi: ' . $returnCode.'</font>';
	// In chi tiết thông báo lỗi (nếu có)
    $messages[] = $output;
	return null;
}
#End Function tạo bản sao lưu
}






#Sao Lưu chương trình Vietbot, chỉ tạo file backup
if (isset($_POST['Backup_Interface'])) {
$messages = [];
$Exclude_Files_Folder = isset($_POST['exclude_files_folder']) ? $_POST['exclude_files_folder'] : [];
$Exclude_File_Format = isset($_POST['exclude_file_format']) ? $_POST['exclude_file_format'] : [];

$messages[] =  "- Đang tiến hành sao lưu dữ liệu Giao Diện Vietbot";
$FileName_Backup_Vietbot = backup_data($Exclude_Files_Folder, $Exclude_File_Format);
if (!is_null($FileName_Backup_Vietbot)) {
$messages[] = "<font color=green>- Hoàn thành Sao Lưu Chương Trình Vietbot Trên Hệ Thống: <b>" .$FileName_Backup_Vietbot."</b></font>";
}
}

#Khôi phục dữ liệu từ bản sao lưu được tải lên
if (isset($_POST['upload_and_restore'])) {
$messages = [];
	$messages[] =  "<font color=green>- Đang tiến hành tải lên bản khôi phục dữ liệu</font>";
    $uploadOk = 1;
    // Kiểm tra xem tệp có được gửi không
    if (isset($_FILES["fileToUpload"])) {
		
	// Kiểm tra và tạo từng thư mục
	foreach ($directoriessss as $directory) {
		createDirectory($directory);
	}
		
        $targetFile = $Download_Path . '/' . basename($_FILES["fileToUpload"]["name"]);
        $fileName = basename($_FILES["fileToUpload"]["name"]);
        // Kiểm tra xem tệp có phải là .tar.gz không
		if (!preg_match('/\.tar\.gz$/', $fileName) || !preg_match('/^Vietbot_Interface/', $fileName)) {
		$messages[] = "<font color=red>- Chỉ chấp nhận tệp .tar.gz, dành cho Vietbot_Interface, và được Giao Diện tạo ra bản sao lưu đó</font>";
		$uploadOk = 0;
		}
        // Kiểm tra xem $uploadOk có bằng 0 không
        if ($uploadOk == 0) {
				deleteDirectory($Extract_Path);
				deleteDirectory($Download_Path);
            $messages[] = "<font color=red>- Tệp sao lưu không được tải lên</font>";
        } else {
            // Di chuyển tệp vào thư mục đích
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
                $messages[] = "<font color=green>- Tệp <b>" . htmlspecialchars($fileName) . "</b> đã được tải lên thành công</font>";
	// Gọi hàm để giải nén
	if (extractTarGz($directory_path.'/'.$targetFile, $Extract_Path)) {
		$Extract_Path_OK = $directory_path.'/'.$Extract_Path.'/';
		$messages[] = "<font color=green>- Giải nén thành công vào đường dẫn: <b>$Extract_Path/</b> </font><br/>";
// Gọi hàm để sao chép các tệp
if (copyFiles($Extract_Path_OK, $directory_path.'/')) {
    $messages[] = "<font color=green><b>- Sao chép toàn bộ tệp và thư mục thành công!</b></font><br/>";
	deleteDirectory($Extract_Path);
	deleteDirectory($Download_Path);
	$messages[] = "<br/><font color=green><b>- Đã hoàn tất khôi phục dữ liệu từ bản sao lưu: ".$fileName."</b></font>";
	$messages[] = "<br/><font color=green><b>- Bạn cần khởi động lại chương trình Vietbot để áp dụng các thay đổi từ bản sao lưu</b></font>";
} else {
    $messages[] = "<font color=red>- Sao chép tệp thất bại</font>";
}
	} else {
    $messages[] = "<font color=red>- Lỗi khi giải nén tệp</font>";
	}	
            } else {
				deleteDirectory($Extract_Path);
				deleteDirectory($Download_Path);
                $messages[] = "<font color=red>- Có lỗi xảy ra khi tải lên tệp sao lưu của bạn</font>";
            }
        }
    } else {
				deleteDirectory($Extract_Path);
				deleteDirectory($Download_Path);
        $messages[] = "<font color=red>- Không có tệp sao lưu nào được tải lên</font>";
    }
}


#Khôi phục dữ liệu Vietbot từ file bAckup
if (isset($_POST['Restore_Backup'])) {
$messages = [];
$messages[] = "Khôi Phục Giao Diện";

// Kiểm tra và tạo từng thư mục
foreach ($directoriessss as $directory) {
    createDirectory($directory);
}
// Kiểm tra value nếu dữ liệu không rỗng
if (!empty($_POST['Restore_Backup'])) {
     $data_restore_file = $_POST['Restore_Backup'];
	//Nếu dữ liệu là đường dẫn Local
	if (strpos($data_restore_file, '/home/') === 0) {
		$messages[] = "<font color=green>- Tiến hành khôi phục dữ liệu từ tệp sao lưu trên hệ thống</font>";
	// Gọi hàm để giải nén
	if (extractTarGz($data_restore_file, $Extract_Path)) {
		$Extract_Path_OK = $directory_path.'/'.$Extract_Path.'/';
		$messages[] = "<font color=green>- Giải nén thành công vào đường dẫn: <b>$Extract_Path/</b> </font><br/>";
// Gọi hàm để sao chép các tệp
if (copyFiles($Extract_Path_OK, $directory_path.'/')) {
    $messages[] = "<font color=green><b>- Sao chép toàn bộ tệp và thư mục thành công!</b></font><br/>";
	deleteDirectory($Extract_Path);
	deleteDirectory($Download_Path);
	$messages[] = "<br/><font color=green><b>- Đã hoàn tất khôi phục dữ liệu từ bản sao lưu: ".basename($data_restore_file)."</b></font>";
	$messages[] = "<br/><font color=green><b>- Bạn cần tải lại trang để áp dụng thay đổi Giao Diện từ bản sao lưu</b></font>";
} else {
    $messages[] = "<font color=red>- Sao chép tệp thất bại</font>";
}
	} else {
    $messages[] = "<font color=red>- Lỗi khi giải nén tệp</font>";
	}
    } else { 
        $messages[] = "<font color=red>- Dữ liệu không bắt đầu bằng 'http' hoặc '/home/'</font>";
    }
}else{
 $messages[] = "<font color=red>- Dữ liệu Restore_Backup là rỗng.</font>";
}
}

#Kiểm tra bản cập nhật giao diện
if (isset($_POST['Check_For_Upgrade'])) {
    // Tách URL thành các phần
    $parsedUrl = parse_url($Github_Repo_Vietbot_Interface);
    $pathParts = explode('/', trim($parsedUrl['path'], '/'));

    // Kiểm tra và gán giá trị
    if (count($pathParts) >= 2) {
        $git_username = $pathParts[0];
        $git_repository = $pathParts[1];

        // Đường dẫn tới file local và URL của file trên GitHub
        $localFile = $directory_path.'/version.json';
        $remoteFileUrl = "https://raw.githubusercontent.com/$git_username/$git_repository/refs/heads/main/version.json";

        // Đọc nội dung file local
        if (file_exists($localFile)) {
            $localContent = file_get_contents($localFile);
            $localData = json_decode($localContent, true);

            // Đọc nội dung file trên GitHub
            $remoteContent = file_get_contents($remoteFileUrl);
            if ($remoteContent !== false) {  // Sửa điều kiện ở đây
                $remoteData = json_decode($remoteContent, true);
                // Lấy giá trị "ui_version" từ cả hai file và so sánh
                if (isset($localData['ui_version']['latest']) && isset($remoteData['ui_version']['latest'])) {
                    if ($localData['ui_version']['latest'] !== $remoteData['ui_version']['latest']) {
                        $messages[] = "<font color=green><b>- Có bản cập nhật giao diện Vietbot mới:</b></font>";
$messages[] = "
<font color=green><ul>
  <li>Phiên Bản Mới:
    <ul>
      <li>Ngày Phát Hành: <b><font color=red>{$remoteData['ui_version']['latest']}</font></b></li>
	  <li>Mô Tả:
	  <ul>
	   <li><b>{$remoteData['ui_version']['notification']}</b></li>
	  </ul>
	  </li>
    </ul>
  </li>
</ul></font>

<font color=blue><ul>
  <li>Phiên Bản Hiện Tại:
    <ul>
      <li>Ngày Phát Hành: <b>{$localData['ui_version']['latest']}</b></li>
	  <li>Mô Tả: <b>{$localData['ui_version']['notification']}</b></li>
    </ul>
  </li>
</ul></font>";

$messages[] = "<font color=green><b>- Hãy cập nhật lên phiên bản mới để được hỗ trợ tốt nhất.</b></font>";

} else {
$messages[] = "<font color=red><b>- Không có bản cập nhật giao diện mới nào</b></font>";
$messages[] = "
<font color=blue><ul>
  <li>Phiên Bản Hiện Tại:
    <ul>
      <li>Ngày Phát Hành: <b>{$localData['ui_version']['latest']}</b></li>
	  <li>Mô Tả: <b>{$localData['ui_version']['notification']}</b></li>
    </ul>
  </li>
</ul></font>";
}
                } else {
                    $messages[] = "<font color=red>Không tìm thấy trường 'ui_version->latest' trong một hoặc cả hai file</font>";
                }
            } else {
                $messages[] = "<font color=red>Không thể tải file từ URL: $remoteFileUrl</font>";
            }
        } else {
            $messages[] = "<font color=red>Không tìm thấy tệp: $localFile</font>";
        }
    } else {
        $messages[] = "<font color=red>Không thể lấy thông tin username và repository từ URL: $Github_Repo_Vietbot_Interface</font>";
    }
}

#Cập nhật giao diện web ui
if (isset($_POST['Upgrade_Interface'])) {
$Exclude_Files_Folder = isset($_POST['exclude_files_folder']) ? $_POST['exclude_files_folder'] : [];
$Exclude_File_Format = isset($_POST['exclude_file_format']) ? $_POST['exclude_file_format'] : [];
$make_a_backup_before_updating = isset($_POST['make_a_backup_before_updating']) ? true : false;
#Các file và thư mục cần bỏ qua không cho cập nhật, ghi đè
$Keep_The_File_Folder_POST = isset($_POST['keep_the_file_folder']) ? $_POST['keep_the_file_folder'] : [];
$messages[] =  "Đang Tiến Hành Cập Nhật Giao Diện Web UI";
	// Kiểm tra và tạo từng thư mục
	foreach ($directoriessss as $directory) {
		createDirectory($directory);
	}
#Xử lý tải xuống bản cập nhật
$download_Git_Repo_As_Named_Zip = downloadGitRepoAsNamedZip($Github_Repo_Vietbot_Interface, $Download_Path);

if (!is_null($download_Git_Repo_As_Named_Zip)) {
$messages[] = "<font color=green>- Tải dữ liệu và giải nén thành công vào đường dẫn: <b>".$download_Git_Repo_As_Named_Zip."/</b></font><br/>";

#lựa chọn có tạo bản sao lưu trước khi cập nhật không
if ($make_a_backup_before_updating === true){
$messages[] =  "- Đang tạo bản sao lưu giao diện trước khi cập nhật";

$FileName_Backup_VietBot = backup_data($Exclude_Files_Folder, $Exclude_File_Format);
if (!is_null($FileName_Backup_VietBot)) {
$messages[] = "<font color=green>- Hoàn thành Sao Lưu Giao Diện Trên Hệ Thống: <b>" .$FileName_Backup_VietBot."</b></font>";

} else {
    $messages[] =  "<font color=red>-Lỗi xảy ra trong quá trình tạo bản sao lưu dữ liệu</font>";
}
}else{
	$messages[] = "<font color=red>- Sao lưu dữ liệu trước khi cập nhật bị tắt, sẽ không có bản sao lưu nào được tạo ra</font>";
}

$messages[] = "<font color=green><b>- Đang tiến hành cập nhật dữ liệu mới...</b></font>";

#tiến hành Sao chép ghi đè dữ liệu mới và bỏ qua file được chọn
if (copyFiles($download_Git_Repo_As_Named_Zip.'/', $directory_path.'/', $Keep_The_File_Folder_POST)) {
    $messages[] = "<font color=green><b>- Đã hoàn tất cập nhật dữ liệu mới</b></font><br/>";

#Xóa các file, thư mục được tải về
deleteDirectory($Extract_Path);
deleteDirectory($Download_Path);

//Chmod lại các file và thư mục thành 0777
#Cấu hình kết nối SSH:
$connection = ssh2_connect($ssh_host, $ssh_port);
ssh2_auth_password($connection, $ssh_user, $ssh_password);
ssh2_exec($connection, "sudo chmod -R 0777 $directory_path");
$messages[] = "<font color=green><b>- Đã hoàn tất dọn dẹp dữ liệu rác</b></font><br/>";
}else{
	$messages[] = "<font color=red>- Lỗi xảy ra trong quá trình cập nhật dữ liệu mới</font>";
}

}else{
	$messages[] =  "<font color=red>-Lỗi xảy ra trong quá trình tải xuống bản cập nhật dữ liệu, Đã dừng quá trình cập nhật</font>";
}



}
?>
<!DOCTYPE html>
<html lang="vi">
<?php
include 'html_head.php';
?>
<head>
    <style>
        .limited-height {
            max-height: 350px; 
            overflow-y: auto;
            padding: 10px;
        }
    </style>

	</head>
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
      <h1>Quản Lý Giao Diện</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item" onclick="loading('show')"><a href="index.php">Trang chủ</a></li>
          <li class="breadcrumb-item active">Giao diện</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
	    <section class="section">
        <div class="row">
		<form method="POST" action="" enctype="multipart/form-data">
<?php
// Kiểm tra và hiển thị thông báo
if (!empty($messages)) {
	echo '<div class="card"><div class="card-body">
<h5 class="card-title">Thông Báo Tiến Trình:</h5>
<div class="limited-height">';
    $allMessages = implode("<br>", $messages);
    echo "<p>$allMessages</p>";
	echo "</div></div></div>";
}
?>

<div class="card">
<div class="card-body">

<h5 class="card-title">Cấu Hình Sao Lưu Giao Diện:</h5>
<div class="row mb-3">
<label for="vietbot_webui_backup_path" class="col-sm-3 col-form-label">Đường dẫn tệp sao lưu:</label>
<div class="col-sm-9">
<div class="input-group mb-3">
<input disabled class="form-control border-danger" type="text" name="vietbot_webui_backup_path" id="vietbot_webui_backup_path" placeholder="<?php echo $Backup_Dir_Save_Interface_Vietbot; ?>" value="<?php echo $Backup_Dir_Save_Interface_Vietbot; ?>">
</div>
</div>
</div>

<div class="row mb-3">
<label for="vietbot_webui_limit_backup_files" class="col-sm-3 col-form-label">Giới hạn tối đa tệp tin sao lưu <i class="bi bi-question-circle-fill" onclick="show_message('Cần chỉnh sửa trong <b>Config.json</b> hoặc tab <b>Cấu Hình Config</b>')"></i> :</label>
<div class="col-sm-9">
<div class="input-group mb-3">
<input disabled class="form-control border-danger" type="number" min="2" step="1" max="10" name="vietbot_webui_limit_backup_files" id="vietbot_webui_limit_backup_files" placeholder="<?php echo $Config['web_interface']['backup_upgrade']['web_interface']['backup']['limit_backup_files']; ?>" value="<?php echo $Config['web_interface']['backup_upgrade']['web_interface']['backup']['limit_backup_files']; ?>">
</div>
</div>
</div>


<div class="row mb-3">
<label for="exclude_files_folder" class="col-sm-3 col-form-label">Loại Trừ File/Thư Mục Không Sao Lưu  <i class="bi bi-question-circle-fill" onclick="show_message('thêm hoặc loại bỏ file, thư mục sẽ được cấu hình trong <b>Config.json</b> hoặc chỉnh sửa trong tab <b>Cấu Hình Config</b>')"></i> :</label>
<div class="col-sm-9">
<div class="input-group mb-3">
<?php
foreach ($Config['web_interface']['backup_upgrade']['web_interface']['backup']['exclude_files_folder'] as $exclude_files_folderr) {
    echo '<input type="checkbox" class="form-check-input" name="exclude_files_folder[]" id="' . htmlspecialchars($exclude_files_folderr) . '" value="' . htmlspecialchars($exclude_files_folderr) . '" checked>&nbsp;<label for="' . htmlspecialchars($exclude_files_folderr) . '">' . htmlspecialchars($exclude_files_folderr) . '</label>&emsp;&emsp;';
}
?>
</div>
</div>
</div>

<div class="row mb-3">
<label for="exclude_file_format" class="col-sm-3 col-form-label">Loại Trừ Định Dạng File Không Sao Lưu <i class="bi bi-question-circle-fill" onclick="show_message('thêm hoặc loại bỏ định dạng file sẽ được cấu hình trong <b>Config.json</b> hoặc chỉnh sửa trong tab <b>Cấu Hình Config</b>')"></i> :</label>
<div class="col-sm-9">
<div class="input-group mb-3">
<?php
foreach ($Config['web_interface']['backup_upgrade']['web_interface']['backup']['exclude_file_format'] as $exclude_file_formatt) {
    echo '<input type="checkbox" class="form-check-input" name="exclude_file_format[]" id="' . htmlspecialchars($exclude_file_formatt) . '" value="' . htmlspecialchars($exclude_file_formatt) . '" checked>&nbsp;<label for="' . htmlspecialchars($exclude_file_formatt) . '">' . htmlspecialchars($exclude_file_formatt) . '</label>&emsp;&emsp;';
}
?>
</div>
</div>
</div>
<hr/>

<div class="card-body">
<div class="row mb-3">
    <label class="col-sm-3 col-form-label"><b>Tải Lên Tệp Khôi Phục:</b></label>
    <div class="col-sm-9">
        <div class="input-group">
	
            <input class="form-control border-success" type="file" name="fileToUpload" accept=".tar.gz">
            <button class="btn btn-warning border-success" type="submit" name="upload_and_restore" onclick="return confirmRestore('Bạn có chắc chắn muốn tải lên tệp để khôi phục dữ liệu giao diện Vietbot không?')">Khôi Phục Dữ Liệu</button>
			
        </div>
		
    </div>
</div>
</div>

<center>
<button type="submit" name="Backup_Interface" class="btn btn-primary rounded-pill" onclick="return confirmRestore('Bạn có chắc chắn muốn tạo bản sao lưu giao diện với Cấu Hình Sao Lưu bên trên?')">Tạo Bản Sao Lưu Giao Diện</button>
<button type="button" name="show_all_file_in_directoryyyy" class="btn btn-success rounded-pill" onclick="show_all_file_in_directory('<?php echo $HTML_Vietbot_Offline . '/' . $Backup_Dir_Save_Interface_Vietbot; ?>', 'Tệp Sao Lưu Giao Diện Trên Hệ Thống', 'show_all_file_folder_Backup_web_interface')">Tệp Sao Lưu Hệ Thống</button>
<div class="limited-height" id="show_all_file_folder_Backup_web_interface"></div>
</center>


<!-- Bootstrap Modal -->
<div class="modal fade" id="responseModal_read_files_in_backup" tabindex="-1" role="dialog" aria-labelledby="responseModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-fullscreen" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="responseModalLabel">Nội dung xem trước tệp tin sao lưu </h5>
<button type="button" class="btn btn-danger" onclick="closeModal_read_files_in_backup()"><i class="bi bi-x-circle"></i> Đóng</button>
      </div>
      <div class="modal-body">
	  <div class="card-body">
       <pre><code id="modal-body-content"></code></pre>
      </div>
      </div>
      <div class="modal-footer">
     <center>   <button type="button" class="btn btn-danger" onclick="closeModal_read_files_in_backup()"><i class="bi bi-x-circle"></i> Đóng</button></center>
      </div>
    </div>
  </div>
</div>


</div>
</div>
		
		
	


<div class="card">
<div class="card-body">
<h5 class="card-title">Cấu Hình Cập Nhật Giao Diện:</h5>

<div class="row mb-3">
<label class="col-sm-3 col-form-label">Tạo Bản Sao Lưu Trước Khi Cập Nhật:</label>
<div class="col-sm-9">
<div class="form-switch">
<input class="form-check-input" type="checkbox" name="make_a_backup_before_updating" id="make_a_backup_before_updating" <?php if ($Config['web_interface']['backup_upgrade']['web_interface']['upgrade']['backup_before_updating']) echo 'checked'; ?>>
</div>
</div>
</div>

<div class="row mb-3">
<label for="loai_tru_file_thu_muc" class="col-sm-3 col-form-label">Giữ lại tệp, thư mục <i class="bi bi-question-circle-fill" onclick="show_message('Giữ lại tệp, thư mục không cho cập nhật, ghi đè. <b>Áp dụng cho những tệp, thư mục lưu trữ cấu hình, thông tin Cá Nhân (Có tính chất Riêng Tư)</b><br/><br/>- Thiết lập thêm bớt file và thư mục trong tab: <b>Cấu Hình Config</b>')"></i> :</label>
<div class="col-sm-9">
<div class="input-group mb-3">
<?php
foreach ($Config['web_interface']['backup_upgrade']['web_interface']['upgrade']['keep_file_directory'] as $keep_the_file_folder_tuyen) {
    echo '<input type="checkbox" class="form-check-input" name="keep_the_file_folder[]" id="' . htmlspecialchars($keep_the_file_folder_tuyen) . '" value="' . htmlspecialchars($keep_the_file_folder_tuyen) . '" checked>&nbsp;<label for="' . htmlspecialchars($keep_the_file_folder_tuyen) . '">' . htmlspecialchars($keep_the_file_folder_tuyen) . '</label>&emsp;&emsp;';
}
?>
</div>
</div>
</div>

<center>
<button type="submit" name="Check_For_Upgrade" class="btn btn-primary rounded-pill">Kiểm Tra Bản Cập Nhật</button>
<button type="submit" name="Upgrade_Interface" class="btn btn-success rounded-pill" onclick="return confirmRestore('Bạn có chắc chắn muốn cập nhật phiên bản giao diện mới?')">Cập Nhật Giao Diện</button>
</center>



</div>
</div>




	
		
		</form>
		</div>
		</section>
	
</main>


  <!-- ======= Footer ======= -->
<?php
include 'html_footer.php';
?>
<!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Nghe thử file âm thanh 
<audio id="audioPlayer" style="display: none;" controls></audio>-->
  <script>
function closeModal_read_files_in_backup() {
    $('#responseModal_read_files_in_backup').modal('hide');
}
</script>
  <!-- Template Main JS File -->
<?php
include 'html_js.php';
?>

</body>
</html>
