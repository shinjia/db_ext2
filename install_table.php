<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

include 'config.php';

// 連接資料庫
$pdo = db_open();

// 新增資料表之SQL語法 (採用陣列方式，可以設定多個)
// 注意資料表名稱要寫兩處，陣列索引和 SQL 字串裡都有
$a_table["person"] = "
CREATE TABLE person (
    uid int(11) NOT NULL auto_increment,
    usercode varchar(255) NULL,
    username varchar(255) NULL,
    address varchar(255)  NULL,
    birthday date default NULL,
    height int(11) default NULL,
    weight int(11) default NULL,
    remark varchar(255) NULL,
    PRIMARY KEY  (uid)
)
";


// 執行SQL及處理結果
$msg = '';
foreach($a_table as $key=>$sqlstr)
{
    $sth = $pdo->prepare($sqlstr);

    $msg .= '資料表『' . $key . '』安裝';
    try {
        $sth->execute();
        $msg .= '完成';
    }
    catch(PDOException $e) {
    // db_error(ERROR_QUERY, $e->getMessage());
    $msg .= '失敗';
    }
}

db_close();


$html = <<< HEREDOC
<h2>資料表安裝結果</h2>
<p class="center">{$msg}</p>
HEREDOC;

include 'pagemake.php';
pagemake($html, '');
?>