<?php
// Code By: Vũ Tuyển
// Facebook: https://www.facebook.com/TWFyaW9uMDAx
$api_Search_Zing = "http://ac.mp3.zing.vn/complete?type=song&num=7&query=";

// Hàm để lấy URL cuối cùng sau khi chuyển hướng
function getFinalUrl($url)
{
    $ch = curl_init($url);
    // Thiết lập cURL để trả về dữ liệu thay vì hiển thị nó
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Thiết lập cURL để theo dõi tất cả các chuyển hướng
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    // Thiết lập thời gian chờ
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    // Tắt chế độ DNS lookup (chỉ sử dụng IPv4)
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    // Tắt SSL verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // Thực hiện yêu cầu cURL và lấy thông tin về chuyển hướng
    curl_exec($ch);
    $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    // Đóng kết nối cURL
    curl_close($ch);
    return $finalUrl;
}

?>
<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <form method="post" id="my-form" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="2" scope="col">
                                <center>Chọn Nguồn Nhạc:</center>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>

                            <td>
                                <!-- Checkbox với giá trị "keymp3" -->
                                <input type="radio" id="keyzingmp3" name="action" value="ZingMp3" checked>
                                <label for="keyzingmp3">Zing MP3</label>
                            </td>

                            <td>
                                <!-- Checkbox với giá trị "keyyoutube" -->
                                <input type="radio" id="keyyoutube" name="action" value="Youtube">
                                <label for="keyyoutube">YouTube</label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="input-group mb-3">
                                    <input type="text" id="tenbaihatInput" class="form-control" name="tenbaihat" required placeholder="Nhập Tên Bài Hát, link.mp3" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">Tìm Kiếm</button>
                                    </div>
                                    <div class="input-group-append">

                                        <button type="button" id="submitButton" class="ajax-button btn btn-success" data-song-artist="Không có dữ liệu" data-song-images="../assets/img/VietBotlogoWhite.png" data-song-name="Không có dữ liệu" data-song-id="" value="">Play .Mp3</button>
                                    </div>
                                </div>
                            </td>

                        </tr>

            </form>

            <tr>
                <td colspan="2">
                    <center>
                        <button type="button" id="volumeDown" class="btn btn-info"><i class="bi bi-volume-down"></i>
                        </button>
                        <button type="button" id="playButton" class="btn btn-success"><i class="bi bi-play-circle"></i>
                        </button>
                        <button type="button" id="pauseButton" class="btn btn-warning"><i class="bi bi-pause-circle"></i>
                        </button>
                        <button type="button" id="stopButton" class="btn btn-danger"><i class="bi bi-stop-circle"></i>
                        </button>

                        <button type="button" id="volumeUp" class="btn btn-info"><i class="bi bi-volume-up"></i>
                        </button>

                    </center>
                </td>

            </tr>
            <tr>
                <td colspan="2">
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
<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'Youtube') {
    $Data_TenBaiHat = $_POST['tenbaihat'];
    $NguonNhac = $_POST['action'];
    // Attention! Here you have to set how many data you want to pull. By default, it pulls a maximum of 20 data.  Here ↴
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
    echo json_encode(['error' => 'Yêu cầu cURL không thành công.']);
} else {
    $dataYoutube = json_decode($responseYoutube, true);

    // Kiểm tra xem có dữ liệu hay không
    if (empty($dataYoutube) || empty($dataYoutube['items'])) {
        echo "Không có dữ liệu.";
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
            echo '<b>Tên bài hát:</b> ' . $Youtube_title . '<br/><b>Tên Kênh:</b> ' . $Youtube_channelTitle . '<br/>';
            //echo '<b>Mô tả:</b> ' . $Youtube_description . ' <br/>';
            echo '<b>Link:</b> ' . $Youtube_videoLink . ' <br/>';
            echo '<button class="ajax-button btn btn-success" data-song-id="' . $Youtube_videoLink . '" disabled>Phát Nhạc/Comback Soon</button>';
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
    echo json_encode(['error' => 'Yêu cầu cURL không thành công.']);
} else {
    $data = json_decode($response, true);
    // Kiểm tra xem có dữ liệu hay không
    if (empty($data)) {
        echo "Không có dữ liệu trên ZingMp3.";
    } else {
        echo "Tên Bài Hát Đang Tìm Kiếm: <b><font color=red>" . $_POST['tenbaihat'] . "</font></b> | Nguồn Nhạc: <font color=red><b>" . $NguonNhac . "</b></font><hr/>";

        if ($data['result'] === true && isset($data['data'][0]['song'])) {
            foreach ($data['data'][0]['song'] as $song) {
                $ID_MP3 = $song['id'];
                $originalUrl = "http://api.mp3.zing.vn/api/streaming/audio/$ID_MP3/128";

                // Sử dụng hàm để lấy URL cuối cùng sau khi chuyển hướng
             
			  $finalUrl = getFinalUrl($originalUrl);
                $img_images = "https://photo-zmp3.zmdcdn.me/" . $song['thumb'];
                echo " <div class='image-container'>";
                echo "<img src='$img_images' class='imagesize' alt='' /> <div class='caption'>";
                echo '<b>Tên bài hát:</b> ' . $song['name'] . '<br/><b>Nghệ sĩ:</b> ' . $song['artist'] . '<br/>';
                //echo 'ID bài hát: ' . $song['id'] . ' <br/>';
                echo '<button class="ajax-button btn btn-success" data-song-artist="' . $song['artist'] . '" data-song-name="' . $song['name'] . '" data-song-images="' . $img_images . '" data-song-id="' . $finalUrl . '">Phát Nhạc</button>';
                //echo "Original URL: $originalUrl<br>";
                // echo "MP3 128 URL: $finalUrl<br/><br/>";
                echo "</div></div><br/>";
            }
        } else {
            //echo json_encode(['error' => 'Không thể lấy dữ liệu.']);
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
$(document).ready(function () {
    // Xử lý sự kiện khi nút Ajax được nhấn
    $('.ajax-button').on('click', function () {
        var songId = $(this).data('song-id');
        var songImages = $(this).data('song-images');
        var songArtist = $(this).data('song-artist');
        var songName = $(this).data('song-name');
        var startTime = new Date(); // Lấy thời gian bắt đầu yêu cầu
		var getTime = formatTime(startTime.getHours()) + ':' + formatTime(startTime.getMinutes()) + ':' + formatTime(startTime.getSeconds());
         if (!songId) {
        //alert('Không có dữ liệu cho songId');
        return; // Dừng thực thi nếu không có dữ liệu đầu vào
    }
	   var settings = {
            "url": "http://<?php echo $serverIP; ?>:5000",
            "method": "POST",
            "timeout": <?php echo $Time_Out_MediaPlayer_API; ?>, // Timeout sau 4 giây
            "headers": {
                "Content-Type": "application/json"
            },
            "data": JSON.stringify({
                "type": 3,
                "data": "<?php echo $object_json->music[0]->value; ?>",
                "link": songId
            }),
        };

        // Gửi yêu cầu Ajax
        $.ajax(settings)
            .done(function (response) {
                var messageElement = document.getElementById("messagee");
                var messageinfomusicplayer = document.getElementById("infomusicplayer");
                let modifiedStringSuccess = response.state.replace("Success", "Truyền Dữ Liệu Thành Công");
                var endTime = new Date(); // Lấy thời gian kết thúc yêu cầu
                var elapsedTime = endTime - startTime; // Tính thời gian thực hiện yêu cầu
                messageElement.innerHTML =   '<div style="color: green;"><b>'+getTime+' - ' + modifiedStringSuccess + ' | ' + elapsedTime + 'ms</b></div>';
  			   //messageElement.innerHTML += '<div style="color: green;"><b>' + modifiedStringSuccess + '</b></div>';
			   
			   messageinfomusicplayer.innerHTML = '<div class="image-container"><div class="rounded-image"><img src='+songImages+' alt="" /></div><div class="caption"><b>Tên bài hát: </b> '+songName+'<br/><b>Nghệ sĩ:</b> '+songArtist+'</div></div>';
			   
			   
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                var messageElement = document.getElementById("messagee");
                var endTime = new Date(); // Lấy thời gian kết thúc yêu cầu
                var elapsedTime = endTime - startTime; // Tính thời gian thực hiện yêu cầu
                if (textStatus === "timeout") {
                    messageElement.innerHTML = '<div style="color: red;"><b>'+getTime+' - Lỗi: Yêu cầu đã vượt quá thời gian chờ. | ' + elapsedTime + 'ms</b></div>';
                  //  messageElement.innerHTML += '<div style="color: red;"><b>Lỗi: Yêu cầu đã vượt quá thời gian chờ.</b></div>';
                } else {
                    messageElement.innerHTML = '<div style="color: red;"><b>'+getTime+' - Lỗi: Không thể kết nối đến API. | ' + elapsedTime + 'ms</b></div>';
                   // messageElement.innerHTML += '<div style="color: red;"><b>Lỗi: Không thể kết nối đến API.</b></div>';
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
    $('#my-form').on('submit', function() {
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
                "url": "http://<?php echo $serverIP; ?>:5000",
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
                .done(function(response) {

                    messageElement.innerHTML = response.answer || '<div style="color: green;"><b>Âm Lượng: ' + response.new_volume + '%</b></div>';
                    //console.log('sending audio control command:', response);
                    // Kiểm tra nếu giá trị trả về 
                    if (response.answer === "All players is paused!") {
                        messageElement.innerHTML = '<div style="color: green;"><b>Đã tạm dừng Phát Nhạc</b></div>';
                    } else if (response.answer === "All players is continued!") {
                        // Kiểm tra nếu giá trị trả về là "All players is continued!"
                        messageElement.innerHTML = '<div style="color: green;"><b>Đã tiếp tục Phát Nhạc</b></div>';
                    } else if (response.answer === "All players is stopped!") {
                        // Kiểm tra nếu giá trị trả về là "All players is stopped!"
                        messageElement.innerHTML = '<div style="color: green;"><b>Đã dừng Phát Nhạc</b></div>';
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
    // Đặt tên mới cho hàm
    function myFunctionmp3local() {
            // Lắng nghe sự kiện mouseup trên nút
            $('#submitButton').on('mouseup', function(event) {
                // Lấy giá trị từ thẻ input
                var tenbaihatValue = $('#tenbaihatInput').val();
                // Kiểm tra nếu giá trị không bắt đầu bằng "http"
                if (!tenbaihatValue.startsWith("http")) {
                    alert("Dữ liệu đầu vào Play .mp3 phải bắt đầu bằng 'http'");
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






<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/bootstrap.js"></script>
</body>
</html>