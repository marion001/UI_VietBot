<?php
//Code By: Vũ Tuyển
//Facebook: https://www.facebook.com/TWFyaW9uMDAx
//include "../Configuration.php";
?>


<body>
    <br/>
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
$backupDir = $DuognDanUI_HTML."/backup_update/backup"; // Đường dẫn thư mục sao lưu
// Tạo tên tệp tin nén với ngày giờ và thời gian
$timestamp = date('d_m_Y_His');
$backupFile = $backupDir . '/vietbot_src_' . $timestamp . '.tar.gz';
function deleteFiles($directory, $excludedFiles, $excludedDirectories, &$deletedItems)
{
    $files = glob($directory . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            $fileName = basename($file);
            
            if (!in_array($fileName, $excludedFiles)) {
                unlink($file);
                $deletedItems[] = $file . '</span>';
            }
        } elseif (is_dir($file)) {
            $dirName = basename($file);
            
            if (!in_array($dirName, $excludedDirectories)) {
                deleteFiles($file, $excludedFiles, $excludedDirectories, $deletedItems);
                rmdir($file);
                $deletedItems[] = $file . '</span>';
            }
        }
    }
}
/*
function copyFiles($sourceDirectory, $destinationDirectory, $excludedFiles, &$copiedItems)
{
    $files = glob($sourceDirectory . '/*');
  
    foreach ($files as $file) {
        if (is_file($file)) {
            $fileName = basename($file);
            
            if (!in_array($fileName, $excludedFiles)) {
                copy($file, $destinationDirectory . '/' . $fileName);
                $copiedItems[] = $destinationDirectory . '/' . $fileName ."\n";
            }
        } elseif (is_dir($file)) {
            $dirName = basename($file);
            
            if (!in_array($dirName, $excludedFiles)) {
                mkdir($destinationDirectory . '/' . $dirName);
                copyFiles($file, $destinationDirectory . '/' . $dirName, $excludedFiles, $copiedItems);
            }
        }
    }
}
*/

function copyFiles($sourceDirectory, $destinationDirectory, $excludedFiles, $excludedDirectories, &$copiedItems)
{
    $files = glob($sourceDirectory . '/*');
  
    foreach ($files as $file) {
        if (is_file($file)) {
            $fileName = basename($file);
            
            if (!in_array($fileName, $excludedFiles)) {
                copy($file, $destinationDirectory . '/' . $fileName);
                $copiedItems[] = $destinationDirectory . '/' . $fileName ."\n";
            }
        } elseif (is_dir($file)) {
            $dirName = basename($file);
            
            if (!in_array($dirName, $excludedDirectories)) {
                $subDestinationDirectory = $destinationDirectory . '/' . $dirName;
                mkdir($subDestinationDirectory);
                copyFiles($file, $subDestinationDirectory, $excludedFiles, $excludedDirectories, $copiedItems);
            }
        }
    }
}


	//restart vietbot
