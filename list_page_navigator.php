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
$ihc_navigator = '';
$ihc_navigator_google = '';
$ihc_navigator_all = '';

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
    $ihc_navigator = pagination($total_page, $page, $nump);
    
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


    // ------ 分頁處理開始 -------------------------------------
    // 處理分頁之超連結：上一頁、下一頁、第一首、最後頁
    $lnk_pageprev = '?page=' . (($page==1)?(1):($page-1));
    $lnk_pagenext = '?page=' . (($page==$total_page)?($total_page):($page+1));
    $lnk_pagehead = '?page=1';
    $lnk_pagelast = '?page=' . $total_page;

    // 處理各頁之超連結：列出所有頁數 (暫未用到，保留供參考)
    $lnk_pagelist = "";
    for($i=1; $i<=$page-1; $i++)
    { $lnk_pagelist .= '<a href="?page='.$i.'">'.$i.'</a> '; }
    $lnk_pagelist .= '[' . $i . '] ';  // 目前的頁面
    for($i=$page+1; $i<=$total_page; $i++)
    { $lnk_pagelist .= '<a href="?page='.$i.'">'.$i.'</a> '; }

    // 處理各頁之超連結：下拉式跳頁選單
    $lnk_pagegoto  = '<form method="GET" action="" style="margin:0;">';
    $lnk_pagegoto .= '<select name="page" onChange="submit();">';
    for($i=1; $i<=$total_page; $i++)
    {
    $is_current = (($i-$page)==0) ? ' SELECTED' : '';
    $lnk_pagegoto .= '<option' . $is_current . '>' . $i . '</option>';
    }
    $lnk_pagegoto .= '</select>';
    $lnk_pagegoto .= '</form>';


    // 分頁導覽列的變化：標準形式
    $ihc_navigator  = <<< HEREDOC
    <h3 align="center" style="background-color:#AACC88;">原本的標準導覽列</h3>
    <table border="0" align="center">
    <tr>
    <td>頁數：{$page} / {$total_page} &nbsp;&nbsp;&nbsp;</td>
    <td>
    <a href="{$lnk_pagehead}">第一頁</a> 
    <a href="{$lnk_pageprev}">上一頁</a> 
    <a href="{$lnk_pagenext}">下一頁</a> 
    <a href="{$lnk_pagelast}">最末頁</a> &nbsp;&nbsp;
    </td>
    <td>移至頁數：</td>
    <td>{$lnk_pagegoto}</td>
    </tr>
    </table>
HEREDOC;


    // 分頁導覽列的變化：仿Google形式
    $link_before = 5;   // 前面的頁數(可自行設定)
    $link_after = 4;   // 後面的頁數(可自行設定)
    $lnk_google = '';
    $gprev = ($page-$link_before<=1) ? 1 : ($page-$link_before);
    for ($i=$gprev; $i<$page; $i++ )
    { $lnk_google .= '<a href="?page='.$i.'">'.$i.'</a> '; }
    $lnk_google .= '[' . $page . '] ';   // 目前的頁面
    $gnext = ($page+$link_after>=$total_page)?($total_page):($page+$link_after);
    for($i=$page+1; $i<=$gnext; $i++)
    { $lnk_google .= '<a href="?page='.$i.'">'.$i.'</a> '; }

    $ihc_navigator_google = <<< HEREDOC
    <h3 align="center" style="background-color:#AACC88;">仿 Google 的分頁導覽列 (可分別設定之前及之後的頁碼數)</h3>
    <p align="center">{$lnk_google}</p>
HEREDOC;


    // 分頁導覽列的變化：全部都列出來
    $ihc_navigator_all  = <<< HEREDOC
    <h3 align="center" style="background-color:#AACC88;">全部的頁數都顯示出來</h3>
    <p align="center">{$lnk_pagelist}</p>
HEREDOC;

    // ------ 分頁處理結束 -------------------------------------

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
{$ihc_navigator}
{$ihc_navigator_google}
{$ihc_navigator_all}
{$ihc_error}
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>