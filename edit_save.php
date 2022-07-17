<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

include 'config.php';

// 接收傳入變數
$uid      = (isset($_POST['uid']))      ? $_POST['uid']      : '';
$usercode = (isset($_POST['usercode'])) ? $_POST['usercode'] : '';
$username = (isset($_POST['username'])) ? $_POST['username'] : '';
$address  = (isset($_POST['address']))  ? $_POST['address']  : '';
$birthday = (isset($_POST['birthday'])) ? $_POST['birthday'] : '';
$height   = (isset($_POST['height']))   ? $_POST['height']   : 0;
$weight   = (isset($_POST['weight']))   ? $_POST['weight']   : 0;
$remark   = (isset($_POST['remark']))   ? $_POST['remark']   : '';

// 連接資料庫
$pdo = db_open();

// SQL 語法
$sqlstr = "UPDATE person SET usercode=:usercode, username=:username, address=:address, birthday=:birthday, height=:height, weight=:weight, remark=:remark WHERE uid=:uid ";

$sth = $pdo->prepare($sqlstr);
$sth->bindParam(':usercode', $usercode, PDO::PARAM_STR);
$sth->bindParam(':username', $username, PDO::PARAM_STR);
$sth->bindParam(':address' , $address , PDO::PARAM_STR);
$sth->bindParam(':birthday', $birthday, PDO::PARAM_STR);
$sth->bindParam(':height'  , $height  , PDO::PARAM_INT);
$sth->bindParam(':weight'  , $weight  , PDO::PARAM_INT);
$sth->bindParam(':remark'  , $remark  , PDO::PARAM_STR);
$sth->bindParam(':uid'     , $uid     , PDO::PARAM_INT);

// 執行SQL及處理結果
if($sth->execute())
{
   $url_display = 'display.php?uid=' . $uid;
   header('Location: ' . $url_display);
}
else
{
   echo print_r($pdo->errorInfo()) . '<br />' . $sqlstr; exit; // 此列供開發時期偵錯用
   header('Location: error.php');
}
db_close();
?>