if (isset($_POST['restart_vietbot'])) {
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die($E_rror_HOST);}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die($E_rror);}
$stream = ssh2_exec($connection, 'systemctl --user restart vietbot');
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
stream_get_contents($stream_out);
header("Location: $PHP_SELF");
exit;
}
//Chmod 777
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
// Kiểm tra xem thư mục nguồn có tồn tại không
if (!is_dir($DuognDanThuMucJson)) {
    echo "<center>Thư mục nguồn $DuognDanThuMucJson không tồn tại.</center>";
    exit;
}
?>
<div class="my-div">
    <span class="corner-text"><h5>Cập Nhật:</h5></span>
    <br/>
    <br/>
    <center>
        <div id="messagee"></div>
    </center>
    <br/>
    <form method="POST" id="my-form" action="">
        <div class="row g-3 d-flex justify-content-center">
            <div class="col-auto">

                <table class="table table-bordered">

                    <tr>
                        <th colspan="2">
                            <center class="text-danger">Chọn File Cần Giữ Lại</center>
                        </th>
                        <th colspan="2">
                            <center class="text-danger">Chọn Thư Mục Cần Giữ Lại</center>
                        </th>
                    </tr>
                    <tr>
                        <th scope="row" title="Hệ thống sẽ tự động xử lý, bạn không cần phải lựa chọn">config.json</th>
                        <!--	<td><input type="checkbox" class="form-check-input" name="exclude[]" value="config.json" disabled></td> -->
                        <td title="Hệ thống sẽ tự động xử lý, bạn không cần phải lựa chọn">-</td>
                        <th scope="row">tts_saved</th>
                        <td>
                            <input type="checkbox" class="form-check-input" name="exclude[]" value="tts_saved" checked>
                        </td>

                    </tr>
                    <tr>
                        <th scope="row" title="Hệ thống sẽ tự động xử lý, bạn không cần phải lựa chọn">skill.json</th>
                        <!-- <td><input type="checkbox" class="form-check-input" name="exclude[]" value="skill.json" disabled></td><td>-</td> -->
                        <td title="Hệ thống sẽ tự động xử lý, bạn không cần phải lựa chọn">-</td>
                        <th scope="row">hotword</th>
                        <td>
                            <input type="checkbox" class="form-check-input" name="exclude[]" value="hotword" checked>
                        </td>

                    </tr>
                    <tr>
                        <th scope="row">google_stt.json</th>
                        <td>
                            <input type="checkbox" class="form-check-input" name="exclude[]" value="google_stt.json" checked>
                        </td>
                        <th scope="row">sound</th>
                        <td>
                            <input type="checkbox" class="form-check-input" name="exclude[]" value="sound" checked>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">google_tts.json</th>
                        <td>
                            <input type="checkbox" class="form-check-input" name="exclude[]" value="google_tts.json" checked>
                        </td>
                        <th scope="row">mp3</th>
                        <td>
                            <input type="checkbox" class="form-check-input" name="exclude[]" value="mp3" checked>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">credentials.json</th>
                        <td>
                            <input type="checkbox" class="form-check-input" name="exclude[]" value="credentials.json" checked>
                        </td>
                        <th scope="row">__pycache__</th>
                        <td>
                            <input type="checkbox" class="form-check-input" name="exclude[]" value="__pycache__">
                        </td>

                    </tr>
                    <tr>
                        <th scope="row">device_config.json</th>
                        <td>
                            <input type="checkbox" class="form-check-input" name="exclude[]" value="device_config.json" checked>
                        </td>
                        <th scope="row">-</th>
                        <td>-</td>

                    </tr>
					                  <br/>  <tr>
									     <th colspan="4">
                            <center class="text-danger">Lựa Chọn Nâng Cao Khi Cập Nhật/Khôi Phục Hoàn Tất</center>
                        </th></tr><tr>
                        <th>
                            <center title="Khởi Động Lại Toàn Bộ Hệ Thống Loa Thông Minh Vietbot">Reboot Hệ Thống:</center>
                        </th>
						  <td>
                           <input type="checkbox"  title="Khởi Động Lại Toàn Bộ Hệ Thống Loa Thông Minh Vietbot" name="reboot_checked" class="single-checkbox form-check-input" value="sudo_reboot">
						
                        </td>
                        <th>
                            <center  title="Chỉ Khởi Động Lại Trợ Lý Vietbot">Restart Vietbot:</center>
                        </th>
								  <td>
                           <input type="checkbox" name="restart_vietbot_checked" class="single-checkbox form-check-input" title="Chỉ Khởi Động Lại Trợ Lý Vietbot" value="restart_vietbot_checked" checked>
                        </td>
                    </tr>
					<tr>
									     <th colspan="3">Thông Báo Âm Thanh:</th>
						<td><input type="checkbox" class="form-check-input" title="Thông Báo Bằng Âm Thanh Khi Cập Nhật Được Hoàn Tất" name="audioo_playmp3_success" value="playmp3_success" checked></td>
						</tr>
						
					<tr>
					<th colspan="3"><span class="inline-elements" title="Tự Động Tải Lại Trang Khi Cập Nhật Hoàn Tất">Tự Động Làm Mới Lại Trang: <font color=red><span id="countdown"></span></font></span></th>
						<td><input type="checkbox" class="form-check-input" name="startCheckboxReload" id="startCheckbox" title="Tự Động Tải Lại Trang Khi Cập Nhật Hoàn Tất" value="start"></td>
						</tr>
						
             
					
                </table>
				           
            </div>            
        </div>
   <div class="row g-3 d-flex justify-content-center">
            <div class="col-auto">
                                <div class="input-group">
								  
                                    <input type="submit" name="checkforupdates" class="btn btn-success" title="Kiểm Tra Phiên Bản Vietbot Mới" value="Kiểm Tra">
                                    <input type="submit" name="backup_update" class="btn btn-warning" title="Cập Nhật Lên Phiên Bản Vietbot Mới" value="Cập Nhật">
                                    <a class="btn btn-primary" href="<?php echo $PHP_SELF; ?>" role="button">Làm Mới</a>
									 <button class="btn btn-danger" id="reloadButton">Tải Lại Trang</button>
                                    <button type="submit" name="restart_vietbot" class="btn btn-dark" title="Khởi Động Lại Trợ Lý VietBot">Restart VietBot</button>
                                </div>
                                </div>
								
                            </div><br/>
							
							</div>
