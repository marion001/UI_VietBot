<?php 
// Code By: Vũ Tuyển 
// Facebook: https://www.facebook.com/TWFyaW9uMDAx 
include "../Configuration.php"; 
?>
<script src="../../assets/js/axios_0.21.1.min.js"></script>
<link rel="stylesheet" href="../../assets/css/bootstrap-icons.css">

</head>

<body>
    <br/>
    <div class="chat-container">
        <div class="chat-wrapper">
            <div id="message-content" class="message-content bg-primary text-white">Chào
                <?php echo $MYUSERNAME; ?> mình là loa thông minh Vietbot!</div>
        </div>

        <div id="chatbox" class="container-fluid"></div>
        <form id="chat-form" class="chat-form">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <select id="message-type-checkbox" class="form-select">
                        <option selected value="VietbotChatBox" title="Chế Độ Hỏi Đáp Ở Chatbox Không Phát Ra Loa">Hỏi Đáp</option>
                        <option value="Gemini" title="Áp Dụng Trợ Lý Ảo">AI Beta</option>
                        <option value="VietbotPodcast" title="Phát Nhạc, Podcast Ra Loa">PodCast</option>
                        <option value="VietbotTTS" title="TTS Chuyển Văn Bản Thành Giọng Nói Để Đọc Ra Loa">Chỉ Đọc</option>

                    </select>
                </div>
                <input type="text" class="form-control" id="user-input" class="chat-input" placeholder="Nhập văn bản, nội dung, tin nhắn..." aria-label="Recipient's username" aria-describedby="basic-addon2">

                <div class="input-group-append">
                    <button type="submit" class="btn btn-success">Gửi</button>
                </div>
            </div>
        </form>
        <center>
            <div class="btn-group-toggle chat-form-button" data-toggle="buttons">
                <button id="delete-all-button" class="btn btn-danger">Xóa tất cả tin nhắn</button>
				
				<i id="openPage" title="Mở trong tab mới" class="bi bi-chevron-double-up" style="cursor: pointer;"></i>

            </div>
        </center>
    </div>
	
<audio id="audioElementTTS" hidden></audio>

