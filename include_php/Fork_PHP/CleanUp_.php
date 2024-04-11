</head>
<body><br/>
<?php

// Xử lý form khi được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["directory"]) && !empty($_POST["directory"])) {
        $selectedDir = $_POST["directory"];
        $dir = $selectedDir;

        // Kiểm tra xem thư mục có tồn tại không
        if (!is_dir($dir)) {
            echo "<center>Đường dẫn thư mục không tồn tại.</center>";
        } else {
            // Hàm để tính tổng dung lượng của tất cả các tệp trong thư mục
            function getDirectorySize($path) {
                $totalSize = 0;
                $files = glob(rtrim($path, '/') . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $totalSize += filesize($file);
                    } elseif (is_dir($file)) {
                        $totalSize += getDirectorySize($file);
                    }
                }
                return $totalSize;
            }

            // Hàm để định dạng dung lượng thành dạng dễ đọc
            function formatSizeUnits($bytes) {
                $units = array('B', 'KB', 'MB', 'GB', 'TB');
                $i = 0;
                while ($bytes >= 1024 && $i < count($units) - 1) {
                    $bytes /= 1024;
                    $i++;
                }
                return round($bytes, 2) . ' ' . $units[$i];
            }

            // Lấy danh sách các tệp trong thư mục
            $files = scandir($dir);

            // Loại bỏ "." và ".." khỏi danh sách
            $files = array_diff($files, array('.', '..'));

            // Hiển thị các tệp và dung lượng
            if (empty($files)) {
                echo "<center>Thư mục <b>".basename($dir)."</b> là trống, không có tệp tin nào.</center>";
            } else {
                $noti_mess = '<center>Các tệp trong thư mục <b>'.basename($dir).'</b> có dung lượng tương ứng là:</center>';
                foreach ($files as $file) {
                    $filePath = $dir . '/' . $file;
                    if (is_file($filePath)) {
                        $fileSize = filesize($filePath);
                        $fileTime = filectime($filePath);
                        $fileList .= "$file (" . formatSizeUnits($fileSize) . " - Tạo lúc: " . date("H:i:s d/m/Y", $fileTime) . ")\n";
                    }
                }
            }

            // Tính toán và hiển thị tổng dung lượng của thư mục
            $directorySize = getDirectorySize($dir);
            // $fileList .= "\nDung lượng tổng cộng của thư mục $dir là: " . formatSizeUnits($directorySize);
        }
    } else {
        // Nếu không có dữ liệu, bạn có thể thông báo lỗi hoặc thực hiện hành động khác
        echo "<center>Không có thư mục được chọn.</center>";
    }
}

?>




<div class="row justify-content-center">
<div class="col-auto">
    <form method="post">
		<div class="input-group">
		<select class="custom-select" id="directory" name="directory">
	<option value="">Chọn nơi cần dọn dẹp</option>
		<option value="/home/pi/vietbot_offline/src/tts_saved">tts_saved</option>
		<input type="submit" class="btn btn-warning" title="Hiển thị các file, tệp tin" value="Load File"> 
		</div>

		
		
    </form>		</div>
		</div>

    <?php 
    if (isset($fileList)) {
		echo '<div class="justify-content-center">
<div class="col-auto">';
        echo "<div>";
		echo $noti_mess."<br/>";
        echo "<center><textarea class='form-control' style='width: 95%; height: 340px;' class='text-info form-control bg-dark' readonly='' rows='10' cols='50'>$fileList</textarea></center>";
        echo "</div>";
        echo "<div>";
        echo "<center>Tổng số tệp trong thư mục ".basename($dir)." là: " . count($files) . " File<br>";
        echo "Có tổng dung lượng là: " . formatSizeUnits($directorySize)."</center>";
        echo "</div>";
        echo "<form method='post'>";
        echo "<input type='hidden' name='directory' value='$dir'>";
        echo "<center><button type='submit' class='btn btn-danger' name='deleteFiles'>Xóa tất cả các tệp</button></center>";
        echo "</form></div></div>";
    }
    ?>

    <?php
    if (isset($_POST['deleteFiles'])) {
        $dir = $_POST['directory'];
        $files = glob("$dir/*");
        foreach ($files as $file) {
            if (is_file($file)) {
                chmod($file, 0777); // Cấp quyền đầy đủ cho tệp
                unlink($file); // Xóa tệp
            }
        }
        echo "<center>Đã xóa và cấp quyền cho tất cả các tệp trong thư mục ".basename($dir)."<center>";
    }
    ?>
	
</body>
</html>
