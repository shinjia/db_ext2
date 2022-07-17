<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

include "config.php";

// 連接資料庫
try {
    $pdo = new PDO('mysql:host='.DB_SERVERIP, DB_USERNAME, DB_PASSWORD);
    if(defined('SET_CHARACTER')) $pdo->query(SET_CHARACTER);
    // 指定 PDO錯誤模式和錯誤處理
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { db_error('ERROR_DBSOURCE', $e->getMessage()); } 

// SQL 語法
$sqlstr = "CREATE DATABASE " . DB_DATABASE;
$sqlstr .= " DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ";

// 執行 SQL
$sth = $pdo->prepare($sqlstr);

$msg = '資料庫『' . DB_DATABASE . '』建立';
try {
    $sth->execute();
    $msg .= '完成';
}
catch(PDOException $e) {
    // db_error(ERROR_QUERY, $e->getMessage());
    $msg .= '失敗';
}


$html = <<< HEREDOC
<h2>資料庫建立</h2>
<p class="center">{$msg}</p>
HEREDOC;

include 'pagemake.php';
pagemake($html, '');
?>