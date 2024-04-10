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
        echo "File not found.";
    }
}

// Hàm để tải lên tệp vào thư mục default
function uploadToDefaultFolder($fileName, $tempFilePath) {
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
function uploadToWelcomeFolder($fileName, $tempFilePath) {
    $uploadDir = $DuognDanThuMucJson.'/sound/welcome/';
    $uploadFile = $uploadDir . basename($fileName);
    if (move_uploaded_file($tempFilePath, $uploadFile)) {
		chmod($uploadFile, 0777);
        return true;
    } else {
        return false;
    }
}

// Kiểm tra xem có tham số "folder" có được truyền vào hay không
if (isset($_GET['folder'])) {
    // Lấy giá trị của tham số "folder" từ GET
    $folder = $_GET['folder'];

    // Đường dẫn tới thư mục con cần kiểm tra
    $dir = $DuognDanThuMucJson.'/sound/'.$folder;

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
    $filePath = $DuognDanThuMucJson.'/sound/'.$_GET['download_file'];

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
    $dir = $DuognDanThuMucJson.'/sound/'.$folder;

    // Kiểm tra nếu tệp tồn tại và xóa nếu có
    $filePath = $dir . '/' . $file;
    if (deleteFile($filePath)) {
        echo "Tệp $file đã được xóa thành công.";
    } else {
        echo "Không thể xóa tệp $file.";
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kiểm tra nếu có tệp được tải lên
    // Kiểm tra xem người dùng đã chọn tệp hay chưa
    if (isset($_FILES["file_default"]) && $_FILES["file_default"]["error"] == UPLOAD_ERR_OK) {
        $fileName = $_FILES["file_default"]["name"];
        $tempFilePath = $_FILES["file_default"]["tmp_name"];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($fileExtension == 'mp3' || $fileExtension == 'wav') {
            if (uploadToDefaultFolder($fileName, $tempFilePath)) {
                echo "Tệp $fileName đã được tải lên thành công vào thư mục default.";
            } else {
                echo "Đã xảy ra lỗi khi tải lên tệp vào thư mục default.";
            }
        } else {
            echo "Chỉ cho phép tải lên các tệp .mp3 và .wav.";
        }
    } elseif (isset($_FILES["file_welcome"]) && $_FILES["file_welcome"]["error"] == UPLOAD_ERR_OK) {
        $fileName = $_FILES["file_welcome"]["name"];
        $tempFilePath = $_FILES["file_welcome"]["tmp_name"];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($fileExtension == 'mp3' || $fileExtension == 'wav') {
            if (uploadToWelcomeFolder($fileName, $tempFilePath)) {

                echo "Tệp $fileName đã được tải lên thành công vào thư mục welcome.";
            } else {
                echo "Đã xảy ra lỗi khi tải lên tệp vào thư mục welcome.";
            }
        } else {
            echo "Chỉ cho phép tải lên các tệp .mp3 và .wav.";
        }
    } else {
        echo "Vui lòng chọn một tệp để tải lên.";
    }
} else {
    // Nếu không có tham số "folder" hoặc "delete_file" được truyền vào, thông báo lỗi
    echo json_encode(array("error" => "Tham số 'folder' hoặc 'delete_file' không được cung cấp."));
}

?>
