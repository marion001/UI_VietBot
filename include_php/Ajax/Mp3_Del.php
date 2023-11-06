<?php
//Code By: Vũ Tuyển
//Facebook: https://www.facebook.com/TWFyaW9uMDAx
include "../../Configuration.php";
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Lấy giá trị từ tham số truyền qua URL
    $fileToDelete = isset($_GET['fileToDelete']) ? $_GET['fileToDelete'] : null;

    if ($fileToDelete) {
        // Đường dẫn đầy đủ của file
        $fullPathToDelete = $DuognDanThuMucJson.'/mp3/' . basename($fileToDelete);

        // Kiểm tra xem file có đuôi là .mp3 không
        $fileInfo = pathinfo($fullPathToDelete);

        if (
            $fileInfo['extension'] === 'mp3' &&
            strpos(realpath($fullPathToDelete), $DuognDanThuMucJson.'/mp3/') === 0 &&
            file_exists($fullPathToDelete)
        ) {
            // Đảm bảo rằng file nằm trong thư mục cho phép, có đuôi là .mp3 và tồn tại trước khi xóa
            unlink($fullPathToDelete);
            echo "File " . basename($fullPathToDelete) . " đã được xóa thành công.";
        } else {
            echo "File không tồn tại hoặc đã bị xóa";
        }
    } else {
        echo "Tham số 'fileToDelete' không hợp lệ.";
    }
}
?>
