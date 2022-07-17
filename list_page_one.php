<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

include 'config.php';
include 'utility.php';

// 頁碼參數
$page = isset($_GET['page']) ? $_GET['page'] : 1;   // 目前的頁碼
$nump = isset($_GET['nump']) ? $_GET['nump'] : 10;   // 每頁的筆數

// 網頁內容預設
$ihc_content = '';
$ihc_error = '';

$nump = 1;  // 強制
$total_rec = 0;

// 連接資料庫
$pdo = db_open();

// SQL 語法：取得分頁所需之資訊 (總筆數、總頁數、擷取記錄之起始位置)
$sqlstr = "SELECT count(*) as total_rec FROM person ";
$sth = $pdo->prepare($sqlstr);
try {
    $sth->execute();
    if($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        $total_rec = $row["total_rec"];
    }
    $total_page = ceil($total_rec / $nump);  // 計算總頁數
}
catch(PDOException $e) {
    // db_error(ERROR_QUERY, $e->getMessage());
    $ihc_error = error_message(ERROR_QUERY, $e->getMessage());
}


// SQL 語法：分頁資訊
$sqlstr = "SELECT * FROM person ";
$sqlstr .= " LIMIT " . (($page-1)*$nump) . "," . $nump;

// 執行 SQL
try { 
    $sth = $pdo->query($sqlstr);

    $data = '';
    while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        $uid = $row['uid'];
        $usercode = html_encode($row['usercode']);
        $username = html_encode($row['username']);
        $address  = html_encode($row['address']);
        $birthday = html_encode($row['birthday']);
        $height   = html_encode($row['height']);
        $weight   = html_encode($row['weight']);
        $remark   = html_encode($row['remark']);
    
        $data .= <<< HEREDOC
   <tr>
   <td>
         
     <div style="width:500px; height:360px; background-color:#FFDDCC">
        <h3 align="center">可在此設計單筆記錄的顯示畫面</h3>
        <p>代碼：{$usercode}</p>
        <p>姓名：{$username}</p>
        <p>地址：{$address}</p>
        <p>生日：{$birthday}</p>
        <p>身高：{$height}</p>
        <p>體重：{$weight}</p>
        <p>備註：{$remark}</p>
     </div>
     
   </td>
  </tr>
HEREDOC;
    }

    // 分頁導覽列
    $ihc_navigator = pagination($total_page, $page, $nump);
    
    //網頁顯示
    $ihc_content = <<< HEREDOC
    <h3>共有 $total_rec 筆記錄</h2>
    {$ihc_navigator}
    <table border="1" class="table">
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


$html = <<< HEREDOC
{$ihc_content}
{$ihc_error}
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>