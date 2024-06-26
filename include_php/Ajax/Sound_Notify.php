<?php
//Code By: Vũ Tuyển
//Facebook: https://www.facebook.com/TWFyaW9uMDAx
include "../../Configuration.php";
// Hàm để xóa tệp
function deleteFile($filePath) {
    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

// Function to download a file
function downloadFile($filePath) {
    // Ensure that the file exists
    if (file_exists($filePath)) {
        // Set headers for download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        // Read the file and output it to the browser
        readfile($filePath);
        exit;
    } else {
        // File not found
        echo "File $filePath không tồn tại để tải xuống";
    }
}

// Hàm để tải lên tệp vào thư mục default
function uploadToDefaultFolder($fileName, $tempFilePath, $DuognDanThuMucJson) {
    $uploadDir = $DuognDanThuMucJson.'/sound/default/';
    $uploadFile = $uploadDir . basename($fileName);
    if (move_uploaded_file($tempFilePath, $uploadFile)) {
		chmod($uploadFile, 0777);
        return true;
    } else {
        return false;
    }
}

// Hàm để tải lên tệp vào thư mục welcome
function uploadToWelcomeFolder($fileName, $tempFilePath, $DuognDanThuMucJson) {
    $uploadDir = $DuognDanThuMucJson.'/sound/welcome/';
    $uploadFile = $uploadDir . basename($fileName);
    if (move_uploaded_file($tempFilePath, $uploadFile)) {
		chmod($uploadFile, 0777);
        return true;
    } else {
        return false;
    }
}
// Hàm đổi tên file
function sanitizeFileName($fileName) {
    // Chuyển đổi tiếng Việt có dấu thành không dấu
    $fileName = removeVietnameseAccents($fileName);
    // Xóa dấu và ký tự đặc biệt (ngoại trừ dấu gạch dưới và dấu chấm)
    $fileName = preg_replace("/[^a-zA-Z0-9._]/", "_", $fileName);
    // Thay thế khoảng trắng bằng dấu "_"
    $fileName = str_replace(" ", "_", $fileName);
    // Xóa các dấu phân cách liên tiếp
    $fileName = preg_replace("/_+/", "_", $fileName);
    // Xóa ký tự "_" ở đầu và cuối chuỗi
    $fileName = trim($fileName, "_");
    return $fileName;
}

// Hàm loại bỏ dấu tiếng Việt
function removeVietnameseAccents($str) {
    $accents = array(
        'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
        'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
        'ì', 'í', 'ị', 'ỉ', 'ĩ',
        'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
        'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
        'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
        'đ',
        'À', 'Á', 'Ạ', 'Ả', 'Ã', 'Â', 'Ầ', 'Ấ', 'Ậ', 'Ẩ', 'Ẫ', 'Ă', 'Ằ', 'Ắ', 'Ặ', 'Ẳ', 'Ẵ',
        'È', 'É', 'Ẹ', 'Ẻ', 'Ẽ', 'Ê', 'Ề', 'Ế', 'Ệ', 'Ể', 'Ễ',
        'Ì', 'Í', 'Ị', 'Ỉ', 'Ĩ',
        'Ò', 'Ó', 'Ọ', 'Ỏ', 'Õ', 'Ô', 'Ồ', 'Ố', 'Ộ', 'Ổ', 'Ỗ', 'Ơ', 'Ờ', 'Ớ', 'Ợ', 'Ở', 'Ỡ',
        'Ù', 'Ú', 'Ụ', 'Ủ', 'Ũ', 'Ư', 'Ừ', 'Ứ', 'Ự', 'Ử', 'Ữ',
        'Ỳ', 'Ý', 'Ỵ', 'Ỷ', 'Ỹ',
        'Đ'
    );
    $noAccents = array(
        'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
        'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
        'i', 'i', 'i', 'i', 'i',
        'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
        'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
        'y', 'y', 'y', 'y', 'y',
        'd',
        'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A',
        'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E',
        'I', 'I', 'I', 'I', 'I',
        'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O',
        'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U',
        'Y', 'Y', 'Y', 'Y', 'Y',
        'D'
    );
    return str_replace($accents, $noAccents, $str);
}



// Kiểm tra xem có tham số "folder" có được truyền vào hay không
if (isset($_GET['folder'])) {
    // Lấy giá trị của tham số "folder" từ GET
    $folder = $_GET['folder'];

    // Đường dẫn tới thư mục con cần kiểm tra
    $dir = $DuognDanThuMucJson.'/sound/' . $folder;

    // Lấy danh sách các tệp trong thư mục con
    $files = scandir($dir);

    // Khởi tạo mảng để lưu trữ các tệp .mp3 và .wav
    $result = array();

    // Thêm các tệp .mp3 và .wav vào mảng kết quả
    foreach ($files as $file) {
        $filePath = $dir . '/' . $file;
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        if (is_file($filePath) && ($fileExtension == 'mp3' || $fileExtension == 'wav')) {
            $result[] = $file;
        }
    }

    // Trả về kết quả dưới dạng JSON
    echo json_encode($result);
} elseif (isset($_GET['download_file'])) {
    // Get the file path from the 'download_file' parameter
    $filePath = $DuognDanThuMucJson.'/sound/' . $_GET['download_file'];

    // Call the downloadFile function with the file path
    downloadFile($filePath);

} 

 elseif (isset($_GET['delete_file'])) {
    // Kiểm tra xem có tham số "delete_file" có được truyền vào hay không
    // Lấy giá trị của tham số "delete_file" từ GET
    $folderAndFile = $_GET['delete_file'];

    // Tách folder và tên file
    $folderAndFileParts = explode('/', $folderAndFile);
    $folder = $folderAndFileParts[0];
    $file = $folderAndFileParts[1];

    // Đường dẫn tới thư mục cần kiểm tra
    $dir = $DuognDanThuMucJson.'/sound/' . $folder;

    // Kiểm tra nếu tệp tồn tại và xóa nếu có
    $filePath = $dir . '/' . $file;
    if (deleteFile($filePath)) {
        echo "File <b>$file</b> đã được xóa thành công.";
    } else {
        echo "Không thể xóa file: <b>$file</b>";
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	
	
    // Kiểm tra nếu có tệp được tải lên
    // Kiểm tra xem người dùng đã chọn tệp hay chưa
    if (isset($_FILES["file_default"]) && $_FILES["file_default"]["error"] == UPLOAD_ERR_OK) {
      //  $fileName = $_FILES["file_default"]["name"];
        $fileName = sanitizeFileName($_FILES["file_default"]["name"]);
        $tempFilePath = $_FILES["file_default"]["tmp_name"];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($fileExtension == 'mp3' || $fileExtension == 'wav') {
            if (uploadToDefaultFolder($fileName, $tempFilePath, $DuognDanThuMucJson)) {
                echo "File <b>$fileName</b> đã được tải lên thành công";
            } else {
                echo "Đã xảy ra lỗi khi tải lên file: <b>$fileName</b>";
            }
        } else {
            echo "Chỉ cho phép tải lên các file có định dạng .mp3 và .wav.";
        }
    } elseif (isset($_FILES["file_welcome"]) && $_FILES["file_welcome"]["error"] == UPLOAD_ERR_OK) {
        //$fileName = $_FILES["file_welcome"]["name"];
        $fileName = sanitizeFileName($_FILES["file_welcome"]["name"]);
        $tempFilePath = $_FILES["file_welcome"]["tmp_name"];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($fileExtension == 'mp3' || $fileExtension == 'wav') {
            if (uploadToWelcomeFolder($fileName, $tempFilePath, $DuognDanThuMucJson)) {

                echo "File <b>$fileName</b> đã được tải lên thành công";
            } else {
                echo "Đã xảy ra lỗi khi tải lên file: <b>$fileName</b>";
            }
        } else {
            echo "Chỉ cho phép tải lên các file có định dạng .mp3 và .wav.";
        }
    } else {
        echo "Vui lòng chọn một file để tải lên.";
    }
} else {
    // Nếu không có tham số "folder" hoặc "delete_file" được truyền vào, thông báo lỗi
    echo json_encode(array("error" => "Cần thực hiện Truy Vấn"));
}
//echo $DuognDanThuMucJson;
?>
