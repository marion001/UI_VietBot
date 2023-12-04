<?php
$uploadDir = '/home/pi/vietbot_offline/src/hotword/';

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
        echo "<li>".basename($file). " <font color=\"red\" type=\"button\" class=\"delete-button\" onclick=\"deleteFileAjax('$file')\" title=\"Xóa file: ". basename($file)."\">Xóa</font></li>";
    }
    echo "</ul>";
}

function deleteFile($filePath) {
    if (file_exists($filePath)) {
        $output = shell_exec("rm '$filePath'");
        if ($output === null) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "not_found";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_file') {
    if (isset($_POST['fileToDelete'])) {
        $fileToDelete = $_POST['fileToDelete'];
        deleteFile($fileToDelete);
    }
} elseif (isset($_GET['action']) && $_GET['action'] === 'list_files') {
    $selectedLanguage = $_GET['language'] ?? '';
    listFiles($uploadDir, $selectedLanguage);
}
?>
