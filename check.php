<?php

require 'vendor/autoload.php';

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

// LINE Bot 設定
$channelAccessToken = 'qyi50N+v38twzJJXGJENtKyW9Loa8t9egaEljwt3dvMiveTkhwG8HzkCHAdslCXtVWz071ziUOw+zOkNA7Jt1J5eINJoz8ESquDX6B3htGz+OOlxydAIqZOO7shERDCfiBWhT9SAY9+4kKfLMIzZLgdB04t89/1O/w1cDnyilFU=';
$channelSecret = '1a4af244b82ad85d6624481378411f43';

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
            // 取得用戶輸入的訂單編號
            $orderId = $event['message']['text'];
            $replyToken = $event['replyToken'];

            // 查詢捐款狀態
            $orderStatus = checkOrderStatus($conn, $orderId);

            // 根據查詢結果生成回覆訊息
            if ($orderStatus) {
                if ($orderStatus['status'] == 1) {
                    $replyMessage = new TextMessageBuilder("繳費成功！感謝您的支持，您的捐款金額為：{$orderStatus['amount']} 元");
                } else {
                    $replyMessage = new TextMessageBuilder("訂單編號：{$orderId} 尚未完成繳費。");
                }
            } else {
                $replyMessage = new TextMessageBuilder("抱歉，未找到您的訂單紀錄，請確認您的訂單編號是否正確。");
            }

            // 回覆用戶
            $bot->replyMessage($replyToken, $replyMessage);
        }
    }
}

// 查詢訂單狀態的函式
function checkOrderStatus($conn, $orderId) {
    $stmt = $conn->prepare("SELECT amount, status FROM donations WHERE id = ?");
    $stmt->bind_param("s", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// 關閉資料庫連接
$conn->close();

?>
