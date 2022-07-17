<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

include 'config.php';
include 'utility.php';

// 接收傳入變數
$page = isset($_GET['page']) ? $_GET['page'] : 1;   // 目前的頁碼

$numpp = 10;  // 每頁的筆數

// 連接資料庫
$pdo = db_open();

// 取得分頁所需之資訊 (總筆數、總頁數、擷取記錄之起始位置)
$sqlstr = "SELECT count(*) as total_rec FROM person ";
$sth = $pdo->query($sqlstr);
if($row = $sth->fetch(PDO::FETCH_ASSOC))
{
   $total_rec = $row["total_rec"];
}
$total_page = ceil($total_rec / $numpp);  // 計算總頁數


// 擷取該分頁資料
$tmp_start = ($page-1) * $numpp;  // 從第幾筆記錄開始抓取資資料

// SQL 語法
$sqlstr = "SELECT * FROM person ";
$sqlstr .= " LIMIT " . $tmp_start . "," . $numpp;

// 執行SQL及處理結果
$data = '';
$sth = $pdo->prepare($sqlstr);

if($sth->execute())
{
   // 成功執行 query 指令
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
      
      $data .= <<< HEREDOC
      <tr>
         <td><input type="checkbox" name="a_uid[]" value="{$uid}"></td>
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
   $lnk_pagelist .= '[' . $i . '] ';
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

   // 將各種超連結組合成HTML顯示畫面
   $ihc_navigator = '';
   // $ihc_navigator .= '<table border="0" align="center"><tr><td>' . $lnk_pagelist . '</td></tr></table>';
   $ihc_navigator .= <<< HEREDOC
   <table border="0" align="center">
   <tr>
   <td>頁數：{$page} / {$total_page}</td>
   <td>&nbsp;&nbsp;&nbsp;</td>
   <td>
      <a href="{$lnk_pagehead}">第一頁</a> 
      <a href="{$lnk_pageprev}">上一頁</a> 
      <a href="{$lnk_pagenext}">下一頁</a> 
      <a href="{$lnk_pagelast}">最末頁</a>
   </td>
   <td>&nbsp;&nbsp;&nbsp;</td>
   <td>移至頁數</td>
   <td>{$lnk_pagegoto}</td>
   </tr>
   </table>
HEREDOC;
   // ------ 分頁處理結束 -------------------------------------
}


$html = <<< HEREDOC
<h2 align="center">共有 {$total_rec} 筆記錄</h2>
{$ihc_navigator}
<p align="center">※※※ 注意：換頁後，目前已勾選之記錄將會取消勾選 ※※※</p>
<form method="post" action="delete_batch_exec.php">
<table border="1" align="center">   
   <tr>
      <th><input type="submit" value="刪除" onClick="return confirm('確定要刪除所有勾選的記錄嗎？');"></th>
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

include 'pagemake.php';
pagemake($html);
?>