<script>
    function getTimestamp() {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        return `${hours}:${minutes}:${seconds}`;
    }
    const RESPONSE_TIMEOUT = 23000; // Thời gian chờ phản hồi cuối (21 giây) để hiển thị thông báo
    const WAIT_MESSAGE_TIMEOUT = 7000; // Thời gian chờ hiển thị thông báo "Vui lòng chờ thêm" (7 giây)
    const WAIT_MESSAGE = 'Vui lòng chờ thêm...'; // Nội dung thông báo chờ phản hồi
    const TIMEOUT_MESSAGE = 'Có vẻ Vietbot đang không phản hồi, vui lòng thử lại!'; // Nội dung thông báo hết thời gian chờ
    const ERROR_MESSAGE_CONNECTION = 'Không kết nối được tới API Vietbot!';
    const chatbox = document.getElementById('chatbox');
    const chatForm = document.getElementById('chat-form');
    const userInput = document.getElementById('user-input');
    const deleteAllButton = document.getElementById('delete-all-button');
    //const messageTypeCheckbox = document.getElementById('message-type-checkbox');
    let typingIndicator;
    let isBotReplying = false;
    let waitMessageTimer; // Biến đếm thời gian chờ hiển thị WAIT_MESSAGE
    let responseTimer; // Biến đếm thời gian chờ phản hồi
    // Tải session từ localStorage nếu có
    let chatSession = JSON.parse(localStorage.getItem('chatSession')) || [];
    let containsWord = false;
	// Tạo một mảng chứa các từ khóa cần kiểm tra
	let keywordsToCheck = [ "code",
						"viết",
						"encode",
						"decode",
						"tạo",
						"đun",
						"nấu",
						"xào",
						"hầm",
						"chiên",
						"hướng dẫn",
						"lập trình",
						"mã hóa",
						"giải mã"];
    //console.log(chatSession);
    // Hiển thị tin nhắn từ session đã lưu khi tải trang
    chatSession.forEach(function(message) {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message');
        if (message.sender === 'Bot') {
            messageElement.classList.add('bot-message');
        } else {
            messageElement.classList.add('user-message');
        }
        const messageContent = document.createElement('div');
        messageContent.classList.add('message-content');
        //messageContent.innerHTML = '<strong>' + message.sender + ':</strong> ' + message.message;
        messageContent.innerHTML = message.message;
        messageElement.appendChild(messageContent);
        chatbox.appendChild(messageElement);
    });

    // Cuộn xuống cuối chatbox sau khi thêm tin nhắn
    chatbox.scrollTop = chatbox.scrollHeight;

    chatForm.addEventListener('submit', async(event) => {
        event.preventDefault();
        const userMessage = userInput.value;
        userInput.value = '';

        if (userMessage.trim() === '') {
            return;
        }
        var botSelector = document.querySelector('#message-type-checkbox').value; // Lấy giá trị bot từ select
        // Lấy ra option được chọn trong select box của form
        //console.log(botSelector);
        const selectedOption = document.querySelector('#message-type-checkbox option:checked');
        // Kiểm tra xem option đã được chọn hay chưa
        // const messageType = parseInt(messageTypeCheckbox.value);
        //Nếu select được chọn là: Google gemini
        if (botSelector === 'Gemini') {
            displayMessage(userMessage, true);
            isBotReplying = true;
            typingIndicator = displayTypingIndicator();
            // Đặt thời gian hiển thị thông báo "Vui lòng chờ thêm..."
            waitMessageTimer = setTimeout(() => {
                if (isBotReplying) {
                    displayMessage(WAIT_MESSAGE, false, true);
                }
            }, WAIT_MESSAGE_TIMEOUT);
            //End thông báo vui lòng chờ thêm
            url_api_gemini = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=<?php echo $key_Gemini; ?>';

            axios.post(url_api_gemini, {
                contents: [{
                    parts: [{
                        text: userMessage
                    }]
                }]
            })

            .then(function(response) {
                    clearTimeout(waitMessageTimer); // Xóa thông báo "Vui lòng chờ thêm..." nếu đã hiển thị
                    isBotReplying = false;
                    removeTypingIndicator();
                    // Xóa thông báo "Vui lòng chờ thêm..." nếu tồn tại trong chatbox
                    const waitMessageElement = chatbox.querySelector('.timeout-message');
                    if (waitMessageElement) {
                        waitMessageElement.remove();
                    }

                    // Thêm tin nhắn từ bot vào chatSession
                    var botGeminiMessage = response.data.candidates[0].content.parts[0].text;
                    //console.log(botGeminiMessage);
                    displayMessage(botGeminiMessage, false);
                })
                .catch(function(error) {
                    clearTimeout(waitMessageTimer); // Xóa thông báo "Vui lòng chờ thêm..." nếu đã hiển thị
                    isBotReplying = false;
                    removeTypingIndicator();
                    // Xóa thông báo "Vui lòng chờ thêm..." nếu tồn tại trong chatbox
                    const waitMessageElement = chatbox.querySelector('.timeout-message');
                    if (waitMessageElement) {
                        waitMessageElement.remove();
                    }
                    // Hiển thị thông báo lỗi vào chatbox
                    var errorMessage = 'Đã xảy ra lỗi: ' + error;
                    displayMessage(errorMessage, false);
                });
        } else if (botSelector === 'VietbotPodcast') {
            displayMessage(userMessage, true);
            isBotReplying = true;
            typingIndicator = displayTypingIndicator();
            // Đặt thời gian hiển thị thông báo "Vui lòng chờ thêm..."
            waitMessageTimer = setTimeout(() => {
                if (isBotReplying) {
                    displayMessage(WAIT_MESSAGE, false, true);
                }
            }, WAIT_MESSAGE_TIMEOUT);
            //End thông báo vui lòng chờ thêm
            // Đặt thời gian hiển thị thông báo "Có vẻ Vietbot đang không phản hồi, vui lòng thử lại!"
            responseTimer = setTimeout(() => {
                if (isBotReplying) {
                    displayMessage(TIMEOUT_MESSAGE, false, true);
                    removeTypingIndicator();
                    isBotReplying = false;
                }
                const waitMessageElement = chatbox.querySelector('.timeout-message');
                if (waitMessageElement) {
                    waitMessageElement.remove();
                }
            }, RESPONSE_TIMEOUT);
            const url = 'http://<?php echo $serverIP; ?>:<?php echo $Port_Vietbot; ?>';
            const data = {
                type: 2,
                data: "play_podcast",
                name: userMessage,
                player_type: "system"
            };

            axios.post(url, data, {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(function(response) {
                    //console.log(response);
                    // Xóa thông báo "Vui lòng chờ thêm..." nếu đã hiển thị
                    clearTimeout(waitMessageTimer);
                    // Xóa thông báo "có vẻ vietbot đang không phản hồi" nếu đã hiển thị
                    clearTimeout(responseTimer);
                    isBotReplying = false;
                    removeTypingIndicator();
                    // Xóa thông báo "Vui lòng chờ thêm..." nếu tồn tại trong chatbox
                    const waitMessageElement = chatbox.querySelector('.timeout-message');
                    if (waitMessageElement) {
                        waitMessageElement.remove();

                    }
                    displayMessage(response.data.response, false);

                })
                .catch(function(error) {
                    // Xóa thông báo "Vui lòng chờ thêm..." nếu đã hiển thị
                    clearTimeout(waitMessageTimer);
                    // Xóa thông báo "có vẻ vietbot đang không phản hồi" nếu đã hiển thị
                    clearTimeout(responseTimer);
                    isBotReplying = false;
                    removeTypingIndicator();
                    // Xóa thông báo "Vui lòng chờ thêm..." nếu tồn tại trong chatbox
                    const waitMessageElement = chatbox.querySelector('.timeout-message');
                    if (waitMessageElement) {
                        waitMessageElement.remove();
                    }
                    // Hiển thị thông báo lỗi vào chatbox
                    var errorMessage = 'Đã xảy ra lỗi: ' + error;
                    displayMessage(errorMessage, false);
                });
        } else if (botSelector === 'VietbotTTS') {
            displayMessage(userMessage, true);
            isBotReplying = true;
            typingIndicator = displayTypingIndicator();
            // Đặt thời gian hiển thị thông báo "Vui lòng chờ thêm..."
            waitMessageTimer = setTimeout(() => {
                if (isBotReplying) {
                    displayMessage(WAIT_MESSAGE, false, true);
                }
            }, WAIT_MESSAGE_TIMEOUT);
            //End thông báo vui lòng chờ thêm
            // Đặt thời gian hiển thị thông báo "Có vẻ Vietbot đang không phản hồi, vui lòng thử lại!"
            responseTimer = setTimeout(() => {
                if (isBotReplying) {
                    displayMessage(TIMEOUT_MESSAGE, false, true);
                    removeTypingIndicator();
                    isBotReplying = false;
                }
                const waitMessageElement = chatbox.querySelector('.timeout-message');
                if (waitMessageElement) {
                    waitMessageElement.remove();
                }
            }, RESPONSE_TIMEOUT);
            const url = 'http://<?php echo $serverIP; ?>:<?php echo $Port_Vietbot; ?>';
            const data = {
                type: 1,
                data: userMessage
            };

            axios.post(url, data, {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(function(response) {
                    //console.log(response.data.response);
                    // Xóa thông báo "Vui lòng chờ thêm..." nếu đã hiển thị
                    clearTimeout(waitMessageTimer);
                    // Xóa thông báo "có vẻ vietbot đang không phản hồi" nếu đã hiển thị
                    clearTimeout(responseTimer);
                    isBotReplying = false;
                    removeTypingIndicator();
                    // Xóa thông báo "Vui lòng chờ thêm..." nếu tồn tại trong chatbox
                    const waitMessageElement = chatbox.querySelector('.timeout-message');
                    if (waitMessageElement) {
                        waitMessageElement.remove();

                    }
					

//Kiểm tra điều kiện để đẩy thông tin kết quả ra chatbox
// Kiểm tra điều kiện để đẩy thông tin kết quả ra chatbox
if (response.data.state === "OK") {
    // Gửi yêu cầu GET bằng Axios
    axios.get("Ajax/Get_TTS_Saved.php")
        .then(response_tts => {
            // Xử lý phản hồi từ máy chủ
            if (response_tts.data) {
                // Dữ liệu từ phản hồi được lưu trong response.data
                //console.log('Dữ liệu từ phản hồi:', response_tts.data);
                let tts_response  = response_tts.data;
                // Hiển thị thông điệp trong chatbox
				
				phanhoi = response.data.response + " <i id='playerSTT' title='Nghe: "+tts_response.tts_file+"' data-url_local_tts='"+tts_response.tts_strippedPath+"' onclick='playerTTS(this)' class='bi bi-play-circle'></i> <a href='"+tts_response.download_link+"' title='Tải xuống: "+tts_response.tts_file+"' target='_bank'><i class='bi bi-download'></i></a>";
				
                displayMessage(phanhoi, false);
                // Hiển thị download link trong console
                //console.log(tts_response.download_link);
                // Hiển thị download link trong chatbox (nếu cần)
                // displayMessage(tts_response.download_link, false);
            } else {
                console.log('Không có dữ liệu từ phản hồi. Get_TTS_Saved.php');
				displayMessage(response.data.response, false);
            }
        })
        .catch(error => {
            // Xử lý lỗi nếu có
            console.error('Lỗi khi gửi yêu cầu GET Get_TTS_Saved.php:', error);
			displayMessage(response.data.response, false);
        });
} else {
    //console.log("tts thất bại");
    displayMessage(response.data.response, false);
}

                    
                    
                })
                .catch(function(error) {
                    // Xóa thông báo "Vui lòng chờ thêm..." nếu đã hiển thị
                    clearTimeout(waitMessageTimer);
                    // Xóa thông báo "Vui lòng chờ thêm..." nếu đã hiển thị
                    clearTimeout(responseTimer);
                    isBotReplying = false;
                    removeTypingIndicator();
                    // Xóa thông báo "Vui lòng chờ thêm..." nếu tồn tại trong chatbox
                    const waitMessageElement = chatbox.querySelector('.timeout-message');
                    if (waitMessageElement) {
                        waitMessageElement.remove();
                    }
                    // Hiển thị thông báo lỗi vào chatbox
                    var errorMessage = 'Đã xảy ra lỗi: ' + error;
                    displayMessage(errorMessage, false);
                });
        }
        //Vietbot Xử lý Chatbox
        else if (botSelector === 'VietbotChatBox') {
            //Ngoài Lệ Ném Cho Trợ Vietbot Xử Lý
            try {
                // Kiểm tra kết nối tới API trước khi gửi yêu cầu để đưa ra thông báo
                const response = await axios.get('http://<?php echo $serverIP; ?>:<?php echo $Port_Vietbot; ?>');
                if (response.status === 200) {
                    // Kết nối thành công, tiến hành gửi yêu cầu và xử lý câu trả lời
                    displayMessage(userMessage, true);
                    // Gửi yêu cầu tới API và xử lý câu trả lời như trước
                    userInput.value = ''; // Xóa giá trị của userInput sau khi gửi
                }
            } catch (error) {
                displayMessage(userMessage, true);
                // Kết nối thất bại, hiển thị thông báo lỗi cho người dùng
                displayMessage(ERROR_MESSAGE_CONNECTION, false, true);
                return;
            }
            // Chuyển đổi userMessageee thành chữ thường để kiểm tra xem có chứa từ code, viết, dễ dàng hơn
            var lowercaseMessage = userMessage.toLowerCase();
			// Kiểm tra xem userMessageee có chứa các từ khóa cần kiểm tra không
			var containsWord = keywordsToCheck.some(keyword => lowercaseMessage.includes(keyword));

            // Kiểm tra xem userMessageee có chứa từ "code", "viết" hoặc "lập trình" không
            //nếu tin nhắn gửi đi chứa các từ đã cho thì quăng cho gemini xử lý
            //if (lowercaseMessage.includes("code") || lowercaseMessage.includes("viết") || lowercaseMessage.includes("lập trình") || lowercaseMessage.includes("giải mã") || lowercaseMessage.includes("mã hóa")) {
            if (containsWord) {
                //nếu tin nhắn gửi đi chứa các từ đã cho thì quăng cho gemini xử lý
                //console.log("Tin nhắn của người dùng chứa từ 'code', 'viết' hoặc 'lập trình'.");
                isBotReplying = true;
                typingIndicator = displayTypingIndicator();
                // Đặt thời gian hiển thị thông báo "Vui lòng chờ thêm..."
                waitMessageTimer = setTimeout(() => {
                    if (isBotReplying) {
                        displayMessage(WAIT_MESSAGE, false, true);
                    }
                }, WAIT_MESSAGE_TIMEOUT);
                //End thông báo vui lòng chờ thêm
                url_api_gemini = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=<?php echo $key_Gemini; ?>';

                axios.post(url_api_gemini, {
                        contents: [{
                            parts: [{
                                text: userMessage
                            }]
                        }]
                    })
                    .then(function(response) {
                        clearTimeout(waitMessageTimer); // Xóa thông báo "Vui lòng chờ thêm..." nếu đã hiển thị
                        //clearTimeout(responseTimer); // Xóa thông báo "Có vẻ Vietbot đang không phản hồi, vui lòng thử lại!" nếu đã hiển thị
                        isBotReplying = false;
                        removeTypingIndicator();
                        // Xóa thông báo "Vui lòng chờ thêm..." nếu tồn tại trong chatbox
                        const waitMessageElement = chatbox.querySelector('.timeout-message');
                        if (waitMessageElement) {
                            waitMessageElement.remove();
                        }

                        var botGeminiMessage = response.data.candidates[0].content.parts[0].text;
                        displayMessage(botGeminiMessage, false);
                    })
                    .catch(function(error) {
                        clearTimeout(waitMessageTimer); // Xóa thông báo "Vui lòng chờ thêm..." nếu đã hiển thị
                        //clearTimeout(responseTimer); // Xóa thông báo "Có vẻ Vietbot đang không phản hồi, vui lòng thử lại!" nếu đã hiển thị
                        isBotReplying = false;
                        removeTypingIndicator();
                        // Xóa thông báo "Vui lòng chờ thêm..." nếu tồn tại trong chatbox
                        const waitMessageElement = chatbox.querySelector('.timeout-message');
                        if (waitMessageElement) {
                            waitMessageElement.remove();
                        }
                        // Hiển thị thông báo lỗi vào chatbox
                        var errorMessage = 'Đã xảy ra lỗi: ' + error;
                        displayMessage(errorMessage, false);
                    });
            }
            //Vietbot Xử lý nếu không có chứa từ khóa
            else if (!containsWord){
                //console.log("Tin nhắn của người dùng không chứa từ 'code', 'viết' hoặc 'lập trình'.");
                //Bắt đầu xử lý Api Chatbox
                ///////////////////////////
                const url = 'http://<?php echo $serverIP; ?>:<?php echo $Port_Vietbot; ?>/';
                const headers = {
                    Accept: '*/*',
                    'Accept-Language': 'vi',
                    'Content-Type': 'application/json',
                };
                const data = {
                    type: 3,
                    data: userMessage
                };
                try {
                    isBotReplying = true;
                    typingIndicator = displayTypingIndicator();
                    // Đặt thời gian hiển thị thông báo "Vui lòng chờ thêm..."
                    waitMessageTimer = setTimeout(() => {
                        if (isBotReplying) {
                            displayMessage(WAIT_MESSAGE, false, true);
                        }
                    }, WAIT_MESSAGE_TIMEOUT);

                    // Đặt thời gian hiển thị thông báo "Có vẻ Vietbot đang không phản hồi, vui lòng thử lại!"
                    responseTimer = setTimeout(() => {
                        if (isBotReplying) {
                            displayMessage(TIMEOUT_MESSAGE, false, true);
                            removeTypingIndicator();
                            isBotReplying = false;
                        }
                        const waitMessageElement = chatbox.querySelector('.timeout-message');
                        if (waitMessageElement) {
                            waitMessageElement.remove();
                        }
                    }, RESPONSE_TIMEOUT);
                    const response = await axios.post(url, data, {
                        headers
                    });
                    clearTimeout(waitMessageTimer); // Xóa thông báo "Vui lòng chờ thêm..." nếu đã hiển thị
                    clearTimeout(responseTimer); // Xóa thông báo "Có vẻ Vietbot đang không phản hồi, vui lòng thử lại!" nếu đã hiển thị
                    isBotReplying = false;
                    removeTypingIndicator();
                    // Xóa thông báo "Vui lòng chờ thêm..." nếu tồn tại trong chatbox
                    const waitMessageElement = chatbox.querySelector('.timeout-message');
                    if (waitMessageElement) {
                        waitMessageElement.remove();
                    }
                    //hiển thị kết quả trả về ra chatbox
                    displayMessage(response.data.response, false);
                    //console.log(response.data.response);

                } catch (error) {
                    //console.error(error);
                    // Hiển thị thông báo lỗi
                    //nếu sau 30 giây vẫn đang chờ câu trả lời từ API  thì nó sẽ coi đó là một trường hợp lỗi và thực hiện các hành động để thông báo về lỗi cho người dùng
                    setTimeout(() => {
                        if (isBotReplying) {
                            isBotReplying = false;
                            removeTypingIndicator();
                            displayMessage('Lỗi!, sự cố không xác định', false, true);
                        }
                    }, 30000);
                }
            } else {
				displayMessage(userMessage, true);
				displayMessage("Ngoại lệ, đã bị chặn bởi dev", false);
				
			}
        } else {
            displayMessage(userMessage, true);
            displayMessage("Trường hợp lựa chọn ngoại lệ, đã bị chặn bởi dev", false);
            //console.log("Trường hợp lựa chọn ngoại lệ, không có trong code");
        }
    });
    const displayMessage = (message, isUserMessage, isTimeoutMessage = false) => {
        var botSelectorr = document.querySelector('#message-type-checkbox').value;
        var botSelectorr_rep; // Khai báo biến botSelectorr_rep ở đây
        if (botSelectorr === 'Gemini') {
            // Gán giá trị cho botSelectorr_rep nếu botSelectorr là 'Gemini'
            botSelectorr_rep = "Vietbot AI";
        } else if (botSelectorr === 'VietbotPodcast') {
            botSelectorr_rep = "Vietbot->PodCast";
        } else if (botSelectorr === 'VietbotTTS') {
            botSelectorr_rep = "Vietbot->TTS";
        } else if (botSelectorr === 'VietbotChatBox') {
            botSelectorr_rep = "Vietbot";
        } else {
            botSelectorr_rep = "Vietbot";
        }
        let messageTypePrefix = isUserMessage ? " " : botSelectorr_rep + ': ';
        //console.log(message);
        //Nếu Giá trị là undefined
        if (typeof message === 'undefined') {
            //message = 'Nội dung trả về không được xác định';
            message = displayMessage(response.data.response, false);
            //return;
        }
        //Nếu Giá trị là null
        if (message === "Lỗi: argument of type 'NoneType' is not iterable") {
            message = 'Không tìm thấy nội dung phù hợp!';
            //return;
        }

        if (message === null) {
            message = 'Không nhận được dữ liệu trả về';
            //return;
        }

        const messageElement = document.createElement('div');
        messageElement.classList.add('message');
        if (isUserMessage) {
            messageElement.classList.add('user-message');
        } else {
            messageElement.classList.add('bot-message');
        }
        const timestamp = getTimestamp(); // Lấy thời gian
        const messageContent = document.createElement('div');
        messageContent.classList.add('message-content');
        // Hàm để hiển thị từng chữ cái của nội dung tin nhắn một cách tuần tự
       /* function typeWriter(element, text, index) {
                if (index < text.length) {
                    element.innerHTML += text.charAt(index);
                    index++;
                    setTimeout(() => typeWriter(element, text, index), 30); // Thay đổi thời gian hiển thị tại đây
                }
            } */
function typeWriter(element, text, index) {
    if (index < text.length) {
        // Kiểm tra xem ký tự hiện tại là ký tự ```
        if (text.charAt(index) === "`") {
            var endIndex = text.indexOf("```", index + 1); // Tìm vị trí của ký tự ``` kế tiếp
            if (endIndex !== -1) {
                // Hiển thị nội dung code với định dạng mong muốn
                element.innerHTML += "<code style='font-size: 17px;'><pre>" + text.substring(index, endIndex + 3) + "</pre></code>";
                index = endIndex + 3; // Cập nhật index
            } else {
                // Nếu không tìm thấy ký tự ``` kế tiếp, chỉ hiển thị ký tự `
                element.innerHTML += "`";
                index++;
            }
        } else {
            // Kiểm tra xem chuỗi text có chứa "</a>" không
            if (text.includes("</a>") || text.includes("</i>")) {
                // Nếu có, hiển thị toàn bộ chuỗi và kết thúc hàm
                element.innerHTML += text;
                return;
            } else {
                // Nếu không chứa "</a>", tiếp tục hiển thị từng ký tự
                element.innerHTML += text.charAt(index);
                index++;
            }
        }
        // Gọi đệ quy để tiếp tục hiển thị
        setTimeout(() => typeWriter(element, text, index), 30);
    }
}


            //Hiển thị tin nhắn lên html
            // Kiểm tra xem ô kiểm hiển thị thời gian được chọn hay không
            //nếu quá 250 ký tự
        const maxlengthMessage = 250;
        //if (showTimestampCheckbox.checked) {
        // Nếu ô kiểm được chọn
        if (!isUserMessage) {
            // Nếu tin nhắn là từ bot
            if (message.length > maxlengthMessage) {
                // Nếu tin nhắn dài hơn 250 ký tự, hiển thị tin nhắn mà không sử dụng typeWriter
                messageContent.innerHTML = '<b>[' + timestamp + ']' + messageTypePrefix + '</b><pre class="vietbot-code">' + message + '</pre>';
                // Lưu session vào localStorage nếu tin nhắn không chứa chuỗi "Vui lòng chờ thêm"
                if (!message.includes('Vui lòng chờ thêm')) {
                    chatSession.push({
                        sender: 'Bot',
                        message: '<b>[' + timestamp + ']' + messageTypePrefix + '</b> <pre class="vietbot-code">' + message + '</pre>'
                    });
                    localStorage.setItem('chatSession', JSON.stringify(chatSession));
                }
            } else {
                // Nếu tin nhắn ngắn hơn 250 ký tự, và không chứa các từ trong biến containsWord sử dụng typeWriter
                if (!containsWord) {
                    //console.log(containsWord);
                    messageContent.innerHTML = '<b>[' + timestamp + ']' + messageTypePrefix + '</b>';
                    typeWriter(messageContent, message, 0); // Gọi hàm typeWriter để hiển thị tin nhắn từ bot
                    // Lưu session vào localStorage nếu tin nhắn không chứa chuỗi "Vui lòng chờ thêm"
                    if (!message.includes('Vui lòng chờ thêm')) {
                        chatSession.push({
                            sender: 'Bot',
                            message: '<b>[' + timestamp + ']' + messageTypePrefix + '</b><pre class="vietbot-code">' + message + '</pre>'
                        });
                        localStorage.setItem('chatSession', JSON.stringify(chatSession));
                    }
                }
                //nếu tin nhắn có chứa containsWord thì hiển thị luôn
                else {
                    messageContent.innerHTML = '<b>[' + timestamp + ']' + messageTypePrefix + '</b><pre class="vietbot-code">' + message + '</pre>';
                    typeWriter(messageContent, 0, 0); // Gọi hàm typeWriter để hiển thị tin nhắn từ bot
                    // Lưu session vào localStorage nếu tin nhắn không chứa chuỗi "Vui lòng chờ thêm"
                    if (!message.includes('Vui lòng chờ thêm')) {
                        chatSession.push({
                            sender: 'Bot',
                            message: '<b>[' + timestamp + ']' + messageTypePrefix + '</b><pre class="vietbot-code">' + message + '</pre>'
                        });
                        localStorage.setItem('chatSession', JSON.stringify(chatSession));
                    }

                }
            }
        } else {
            // Nếu tin nhắn là từ người dùng, hiển thị tin nhắn với thời gian
            messageContent.innerHTML = '<b>[' + timestamp + ']' + messageTypePrefix + '</b>' + message;
            // Lưu session vào localStorage nếu tin nhắn không chứa chuỗi "Vui lòng chờ thêm"
            if (!message.includes('Vui lòng chờ thêm')) {
                // Lưu session vào localStorage
                chatSession.push({
                    sender: 'Bạn',
                    message: '<b>[' + timestamp + ']' + messageTypePrefix + '</b>' + message
                });
                localStorage.setItem('chatSession', JSON.stringify(chatSession));
            }
        }
        //} 
        const deleteButton = document.createElement('button');
        deleteButton.classList.add('delete-button');
        deleteButton.innerHTML = '&times;';
        deleteButton.addEventListener('click', () => {
            messageElement.remove();
        });

        messageElement.appendChild(messageContent);
        messageElement.appendChild(deleteButton);

        // Nếu là tin nhắn "Vui lòng chờ thêm", chèn phía dưới tin nhắn hiện tại
        if (isTimeoutMessage) {
            const currentMessages = chatbox.querySelectorAll('.message');
            const lastMessage = currentMessages[currentMessages.length - 1];
            if (lastMessage) {
                lastMessage.insertAdjacentElement('afterend', messageElement);
            } else {
                chatbox.appendChild(messageElement);
            }
        } else {
            chatbox.appendChild(messageElement);
        }

        chatbox.scrollTop = chatbox.scrollHeight;

        if (isTimeoutMessage) {
            messageElement.classList.add('timeout-message');
        }
    };
    const displayTypingIndicator = () => {
        const typingIndicator = document.createElement('div');
        typingIndicator.classList.add('typing-indicator');
        for (let i = 0; i < 3; i++) {
            const dot = document.createElement('span');
            typingIndicator.appendChild(dot);
        }

        chatbox.appendChild(typingIndicator);

        chatbox.scrollTop = chatbox.scrollHeight;

        return typingIndicator;
    };

    const removeTypingIndicator = () => {
        if (typingIndicator) {
            typingIndicator.remove();
        }

        chatbox.scrollTop = chatbox.scrollHeight;
    };

    deleteAllButton.addEventListener('click', () => {
        //Xóa  tất cả nội dung trong chatbox Và Seension sau đó tải lại trang
        chatbox.innerHTML = '';
        clearChatSession();
        location.reload();
    });

    function clearChatSession() {
        localStorage.removeItem('chatSession');
    }
	

</script>
<script>
    function playerTTS(element) {
        var url = element.getAttribute('data-url_local_tts');
        var audio = document.getElementById('audioElementTTS');
        if (audio) {
            if (audio.paused) {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'Ajax/Listen.php?song=' + url, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        const base64Audio = xhr.responseText;
                        audio.src = "data:audio/mpeg;base64," + base64Audio;
                        audio.load();
                        audio.play();
                    }
                };
                xhr.send();
            } else {
                audio.pause();
                audio.currentTime = 0;
            }
        } else {
            console.error('Phần tử audio không được tìm thấy.');
        }
    }
</script>

<script>
document.getElementById('openPage').addEventListener('click', function() {
    // URL của trang mới bạn muốn mở
    var newPageURL = "ChatBot.php";
    
    // Mở trang mới với URL được chỉ định và tên của trang đó
    window.open(newPageURL, "_blank");
});
</script>
</body>

</html>
