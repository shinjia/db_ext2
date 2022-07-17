<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

include 'config.php';
include 'utility.php';

// 頁碼參數
$page = isset($_GET['page']) ? $_GET['page'] : 1;   // 目前的頁碼
$nump = isset($_GET['nump']) ? $_GET['nump'] : 6;   // 每頁的筆數
// 每頁的筆數應該要 (每列欄數*列數)
$columns = 3;  // 多欄顯示之欄位數設定

// 網頁內容預設
$ihc_content = '';
$ihc_error = '';

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
    $cnt = 0;
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
        
        // 多欄處理：若為第一欄，資料顯示前需要先加上新列的頭 <tr>
        if(($cnt % $columns)==0) {
            $data .= '<tr>';
        }
        
        // 配合多欄顯示，資料的顯示方法也需要修改
        // 包含：不含tr的定義；資料項目要包在一個td內；最好能控制寬度
        $data .= <<< HEREDOC
            <td>
            <div style="width:140px; height:160px;">
                {$usercode}<br>
                {$username}<br>
                {$address}<br>
                生日：{$birthday}<br>
                身高：{$height}<br>
                體重：{$weight}<br>
                備註：{$remark}<br>
            </div>
            </td>
HEREDOC;

        // 多欄處理：若為最後一欄，資料顯示後需要加上此列的尾 </tr>
        if(($cnt % $columns)==($columns-1)) {
            $data .= '</tr>';
        }
        
        $cnt++;
    }

    // 多欄處理：若每頁筆數($numpp)未調整成欄數的倍數，則每頁均需補後面不足的空項
    $cnt1 = $cnt % $columns;  // 此列已顯示的項目數
    // 不是最後也不是第一個
    if( ($cnt1<$columns) && ($cnt1>0) ) {
        for($i=$cnt1+1; $i<=$columns; $i++) {
            $data .= '<td><div style="width:140px; height:120px;">&nbsp;</div></td>';
        }
        $data .= '</tr>';
    }

    // 多欄處理：如有需要，在最後一頁，可考慮再把不滿的列數也補上
    if($page==$total_page) {
        $rec_last = $total_rec - ($nump * ($page-1)); // 最後一頁的記錄數
        $rr1 = ceil($rec_last / $columns);  //最後一頁，出現資料的列數
        $rr2 = ceil($nump / $columns);  // 應該有的列數
        for($j=$rr1+1; $j<=$rr2; $j++) {
            // 補上完整的第$i列      
            $data .= '<tr>';
            for($i=1; $i<=$columns; $i++) {
                $data .= '<td><div style="width:140px; height:120px;">&nbsp;</div></td>';
            }
            $data .= '</tr>';
        } 
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