<?php 
#Code By: Vũ Tuyển
#Designed by: BootstrapMade
#Facebook: https://www.facebook.com/TWFyaW9uMDAx
include 'Configuration.php';
?>
  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <!-- Thông báo -->
  <script src="assets/vendor/jquery/jquery-3.5.1.min.js"></script>
  <script src="assets/vendor/popper/popper.min.js"></script>
  <script src="assets/vendor/hls/hls.js"></script>
  <!--END Thông báo -->

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
<script>
//Hiển thị thời gian
window.onload = setInterval(updateTime, 1000);

function updateTime() {
 if (document.getElementById("times")){
    var d = new Date();
    var hour = d.getHours();
    var min = d.getMinutes();
    var sec = d.getSeconds();
    document.getElementById("times").innerHTML = formatTime(hour) + ":" + formatTime(min) + ":" + formatTime(sec);
	//console.log(formatTime(hour) + ":" + formatTime(min) + ":" + formatTime(sec))
 }
}

function formatTime(unit) {
    return unit < 10 ? "0" + unit : unit;
}

// Cập nhật ngày và thứ chỉ một lần khi trang tải
function updateDate() {
	if (document.getElementById("days") && document.getElementById("dates")) {
    var d = new Date();
    var date = d.getDate();
    var month = d.getMonth();
    //var montharr = ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"];
    var montharr = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
    var year = d.getFullYear();
    var day = d.getDay();
    var dayarr = ["Chủ Nhật", "Thứ 2", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7"];
    document.getElementById("days").innerHTML = dayarr[day];
    document.getElementById("dates").innerHTML = date + "/" + montharr[month] + "/" + year;
    //console.log(date + " " + montharr[month] + " " + year);
	}
}
//Cập nhật ngày tháng khi trang tải xong
document.addEventListener('DOMContentLoaded', function() {
    // Lần cập nhật đầu tiên sau 1 giây
    updateDate();
    // Lần cập nhật thứ hai sau 3 giây (cách lần đầu tiên 2 giây)
    setTimeout(updateDate, 2000);
});


// Hàm để hiển thị hoặc ẩn overlay
function loading(action) {
    const overlay = document.getElementById('loadingOverlay');
    if (action === 'show') {
        overlay.style.display = 'flex';
    } else if (action === 'hide') {
        overlay.style.display = 'none';
    }
}
loading("hide");
</script>
<script>
//Hộp thoại thông báo khi người dúng nhấn vào button submit dùng onclick
function confirmRestore(text_notify) {
            var result = confirm(text_notify);
			if (result) {
            loading('show');
			}
            // Nếu người dùng nhấn "Cancel", ngăn không cho form được submit
            return result;
        }

//tìm lại mật khẩu
function forgotPassword() {
    loading("show");
    var email = document.getElementById("forgotPassword_email").value;
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "Login.php?forgot_password&mail=" + encodeURIComponent(email), true);
    xhr.onreadystatechange = function() {
        loading("hide");
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                show_message("Thành công, mật khẩu của bạn là: <b>" + response.message + "</b>");
            } else {
                show_message("Lỗi: " + response.message + "<hr/>- Bạn có thể thay đổi/tìm lại mật khẩu thủ công bằng cách truy cập giá trị <b>config.json->web_interface->login_authentication->password</b>");
            }
        }
    };
    xhr.send();
}

