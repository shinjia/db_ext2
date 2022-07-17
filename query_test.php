<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

include 'config.php';
include 'utility.php';

// 接收傳入變數
$sql = isset($_POST['sql']) ? $_POST['sql'] : ' ';
$sql = trim(stripslashes($sql));  // 去除表單傳遞時產生的脫逸符號

// 網頁內容預設
$ihc_content = '';
$ihc_error = '';

$total_rec = 0;

if(!empty($sql))
{
   // 連接資料庫
   $pdo = db_open();

   // SQL 語法
   $sqlstr = $sql;

   // 執行 SQL
   try {
      $sth = $pdo->query($sqlstr);

      // Part1：取得欄位名稱
      $total_fields = $sth->columnCount();
   
      $data_fld = '<tr>';
      $data_fld .= '<th>順序</th>';
      for($i=0; $i<$total_fields; $i++)
      {
         // 欄位名稱
         $a_info = $sth->getColumnMeta($i);
         //echo '<pre>';
         //print_r($a_info);
         //echo '<pre>';
         $data_fld .= '<th>' . $a_info['name'] . '</th>';
      }
      $data_fld .= '</tr>';
      
      // Part2：取得記錄內容
      $total_rec = $sth->rowCount();
      
      // Part3：列出各筆記錄資料
      $cnt = 0;
      $data = '';
      while($row = $sth->fetch(PDO::FETCH_ASSOC))
      {
         $cnt++;
         $data .= '<tr>';
         $data .= '<th>' . $cnt . '</th>';
         foreach($row as $one)
         {
            $data .= '<td>' . $one . '</td>';
         }
         $data .= '</tr>';
      }
      $data .= '</table>';
   
      $ihc_content = <<< HEREDOC
      <h2>共有 {$total_rec} 筆記錄；{$total_fields} 個欄位</h2>
      <table border="1" cellpadding="2" cellspaceing="0" align="center">
      {$data_fld}
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
}
else
{
   // 初次執行
   $data = '歡迎使用 SQL 測試程式';
   $sql = 'SELECT * FROM person';
}


$html = <<< HEREDOC
<div style="text-align:center;">
   <h1>SQL 指令測試程式</h1>
   <form method="post" action="">
   <textarea name="sql" rows="6" cols="80">{$sql}</textarea><br />
   <input type="submit" value="送出查詢">
   </form>
   <hr>
   {$ihc_content}
</div>
{$ihc_error}
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>