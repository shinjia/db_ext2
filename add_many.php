<?php
/* db_ext v2.0  @Shinjia  #2022/07/16 */

include 'config.php';
include 'utility.php';
include 'function.chinese_name.php';

$count = isset($_POST['count']) ? $_POST['count'] : 0;

$a_addr = array('基隆', '台北', '新北', '桃園', '新竹','台中', '彰化', '雲林', '嘉義', '台南', '高雄', '屏東', '台東', '花蓮', '宜蘭', '南投');

// 網頁內容預設
$ihc_content = '';
$ihc_error = '';

$total_rec = 0;

if($count>0)
{
    // 連接資料庫
    $pdo = db_open();

    $record_all = '';  // 全部記錄串成的字串
    for($i=1; $i<=$count; $i++)
    {
        $usercode = uniqid();
        $username = chinese_name();
        $address  = $a_addr[array_rand($a_addr)];
        $birthday = @date('Y-m-d', @strtotime('-'.mt_rand(0,650*50).' day'));  // 前五十年內的任一天
        $height   = mt_rand(150, 190);
        $weight   = mt_rand(45, 95);
        $remark   = CHR(mt_rand(65, 90));

        // 寫出 SQL 語法
        $record_all .= "(";
        $record_all .= "'$usercode',";
        $record_all .= "'$username',";
        $record_all .= "'$address',";
        $record_all .= "'$birthday',";
        $record_all .= "'$height',";
        $record_all .= "'$weight',";
        $record_all .= "'$remark'),";  // 注意：最後一個欄位之後的符號
    }

    $record_all = rtrim($record_all, ',');  // 移除最後一個逗號

    
    // SQL 語法
    $sqlstr = "INSERT INTO person(usercode, username, address, birthday, height, weight, remark) VALUES ";
    $sqlstr .= $record_all;

    $sth = $pdo->prepare($sqlstr);

    $timer1 = microtime(true);

    // 執行SQL
    try {
        $sth = $pdo->query($sqlstr);
        // $sth->execute();
        
        $total_rec = $sth->rowCount();

        $timer2 = microtime(true);
        $time_diff = $timer2 - $timer1;

        $ihc_content = <<< HEREDOC
        <p class="center">新增成功 {$total_rec} 記錄</p>
        <p class="center">執行耗費時間：{$time_diff} (秒)</p>
HEREDOC;
    }
    catch(PDOException $e) {
        // db_error(ERROR_QUERY, $e->getMessage());
        $ihc_error = error_message('ERROR_QUERY', $e->getMessage());
        $ihc_content = 'XX';
    }
}


$html = <<< HEREDOC
<h2>新增記錄</h2>
<div class="center">
<form method="post" action="?" style="margin:0px;">
    一次新增 <input type="text" name="count" size="2" value="100"> 筆記錄
    <input type="submit" value="執行">
</form>
{$ihc_content}
{$ihc_error}
</div>
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>