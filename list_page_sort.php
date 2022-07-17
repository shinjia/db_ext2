<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

include 'config.php';
include 'utility.php';

// 頁碼參數
$page = isset($_GET['page']) ? $_GET['page'] : 1;   // 目前的頁碼
$nump = isset($_GET['nump']) ? $_GET['nump'] : 10;   // 每頁的筆數

// 指定排序時，傳入的參數
$sort1 = isset($_GET['sort1']) ? $_GET['sort1'] : '';
$sort2 = isset($_GET['sort2']) ? $_GET['sort2'] : '';

// 依排序要寫出相關的SQL部份指令，可含公式
$a_sort = array(
     'SF1'=>'username',  
     'SF2'=>'address', 
     'SF3'=>'birthday', 
     'SF4'=>'height', 
     'SF5'=>'weight',
     'MMDD'=>'month(birthday)*100+day(birthday)'  );

if(!empty($a_sort[$sort1]))
{
   $sql_sort = ' ORDER BY ' . $a_sort[$sort1];
   if($sort2=='Y')
   {
      $sql_sort .= ' DESC ';
   }
}
else
{
   $sql_sort = '';
}

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
$sqlstr .= $sql_sort;
$sqlstr .= " LIMIT " . (($page-1)*$nump) . "," . $nump;

// -------------------------------
// // 在各超連結內要增加排序時所需要傳入的參數
// $url_ext = "?sort1=" . $sort1 . "&sort2=" . $sort2;

// // ------ 分頁處理開始 -------------------------------------
// // 處理分頁之超連結：上一頁、下一頁、第一首、最後頁
// $lnk_pageprev = $url_ext . '&page=' . (($page==1)?(1):($page-1));
// $lnk_pagenext = $url_ext . '&page=' . (($page==$total_page)?($total_page):($page+1));
// $lnk_pagehead = $url_ext . '&page=1';
// $lnk_pagelast = $url_ext . '&page=' . $total_page;

// // 處理各頁之超連結：列出所有頁數 (暫未用到，保留供參考)
// $lnk_pagelist = "";
// for($i=1; $i<=$page-1; $i++)
// { $lnk_pagelist .= '<a href="' . $url_ext . '&page='.$i.'">'.$i.'</a> '; }
// $lnk_pagelist .= '[' . $i . '] ';
// for($i=$page+1; $i<=$total_page; $i++)
// { $lnk_pagelist .= '<a href="' . $url_ext . '&page='.$i.'">'.$i.'</a> '; }

// // 處理各頁之超連結：下拉式跳頁選單
// $lnk_pagegoto  = '<form method="GET" action="" style="margin:0;">';
// $lnk_pagegoto .= '<select name="page" onChange="submit();">';
// for($i=1; $i<=$total_page; $i++)
// {
//    $is_current = (($i-$page)==0) ? ' SELECTED' : '';
//    $lnk_pagegoto .= '<option' . $is_current . '>' . $i . '</option>';
// }
// $lnk_pagegoto .= '</select>';
// $lnk_pagegoto .= '<input type="hidden" name="sort1" value="' . $sort1 . '">';
// $lnk_pagegoto .= '<input type="hidden" name="sort2" value="' . $sort2 . '">';
// $lnk_pagegoto .= '</form>';

// // 將各種超連結組合成HTML顯示畫面
// $ihc_navigator = '';
// // $ihc_navigator .= '<table border="0" align="center"><tr><td>' . $lnk_pagelist . '</td></tr></table>';
// $ihc_navigator .= <<< HEREDOC
// <table border="0" align="center">
//  <tr>
//   <td>頁數：{$page} / {$total_page}</td>
//   <td>&nbsp;&nbsp;&nbsp;</td>
//   <td>
//    <a href="{$lnk_pagehead}">第一頁</a> 
//    <a href="{$lnk_pageprev}">上一頁</a> 
//    <a href="{$lnk_pagenext}">下一頁</a> 
//    <a href="{$lnk_pagelast}">最末頁</a>
//   </td>
//   <td>&nbsp;&nbsp;&nbsp;</td>
//   <td>移至頁數</td>
//   <td>{$lnk_pagegoto}</td>
//  </tr>
// </table>
// HEREDOC;
// ------ 分頁處理結束 --------------------------------------


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
            <tr align="center">
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
    $a_ext = array(
        "sort1"=>$sort1,
        "sort2"=>$sort2
    );
    $ihc_navigator = pagination_ext($total_page, $page, $nump, $a_ext);
    // $ihc_navigator = pagination($total_page, $page, $nump);
    
   // $url_ext = "?sort1=" . $sort1 . "&sort2=" . $sort2;
    
    //網頁顯示
    $ihc_content = <<< HEREDOC
    <h3>共有 $total_rec 筆記錄</h2>
    {$ihc_navigator}
    <table border="1" class="table">   
        <tr>
            <th>序號</th>
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


$html = <<< HEREDOC
<p align="center"><a href="javascript:show_sql();">查看SQL語法</a></p>
{$ihc_content}
{$ihc_error}
<div id="dialog" title="SQL String"></div>
HEREDOC;

include 'pagemake.php';
pagemake($html, $head);
?>