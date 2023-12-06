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
	if ($selectedLanguage == 'vi') {
    $selectedLanguageReplace = 'Tiếng Việt';
	} elseif ($selectedLanguage == 'eng') {
    $selectedLanguageReplace = 'Tiếng Anh';
	} elseif ($selectedLanguage == 'default') {
    $selectedLanguageReplace = 'Mặc Định';
	}
    $files = glob($uploadDir . $selectedLanguage . '/*.ppn');
	echo "<p>Tổng số file Hotword $selectedLanguageReplace: <font color=red>" . count($files) . "</font></p>";
    echo "<ul>";
	$fileCount = 0;
    foreach ($files as $file) {
		$fileCount++;
        echo "<li>" . end(explode('/', $file)) . " <font color=\"red\" type=\"button\" class=\"delete-button\" onclick=\"deleteFileAjax('$file')\" title=\"Xóa file: " . end(explode('/', $file)) . "\">Xóa</font>
		<font color=\"blue\" type=\"button\" class=\"download-button\" onclick=\"downloadFileAjax('".$selectedLanguage."/".end(explode('/', $file))."')\" title=\"Tải xuống file: " . end(explode('/', $file)) . "\">Tải xuống</font></li>";
	}
    echo "</ul>";
}
function deleteFile($filePath) {
    if (preg_match('/\.ppn$/', $filePath)) {
        $utf8FilePath = mb_convert_encoding($filePath, 'UTF-8', 'auto');
        if (file_exists($utf8FilePath)) {
            if (unlink($utf8FilePath)) {
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



function downloadFile($fileName, $selectedLanguage,$DuognDanThuMucJson) {
    $allowedExtension = 'ppn';
    $filePath = "$DuognDanThuMucJson/hotword/" . $selectedLanguage . '/' . $fileName;  // Đường dẫn đầy đủ
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
            downloadFile($fileToDownload, $selectedLanguage,$DuognDanThuMucJson);
        }
    }
}

?>
