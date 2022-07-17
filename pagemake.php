<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

function pagemake($content='', $head='')
{  
  $html = <<< HEREDOC
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>基本資料庫系統</title>

<script src="jquery.js"></script>
<script src="jquery-ui.js"></script>
<link href="jquery-ui.css" rel="stylesheet" type="text/css">
<style type="text/css">
* {
  // margin: 0;
}

page {
  font-size: 16px;
}

div.container {
  margin: 0 auto;
  width: 96%;
  background-color: #FFFFAA;
}

div#header {
  background-color: #99AA99; 
  padding: 8px;
}

div#footer {
  background-color: #99AA99; 
  text-align: center;
}

#menu {
  height: 30px;
}
#menu > li {
  float: left;  
  width:auto;    
}

table.table {
  margin-left: auto;
  margin-right: auto;
}

th {
  background-color: #AAAAAA;
}

.center {
  text-align: center;
}

h2 {
  text-align: center;
}

h3 {
  text-align: center;
}
</style>

<script>
$(document).ready(function(){
  $("#menu").menu({position: {at: "left bottom"}});
});   
</script>
{$head}
</head>
<body>

<div class="container">

  <div id="header">
    <h1>資料庫程式學習輔助工具 (db_ext v2.0)</h1>
  </div>
  
  <div id="nav">     
    <ul id="menu" class="menubar-icons">
      
      <li>
        <a href="#">說明</a>
        <ul>
          <li><a href="index.php">首頁</a></li>
          <li><a href="page.php?code=note">說明</a></li>
        </ul>
      </li>
      
      <li>
        <a href="#">資料管理</a>
        <ul>
          <li><a href="list_page.php">列出清單 (分頁)</a></li>
          <li><a href="list_all.php">列出清單 (全部，請注意)</a></li>
          <li></li>
          <li><a href="add_many.php">隨機產生多筆記錄新增</a></li>
          <li></li>
          <li><a href="delete_all.php" onclick="return confirm('請注意！確定要全部刪除嗎？')">刪除全部記錄</a></li>
          <li><a href="delete_batch_list.php">勾選多筆記錄刪除</a></li>
        </ul>
      </li>
      
      <li>
          <a href="#">資料顯示變化</a>
          <ul>
            <li><a href="list_page_color.php">奇偶列分顏色</a></li>
            <li><a href="list_page_column.php">多欄式列表</a></li>
            <li><a href="list_page_one.php">單頁單筆記錄的顯示</a></li>
            <li></li>
            <li><a href="list_page_navigator.php">導覽列 (多樣的分頁)</a></li>
            <li><a href="list_page_pagination.php">導覽列 (結合CSS)</a></li>
          </ul>
      </li>
      
      <li>
        <a href="#">SELECT變化</a>
        <ul>
          <li><a href="list_page_bmi.php">自訂計算的欄位(bmi值)</a></li>
          <li></li>
          <li><a href="list_page_sort.php?sort1=SF1">排序 (姓名)</a></li>
          <li><a href="list_page_sort.php?sort1=SF2">排序 (地址)</a></li>
          <li><a href="list_page_sort.php?sort1=SF3">排序 (生日)</a></li>
          <li><a href="list_page_sort.php?sort1=SF4">排序 (身高)</a></li>
          <li><a href="list_page_sort.php?sort1=SF5">排序 (體重)</a></li>
          <li><a href="list_page_sort.php?sort1=MMDD">排序 (月日)</a></li>
          <li></li>
          <li><a href="list_page_sort.php?sort1=SF1&sort2=Y">排序 (姓名)遞減</a></li>
          <li><a href="list_page_sort.php?sort1=SF2&sort2=Y">排序 (地址)遞減</a></li>
          <li><a href="list_page_sort.php?sort1=SF3&sort2=Y">排序 (生日)遞減</a></li>
          <li><a href="list_page_sort.php?sort1=SF4&sort2=Y">排序 (身高)遞減</a></li>
          <li><a href="list_page_sort.php?sort1=SF5&sort2=Y">排序 (體重)遞減</a></li>
          <li><a href="list_page_sort.php?sort1=MMDD&sort2=Y">排序 (月日)遞減</a></li>
        </ul>
      </li>
      
      <li>
        <a href="#">查詢功能</a>
        <ul>
          <li><a href="find1.php">簡單查詢功能</a></li>
          <li><a href="find2.php">進階查詢功能</a></li>
        </ul>
      </li>
      
      <li>
        <a href="#">進階SQL</a>
        <ul>
          <li><a href="query_statistic.php">統計功能</a></li>
          <li><a href="query_distinct.php">不重複記錄</a></li>
          <li><a href="query_group.php">群組功能</a></li>
          <li></li>
          <li><a href="query_desc_table.php">資料表定義</a></li>
          <li></li>
          <li><a href="query_test.php">直接SQL查詢</a></li>
        </ul>
      </li>
      
      <li>
        <a href="#">系統安裝</a>
        <ul>
          <li><a href="install_database.php">建立資料庫 (class)</a></li>
          <li><a href="install_table.php">安裝資料表 (person)</a></li>
          <li><a href="drop_table.php" onClick="return confirm('資料表刪除後，所有記錄亦一併刪除。\n\n確定要刪除資料表嗎？');">刪除資料表 (person)</a></li>
        </ul>
      </li>
        
    </ul>
  </div>
  
  <div id="main">
    {$content}
  </div>

  <div id="footer">
    <p>版權聲明</p>
  </div>

</div>

</body>
</html>  
HEREDOC;

echo $html;
}

?>