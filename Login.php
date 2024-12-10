<?php
#Code By: Vũ Tuyển
#Designed by: BootstrapMade
#Facebook: https://www.facebook.com/TWFyaW9uMDAx
include 'Configuration.php';

#Quên Mật Khẩu
if (isset($_GET['forgot_password'])) {
    $my_email = $_GET['mail'];
    if (!empty($my_email)) {
        if ($my_email === $Config['web_interface']['email']) {
            // Hiển thị mật khẩu hoặc gửi liên kết đặt lại mật khẩu
            $response = [
                "success" => true,
                "message" => $Config['web_interface']['login_authentication']['password']
            ];
            // Ví dụ: Gửi email chứa liên kết đặt lại mật khẩu
            // sendResetLink($my_email);
        } else {
            $response = [
                "success" => false,
                "message" => "Email không khớp!"
            ];
        }
    } else {
        $response = [
            "success" => false,
            "message" => "Vui lòng nhập email!"
        ];
    }

    // Trả về dữ liệu dạng JSON
    header('Content-Type: application/json');
    echo json_encode($response);
	exit();
}

//Đổi mật khẩu
if (isset($_GET['change_password'])) {
	// Trả về dữ liệu dạng JSON
	header('Content-Type: application/json');
    $currentPassword = $_GET['currentPassword'];
    $newPassword = $_GET['newpassword'];
    $renewPassword = $_GET['renewpassword'];

    
    // Kiểm tra xem tất cả các tham số có giá trị không
    if (!empty($currentPassword) && !empty($newPassword) && !empty($renewPassword)) {
        // Kiểm tra xem mật khẩu cũ có khớp với mật khẩu hiện tại không
        if ($currentPassword === $Config['web_interface']['login_authentication']['password']) {
            // Kiểm tra độ dài mật khẩu mới
            if (strlen($newPassword) >= 6 && strlen($newPassword) <= 32) {
                // Kiểm tra xem mật khẩu mới và nhập lại mật khẩu mới có khớp nhau không
                if ($newPassword === $renewPassword) {
                    // Tiến hành cập nhật mật khẩu mới tại đây
                    // Ví dụ, lưu mật khẩu mới vào cơ sở dữ liệu
                    // ...
					$Config['web_interface']['login_authentication']['password'] = $renewPassword;

					file_put_contents($Config_filePath, json_encode($Config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

                    $response = [
                        "success" => true,
                        "message" => "Mật khẩu đã được thay đổi thành công!"
                    ];
                } else {
                    $response = [
                        "success" => false,
                        "message" => "Mật khẩu mới và xác nhận mật khẩu không khớp!"
                    ];
                }
            } else {
                $response = [
                    "success" => false,
                    "message" => "Mật khẩu mới phải từ 6 đến 32 ký tự!"
                ];
            }
        } else {
            $response = [
                "success" => false,
                "message" => "Mật khẩu cũ không đúng!"
            ];
        }
    } else {
        $response = [
            "success" => false,
            "message" => "Vui lòng nhập đầy đủ thông tin!"
        ];
    }
echo json_encode($response);
exit();
}


session_start();
//Đăng xuất
if (isset($_GET['logout'])) {
// Xóa session user_login mà không cần kiểm tra mật khẩu
unset($_SESSION['user_login']);
header('Location: Login.php');
exit;
}


if ($Config['web_interface']['login_authentication']['active']){
// Kiểm tra xem người dùng đã đăng nhập chưa
if (isset($_SESSION['user_login'])) {
    // Nếu đã đăng nhập, chuyển hướng đến trang index
    header('Location: index.php');
    exit;
}
}else{
    header('Location: index.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password_user = $_POST['yourPassword'];

    // Kiểm tra mật khẩu trực tiếp
	// Thay 'password' bằng mật khẩu thực tế của bạn
    if ($password_user === $Config['web_interface']['login_authentication']['password']) {
        $_SESSION['user_login'] = [
			// Lưu mật khẩu vào session để kiểm tra khi đăng xuất
            //'password' => $password_user, 
            'login_time' => time()
        ];
        
        header('Location: index.php');
        exit;
    } else {
        $error = "Sai mật khẩu!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<?php
include 'html_head.php';
?>

<body>
  <main>
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
              <div class="d-flex justify-content-center py-4">
                <a href="index.php" class="logo d-flex align-items-center w-auto">
                  <img src="assets/img/logo.png" alt="">
                  <span class="d-none d-lg-block"><?php echo $Title_HTML; ?></span>
                </a>
              </div><!-- End Logo -->



<div class="card">
            <div class="card-body">

              <!-- Default Tabs -->
			  
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Đăng Nhập</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="quenmatkhau-tab" data-bs-toggle="tab" data-bs-target="#quenmatkhau" type="button" role="tab" aria-controls="quenmatkhau" aria-selected="false" tabindex="-1">Quên Mật Khẩu</button>
                </li>
              </ul>
              <div class="tab-content pt-2">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
  
                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Đăng Nhập Hệ Thống</h5>
                  </div>
				    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
                  <form class="row g-3 needs-validation" novalidate method="POST" action="">

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Mật khẩu</label>
                      <input type="password" name="yourPassword" class="form-control border-success" id="yourPassword" required>
                      <div class="invalid-feedback">Vui lòng nhập mật khẩu của bạn!</div>
                    </div>

                  
                    <div class="d-grid gap-2 mt-3">
                      <button onclick="loading('show')" class="btn btn-primary rounded-pill" type="submit">Đăng nhập</button>
                    </div>
			
                  </form>

<?php
// Tách email thành hai phần: trước và sau dấu @
list($localPart, $domain) = explode('@', $Config['web_interface']['email']);
// Tìm độ dài chuỗi trước @
$localLength = strlen($localPart);
// Số ký tự cần ẩn
$hiddenLength = 6;
// Đảm bảo không ẩn nhiều hơn độ dài chuỗi
if ($hiddenLength > $localLength) {
    $hiddenLength = $localLength;
}
// Phần hiển thị và ẩn của email
$hiddenPart = str_repeat('*', $hiddenLength);
$visiblePart = substr($localPart, $hiddenLength);
// Kết hợp các phần
$maskedEmail = $hiddenPart . $visiblePart . '@' . $domain;
?>

				</div>
                <div class="tab-pane fade" id="quenmatkhau" role="tabpanel" aria-labelledby="quenmatkhau-tab">
				             <div class="col-12">
                    <h5 class="card-title text-center pb-0 fs-4">Quên Mật Khẩu</h5>
                  </div>
				       <div class="col-12">
                      <label for="forgotPassword_email" class="form-label">Nhập Email</label>
                      <input type="text" placeholder="<?php echo $maskedEmail; ?>" name="forgotPassword_email" class="form-control border-success" id="forgotPassword_email" required>
                    
                    </div>
					<div class="d-grid gap-2 mt-3">
                      <button type="button" onclick="forgotPassword()" class="btn btn-primary rounded-pill">Lấy Mật Khẩu</button>
                    </div>
			  </div>
         
              </div><!-- End Default Tabs -->

            </div>
          </div>
              <div class="credits">
                <!-- All the links in the footer should remain intact. -->
                <!-- You can delete the links only if you purchased the pro version. -->
                <!-- Licensing information: https://bootstrapmade.com/license/ -->
                <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
                Code by <a href="https://www.facebook.com/TWFyaW9uMDAx" target="_bank">(Vũ Tuyển)</a>, Designed by: <a href="https://bootstrapmade.com/" target="_bank">BootstrapMade</a>
   
              </div>

            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->


  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Template Main JS File -->
<?php
include 'html_js.php';
?>

</body>

</html>