//Đọc dữ liệu cấu trúc bên trong file backup theo path
function read_file_backup(path_backup_file) {
	loading('show');
    // Tạo URL yêu cầu tới script PHP
    var url = 'includes/php_ajax/Show_file_path.php?read_file_backup&file=' + encodeURIComponent(path_backup_file);
    var xhr = new XMLHttpRequest();
    // Mở yêu cầu GET
    xhr.open('GET', url, true);
    // Xử lý khi yêu cầu thành công
    xhr.onload = function() {
        if (xhr.status === 200) {
			 var fileName = path_backup_file.split('/').pop();
            try {
                // Phân tích cú pháp JSON
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
					loading('hide');
                    // Tạo bảng để hiển thị thông tin tệp
                    var table = '<table class="table table-bordered border-primary">';
					table += '<tr><th colspan="3"  class="text-success"><center>Cấu Trúc Tệp: '+fileName+'</center></th></tr>';
                    table += '<tr><th><center>STT</center></th><th><center>Tên tệp</center></th><th><center>Hành động</center></th></tr>';
                    // Duyệt qua danh sách các tệp trong response.data
                    response.data.forEach(function(file, index) {
                        table += '<tr>';
                        table += '<td style="text-align: center; vertical-align: middle;">' + (index + 1) + '</td>'; // STT
                        table += '<td style="vertical-align: middle;"><font color=blue>' + file + '</font></td>'; // Tên tệp
                        table += '<td style="text-align: center; vertical-align: middle;">';
                        // Hành động: Bạn có thể thêm các nút hoặc liên kết hành động tại đây
                        table += '<button type="button" class="btn btn-success" onclick="read_files_in_backup(\''+path_backup_file+'\', \'' + file + '\')" title="Xem nội dung tệp tin: \''+file+'\'"><i class="bi bi-eye"></i> Xem</button>';
                        table += '</td>';
                        table += '</tr>';
                    });
                    table += '</table>';
					
				if (document.getElementById('show_all_file_folder_Backup_Program')) {
                    document.getElementById('show_all_file_folder_Backup_Program').innerHTML = table;
                }else if (document.getElementById('show_all_file_folder_Backup_web_interface')){
					document.getElementById('show_all_file_folder_Backup_web_interface').innerHTML = table;
				}
                   // document.getElementById(id_inter_html).innerHTML = table; // Hiển thị bảng
                } else {
					loading('hide');
                    show_message(response.message);
                }
            } catch (e) {
				loading('hide');
                // Lỗi trong quá trình phân tích JSON
                show_message('Lỗi xử lý dữ liệu: ' + e.message);
            }
        } else {
			loading('hide');
            // Lỗi HTTP khác ngoài 200 (OK)
            show_message('Lỗi tải dữ liệu: ' + xhr.status);
        }
    };
    xhr.onerror = function() {
        show_message("Lỗi kết nối. Vui lòng thử lại sau.");
    };
    xhr.send();
}


//Đọc dữ liệu cấu trúc con bên trong file backup theo path
function read_files_in_backup(file_path, file_name) {
	loading('show');
    // Tạo URL yêu cầu tới script PHP
    var url = 'includes/php_ajax/Show_file_path.php?read_files_in_backup&file_path='+encodeURIComponent(file_path)+'&file_name='+ encodeURIComponent(file_name);
    var xhr = new XMLHttpRequest();
    // Mở yêu cầu GET
    xhr.open('GET', url, true);
    // Xử lý khi yêu cầu thành công
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                // Phân tích cú pháp JSON
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
					loading('hide');
                    // Kiểm tra nếu tệp là JSON
                    if (file_name.endsWith('.json')) {
                        // Hiển thị nội dung JSON
                        document.getElementById('modal-body-content').textContent = JSON.stringify(response.data, null, 2); // Hiển thị JSON với indent
                    } else {
                        // Làm sạch nội dung tệp khác
                        var fileContent = response.data
                            .replace(/\\r/g, '') // Xóa ký tự \r
                            .replace(/\\n/g, '\n'); // Thay thế \n bằng ký tự xuống dòng thực
                        document.getElementById('modal-body-content').textContent = fileContent; // Cập nhật nội dung
                    }
                    $('#responseModal_read_files_in_backup').modal('show'); // Hiện modal
                   // document.getElementById(id_inter_html).innerHTML = table; // Hiển thị bảng
                } else {
					loading('hide');
                    show_message(response.message);
                }
            } catch (e) {
				loading('hide');
                // Lỗi trong quá trình phân tích JSON
                show_message('Lỗi xử lý dữ liệu: ' + e.message);
            }
        } else {
			loading('hide');
            // Lỗi HTTP khác ngoài 200 (OK)
            show_message('Lỗi tải dữ liệu: ' + xhr.status);
        }
    };
    xhr.onerror = function() {
        show_message("Lỗi kết nối. Vui lòng thử lại sau.");
    };
    xhr.send();
}

