<?php
//Code By: Vũ Tuyển
//Facebook: https://www.facebook.com/TWFyaW9uMDAx
//include "../Configuration.php";
?>


<body>
    <br/>
    </center>
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/jquery-3.6.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#my-form').on('submit', function() {
                // Hiển thị biểu tượng loading
                $('#loading-overlay').show();

                // Vô hiệu hóa nút gửi
                $('#submit-btn').attr('disabled', true);
            });
        });
    </script>
    <div id="loading-overlay">
        <img id="loading-icon" src="../assets/img/Loading.gif" alt="Loading...">
        <div id="loading-message">Đang tiến hành, vui lòng đợi...</div>
    </div>
<?php
/*
// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['root_id'])) {
    // Nếu chưa đăng nhập, chuyển hướng về trang đăng nhập (index.php)
    //header("Location: ./index.php");
	echo "<br/><center><h1>Có Vẻ Như Bạn Chưa Đăng Nhập!<br/><br>
	- Nếu Bạn Đã Đăng Nhập, Hãy Nhấn Vào Nút Dưới<br/><br/><a href='$PHP_SELF'><button type='button' class='btn btn-danger'>Tải Lại</button></a></h1>
	</center>";
    exit();
}
*/
?>
<?php
    // Hàm đệ quy để sao chép tất cả các tệp và thư mục
    function copyRecursive($source, $destination) {
        $dir = opendir($source);
        @mkdir($destination);

        while (($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                $sourceFile = $source . '/' . $file;
                $destinationFile = $destination . '/' . $file;

                if (is_dir($sourceFile)) {
                    copyRecursive($sourceFile, $destinationFile);
                } else {
                    copy($sourceFile, $destinationFile);
                }
            }
        }

        closedir($dir);
    }

    // Hàm đệ quy để xóa nội dung trong thư mục
    function deleteRecursive($path) {
        if (is_dir($path)) {
            $files = array_diff(scandir($path), array('.', '..'));

            foreach ($files as $file) {
                $filePath = $path . '/' . $file;

                if (is_dir($filePath)) {
                    deleteRecursive($filePath);
                } else {
                    unlink($filePath);
                }
            }

            return rmdir($path);
        } elseif (is_file($path)) {
            return unlink($path);
        }

        return false;
    }
function extractTarGz($file, $destination) {
    $command = "tar -xzf $file -C $destination";
    exec($command);
}
function copyRecursiveExclude($source, $destination, $excludeExtensions = array('.zip', '.tar.gz')) {
    $dir = opendir($source);
    @mkdir($destination);

    while (($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            $sourceFile = $source . '/' . $file;
            $destinationFile = $destination . '/' . $file;

            if (is_dir($sourceFile)) {
                copyRecursiveExclude($sourceFile, $destinationFile, $excludeExtensions);
            } else {
                $extension = pathinfo($sourceFile, PATHINFO_EXTENSION);
                if (!in_array($extension, $excludeExtensions)) {
                    copy($sourceFile, $destinationFile);
                }
            }
        }
    }
    closedir($dir);
}
function deleteDirectory($directory) {
    if (!file_exists($directory)) {
        return;
    }
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($iterator as $file) {
        if ($file->isDir()) {
            rmdir($file->getPathname());
        } else {
            unlink($file->getPathname());
        }
    }
    rmdir($directory);
}
//Chmod 777
if (isset($_POST['set_full_quyen'])) {
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die($E_rror_HOST);}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die($E_rror);}
$stream1 = ssh2_exec($connection, 'sudo chmod -R 0777 '.$Path_Vietbot_src);
stream_set_blocking($stream1, true);
$stream_out1 = ssh2_fetch_stream($stream1, SSH2_STREAM_STDIO);
stream_get_contents($stream_out1);
header("Location: $PHP_SELF"); exit;
}
// Thư mục cần kiểm tra 777
$directories = array("$DuognDanUI_HTML","$DuognDanThuMucJson");
function checkPermissions($path, &$hasPermissionIssue) {
    $files = scandir($path);
    foreach ($files as $file) {
		// bỏ qua thư mục tts_saved và __pycache__ check quyền
        if ($file === '.' || $file === '..' || $file === 'tts_saved' || $file === '__pycache__' || $file === 'backup') {continue;}
        $filePath = $path . '/' . $file;
        $permissions = fileperms($filePath);
        if ($permissions !== false && ($permissions & 0777) !== 0777) {
            if (!$hasPermissionIssue) {
               // echo "<br/><center><h3 class='text-danger'>Một Số File,Thư Mục Trong <b>$path</b> Không Có Quyền Can Thiệp.<h3><br/>";
			   echo "<center>Phát hiện thấy một số nội dung bị thay đổi quyền hạn.<br/>";
			echo "<form method='post' id='my-form' action='".$PHP_SELF."'> <button type='submit' name='set_full_quyen' class='btn btn-success'>Cấp Quyền</button></form></center>";
                $hasPermissionIssue = true;
				exit();
			}	
            break;}
        if (is_dir($filePath)) {
            checkPermissions($filePath, $hasPermissionIssue);
        }}}
