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

function install_source_node($DuognDanUI_HTML,$serverIP,$SSH_Port,$SSH_TaiKhoan,$SSH_MatKhau,$E_rror_HOST,$E_rror,$PHP_SELF) {
	
		$url = 'https://raw.githubusercontent.com/marion001/Google-APIs-Client-Library-PHP/main/node_modules.tar.gz';
		$destination = $DuognDanUI_HTML.'/assets/lib_php/node_modules.tar.gz';
		$extractedFolderPath = $DuognDanUI_HTML.'/assets/lib_php/';
		// Tải file từ URL
		$fileContent = file_get_contents($url);

		if ($fileContent !== false) {
		// Lưu nội dung vào file đích
			$result = file_put_contents($destination, $fileContent);

			if ($result !== false) {
				echo 'File đã được tải xuống thành công và lưu vào ' . $destination;
				$phar = new PharData($destination);
				$phar->extractTo($extractedFolderPath, null, true);  // Tham số thứ ba (true) cho phép ghi đè
				echo "Tệp đã được giải nén thành công vào $extractedFolderPath<br/>";
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
				} 
			else {
        echo 'Lỗi khi lưu nội dung vào ' . $destination;
			}
		} else {
			echo 'Lỗi khi tải file từ ' . $url;
		}
	
}

