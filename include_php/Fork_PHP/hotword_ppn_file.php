<?php
//Code By: Vũ Tuyển
//Facebook: https://www.facebook.com/TWFyaW9uMDAx
include "../../Configuration.php";
?>
<?php
$uploadDir = "$DuognDanThuMucJson/hotword/";

function listFiles($uploadDir, $selectedLanguage) {
    if ($selectedLanguage === '') {
        echo "Ngôn ngữ không được xác định.";
        return;
    }
    $files = glob($uploadDir . $selectedLanguage . '/*.ppn');
	echo "<p>Tổng số file Hotword: <font color=red>" . count($files) . "</font></p>";
    echo "<ul>";
	$fileCount = 0;
    foreach ($files as $file) {
		$fileCount++;
        echo "<li>" . basename($file) . " <font color=\"red\" type=\"button\" class=\"delete-button\" onclick=\"deleteFileAjax('$file')\" title=\"Xóa file: " . basename($file) . "\">Xóa</font>
		<font color=\"blue\" type=\"button\" class=\"download-button\" onclick=\"downloadFileAjax('".$selectedLanguage."/".basename($file)."')\" title=\"Tải xuống file: " . basename($file) . "\">Tải xuống</font></li>";
	}
    echo "</ul>";
}

function deleteFile($filePath) {
    // Kiểm tra xem đường dẫn file hợp lệ không
    if (preg_match('/\.ppn$/', $filePath)) {
        // Sử dụng escapeshellarg để tránh các tấn công command injection
        $escapedFilePath = escapeshellarg($filePath);
        // Kiểm tra xem file tồn tại không
        if (file_exists($filePath)) {
            // Thực hiện lệnh xóa với lệnh shell
            $output = shell_exec("rm $escapedFilePath");
            // Kiểm tra xem lệnh đã thành công không
            if ($output === null) {
                echo "success";
            } else {
                echo "error";
            }
        } else {
            echo "not_found";
        }
    } else {
        echo "not_ppn_file";
    }
}


function downloadFile($fileName, $selectedLanguage) {
    $allowedExtension = 'ppn';
    $filePath = '/home/pi/vietbot_offline/src/hotword/' . $selectedLanguage . '/' . $fileName;  // Đường dẫn đầy đủ
    $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
    if (file_exists($filePath) && strtolower($fileExtension) === $allowedExtension) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        ob_clean();
        readfile($filePath);
        exit;
    } else {
        echo "not_found";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_file') {
    if (isset($_POST['fileToDelete'])) {
        $fileToDelete = $_POST['fileToDelete'];
        deleteFile($fileToDelete);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    if ($_GET['action'] === 'list_files') {
        $selectedLanguage = $_GET['language'] ?? '';
        listFiles($uploadDir, $selectedLanguage);
    } elseif ($_GET['action'] === 'download_file') {
        if (isset($_GET['fileToDownload'])) {
            $fileToDownload = $_GET['fileToDownload'];
            $selectedLanguage = isset($_GET['language']) ? $_GET['language'] : '';
            downloadFile($fileToDownload, $selectedLanguage);
        }
    }
}

?>
