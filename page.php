<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

$code = isset($_GET['code']) ? $_GET['code'] : 'error';


// 參數定義，可依需要自行修改
$path = 'data/';   // 存放網頁內容的資料夾
$filename = $path . $code . '.html';  // 規定副檔案為 .html

if (!file_exists($filename))
{
    // 找不到檔案時的顯示訊息
    $html  = '<br />錯誤：傳遞參數有誤。檔案『' . $filename . '』不存在！';
}
else
{
   $html = join ('', file($filename));   // 讀取檔案內容並組成文字串
}

include 'pagemake.php';
pagemake($html);
?>