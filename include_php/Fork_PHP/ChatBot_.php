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

                    <!--	<span class="input-group-text" id="basic-addon1">Chỉ đọc: &nbsp;<input title="Chỉ đọc nội dung văn bản bạn đã nhập ra loa và sẽ không hiển thị trong giao diện chatbox" type="checkbox" class="form-check-input" id="message-type-checkbox">
					</span> -->
                    <select id="message-type-checkbox" class="form-select">
                        <option selected value="3" title="Chế Độ Hỏi Đáp Ở Chatbox Không Phát Ra Loa">Hỏi Đáp</option>
                        <option value="Gemini" title="Áp Dụng Trợ Lý Ảo">AI</option>
                        <option value="2" title="Phát Nhạc, Podcast Ra Loa" data-podcastname="play_podcast">PodCast</option>
                        <option value="1" title="TTS Chuyển Văn Bản Thành Giọng Nói Để Đọc Ra Loa">Chỉ Đọc</option>

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
                <label class="btn btn-secondary">
                    <input type="checkbox" checked autocomplete="off" id="show-timestamp-checkbox"> Hiển thị thời gian
                </label>
                <button id="delete-all-button" class="btn btn-danger">Xóa tất cả tin nhắn</button>
            </div>
        </center>
    </div>
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
    const messageTypeCheckbox = document.getElementById('message-type-checkbox');
    let typingIndicator;
    let isBotReplying = false;
    let waitMessageTimer; // Biến đếm thời gian chờ hiển thị WAIT_MESSAGE
    let responseTimer; // Biến đếm thời gian chờ phản hồi
    // Tải session từ localStorage nếu có
    let chatSession = JSON.parse(localStorage.getItem('chatSession')) || [];
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
        if (selectedOption) {
            // Nếu option đã được chọn, kiểm tra xem có thuộc tính data-podcastname không
            const podcastName = selectedOption.getAttribute('data-podcastname');
            if (podcastName) {
                // Nếu có thuộc tính data-podcastname, hiển thị giá trị trong console.log
                //console.log('Data Podcast Name:', podcastName);
                userMessageee = podcastName;
            } else {
                // Nếu không có thuộc tính data-podcastname, thông báo lỗi hoặc thực hiện hành động khác tùy thuộc vào yêu cầu của bạn
                //console.log('Option được chọn nhưng không có thuộc tính data-podcastname.');
                userMessageee = userMessage;

            }
        } else {
            userMessageee = userMessage;
        }

        const messageType = parseInt(messageTypeCheckbox.value);
        //Nếu select được chọn là: Google gemini
        if (botSelector === 'Gemini') {
            //console.log(userMessage);
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
                    //clearTimeout(responseTimer); // Xóa thông báo "Có vẻ Vietbot đang không phản hồi, vui lòng thử lại!" nếu đã hiển thị
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
        //Vietbot Xử lý Chatbox
        else {
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

            // Tạo một đối tượng JSON với các từ cần kiểm tra
            const wordsToCheck = {
                "code": true,
                "viết": true,
                "encode": true,
                "decode": true,
                "tạo": true,
                "hướng dẫn": true,
                "lập trình": true,
                "mã hóa": true,
                "giải mã": true
            };

            // Chuyển đổi userMessageee thành chữ thường để kiểm tra xem có chứa từ code, viết, dễ dàng hơn
            var lowercaseMessage = userMessageee.toLowerCase();
            // Kiểm tra xem userMessageee có chứa từ trong danh sách từ cần kiểm tra không
            let containsWord = false;
            for (const word in wordsToCheck) {
                if (lowercaseMessage.includes(word)) {
                    containsWord = true;
                    break;
                }
            }
            // Kiểm tra xem userMessageee có chứa từ "code", "viết" hoặc "lập trình" không
            //if (lowercaseMessage.includes("code") || lowercaseMessage.includes("viết") || lowercaseMessage.includes("lập trình") || lowercaseMessage.includes("giải mã") || lowercaseMessage.includes("mã hóa")) {
            if (containsWord) {
                //console.log("Tin nhắn của người dùng chứa từ 'code', 'viết' hoặc 'lập trình'.");
                //console.log(userMessage);
                //displayMessage(userMessage, true);
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

                        // Thêm tin nhắn từ bot vào chatSession
                        var botGeminiMessage = response.data.candidates[0].content.parts[0].text;
                        //console.log(botGeminiMessage);
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

            //Vietbot Xử lý
            else {
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
                    type: messageType,
                    //ChatBox + TTS
                    data: userMessageee,

                    //PodCasst
                    name: userMessage,
                    player_type: "system"
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
            }
        }
    });

    const showTimestampCheckbox = document.getElementById('show-timestamp-checkbox');
    // Thêm một trình nghe sự kiện để xử lý sự thay đổi của ô kiểm
    showTimestampCheckbox.addEventListener('change', () => {
        // Bật/tắt lớp 'hide-timestamp' trên khung chat dựa vào trạng thái của ô kiểm
        chatContainer.classList.toggle('hide-timestamp', !showTimestampCheckbox.checked);
    });

    const displayMessage = (message, isUserMessage, isTimeoutMessage = false) => {
        var botSelectorr = document.querySelector('#message-type-checkbox').value;
        var botSelectorr_rep; // Khai báo biến botSelectorr_rep ở đây
        if (botSelectorr === 'Gemini') {
            // Gán giá trị cho botSelectorr_rep nếu botSelectorr là 'Gemini'
            botSelectorr_rep = "Vietbot AI";
        } else if (botSelectorr === '2') {
            botSelectorr_rep = "Vietbot->PodCast";
        } else if (botSelectorr === '1') {
            botSelectorr_rep = "Vietbot->TTS";
        } else {
            botSelectorr_rep = "Vietbot";
        }
        //console.log(botSelectorr_rep);

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
        function typeWriter(element, text, index) {
                if (index < text.length) {
                    element.innerHTML += text.charAt(index);
                    index++;
                    setTimeout(() => typeWriter(element, text, index), 50); // Thay đổi thời gian hiển thị tại đây
                }
            }
            //Hiển thị tin nhắn lên html
            // Kiểm tra xem ô kiểm hiển thị thời gian được chọn hay không
            //nếu quá 250 ký tự
        const maxlengthMessage = 250;
        if (showTimestampCheckbox.checked) {
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
                    // Nếu tin nhắn ngắn hơn 250 ký tự, sử dụng typeWriter
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
        } else {
            // Nếu ô kiểm không được chọn
            if (!isUserMessage) {
                // Nếu tin nhắn là từ bot
                if (message.length > maxlengthMessage) {
                    // Nếu tin nhắn không dài hơn 250 ký tự, hiển thị tin nhắn mà không sử dụng typeWriter
                    messageContent.innerHTML = '<b>' + messageTypePrefix + '</b><pre class="vietbot-code">' + message + '</pre>';
                    // Lưu session vào localStorage nếu tin nhắn không chứa chuỗi "Vui lòng chờ thêm"
                    if (!message.includes('Vui lòng chờ thêm')) {
                        chatSession.push({
                            sender: 'Bot',
                            message: '<b>' + messageTypePrefix + '</b><pre class="vietbot-code">' + message + '</pre>'
                        });
                        localStorage.setItem('chatSession', JSON.stringify(chatSession));
                    }
                } else {
                    // Nếu tin nhắn dưới 250 ký tự, sử dụng typeWriter
                    messageContent.innerHTML = '<b>' + messageTypePrefix + '</b><pre class="vietbot-code">' + message + '</pre>';
                    // Gọi hàm typeWriter để hiển thị tin nhắn từ bot
                    typeWriter(messageContent, 0, 0);
                    if (!message.includes('Vui lòng chờ thêm')) {
                        chatSession.push({
                            sender: 'Bot',
                            message: '<b>' + messageTypePrefix + '</b><pre class="vietbot-code">' + message + '</pre>'
                        });
                        localStorage.setItem('chatSession', JSON.stringify(chatSession));
                    }
                }
            } else {
                // Nếu tin nhắn là từ người dùng, hiển thị tin nhắn với thời gian
                messageContent.innerHTML = '<b>' + messageTypePrefix + '</b><pre class="vietbot-code">' + message + '</pre>';
                // Lưu session vào localStorage
                if (!message.includes('Vui lòng chờ thêm')) {
                    chatSession.push({
                        sender: 'Bạn',
                        message: '<b>' + messageTypePrefix + '</b><pre class="vietbot-code">' + message + '</pre>'
                    });
                    localStorage.setItem('chatSession', JSON.stringify(chatSession));
                }
            }
        }
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
</script>
</body>

</html>
