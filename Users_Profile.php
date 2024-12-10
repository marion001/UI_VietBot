<?php
#Code By: Vũ Tuyển
#Designed by: BootstrapMade
#Facebook: https://www.facebook.com/TWFyaW9uMDAx
include 'Configuration.php';
?>
<?php
if ($Config['web_interface']['login_authentication']['active']){
session_start();
// Kiểm tra xem người dùng đã đăng nhập chưa và thời gian đăng nhập
if (!isset($_SESSION['user_login']) ||
    (isset($_SESSION['user_login']['login_time']) && (time() - $_SESSION['user_login']['login_time'] > 43200))) {
    
    // Nếu chưa đăng nhập hoặc đã quá 12 tiếng, hủy session và chuyển hướng đến trang đăng nhập
    session_unset();
    session_destroy();
    header('Location: Login.php');
    exit;
}
// Cập nhật lại thời gian đăng nhập để kéo dài thời gian session
//$_SESSION['user_login']['login_time'] = time();
}
?>
<?php

if (isset($_POST['save_change_info_name'])) {
#CẬP NHẬT Thông tin người dùng
$Config['smart_user']['name'] = $_POST['full_name'];
$Config['smart_user']['address']['wards'] = $_POST['wards_name'];
$Config['smart_user']['address']['district'] = $_POST['district_name'];
$Config['smart_user']['address']['province'] = $_POST['province_name'];


$Config['web_interface']['email'] = $_POST['email_name'];
$Config['login_authentication']['password'] = $_POST['webui_password'];

// Lưu cấu hình $Config vào file JSON
file_put_contents($Config_filePath, json_encode($Config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

if (isset($_POST['save_change_user_login'])) {
$Config['web_interface']['login_authentication']['active'] = isset($_POST['user_login_active']) ? true : false;
// Lưu cấu hình $Config vào file JSON
file_put_contents($Config_filePath, json_encode($Config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}
?>

<!DOCTYPE html>
<html lang="vi">
<?php
include 'html_head.php';
?>

<body>
<!-- ======= Header ======= -->
<?php
include 'html_header_bar.php'; 
?>
<!-- End Header -->

  <!-- ======= Sidebar ======= -->
<?php
include 'html_sidebar.php';
?>
<!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Thông tin cá nhân</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item" onclick="loading('show')"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Người dùng</li>
          <li class="breadcrumb-item active">Hồ sơ</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->



    <section class="section profile">
      <div class="row">
        <div class="col-xl-4">

          <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

              <img src="<?php echo $Avata_File; ?>" alt="Profile" class="rounded-circle">
              <h2><?php echo $Config['smart_user']['name']; ?></h2>
            </div>
          </div>

        </div>

<div class="col-xl-8">

          <div class="card">
            <div class="card-body pt-3">
              <!-- Bordered Tabs -->
              <ul class="nav nav-tabs nav-tabs-bordered">

                <li class="nav-item">
                  <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Tổng quan</button>
                </li>

                <li class="nav-item">
                  <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Chỉnh sửa hồ sơ</button>
                </li>

                <li class="nav-item">
                  <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings">Cài đặt</button>
                </li>

                <li class="nav-item">
                  <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Đổi mật khẩu</button>
                </li>

               <li class="nav-item">
                  <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-forgot-password">Quên mật khẩu</button>
                </li>

              </ul>
			  
              <div class="tab-content pt-2">



                <div class="tab-pane fade profile-overview active show" id="profile-overview" role="tabpanel">
                  <h5 class="card-title">Chi tiết hồ sơ</h5>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label ">Tên</div>
                    <div class="col-lg-9 col-md-8"><?php echo $Config['smart_user']['name']; ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Địa Chỉ</div>
                    <div class="col-lg-9 col-md-8"><?php echo $Config['smart_user']['address']['wards'].", ".$Config['smart_user']['address']['district'].", ".$Config['smart_user']['address']['province']; ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Email:</div>
                    <div class="col-lg-9 col-md-8"><?php echo $Config['web_interface']['email']; ?></div>
                  </div>

                </div>




                <div class="tab-pane fade profile-edit pt-3" id="profile-edit" role="tabpanel">


                  <!-- Profile Edit Form -->
               <form class="row g-3 needs-validation" enctype="multipart/form-data" novalidate method="POST" action="">
                    <div class="row mb-3">
                      <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Ảnh hồ sơ cá nhân</label>
                      <div class="col-md-8 col-lg-9">
                        <img src="<?php echo $Avata_File; ?>" alt="Profile">
                        <div class="pt-2">
						
						
						<div class="input-group">
            <input class="form-control border-success" type="file" id="avataa_fileToUpload" accept="<?php echo $accept_types; ?>"> <!-- Thêm thuộc tính multiple -->
			<button class="btn btn-success border-success" type="button" onclick="fileToUpload_avata()">Tải Lên</button>
			<button type="button" name="remove_avata" id="remove_avata" class="btn btn-danger border-success" onclick="deleteFile('../../<?php echo $Avata_File; ?>')" title="Xóa Avata"><i class="bi bi-trash"></i></button>
        </div>
                          
                        </div>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="full_name" class="col-md-4 col-lg-3 col-form-label">Tên</label>
                      <div class="col-md-8 col-lg-9">
                        <input required name="full_name" type="text" class="form-control border-success" id="full_name" value="<?php echo $Config['smart_user']['name']; ?>">
 <div class="invalid-feedback">Vui Lòng Nhập Tên!</div>                     
					 </div>
                    </div>

                    <div class="row mb-3">
                      <label for="address_name" class="col-md-4 col-lg-3 col-form-label">Địa Chỉ</label>
                      <div class="col-md-8 col-lg-9">
					  
					  <div class="input-group mb-3 border-success">
  <div class="input-group-prepend">
    <span class="input-group-text border-success" id="wards_name">Xã/Phường: </span>
  </div>
  <input type="text" name="wards_name" id="wards_name" class="form-control border-success" placeholder="<?php echo $Config['smart_user']['address']['wards']; ?>" value="<?php echo $Config['smart_user']['address']['wards']; ?>">
</div>
					  
<div class="input-group mb-3 border-success">
  <div class="input-group-prepend">
    <span class="input-group-text border-success" id="district_name">Quận/Huyện: </span>
  </div>
  <input type="text" name="district_name" id="district_name" class="form-control border-success" placeholder="<?php echo $Config['smart_user']['address']['district']; ?>" value="<?php echo $Config['smart_user']['address']['district']; ?>">
</div>

<div class="input-group mb-3 border-success">
  <div class="input-group-prepend">
    <span class="input-group-text border-success" id="province_name">Tỉnh/Thành Phố: </span>
  </div>
  <input type="text" name="province_name" id="province_name" class="form-control border-success" placeholder="<?php echo $Config['smart_user']['address']['province']; ?>" value="<?php echo $Config['smart_user']['address']['province']; ?>">
</div>
					 </div>
                    </div>


                    <div class="row mb-3">
                      <label for="email_name" class="col-md-4 col-lg-3 col-form-label">Email</label>
                      <div class="col-md-8 col-lg-9">
                        <input required name="email_name" type="text" class="form-control border-success" id="email_name" placeholder="<?php echo $Config['web_interface']['email']; ?>" value="<?php echo $Config['web_interface']['email']; ?>">
                     <div class="invalid-feedback">Vui Lòng Nhập Email (Dùng để tìm lại mật khẩu và 1 số chức năng khác) !</div>    
					</div>
                    </div>
                    <div class="row mb-3">
                      <label for="webui_password" class="col-md-4 col-lg-3 col-form-label">Mật khẩu Web UI</label>
                      <div class="col-md-8 col-lg-9">
                        <input required name="webui_password" type="text" class="form-control border-success" id="webui_password" placeholder="<?php echo $Config['web_interface']['login_authentication']['password']; ?>" value="<?php echo $Config['web_interface']['login_authentication']['password']; ?>">
                     <div class="invalid-feedback">Vui Lòng Nhập Mật Khảu Đăng Nhập Web UI (Dùng để đăng nhập khi bạn bật đăng nhập trên web ui) !</div>    
					</div>
                    </div>
                    <div class="text-center">
                      <button type="submit" name="save_change_info_name" class="btn btn-primary rounded-pill">Lưu Hồ Sơ</button>
                    </div>
                 </form> <!-- End Profile Edit Form -->




                </div>

                <div class="tab-pane fade pt-3" id="profile-settings" role="tabpanel">
 <form class="row g-3 needs-validation" enctype="multipart/form-data" novalidate method="POST" action="">
                  <!-- Settings Form -->
            <div class="row mb-3">
                  <label class="col-sm-3 col-form-label">Đăng nhập Web UI</label>
                  <div class="col-sm-9">
                    <div class="form-switch">
                      <input class="form-check-input" type="checkbox" name="user_login_active" id="user_login_active" <?php echo $Config['web_interface']['login_authentication']['active'] ? 'checked' : ''; ?>>
                      <label class="form-check-label" for="user_login_active">Bật hoặc đăng nhập</label>
                    </div>
                  </div>
                </div>

<hr/>
                    <div class="text-center">
                      <button type="submit" name="save_change_user_login" class="btn btn-primary rounded-pill">Lưu Cài Đặt</button>
                    </div><!-- End settings Form -->
</form>
                </div>


                <div class="tab-pane fade pt-3" id="profile-change-password" role="tabpanel">
                  <!-- Change Password Form -->

                    <div class="row mb-3">
                      <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Mật Khẩu Cũ</label>
                      <div class="col-md-8 col-lg-9">
                        <input required name="currentPassword" type="password" placeholder="<?php echo $Config['web_interface']['login_authentication']['password']; ?>" value="<?php echo $Config['web_interface']['login_authentication']['password']; ?>" class="form-control border-success" id="currentPassword">
						<div class="valid-feedback">Cần nhập mật khẩu cũ!</div>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">Mật Khẩu Mới</label>
                      <div class="col-md-8 col-lg-9">
                        <input required name="newPassword" type="password" class="form-control border-success" id="newPassword">
						<div class="valid-feedback">Cần nhập mật khẩu mới!</div>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Nhập Lại Mật Khẩu Mới</label>
                      <div class="col-md-8 col-lg-9">
                        <input required name="renewPassword" type="password" class="form-control border-success" id="renewPassword">
						<div class="valid-feedback">Cần nhập lại mật khẩu mới!</div>
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="button" onclick="changePassword()" class="btn btn-primary rounded-pill">Đổi Mật Khẩu</button>
                    </div>

                </div>


                <div class="tab-pane fade pt-3" id="profile-forgot-password" role="tabpanel">
                  <!-- Change Password Form -->
			
                    <div class="row mb-3">
                      <label for="forgotPassword_email" class="col-md-4 col-lg-3 col-form-label">Nhập Email</label>
                      <div class="col-md-8 col-lg-9">
                        <input required name="forgotPassword_email" type="text" class="form-control border-success" id="forgotPassword_email" value="<?php echo $Config['web_interface']['email']; ?>">
						<div class="valid-feedback">Cần nhập địa chỉ email để lấy lại mật khẩu</div>
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="button" onclick="forgotPassword()" class="btn btn-primary rounded-pill">Lấy Mật Khẩu</button>
                    </div>

                </div>

              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
<?php
include 'html_footer.php';
?>
<!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Nghe thử file âm thanh -->
<audio id="audioPlayer" style="display: none;" controls></audio>

  <!-- Template Main JS File -->
<?php
include 'html_js.php';
?>

    <script>
        function fileToUpload_avata() {
            var fileInput = document.getElementById('avataa_fileToUpload');
            if (fileInput.files.length === 0) {
                show_message('Chưa chọn tệp nào.');
                return;
            }

            var formData = new FormData();
            formData.append('fileToUpload_avata', fileInput.files[0]);
            formData.append('upload_avata', 'true');

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'includes/php_ajax/Upload_file_path.php?upload_avata', true);

            xhr.onload = function () {
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        console.log(response);
                        // Xử lý phản hồi từ máy chủ
                        if (response.success) {
                            show_message(response.message+"<br/>Hãy tải lại trang để áp dụng");
                        } else {
                            show_message(response.message);
                        }
                    } catch (e) {
                        console.error('Lỗi khi phân tích phản hồi JSON:', e);
                        show_message('Lỗi khi xử lý phản hồi từ máy chủ.');
                    }
                } else {
                    console.error('Yêu cầu thất bại. Trạng thái trả về: ' + xhr.status);
                    show_message('Yêu cầu bị lỗi với mã trạng thái: ' + xhr.status);
                }
            };

            xhr.onerror = function () {
                console.error('Yêu cầu thất bại.');
                show_message('Yêu cầu bị lỗi.');
            };

            xhr.send(formData);
        }

    </script>
	
    <script>
		//Thay đổi mật khẩu web UI
        function changePassword() {
            var currentPassword = document.getElementById("currentPassword").value;
            var newPassword = document.getElementById("newPassword").value;
            var renewPassword = document.getElementById("renewPassword").value;

            var xhr = new XMLHttpRequest();
            xhr.open("GET", "Login.php?change_password&currentPassword=" 
                + encodeURIComponent(currentPassword) 
                + "&newpassword=" + encodeURIComponent(newPassword) 
                + "&renewpassword=" + encodeURIComponent(renewPassword), true);

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);

                    if (response.success) {
                        //console.log("Success: " + response.message);
                       show_message(response.message);
                    } else {
                        //console.log("Error: " + response.message);
                        show_message("Lỗi: " + response.message);
                    }
                }
            };

            xhr.send();
        }
		
		
	
    </script>
</body>

</html>