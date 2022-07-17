<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

$type = isset($_GET['type']) ? $_GET['type'] : '';

$msg = '';
switch($type)
{
   case 'add_save' :
         $msg = '無法新增資料';
		 break;
		 
   case 'edit_save' :
         $msg = '無法修改資料';
		 break;
		 
   case 'delete' :
         $msg = '無法刪除資料';
		 break;
}


$html = <<< HEREDOC
<h2>錯誤警告</h2>
<p>{$msg}</p>
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>