<?php

// 確保正確引用 Composer 的自動加載文件
require __DIR__ . '/vendor/autoload.php';

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

// LINE Bot 設定
$channelAccessToken = 'YOUR_CHANNEL_ACCESS_TOKEN';
$channelSecret = 'YOUR_CHANNEL_SECRET';

$httpClient = new CurlHTTPClient($channelAccessToken);
$bot = new LINEBot($httpClient, ['channelSecret' => $channelSecret]);

// 資料庫連接設定
$servername = "localhost";
$username = "CS380B";
$password = "YZUCS380B";
$dbname = "CS380B";

// 建立 MySQL 資料庫連接
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 取得 Webhook 資料
$content = file_get_contents('php://input');
$events = json_decode($content, true);

if (!is_null($events['events'])) {
    foreach ($events['events'] as $event) {
        if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
            $orderId = $event['message']['text']; // 使用者輸入的訂單編號
            $replyToken = $event['replyToken'];

            // 查詢資料庫以檢查訂單是否存在
            $stmt = $conn->prepare("SELECT amount FROM s1131408 WHERE order_id = ?");
            $stmt->bind_param("s", $orderId);
            $stmt->execute();
            $result = $stmt->get_result();

            // 根據查詢結果生成回覆訊息
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $amount = $row['amount'];
                $replyMessage = "捐款成功！感謝您的支持，您的捐款金額為：{$amount} 元";
            } else {
                $replyMessage = "抱歉，未找到您的訂單紀錄，請確認您的訂單編號是否正確。";
            }

            // 使用 TextMessageBuilder 回覆用戶
            $textMessageBuilder = new TextMessageBuilder($replyMessage);
            $bot->replyMessage($replyToken, $textMessageBuilder);
        }
    }
}

// 關閉資料庫連接
$conn->close();

?>