<br/>

<br/>
<div class="my-div">
    <span class="corner-text"><h5>Sao Lưu/Khôi Phục:</h5></span>
    <br/>
    <br/>
    <center>
        <div id="message"></div>
    </center>
    <br/>
	<?php
	    // Lấy danh sách các tệp tin sao lưu
    $files = glob($backupDir . '/*.tar.gz');
    if (count($files) === 0) {
        echo '<center>Không có tệp tin sao lưu nào</center>';
    } else {
		?>
        <div class="row justify-content-center"><div class="col-auto"><div class="input-group">
  <?php
  // Tạo danh sách tệp tin sao lưu dưới dạng select dropdown
  echo '<select class="form-select" name="selectedFile"><option value="">Chọn file backup</option>';
        foreach ($files as $file) {
            $filename = basename($file);
            echo '<option value="' . $filename . '">' . $filename . '</option>';
        }
        echo '</select>';
        echo '<input type="submit" name="download" class="btn btn-primary" value="Tải xuống">';
        echo ' <input type="submit" name="restore" class="btn btn-warning" value="Khôi Phục">';
    }
	?>
</div></div></div><br/></div></form>
<br/> <p class="right-align"><b>Phiên bản Vietbot:  <font color=red><?php echo $dataVersionVietbot->vietbot_version->latest; ?></font></b></p>
	<?php
