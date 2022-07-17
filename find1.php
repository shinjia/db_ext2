<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

include 'config.php';

$html = <<< HEREDOC
<div style="text-align: center;">

<h2>簡單查詢功能</h2>

<h3>查詢單筆記錄</h3>
<form method="post" action="find_show1a.php">
  <p>查詢代碼：<input type="text" name="key"> (必須完全相同，且至多只列出一筆)
  <input type="submit" value="送出">
  <br>因資料做列出一筆，故使用在查詢欄位為唯一的情況。
  </p>
</form>
<hr>

<h3>查詢多筆記錄─文字欄位</h3>
<form method="post" action="find_show1b.php">
  <p>查詢姓名：<input type="text" name="key"> (名字內包含之字元)
  <input type="submit" value="送出">
  </p>
</form>
<hr>

<h3>查詢多筆記錄─數字欄位</h3>
<form method="post" action="find_show1c.php">
  <p>查詢 BMI 值介於：<input type="text" name="key1" size="2"> 到 <input type="text" name="key2" size="2"> 之間
  <input type="submit" value="送出">
  </p>
</form>
<hr>

<h3>查詢多筆記錄─日期欄位</h3>
<form method="post" action="find_show1d.php">
  <p>查詢壽星：在 <input type="text" name="key" size="2"> 月份出生的人
  <input type="submit" value="送出">
  </p>
</form>
</div>
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>
