<?php
/*
  使用者登入頁php
  created_by：Jeff
  created_at：2021/09/21
*/
include_once("../library/config.php");
include_once("../library/response.php");
include_once("../library/db.php");


// 解析 $action
if (empty($_POST["action"]))
{
  echo json_response(array(), "action_error", "查無目標");
  exit();
}
$action = $_POST["action"];

// 由 $action 來判斷需要處理的項目
switch ($action) {
  // 登入帳號
  case "loginAccount":
    // 先檢查必填項目是否不為空
    $empty_error_str = "";
    if (!isset($_POST["account"]) || empty($_POST["account"])) {
      $empty_error_str .= "帳號 ";
    }
    if (!isset($_POST["password"]) || empty($_POST["password"])) {
      $empty_error_str .= "密碼 ";
    }
    if (!empty($empty_error_str)) {
        echo json_response(array(), "input_error", $empty_error_str . "為必填項目,請填寫");
        exit();
    }
    $account = $_POST["account"];
    $password = $_POST["password"];
    // 查詢資料庫是否能找到該帳號
    try {
      $sql = "SELECT * FROM $cfg_db.users WHERE account=?";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(1, $account);
      $stmt->execute();
    }
    catch(PDOException $e) {
      // 資料查詢錯誤
      echo json_response(array(), "sql_error", "資料庫查詢錯誤");
      exit();
    }

    $password_confirm = false;
    // 比對密碼
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      if (trim($row["password"]) == $password) {
        $password_confirm = true;
      }
    }

    if ($password_confirm === true) {
      // 密碼正確
      // 設定 status = 1 的時候是未審核狀態
      if (trim($row["status"]) == 1) {
        echo json_response(array(), "login_status", "此帳號待審核開通");
        exit();
      }
      else {
        echo json_success_response(array());
      }
    }
    else {
      // 登入失敗
      echo json_response(array(), "login_failed", "帳號或密碼錯誤");
    }
  break;

  // 確認單位編號
  case "checkOrgNo":
    // 先檢查單位編號是否不為空
    if (!isset($_POST["org_no"]) || empty($_POST["org_no"])) {
      echo json_response(array(), "input_error", "請填寫單位編號");
      exit();
    }
    $org_no = $_POST["org_no"];
    // 查詢資料庫是否能找到該單位編號
    try {
      $sql = "SELECT * FROM $cfg_db.orgs WHERE org_no=?";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(1, $org_no);
      $stmt->execute();
    }
    catch(PDOException $e) {
      // 資料查詢錯誤
      echo json_response(array(), "sql_error", "資料庫查詢錯誤");
      exit();
    }

    // 若能查到該單位編號,代表一切順利
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      echo json_success_response(array());
    }
    // 若查不到該單位編號
    else {
      // 確認使用者有沒有輸入單位名稱,使用者沒有輸入的話會回傳錯誤訊息請使用者輸入單位名稱
      if (!isset($_POST["title"]) || empty($_POST["title"]))
      {
        echo json_response(array(), "input_error", "因查無輸入的單位編號,須新建單位,因此請多輸入單位名稱");
        exit();
      }
      $title = $_POST["title"];
      // 有輸入的情況將新建單位,
      try {
        $sql = "INSERT $cfg_db.orgs (title, org_no, created_at, updated_at) VALUES (?, ?, NOW(), NOW())";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $title);
        $stmt->bindValue(2, $org_no);
        $stmt->execute();
      }
      catch(PDOException $e) {
        // 資料庫新增錯誤
        echo json_response(array(), "sql_error", "資料庫新增錯誤");
        exit();
      }
      echo json_success_response(array());
    }
  break;

  // 建立帳號
  case "createAccount":
    // 先檢查必填項目是否不為空
    $empty_error_str = "";

    if (!isset($_POST["name"]) || empty($_POST["name"])) {
      $empty_error_str .= "名稱 ";
    }
    // 生日非必填,若使用者未輸入的情況塞入預設值
    if (!isset($_POST["birthday"]) || empty($_POST["birthday"])) {
      $birthday = '';
    }

    if (!isset($_POST["email"]) || empty($_POST["email"])) {
      $empty_error_str .= "email ";
    }

    if (!isset($_POST["account"]) || empty($_POST["account"])) {
      $empty_error_str .= "帳號 ";
    }

    if (!isset($_POST["password"]) || empty($_POST["password"])) {
      $empty_error_str .= "密碼 ";
    }

    if (!isset($_FILES["applyQualification"]) || empty($_FILES["applyQualification"])) {
      $empty_error_str .= "申請資格附檔上傳 ";
    }

    if (!empty($empty_error_str)) {
        echo json_response(array(), "input_error", $empty_error_str . "為必填項目,請填寫");
        exit();
    }

    $org_no = $_POST["org_no"];
    $name = $_POST["name"];
    $birthday = $_POST["birthday"];
    $email = $_POST["email"];
    $account = $_POST["account"];
    $password = $_POST["password"];

    // 檢查檔案是否上傳成功
    if ($_FILES['applyQualification']['error'] === UPLOAD_ERR_OK) {
      $file_path = '../upload/' . $_FILES['applyQualification']['name'];
      // 檢查檔案是否已經存在
      if (file_exists('../upload/' . $_FILES['applyQualification']['name'])) {
      }
      // 不存在的情況將檔案遷移
      else {
        $file = $_FILES['applyQualification']['tmp_name'];
        // 將檔案移至指定位置
        move_uploaded_file($file, $file_path);
      }
    } 
    else {
        echo json_response(array(), "upload_error", "檔案上傳失敗");
    }

    // 查詢資料庫該帳號是否有重複過
    try {
      $sql = "SELECT * FROM $cfg_db.users WHERE account=?";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(1, $account);
      $stmt->execute();
    }
    catch(PDOException $e) {
      // 資料庫錯誤
      echo json_response(array(), "sql_error", "資料庫錯誤");
      exit();
    }

    // 若能查到該帳號,代表之前已建立,請使用者使用另外的帳號建立
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_response(array(), "duplicate_error", "帳號重複,請輸入其他帳號");
        exit();
    }

    // 用org_no取得org_id
    // 查詢資料庫是否能找到該單位編號
    try {
      $sql = "SELECT id FROM $cfg_db.orgs WHERE org_no=?";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(1, $org_no);
      $stmt->execute();
    }
    catch(PDOException $e) {
      // 資料查詢錯誤
      echo json_response(array(), "sql_error", "資料庫查詢錯誤");
      exit();
    }

    // 若能查到該單位編號,則能取得org_id
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $org_id = trim($row["id"]);
    }
    // 若查不到該單位編號
    else {
      echo json_response(array(), "input_error", "無法尋找到對應的單位編號,請重整後再次嘗試");
      exit();
    }

    // 一切順利的情況下將建立帳號
    try {
      // 設定 status = 1 的時候是未審核狀態
      $sql = "INSERT $cfg_db.users (org_id, name, birthday, email, account, password, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, '1', NOW(), NOW())";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(1, $org_id);
      $stmt->bindValue(2, $name);
      $stmt->bindValue(3, $birthday);
      $stmt->bindValue(4, $email);
      $stmt->bindValue(5, $account);
      $stmt->bindValue(6, $password);
      $stmt->execute();
      $last_user_id = $db->lastInsertId();
    }
    catch(PDOException $e) {
      // 資料庫新增錯誤
      echo json_response(array(), "sql_error", "資料庫新增錯誤");
      exit();
    }

    // 填入申請資格相關資訊
    try {
      $sql = "INSERT $cfg_db.apply_file (user_id, file_path, created_at, updated_at) VALUES (?, ?, NOW(), NOW())";
      $stmt = $db->prepare($sql);
      $stmt->bindValue(1, $last_user_id);
      $stmt->bindValue(2, $file_path);
      $stmt->execute();
    }
    catch(PDOException $e) {
      // 資料庫新增錯誤
      echo json_response(array(), "sql_error", "資料庫新增錯誤");
      exit();
    }
    echo json_success_response(array());
  break;

  
  default:
    echo json_response(array(), "action_error", "查無目標:「" . $action . "」");
    exit();
}
?>
