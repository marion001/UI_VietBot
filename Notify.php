<?php
#Code By: Vũ Tuyển
#Designed by: BootstrapMade
#Facebook: https://www.facebook.com/TWFyaW9uMDAx
include 'Configuration.php';
$output_notify = ''; // Biến tạm để lưu nội dung
$i_count = 0; // Khai báo biến toàn cục để đếm

function checkPermissions($dir) {
    global $output_notify, $i_count, $excluded_items_chmod; // Khai báo biến toàn cục để lưu nội dung
    $items = scandir($dir); // Mở thư mục để duyệt qua các file và thư mục con
    foreach ($items as $item) {
        //if ($item == '.' || $item == '..') continue;
		if (in_array($item, $excluded_items_chmod)) continue;
        $path = $dir . '/' . $item;
        $permissions = substr(sprintf('%o', fileperms($path)), -3); // Lấy quyền hiện tại
        // Kiểm tra quyền có phải là 777 hay không
        if ($permissions != '777') {
			$i_count++;
            $output_notify .= '
			<li>
              <hr class="dropdown-divider">
            </li><li class="notification-item">
              <i class="bi bi-exclamation-circle text-warning"></i>
              <div>
                <h4>Cấp Quyền Chmod</h4>
                <p>Một số file, thư mục chưa được cấp quyền '.$path.'</p>
                <p class="text-danger" onclick="command_php(\'chmod_vietbot\', true)">Cấp Quyền</p>
              </div>
            </li>';
        }
        // Nếu là thư mục, đệ quy để kiểm tra các thư mục con
        if (is_dir($path)) {
            checkPermissions($path);
        }
    }
}
// Gọi hàm kiểm tra quyền với thư mục WEbUI
#checkPermissions($directory_path.'/');
checkPermissions($VietBot_Offline_Path);
?>


<?php
#kiểm tra xem có bật tự động kiểm tra cập nhật không
if ($Config['web_interface']['backup_upgrade']['automatically_check_for_updates'] === true){


//Thông báo cập nhật chương trình VBot
$parsedUrl = parse_url($Github_Repo_Vietbot_Program);
$pathParts = explode('/', trim($parsedUrl['path'], '/'));
$git_username = $pathParts[0];
$git_repository = $pathParts[1];
// Thông báo cập nhật phiên bản Vbot chương trình
$localFile = $VietBot_Offline_Path.'src/version.json';
$remoteFileUrl = "https://raw.githubusercontent.com/$git_username/$git_repository/refs/heads/beta/src/version.json";
// Đọc nội dung file local
if (file_exists($localFile)) {
    $localContent = file_get_contents($localFile);
    $localData = json_decode($localContent, true);
    // Đọc nội dung file trên GitHub
    $remoteContent = file_get_contents($remoteFileUrl);
    if ($remoteContent !== false) {  // Sửa điều kiện ở đây để kiểm tra thành công
        $remoteData = json_decode($remoteContent, true);
        // Lấy giá trị "vietbot_version" từ cả hai file và so sánh
        if (isset($localData['vietbot_version']['latest']) && isset($remoteData['vietbot_version']['latest'])) {
            if ($localData['vietbot_version']['latest'] !== $remoteData['vietbot_version']['latest']) {
                $i_count++;
            $output_notify .= '
			<li>
              <hr class="dropdown-divider">
            </li><li class="notification-item">
              <a href="_Program.php"><font color=green><i class="bi bi-box-arrow-in-up"></i></font></a>
              <div>
                <h4><font color=green>Cập Nhật Chương Trình</font></h4>
			<p class="text-primary">Có phiên bản mới: '.$remoteData['vietbot_version']['latest'].'</p>
			<a href="_Program.php"><p class="text-danger">Kiểm Tra</p></a>
              </div>
            </li>';
            }
        }
    } 
}


//Thông báo cập nhật Giao diện VBot
$parsedUrl_ui = parse_url($Github_Repo_Vietbot_Interface);
$pathParts_ui = explode('/', trim($parsedUrl_ui['path'], '/'));
$git_username_ui = $pathParts_ui[0];
$git_repository_ui = $pathParts_ui[1];
// Thông báo cập nhật phiên bản Vietbot chương trình
$localFile_ui = $directory_path.'/version.json';
$remoteFileUrl_ui = "https://raw.githubusercontent.com/$git_username_ui/$git_repository_ui/refs/heads/main/version.json";
// Đọc nội dung file local
if (file_exists($localFile_ui)) {
    $localContent_ui = file_get_contents($localFile_ui);
    $localData_ui = json_decode($localContent_ui, true);
    // Đọc nội dung file trên GitHub
    $remoteContent_ui = file_get_contents($remoteFileUrl_ui);
    if ($remoteContent_ui !== false) {  // Sửa điều kiện ở đây để kiểm tra thành công
        $remoteData_ui = json_decode($remoteContent_ui, true);
        // Lấy giá trị "ui_version" từ cả hai file và so sánh
        if (isset($localData_ui['ui_version']['latest']) && isset($remoteData_ui['ui_version']['latest'])) {
            if ($localData_ui['ui_version']['latest'] !== $remoteData_ui['ui_version']['latest']) {
                $i_count++;
            $output_notify .= '
			<li>
              <hr class="dropdown-divider">
            </li><li class="notification-item">
              <a href="_Dashboard.php"><font color=green><i class="bi bi-box-arrow-in-up"></i></font></a>
              <div>
                <h4><font color=green>Cập Nhật Giao Diện</font></h4>
			<p class="text-primary">Có phiên bản mới: '.$remoteData_ui['ui_version']['latest'].'</p>
			<a href="_Dashboard.php"><p class="text-danger">Kiểm Tra</p></a>
              </div>
            </li>';
            }
        }
    } 
}

}
?>

<a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown" title="Thông báo">
    <i class="bi bi-bell text-success"></i>
    <span class="badge bg-primary badge-number"><?php if ($i_count != 0) { echo $i_count; } ?></span>
</a>
<!-- End Notification Icon -->

<ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications" style="max-height: 400px; overflow-y: auto; width: auto;">
    <li class="dropdown-header">

        <font color=red>Bạn có <b><?php echo $i_count; ?></b> thông báo mới.</font>
       <!-- <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">Xem tất cả</span></a> -->
    </li>
    <?php echo $output_notify; ?>
</ul>
<!-- End Notification Dropdown Items -->