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
$sqlstr = 'DESC person';
// $sqlstr = 'SHOW COLUMNS FROM person';

// 執行 SQL
try { 
    $sth = $pdo->query($sqlstr);

   // 各欄位名稱
    $i = 0;
    $data = '';
    $a_info = $sth->fetchAll(PDO::FETCH_ASSOC);
    foreach ($a_info as $a_fld)
    {
        $i++;
        //echo '<pre>';
        //print_r($a_fld);
        //echo '</pre>';
        $data .= '<tr>';
        $data .= '<td>' . $i . '</td>';
        $data .= '<td>' . $a_fld['Field'] . '</td>';
        $data .= '<td>' . $a_fld['Type'] . '</td>';
        $data .= '<td>' . $a_fld['Null'] . '</td>';
        $data .= '<td>' . $a_fld['Key'] . '</td>';
        $data .= '<td>' . $a_fld['Default'] . '</td>';
        $data .= '<td>' . $a_fld['Extra'] . '</td>';
        $data .= '</tr>';
    }

    $ihc_content = <<< HEREDOC
    <table border="1" class="table">
            <tr>
            <th>順序</th>
            <th>Field</th>
            <th>Type</th>
            <th>Null</th>
            <th>Key</th>
            <th>Default</th>
            <th>Extra</th>
        </tr>
    {$data}
    </table>
HEREDOC;
}
catch(PDOException $e) {
    // db_error(ERROR_QUERY, $e->getMessage());
    $ihc_error = error_message('ERROR_QUERY', $e->getMessage());
}
db_close();


//網頁表頭
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
<h2>資料表定義</h2>
{$ihc_content}
{$ihc_error}
</table>
<div id="dialog" title="SQL String"></div>
HEREDOC;

include 'pagemake.php';
pagemake($html, $head);
?>