if (isset($_POST['install_lib_node_js'])) {
	
	$connection = ssh2_connect($serverIP, $SSH_Port);
    if (!$connection) {
        die($E_rror_HOST);
    }
    if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {
        die($E_rror);
    }
    $stream = ssh2_exec($connection, 'sudo apt install nodejs -y');
    stream_set_blocking($stream, true);
    $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
    stream_get_contents($stream_out);
install_source_node($DuognDanUI_HTML,$serverIP,$SSH_Port,$SSH_TaiKhoan,$SSH_MatKhau,$E_rror_HOST,$E_rror,$PHP_SELF);
header("Location: $PHP_SELF");
exit();
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
	install_source_node($DuognDanUI_HTML,$serverIP,$SSH_Port,$SSH_TaiKhoan,$SSH_MatKhau,$E_rror_HOST,$E_rror,$PHP_SELF);
	header("Location: $PHP_SELF");
	exit();
	
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
<td>
                                <!-- Checkbox với giá trị "keymp3" -->
                                <input type="radio" id="LocalMp3" name="action" value="Local" title="Tìm kiếm trên thiết bị" onchange="handleRadioChangeLocal()">
                                <label for="LocalMp3" title="Tìm kiếm trên thiết bị">Local MP3</label>
                            </td>
                            <td>
                                <!-- Checkbox với giá trị "keymp3" -->
                                <input type="radio" id="keyzingmp3" name="action" value="ZingMp3" title="Tìm kiếm trên ZingMp3" checked onchange="handleRadioChangeLocal()">
                                <label for="keyzingmp3" title="Tìm kiếm trên ZingMp3">Zing MP3</label>
                            </td>

                            <td>
                                <!-- Checkbox với giá trị "keyyoutube" -->
                                <input type="radio" id="keyyoutube" name="action" value="Youtube"  onchange="handleRadioChangeLocal()">
                                <label for="keyyoutube" title="Tìm kiếm trên youtube">YouTube</label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div class="input-group mb-3">
                                    <input type="text" id="tenbaihatInput" class="form-control" title="Nhập tên bài hát hoặc link: https://zxc.com/1.mp3" name="tenbaihat" required placeholder="Nhập Tên Bài Hát, link.mp3" aria-label="Recipient's username" aria-describedby="basic-addon2" oninput="handleInputHTTP()">
                                    <div style="text-align: center;" class="input-group-append">
                                        <button class="btn btn-primary" id="TimKiem" type="submit" title="Tìm kiếm bài hát">Tìm Kiếm</button>
                                       
                                    </div>
                                    <div class="input-group-append">

                                        <button type="button" id="submitButton" class="ajax-button btn btn-success" data-song-kichthuoc="---" data-song-thoiluong="---" data-song-link_type="direct" data-song-artist="---" data-song-images="../assets/img/NotNhac.png" data-song-name="Không có dữ liệu" data-song-id="" value="" hidden>Play .Mp3</button>
                                    </div>
									                         <div class="input-group-append">

                                        <a class="btn btn-danger" href="<?php echo $PHP_SELF; ?>" role="button" title="Làm mới lại trang">Làm Mới</a>
                                    </div>
                                </div>
                            </td>

                        </tr>

            </form>


 <tr>
<th colspan="3" scope="col">	<div id="UpLoadFileMp3" hidden>			

<form method="post" id="uploadmp3local" action="<?php echo $_SERVER['PHP_SELF']; ?>"  enctype="multipart/form-data">				
<div class="input-group" >

  <div class="custom-file">
	<input type="file" class="form-control" name="mp3Files[]" id="mp3File" max="<?php echo $maxFilesUploadMp3; ?>" multiple accept=".mp3" required>
	<input type="hidden" name="action" value="UploadMp3">
  </div>
  <div class="input-group-append">
    <button class="btn btn-primary" type="submit" title="Tải lên file mp3">Tải Lên</button>
  </div> 
</div> </form><font color=blue>Chọn tối đa: 20 File, Max 300MB/1 File</font></div>

  
                                </th>
                   </tr>
            <tr>
			
                <td colspan="3"><center>
<div id="code-section">
<b><p id="media1-name"></p></b>
    <span id="selected-time"></span>
    <input type="range" id="time-slider" min="1" max=""> 
	<span id="media1-duration"></span>
    <p id="player-state">Trạng thái: Đang đồng bộ...</p>
</div>
				
                    </center>
                </td>

            </tr>
            <tr>
			
                <td colspan="2">
                    <center>
                        <button type="button" id="volumeDown" title="Giảm âm lượng" class="btn btn-info"><i class="bi bi-volume-down"></i>
                        </button>
                        <button type="button" id="playButton" title="Phát nhạc" class="btn btn-success"><i class="bi bi-play-circle"></i>
                        </button>
                        <button type="button" id="pauseButton" title="Tạm dừng phát nhạc" class="btn btn-warning"><i class="bi bi-pause-circle"></i>
                        </button>
                        <button type="button" id="stopButton" title="Dừng phát nhạc" class="btn btn-danger"><i class="bi bi-stop-circle"></i>
                        </button>
                        <button type="button" id="volumeUp" title="Tăng âm lượng" class="btn btn-info"><i class="bi bi-volume-up"></i>
                        </button>
						

                    </center>
                </td><td><center>
<label for="run-checkbox" class="btn btn-warning" title="Bạn có thể cấu hình mặc định trong tab Skill->Media Player">

 <input title="Bạn có thể cấu hình mặc định trong tab Skill->Media Player" type="checkbox" id="run-checkbox" <?php echo ($sync_media_player_checkbox) ? 'checked' : ''; ?>> Đồng bộ</label>

<i class="bi bi-info-circle-fill" onclick="togglePopupSync()" title="Nhấn Để Tìm Hiểu Thêm"></i>
<div id="popupContainer" class="popup-container" onclick="hidePopupSync()">
    <div id="popupContent" onclick="preventEventPropagationSync(event)">
        <p><b>Đồng bộ Trạng Thái Media Player của Loa với Web UI</b></p>
		- Cài Đặt: <b>Tab Skill</b> -> <b>Media Player</b> -> <b>Đồng Bộ (Sync)</b> -> tích chọn <b>Đồng Bộ Media Với Web UI</b> -> <b>Lưu cấu hình</b><br/>
        <button class="btn btn-info" type="button" onclick="hidePopupSync()">Đóng</button>
    </div>
</div>

</td>
            </tr>
            <tr>
                <td colspan="3">
                    <center>
                        <div id="messagee"></div>
                    </center>
                </td>

            </tr>

            </tbody>
            </table>

            <div id="infomusicplayer">

            </div>
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
            echo '<b>Thời lượng:</b> ' . formatTimephp($duration) . '<br/>';
            echo '<b>Kích thước:</b> ' . $fileSizeMB . ' MB<br/>';
            echo '<button class="ajax-button btn btn-success" data-song-tenkenhnghesi="Nghệ Sĩ" data-song-kichthuoc="' . $fileSizeMB . ' MB" data-song-thoiluong="' . formatTimephp($duration) . '" data-song-artist=" ---" data-song-images="../assets/img/NotNhac.png" data-song-name="' . basename($mp3File) . '" data-song-link_type="local" data-song-id="mp3/' . basename($mp3File) . '">Phát Nhạc</button>';
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
            echo '<button class="ajax-button btn btn-success" data-song-tenkenhnghesi="Tên Kênh" data-song-link_type="direct" data-song-artist="' . $Youtube_channelTitle . '" data-song-images="' .$Youtube_images.'" data-song-name="'  . $Youtube_title . '" data-song-kichthuoc=" ---" data-song-thoiluong=" ---" data-song-id="' . $Youtube_videoLink . '" >Phát Nhạc</button>';
            echo "</div></div><br/>";
        }
    }
}
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ZingMp3') {
    $Data_TenBaiHat = urlencode($_POST['tenbaihat']);
	$NguonNhac = $_POST['action'];
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
                echo '<button class="ajax-button btn btn-success" data-song-tenkenhnghesi="Nghệ Sĩ" data-song-kichthuoc="---" data-song-thoiluong="---" data-song-link_type="zingmp3" data-song-artist="' . $song['artist'] . '" data-song-name="' . $song['name'] . '" data-song-images="' . $img_images . '" data-song-id="' . $originalUrl . '">Phát Nhạc</button>';
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
?>
      </div>
	</div>

  </div>
