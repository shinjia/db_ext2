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

$total_rec = 0;
$total_page = 0;

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

    // 找不到資料時
    if($total_rec==0) { $ihc_content = '<p class="center">無資料</p>';}
}
catch(PDOException $e) {
    // db_error(ERROR_QUERY, $e->getMessage());
    $ihc_error = error_message('ERROR_QUERY', $e->getMessage());
}

db_close();


// 套用 Pagination 的導覽列內容 (採用<li>方式處理)
$link_before = 6;   // 前面的頁數(可自行設定)
$link_after = 6;   // 後面的頁數(可自行設定)

$lnk_pageprev = '?nump=' . $nump . '&page=' . (($page==1)?1:($page-1));
$lnk_pagenext = '?nump=' . $nump . '&page=' . (($page==$total_page)?$total_page:($page+1));

$lnk_pagination = '';
$lnk_pagination .= '
  <ul>
    <li class="disabled">第一頁</li>
    <li><a href="' . $lnk_pageprev . '">上一頁</a></li>';

$gprev = ($page-$link_before<=1) ? 1 : ($page-$link_before);
for ($i=$gprev; $i<$page; $i++ )
{ $lnk_pagination .= '<li><a href="?nump='.$nump.'&page='.$i.'">'.$i.'</a></li>'; }
$lnk_pagination .= '<li class="current">' . $i . '</li>';
$gnext = ($page+$link_after>=$total_page)?($total_page):($page+$link_after);
for($i=$page+1; $i<=$gnext; $i++)
{ $lnk_pagination .= '<li><a href="?nump='.$nump.'&page='.$i.'">'.$i.'</a></li>'; }

$lnk_pagination .= '
    <li><a href="' . $lnk_pagenext . '">下一頁</a></li>
    <li class="disabled">最後一頁</li>
  </ul>';


// ------ 分頁處理結束 -------------------------------------

$head = '<link type="text/css" rel="stylesheet" href="pagination.css" />';

$html = <<< HEREDOC
{$ihc_content}

<h3 align="center" style="background-color:#AACC88;">其他各種結合CSS的分頁導覽列</h3>

<h2 class="pagination_title">Digg Style</h2>
<div class="pagination digg">{$lnk_pagination}</div>

<h2 class="pagination_title">Yahoo Style</h2>
<div class="pagination yahoo">{$lnk_pagination}</div>

<h2 class="pagination_title">Meneame Style</h2>
<div class="pagination meneame">{$lnk_pagination}</div>

<h2 class="pagination_title">Flickr Style</h2>
<div class="pagination flickr">{$lnk_pagination}</div>

<h2 class="pagination_title">Sabros.us Style (Mi sabros.us)</h2>
<div class="pagination sabrosus">{$lnk_pagination}</div>

<h2 class="pagination_title">Green Style</h2>
<div class="pagination scott">{$lnk_pagination}</div>

<h2 class="pagination_title">Gray Style</h2>
<div class="pagination quotes">{$lnk_pagination}</div>

<h2 class="pagination_title">Black Style</h2>
<div class="pagination black">{$lnk_pagination}</div>

<h2 class="pagination_title">Mis Algoritmos Style</h2>
<div class="pagination black2">{$lnk_pagination}</div>

<h2 class="pagination_title">Black-Red Style</h2>
<div class="pagination black-red">{$lnk_pagination}</div>

<h2 class="pagination_title">Gray Style 2</h2>
<div class="pagination grayr">{$lnk_pagination}</div>

<h2 class="pagination_title">Yellow Style</h2>
<div class="pagination yellow">{$lnk_pagination}</div>

<h2 class="pagination_title">Jogger Style</h2>
<div class="pagination jogger">{$lnk_pagination}</div>

<h2 class="pagination_title">starcraft 2 Style</h2>
<div class="pagination starcraft2">{$lnk_pagination}</div>

<h2 class="pagination_title">Tres Style</h2>
<div class="pagination tres">{$lnk_pagination}</div>

<h2 class="pagination_title">512megas Style</h2>
<div class="pagination megas512">{$lnk_pagination}</div>

<h2 class="pagination_title">Technorati Style</h2>
<div class="pagination technorati">{$lnk_pagination}</div>

<h2 class="pagination_title">YouTube Style</h2>
<div class="pagination youtube">{$lnk_pagination}</div>

<h2 class="pagination_title">MSDN Search Style</h2>
<div class="pagination msdn">{$lnk_pagination}</div>

<h2 class="pagination_title">Badoo Style</h2>
<div class="pagination badoo">{$lnk_pagination}</div>

<h2 class="pagination_title">Blue Style </h2>
<div class="pagination manu">{$lnk_pagination}</div>
HEREDOC;

include 'pagemake.php';
pagemake($html, $head);
?>