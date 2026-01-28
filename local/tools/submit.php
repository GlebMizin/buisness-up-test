<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/depend/vendor/autoload.php';
use Bitrix\Main\Loader;
use Bitrix\Main\Application;

// Функция для отправки JSON-ответа
function sendJsonResponse($success, $message, $httpCode = 200) {
    http_response_code($httpCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => $success, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = include($_SERVER['DOCUMENT_ROOT'].'/bitrix/.settings.php');

    $captchaSecretKey = $settings['api_keys']['recaptcha_secret_key'];
    $telegramBotToken = $settings['api_keys']['telegram_bot_token'];
    $telegramChatId = $settings['api_keys']['telegram_chat_id'];
    $smtp_password = $settings['api_keys']['smtp_password'];
    $smtp_host = $settings['api_keys']['smtp_host'];
    $smtp_port = $settings['api_keys']['smtp_port'];
    $smtp_user = $settings['api_keys']['smtp_user'];

    // Проверка капчи
    $captchaResponse = $_POST['g-recaptcha-response'] ?? '';

    if (empty($captchaResponse)) {
        sendJsonResponse(false, 'Пожалуйста, пройдите проверку reCAPTCHA.', 400);
    }

    $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $response = file_get_contents($verifyUrl . '?secret=' . $captchaSecretKey . '&response=' . $captchaResponse);
    $responseKeys = json_decode($response, true);

    if (intval($responseKeys["success"]) !== 1) {
        sendJsonResponse(false, 'Капча не пройдена. Попробуйте еще раз.', 400);
    }

    // Обработка данных формы
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $resume = $_FILES['resume'];

    // Проверка и отправка резюме
    if ($resume['error'] === UPLOAD_ERR_OK) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/upload/resumes/';

        // Создаем директорию если её нет
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $uploadFile = $uploadDir . basename($resume['name']);

        // Получаем MIME-тип файла
        $fileType = mime_content_type($resume['tmp_name']);

        // Проверка MIME-типов
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
        if (!in_array($fileType, $allowedTypes)) {
            sendJsonResponse(false, "Неверный тип файла. Разрешены только PDF, DOC, DOCX и TXT.", 400);
        }

        // Перемещаем загруженный файл в директорию
        if (!move_uploaded_file($resume['tmp_name'], $uploadFile)) {
            sendJsonResponse(false, "Ошибка при загрузке файла: " . $resume['error'], 500);
        }
    } else {
        sendJsonResponse(false, "Ошибка загрузки файла.", 400);
    }

    // Настройка PHPMailer
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->isSMTP();
    $mail->Host = $smtp_host;
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_user;
    $mail->Password = $smtp_password;
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $smtp_port;
    $mail->CharSet = 'UTF-8';

    // Отправитель и получатель
    $mail->setFrom($smtp_user, 'Отправитель почты');
    $mail->addAddress($smtp_user);
    $mail->Subject = 'Новая заявка с формы';
    $mail->Body    = "Имя: $name\nПочта: $email\nТелефон: $phone\nРезюме: " . basename($uploadFile);

    // Добавление файла как вложение
    $mail->addAttachment($uploadFile, basename($uploadFile));

    // Отправка письма
    if (!$mail->send()) {
        sendJsonResponse(false, 'Сообщение не может быть отправлено. Почтовая ошибка: ' . $mail->ErrorInfo, 500);
    }

    // Отправка резюме в Telegram
    $telegramMessage = "Новая заявка:\nИмя: $name\nПочта: $email\nТелефон: $phone\nРезюме: " . basename($uploadFile);

    // Отправка документа в Telegram
    $arrayQuery = array(
        'chat_id' => $telegramChatId,
        'caption' => $telegramMessage,
        'document' => new CURLFile($uploadFile, $fileType, basename($uploadFile))
    );

    $ch = curl_init('https://api.telegram.org/bot' . $telegramBotToken . '/sendDocument');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);

    sendJsonResponse(true, 'Заявка успешно отправлена!', 200);
}
?>