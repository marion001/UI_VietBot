<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
   <link rel="shortcut icon" href="../assets/img/VietBot128.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
<link rel="stylesheet" href="../assets/css/loading.css">
 <link rel="stylesheet" href="../assets/css/4.5.2_css_bootstrap.min.css">
    <title>Tìm kiếm Zing MP3</title>
  <script src="../assets/js/jquery-3.6.1.min.js"></script>
   <style>
        /* Style để định dạng vị trí của hình ảnh và ghi chú */
        .image-container {
            display: flex;
            align-items: center;
        }

        .imagesize {
            height: 150px; 
			width: 150px;
        }

        .caption {
            margin-left: 10px; /* Khoảng cách giữa hình ảnh và ghi chú */
        }
		 .custom-div {
			max-height: 70vh; /* Chiều cao tối đa là 70% của chiều cao của màn hình */
            overflow-y: auto; /* Thêm thanh cuộn nếu nội dung vượt quá kích thước */
            background-color: #f0f0f0; /* Màu nền của div (có thể thay đổi) */
        }
    </style>
</head>
<body>
<div id="loading-overlay"><img id="loading-icon" src="../assets/img/Loading.gif" alt="Loading...">
<div id="loading-message">- Đang Thực Hiện</div>
</div>
<?php
$api_Search_Zing = "http://ac.mp3.zing.vn/complete?type=song&num=20&query=";

// Hàm để lấy URL cuối cùng sau khi chuyển hướng
function getFinalUrl($url)
{
    $ch = curl_init($url);

    // Thiết lập cURL để trả về dữ liệu thay vì hiển thị nó
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Thiết lập cURL để theo dõi tất cả các chuyển hướng
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // Thực hiện yêu cầu cURL và lấy thông tin về chuyển hướng
    curl_exec($ch);
    $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

    // Đóng kết nối cURL
    curl_close($ch);

    return $finalUrl;
}
?>







<div class="row">
<div>
<div class="row g-3 d-flex justify-content-center"><div class="col-auto">
   <form method="post" id="my-form"  action="<?php echo $_SERVER['PHP_SELF']; ?>">


   
<table class="table table-bordered">
  <thead>
    <tr>
      <th colspan="2" scope="col"><center>Nguồn Nhạc:</center></th>
    </tr>
  </thead>
  <tbody>
    <tr>
    
      <td><!-- Checkbox với giá trị "keymp3" -->
 <input type="radio" id="keyzingmp3" name="action" value="ZingMp3" checked>
    <label for="keyzingmp3">Zing MP3</label> </td>
    
	<td><!-- Checkbox với giá trị "keyyoutube" -->
    <input type="radio"  id="keyyoutube" name="action" value="Youtube">
    <label for="keyyoutube">YouTube</label></td>
   </tr>
   <tr>
   <td colspan="2">	<div class="input-group mb-3">
  <input type="text" class="form-control" name="tenbaihat" required placeholder="Nhập Tên Bài Hát" aria-label="Recipient's username" aria-describedby="basic-addon2">
  <div class="input-group-append">
    <button class="btn btn-primary" type="submit">Tìm Kiếm</button>
  </div>
</div></td>
   
    </tr>
  </tbody>
</table> 
	 
</form>
<div id="messagee"></div>
</div>
</div>








</div>



  <div class="row">
   
      <div class="col-8 col-xl-6">
	   <div class="custom-div">
<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'Youtube') {
    $Data_TenBaiHat = $_POST['tenbaihat'];
    $NguonNhac = $_POST['action'];
    $apiKey = "AIzaSyDPokTvyB7XGaCrpBptSnqwDDOrZ9oV6rQ"; // Thay YOUR_YOUTUBE_API_KEY bằng khóa API YouTube của bạn
    // Attention! Here you have to set how many data you want to pull. By default, it pulls a maximum of 20 data.  Here ↴
    $searchUrlYoutube = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=" . urlencode($Data_TenBaiHat) . "&maxResults=20&key=" . $apiKey;

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
        echo "Tên Bài Hát Đang Tìm Kiếm: <b><font color=red>" . $_POST['tenbaihat'] . "</font></b> | Nguồn Nhạc: " . $NguonNhac . "<hr/>";

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
            echo '<b>Mô tả:</b> ' . $Youtube_description . ' <br/>';
            echo '<b>Link:</b> ' . $Youtube_videoLink . ' <br/>';
            echo '<button class="ajax-button btn btn-success" data-song-id="' . $Youtube_videoLink . '" disabled>Play Nhạc Ajax Vietbot</button>';
            echo "</div></div><br/>";
        }
    }
}

}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ZingMp3') {
    $Data_TenBaiHat = urlencode($_POST['tenbaihat']);
	$NguonNhac = $_POST['action'];
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
        echo "Tên Bài Hát Đang Tìm Kiếm: <b><font color=red>" . $_POST['tenbaihat'] . "</font></b> | Nguồn Nhạc: " . $NguonNhac . "<hr/>";

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
                echo '<button class="ajax-button btn btn-success" data-song-id="' . $finalUrl . '">Play Nhạc Ajax Vietbot</button>';
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
      <div class="col-4 col-xl-6">
        Level 2: .col-4 .col-sm-6
      </div>
    
  </div>
</div>





<!-- Đoạn mã JavaScript của bạn -->
<script>
    $(document).ready(function () {
        // Xử lý sự kiện khi nút Ajax được nhấn
        $('.ajax-button').on('click', function () {
           // console.log('Button clicked');
            var songId = $(this).data('song-id');

            // Tạo đối tượng settings
            var settings = {
                "url": "http://192.168.14.194:5000",
                "method": "POST",
                "timeout": 0,
                "headers": {
                    "Content-Type": "application/json"
                },
                "data": JSON.stringify({
                    "type": 3,
                    "data": "nhạc",
                    "link": songId
                }),
            };

            // Gửi yêu cầu Ajax
            $.ajax(settings).done(function (response) {
              //  console.log('Ajax request completed:', response);
			var messageElement = document.getElementById("messagee");
                // Thay đổi nội dung của một phần tử có ID là 'result-container'
              //  $('#result-container').append('<div style="color: red;">' + response.answer + '</div>');
              //  $('#result-container').append('<div style="color: green;">' + response.state + '</div>');
			messageElement.innerHTML = '<div style="color: red;">' + response.answer + '</div>';
			messageElement.innerHTML += '<div style="color: green;">' + response.state + '</div>';
            });
        });
    });
	
	
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

<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/bootstrap.js"></script>
</body>
</html>
