<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

include 'config.php';
include 'utility.php';

// 網頁內容預設
$ihc_content = '';
$ihc_error = '';

$total_rec = 0;

// 連接資料庫
$pdo = db_open();

// 寫出 SQL 語法
$sqlstr = "DELETE FROM person";

$sth = $pdo->prepare($sqlstr);

// 執行SQL
try {
   $sth->execute();

   $total_rec = $sth->rowCount();

   $msg = '所有記錄已全部刪除，共 ' . $total_rec . ' 筆記錄';
}
catch(PDOException $e) {
    // db_error(ERROR_QUERY, $e->getMessage());
    $ihc_error = error_message('ERROR_QUERY', $e->getMessage());
    $msg = '資料無法刪除';
}

$ihc_content = '<p class="center">' . $msg . '</p>';


$html = <<< HEREDOC
{$ihc_content}
{$ihc_error}
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>