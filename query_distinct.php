<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

include 'config.php';
include 'utility.php';

// 網頁內容預設
$ihc_content = '';
$ihc_error = '';

// 連接資料庫
$pdo = db_open();

// SQL 語法
$sqlstr = "SELECT distinct address FROM person ";

// 執行 SQL
try {
    $sth = $pdo->query($sqlstr);
    
    $total_rec = $sth->rowCount();
    $cnt = 0;
    $data = '';
    while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        $address = $row['address'];

        $cnt++;

        $data .= <<< HEREDOC
        <tr>
            <th>{$cnt}</th>
            <td>{$address}</td>
        </tr>
HEREDOC;
    }

    //網頁顯示
    $ihc_content = <<< HEREDOC
    <p class="center"><a href="javascript:show_sql();">查看SQL語法</a></p>
    <h3>共有 $total_rec 筆記錄</h2>
    <table border="1" class="table">   
        <tr>
            <th>順序</th>
            <th>地區清單</th>
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


//網頁顯示
$js_sql = nl2br($sqlstr);
$js_sql = trim(preg_replace('/\s\s+/', ' ', $js_sql));
$head = <<< HEREDOC
<script language="javascript">
function show_sql()
{
    $('#dialog').html('{$js_sql}');
    $("#dialog").dialog();
}
</script>
HEREDOC;

$html = <<< HEREDOC
<p align="center"><a href="javascript:show_sql();">查看SQL語法</a></p>
{$ihc_content}
{$ihc_error}
<div id="dialog" title="SQL String"></div>
HEREDOC;

include 'pagemake.php';
pagemake($html, $head);
?>