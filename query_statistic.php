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
$sqlstr = "
SELECT count(*) as f1, sum(height) as h1, avg(height) as h2, max(height) as h3, min(height) as h4, sum(weight) as w1, avg(weight) as w2, max(weight) as w3, min(weight) as w4 
FROM person
";

// 執行 SQL
try { 
    $sth = $pdo->query($sqlstr);

    $data = '';
    if($row = $sth->fetch(PDO::FETCH_ASSOC))
    {
        $f1 = $row['f1'];
        $h1 = $row['h1'];
        $h2 = number_format($row['h2'],2);
        $h3 = $row['h3'];
        $h4 = $row['h4'];
        $w1 = $row['w1'];
        $w2 = number_format($row['w2'],2);
        $w3 = $row['w3'];
        $w4 = $row['w4'];

        $data = <<< HEREDOC
        <table border="1" class="table">
            <tr>
                <th>總筆數</th>
                <td colspan="4">{$f1}</td>
            </tr>
            <tr>
                <th>&nbsp;</th>
                <th align="right">總和</th>
                <th align="right">平均</th>
                <th align="right">最大值</th>
                <th align="right">最小值</th>
            </tr>
            <tr>
                <th>身高</th>
                <td align="right">{$h1}</td>
                <td align="right">{$h2}</td>
                <td align="right">{$h3}</td>
                <td align="right">{$h4}</td>
            </tr>
            <tr>
                <th>體重</th>
                <td align="right">{$w1}</td>
                <td align="right">{$w2}</td>
                <td align="right">{$w3}</td>
                <td align="right">{$w4}</td>
            </tr>
        </table>
HEREDOC;
    }
    else {
        $data = '無資料';
    }

    $ihc_content = $data;
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
<p class="center"><a href="javascript:show_sql();">查看SQL語法</a></p>
{$ihc_content}
{$ihc_error}
<div id="dialog" title="SQL String">
HEREDOC;

include 'pagemake.php';
pagemake($html, $head);
?>