// Kiểm tra từng thư mục 777
foreach ($directories as $directory) {
    $hasPermissionIssue = false;
    checkPermissions($directory, $hasPermissionIssue);
}

if (isset($_POST['checkforupdates_ui'])) {
$localFile = $DuognDanUI_HTML.'/version.json';
// Lấy nội dung JSON từ URL
$remoteJsonData = file_get_contents($UI_Version);
$remoteData = json_decode($remoteJsonData, true);
// Đọc nội dung JSON từ tệp tin cục bộ
$localJsonData = file_get_contents($localFile);
$localData = json_decode($localJsonData, true);
// Lấy giá trị 'value' từ cả hai nguồn dữ liệu
$remoteValue = $remoteData['ui_version']['latest'];
$localValue = $localData['ui_version']['current'];
// So sánh giá trị
if ($remoteValue !== $localValue) {
    $messagee .= 'Có phiên bản mới: <font color=red>'.$remoteValue.'</font><br/>';
    $messagee .= 'Phiên bản hiện tại của bạn: <font color=red>'.$localValue.',</font> vui lòng cập nhật.<br/>';
    //$messagee .= $remoteData['ui_version']['notification'].'\n';
	if (empty($remoteData['ui_version']['notification'])) {
    //echo "Không có dữ liệu";
	} else {
    $messagee .= 'Nội Dung Cập Nhật: <font color=red>'.$remoteData['ui_version']['notification'].'</font><br/>';
	}
} else {
    $messagee .= 'Bạn đang sử dụng phiên bản mới nhất: <font color=red>'.$localValue.'<br/>';
}
}

if (isset($_POST['ui_update'])) {
	if (isset($block_updates_web_ui) && $block_updates_web_ui === true) {
        //echo "Checkbox được tích và không cho cập nhật.";
        $messagee .= '<font color=red><i>Cập Nhật <b>Web UI</b> Đã Bị Tắt, Cần Đi Tới Tab <b>Cấu Hình Config</b> Để Bỏ Tích</i></font>';
    } else {
$backupDir = $DuognDanUI_HTML.'/ui_update/backup'; // Đường dẫn thư mục sao lưu lại file sao lưu
$timestamp = date('d_m_Y_His'); 
$startCheckboxReload = $_POST['startCheckboxReload'];
$backupFile = $backupDir . '/ui_backup_' . $timestamp . '.tar.gz';
$excludeArgs = '--exclude="*.tar.gz" --exclude="backup_update/extract/UI_VietBot-main/*"';
$tarCommand = 'tar -czvf ' . $backupFile . ' ' . $excludeArgs . ' -C ' . dirname($DuognDanUI_HTML) . ' ' . basename($DuognDanUI_HTML);
exec($tarCommand, $output, $returnCode);
if ($returnCode === 0) {
    chmod($backupFile, 0777);
  //  $messagee .= 'Tạo bản sao lưu giao diện thành công, hãy tải lại trang để áp dụng\n';
    $backupFiles = glob($backupDir . '/*.tar.gz');
    $numBackupFiles = count($backupFiles);
    if ($numBackupFiles > $maxBackupFilesUI) {
        usort($backupFiles, function ($a, $b) {
            return filemtime($a) - filemtime($b);
        });
        $filesToDelete = array_slice($backupFiles, 0, $numBackupFiles - $maxBackupFilesUI);
        foreach ($filesToDelete as $file) {
            unlink($file);
			$basenameeee = basename($file);
           // $messagee .= 'Backup đạt giới hạn, đã xóa tệp tin sao lưu cũ: ' . $basenameeee . '\n';
        }
    }
} else { 
    $messagee .= '<font color=red>Có lỗi xảy ra khi tạo bản sao lưu.</font><br/>';
}
//END sao Lưu
$url = $UI_VietBot.'/archive/master.zip';
$zipFile = $DuognDanUI_HTML.'/ui_update/dowload_extract/UI_VietBot.zip';
$extractPath = $DuognDanUI_HTML.'/ui_update/extract';
//$destinationPath = '/home/pi/vietbot_offline/html';
$sourceDirectory = $extractPath . '/UI_VietBot-main';
// Tải tập tin từ URL
$fileContents = file_get_contents($url);
// Lưu nội dung vào tập tin đích
file_put_contents($zipFile, $fileContents);
// Mở tập tin zip
$zip = zip_open($zipFile);
if ($zip) {
    // Lặp qua các mục trong tập tin zip
    while ($zipEntry = zip_read($zip)) {
        $entryName = zip_entry_name($zipEntry);
        $entryPath = $extractPath . '/' . $entryName;
        // Tạo các thư mục cha nếu chưa tồn tại
        $dirPath = dirname($entryPath);
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
        // Mở mục trong tập tin zip
        if (zip_entry_open($zip, $zipEntry, "r")) {
            // Đọc nội dung mục trong tập tin zip
            $entryContent = zip_entry_read($zipEntry, zip_entry_filesize($zipEntry));
            // Lưu nội dung vào đường dẫn cụ thể
            file_put_contents($entryPath, $entryContent);
            // Đóng mục trong tập tin zip
            zip_entry_close($zipEntry);
        }
    }
    // Đóng tập tin zip
    zip_close($zip);
  //  $messagee .= 'Đã tải xuống và giải nén giao diện mới thành công!\n';
    // Gọi hàm sao chép đệ quy
    copyRecursive($sourceDirectory, $DuognDanUI_HTML);
    $messagee .= '<font color=red>Cập nhật giao diện mới thành công!</font>';
   // $messagee .= 'Bạn Hãy Tắt Trang Và Truy Cập Lại Để Áp Dụng, (Hoặc F5 Để Áp Dụng)....!\n';
    // Gọi hàm xóa đệ quy
    deleteRecursive($sourceDirectory);
	shell_exec("rm $zipFile");
} else {
    $messagee .= '<font color=red>Có lỗi xảy ra, không thể mở tập tin giao diện đã tải về!</font>';
}
//Chmod 777 khi chạy xong backup
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die($E_rror_HOST);}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die($E_rror);}
$stream1 = ssh2_exec($connection, 'sudo chmod -R 0777 '.$Path_Vietbot_src);
$stream2 = ssh2_exec($connection, 'sudo chown -R pi:pi '.$Path_Vietbot_src);
stream_set_blocking($stream1, true); 
stream_set_blocking($stream2, true); 
$stream_out1 = ssh2_fetch_stream($stream1, SSH2_STREAM_STDIO); 
$stream_out2 = ssh2_fetch_stream($stream2, SSH2_STREAM_STDIO); 
stream_get_contents($stream_out1);
stream_get_contents($stream_out2);
///////////////////
if (@$_POST['audioo_playmp3_success'] === "playmp3_success") {
	echo '<audio style="display: none;" id="myAudio_success" controls autoplay>';
    echo '<source src="../assets/audio/ui_update_success.mp3" type="audio/mpeg">';
    echo 'Your browser does not support the audio element.';
    echo '</audio>';
	echo '<script>';
	echo 'var audio = document.getElementById("myAudio_success");';
    echo 'audio.play();';
	echo '</script>';
}
//echo $startCheckboxReload;
?>

