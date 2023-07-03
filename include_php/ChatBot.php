<?php
//Code By: Vũ Tuyển
//Facebook: https://www.facebook.com/TWFyaW9uMDAx
include "../Configuration.php";
?>
<!DOCTYPE html>
<html>
<!-- Code By: Vũ Tuyển
Facebook: https://www.facebook.com/TWFyaW9uMDAx  -->
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $MYUSERNAME; ?>, Vietbot Offline Chat</title>
    <link rel="shortcut icon" href="../assets/img/VietBot128.png">
    <script src="../assets/js/jquery-3.6.1.min.js"></script>
    <link href="../assets/css/bootstrap_3.3.6.css" rel="stylesheet">
    <link href="../assets/css/font-awesome.min.css" rel="stylesheet">
    <style type="text/css">
        .fixed-panel {
					
            min-height: 390px;
            max-height: 390px;
            background-color: #19313c;
            color: white;
            overflow: auto;
        }

        .media-list {
            overflow: auto;
            clear: both;
            display: table;
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: normal;
            line-break: strict;
        }
        .panel {
			height: 100%;
            margin-bottom: 20px;
            background-color: #fff;
            border: 6px solid transparent;
            border-radius: 25px;
            -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
            box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
        }

        .panel-info {
            border-color: #0c2735;
        }
        .panel-info>.panel-heading {
            color: white;
            background-color: #0c2735;
            border-color: #0c2735;
        }

        .panel-footer {
            padding: 10px 15px;
            background-color: #0c2735;
            border-top: 1px solid #0c2735;
            border-bottom-right-radius: 3px;
            border-bottom-left-radius: 3px
        }
body {
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
    font-size: 14px;
    line-height: 1.42857143;
    color: #333;
    background-color: #dbe0c9;
}

    </style>
</head>

<body>
  


           
                <div id="chatPanel" class="panel panel-info">
                    <div class="panel-heading">
                        <strong><span class="glyphicon glyphicon-globe"></span> Nhập câu hỏi, bot sẽ tự trả lời hoặc mượn trả lời từ Google Assistant hoặc ChatGPT </strong>
                    </div>
                    <div class="panel-body fixed-panel">
                        <ul class="media-list">
                        </ul>
                    </div>
                    <div class="panel-footer">
                        <form method="post" id="chatbot-form">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Enter Message" name="messageText"
                                    id="messageText" autofocus />
                                <span class="input-group-btn">
                                    <button class="btn btn-info" type="button" id="chatbot-form-btn">Gửi</button>
                                    <button class="btn btn-info" type="button"
                                        id="chatbot-form-btn-clear">Xóa</button>
                                    <button class="btn btn-info" type="button"
                                        id="chatbot-form-btn-voice">Đọc ra Loa</button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
           
    
    <script src="../assets/js/jquery_1_12_4_min.js"></script>
    <script src="../assets/js/bootstrap_3_3_6_js_bootstrap.min.js"></script>
    <script>
        var exports = {};
    </script>
    <script src="../assets/js/speech_to_text_0_7_4_lib_index.js"></script>
    <script>
        $(function () {
            var synth = window.speechSynthesis;

            var msg = new SpeechSynthesisUtterance();
            var voices = synth.getVoices();
            msg.voice = voices[0];
            msg.rate = 1;
            msg.pitch = 1;
            msg.lang = 'vi-VN'

            $('#chatbot-form-btn').click(function (e) {
                e.preventDefault();
                $('#chatbot-form').submit();
            });
            $('#chatbot-form-btn-clear').click(function (e) {
                e.preventDefault();
                $('#chatPanel').find('.media-list').html('');
            });
            $('#chatbot-form-btn-voice').click(function (e) {
                e.preventDefault();

                var onAnythingSaid = function (text) {
                    console.log('Interim text: ', text);
                };
                var onFinalised = function (text) {
                    console.log('Finalised text: ', text);
                    $('#messageText').val(text);
                };
                var onFinishedListening = function () {
                    $('#chatbot-form-btn').click();
                };

                try {
                    var listener = new SpeechToText(onAnythingSaid, onFinalised, onFinishedListening);
                    listener.startListening();

                    setTimeout(function () {
                        listener.stopListening();
                        if ($('#messageText').val()) {
                            $('#chatbot-form-btn').click();
                        }
                    }, 5000);
                } catch (error) {
                    console.log(error);
                }
            });

            $('#chatbot-form').submit(function (e) {
                e.preventDefault();
                var message = $('#messageText').val();
                $(".media-list").append(
                    '<li class="media"><div class="media-body"><div class="media"><div style = "text-align:right; color : #2EFE2E" class="media-body">' +
                    message + '<hr/></div></div></div></li>');
                $.ajax({
                    url: "<?php echo "http://"."$HostName".":"."$PORT_CHATBOT"; ?>",
                    type: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    data: JSON.stringify({
                        "type": 2,
                        "data":  message
                    }),
/*                     dataType: "json",                  */
                    success: function (response) {
/*                         $('#messageText').val(''); */
                        console.log("API request succeeded:", response);
                        var answer = response.answer;
                        console.log("Data:", answer);                       
                        const chatPanel = document.getElementById("chatPanel");
                        $(".media-list").append(
                            '<li class="media"><div class="media-body"><div class="media"><div style = "color : white" class="media-body">' +
                            answer + '<hr/></div></div></div></li>');
                        $(".fixed-panel").stop().animate({
                            scrollTop: $(".fixed-panel")[0].scrollHeight
                        }, 1000);
                        msg.text = answer;
<!--                         speechSynthesis.speak(msg); -->
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            });
        });
    </script>
</body>

</html>