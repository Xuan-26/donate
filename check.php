<?php

// 確保正確引用 Composer 的自動加載文件
require __DIR__ . '/vendor/autoload.php';

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

// 使用環境變數來保護敏感資訊
$channelAccessToken = getenv('LINE_CHANNEL_ACCESS_TOKEN');
$channelSecret = getenv('LINE_CHANNEL_SECRET');

// 初始化 LINE Bot
$httpClient = new CurlHTTPClient($channelAccessToken);
$bot = new LINEBot($httpClient, ['channelSecret' => $channelSecret]);

// 資料庫連接設定
$servername = "localhost";
$username = "CS380B";
$password = "YZUCS380B";
$dbname = "CS380B";

try {
    // 建立 MySQL 資料庫連接
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    exit("無法連接至資料庫");
}

// 取得 Webhook 資料
$content = file_get_contents('php://input');
$events = json_decode($content, true);

if (!is_null($events['events'])) {
    foreach ($events['events'] as $event) {
        if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
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
    
    return $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

// 關閉資料庫連接
$conn->close();

?>