</div>
<!-- Đoạn mã JavaScript của bạn -->
<script>
    $(document).ready(function() {
        // Xử lý sự kiện khi nút Ajax được nhấn
        $('.ajax-button').on('click', function() {
			$('#loading-overlay').show();
			var messageElement = document.getElementById("messagee");
            var songId = $(this).data('song-id');
            var link_type = $(this).data('song-link_type');
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
                                "type": 3,
                                "data": "<?php echo $object_json->music[0]->value; ?>",
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
                                messageElement.innerHTML = '<div style="color: green;"><b>' + getTimee + ' - ' + modifiedStringSuccess + ' | ' + elapsedTime + 'ms</b></div>';
                                messageinfomusicplayer.innerHTML = '<div class="image-container"><div class="rounded-image"><img src=' + songImages + ' alt="" /></div><div class="caption"><b>Tên bài hát: </b> ' + songName + '<br/><b>'+songTenKenhNgheSi+': </b> ' + songArtist + '<br/><b>Thời lượng: </b> ' + songThoiLuong + '<br/><b>Kích thước: </b> ' + songKichThuoc + '</div></div>';
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
            sendAudioControlCommand('<?php echo $action_json->decrase->value." ".$object_json->volume[0]->value." 10%";  ?>');
        });

        $('#playButton').on('click', function() {
            sendAudioControlCommand('<?php echo $action_json->continue->value; ?>');
        });

        $('#pauseButton').on('click', function() {
            sendAudioControlCommand('<?php echo $action_json->pause[0]->value; ?>');
        });

        $('#stopButton').on('click', function() {
            sendAudioControlCommand('<?php echo $action_json->stop[0]->value; ?>');
        });
        $('#volumeUp').on('click', function() {
            sendAudioControlCommand('<?php echo $action_json->incrase->value." ".$object_json->volume[0]->value." 10%";  ?>');
        });

        function sendAudioControlCommand(action) {
            var settings = {
                "url": "http://<?php echo $serverIP; ?>:<?php echo $Port_Vietbot; ?>",
                "method": "POST",
                "timeout": <?php echo $Time_Out_MediaPlayer_API; ?> ,
                "headers": {
                    "Content-Type": "application/json"
                },
                "data": JSON.stringify({
                    "type": 3,
                    "data": action
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
            
            messageElement.innerHTML = '<div style="color: green;"><b>' + displayText + '</b></div>';
        }
    } else {
        // Hiển thị thông báo khi checkbox không được tích
        var displayText = responseh.new_volume !== undefined ? 'Âm Lượng: ' + responseh.new_volume + '%' : responseh.response;
        messageElement.innerHTML = '<div style="color: green;"><b>' + displayText + '</b></div>';
    }
})

                .fail(function(jqXHR, textStatus, errorThrown) {
                    if (textStatus === "timeout") {
                        messageElement.innerHTML = '<div style="color: red;"><b>Lỗi: Hết thời gian chờ khi kết nối với API.</b></div>';
                    } else {
                        messageElement.innerHTML = '<div style="color: red;"><b>Lỗi! Không thể kết nối tới API</b></div>';
                    }
                    // console.error('<div style="color: red;"><b>Error sending audio control command:</b></div>', textStatus, errorThrown);
                    messageElement.innerHTML = '<div style="color: red;"><b>Lỗi khi gửi lệnh điều khiển chức năng:</b></div>' + textStatus + errorThrow;
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

            var timestamp = new Date().getTimee();
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
    var settings = {
        "url": "http://<?php echo $serverIP; ?>:<?php echo $Port_Vietbot; ?>",
        "method": "POST",
        "timeout": 0,
        "headers": {
            "Content-Type": "application/json"
        },
        "data": JSON.stringify({
            "type": 3,
            "data": "get_api_playback"
        }),
    };

    $.ajax(settings)
        .done(function(response) {
            // Extract specific fields from the response
            var media2_duration = response.media2_duration;
            var media1_path = response.media1_path;
            var media1_position = response.media1_position;
            var player1_state = response.player1_state;
            var state = response.state;

            // Convert media1_duration to seconds
            var media1_durationInSeconds = Math.round(response.media1_duration);

            // Convert media1_position to seconds
            var media1_positionInSeconds = media1_position === -1.0 ? -1.0 : Math.round(media1_position * media1_durationInSeconds);

            // Further processing or UI updates can be done here

            // Update the selected time on the UI if media1_position is not -1.0
            if (media1_positionInSeconds !== -1.0) {
                $("#selected-time").text(formatTimeajax(media1_positionInSeconds));
            }

            if (media1_path.startsWith("file:///home/pi/vietbot_offline/src/mp3/")) {
                // Giải mã chuỗi URL
                const decodedString = decodeURIComponent(media1_path);
                // Bỏ phần đường dẫn
                const fileNameWithoutPath = decodedString.split('/').pop();
                // Bỏ phần mở rộng
                const fileNameWithoutExtension = fileNameWithoutPath.replace(/\..+$/, '');
                // Giới hạn tên file tối đa 20 ký tự và ngắt tại khoảng trắng
                const maxLength = 25;
                const truncatedFileName = truncateFileName(fileNameWithoutExtension, maxLength);
                $("#media1-name").text(truncatedFileName).attr("title", fileNameWithoutExtension);
                // console.log('Tên file sau khi giải mã, loại bỏ đường dẫn và mở rộng:', truncatedFileName);
            } else if (media1_path.startsWith("http://vnno-")) {
                $("#media1-name").text("ZingMp3");
                //console.log('Xử lý cho trường hợp khác');
            } else if (media1_path.startsWith("https://rr")) {
                $("#media1-name").text("Youtube");
            } else {
                $("#media1-name").text("Tên Bài Hát: .....");
                //console.log('Xử lý cho trường hợp mặc định');
            }
            // Update the slider values
            $("#time-slider").attr("max", media1_durationInSeconds);
            $("#time-slider").val(media1_positionInSeconds);

            // Convert and display media1_duration in HH:MM:SS format
            $("#media1-duration").text(formatTimeajax(media1_durationInSeconds));

            // Display player state based on player1_state
            var playerStateText = "";
            var playerStateColor = "";
            switch (player1_state) {
                case "State.Ended":
                    playerStateText = "Kết thúc";
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
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            // Handle the failure (e.g., no connection to API)
            $("#player-state").text("Trạng thái: Không kết nối được tới API get_api_playback");
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
            fetchData();
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
