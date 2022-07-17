<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

include 'config.php';

// 接收傳入變數
$usercode = isset($_POST['usercode']) ? $_POST['usercode'] : '';
$username = isset($_POST['username']) ? $_POST['username'] : '';
$address  = isset($_POST['address'])  ? $_POST['address']  : '';
$birthday = isset($_POST['birthday']) ? $_POST['birthday'] : '';
$height   = isset($_POST['height'])   ? $_POST['height']   : '';
$weight   = isset($_POST['weight'])   ? $_POST['weight']   : '';
$remark   = isset($_POST['remark'])   ? $_POST['remark']   : '';

// 連接資料庫
$pdo = db_open();

// SQL 語法
$sqlstr = "INSERT INTO person(usercode, username, address, birthday, height, weight, remark) VALUES (:usercode, :username, :address, :birthday, :height, :weight, :remark)";

$sth = $pdo->prepare($sqlstr);
$sth->bindParam(':usercode', $usercode, PDO::PARAM_STR);
$sth->bindParam(':username', $username, PDO::PARAM_STR);
$sth->bindParam(':address' , $address , PDO::PARAM_STR);
$sth->bindParam(':birthday', $birthday, PDO::PARAM_STR);
$sth->bindParam(':height'  , $height  , PDO::PARAM_INT);
$sth->bindParam(':weight'  , $weight  , PDO::PARAM_INT);
$sth->bindParam(':remark'  , $remark  , PDO::PARAM_STR);

// 執行 SQL
if($sth->execute())
{
   $new_uid = $pdo->lastInsertId();    // 傳回剛才新增記錄的 auto_increment 的欄位值
   $url_display = "display.php?uid=" . $new_uid;
   header("Location: " . $url_display);
}
else
{
   header("Location: error.php?type=add_save");
   echo print_r($pdo->errorInfo()); exit;  // 此列供開發時期偵錯用
}

db_close();
?>