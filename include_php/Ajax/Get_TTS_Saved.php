<?php

include "../../Configuration.php";


$directoryPath = $DuognDanThuMucJson.'/tts_saved';

// Hàm GET để truyền tên file và tải xuống file
if(isset($_GET['Download_TTS'])) {
    $requestedFile = $_GET['Download_TTS'];
    $fileToDownload = $directoryPath . '/' . $requestedFile;

    // Kiểm tra xem file tồn tại trong thư mục không
    if(file_exists($fileToDownload)) {
        // Thiết lập header để tải xuống file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($fileToDownload) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fileToDownload));
        readfile($fileToDownload);
        exit;
    } else {
        // Nếu file không tồn tại, trả về thông báo lỗi
        header("HTTP/1.0 404 Not Found");
        echo json_encode(array('error' => 'File not found'));
        exit;
    }
} elseif(isset($_GET['Play_TTS'])) {
    // Hàm GET để truyền tên file và phát file âm thanh
    $requestedFile = $_GET['Play_TTS'];
    $fileToPlay = $directoryPath . '/' . $requestedFile;

    // Kiểm tra xem file tồn tại trong thư mục không
    if(file_exists($fileToPlay)) {
        // Tạo đường dẫn phát file
        $filePlayLink = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?Download_TTS=' . $requestedFile;

        // Tạo thẻ audio để phát file
        $audioElement = '<audio controls autoplay><source src="' . $filePlayLink . '" type="audio/mpeg">Your browser does not support the audio element.</audio>';

        // Trả về thẻ audio
        echo $audioElement;
        exit;
    } else {
        // Nếu file không tồn tại, trả về thông báo lỗi
        header("HTTP/1.0 404 Not Found");
        echo json_encode(array('error' => 'File không tồn tại'));
        exit;
    }
} else {
    // Lấy danh sách tất cả các file trong thư mục
    $files = glob($directoryPath . '/*');

    // Sắp xếp các file dựa trên thời gian tạo
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });

    // Lấy file đầu tiên (file mới nhất)
    $newestFile = $files[0];
	$strippedPath = str_replace('/home/pi/vietbot_offline/src/', '', $newestFile);
    // Lấy thời gian tạo của file (timestamp)
    $timestamp = filemtime($newestFile);

    // Chuyển đổi thời gian tạo thành định dạng giờ, phút, giây
    $createdTime = date('H:i:s', $timestamp);

    // Tạo đường dẫn tải xuống file và đường dẫn phát file
    $fileDownloadLink = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?Download_TTS=' . basename($newestFile);
    $filePlayLink = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?Play_TTS=' . basename($newestFile);

    // Tạo một mảng kết quả
    $result = array(
        'tts_file' => basename($newestFile),
        'tts_file_path' => $newestFile,
        'tts_strippedPath' => $strippedPath,
        'created_time' => $createdTime,
        'timestamp' => $timestamp,
        'download_link' => $fileDownloadLink,
        'play_link' => $filePlayLink
    );

    // Chuyển đổi mảng kết quả thành định dạng JSON và trả về
    echo json_encode($result);
}
?>
