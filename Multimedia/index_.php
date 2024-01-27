<?php
// Code By: Vũ Tuyển
// Facebook: https://www.facebook.com/TWFyaW9uMDAx
//error_reporting(E_ALL);
require_once '../assets/lib_php/getid3/getid3.php';
?>
<script>
    //icon Loading
    $(document).ready(function() {
        $('#my-form').on('submit', function() {
            // Hiển thị biểu tượng loading
            $('#loading-overlay').show();
            // Vô hiệu hóa nút gửi
            $('#submit-btn').attr('disabled', true);
        });
    });
	</script>
	
<?php

$cfg_action_json = "$DuognDanUI_HTML/Multimedia/cfg_action.json";
if (!file_exists($cfg_action_json)) {
    // Tạo mới tệp
    $file_content_action = json_encode(['music_source' => 'ZingMp3'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    file_put_contents($cfg_action_json, $file_content_action);
    chmod($cfg_action_json, 0777);
    //echo "Tệp $cfg_action_json đã được tạo mới và quyền chmod đã được thiết lập thành 0777.";
}

$Data_CFG_ACTION = json_decode(file_get_contents($cfg_action_json), true);

//echo $Data_CFG_ACTION['music_source'];

if (!is_numeric($sync_media_player_sync_delay) || $sync_media_player_sync_delay < 1 || $sync_media_player_sync_delay > 5) {
    // Nếu không nằm trong khoảng, thiết lập giá trị mặc định là 1
    $sync_media_player_sync_delay = 1;
}
// Kiểm tra giá trị của biến $sync_music_stream
if ($sync_music_stream != 'web_ui' && $sync_music_stream != 'mic') {
    // Nếu không phải là 'web_ui' hoặc 'mic', đặt giá trị mặc định là 'mic'
    $sync_music_stream = 'mic';
}

function install_source_node($DuognDanUI_HTML,$serverIP,$SSH_Port,$SSH_TaiKhoan,$SSH_MatKhau,$E_rror_HOST,$E_rror) {
	
		$url = 'https://raw.githubusercontent.com/marion001/Google-APIs-Client-Library-PHP/main/node_modules.tar.gz';
		$destination = $DuognDanUI_HTML.'/assets/lib_php/node_modules.tar.gz';
		$extractedFolderPath = $DuognDanUI_HTML.'/assets/lib_php/';
		// Tải file từ URL
		$fileContent = file_get_contents($url);

		if ($fileContent !== false) {
		// Lưu nội dung vào file đích
			$result = file_put_contents($destination, $fileContent);

			if ($result !== false) {
				//echo 'Dữ liệu đã được tải xuống thành công và lưu vào ' . $destination.'<br/>';
				echo '<center>Dữ liệu đã được tải xuống thành công</center>';
				$phar = new PharData($destination);
				$phar->extractTo($extractedFolderPath, null, true);  // Tham số thứ ba (true) cho phép ghi đè
				//echo "Tệp dữ liệu đã được cấu hình thành công vào $extractedFolderPath <br/>Hãy tải lại trang để áp dụng<br/>";
				echo "<center>Tệp dữ liệu đã được cấu hình thành công. <br/>Hãy tải lại trang để áp dụng<br/><center>";
				chmod($extractedFolderPath . 'node_modules', 0777);
				shell_exec("rm $destination");
				$connection = ssh2_connect($serverIP, $SSH_Port);
				if (!$connection) {
				die($E_rror_HOST);
				}
				if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {
				die($E_rror);
				}
				$stream = ssh2_exec($connection, 'sudo mv '.$DuognDanUI_HTML.'/assets/lib_php/node_modules /home/pi/node_modules');
				stream_set_blocking($stream, true);
				$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
				stream_get_contents($stream_out);
				echo '<center><a href="index.php"><button type="submit" class="btn btn-danger">Tải Lại</button></a></center>';
				} 
			else {
        echo 'Lỗi khi lưu nội dung tệp cấu hình vào ' . $destination;
		echo '<br/><center><a href="index.php"><button type="submit" class="btn btn-danger">Tải Lại</button></a></center>';
			}
		} else {
			echo 'Lỗi khi tải xuống tệp cấu hình từ ' . $url;
			echo '<br/><center><a href="index.php"><button type="submit" class="btn btn-danger">Tải Lại</button></a></center>';
		}
		exit();
}

if (isset($_POST['install_lib_node_js'])) {
	
	$connection = ssh2_connect($serverIP, $SSH_Port);
    if (!$connection) {
        die($E_rror_HOST);
    }
    if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {
        die($E_rror);
    }
	//cập nhật nguồn trước khi cài thư viện node js
    $stream1 = ssh2_exec($connection, 'sudo apt update');
    stream_set_blocking($stream1, true);
    $stream_out1 = ssh2_fetch_stream($stream1, SSH2_STREAM_STDIO);
    stream_get_contents($stream_out1);
	
    $stream = ssh2_exec($connection, 'sudo apt install nodejs -y');
    stream_set_blocking($stream, true);
    $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
    stream_get_contents($stream_out);
install_source_node($DuognDanUI_HTML,$serverIP,$SSH_Port,$SSH_TaiKhoan,$SSH_MatKhau,$E_rror_HOST,$E_rror);

}


// Kiểm tra xem Node.js đã được cài đặt chưa
$nodeCheck = shell_exec('node -v');
if (empty($nodeCheck)) {
    //echo 'Node.js chưa được cài đặt.<br>';
		echo '<br/><br/><center><form method="POST" id="my-form" action="">';
		echo "<button name='install_lib_node_js' class='btn btn-success'>Cấu Hình Media</button>";
		echo "<a href='$PHP_SELF'><button class='btn btn-primary'>Làm Mới</button></a></center>";
		echo "</form></center>";
    exit;
} else {
    //echo 'Node.js đã được cài đặt. Phiên bản: ' . $nodeCheck . '<br>';
$directory = '/home/pi';
// Kiểm tra xem thư mục node_modules tồn tại hay không
if (is_dir($directory . '/node_modules')) {
    //echo 'Thư mục node_modules tồn tại.<br>';
} else {
    //echo 'Thư mục node_modules không tồn tại.<br>';
	install_source_node($DuognDanUI_HTML,$serverIP,$SSH_Port,$SSH_TaiKhoan,$SSH_MatKhau,$E_rror_HOST,$E_rror);
	
}



	
	
}
?>
<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <form method="post" id="my-form" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="3" scope="col">
                                <center>Chọn Nguồn Nhạc:</center>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
<td><center>
                                <!-- Checkbox với giá trị "keymp3" -->
                                <input type="radio" id="LocalMp3" name="action" value="Local" title="Tìm kiếm trên thiết bị" onchange="handleRadioChangeLocal()">
                                <label for="LocalMp3" title="Tìm kiếm trên thiết bị">Local MP3</label>
                           </center> </td>
                            <td><center>
                                <!-- Checkbox với giá trị "keymp3" -->
                                <input type="radio" id="keyzingmp3" name="action" value="ZingMp3" title="Tìm kiếm trên ZingMp3" onchange="handleRadioChangeLocal()">
                                <label for="keyzingmp3" title="Tìm kiếm trên ZingMp3">Zing MP3</label>
                            </center> </td>

                            <td><center>
                                <!-- Checkbox với giá trị "keyyoutube" -->
                                <input type="radio" id="keyyoutube" name="action" value="Youtube"  onchange="handleRadioChangeLocal()">
                                <label for="keyyoutube" title="Tìm kiếm trên youtube">YouTube</label>
                             </center></td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div class="form-group mb-2">
								<div class="form-group mx-sm-3 mb-2">
                                  <center>    <input type="text" id="tenbaihatInput" class="form-control" title="Nhập tên bài hát, link Youtube, hoặc link mp3: https://zxc.com/1.mp3" name="tenbaihat" placeholder="Nhập Tên Bài Hát, link.mp3, link youtube" aria-label="Recipient's username" aria-describedby="basic-addon2" oninput="handleInputHTTP()">
                                    </center> </div>
                                      <center>  <button class="btn btn-primary" id="TimKiem" type="submit" title="Tìm kiếm bài hát">Tìm Kiếm</button>
                                       
                                   

                                        <button type="button" id="submitButton" class="ajax-button btn btn-success" data-song-kichthuoc="---" data-song-thoiluong="---" data-song-link_type="direct" data-song-artist="---" data-song-images="../assets/img/NotNhac.png" data-song-name="Không có dữ liệu" data-song-id="" value="" hidden>Play .Mp3</button>
                                    

                                        <a class="btn btn-danger" href="<?php echo $PHP_SELF; ?>" role="button" title="Làm mới lại trang">Làm Mới</a> 
                                   </center>
                                </div></form>
								
<div id="UpLoadFileMp3" hidden>				
<form method="post" id="uploadmp3local" action="<?php echo $_SERVER['PHP_SELF']; ?>"  enctype="multipart/form-data">				
<div class="input-group" >

  <div class="custom-file">
	<input type="file" class="form-control" name="mp3Files[]" id="mp3File" max="<?php echo $maxFilesUploadMp3; ?>" multiple accept=".mp3" required>
	<input type="hidden" name="action" value="UploadMp3">
  </div>
  <div class="input-group-append">
    <button class="btn btn-primary" type="submit" title="Tải lên file mp3">Tải Lên</button>
  </div> 
</div> </form><font color=blue>Chọn tối đa: 20 File, Max 300MB/1 File</font>
</div>

                            </td>

                        </tr>

           


 <tr>
<th colspan="3" scope="col"></th></tr>
            <tr>
			
                <td colspan="3"><center>
<div id="code-section">
 <div id="infomusicplayer"> </div>
<b><p id="media1-name"></p></b>
    <span id="selected-time"></span>
    <input type="range" id="time-slider" min="1" max=""> 
	<span id="media1-duration"></span>
    <p id="player-state">Trạng thái: Đang đồng bộ...</p>
</div>
<center>
                        <div id="messagee"></div>
                    </center>
				
                    </center>
                </td>

            </tr>
		
  <tr>
    <td rowspan="2" colspan="2"><center>
	
						<div>
  <i id="volumeIcon" class="bi bi-volume-up-fill"></i>
  <input type="range" id="volume" name="volume" min="0" max="100" value="" oninput="updateVolume(this.value)">
  <span id="currentVolume">...</span>%
</div><br/>
	
	
                        <p>
                        <button type="button" id="playButton" title="Phát nhạc" class="btn btn-success"><i class="bi bi-play-circle"></i>
                        </button>
                        <button type="button" id="pauseButton" title="Tạm dừng phát nhạc" class="btn btn-warning"><i class="bi bi-pause-circle"></i>
                        </button>
                        <button type="button" id="stopButton" title="Dừng phát nhạc" class="btn btn-danger"><i class="bi bi-stop-circle"></i>
                        </button></p>
						<!--<p>
						<button type="button" id="volumeDown" title="Giảm âm lượng" class="btn btn-info"><i class="bi bi-volume-down"></i>
                        </button>
                        <button type="button" id="volumeUp" title="Tăng âm lượng" class="btn btn-info"><i class="bi bi-volume-up"></i>
                        </button></p> -->
					
                    </center>
					
					
					
					
					

					
					</td>
						
   
  </tr>
  <tr>
 <td><center><label for="run-checkbox" class="btn btn-warning" title="Bạn có thể cấu hình mặc định trong tab Skill->Media Player">
 <input title="Bạn có thể cấu hình mặc định trong tab Skill->Media Player" type="checkbox" id="run-checkbox" <?php echo ($sync_media_player_checkbox) ? 'checked' : ''; ?>> Đồng bộ </label>
<i class="bi bi-info-circle-fill" onclick="togglePopupSync()" title="Nhấn Để Tìm Hiểu Thêm"></i>
</center></td>
  </tr>
<div id="popupContainer" class="popup-container" onclick="hidePopupSync()">
    <div id="popupContent" onclick="preventEventPropagationSync(event)">
        <p><b><center>Đồng bộ Trạng Thái Media Player của Loa với Web UI</center></b></p>
		- <b>Tự Động Sync: </b> Truy cập <b>Tab Skill</b> -> <b>Media Player</b> -> <b>Đồng Bộ (Sync)</b> -> tích chọn <b>Đồng Bộ Media Với Web UI</b> -> <b>Lưu cấu hình</b><br/>
		- <b>Thủ Công:</b> Bạn có thể nhấn tích vào nút <b>Đồng Bộ</b> để Sync thủ công ngay tại tab <b>Media player</b><br/><br/>
		<i>Lưu Ý: Có thể ảnh hưởng đến tốc độ của Bot có phần cứng yêu nếu bật <b>Cài Đặt Tự Động Sync</b></i><br/>
        <center><button class="btn btn-info" type="button" onclick="hidePopupSync()">Đóng</button></center>
    </div>
</div>


            </tbody>
            </table>
        </div>
        <div class="col-sm-6">


            <div class="custom-div">
			<div id="messagee2">
			</div>
<?php
$api_Search_Zing = "http://ac.mp3.zing.vn/complete?type=song&num=20&query=";


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'UploadMp3') {
    $targetDirectory = $DuognDanThuMucJson.'/mp3/';
	$uploadedFilesSelect_name = array();
	$successCountUploadFile = 0; 
	//giới hạn file tải lên
	if (count($_FILES["mp3Files"]["name"]) > $maxFilesUploadMp3) {
        echo "<script>";
        echo "var messageElement = document.getElementById('messagee');";
        echo "messageElement.innerHTML = '<font color=red>Chỉ được phép tải lên tối đa $maxFilesUploadMp3 tệp tin</font>';";
        echo "</script>";
    } else {
    // Lặp qua mỗi file đã tải lên
    foreach ($_FILES["mp3Files"]["name"] as $key => $name) {
        $name_file_mp3 = basename($name);
		// Đổi tên file từ chữ in hoa thành chữ thường
		$name_file_mp3 = strtolower($name_file_mp3);
        $targetFile = $targetDirectory . $name_file_mp3;
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Kiểm tra định dạng file
        if ($fileType !== "mp3") {
            echo "<script>";
            echo "var messageElement = document.getElementById('messagee');";
            echo "messageElement.innerHTML = '<font color=red>Chỉ chấp nhận file có đuôi .mp3</font>';";
            echo "</script>";
            $uploadOk = 0;
        }

        // Kiểm tra xem file đã tồn tại chưa
        if (file_exists($targetFile)) {
            echo "<script>";
            echo "var messageElement = document.getElementById('messagee');";
            echo "messageElement.innerHTML = '<font color=red>File<b> $name_file_mp3 </b>đã tồn tại</font>';";
            echo "</script>";
            $uploadOk = 0;
        }

        // Kiểm tra kích thước file (giả sử giới hạn là 300MB)
        if ($_FILES["mp3Files"]["size"][$key] > $Upload_Max_Size * 1024 * 1024) {
            echo "<script>";
            echo "var messageElement = document.getElementById('messagee');";
            echo "messageElement.innerHTML = '<font color=red>File quá lớn, vui lòng chọn file dưới 300MB</font>';";
            echo "</script>";
            $uploadOk = 0;
        }

        // Kiểm tra trạng thái upload
        if ($uploadOk == 0) {
            echo "<script>";
            echo "var messageElement = document.getElementById('messagee');";
            echo "messageElement.innerHTML = '<font color=red>Không thể upload file, hoặc file đã tồn tại</font>';";
            echo "</script>";
			//return; // Dừng lại nếu không thành công
        } else {
            // Nếu mọi điều kiện ok, thực hiện upload
            if (move_uploaded_file($_FILES["mp3Files"]["tmp_name"][$key], $targetFile)) {
                chmod($targetFile, 0777);
				//liệt kê tên file vào bộ nhớ tạm
				$uploadedFilesSelect_name[] = $name_file_mp3;
				//đếm file tải lên ok
				$successCountUploadFile++;
            } else {
                echo "<script>";
                echo "var messageElement = document.getElementById('messagee');";
                echo "messageElement.innerHTML = '<font color=red>Có lỗi xảy ra khi tải lên file: <b>$name_file_mp3</b></font>';";
                echo "</script>";
            }
        }
    }
	        // Hiển thị các file được tải lên thành công
        if ($successCountUploadFile > 0) {
            echo "<script>";
            echo "var successElement = document.getElementById('messagee');";
            echo "successElement.innerHTML = '<font color=green>" .$successCountUploadFile. " File được tải lên thành công: <br/><b> " . implode("<hr/>", $uploadedFilesSelect_name) . "<b></font>';";
            echo "</script>";
        }
}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'Local') {
	
	$NguonNhac = $_POST['action'];
	// Cập nhật giá trị mới
    $Data_CFG_ACTION['music_source'] = $NguonNhac;
    // Ghi lại nội dung tệp JSON
    $Data_CFG_ACTION_new_music_source = json_encode($Data_CFG_ACTION, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    file_put_contents($cfg_action_json, $Data_CFG_ACTION_new_music_source);
	
    $directory = $DuognDanThuMucJson.'/mp3';
    $pattern = '*.mp3';
    $mp3Files = glob($directory . DIRECTORY_SEPARATOR . $pattern);
    $fileCount = count($mp3Files);

    if ($fileCount > 0) {
        echo "<center>Danh sách file MP3:</center><br/>";

        foreach ($mp3Files as $mp3File) {
           $getID3 = new getID3();
$fileInfo = $getID3->analyze($mp3File);

$duration = isset($fileInfo['playtime_seconds']) ? round($fileInfo['playtime_seconds']) : 'N/A';

            $fileSizeMB = round(filesize($mp3File) / (1024 * 1024), 2);

            echo " <div class='image-container'>";
            echo "<img src='../assets/img/NotNhac.png' class='imagesize' alt='' /> <div class='caption'>";
            echo '<b>Tên bài hát:</b> ' . basename($mp3File) . '<br/>';
           // echo '<b>Thời lượng:</b> ' . formatTimephp($duration) . '<br/>';
            echo '<b>Kích thước:</b> ' . $fileSizeMB . ' MB<br/>';
            echo '<button class="ajax-button btn btn-success" data-song-tenkenhnghesi="Nghệ Sĩ" data-song-data_type="'.$api_vietbot->playback_direct_music_api->payload->type.'" data-song-data_play_music="'.$api_vietbot->playback_direct_music_api->payload->data.'" data-song-kichthuoc="' . $fileSizeMB . ' MB" data-song-thoiluong="' . formatTimephp($duration) . '" data-song-artist=" ---" data-song-images="../assets/img/NotNhac.png" data-song-name="' . basename($mp3File) . '" data-song-link_type="'.$api_vietbot->playback_direct_music_api->payload->link_type.'" data-song-id="mp3/' . basename($mp3File) . '">Phát Nhạc</button>';
            echo '<button class="deleteBtn btn btn-danger" data-file="' . basename($mp3File) . '">Xóa File</button>';
            echo "</div></div><br/>";
        }

        echo "<script>";
        echo "var messageElementtt = document.getElementById('messagee2');";
        echo "messageElementtt.innerHTML = '<font color=green>Tổng số file MP3: <b>".$fileCount."</b> file</font>';";
        echo "</script>";
    } else {
        echo "<script>";
        echo "var messageElement = document.getElementById('messagee');";
        echo "messageElement.innerHTML = '<font color=red><b>Không tìm thấy file MP3 nào trong thư mục</b></font>';";
        echo "</script>";
    }
}




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'Youtube') {
    $Data_TenBaiHat = $_POST['tenbaihat'];
    $NguonNhac = $_POST['action'];
	// Cập nhật giá trị mới
    $Data_CFG_ACTION['music_source'] = $NguonNhac;
    // Ghi lại nội dung tệp JSON
    $Data_CFG_ACTION_new_music_source = json_encode($Data_CFG_ACTION, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    file_put_contents($cfg_action_json, $Data_CFG_ACTION_new_music_source);
	
	
    if (empty($Data_TenBaiHat)) {
        echo "<b><font color=red>Hãy nhập tên bài hát, nội dung cần tìm kiếm trên Youtube</font></b>";
    } else {

    
	
    $searchUrlYoutube = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=" . urlencode($Data_TenBaiHat) . "&maxResults=20&key=" . base64_decode($apiKeyYoutube);

/*
if (strpos($Data_TenBaiHat, 'http') !== false) {
    // Biến chứa "http", hiển thị thông báo và ngừng thực thi
  //  echo "Biến không được chứa 'http'";
  echo '<script>document.getElementById("messagee").innerHTML = "<font color=red><b>‼️ Nội dung tìm kiếm không được phép có \'http\'</b></font>";</script>';
    die();
}
*/

$curlYoutube = curl_init();
curl_setopt_array($curlYoutube, array(
  CURLOPT_URL => $searchUrlYoutube,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$responseYoutube = curl_exec($curlYoutube);

curl_close($curlYoutube);
//echo $responseYoutube;

if ($responseYoutube === false) {
//    echo json_encode(['error' => 'Yêu cầu cURL không thành công.']);
	
		echo "<script>";
		echo "var messageElement = document.getElementById('messagee');";
		echo "messageElement.innerHTML = '<font color=red><b>Yêu cầu cURL tìm kiếm youtube không thành công.</b></font>';";
		echo "</script>";
	
} else {
    $dataYoutube = json_decode($responseYoutube, true);

    // Kiểm tra xem có dữ liệu hay không
    if (empty($dataYoutube) || empty($dataYoutube['items'])) {
		//echo "$responseYoutube";
		//echo "Không có dữ liệu: ".$dataYoutube['error']['message'];
		
		echo "<script>";
		echo "var messageElement = document.getElementById('messagee');";
		echo "messageElement.innerHTML = '<font color=red><b>Không có dữ liệu trả về: " .$dataYoutube['error']['message']."</b></font>';";
		echo "</script>";
		
        
    } else {
        echo "<br/>Tên Bài Hát Đang Tìm Kiếm: <b><font color=red>" . $_POST['tenbaihat'] . "</font></b> | Nguồn Nhạc: <font color=red><b>" . $NguonNhac . "</b></font><hr/>";

        foreach ($dataYoutube['items'] as $itemYoutube) {
            $Youtube_title = $itemYoutube['snippet']['title'];
            $Youtube_description = $itemYoutube['snippet']['description'];
            $Youtube_channelTitle = $itemYoutube['snippet']['channelTitle'];
            $Youtube_videoId = $itemYoutube['id']['videoId'];
            $Youtube_images = $itemYoutube['snippet']['thumbnails']['high']['url'];
            $Youtube_videoLink = "https://www.youtube.com/watch?v=" . $Youtube_videoId;

            echo " <div class='image-container'>";
            echo "<img src='$Youtube_images' class='imagesize' alt='' /> <div class='caption'>";
            echo '<b>Tên bài hát:</b><a href="'.$Youtube_videoLink.'" target="_bank" style="color: black;" title="Mở trong Youtube"> ' . $Youtube_title . '</a><br/><b>Tên Kênh:</b> ' . $Youtube_channelTitle . '<br/>';
            //echo '<b>Mô tả:</b> ' . $Youtube_description . ' <br/>';
            //echo '<b>Link:</b> ' . $Youtube_videoLink . ' <br/>';
            echo '<button class="ajax-button btn btn-success" data-song-data_type="'.$api_vietbot->playback_youtube_music_api->payload->type.'" data-song-data_play_music="'.$api_vietbot->playback_youtube_music_api->payload->data.'" data-song-tenkenhnghesi="Tên Kênh" data-song-link_type="direct" data-song-artist="' . $Youtube_channelTitle . '" data-song-images="' .$Youtube_images.'" data-song-name="'  . $Youtube_title . '" data-song-kichthuoc=" ---" data-song-thoiluong=" ---" data-song-id="' . $Youtube_videoLink . '" >Phát Nhạc</button>';
            echo "</div></div><br/>";
        }
    }

}
}
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ZingMp3') {
    $Data_TenBaiHat = urlencode($_POST['tenbaihat']);
	$NguonNhac = $_POST['action'];
	// Cập nhật giá trị mới
    $Data_CFG_ACTION['music_source'] = $NguonNhac;
    // Ghi lại nội dung tệp JSON
    $Data_CFG_ACTION_new_music_source = json_encode($Data_CFG_ACTION, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    file_put_contents($cfg_action_json, $Data_CFG_ACTION_new_music_source);
	
	if (empty($Data_TenBaiHat)) {
    echo "<b><font color=red>Hãy nhập tên bài hát, nội dung cần tìm kiếm trên Zing MP3</font></b>";
} else {
    // Thực hiện các hành động khác nếu $Data_TenBaiHat có giá trị
	/*
	if (strpos($Data_TenBaiHat, 'http') !== false) {
    // Biến chứa "http", hiển thị thông báo và ngừng thực thi
    echo '<script>document.getElementById("messagee").innerHTML = "<font color=red><b>‼️ Nội dung tìm kiếm không được phép có \'http\'</b></font>";</script>';
  
    die();
}
*/
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $api_Search_Zing . $Data_TenBaiHat,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);
    curl_close($curl);
//echo $response;
if ($response === false) {
  //  echo json_encode(['error' => 'Yêu cầu cURL không thành công.']);
		echo "<script>";
		echo "var messageElement = document.getElementById('messagee');";
		echo "messageElement.innerHTML = '<font color=red><b>Yêu cầu cURL tìm kiếm ZingMp3 không thành công.</b></font>';";
		echo "</script>";
} else {
    $data = json_decode($response, true);
    // Kiểm tra xem có dữ liệu hay không
    if (empty($data)) {
        //echo "Không có dữ liệu trên ZingMp3.";
		echo "<script>";
		echo "var messageElement = document.getElementById('messagee');";
		echo "messageElement.innerHTML = '<font color=red><b>Không có dữ liệu trả về trên ZingMp3.</b></font>';";
		echo "</script>";
    } else {
        echo "Tên Bài Hát Đang Tìm Kiếm: <b><font color=red>" . $_POST['tenbaihat'] . "</font></b><br/>Nguồn Nhạc: <font color=red><b>" . $NguonNhac . "</b></font><hr/>";

        if ($data['result'] === true && isset($data['data'][0]['song'])) {
            foreach ($data['data'][0]['song'] as $song) {
                $ID_MP3 = $song['id'];
                $originalUrl = "http://api.mp3.zing.vn/api/streaming/audio/$ID_MP3/128";
                $img_images = "https://photo-zmp3.zmdcdn.me/" . $song['thumb'];
                echo " <div class='image-container'>";
                echo "<img src='$img_images' class='imagesize' alt='' /> <div class='caption'>";
                echo '<b>Tên bài hát:</b> ' . $song['name'] . '<br/><b>Nghệ sĩ:</b> ' . $song['artist'] . '<br/>';
                //echo 'ID bài hát: ' . $song['id'] . ' <br/>';
                echo '<button class="ajax-button btn btn-success" data-song-tenkenhnghesi="Nghệ Sĩ" data-song-kichthuoc="---" data-song-thoiluong="---" data-song-link_type="'.$api_vietbot->playback_zingmp3_music_api->payload->link_type.'" data-song-data_type="'.$api_vietbot->playback_zingmp3_music_api->payload->type.'" data-song-data_play_music="'.$api_vietbot->playback_zingmp3_music_api->payload->data.'" data-song-artist="' . $song['artist'] . '" data-song-name="' . $song['name'] . '" data-song-images="' . $img_images . '" data-song-id="' . $originalUrl . '">Phát Nhạc</button>';
                //echo "Original URL: $originalUrl<br>";
                // echo "MP3 128 URL: $finalUrl<br/><br/>";
                echo "</div></div><br/>";
            }
        } else {
            echo "Không có dữ liệu với từ khóa đang tìm kiếm trên ZingMp3";
        }
    }
}
    //exit; // Dừng xử lý ngay sau khi gửi dữ liệu JSON về trình duyệt
}
}

?>
      </div>
	</div>

  </div>
</div>
<!-- Đoạn mã JavaScript của bạn -->
<script>

    function truncateFileName(fileName, maxLength) {
        if (fileName.length <= maxLength) {
            return fileName;
        }

        // Tìm vị trí khoảng trắng gần giới hạn maxLength
        const lastSpaceIndex = fileName.lastIndexOf(' ', maxLength);

        // Nếu không có khoảng trắng, cắt tên file
        if (lastSpaceIndex === -1) {
            return fileName.substring(0, maxLength) + '...';
        }

        // Ngắt tên file tại khoảng trắng gần giới hạn maxLength
        return fileName.substring(0, lastSpaceIndex) + '...';
    }

    $(document).ready(function() {
        // Xử lý sự kiện khi nút Ajax được nhấn
        $('.ajax-button').on('click', function() {
			$('#loading-overlay').show();
			var messageElement = document.getElementById("messagee");
            var songId = $(this).data('song-id');
            var link_type = $(this).data('song-link_type');
            var data_play_music = $(this).data('song-data_play_music');
            var data_type = $(this).data('song-data_type');
            var songImages = $(this).data('song-images');
            var songTenKenhNgheSi = $(this).data('song-tenkenhnghesi');
            var songKichThuoc = $(this).data('song-kichthuoc');
            var songThoiLuong = $(this).data('song-thoiluong');
            var songArtist = $(this).data('song-artist');
            var songName = $(this).data('song-name');
            var startTime = new Date(); // Lấy thời gian bắt đầu yêu cầu
            var getTimee = formatTime(startTime.getHours()) + ':' + formatTime(startTime.getMinutes()) + ':' + formatTime(startTime.getSeconds());
			
			
			messageElement.innerHTML = '<font color=red>Đang Chuyển Đổi Dữ Liệu...</font>';
            if (!songId) {
                //alert('Không có dữ liệu cho songId');
                return; // Dừng thực thi nếu không có dữ liệu đầu vào
				messageElement.innerHTML = '<font color=red>Không Có Dữ Liệu Bài Hát songId...</font>';
            }
			
//Gửi thông tin tên bài hát và cover tới vietbot
var settings_cover_name = {
  "url": "http://<?php echo $serverIP; ?>:<?php echo $Port_Vietbot; ?>",
  "method": "POST",
  "timeout": 0,
  "headers": {
    "Content-Type": "application/json"
  },
  "data": JSON.stringify({
    "type": 2,
    "data": "set_song_info",
    "song_name": songName,
    "cover_link": songImages
  }),
};
$.ajax(settings_cover_name).done(function (response_cover_name) {
  //console.log(response_cover_name);
});

			
			
             //console.log('song id:', songId);
            $.ajax({
                url: '../include_php/Ajax/Get_Final_Url_ZingMp3.php?url=' + encodeURIComponent(songId),
                method: 'GET',
                dataType: 'json',
                success: function(response) {
					
                    if (response.finalUrl) {
						
                        var finalUrl = response.finalUrl;
                            //console.log('Final URL:', finalUrl);
							
                        // Phần còn lại của đoạn mã xử lý Ajax
                        var settings = {
                            "url": "http://<?php echo $serverIP; ?>:<?php echo $Port_Vietbot; ?>",
                            "method": "POST",
                            "timeout": <?php echo $Time_Out_MediaPlayer_API; ?> ,
                            "headers": {
                                "Content-Type": "application/json"
                            },
                            "data": JSON.stringify({
                                "type": data_type,
                                "data": data_play_music,
                                "link_type": link_type,
                                "link": finalUrl
                            }),
                        };
						messageElement.innerHTML = '<font color=red>Thực Thi Dữ Liệu Đã Chuyển Đổi...</font>';
                        // Gửi yêu cầu Ajax
                        $.ajax(settings)
                            .done(function(response) {
                                //var messageElement = document.getElementById("messagee");
                                var messageinfomusicplayer = document.getElementById("infomusicplayer");
                                let modifiedStringSuccess = response.state.replace("Success", "Thành Công");
                                var endTime = new Date(); // Lấy thời gian kết thúc yêu cầu
                                var elapsedTime = endTime - startTime; // Tính thời gian thực hiện yêu cầu
								
								const maxLengthhhh = 50;
								const truncatedFileNamesongName = truncateFileName(songName, maxLengthhhh);
								//hiển thị thẻ div  messagee
								messageElement.style.display = "block";
                                messageElement.innerHTML = '<div style="color: green;"><b>' + getTimee + ' - ' + modifiedStringSuccess + ' | ' + elapsedTime + 'ms</b></div>';
                                messageinfomusicplayer.innerHTML = '<div class="image-container"><div class="rounded-image"><img src=' + songImages + ' alt="" /></div><div class="caption"><ul><li><p style="text-align: left;"><b>Tên bài hát: </b> ' + truncatedFileNamesongName + '</p></li><li><p style="text-align: left;"><b>'+songTenKenhNgheSi+': </b> ' + songArtist + '</p></li><li><p style="text-align: left;"><b>Kích thước: </b> ' + songKichThuoc + '</p></li></ul></div></div>';
                            

							
							
							  if (messageElement) {
								// Sử dụng setTimeout để ẩn thẻ sau 5 giây
							setTimeout(function() {
							messageElement.style.display = "none";
							}, 7000); // 5000 milliseconds = 5 giây
							}
							
							})
                            .fail(function(jqXHR, textStatus, errorThrown) {
                                //var messageElement = document.getElementById("messagee");
                                var endTime = new Date(); // Lấy thời gian kết thúc yêu cầu
                                var elapsedTime = endTime - startTime; // Tính thời gian thực hiện yêu cầu
                                if (textStatus === "timeout") {
                                    messageElement.innerHTML = '<div style="color: red;"><b>' + getTimee + ' - Lỗi: Yêu cầu đã vượt quá thời gian chờ. | ' + elapsedTime + 'ms</b></div>';

                                } else {
                                    messageElement.innerHTML = '<div style="color: red;"><b>' + getTimee + ' - Lỗi: Không thể kết nối đến API. | ' + elapsedTime + 'ms</b></div>';
                                }
                            });
                    } else {
                        //console.error('Lỗi:', response.error || 'Không xác định');
                        messageElement.innerHTML = '<div style="color: red;"><b>' + getTimee + ' - Lỗi: ' + response.error + ' Không xác định || ' + elapsedTime + 'ms</b></div>';
                    }
					$('#loading-overlay').hide();
					
                },
                error: function(jqXHR, textStatus, errorThrown) {
					$('#loading-overlay').hide();
                    //console.error('Lỗi AJAX:', textStatus, errorThrown);
                    messageElement.innerHTML = '<div style="color: red;"><b>' + getTimee + ' - Lỗi AJAX: ' + textStatus + ' || ' + errorThrown + ' || ' + elapsedTime + 'ms</b></div>';
                }
				
            });
			

        });
    });



    //đổi thời gian nếu có 1 số thì thêm số 0 phía trước
    function formatTime(time) {
        return (time < 10) ? '0' + time : time;
    }


    //icon Loading
    $(document).ready(function() {
        $('#uploadmp3local').on('submit', function() {
            // Hiển thị biểu tượng loading
            $('#loading-overlay').show();
            // Vô hiệu hóa nút gửi
            $('#submit-btn').attr('disabled', true);
        });
    });
</script>
<script>
    function setupAudioControls() {
		
        var messageElement = document.getElementById("messagee");

        $('#volumeDown').on('click', function() {
            sendAudioControlCommand('<?php echo $api_vietbot->set_volume_down->payload->action; ?>', '<?php echo $api_vietbot->set_volume_down->payload->data; ?>', <?php echo $api_vietbot->set_volume_down->payload->type; ?>, '<?php echo $api_vietbot->set_volume_down->method; ?>');
        });

        $('#playButton').on('click', function() {
            sendAudioControlCommand('<?php echo $api_vietbot->set_player_continue_state->payload->action; ?>', '<?php echo $api_vietbot->set_player_continue_state->payload->data; ?>', <?php echo $api_vietbot->set_player_continue_state->payload->type; ?>, '<?php echo $api_vietbot->set_player_continue_state->method; ?>');
        });

        $('#pauseButton').on('click', function() {
            sendAudioControlCommand('<?php echo $api_vietbot->set_player_pause_state->payload->action; ?>', '<?php echo $api_vietbot->set_player_pause_state->payload->data; ?>', <?php echo $api_vietbot->set_player_pause_state->payload->type; ?>, '<?php echo $api_vietbot->set_player_pause_state->method; ?>');
        });

        $('#stopButton').on('click', function() {
            sendAudioControlCommand('<?php echo $api_vietbot->set_player_stop_state->payload->action; ?>', '<?php echo $api_vietbot->set_player_stop_state->payload->data; ?>', <?php echo $api_vietbot->set_player_stop_state->payload->type; ?>, '<?php echo $api_vietbot->set_player_stop_state->method; ?>');
        });
        $('#volumeUp').on('click', function() {
            sendAudioControlCommand('<?php echo $api_vietbot->set_volume_up->payload->action; ?>', '<?php echo $api_vietbot->set_volume_up->payload->data; ?>', <?php echo $api_vietbot->set_volume_up->payload->type; ?>, '<?php echo $api_vietbot->set_volume_up->method; ?>');
        });

        function sendAudioControlCommand(action, data, type, method) {
			$('#loading-overlay').show();
            var settings = {
                "url": "http://<?php echo $serverIP; ?>:<?php echo $Port_Vietbot; ?>",
                "method": method,
                "timeout": <?php echo $Time_Out_MediaPlayer_API; ?> ,
                "headers": {
                    "Content-Type": "application/json"
                },
                "data": JSON.stringify({
                    "type": type,
                    "data": data,
					"action": action
                }),
            };

            $.ajax(settings)
// Biến để theo dõi trạng thái của checkbox


// ...

.done(function(responseh) {
	var isCheckboxChecked = $("#run-checkbox").is(":checked");
    // Kiểm tra nếu checkbox được tích
    if (isCheckboxChecked) {
		var displayText = responseh.new_volume !== undefined ? 'Âm Lượng: ' + responseh.new_volume + '%' : responseh.response;
		messageElement.innerHTML = ' ';
        // Hiển thị thông báo khi checkbox được tích và responseh.response không phải là một trong các giá trị chỉ định
        if (!(responseh.response === "Đã dừng!" || responseh.response === "Đã tiếp tục!" || responseh.response === "Đã tạm dừng!")) {
            // Xử lý và hiển thị response
            		messageElement.style.display = "block";
            messageElement.innerHTML = '<div style="color: green;"><b>' + displayText + '</b></div>';
			$('#loading-overlay').hide();
		// Kiểm tra xem thẻ có tồn tại không trước khi ẩn
		if (messageElement) {
		// Sử dụng setTimeout để ẩn thẻ sau 5 giây
		setTimeout(function() {
		messageElement.style.display = "none";
		}, 7000); // 5000 milliseconds = 5 giây
		}
			
        }
    } else {
        // Hiển thị thông báo khi checkbox không được tích
        var displayText = responseh.new_volume !== undefined ? 'Âm Lượng: ' + responseh.new_volume + '%' : responseh.response;
        messageElement.innerHTML = '<div style="color: green;"><b>' + displayText + '</b></div>';
		$('#loading-overlay').hide();

    }
	
})

                .fail(function(jqXHR, textStatus, errorThrown) {
                    if (textStatus === "timeout") {
                        messageElement.innerHTML = '<div style="color: red;"><b>Lỗi: Hết thời gian chờ khi kết nối với API.</b></div>';
						$('#loading-overlay').hide();
                    } else {
                        messageElement.innerHTML = '<div style="color: red;"><b>Lỗi! Không thể kết nối tới API</b></div>';
						$('#loading-overlay').hide();
                    }
                    // console.error('<div style="color: red;"><b>Error sending audio control command:</b></div>', textStatus, errorThrown);
                    messageElement.innerHTML = '<div style="color: red;"><b>Lỗi khi gửi lệnh điều khiển chức năng:</b></div>' + textStatus + errorThrow;
					$('#loading-overlay').hide();
                });
        }

        function showMessage(message) {
            messageElement.innerHTML = '<div style="color: red;"><b>' + message + '</b></div>';
        }
    }
    $(document).ready(function() {
        setupAudioControls();
    });
</script>

<script>
    
    function myFunctionmp3local() {
            // Lắng nghe sự kiện mouseup trên nút
            $('#submitButton').on('mouseup', function(event) {
                // Lấy giá trị từ thẻ input
                var tenbaihatValue = $('#tenbaihatInput').val();
                // Kiểm tra nếu giá trị không bắt đầu bằng "http"

                var inputValueLowercase = tenbaihatValue.toLowerCase();
                var searchStringLowercase = "http";

                if (!inputValueLowercase.startsWith(searchStringLowercase)) {
                    alert("Dữ liệu đầu vào để Play .mp3 phải bắt đầu bằng 'http'");
                    event.preventDefault(); // Ngăn chặn hành động mặc định của nút
                    return; // Dừng thực thi nếu không hợp lệ
                }
                // Truyền giá trị vào thuộc tính data-song-id của thẻ button html
                $(this).data('song-id', tenbaihatValue);
                // Log giá trị để kiểm tra
                //  console.log('Tên bài hát:', tenbaihatValue);
                //  console.log('data-song-id:', $(this).data('song-id'));
            });
        }
        // Gọi hàm mới khi trang đã sẵn sàng
    $(document).ready(myFunctionmp3local);
</script>


<script> 
// chọn radio
// điều kiện khi nhập text vào input
    function handleRadioChangeLocal() {
        // Lấy tham chiếu đến radio button và input
        var radio_Local = document.getElementById("LocalMp3");
        var UpLoadFileMp3 = document.getElementById("UpLoadFileMp3");
        var button_Playmp3 = document.getElementById("submitButton");
        var input_tenbaihatInput = document.getElementById("tenbaihatInput");
        var timkiemButton = document.getElementById("TimKiem");

        // Nếu radio được chọn, disabled input
        if (radio_Local.checked) {
			UpLoadFileMp3.hidden = false;
            input_tenbaihatInput.disabled = true;
            input_tenbaihatInput.hidden = true;
            input_tenbaihatInput.value = "";
            button_Playmp3.disabled = true;
            button_Playmp3.hidden = true;
            timkiemButton.hidden = false;
            timkiemButton.disabled = false;
        } else {
			UpLoadFileMp3.hidden = true;
            input_tenbaihatInput.disabled = false;
            input_tenbaihatInput.hidden = false;
            input_tenbaihatInput.value = "";
            button_Playmp3.disabled = true;
            button_Playmp3.hidden = true;
            timkiemButton.hidden = false;
            timkiemButton.disabled = false;
        }
    }
//Nhập text vào input
    function handleInputHTTP() {
        var input_http = document.getElementById("tenbaihatInput");
        var timkiemButton = document.getElementById("TimKiem");
        var submitButton = document.getElementById("submitButton");
        var inputValueLowercase = input_http.value.toLowerCase();
        var searchStringLowercase = "http";
        if (inputValueLowercase.startsWith(searchStringLowercase)) {
            timkiemButton.disabled = true;
            timkiemButton.hidden = true;
            submitButton.hidden = false;
            submitButton.disabled = false;
        } else {
            timkiemButton.disabled = false;
            timkiemButton.hidden = false;
            submitButton.hidden = true;
            submitButton.disabled = true;
        }
    }
</script>
 <script>
    //xóa file
    $(document).ready(function() {
        var messageElement = document.getElementById("messagee");
        // Khi nút "Xóa File" được nhấn
        $('.deleteBtn').on('click', function() {
            var fileToDelete = $(this).data('file');
            //console.log(fileToDelete)
            var url = '../include_php/Ajax/Mp3_Del.php?fileToDelete=' + fileToDelete;
            var xacNhan = confirm("Bạn có chắc chắn muốn xóa file: " + fileToDelete);
            if (xacNhan) {
                // Gửi yêu cầu AJAX
                $.ajax({
                    type: 'GET',
                    url: url,
                    success: function(response) {
                        messageElement.innerHTML = '<div style="color: red;">' + response + '</div>';
                        //alert(response);
                    },
                    error: function() {
                        alert('Có lỗi xảy ra của ajax khi gửi yêu cầu xóa file');
                    }
                });
                // Người dùng đã nhấn nút "OK"
                // alert("Hành động đã được thực hiện!");
            } else {
                // Người dùng đã nhấn nút "Cancel" hoặc đóng hộp thoại
                // alert("Hành động đã bị hủy bỏ!");
                messageElement.innerHTML = '<div style="color: red;">Thao tác xóa file <b>' + fileToDelete + '</b> đã bị hủy bỏ</div>';
				
            }
        });
    });
</script>
<script>
// Hàm thực hiện AJAX để đọc dữ liệu từ tệp JSON
function readJsonAndCheckCheckbox() {
    $.ajax({
        url: 'cfg_action.json',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            // Đánh dấu checked cho checkbox nếu điều kiện được đáp ứng
            if (data && data.music_source === 'ZingMp3') {
                $('#keyzingmp3').prop('checked', true);
            }else if (data && data.music_source === 'Youtube') {
                // Thực hiện hành động khác nếu giá trị khác
               $('#keyyoutube').prop('checked', true);
            }else if (data && data.music_source === 'Local') {
                // Thực hiện hành động khác nếu giá trị khác
               $('#LocalMp3').prop('checked', true);
            }
        },
        error: function(error) {
			//Nếu lỗi json thì mặc định sẽ chọn zingmp3
			$('#keyzingmp3').prop('checked', true);
            console.error('Failed to read JSON file cfg_action.json:', error);
        }
    });
}

// Gọi hàm khi trang web được tải
$(document).ready(function() {
    readJsonAndCheckCheckbox();
});
</script>



<script>


    // Function to convert seconds to HH:MM:SS format
    function formatTimeajax(seconds) {
        var hours = Math.floor(seconds / 3600);
        var minutes = Math.floor((seconds % 3600) / 60);
        var remainingSeconds = seconds % 60;

        // Ensure two digits for hours, minutes, and seconds
        var formattedHours = hours < 10 ? "0" + hours : hours;
        var formattedMinutes = minutes < 10 ? "0" + minutes : minutes;
        var formattedSeconds = remainingSeconds < 10 ? "0" + remainingSeconds : remainingSeconds;

        return formattedHours + ":" + formattedMinutes + ":" + formattedSeconds;
    }

    // Function to make the API request and handle data
    function fetchData() {
        var selectedOption = $("#select-playback").find('option:selected');
        var get_playback = selectedOption.data('playback');
        var settings = {
            "url": "http://<?php echo $serverIP; ?>:<?php echo $Port_Vietbot; ?>/?api_type=<?php echo $api_vietbot->get_long_player_state->payload->api_type; ?>&data=<?php echo $api_vietbot->get_long_player_state->payload->data; ?>",
            "method": "GET",
            "timeout": 0,
            "headers": {
                "Content-Type": "application/json"
            }
        };

        $.ajax(settings)
            .done(function(response) {
				var messageinfomusicplayer = document.getElementById("infomusicplayer");
                var media_path = response.media1_path;
                var playervlc_state = response.player1_state;
                var media_position = response.media1_position;
                var cover_link = response.cover_link;
                var last_request = response.last_request;
				
				
                var song_name = response.song_name;
                //var cover_link = response.cover_link;
				
                //console.log("media_path:", media_path);
                var state = response.player1_state;
                // Convert player_duration to seconds
                var media_durationInSeconds = Math.round(response.media1_duration);
                // Convert media_position to seconds
                var media1_positionInSeconds = media_position === -1.0 ? -1.0 : Math.round(media_position * media_durationInSeconds);
                // Further processing or UI updates can be done here
                // Update the selected time on the UI if media_position is not -1.0
                if (media1_positionInSeconds !== -1.0) {
                    $("#selected-time").text(formatTimeajax(media1_positionInSeconds));
                }
                if (media_path.startsWith("file:///home/pi/vietbot_offline/src/mp3/")) {
                    // Giải mã chuỗi URL
                    const decodedString = decodeURIComponent(media_path);
                    // Bỏ phần đường dẫn
                    const fileNameWithoutPath = decodedString.split('/').pop();
                    // Bỏ phần mở rộng
                    const fileNameWithoutExtension = fileNameWithoutPath.replace(/\..+$/, '');
                    // Giới hạn tên file tối đa 20 ký tự và ngắt tại khoảng trắng
                    const maxLength = 25;
                    const truncatedFileName = truncateFileName(fileNameWithoutExtension, maxLength);
                    var nguonnhac = "<font color=green>Local MP3</font>";
                    // console.log('Tên file sau khi giải mã, loại bỏ đường dẫn và mở rộng:', truncatedFileName);
                } else if (media_path.startsWith("http://vnno-")) {
                    //$("#media1-name").html("Nguồn nhạc: <font color=green>ZingMp3</font>");
                    var nguonnhac = "<font color=green>ZingMp3</font>";
                    //console.log('Xử lý cho trường hợp khác');
                } else if (media_path.startsWith("https://rr")) {
                    //$("#media1-name").html("Nguồn nhạc: <font color=green>Youtube</font>");
                    var nguonnhac = "<font color=green>Youtube</font>";
                } else if (media_path.startsWith("file:///home/pi/vietbot_offline/src/tts_saved/")) {
                    //$("#media1-name").html("Luồng Mic: Không có dữ liệu");
                    var nguonnhac = "Không có dữ liệu";
                } else {
                    //$("#media1-name").html("Nguồn nhạc: <font color=green>.....</font>");
                    var nguonnhac = "<font color=green>.....</font>";
                    //console.log('Xử lý cho trường hợp mặc định');
                }
                // Update the slider values
                $("#time-slider").attr("max", media_durationInSeconds);
                $("#time-slider").val(media1_positionInSeconds);
                // Convert and display media1_duration in HH:MM:SS format
                $("#media1-duration").text(formatTimeajax(media_durationInSeconds));
				
				$("infomusicplayer").html("Nguồn nhạc: <font color=green>.....</font>");
				//messageinfomusicplayer.innerHTML = '<div class="image-container"><div class="rounded-image"><img src=' + cover_link + ' alt="" /></div><div class="caption"><b>Tên bài hát: </b> ' + songName + '<br/><b>'+songTenKenhNgheSi+': </b> ' + songArtist + '<br/><b>Thời lượng: </b> ' + songThoiLuong + '<br/><b>Kích thước: </b> ' + songKichThuoc + '</div></div>';
                messageinfomusicplayer.innerHTML = '<div class="image-container"><div class="rounded-image"><img src='+cover_link+' alt="" /></div><div class="caption"><ul><li><p style="text-align: left;"><b>Yêu Cầu: </b>'+truncateFileName(last_request, 30)+'</p></li><li><p style="text-align: left;"><b>Tên bài hát: </b><font color=blue>'+truncateFileName(song_name, 20)+'</font></p></li><li><p style="text-align: left;"><b>Nguồn Nhạc:</b> '+nguonnhac+'</li></p></ul></div></div>';
                //thay đổi giá trị volume ở thanh slile
				document.getElementById('volume').value = response.volume;
				document.getElementById('currentVolume').innerText = response.volume;
				

				
				
				
                // Display player state based on playervlc_state
                var playerStateText = "";
                var playerStateColor = "";
                switch (playervlc_state) {
                    case "State.Ended":
                        playerStateText = "Đã kết thúc";
                        playerStateColor = "gray";
                        break;
                    case "State.Playing":
                    case "State.Opening":
                        playerStateText = "Đang phát nhạc";
                        playerStateColor = "green";
                        break;
                    case "State.Paused":
                        playerStateText = "Đã tạm dừng";
                        playerStateColor = "blue";
                        break;
                    case "State.Stopped":
                        playerStateText = "Đã dừng";
                        playerStateColor = "red";
                        break;
                    default:
                        playerStateText = "Trạng thái không xác định";
                        playerStateColor = "black";
                }
                $("#player-state").text("Trạng thái: " + playerStateText).css("color", playerStateColor);
				$('#loading-overlay').hide();
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                // Handle the failure (e.g., no connection to API)
                $("#player-state").text("Trạng thái: Không kết nối được tới API get_api_playback");
				$('#loading-overlay').hide();
            });
    }

    // Function to check if the code should run
    function shouldRunCode() {
        return $("#run-checkbox").is(":checked");
    }

    // Set an interval to call the fetchData function every 3 seconds
    var intervalID;

    function startInterval() {
        intervalID = setInterval(function() {
            if (shouldRunCode()) {
                var parentUrl = window.top.location.href;
                // Tách đường dẫn URL để lấy phần fragment sau dấu #
                var fragments = parentUrl.split('#');
                if (fragments.length > 1) {
                    var fragment = fragments[1];
                    // Kiểm tra giá trị của fragment
                    if (fragment === "MediaPlayer") {
                        fetchData();
                    }
                    //else {console.log("Không có MediaPlayer trong đường dẫn URL của trang cha.");}
                }
                //else {console.log("Không có fragment trong đường dẫn URL của trang cha.");}



            }
        }, <?php echo $sync_media_player_sync_delay; ?> * 1000);
    }

    // Check the initial state of the checkbox and show/hide the code section accordingly
    $(document).ready(function() {
        if (shouldRunCode()) {
            startInterval();
            $("#code-section").show();
        } else {
            $("#code-section").hide();
            //   $("#player-state").text("Player State: Code execution stopped.");
        }
    });

    // Update the selected time when the slider value changes
    $("#time-slider").on("input", function() {
        $("#selected-time").text(formatTimeajax($(this).val()));
    });

    // Update the code execution and visibility when the checkbox state changes
    $("#run-checkbox").on("change", function() {
        if (shouldRunCode()) {
            startInterval();
            $("#code-section").show();
        } else {
            clearInterval(intervalID);
            $("#code-section").hide();
            // $("#player-state").text("Trạng thái: Code execution stopped.");
        }
    });
</script>




<script>
  function updateVolume(newVolume) {
    // Update the display of the current volume
    document.getElementById('currentVolume').innerText = newVolume;

    // Send the new volume value via Ajax
    var ajaxSettingsss = {
      "url": "http://<?php echo $serverIP; ?>:<?php echo $Port_Vietbot; ?>",
      method: "<?php echo $api_vietbot->set_volume_percent->method; ?>",
      timeout: 0,
      headers: {
        "Content-Type": "application/json"
      },
      data: JSON.stringify({
        type: <?php echo $api_vietbot->set_volume_percent->payload->type; ?>,
        data: "<?php echo $api_vietbot->set_volume_percent->payload->data; ?>",
        action: "<?php echo $api_vietbot->set_volume_percent->payload->action; ?>",
        new_value: parseInt(newVolume) // Convert newVolume to an integer
      }),
    };

    $.ajax(ajaxSettingsss).done(function (response) {
      console.log(response);

      // Update the volume slider value and the displayed current volume with the new_volume value from the response
      document.getElementById('volume').value = response.new_volume;
      document.getElementById('currentVolume').innerText = response.new_volume;
    });
  }
</script>




<script>
    // Your JavaScript code here
    function togglePopupSync() {
        var popupContainer = document.getElementById("popupContainer");
        popupContainer.classList.toggle("show");
    }

    function hidePopupSync() {
        var popupContainer = document.getElementById("popupContainer");
        popupContainer.classList.remove("show");
    }

    function preventEventPropagationSync(event) {
        event.stopPropagation();
    }
</script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/bootstrap.js"></script>
</body>
</html>
<?php

function formatTimephp($seconds)
{
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;

    return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}


?>