//Hàm tải xuống file theo đường dẫn
function downloadFile(filePath) {
    var link = document.createElement('a');
    link.href = 'includes/php_ajax/Download_file_path.php?file=' + encodeURIComponent(filePath);
    link.target = '_blank';
    link.download = filePath.substring(filePath.lastIndexOf('/') + 1); // Lấy tên file từ đường dẫn
    link.style.display = 'none'; // Ẩn liên kết
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

//Xóa File theo path
function deleteFile(filePath, source_backup=null, id_div_html=null) {
    if (!confirm("Bạn có chắc chắn muốn xóa file: '" + filePath.substring(filePath.lastIndexOf('/') + 1) + "' này không?")) {
        return;
    }
    loading("show");
	//Tách tên file, chỉ lấy đường dẫn path
	const directoryPath = filePath.substring(0, filePath.lastIndexOf('/'));
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'includes/php_ajax/Del_file_path.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        loading("hide");
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                showMessagePHP(response.message, 3);
            } else {
                show_message("<center>" + response.message + "</center>");
            }
			//Kiểm tra thẻ có id để load lại nội dung khi thực hiện hành động deleteFile
			if (document.getElementById(id_div_html)) {
				show_all_file_in_directory(directoryPath, source_backup, id_div_html);
			}
			
        } else {
            show_message("<center>Có lỗi xảy ra khi xóa file.</center>");
        }
    };
    xhr.send('filePath=' + encodeURIComponent(filePath));
}


//Hiển thị tất cả các file có trong thư mục show ra tên file, đường dẫn, thời gian tạo, kích thước tệp
function show_all_file_in_directory(directory_path, source_backup, resultDiv_Id) {
	//show_all_file_in_directory(đường dẫn, tên định danh, nội dung , id của thẻ cần hiển thị nội dung vào html)
	loading("show");
    var xhr = new XMLHttpRequest();
    var url = 'includes/php_ajax/Show_file_path.php?show_all_file&directory_path=' + directory_path;
    xhr.open('GET', url, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
				loading("hide");
                var response = JSON.parse(xhr.responseText);
                var resultDiv_show_all_File = document.getElementById(resultDiv_Id); // Sử dụng ID được truyền vào
                if (!resultDiv_show_all_File) {
					showMessagePHP('Không tìm thấy phần tử có id là: '+resultDiv_Id+' để hiển thị kết quả.');
                    return;
                }
                if (response.success) {
                    showMessagePHP(response.message);
                    //console.log(response);
                    // Tạo bảng để hiển thị thông tin tệp
                    var table = '<table class="table table-bordered border-primary">';
					table += '<tr><th colspan="5" class="text-primary" style="text-align: center; vertical-align: middle;">'+source_backup+'</th></tr>';
                    table += '<tr><th style="text-align: center; vertical-align: middle;">STT</th><th style="text-align: center; vertical-align: middle;">Tên tệp</th><th style="text-align: center; vertical-align: middle;">Thời gian tạo</th><th style="text-align: center; vertical-align: middle;">Kích thước</th><th style="text-align: center; vertical-align: middle;">Hành động</th></tr>';

                    response.data.forEach(function(file, index) {
                        table += '<tr>';
                        table += '<td style="text-align: center; vertical-align: middle;">' + (index + 1) + '</td>'; // STT
                        table += '<td style="text-align: center; vertical-align: middle;">' + file.name + '</td>'; // Tên tệp
                        table += '<td style="text-align: center; vertical-align: middle;">' + file.created_at + '</td>'; // Thời gian tạo
                        table += '<td style="text-align: center; vertical-align: middle;">' + file.size + '</td>'; // Kích thước
                        table += '<td style="text-align: center; vertical-align: middle;">';
						table += '<form method="POST" action=""><button type="submit" onclick="return confirmRestore(\'Bạn có chắc chắn muốn khôi phục dữ liệu từ bản sao lưu trên hệ thống: ' + file.name +'\')" name="Restore_Backup" value="' + file.path + '" class="btn btn-primary" title="Khôi phục dữ liệu: ' + file.name + '"><i class="bi bi-arrow-counterclockwise" title="Khôi phục dữ liệu: ' + file.name + '"></i></button> </form> ';
						table += ' <button type="button" class="btn btn-success" title="Xem cấu trúc bên trong tệp: ' + file.name + '" onclick="read_file_backup(\'' + file.path + '\')"><i class="bi bi-eye"></i></button> ';
						table += ' <button type="button" class="btn btn-warning" title="Tải xuống file: ' + file.name + '" onclick="downloadFile(\'' + file.path + '\')"><i class="bi bi-download"></i></button> ';
						table += ' <button type="button" class="btn btn-danger" onclick="deleteFile(\'' + file.path + '\', \''+source_backup+'\', \'' + resultDiv_Id + '\')"><i class="bi bi-trash"></i></button></td>';
                        table += '</tr>';
                    });

                    table += '</table>';
                    resultDiv_show_all_File.innerHTML = table; // Hiển thị bảng
                } else {
                    show_message(response.message);
                }
            } else {
				loading("hide");
                show_message('Có lỗi xảy ra: ' + xhr.status);
            }
        }
    };
    // Gửi yêu cầu
    xhr.send();
}
</script>
	
