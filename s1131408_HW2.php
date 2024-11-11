<?php
// 引入必要的庫
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // 自動加載 PHPMailer 和其他依賴庫
require_once('tcpdf/tcpdf.php'); // 引入 TCPDF 用於 PDF 生成
include('phpqrcode/qrlib.php'); // 引入 PHP QR Code 庫

// 資料庫連接設定
$servername = "localhost";
$username = "CS380B";
$password = "YZUCS380B";
$dbname = "CS380B";

// 創建資料庫連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查資料庫連接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 表單提交處理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['Name'];
    $phone = $_POST['PhoneNumber'];
    $email = $_POST['Email'];
    $amount = $_POST['Amount'];
    $message = $_POST['Txtarea'];
    $orderId = substr($name, 0, 3) . '_' . date("md_His");

    // 檢查所有必填字段是否已填寫
    if (!empty($name) && !empty($phone) && !empty($email) && !empty($amount)) {
        // 將資料插入資料庫
        $stmt = $conn->prepare("INSERT INTO s1131408 (name, phone, email, amount, message, order_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $phone, $email, $amount, $message, $orderId);

        if ($stmt->execute()) {
            echo "Data saved to database successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();

        // 關閉資料庫連接
        $conn->close();

        // 生成 PDF 收據
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);

        // 設置收據標題
        $pdf->Cell(0, 10, 'Donation Receipt', 0, 1, 'C');

        // 捐贈者信息表
        $pdf->SetFillColor(173, 216, 230); // 淺藍色
        $pdf->Cell(40, 10, 'Name:', 1, 0, '', true); // true 表示使用背景色
        $pdf->Cell(100, 10, $name, 1, 1);
        $pdf->Cell(40, 10, 'Phone:', 1, 0, '', true);
        $pdf->Cell(100, 10, $phone, 1, 1);
        $pdf->Cell(40, 10, 'Email:', 1, 0, '', true);
        $pdf->Cell(100, 10, $email, 1, 1);
        $pdf->Cell(40, 10, 'Donation Amount:', 1, 0, '', true);
        $pdf->Cell(100, 10, $amount, 1, 1);
        $pdf->Cell(40, 10, 'Message:', 1, 0, '', true);
        $pdf->Cell(100, 10, $message, 1, 1);

        // 設置 PDF 文件保存目錄，並確認 orders 資料夾是否存在
        $pdfDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'orders';
        if (!is_dir($pdfDirectory)) {
            mkdir($pdfDirectory, 0777, true); // 如果 orders 資料夾不存在，則創建它
        }
        $pdfFilePath = $pdfDirectory . DIRECTORY_SEPARATOR . $orderId . '_receipt.pdf';
        $pdf->Output($pdfFilePath, 'F'); // 'F' 表示保存到文件

        // 生成 QR Code 圖片（PNG 或 JPG 格式）
        $qrFilePath = $pdfDirectory . DIRECTORY_SEPARATOR . $orderId . '_qrcode.png';
        $qrData = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/orders/' . $orderId . '_receipt.pdf';
        QRcode::png($qrData, $qrFilePath); // 生成 PNG 格式的 QR Code

        // 發送確認郵件
        $mail = new PHPMailer(true);

        try {
            // SMTP 伺服器設置
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'Xuan.cyo26@gmail.com'; // 替換為您的 Gmail 地址
            $mail->Password = 'nfij ajvt qndt tfeh'; // 替換為您的應用程式專用密碼
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port = 587; 

            // 收件人
            $mail->setFrom('Xuan.cyo26@gmail.com', 'Donation Team');
            $mail->addAddress($email, $name);

            // 郵件內容
            $mail->isHTML(true);
            $mail->Subject = 'Donation Confirmation';
            $mail->Body = "Thank you for your donation!<br>Please find the QR code in the attachment to access your receipt.";
            $mail->AltBody = "Thank you for your donation! Please use the QR code in the attachment to access your receipt.";

            // 附加 QR Code 圖片文件
            $mail->addAttachment($qrFilePath, 'Donation_QRCode.png');

            // 發送郵件
            $mail->send();
            echo "Confirmation email sent successfully.";

            // 顯示 PDF 文件
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . basename($pdfFilePath) . '"');
            readfile($pdfFilePath);
            exit;
        } catch (Exception $e) {
            echo "Failed to send email: {$mail->ErrorInfo}";
        }
    } else {
        echo "Please fill in all required fields.";
    }
}
?>
