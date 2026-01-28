<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/depend/vendor/autoload.php';
use Bitrix\Main\Loader;
use Bitrix\Main\Application;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = include($_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/include/.settings.php');

    $captchaSecretKey = $settings['api_keys']['recaptcha_secret_key'];
    $telegramBotToken = $settings['api_keys']['telegram_bot_token'];
    $telegramChatId = $settings['api_keys']['telegram_chat_id'];

    // Проверка капчи
    $captchaResponse = $_POST['g-recaptcha-response'];
    $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $response = file_get_contents($verifyUrl . '?secret=' . $captchaSecretKey . '&response=' . $captchaResponse);
    $responseKeys = json_decode($response, true);

    if (intval($responseKeys["success"]) !== 1) {
        exit('Капча не пройдена.');
    }

    // Обработка данных формы
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $resume = $_FILES['resume'];

    // Проверка и отправка резюме
    if ($resume['error'] === UPLOAD_ERR_OK) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/upload/resumes/';
        $uploadFile = $uploadDir . basename($resume['name']);

        // Получаем MIME-тип файла
        $fileType = mime_content_type($resume['tmp_name']);

        // Проверка MIME-типов
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
        if (!in_array($fileType, $allowedTypes)) {
            exit("Неверный тип файла. Разрешены только PDF, DOC, DOCX и TXT.");
        }

        // Перемещаем загруженный файл в директорию
        if (!move_uploaded_file($resume['tmp_name'], $uploadFile)) {
            exit("Ошибка при загрузке файла: " . $resume['error']);
        }
    }

    // Настройка PHPMailer
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->isSMTP();
    $mail->Host = 'smtp.yandex.ru';
    $mail->SMTPAuth = true;
    $mail->Username = 'g.mizin@dapsite.ru';
    $mail->Password = 'fzrivxsmztimlplm';
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Отправитель и получатель
    $mail->setFrom('g.mizin@dapsite.ru', 'Отправитель почты');
    $mail->addAddress('g.mizin@dapsite.ru');
    $mail->Subject = 'Новая заявка с формы';
    $mail->Body    = "Имя: $name\nПочта: $email\nТелефон: $phone\nРезюме: " . basename($uploadFile);

    // Добавление файла как вложение
    $mail->addAttachment($uploadFile, basename($uploadFile));

    // Отправка письма
    if (!$mail->send()) {
        exit('Сообщение не может быть отправлено. Почтовая ошибка: ' . $mail->ErrorInfo);
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

    exit('Заявка отправлена в Telegram.');
}
?>