<!-- Chatbot -->
<!--
<script>
    // Hàm thay đổi class giữa modal-lg, modal-xl và modal-fullscreen và cập nhật icon dao diện chatbox
    function chatbot_toggleFullScreen() {
        // Lấy thẻ div cần thay đổi class
        var chatbotSizeSetting = document.getElementById('chatbot_size_setting');
        // Lấy thẻ icon cần thay đổi class
        var chatbotIcon = document.getElementById('chatbot_fullscreen');
        // Kiểm tra và thay đổi class giữa modal-lg, modal-xl, và modal-fullscreen
        if (chatbotSizeSetting.classList.contains('modal-lg')) {
            chatbotSizeSetting.classList.remove('modal-lg');
            chatbotSizeSetting.classList.add('modal-xl');
        } else if (chatbotSizeSetting.classList.contains('modal-xl')) {
            chatbotSizeSetting.classList.remove('modal-xl');
            chatbotSizeSetting.classList.add('modal-fullscreen');
            // Thay đổi icon thành bi-fullscreen-exit khi ở chế độ fullscreen
            chatbotIcon.classList.remove('bi-arrows-fullscreen');
            chatbotIcon.classList.add('bi-fullscreen-exit');
        } else if (chatbotSizeSetting.classList.contains('modal-fullscreen')) {
            chatbotSizeSetting.classList.remove('modal-fullscreen');
            chatbotSizeSetting.classList.add('modal-lg');
            // Trở lại icon fullscreen khi không ở chế độ fullscreen
            chatbotIcon.classList.remove('bi-fullscreen-exit');
            chatbotIcon.classList.add('bi-arrows-fullscreen');
        }
    }

    //  hàm cuộn xuống dưới cùng tin nhắn
    function scrollToBottom() {
        const chatbox = document.getElementById('chatbox');
        chatbox.scrollTop = chatbox.scrollHeight;
    }

    // Hàm lấy thời gian hiện tại dưới định dạng dd/mm/yyyy hh:mm:ss
    function getCurrentTime() {
        const now = new Date();
        const day = String(now.getDate()).padStart(2, '0');
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const year = now.getFullYear();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        return hours + ':' + minutes + ':' + seconds + ' ' + day + '/' + month + '/' + year;
    }

    // Hàm lưu tin nhắn vào localStorage
    function saveMessage(type, text) {
        const messages = JSON.parse(localStorage.getItem('messages')) || [];
        messages.push({
            type: type,
            text: text,
            time: getCurrentTime()
        });
        localStorage.setItem('messages', JSON.stringify(messages));
    }

    // Hàm xóa tin nhắn khỏi localStorage và giao diện
    function deleteMessage(index) {
        const messages = JSON.parse(localStorage.getItem('messages')) || [];
        messages.splice(index, 1);
        localStorage.setItem('messages', JSON.stringify(messages));
        loadMessages(); // Tải lại tin nhắn sau khi xóa
    }

// Hàm tải tin nhắn từ localStorage
function loadMessages() {
        const chatbox = document.getElementById('chatbox');
        const messages = JSON.parse(localStorage.getItem('messages')) || [];
        // Xóa nội dung hiện tại của chatbox
        chatbox.innerHTML = '';
        messages.forEach(function(message, index) {
            var messageHTML = '<div class="message ' + (message.type === 'user' ? 'user-message' : 'bot-message') + '">' +
                '<span class="delete_message_chatbox" data-index="' + index + '" title="Xóa tin nhắn">x</span>' +
                '<div class="message-time">' + message.time + '</div>';
            // Kiểm tra nếu tin nhắn là tệp âm thanh
            if (message.text && /^TTS_Audio.*\.(mp3|ogg|wav)$/i.test(message.text)) {
                var audioExtension = message.text.split('.').pop(); // Lấy đuôi mở rộng của tệp
                var fullAudioUrl = 'includes/php_ajax/Show_file_path.php?TTS_Audio=' + encodeURIComponent(message.text); // URL tới PHP proxy
                messageHTML +=
                    '<div class="audio-container">' +
                    '    <audio controls>' +
                    '        <source src="' + fullAudioUrl + '" type="audio/' + audioExtension + '">' +
                    '        Your browser does not support the audio element.' +
                    '    </audio>' +
                    '</div>';
            } else {
                messageHTML += '<div>' + message.text + '</div>';
            }
            messageHTML += '</div>';
            chatbox.innerHTML += messageHTML;
        });
        // Cuộn xuống dưới cùng
        scrollToBottom();
        // Thêm sự kiện click cho dấu x
        document.querySelectorAll('.delete_message_chatbox').forEach(function(button) {
            button.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'), 10);
                deleteMessage(index);
            });
        });
        // showMessagePHP("Đã tải lại dữ liệu Chatbox", 5);
    }
    // Hàm xóa tất cả tin nhắn từ localStorage và giao diện
