<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

include 'config.php';
include 'utility.php';

$key_cd = isset($_POST["key_cd"]) ? $_POST["key_cd"] : "XX";
$key_mv = isset($_POST["key_mv"]) ? $_POST["key_mv"] : "";

// 網頁內容預設
$ihc_content = '';
$ihc_error = '';

// 連接資料庫
$pdo = db_open();

// 寫出 SQL 語法
switch($key_cd)
{
    case "A": // 查詢姓名
            $sql_where = " WHERE username LIKE '%" . $key_mv . "%' ";
            $str_find = '搜尋姓名為『' . $key_mv . '』的記錄';
            break;
            
    case "B": // 查詢地址
            $sql_where = " WHERE address='" . $key_mv . "' ";
            $str_find = '搜尋地址為『' . $key_mv . '』的記錄';
            break;
            
    case "C": // 查詢生日
            $sql_where = " WHERE birthday='" . $key_mv . "' ";
            $str_find = '搜尋生日為『' . $key_mv . '』的記錄';
            break;
            
    default:
            $sql_where = " WHERE false ";
}

$sqlstr = "SELECT * FROM person ";
$sqlstr .= $sql_where;

$sth = $pdo->prepare($sqlstr);

// 執行 SQL
try { 
    $sth->execute();

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
pagemake($html, $head);
?>