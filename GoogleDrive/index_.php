<?php
// Code By: Vũ Tuyển
// Facebook: https://www.facebook.com/TWFyaW9uMDAx
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
?>
  <body>
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
	<script>
    function selectAllText() {
        var input = document.getElementById("boidennoidung");
        input.select();
		try {
            document.execCommand("copy");
          //  alert("Nội dung đã được sao chép thành công!");
        } catch (err) {
            //console.error('Lỗi khi sao chép nội dung: ', err);
           // alert("Lỗi khi sao chép nội dung. Vui lòng thử lại.");
        }
    }
	
</script>
    <div id="loading-overlay">
        <img id="loading-icon" src="../assets/img/Loading.gif" alt="Loading...">
       <div id="loading-message">Đang tiến hành, vui lòng đợi...</div> 
         
    </div>
  <center><h4>Google Drive Auto Backup Vietbot</h4></center><br/>
<?php
	//restart vietbot
if (isset($_POST['reset_token'])) {
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die($E_rror_HOST);}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die($E_rror);}
$stream = ssh2_exec($connection, "rm $DuognDanUI_HTML/GoogleDrive/token.json");
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
stream_get_contents($stream_out);
header("Location: $PHP_SELF");
exit;
}


if (isset($Web_UI_Enable_GDrive_Backup) && $Web_UI_Enable_GDrive_Backup === true) {
    $jsonFilePath = $DuognDanUI_HTML.'/GoogleDrive/client_secret.json';

    $jsonData = file_get_contents($jsonFilePath);
    $DataArrayClient_Secret = json_decode($jsonData, true);



// Kiểm tra lỗi JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    // Có lỗi khi giải mã JSON
    echo '<center><font color=red><h4>Lỗi sai cấu trúc tệp json, mã lỗi: <b>' . json_last_error_msg().'</b><br/>';
	echo "Kiểm tra lại dữ liệu nhập vào ở tab <b>Config/Cấu Hình</b></h4></font><br/>";
	echo "<a href='$PHP_SELF'><button class='btn btn-primary'>Làm Mới</button></a></center>";
	die();
}

    if ($DataArrayClient_Secret === null) {
        die('Lỗi cấu trúc khi đọc và chuyển đổi dữ liệu tệp <b>client_secret.json</b>');
		
    }
    $tokenFilePath = $DuognDanUI_HTML.'/GoogleDrive/token.json';
    $client = new Google_Client();
    $client->setClientId($DataArrayClient_Secret['installed']['client_id']);
    $client->setClientSecret($DataArrayClient_Secret['installed']['client_secret']);
    $client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
    $client->setScopes(['https://www.googleapis.com/auth/drive', 'https://www.googleapis.com/auth/drive.file']);

    function saveTokenToFile($token, $filePath)
    {
        file_put_contents($filePath, json_encode($token));
    }

    function readTokenFromFile($filePath)
    {
        return json_decode(file_get_contents($filePath), true);
    }