// Xử lý tải xuống tệp tin được chọn
if (isset($_POST['download']) && isset($_POST['selectedFile'])) {
    $selectedFile = $_POST['selectedFile'];
    $filePath = '/backup_update/backup/' . $selectedFile; // Đường dẫn đến thư mục chứa tệp tin
	    if (!empty($selectedFile)) {
        // Tạo liên kết tới trang mục tiêu trong tab mới
        $targetLink = "http://$serverIP$filePath"; // Đặt đường dẫn mục tiêu tại đây
        echo "<script>window.open('$targetLink',  '_blank');</script>";
    } else {
        // Xử lý khi $selectedFile không có giá trị
        $message .= '<font color=red>Không có tệp tin được chọn để tải xuống</font>';
    }
}
?>
<br/>
<?php
if (isset($_POST['checkforupdates'])) {
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://'.$serverIP.':5000',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{"type": 3,"data": "vietbot_version"}',
  CURLOPT_HTTPHEADER => array(
    'Accept: */*',
    'Accept-Language: vi',
    'Connection: keep-alive',
    'Content-Type: application/json',
    'DNT: 3',
    'Origin: http://'.$serverIP,
    'Referer: http://'.$serverIP.'/',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36'
  ),
));
$response = curl_exec($curl);
curl_close($curl);
$data = json_decode($response, true);
// Kiểm tra kết quả từ yêu cầu cURL
if (!empty($data) && isset($data['result'])) {
  $currentresult = $data['result'];
} else {
  // Lấy dữ liệu "latest" từ tệp tin version.json cục bộ
  $localJson = file_get_contents($DuognDanThuMucJson.'/version.json');
  $localData = json_decode($localJson, true);
  $currentresult = $localData['vietbot_version']['latest'];
}
$gitJson = file_get_contents($Vietbot_Version);
$gitData = json_decode($gitJson, true);
$latestVersion = $gitData['vietbot_version']['latest'];
// So sánh giá trị "vietbot_version" từ cURL và từ GitHub
if ($currentresult === $latestVersion) {
  $messagee .= "Bạn đang sử dụng phiên bản mới nhất: <font color=red>" . $currentresult . "</font>";
} else {
  $messagee .= "Có phiên bản mới: <font color=red>" . $latestVersion.'</font><br/>';
  $messagee .= "Phiên bản hiện tại: <font color=red>" . $currentresult.'</font><br/><br/>';
	if (empty($gitData['new_features'])) {
    //echo "Không có dữ liệu";
	} else {
    $messagee .= 'Tính năng mới: <font color=red>'.$gitData['new_features'].'</font><br/>';
	}
	
	if (empty($gitData['bug_fixed'])) {
    //echo "Không có dữ liệu";
	} else {
    $messagee .= 'Sửa lỗi: <font color=red>'.$gitData['bug_fixed'].'</font><br/>';
	}
	
	if (empty($gitData['improvements'])) {
    //echo "Không có dữ liệu";
	} else {
    $messagee .= 'Cải thiện: <font color=red>'.$gitData['improvements'].'</font><br/>';
	}
	

	if (empty($gitData['update_command'])) {
    //echo "Không có dữ liệu";
	} else {
    $messagee .= 'Lệnh Cần Bổ Sung $:> <font color=red>'.$gitData['update_command'].'</font><br/>';
	}
}
}
if (isset($_POST['backup_update'])) {
	if (isset($block_updates_vietbot_program) && $block_updates_vietbot_program === true) {
        //echo "Checkbox được tích và không cho cập nhật.";
        $messagee .= '<font color=red>Cập Nhật Phần Mềm Đã Bị Tắt, Cần Đi Tới <b><i>Tab Cấu hình Config</i></b> Để Bỏ Tích</font>';
    } else {
//Coppy file config, skill và chmod ra bộ nhớ tạm để lấy và thay thế các value giống nhau
exec("cp $DuognDanThuMucJson/config.json $DuognDanUI_HTML/backup_update/backup/config_.json");
exec("cp $DuognDanThuMucJson/skill.json $DuognDanUI_HTML/backup_update/backup/skill_.json");
exec("cp $DuognDanThuMucJson/state.json $DuognDanUI_HTML/backup_update/backup/state_.json");
exec("chmod 777 $DuognDanUI_HTML/backup_update/backup/config_.json");
exec("chmod 777 $DuognDanUI_HTML/backup_update/backup/skill_.json");
exec("chmod 777 $DuognDanUI_HTML/backup_update/backup/state_.json");
//END Coppy
/////////////////////
        // Tạo lệnh để nén thư mục
		$tarCommand = "tar -czvf " . $backupFile . " -C $Path_Vietbot_src resources src";
        exec($tarCommand, $output, $returnCode);
        if ($returnCode === 0) {
            chmod($backupFile, 0777);
         //$messagee .= 'Tạo bản sao lưu thành công\n';
            // Xóa các file cũ nếu số lượng tệp tin sao lưu vượt quá giới hạn
            $backupFiles = glob($backupDir . '/*.tar.gz');
            $numBackupFiles = count($backupFiles);
            if ($numBackupFiles > $maxBackupFiles) {
                // Sắp xếp tệp tin sao lưu theo thời gian tăng dần
                usort($backupFiles, function ($a, $b) {
                    return filemtime($a) - filemtime($b);
                });
                // Xóa các tệp tin cũ nhất cho đến khi số lượng tệp tin sao lưu đạt đến giới hạn
                $filesToDelete = array_slice($backupFiles, 0, $numBackupFiles - $maxBackupFiles);
                foreach ($filesToDelete as $file) {
                    unlink($file);
                   //  $messaege .= 'Backup đã đạt giới hạn, đã xóa tệp tin cũ: ' . $file . '\n';
				//;
                }
            }
        } else {
            $messagee .= 'Có lỗi xảy ra khi tạo bản sao lưu. Thư mục <font color=red>resources</font> hoặc <font color=red>src</font> không tồn tại.<br/>';
        }
		if (!file_exists($PathResources)) {
    // Nếu không tồn tại, tạo mới thư mục
    if (!mkdir($PathResources, 0777, true)) {
       // die('Không thể tạo thư mục');
    } else {
        // Nếu tạo mới thành công, đặt quyền chmod 777 cho thư mục
        chmod($PathResources, 0777);
       // echo 'Tạo và đặt quyền thành công!';
    }
}
///////////////////////
		
    $excludedFiles = [];
    $excludedDirectories = [];
    $deletedItems = [];
    $copiedItems = [];
    if (isset($_POST['exclude'])) {
        foreach ($_POST['exclude'] as $item) {
            if (is_file($DuognDanThuMucJson . '/' . $item)) {
                $excludedFiles[] = $item;
            } elseif (is_dir($DuognDanThuMucJson . '/' . $item)) {
                $excludedDirectories[] = $item;
            }
        }
    }
    deleteFiles($DuognDanThuMucJson, $excludedFiles, $excludedDirectories, $deletedItems);
    $downloadLink = "$GitHub_VietBot_OFF/archive/beta.zip";
    $zipFile = 'vietbot_offline.zip';
    $zipFilePath = $DuognDanUI_HTML.'/backup_update/update' . $zipFile;
    if (copy($downloadLink, $zipFilePath)) {
        $zip = new ZipArchive;
        if ($zip->open($zipFilePath) === true) {
            $zip->extractTo($DuognDanUI_HTML.'/backup_update/extract');
            $zip->close();
            $sourceDirectory = $DuognDanUI_HTML.'/backup_update/extract/vietbot_offline-beta/src';
			$sourceDirectoryyy = $DuognDanUI_HTML.'/backup_update/extract/vietbot_offline-beta/resources';
            copyFiles($sourceDirectory, $DuognDanThuMucJson, $excludedFiles, $excludedDirectories, $copiedItems);
			copyFiles($sourceDirectoryyy, $PathResources, $excludedFiles, $excludedDirectories, $copiedItems);
            $messagee .= '<font color=red>Đã tải xuống phiên bản Vietbot mới và cập nhật thành công!</font><br/>';
			shell_exec("rm -rf $DuognDanUI_HTML/backup_update/extract/vietbot_offline-beta");
			?>
			<div class="form-check form-switch d-flex justify-content-center"> 
			<div class="container"><div class="row"><div class="col div-div1 scrollable-menu">
			<?php
            if (!empty($deletedItems)) {
              //  echo '<font color="red"><b>Các file/thư mục đã xóa để cập nhật:</b></font><br/>';
                foreach ($deletedItems as $item) {
                  //  echo $item . '<br/>';
                }
            }
			?>
			</div><div class="col div-div1 scrollable-menu">
			<?php
            if (!empty($copiedItems)) {
              //  echo '<font color="green"><b>Các file/thư mục đã được cập nhật:</b></font><br>';
                foreach ($copiedItems as $item) {
                //    echo $item . '<br/>';
                }
            }
			?>
			</div></div></div></div>
			<?php
        } else {
            $messagee .=  '<font color=red>Có lỗi xảy ra, không thể giải nén tệp tin cập nhật.</font><br/>';
        }
        unlink($zipFilePath);
    } else {
        $messagee .=  '<font color=red>Có lỗi xảy ra, không thể tải xuống tệp tin cập nhật.</font><br/>';
    }
/////////////////////////////
if (@$_POST['restart_vietbot_checked'] === "restart_vietbot_checked") {
    $actionCommand = "systemctl --user restart vietbot";
    $messagee .= '<font color=red>Đang Restart lại Vietbot, vui lòng chờ Vietbot khởi động lại!</font>';
} elseif (@$_POST['reboot_checked'] === "reboot_checked") {
    $actionCommand = "sudo reboot";
    $messagee .= '<font color=red>Đang Reboot hệ thống, vui lòng chờ hệ thống khởi động lại!</font>';
} else {
	$actionCommand = "uname";
    $messagee .= '<font color=red>Hãy Restart lại Vietbot để áp dụng cập nhật mới.</font>';
}
///////////////////////////////////////
// thay thế các giá trị config từ cũ sang mới
$oldConfigPath = $DuognDanUI_HTML.'/backup_update/backup/config_.json';
$oldConfigContent = file_get_contents($oldConfigPath);
// Đọc nội dung từ tệp config1.json
$newConfigPath = $DuognDanThuMucJson.'/config.json';
$newConfigContent = file_get_contents($newConfigPath);
$oldConfigData = json_decode($oldConfigContent, true);
$newConfigData = json_decode($newConfigContent, true);
function recursiveReplace(&$oldArray, $newArray) {
    foreach ($newArray as $key => $newValue) {
        if (is_array($newValue) && isset($oldArray[$key]) && is_array($oldArray[$key])) {
            recursiveReplace($oldArray[$key], $newValue);
        } else {
            replaceIfDifferent($oldArray[$key], $newValue);
        }
    }
}
function replaceIfDifferent(&$oldValue, $newValue) {
    if (isset($newValue)) {
        $oldValue = $newValue;
    } 
	

	
}
recursiveReplace($newConfigData, $oldConfigData);
$newConfigUpdatedContent = json_encode($newConfigData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
file_put_contents($newConfigPath, $newConfigUpdatedContent);
//Xử Lý Riêng logging_type chọn là Null
//$GET_LOG_Null = $oldConfigData['smart_config']['logging_type'];
$newConfigData['smart_config']['logging_type'] = $oldConfigData['smart_config']['logging_type'];
    $new_json_data_config = json_encode($newConfigData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    file_put_contents($newConfigPath, $new_json_data_config);
//END
// thay thế các giá trị Skill từ cũ sang mới
$oldSkillPath = $DuognDanUI_HTML.'/backup_update/backup/skill_.json';
$oldSkillContent = file_get_contents($oldSkillPath);
$newSkillPath = $DuognDanThuMucJson.'/skill.json';
$newSkillContent = file_get_contents($newSkillPath);
$oldSkillData = json_decode($oldSkillContent, true);
$newSkillData = json_decode($newSkillContent, true);
function recursiveReplaceSkill(&$oldArray, $newArray) {
    foreach ($newArray as $key => $newValue) {
        if (is_array($newValue) && isset($oldArray[$key]) && is_array($oldArray[$key])) {
            recursiveReplaceSkill($oldArray[$key], $newValue);
        } else {
            replaceIfDifferentSkill($oldArray[$key], $newValue);
        }
    }
}
function replaceIfDifferentSkill(&$oldValue, $newValue) {
    if (isset($newValue)) {
        $oldValue = $newValue;
    }
}
recursiveReplaceSkill($newSkillData, $oldSkillData);
$newSkillUpdatedContent = json_encode($newSkillData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
file_put_contents($newSkillPath, $newSkillUpdatedContent);
/////////////////////////////////////////////////////////////
//Thay Thế Giá Trị Volume ở file state.json từ cũ sang mới
$input_json = file_get_contents($DuognDanUI_HTML.'/backup_update/backup/state_.json');
$data_State = json_decode($input_json, true);
$volume_State_value = $data_State['volume'];
$data_State['volume'] = $volume_State_value;
$output_State_json = json_encode($data_State, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
file_put_contents($DuognDanThuMucJson.'/state.json', $output_State_json);
////End thay thế các giá trị

//Chmod 777 khi chạy xong backup
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die($E_rror_HOST);}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die($E_rror);}
$stream1 = ssh2_exec($connection, "sudo chmod -R 0777 $Path_Vietbot_src");
$stream2 = ssh2_exec($connection, "sudo chown -R pi:pi $Path_Vietbot_src");
$stream3 = ssh2_exec($connection, "$actionCommand");
stream_set_blocking($stream1, true); 
stream_set_blocking($stream2, true); 
stream_set_blocking($stream3, true); 
$stream_out1 = ssh2_fetch_stream($stream1, SSH2_STREAM_STDIO); 
$stream_out2 = ssh2_fetch_stream($stream2, SSH2_STREAM_STDIO); 
$stream_out3 = ssh2_fetch_stream($stream3, SSH2_STREAM_STDIO); 
stream_get_contents($stream_out1); 
stream_get_contents($stream_out2); 
stream_get_contents($stream_out3); 

exec("rm $DuognDanUI_HTML/backup_update/backup/config_.json");
exec("rm $DuognDanUI_HTML/backup_update/backup/skill_.json");
exec("rm $DuognDanUI_HTML/backup_update/backup/state_.json");

//Play Mp3 khi cập nhật hoàn tất
if (@$_POST['audioo_playmp3_success'] === "playmp3_success") {
	echo '<audio style="display: none;" id="myAudio_success" controls autoplay>';
    echo '<source src="../assets/audio/vietbot_update_success.mp3" type="audio/mpeg">';
    echo 'Your browser does not support the audio element.';
    echo '</audio>';
	echo '<script>';
	echo 'var audio = document.getElementById("myAudio_success");';
    echo 'audio.play();';
	echo '</script>';
}
$startCheckboxReload = $_POST['startCheckboxReload'];






}
}





//Dowload backup restor
//Chọn file backup và restore
if (isset($_POST['restore']) && isset($_POST['selectedFile'])) {
    $selectedFile = $_POST['selectedFile'];
	$sourceDirectory = $DuognDanUI_HTML.'/backup_update/extract/src';
	$sourceDirectoryyy = $DuognDanUI_HTML.'/backup_update/extract/resources';
 //   echo 'Tiến Hành Khôi Phục File: '.$selectedFile.'<br/>';
 if (!empty($selectedFile)) {
//Giải nén backup
	$archivePath = $DuognDanUI_HTML.'/backup_update/backup/'.$selectedFile;
	$extractPath = $DuognDanUI_HTML.'/backup_update/extract';
// Kiểm tra xem tệp tin nén tồn tại
if (file_exists($archivePath)) {
    // Kiểm tra xem đường dẫn giải nén tồn tại, nếu không tồn tại thì tạo mới
    if (!file_exists($extractPath)) {
        mkdir($extractPath, 0777, true);
    }
// Giải nén tệp tin
    $command = "tar -xvf $archivePath -C $extractPath";
    exec($command);
// Kiểm tra kết quả giải nén
    if (file_exists($extractPath)) {
        //$message .= 'Giải nén thành công: '.$selectedFile.'\n';
    } else {
        $message .= 'Có lỗi xảy ra khi giải nén: <font color=red>'.$selectedFile.'</font>';
    }
} else {
    $message .= 'Tệp tin giải nén không tồn tại: <font color=red>'.$selectedFile.'</font>';
	
}
//End giải nén backup
$excludedFiles = array('excluded_file_VUTUYEN.txt'); //Bỏ Qua File không coppy giai đoạn thử nghiệm
$excludedDirectories = array('excluded_file_VUTUYEN'); //Bỏ Qua thư mục không coppy giai đoạn thử nghiệm
$copiedItems = array();
copyFiles($sourceDirectory, $DuognDanThuMucJson, $excludedFiles, $excludedDirectories, $copiedItems);
copyFiles($sourceDirectoryyy, $PathResources, $excludedFiles, $excludedDirectories, $copiedItems);
 $message .= '<font color=red>Khôi phục bản sao lưu thành công</font>';
?>
<div class="form-check form-switch d-flex justify-content-center"> 
<div class="container">
<div class="row">
<div class="col div-div1 scrollable-menu">
<?php
//echo  '<font color="green"><b>Đã khôi phục Firmware Vietbot thành công file:</b></font><br/>';
// Hiển thị danh sách các tệp tin đã được sao chép
foreach ($copiedItems as $copiedItem) {
    //echo  '<font color="green">Đã khôi phục thành công file: </font>'.$copiedItem.'<br/>';
   // echo  $copiedItem.'<br/>';
}
?>
</div>
<?php
//Xóa Nội dung đã giải nén khi restore
$excludedFilesrestore = array('README.md'); //bỏ qua file không bị xóa
$excludedDirectories = array('excluded_file_VUTUYEN'); // bỏ qua thư mục không bị xóa giai đoạn thử nghiệm
$deletedItems = array();
deleteFiles($sourceDirectory, $excludedFilesrestore, $excludedDirectories, $deletedItems);
deleteFiles($sourceDirectoryyy, $excludedFilesrestore, $excludedDirectories, $deletedItems);
?>

 <div class="col div-div1 scrollable-menu">
<?php
//echo '<font color="red"><b>Đã xóa các file khôi phục trong bộ nhớ tạm:</b></font><br/>';
// Hiển thị danh sách các tệp tin và thư mục đã bị xóa
foreach ($deletedItems as $deletedItem) {
   // echo $deletedItem . '<br/>';
}
    } else {
        // Xử lý khi $selectedFile không có giá trị
        $message .= "<font color=red>Không có tệp tin được chọn để tiến hành khôi phục.</font>";
    }	
//Chmod 777 khi restor xong backup
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





}
	?>
</div>
	</div>
	</div>
	</div>
	    <script>
        var messageElement = document.getElementById("message");
        var message = "<?php echo $message; ?>";
        messageElement.innerHTML = message;

        var messageElementt = document.getElementById("messagee");
        var messagee = "<?php echo $messagee; ?>";
        messageElementt.innerHTML = messagee;
    </script>
	 <script>
        const checkboxes = document.querySelectorAll('.single-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                if (checkbox.checked) {
                    checkboxes.forEach(otherCheckbox => {
                        if (otherCheckbox !== checkbox) {
                            otherCheckbox.checked = false;
                        }
                    });
                }
            });
        });
    </script>
	
	<script>
var reloadButton = document.getElementById('reloadButton');
var startCheckbox = document.getElementById('startCheckbox');
var countdownElement = document.getElementById('countdown');
var requiredValue = "<?php echo $startCheckboxReload; ?>";
var countdown = "<?php echo $Page_Load_Time_Countdown; ?>";
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