<?php





}
}
if (isset($_POST['restors_ui'])) {
    $selectedFile = $_POST['tarFile'];
    if ($selectedFile === "....") {
        $message .= '<font color=red>Vui lòng chọn file cần khôi phục!</font>';
    } else {
        $tarFile = $DuognDanUI_HTML.'/ui_update/backup/' . $selectedFile;
        $extractDirectory = $DuognDanUI_HTML.'/ui_update/extract';
        //$copyDestination = '/home/pi/vietbot_offline/html';
        $deleteDirectory = $extractDirectory . '/html';
        // Giải nén tệp tin .tar.gz
        extractTarGz($tarFile, $extractDirectory);
        // Sao chép nội dung và loại trừ các tệp .zip và .tar.gz
        copyRecursiveExclude($extractDirectory . '/html', $DuognDanUI_HTML, array('.zip', '.tar.gz'));
        // Xóa thư mục /home/pi/vietbot_offline/html/ui_update/extract/html
        deleteDirectory($deleteDirectory);
         $message .= '<font color=red>Đã khôi phục giao diện backup thành công!<br/>';
         $message .= 'Bạn Hãy Tải Lại Trang Để Áp Dụng....!</font>';
    }
}
if (isset($_POST['download']) && isset($_POST['tarFile'])) {
    $selectedFile = $_POST['tarFile'];
    $filePath = '/ui_update/backup/' . $selectedFile; // Đường dẫn đến thư mục chứa tệp tin
    if ($selectedFile === "....") {
         $message .= '<font color=red>Vui lòng chọn file cần tải xuống!</font>';
    } else {
        // Tạo liên kết tới trang mục tiêu trong tab mới
        $targetLink = "http://$serverIP$filePath"; // Đặt đường dẫn mục tiêu tại đây
        echo "<script>window.open('$targetLink', '_blank');</script>";
    }
}
?>
  <form method="POST" id="my-form" action="">
  
   	<div class="my-div">
    <span class="corner-text"><h5>Cập Nhật:</h5></span><br/><br/>
	<center> 
	<div id="messagee"></div><br/></center>
	<div class="row justify-content-center"><div class="col-auto">
	<table class="table table-bordered">
  <thead> 
    <tr>
      <th scope="col" colspan="2"><font color=red>Lựa Chọn Nâng Cao Khi Cập Nhật Hoàn Tất</font></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th>Thông Báo Âm Thanh:</th>
	  <td><input type="checkbox" name="audioo_playmp3_success" value="playmp3_success" checked></td>
    </tr>
    <tr>
      <th><span class="inline-elements" title="Tự Động Tải Lại Trang Khi Cập Nhật Hoàn Tất">Tự Động Làm Mới Lại Trang: <font color=red><span id="countdown"></span></font></span></th>
	  <td> <input type="checkbox" name="startCheckboxReload" id="startCheckbox" title="Tự Động Tải Lại Trang Khi Cập Nhật Hoàn Tất" value="start" checked></td>
    </tr>
  </tbody>
