<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Đường dẫn đến thư mục lưu trữ file
    $uploadDir = '/home/pi/vietbot_offline/src/hotword/';

    // Số lượng file tối đa cho mỗi lần tải lên
    $maxFiles = 20;

    // Ngôn ngữ được chọn, mặc định là 'vi' nếu không được xác định
    //$selectedLanguage = $_POST['language_hotword'] ?? 'vi';
    $selectedLanguage = $_POST['language_hotword'] ?? '';

    // Kiểm tra xem ngôn ngữ có được xác định không
	if ($selectedLanguage !== 'eng' && $selectedLanguage !== 'vi') {
    echo "Chỉ nhấp nhận tải lên khi chọn ngôn ngữ Tiếng Anh hoặc Tiếng Việt";
    return;
	}
    // Thêm ngôn ngữ vào đường dẫn
    $uploadDir .= $selectedLanguage . '/';

    // Kiểm tra xem thư mục lưu trữ file đã tồn tại chưa, nếu không thì tạo mới
/*  
  if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
*/
    // Kiểm tra xem có tệp tin được tải lên hay không
    if (isset($_FILES['files'])) {
        $files = $_FILES['files'];

        // Duyệt qua từng file
        for ($i = 0; $i < count($files['name']); $i++) {
            $fileName = $files['name'][$i];
            $fileTmpName = $files['tmp_name'][$i];

            // Lấy thông tin về tên file
            $fileInfo = pathinfo($fileName);

            // Kiểm tra nếu phần mở rộng là '.ppn'
            if (strtolower($fileInfo['extension']) === 'ppn') {
                // Đường dẫn đến file đích
                $destination = $uploadDir . $fileName;

                // Di chuyển file tải lên đến đúng đường dẫn
                move_uploaded_file($fileTmpName, $destination);
				 chmod($destination, 0777);
                // Thông báo thành công
                echo "Tải lên thành công: $fileName\n";
            } else {
                // Thông báo lỗi nếu phần mở rộng không phải '.ppn'
                echo "Chỉ chấp nhận tệp tin có phần mở rộng .ppn!";
            }
        }
    }
}
?>
