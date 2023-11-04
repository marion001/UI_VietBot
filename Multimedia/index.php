<?php
// Code By: Vũ Tuyển
// Facebook: https://www.facebook.com/TWFyaW9uMDAx
include "../Configuration.php";
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/img/VietBot128.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/loading.css">
    <link rel="stylesheet" href="../assets/css/4.5.2_css_bootstrap.min.css">
    <title>Media Player</title>
    <script src="../assets/js/jquery-3.6.1.min.js"></script>
    <style>
        body,
        html {
            background-color: #dbe0c9;
            overflow-x: hidden;
            /* Ẩn thanh cuộn ngang */
            
            max-width: 100%;
            /* Ngăn cuộn ngang trang */
        }
        /* Style để định dạng vị trí của hình ảnh và ghi chú */
        
        .image-container {
            display: flex;
            align-items: center;
        }
        
        .imagesize {
            height: 150px;
            width: 150px;
        }
        
        .caption {
            margin-left: 10px;
            /* Khoảng cách giữa hình ảnh và ghi chú */
        }
        
        .custom-div {
            max-height: 95vh;
            /* Chiều cao tối đa là 70% của chiều cao của màn hình */
            
            overflow-y: auto;
            /* Thêm thanh cuộn nếu nội dung vượt quá kích thước */
            
            background-color: #dbe0c9;
            /* Màu nền của div (có thể thay đổi) */
        }
        
        ::-webkit-scrollbar {
            width: 5px;
        }
        
        ::-webkit-scrollbar-track {
            -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
            -webkit-border-radius: 10px;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            -webkit-border-radius: 10px;
            border-radius: 10px;
            background: rgb(251, 255, 7);
            -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.5);
        }
        
        .rounded-image {
            border-radius: 50%;
            /* Góc bo tròn hình ảnh */
            
            overflow: hidden;
            width: 150px;
            /* Đổi kích thước hình ảnh theo nhu cầu */
            
            height: 150px;
            position: relative;
            animation: rotateImage 5s linear infinite;
            /* Xoay hình ảnh trong 5 giây vô hạn */
        }
        
        .rounded-image img {
            max-width: 100%;
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Đảm bảo hình ảnh đầy đủ và không bị méo */
            
            transform-origin: center;
            /* Đặt điểm xoay ở giữa hình ảnh */
        }
        
        @keyframes rotateImage {
            to {
                transform: rotate(360deg);
                /* Xoay ảnh 360 độ */
            }
        }
    </style>
</head>

<body>
    <div id="loading-overlay"><img id="loading-icon" src="../assets/img/Loading.gif" alt="Loading...">
        <div id="loading-message">- Đang Thực Hiện</div>
    </div>
<?php	
if (isset($Web_UI_Login) && $Web_UI_Login === true) {
	if (!isset($_SESSION['root_id'])) {
		echo "<br/><center><h1>Có Vẻ Như Bạn Chưa Đăng Nhập!<br/><br>
		- Nếu Bạn Đã Đăng Nhập, Hãy Nhấn Vào Nút Dưới<br/><br/><a href='$PHP_SELF'><button type='button' class='btn btn-danger'>Tải Lại</button></a></h1>
		</center>";
		exit();
}
	include "index_.php";
	
	} else {
	   
	   include "index_.php";
	   
	   
	}
?>