function clearMessages() {
    if (!confirm("Bạn có chắc chắn muốn xóa lịch sử chat ?")) {
        return;
    }
    localStorage.removeItem('messages');
	// Tải lại chatbox sau khi xóa tất cả
    loadMessages();
}

// Hàm xóa tin nhắn khỏi localStorage và giao diện
function deleteMessage(index) {
    const messages = JSON.parse(localStorage.getItem('messages')) || [];
    messages.splice(index, 1);
    localStorage.setItem('messages', JSON.stringify(messages));
	// Tải lại tin nhắn sau khi xóa
    loadMessages();
}

// Hàm để dừng tất cả các phần tử audio đang phát
function stopAllAudio() {
	console.log("dừng audio");
    var audios = document.querySelectorAll('audio');
    audios.forEach(function(audio) {
        audio.pause();
		// Đặt thời gian phát lại về 0
        audio.currentTime = 0;
    });
}

// Hàm gửi yêu cầu POST và xử lý phản hồi
function sendRequest(message) {
    var data = JSON.stringify({
        "type": 3,
        "data": "main_processing",
        "action": "processing_api",
        "value": message
    });

    var xhr = new XMLHttpRequest();
    var chatbox = document.getElementById('chatbox');
    var typingIndicator = document.createElement('div');
    var timeout;
    typingIndicator.className = 'typing-indicator';
    typingIndicator.innerHTML = 'Đang xử lý...';
    chatbox.appendChild(typingIndicator);
    xhr.addEventListener("readystatechange", function() {
        if (this.readyState === 4) {
            clearTimeout(timeout);
            typingIndicator.remove();
            if (this.status === 200) {
                var response = JSON.parse(this.responseText);
				 stopAllAudio();
                var botMessageHTML = '';

                if (response.success) {
                    // Biểu thức chính quy kiểm tra chuỗi có bắt đầu bằng 'TTS_Audio' và kết thúc bằng mp3, ogg, hoặc wav
                    var audioUrl = response.message;
                    var audioPattern = /^TTS_Audio.*\.(mp3|ogg|wav)$/i;
                    if (audioPattern.test(audioUrl)) {
                        var audioExtension = audioUrl.split('.').pop(); // Lấy đuôi mở rộng của tệp
                        var fullAudioUrl = 'includes/php_ajax/Show_file_path.php?TTS_Audio=' + encodeURIComponent(audioUrl); // URL tới PHP proxy

                        botMessageHTML =
                            '<div class="message bot-message">' +
                            '    <div class="message-time">' + getCurrentTime() + '</div>' +
                            '    <div class="audio-container">' +
                            //'         <audio controls autoplay>' +
                            '         <audio controls>' +
                            '            <source src="' + fullAudioUrl + '" type="audio/' + audioExtension + '">' +
                            '            Your browser does not support the audio element.' +
                            '        </audio>' +
                            '    </div>' +
                            '</div>';
                    } else {
                        botMessageHTML =
                            '<div class="message bot-message">' +
                            '    <div class="message-time">' + getCurrentTime() + '</div>' +
                            '    <div>' + response.message + '</div>' +
                            '</div>';
                    }

                    // Thêm tin nhắn của bot vào chatbox
                    document.getElementById('chatbox').innerHTML += botMessageHTML;
                    // Lưu tin nhắn của bot vào localStorage
                    saveMessage('bot', response.message);

                } else {
                    var errorMessageHTML =
                        msg_error = "Có lỗi xảy ra. Vui lòng thử lại";
                    /*  
					'<div class="message">' +
                    '    Có lỗi xảy ra. Vui lòng thử lại.' +
                    '</div>';
					*/
                    '<div class="message bot-message">' +
                    '    <div class="message-time">' + getCurrentTime() + '</div>' +
                        '    <div>' + msg_error + '</div>' +
                        '</div>';
                    // Thêm tin nhắn lỗi vào chatbox
                    document.getElementById('chatbox').innerHTML += errorMessageHTML;
                    saveMessage('bot', msg_error);
                }
                setTimeout(scrollToBottom, 100);
            } else {
                msg_error = "Có vẻ bot đang không phản hồi, vui lòng thử lại.";
                var failureMessageHTML =
                    '<div class="message bot-message">' +
                    '    <div class="message-time">' + getCurrentTime() + '</div>' +
                    '    <div>' + msg_error + '</div>' +
                    '</div>';
                // Thêm tin nhắn thất bại vào chatbox
                document.getElementById('chatbox').innerHTML += failureMessageHTML;
                saveMessage('bot', msg_error);
            }
        }
    });

    xhr.open("POST", "<?php echo $Protocol.$serverIp.':'.$Port_API; ?>");
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(data);
    // Thiết lập hẹn giờ để hiển thị thông báo nếu phản hồi quá chậm
    timeout = setTimeout(function() {
        typingIndicator.innerHTML = 'Vui lòng chờ thêm...';
        timeout = setTimeout(function() {
            msg_error = "Có vẻ bot đang không phản hồi, vui lòng thử lại";
            typingIndicator.innerHTML = msg_error;
            saveMessage('bot', msg_error);
        }, 13000); // 13 giây nữa để tổng cộng là 20 giây
    }, 7000); // 7 giây
}

