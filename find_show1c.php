<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

include 'config.php';
include 'utility.php';

$key1 = isset($_POST['key1']) ? $_POST['key1'] : '';
$key2 = isset($_POST['key2']) ? $_POST['key2'] : '';

$key1 = (empty(trim($key1))) ? 0 : $key1;
$key2 = (empty(trim($key2))) ? 0 : $key2;

// 網頁內容預設
$ihc_content = '';
$ihc_error = '';

// 連接資料庫
$pdo = db_open();

// 寫出 SQL 語法
$sqlstr = "SELECT *, (weight/((height/100)*(height/100))) as bmi ";
$sqlstr .= " FROM person ";
$sqlstr .= " WHERE (weight/((height/100)*(height/100))) BETWEEN ? AND ? ";
$sqlstr .= " ORDER BY bmi ";

$sth = $pdo->prepare($sqlstr);
$sth->bindValue(1, $key1, PDO::PARAM_STR);
$sth->bindValue(2, $key2, PDO::PARAM_STR);

// 執行 SQL
try { 
    $sth->execute();

    $total_rec = $sth->rowCount();
    
    $cnt = 0;
    $data = '';
    while($row = $sth->fetch(PDO::FETCH_ASSOC))
    {
        $uid = $row['uid'];
        $usercode = html_encode($row['usercode']);
        $username = html_encode($row['username']);
        $address  = html_encode($row['address']);
        $birthday = html_encode($row['birthday']);
        $height   = html_encode($row['height']);
        $weight   = html_encode($row['weight']);
        $remark   = html_encode($row['remark']);

        $cnt++;

        $data .= <<< HEREDOC
        <tr>
            <th align="center">{$cnt}</th>
            <td>{$uid}</td>
            <td>{$usercode}</td>
            <td>{$username}</td>
            <td>{$address}</td>
            <td>{$birthday}</td>
            <td>{$height}</td>
            <td>{$weight}</td>
            <td>{$remark}</td>
        </tr>
HEREDOC;
    }

    $str_find = '搜尋BMI值介於『' . $key1 . '』和『' . $key2 . '』之間的記錄';

    //網頁顯示
    $ihc_content = <<< HEREDOC
    <h2>共有 {$total_rec} 筆記錄</h2>
    <p class="center">{$str_find}</p>
    <table border="1" class="table">   
        <tr>
            <th>順序</th>
            <th>代碼</th>
            <th>姓名</th>
            <th>地址</th>
            <th>生日</th>
            <th>身高</th>
            <th>體重</th>
            <th>備註</th>
            <th>BMI值</th>
        </tr>
    {$data}
    </table>
HEREDOC;

    // 找不到資料時
    if($total_rec==0) { $ihc_content = '<p class="center">無資料</p>';}
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