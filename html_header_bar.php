<?php
#Code By: Vũ Tuyển
#Designed by: BootstrapMade
#Facebook: https://www.facebook.com/TWFyaW9uMDAx
include 'Configuration.php';

?>
<head>
<!-- css ChatBot -->
<link href="assets/css/chatbot_head_bar.css" rel="stylesheet">
</head>
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" onclick="loading('show')" class="logo d-flex align-items-center" title="Trợ Lý Ảo <?php echo $Title_HEAD_BAR; ?>">
        <img src="<?php echo $Logo_File; ?>" alt="">
        <span class="d-none d-lg-block"><?php echo $Title_HEAD_BAR; ?></span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->


    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">



<div id="container_time" class="border-success pe-3">
    <div id="day-date-container_time">
        <div id="days"></div>,&nbsp;<div id="dates"></div>
    </div>
 <font color="red">   <div id="times"></div></font>
</div>
<li class="nav-item dropdown">
<?php
include 'Notify.php';
?>
 </li><!-- End Notification Nav -->
<!-- Chatbot Biểu tượng mở chatbox -->
<!--
<li class="nav-item nav-icon">
    <i class="bi bi-chat-dots text-primary" type="button" class="btn btn-primary" title="Mở ChatBot" data-bs-toggle="modal" data-bs-target="#modalDialogScrollable_chatbot"></i>
</li>
<div class="modal fade" id="modalDialogScrollable_chatbot" tabindex="-1" data-bs-backdrop="false" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" id="chatbot_size_setting">
        <div class="modal-content">
            <div id="welcome-message" class="welcome-message">ChatBot
			<div class="icon-group_chatbot">
			<i class="bi bi-arrow-repeat pe-3" onclick="loadMessages()" title="Tải lại Chatbox"></i>
			<i class="bi bi-arrows-fullscreen pe-3" id="chatbot_fullscreen" onclick="chatbot_toggleFullScreen()" title="Phóng to, thu nhỏ giao diện chatbox"></i>
                <i class="bi bi-x-lg" data-bs-dismiss="modal" title="Đóng ChatBox"></i>
            </div>
            </div>
            <div class="modal-body">
                <div id="chatbox_wrapper">
                    <div id="chatbox"></div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="input-group mb-3">
				 <button class="btn btn-info border-success" onclick="Recording_STT('start', '6')"><i class="bi bi-mic"></i></button>
                    <input type="text" class="form-control border-success" id="user_input_chatbox" placeholder="Nhập tin nhắn...">
                      <button id="send_button_chatbox" class="btn btn-primary border-success" title="Gửi tin nhắn"><i class="bi bi-send"></i>
                    </button>
					<button id="re-load_button_chatbox" class="btn btn-info border-success" onclick="loadMessages()" title="Tải lại Chatbox"><i class="bi bi-arrow-repeat"></i>
                </button>
					<button id="clear_button_chatbox" class="btn btn-warning border-success" onclick="clearMessages()" title="Xóa lịch sử Chat"><i class="bi bi-trash"></i>
                </button>
				 <button type="button" class="btn btn-danger border-success" data-bs-dismiss="modal" title="Đóng ChatBox"><i class="bi bi-x-lg"></i>
                </button>
                </div>
            </div>
        </div>
    </div>
</div>
-->
<!-- end Chatbot --> 
		
<!-- restart vbot -->	


        <li class="nav-item dropdown pe-3">

          <a class="nav-item nav-icon" href="#" data-bs-toggle="dropdown">
           <i class="bi bi-power text-danger"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow POWER_CONTROL">

            <li>
              <a class="dropdown-item d-flex align-items-center" onclick="command_php('auto_start', null,'Khởi chạy chương trình Vietbot')">
                <i class="bi bi-align-start text-success" title="Chạy chương trình Vietbot"></i>
                 <span class="text-primary">Start Vietbot</span>
              </a>
            </li> 
			
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" onclick="command_php('auto_stop', null, 'Bạn có chắc chắn muốn dừng chương trình Vietbot')">
                <i class="bi bi-stop-btn text-danger" title="Dừng chương trình Vietbot"></i>
                 <span class="text-primary">Stop Vietbot</span>
              </a>
            </li> 
			
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" onclick="command_php('auto_restart', null, 'Bạn có chắc chắn muốn khởi động lại chương trình Vietbot')">
                <i class="bi bi-arrow-repeat text-warning" title="Khởi động lại chương trình Vietbot"></i>
                <span class="text-primary">Restart Vietbot</span>
              </a>
            </li>
			

            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" onclick="command_php0('reboot_os', null, 'Bạn có chắc chắn muốn khởi động lại toàn bộ hệ thống')">
                <i class="bi bi-bootstrap-reboot text-primary" title="Khởi động lại toàn bộ hệ thống"></i>
                 <span class="text-danger">Reboot OS</span>
              </a>
            </li>

          </ul>
        </li>

<!-- end restart vbot -->		
        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="<?php echo $Avata_File; ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $Config['smart_user']['name']; ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $Config['smart_user']['name']; ?></h6>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" onclick="loading('show')" href="Users_Profile.php">
                <i class="bi bi-person"></i>
                <span>Cá nhân</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>


            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="FAQ.php">
                <i class="bi bi-question-circle"></i>
                <span>Hướng Dẫn</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

				<?php
				if ($Config['web_interface']['login_authentication']['active']){
					
					echo '            <li>
              <a class="dropdown-item d-flex align-items-center" onclick="loading(\'show\')" href="Login.php?logout">
                <font color=red><i class="bi bi-box-arrow-right"></i>
                <span>Đăng xuất</span></font>
              </a>
            </li>';
				}
				?>



          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header>