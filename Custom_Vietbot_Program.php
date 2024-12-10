<?php
#Code By: Vũ Tuyển
include 'Configuration.php';

if ($Config['web_interface']['login_authentication']['active']){
    session_start();
    if (!isset($_SESSION['user_login']) || 
        (isset($_SESSION['user_login']['login_time']) && (time() - $_SESSION['user_login']['login_time'] > 43200))) {
        session_unset();
        session_destroy();
        header('Location: Login.php');
        exit;
    }
    // $_SESSION['user_login']['login_time'] = time();
}
?>
<?php
// Đường dẫn đến thư mục chứa các file Python
$directory = $VietBot_Offline_Path.'src/';

// Xử lý API
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        
        // Lấy danh sách các tệp .py
if ($action === 'get_files') {
    // Danh sách các tệp không được phép
    $disallowedFiles = ['start.py'];
    
    // Lấy tất cả các tệp .py trong thư mục và lọc chỉ các tệp hợp lệ
    $files = array_filter(glob($directory . '*.py'), 'is_file');
    
    // Loại bỏ các tệp không hợp lệ
    $validFiles = array_filter($files, function($file) use ($disallowedFiles) {
        return !in_array(basename($file), $disallowedFiles); // Loại bỏ các tệp không hợp lệ
    });

    // Trả về danh sách các tệp hợp lệ dưới dạng mảng có chỉ mục liên tiếp
    echo json_encode(array_values(array_map('basename', $validFiles)));
    exit;
}


        // Lấy nội dung tệp
        if ($action === 'get_content' && isset($_GET['file'])) {
			// Ngăn chặn truy cập tệp không hợp lệ
            $file = basename($_GET['file']);
            $filePath = $directory . $file;
            if (file_exists($filePath)) {
                echo file_get_contents($filePath);
            } else {
                http_response_code(404);
                echo 'File not found';
            }
            exit;
        }
    }
}

// Xử lý lưu nội dung tệp
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['file']) && isset($_POST['content'])) {
        $file = basename($_POST['file']); // Ngăn chặn truy cập tệp không hợp lệ
        $filePath = $directory . $file;
        if (file_exists($filePath)) {
            file_put_contents($filePath, $_POST['content']);
            echo 'File saved successfully';
        } else {
            http_response_code(404);
            echo 'File not found';
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'html_head.php'; ?>
    <link rel="stylesheet" href="assets/vendor/codemirror/codemirror.min.css">
    <link rel="stylesheet" href="assets/vendor/codemirror/dracula.min.css">
    <style>
        #codeEditor {
            width: 100%;
            margin-top: 0px;
        }
        .CodeMirror {
			border-radius: 10px;
            height: 450px;
        }
    </style>
</head>
<body>
<?php include 'html_header_bar.php'; ?>
<?php include 'html_sidebar.php'; ?>
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Custom Vietbot</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Trang Chủ</a></li>
                <li class="breadcrumb-item active">Chỉnh sửa</li>
            </ol>
        </nav>
    </div>


<div class="form-floating mb-3">
<select class="form-select border-success" id="fileSelect">
<option value="">--Chưa có tệp nào được chọn--</option>
</select>
<label for="fileSelect">Chọn Tệp python</label>
</div>
<textarea id="codeEditor"></textarea>
<br/>
<center><button class="btn btn-primary rounded-pill" id="saveBtn">Lưu Code</button></center>


</main>
<?php include 'html_footer.php'; ?>
<script src="assets/vendor/codemirror/codemirror.min.js"></script>
<script src="assets/vendor/codemirror/python.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileSelect = document.getElementById('fileSelect');
        const codeEditor = document.getElementById('codeEditor');
        const saveBtn = document.getElementById('saveBtn');
        let editor;

        // Khởi tạo CodeMirror
        editor = CodeMirror.fromTextArea(codeEditor, {
            mode: 'python',
            theme: 'dracula',
            lineNumbers: true,
            indentUnit: 4,
            tabSize: 4,
            matchBrackets: true
        });

        // Lấy danh sách tệp .py sử dụng XMLHttpRequest
        function loadFiles() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', '?action=get_files');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            const files = JSON.parse(xhr.responseText);
                            if (Array.isArray(files)) {
                                files.forEach(file => {
                                    const option = document.createElement('option');
                                    option.value = file;
                                    option.textContent = file;
                                    fileSelect.appendChild(option);
                                });
								//showMessagePHP('Load dữ liệu thành công', 3);
                            } else {
								show_message('Dữ liệu trả về không phải là mảng: ' +xhr.responseText);
                            }
                        } catch (error) {
                            show_message("Lỗi phân tích dữ liệu JSON: " +error);
                        }
                    } else {
                        show_message("Lỗi từ server: " + xhr.status + xhr.statusText);
                    }
                }
            };
            xhr.send();
        }

        // Gọi hàm tải danh sách tệp
        loadFiles();

        // Khi người dùng chọn tệp
        fileSelect.addEventListener('change', function() {
            const fileName = this.value;
            if (fileName) {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', '?action=get_content&file=' + encodeURIComponent(fileName));
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {editor.setValue(xhr.responseText);showMessagePHP('Đã nạp nội dung tệp: '+fileName);} else {
                            show_message("Lỗi tải nội dung tệp: " +xhr.status + xhr.statusText);
                        }
                    }
                };
                xhr.send();
            } else {
                editor.setValue('');
            }
        });

        // Lưu nội dung tệp sử dụng XMLHttpRequest
        saveBtn.addEventListener('click', function() {
            const fileName = fileSelect.value;
            if (fileName) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
							showMessagePHP('Lưu tệp '+fileName+' thành công', 3);
                        } else {
                            show_message("Lỗi lưu tệp: " + xhr.status + xhr.statusText);
                        }
                    }
                };
                xhr.send('file=' + encodeURIComponent(fileName) + '&content=' + encodeURIComponent(editor.getValue()));
            } else {
                show_message('Vui lòng chọn tệp để lưu');
            }
        });
    });
</script>
<?php include 'html_js.php'; ?>
</body>
</html>
