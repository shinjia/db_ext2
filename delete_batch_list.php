<?php
/* db_ext v2.0  @Shinjia  #2022/07/17 */

include 'config.php';
include 'utility.php';

// 頁碼參數
$page = isset($_GET['page']) ? $_GET['page'] : 1;   // 目前的頁碼
$nump = isset($_GET['nump']) ? $_GET['nump'] : 10;   // 每頁的筆數

// 網頁內容預設
$ihc_content = '';
$ihc_error = '';

// 變數設定
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

// SQL 語法
$sqlstr = "SELECT * FROM person ";
$sqlstr .= " LIMIT " . (($page-1)*$nump) . "," . $nump;

// 執行SQL及處理結果
$data = '';
$sth = $pdo->prepare($sqlstr);

// 執行 SQL
try { 
    $sth->execute();

    $cnt = (($page-1)*$nump);  // 注意分頁的起始編號
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
            <td><input type="checkbox" name="a_uid[]" value="{$uid}"></td>
            <th>{$cnt}</th>
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

    // 分頁導覽列
    $ihc_navigator = pagination($total_page, $page, $nump);

	$ihc_content = <<< HEREDOC
	<h2 align="center">共有 {$total_rec} 筆記錄</h2>
	{$ihc_navigator}
	<p align="center">※※※ 注意：換頁後，目前已勾選之記錄將會取消勾選 ※※※</p>
	<form method="post" action="delete_batch_exec.php">
	<table border="1" align="center">   
        <tr>
            <th><input type="submit" value="刪除" onClick="return confirm('確定要刪除所有勾選的記錄嗎？');"></th>
            <th>順序</th>
            <th>uid</th>
            <th>代碼</th>
            <th>姓名</th>
            <th>地址</th>
            <th>生日</th>
            <th>身高</th>
            <th>體重</th>
            <th>備註</th>
        </tr>
	{$data}
	</table>
	<input type="hidden" name="page" value="{$page}">
	</form>
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
<h2>資料列表選擇刪除</h2>
{$ihc_content}
{$ihc_error}
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>