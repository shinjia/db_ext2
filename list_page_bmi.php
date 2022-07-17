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

// 定義BMI等級的評語
$a_msg = array(
   'A' => '<span style="color:#FF0000;">太胖了</span>',
   'B' => '<span style="color:#FFAAAA;">稍重</span>'  , 
   'C' => '<span style="color:#0000FF;">標準</span>'  ,
   'D' => '<span style="color:#44FF44;">太輕了</span>',
   'X' => '不詳' );

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


// SQL 語法
$sqlstr = "SELECT uid, usercode, username, height, weight, (weight/((height/100)*(height/100))) as bmi FROM person ";
$sqlstr .= " LIMIT " . (($page-1)*$nump) . "," . $nump;


// 執行 SQL
try { 
   $sth = $pdo->query($sqlstr);

   $data = '';
   while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
      $usercode = html_encode($row['usercode']);
      $username = html_encode($row['username']);
      $height   = html_encode($row['height']);
      $weight   = html_encode($row['weight']);
      
      $bmi      = $row['bmi'];
   
      $str_bmi = number_format($bmi,1);  // 顯示在網頁上的值取一位小數
   
      // 進行判斷
      if( ($bmi >= 25.0) && ($bmi<50) ) $grade = "A";
      elseif(($bmi>=23.0)&&($bmi<25.0)) $grade = "B";
      elseif(($bmi>=18.5)&&($bmi<23.0)) $grade = "C";
      elseif(($bmi< 18.5)&&($bmi>10.0)) $grade = "D";
      else $grade = "X";
      
      $str_msg = $a_msg[$grade];
         
      $data .= <<< HEREDOC
         <tr align="center">
         <td>{$usercode}</td>
         <td>{$username}</td>
         <td>{$height}</td>
         <td>{$weight}</td>
         <td align="right">{$str_bmi}</td>
         <td>{$str_msg}</td>
      </tr>
HEREDOC;
   }

   // 分頁導覽列
   $ihc_navigator = pagination($total_page, $page, $nump);
   
   //網頁顯示
   $ihc_content = <<< HEREDOC
   <p class="center"><a href="javascript:show_sql();">查看SQL語法</a></p>
   <h3>共有 $total_rec 筆記錄</h3>
   {$ihc_navigator}
   <table border="1" class="table">   
      <tr>
         <th>代碼</th>
         <th>姓名</th>
         <th>身高</th>
         <th>體重</th>
         <th>BMI值</th>      
         <th>判定</th>
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


//網頁顯示
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