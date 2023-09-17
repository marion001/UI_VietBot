<?php
// Code By: Vũ Tuyển
// Facebook: https://www.facebook.com/TWFyaW9uMDAx
include "../Configuration.php";
?>
<html>
<head>
    <title><?php echo $MYUSERNAME; ?>, Cấu Hình Skill VietBot</title>
	    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <link rel="shortcut icon" href="../assets/img/VietBot128.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/4.5.2_css_bootstrap.min.css">
	<link rel="stylesheet" href="../assets/css/loading.css">
	<link rel="stylesheet" href="../assets/css/11.3.1_styles_monokai-sublime.min.css">
	<script src="../assets/js/3.5.1_jquery.min.js"></script>
	<script src="../assets/js/1.16.0_umd_popper.min.js"></script>
	<script src="../assets/js/11.3.1_highlight.min.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>
<style>
    body,
    html {
        background-color: #dbe0c9;
        overflow-x: hidden;
        /* Ẩn thanh cuộn ngang */
        
        max-width: 100%;
        /* Ngăn cuộn ngang trang */
    }
    
    .scrollable-content {
        overflow-y: auto;
        max-height: 400px;
        display: none;
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
    
    .popup-container {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
    }
    
    .popup-container.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    #popupContent {
        background-color: white;
        padding: 20px;
        border: 1px solid gray;
        border-radius: 5px;
    }
    
    .scrollable-divradio {
        height: 200px;
        overflow: auto;
    }
    
    pre {
        border: 1px solid #ccc;
        border-radius: 5px;
        font-family: 'Courier New', monospace;
        font-size: 14px;
        white-space: pre-wrap;
        overflow: auto;
        /* Thêm thuộc tính này */
    }
    
    #popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    
    #popup-content {
        background-color: #ffffff00;
        padding: 5px;
        border-radius: 5px;
        width: 100vw;
        /* Sử dụng đơn vị vw cho chiều rộng tối đa */
        
        height: 100%;
        overflow: auto;
    }
    
    .thoi-gian-container {
        display: flex;
        align-items: center;
    }
    
    .thoi-gian-container label {
        margin-right: 10px;
    }
    
    .thoi-gian-container select {
        padding: 5px;
    }
</style>
<style>
    /* CSS cho popup */
    
    .popup-hass {
        display: none;
        justify-content: center;
        align-items: center;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 8;
    }
    /* CSS cho nội dung popup */
    
    .popup-hass-content {
        margin: 10px;
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        display: flex;
        flex-direction: column;
        /* Hiển thị nội dung dưới dạng cột */
        
        align-items: center;
        justify-content: center;
        position: relative;
        top: 50%;
        /* Dịch phần trên của popup lên 50% của màn hình */
        
        transform: translateY(-50%);
        /* Điều chỉnh phần trên của popup lên trên */
    }
    /* CSS cho nút đóng */
    
    .close-button {
        cursor: pointer;
        position: absolute;
        bottom: 10px;
        /* Đặt nút ở dưới cùng */
        
        right: 50%;
    }
</style>
	</head>

<?php	
if (isset($Web_UI_Login) && $Web_UI_Login === true) {
	if (!isset($_SESSION['root_id'])) {
		echo "<br/><center><h1>Có Vẻ Như Bạn Chưa Đăng Nhập!<br/><br>
		- Nếu Bạn Đã Đăng Nhập, Hãy Nhấn Vào Nút Dưới<br/><br/><a href='$PHP_SELF'><button type='button' class='btn btn-danger'>Tải Lại</button></a></h1>
		</center>";
		exit();
}
	include "Skill_.php";
	
	} else {
	   
	   include "Skill_.php";
	   
	   
	}
?>	
	