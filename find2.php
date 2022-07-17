<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

include "config.php";


$html = <<< HEREDOC
<div style="text-align: center;">

<h2>進階查詢功能</h2>

<h3>同一個輸入執行不同查詢</h3>
<form method="post" action="find_show2a.php">
    <p>請輸入查詢的資料 <input type="text" name="key_mv" size="10">
    <select name="key_cd">
        <option value="A">姓名</a>
        <option value="B">地址</a>
        <option value="C">生日</a>
    </select>
    <input type="submit" value="送出">
    </p>
</form>
<hr>

<h3>複合式條件查詢</h3>
<p>請輸入欲查詢的資料</p>
<form method="post" action="find_show2b.php">
    姓名：<input type="text" name="key_name"><BR>
    地址：<input type="text" name="key_addr"><BR>
    生日：
        <input type="text" name="key_yy" size="1">年
        <input type="text" name="key_mm" size="1">月
        <input type="text" name="key_dd" size="1">日<BR>
    身高：介於<input type="text" name="key_h1" size="1">至<input type="text" name="key_h2" size="1">之間<BR>
    體重：介於<input type="text" name="key_w1" size="1">至<input type="text" name="key_w2" size="1">之間<BR>
    <BR><input type="submit" value="送出">
</form>
</div>
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>
