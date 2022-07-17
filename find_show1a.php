<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

include 'config.php';
include 'utility.php';

$key = isset($_POST['key']) ? $_POST['key'] : '';

// 網頁內容預設
$ihc_content = '';
$ihc_error = '';

// 連接資料庫
$pdo = db_open();

// SQL 語法
$sqlstr = "SELECT * FROM person WHERE usercode=?";

$sth = $pdo->prepare($sqlstr);
$sth->bindValue(1, $key, PDO::PARAM_INT);

// 執行 SQL
try { 
    $sth->execute();

    $data = '';
    if($row = $sth->fetch(PDO::FETCH_ASSOC))
    {
        $uid = $row['uid'];
        $usercode = html_encode($row['usercode']);
        $username = html_encode($row['username']);
        $address  = html_encode($row['address']);
        $birthday = html_encode($row['birthday']);
        $height   = html_encode($row['height']);
        $weight   = html_encode($row['weight']);
        $remark   = html_encode($row['remark']);

        $data = <<< HEREDOC
        <table border="1" class="table">
            <tr><th>代碼</th><td>{$usercode}</td></tr>
            <tr><th>姓名</th><td>{$username}</td></tr>
            <tr><th>地址</th><td>{$address}</td></tr>
            <tr><th>生日</th><td>{$birthday}</td></tr>
            <tr><th>身高</th><td>{$height}</td></tr>
            <tr><th>體重</th><td>{$weight}</td></tr>
            <tr><th>備註</th><td>{$remark}</td></tr>
        </table>
HEREDOC;
    }
    else
    {
        $data = '<p class="center">查不到相關記錄！</a>';
    }

    $ihc_content = $data;
}
catch(PDOException $e) {
    // db_error(ERROR_QUERY, $e->getMessage());
    $ihc_error = error_message('ERROR_QUERY', $e->getMessage());
}

db_close();

// 網頁顯示
$html = <<< HEREDOC
{$ihc_content}
{$ihc_error}
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>