// Xử lý sự kiện khi nhấn nút gửi
document.getElementById('send_button_chatbox').addEventListener('click', function() {
    var userInput = document.getElementById('user_input_chatbox');
    var message = userInput.value.trim();
    if (message) {
        // Tạo nội dung HTML cho tin nhắn của người dùng
        const userMessageHTML =
            '<div class="message user-message">' +
            '    <div class="message-time">' + getCurrentTime() + '</div>' +
            '    <div>' + message + '</div>' +
            '</div>';
        // Thêm tin nhắn vào chatbox
        document.getElementById('chatbox').innerHTML += userMessageHTML;
        // Lưu tin nhắn của người dùng vào localStorage
        saveMessage('user', message);
        // Gửi yêu cầu với tin nhắn của người dùng
        sendRequest(message);
        // Xóa trường nhập liệu sau khi gửi
        userInput.value = '';
        //kéo xuống cuối cùng Chatbox
        setTimeout(scrollToBottom, 100);
    }
});

// Xử lý sự kiện nhấn phím Enter để gửi tin nhắn
document.getElementById('user_input_chatbox').addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        document.getElementById('send_button_chatbox').click();
    }
});
// Tải tin nhắn từ localStorage khi trang được tải
loadMessages();

//Khi chatbox được hiển thị hoàn toàn
document.addEventListener('DOMContentLoaded', () => {
    const myModal = document.getElementById('modalDialogScrollable_chatbot');
    myModal.addEventListener('shown.bs.modal', () => {
        scrollToBottom();
    });
});
</script>
-->
 
<script>
// Hàm gửi yêu cầu tới Command.php bằng XMLHttpRequest
function command_php(command_line, reload_page=null, mess=null) {
    // Kiểm tra nếu command_line không có giá trị
    if (!command_line) {
        showMessagePHP('Vui lòng nhập lệnh hợp lệ để thực thi.');
        return;
    }
    // Kiểm tra nếu có nội dung mess, hiển thị hộp thoại xác nhận
    if (mess) {
        const confirmAction = confirm(mess);
        if (!confirmAction) {
            return;
        }
    }
    loading('show');
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'Command.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            loading('hide');
            if (xhr.status === 200) {
                showMessagePHP("Thao tác thành công", 5);
				if (reload_page === true){
					location.reload();
				}
            } else {
                show_message('Lỗi: Không thể xử lý yêu cầu. Mã trạng thái:', xhr.status);
            }
        }
    };
    xhr.onerror = function() {
        loading('hide');
        show_message('Lỗi: Không thể kết nối tới máy chủ.');
    };
    xhr.send(command_line + '=1');
}
</script>