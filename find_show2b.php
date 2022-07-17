<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

include 'config.php';
include 'utility.php';

$key_name = isset($_POST['key_name']) ? $_POST['key_name'] : '';
$key_addr = isset($_POST['key_addr']) ? $_POST['key_addr'] : '';
$key_yy = isset($_POST['key_yy']) ? $_POST['key_yy'] : '';
$key_mm = isset($_POST['key_mm']) ? $_POST['key_mm'] : '';
$key_dd = isset($_POST['key_dd']) ? $_POST['key_dd'] : '';
$key_h1 = isset($_POST['key_h1']) ? intval($_POST['key_h1']) : 0;
$key_h2 = isset($_POST['key_h2']) ? intval($_POST['key_h2']) : 0;
$key_w1 = isset($_POST['key_w1']) ? intval($_POST['key_w1']) : 0;
$key_w2 = isset($_POST['key_w2']) ? intval($_POST['key_w2']) : 0;

// 網頁內容預設
$ihc_content = '';
$ihc_error = '';

// 連接資料庫
$pdo = db_open();

// SQL 語法
$sql_where = "WHERE true ";
$sql_where .= (empty($key_name)) ? "" : " AND username LIKE '%" . $key_name . "%' ";  // 處理姓名欄位
$sql_where .= (empty($key_addr)) ? "" : " AND address LIKE '%" . $key_addr . "%' ";  // 處理地址欄位
$sql_where .= (empty($key_yy)) ? "" : " AND YEAR(birthday)=" . ($key_yy+0);  // 處理生日欄位的年
$sql_where .= (empty($key_mm)) ? "" : " AND MONTH(birthday)=". ($key_mm+0);  // 處理生日欄位的月
$sql_where .= (empty($key_dd)) ? "" : " AND DAY(birthday)="  . ($key_dd+0);  // 處理生日欄位的日
// 處理身高
if(empty($key_h1) && empty($key_h2)) {
   // Nothing to do
}
elseif(!empty($key_h1) && empty($key_h2)) {
    $sql_where .= " AND height > " . $key_h1;
}
elseif(empty($key_h1) && !empty($key_h2)) {
    $sql_where .= " AND height < " . $key_h2;
}
else {
    $sql_where .= " AND height BETWEEN " . min($key_h1,$key_h2) . " AND " . max($key_h1,$key_h2);
}
// 處理體重 (方法同身高)
if(empty($key_w1) && empty($key_w2)) {
   // Nothing to do
}
elseif(!empty($key_w1) && empty($key_w2)) {
    $sql_where .= " AND weight > " . $key_w1;
}
elseif(empty($key_w1) && !empty($key_w2)) {
    $sql_where .= " AND weight < " . $key_w2;
}
else {
    $sql_where .= " AND weight BETWEEN " . min($key_w1,$key_w2) . " AND " . max($key_w1,$key_w2);
}

$str_find = '搜尋符合的記錄';

$sqlstr = "SELECT * FROM person ";
$sqlstr .= $sql_where;

$sth = $pdo->prepare($sqlstr);

// 執行 SQL
try { 
    $sth = $pdo->query($sqlstr);

    $total_rec = $sth->rowCount();
    $cnt = 0;
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

        $cnt++;

        $data .= <<< HEREDOC
            <tr align="center">
            <th>{$cnt}</th>
            <td>{$uid}</td>
            <td>{$usercode}</td>
            <td>{$username}</td>
            <td>{$address}</td>
            <td>{$birthday}</td>
            <td>{$height}</td>
            <td>{$weight}</td>
            <td>{$remark}</td>
            <td><a href="display.php?uid=$uid">詳細</a></td>
            <td><a href="edit.php?uid=$uid">修改</a></td>
            <td><a href="delete.php?uid=$uid" onClick="return confirm('確定要刪除嗎？');">刪除</a></td>
        </tr>
HEREDOC;
    }

   //網頁顯示
    $ihc_content = <<< HEREDOC
    <p align="center"><a href="javascript:show_sql();">查看SQL語法</a></p>
    <h3>共有 $total_rec 筆記錄</h2>
    <table border="1" class="table">   
        <tr>
            <th>順序</th>
            <th>uid</th>
            <th>代碼</th>
            <th>姓名</th>
            <th>地址</th>
            <th>生日</th>
            <th>身高</th>
            <th>體重</th>
            <th>備註</th>
            <th colspan="3" align="center"><a href="add.php">新增記錄</a></th>
        </tr>
    {$data}
    </table>
HEREDOC;

    // 找不到資料時
    if ($total_rec==0) { $ihc_content = '<p class="center">無資料</p>';}
}
catch(PDOException $e) {
    // db_error(ERROR_QUERY, $e->getMessage());
    $ihc_error = error_message('ERROR_QUERY', $e->getMessage());
}

db_close();


//網頁表頭
$js_sql = nl2br($sqlstr);
$js_sql = addslashes($js_sql);
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


// 網頁顯示
$html = <<< HEREDOC
<p align="center"><a href="javascript:show_sql();">查看SQL語法</a></p>
{$ihc_content}
{$ihc_error}
<div id="dialog" title="SQL String"></div>
HEREDOC;

include 'pagemake.php';
pagemake($html, $head);;
?>