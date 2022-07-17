<?php
/* db_ext v2.0  @Shinjia  #2022/07/17 */

include 'config.php';
include 'utility.php';

$a_uid = isset($_POST['a_uid']) ? $_POST['a_uid'] : '';

// 頁碼參數
$page = isset($_GET['page']) ? $_GET['page'] : 1;   // 目前的頁碼
$nump = isset($_GET['nump']) ? $_GET['nump'] : 10;   // 每頁的筆數

// 網頁內容預設
$ihc_content = '';
$ihc_error = '';

if(!empty($a_uid))
{
   $str_list = join($a_uid, ",");
}
else
{
   $str_list = 'null';
}


// 連接資料庫
$pdo = db_open();

// SQL 語法
$sqlstr = "DELETE FROM person WHERE uid IN(" . $str_list . ") ";

$sth = $pdo->prepare($sqlstr);

// 執行 SQL
try { 
   if($sth->execute()) {
      $msg = '勾選之資料已刪除!!!!!!!!';
   }
   else {
      $msg = '有問題，資料無法刪除。';
   }

   $ihc_content = '<p class="center">' . $msg . '</p>';
}
catch(PDOException $e) {
   // db_error(ERROR_QUERY, $e->getMessage());
   $ihc_error = error_message('ERROR_QUERY', $e->getMessage());

}

$html = <<< HEREDOC
<h2>資料整批刪除作業</h2>
<p class="center"><a href="delete_batch_list.php?page={$page}">再回到資料列表畫面</a></p>
{$ihc_content}
{$ihc_error}
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>