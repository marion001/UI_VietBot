<?php
include "../../Configuration.php";
?>
<?php

// Kiểm tra xem biến volume_state_json đã được gửi hay chưa
if(isset($_GET['volume_state_json'])) {
    // Lấy giá trị của biến volume_state_json
    $volume_state_json = $_GET['volume_state_json'];

    // Kiểm tra xem giá trị có phải là một số hợp lệ từ 0 đến 100 không
    if(is_numeric($volume_state_json) && $volume_state_json >= 0 && $volume_state_json <= 100) {
        // Chuyển đổi giá trị thành số nguyên
        $volume_state_json = intval($volume_state_json);

        // Đường dẫn đến tệp state.json
        $file_path = "$DuognDanThuMucJson/state.json"; // Sử dụng biến $DuongDanThuMucJson

        // Đọc dữ liệu từ tệp state.json và chuyển thành mảng
        $state_json = json_decode(file_get_contents($file_path), true);

        // Cập nhật giá trị volume trong mảng dữ liệu
        $state_json['volume'] = $volume_state_json;

        // Chuyển mảng thành chuỗi JSON
        $updated_data = json_encode($state_json, JSON_PRETTY_PRINT);

        // Ghi nội dung đã cập nhật vào tệp state.json
        file_put_contents($file_path, $updated_data);

        // In ra thông báo khi ghi dữ liệu thành công
        echo "Dữ liệu: $volume_state_json đã được cập nhật vào state.json->volume thành công.";
    } else {
        // Nếu giá trị không hợp lệ, in ra thông báo lỗi
        echo "Giá trị không hợp lệ. Vui lòng chỉ nhập số từ 0 đến 100.";
    }
} else {
    // Nếu không có dữ liệu được gửi, in ra thông báo
    echo "Không có dữ liệu được gửi.";
}
?>