</table>
	</div></div>
  <div class="row justify-content-center"><div class="col-auto"><div class="input-group">
    		  <input type="submit" name="checkforupdates_ui" class="btn btn-success" value="Kiểm tra">
		   <input type="submit" name="ui_update" class="btn btn-warning" value="Cập Nhật">
		   <a class="btn btn-primary" href="<?php echo $PHP_SELF; ?>" role="button">Làm Mới</a>
		   <button class="btn btn-danger" id="reloadButton">Tải Lại Trang</button>
		   </div>
		   </div>
		   </div>  <br/></div>
	<br/>   <div class="my-div">
    <span class="corner-text"><h5>Sao Lưu/Khôi Phục:</h5></span><br/><br/>

<center><div id="message"></div></center>

	   <div class="row justify-content-center"><div class="col-auto"><div class="input-group">
<?php
$directory = $DuognDanUI_HTML.'/ui_update/backup';
// Lấy danh sách các tệp .tar.gz trong thư mục
$files = glob($directory . '/*.tar.gz');
// Tạo đoạn mã HTML cho select dropdown
$selectDropdown = '<select class="form-select" name="tarFile"><option value="....">Chọn File Giao Diện</option>';
// Lặp qua từng tệp và thêm mục vào select dropdown
foreach ($files as $file) {
    // Lấy tên tệp từ đường dẫn đầy đủ
    $filename = basename($file);
    // Thêm mục vào select dropdown
    $selectDropdown .= '<option value="' . $filename . '">' . $filename . '</option>';
}
$selectDropdown .= '</select>';
// Hiển thị select dropdown
echo $selectDropdown; 
?>
<input type="submit" name="download" class="btn btn-primary" value="Tải xuống">
<input type="submit" name="restors_ui" class="btn btn-warning" value="Khôi Phục">
</div>
</div>
</div><br/>
  </div>
  </form>
 <br/> <p class="right-align"><b>Phiên bản giao diện:  <font color=red><?php echo $dataVersionUI->ui_version->current; ?></font></b></p>
  
  	    <script>
        var messageElement = document.getElementById("message");
        var message = "<?php echo $message; ?>";
       // messageElement.innerText = message;
        messageElement.innerHTML = message;
    </script>
	
	
	    <script>
        var messageElementt = document.getElementById("messagee");
        var messagee = "<?php echo $messagee; ?>";
       // messageElementt.innerText = messagee;
        messageElementt.innerHTML = messagee;
    </script>
	
	<script>
var reloadButton = document.getElementById('reloadButton');
var startCheckbox = document.getElementById('startCheckbox');
var countdownElement = document.getElementById('countdown');
var requiredValue = "<?php echo $startCheckboxReload; ?>";
var countdown = '3';
var countdownInterval;
 
function updateCountdown() {
  countdownElement.textContent = countdown;
} 

function reloadHostPage() {
  // Gửi thông điệp tới trang chính để yêu cầu tải lại
  window.parent.postMessage('reload', '*');
  // Tải lại trang chính (host page) bằng cách truy cập vào window cha và gọi hàm location.reload()
  window.parent.location.reload();
}

function startCountdown() {
  //countdown = 3;
  updateCountdown();
  countdownInterval = setInterval(function() {
    if (countdown === 0) {
      clearInterval(countdownInterval);
      reloadHostPage();
    } else {
      countdown--;
      updateCountdown();
    }
  }, 1000);
}

if (startCheckbox.checked && startCheckbox.value === requiredValue) {
  startCountdown();
}

reloadButton.addEventListener('click', function() {
  reloadHostPage();
});

startCheckbox.addEventListener('change', function() {
  if (startCheckbox.checked && startCheckbox.value === requiredValue) {
    startCountdown();
  } else {
    clearInterval(countdownInterval);
    countdownElement.textContent = "";
  }
});
</script>

	</body>
	</html>