if (file_exists($tokenFilePath)) {
    $accessToken = readTokenFromFile($tokenFilePath);
    $client->setAccessToken($accessToken);

    // Kiểm tra xem token có hợp lệ và chưa hết hạn không
    if ($client->isAccessTokenExpired()) {
        try {
            // Làm mới token nếu nó đã hết hạn
            $newAccessToken = $client->fetchAccessTokenWithRefreshToken();
            saveTokenToFile($newAccessToken, $tokenFilePath);
            chmod($tokenFilePath, 0777);

            echo '<center><font color=green><h4>Token đã được tự động làm mới thành công!</h4></font></center>';
			echo "<br/><a href='$PHP_SELF'><button class='btn btn-primary'>Về Trang Chủ</button></a></center>";
        } catch (Exception $e) {
            error_log('Lỗi khi làm mới token: ' . $e->getMessage());
            echo '<br/><center><font color=red><h4>Lỗi khi làm mới token: ' . $e->getMessage().'</h4></font></center>';
			echo "<br/><a href='$PHP_SELF'><button class='btn btn-primary'>Về Trang Chủ</button></a></center>";
        }
    } else {
		echo '<form method="POST" id="my-form" action="">';
        echo '<center><h4><font color=green>Google Drive Auto Backup hiện đang hợp lệ và hoạt động bình thường!</font></h4><br/>';
		echo "<button name='reset_token' class='btn btn-danger'>Reset Token</button>";
		echo "<a href='$PHP_SELF'><button class='btn btn-primary'>Làm Mới</button></a></center>";
		echo "</form>";
    }
} else {
        if (!isset($_POST['code_token'])) {
			$client->setAccessType('offline');
			$client->setPrompt('consent');
			$authUrl = $client->createAuthUrl();
			echo '<div class="row g-3 d-flex justify-content-center">
            <div class="col-auto">';
			echo "<h5><font color=red>- Trình Xác Thực</font></h5>";
            echo '<font color=green>Vui lòng xác thực ứng dụng với <b>Vietbot</b> bằng cách truy cập đường dẫn sau và nhập mã ủy quyền:</font><br/><br/>';
			echo 'Sao chép địa chỉ bên dưới đây và dán vào trình duyệt để <a href="' . $authUrl . '" target="_bank">Lấy Mã Ủy Quyền</a>:<br/>';
            echo '<div class="input-group mb-3"><input type="text" id="boidennoidung" class="form-control" value="' . $authUrl . '" aria-describedby="basic-addon2">
			<button onclick="selectAllText()" class="btn btn-success">Sao Chép</button></div><br>';
			
			
			
			echo '<form method="POST" id="my-form" action="">Nhập Mã Ủy Quyền <font color=red>*</font>:<br/><div class="input-group mb-3">
			<input type="text" name="code_token" class="form-control" placeholder="Nhập mã ủy quyền vào đây" aria-describedby="basic-addon2" required>
			<div class="input-group-append"><input class="btn btn-primary" type="submit" value="Xác thực"></div></div></form>';
			echo "</div></div>";
            die();
        } else {
			if (empty($_POST['code_token'])) {
			echo '<center><font color=red><h4>Vui lòng nhập mã ủy quyền để xác thực!</h4></font>';
			echo "Mã ủy quyền có dạng: <code>4/1AfJohXngyjRjD4jTPCQDTzp6mZ2PdL4xMIupmfvdvf542J0vQCer_bxCbgfggf</code><br/><br/>";
			echo "<a href='$PHP_SELF'><button class='btn btn-primary'>Quay lại</button></a></center>";
				}else {
				$client->authenticate($_POST['code_token']);
				$accessToken = $client->getAccessToken();
					if (!$accessToken) {
					// Thông báo khi xác thực thất bại
					echo '<center><h4><font color=red>Xác thực thất bại. Vui lòng kiểm tra lại mã ủy quyền</font></h4>';
					echo "Mã ủy quyền có dạng: <code>4/1AfJohXngyjRjD4jTPCQDTzp6mZ2PdL4xMIupmfvdvf542J0vQCer_bxCbgfggf</code><br/><br/>";
					echo "<a href='$PHP_SELF'><button class='btn btn-primary'>Thử Lại</button></a></center>";
					
					}else {
				saveTokenToFile($accessToken, $tokenFilePath);
				chmod($tokenFilePath, 0777);
				// Thông báo khi xác thực thành công
				echo '<center><font color=green><h4>Xác thực thành công! Dữ liệu đã được lưu.</h4></font><br/>';
				echo "<a href='$PHP_SELF'><button class='btn btn-primary'>Về Trang Chủ</button></a></center>";
    }
    }

}
    }
} else {
    echo "<font color=red><h4><center>Cần phải được bật <b>Google Drive Auto Backup</b> trong tab <b>Config/Cấu Hình</b> để xem và thiết lập</center></h4></font><br/>";
	echo "<center><a href='$PHP_SELF'><button class='btn btn-primary'>Tải lại</button></a></center>";
}
?>


</body></html>