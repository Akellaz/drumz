<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $telegram_bot_token = '8437219378:AAGwxLY5uWbQVj02Il-woz7vCXyAUkF3l8k';
    $telegram_chat_id = '1917758771';

    // Получаем и очищаем данные из формы
    $name = htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8');
    $purpose = htmlspecialchars($_POST['purpose'] ?? '', ENT_QUOTES, 'UTF-8');
    $source = htmlspecialchars($_POST['source'] ?? 'неизвестно', ENT_QUOTES, 'UTF-8');
    $teacher = htmlspecialchars($_POST['teacher'] ?? 'неизвестно', ENT_QUOTES, 'UTF-8');
    $location = htmlspecialchars($_POST['location'] ?? 'неизвестно', ENT_QUOTES, 'UTF-8');

    // Проверяем, что обязательные поля заполнены
    if (empty($name) || empty($phone) || empty($purpose)) {
        http_response_code(400);
        echo "Ошибка: Все поля обязательны.";
        exit;
    }

    // Формируем сообщение для Telegram
    $telegram_msg = "🔔 <b>Новая заявка с сайта Drumz.ru!</b>\n\n";
    $telegram_msg .= "<b>Имя:</b> $name\n";
    $telegram_msg .= "<b>Телефон:</b> $phone\n";
    $telegram_msg .= "<b>Цель:</b> $purpose\n";
    $telegram_msg .= "<b>Источник:</b> $source\n";
    $telegram_msg .= "<b>Преподаватель:</b> $teacher\n";
    $telegram_msg .= "<b>Локация:</b> $location\n";

    // URL для отправки сообщения
    $telegram_url = "https://api.telegram.org/bot" . $telegram_bot_token . "/sendMessage";

    // Подготовка данных для отправки
    $post_fields = [
        'chat_id' => $telegram_chat_id,
        'text' => $telegram_msg,
        'parse_mode' => 'HTML'
    ];

    // Инициализация cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $telegram_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    // Выполняем запрос
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Проверяем результат
    if ($http_code == 200) {
        $response_data = json_decode($response, true);
        if ($response_data['ok']) {
            // Успешно отправлено в Telegram
            http_response_code(200);
            echo "Спасибо! Ваша заявка отправлена. Мы свяжемся с вами в ближайшее время.";
        } else {
            // Ошибка от API Telegram
            http_response_code(500);
            error_log("Telegram API Error: " . print_r($response_data, true));
            echo "Произошла ошибка при отправке заявки в Telegram. Пожалуйста, свяжитесь с нами напрямую через Telegram @SchepotinSergey.";
        }
    } else {
        // Ошибка HTTP-запроса
        http_response_code(500);
        error_log("Telegram HTTP Error: Code $http_code, Response: $response");
        echo "Произошла ошибка при отправке заявки. Пожалуйста, свяжитесь с нами напрямую через Telegram @SchepotinSergey.";
    }
} else {
    // Если форма была вызвана не через POST
    http_response_code(403);
    echo "Доступ запрещен.";
}
?>