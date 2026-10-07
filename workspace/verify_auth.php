<?php
session_start();
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$token = $input['token'] ?? '';

if (!$token) {
    echo json_encode(['status' => 'error', 'message' => 'Токен не предоставлен']);
    exit;
}

// Функция для безопасного декодирования base64url (стандарт JWT)
function base64url_decode($data) {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

try {
    // JWT состоит из 3 частей, разделенных точкой: header.payload.signature
    $tokenParts = explode('.', $token);
    if (count($tokenParts) !== 3) {
        throw new Exception('Неверный формат токена');
    }

    // Декодируем полезную нагрузку (payload)
    $payloadJson = base64url_decode($tokenParts[1]);
    $payload = json_decode($payloadJson, true);

    if (!$payload) {
        throw new Exception('Не удалось декодировать данные токена');
    }

    // ═══════════════════════════════════════════════════════════════
    // 🔒 ПРОВЕРКИ БЕЗОПАСНОСТИ FIREBASE
    // ═══════════════════════════════════════════════════════════════
    $projectId = 'drumz-c9989'; // Твой Project ID (виден в логах Firestore)
    $expectedIss = 'https://securetoken.google.com/' . $projectId;
    
    // ⚠️ ЗАМЕНИ ЭТУ ПОЧТУ НА СВОЮ РЕАЛЬНУЮ!
    $adminEmail = 's.schepotin@gmail.com'; 

    // 1. Проверяем издателя (должен быть Firebase)
    if (!isset($payload['iss']) || $payload['iss'] !== $expectedIss) {
        throw new Exception('Неверный издатель токена (iss)');
    }

    // 2. Проверяем аудиторию (должен быть Project ID, а не App ID!)
    if (!isset($payload['aud']) || $payload['aud'] !== $projectId) {
        throw new Exception('Неверная аудитория (aud). Ожидалось: ' . $projectId . ', получено: ' . ($payload['aud'] ?? 'null'));
    }

    // 3. Проверяем, что токен не просрочен
    if (isset($payload['exp']) && time() >= $payload['exp']) {
        throw new Exception('Срок действия токена истек');
    }

    // 4. Проверяем email администратора
    if (!isset($payload['email']) || $payload['email'] !== $adminEmail) {
        session_destroy();
        echo json_encode([
            'status' => 'error', 
            'message' => 'Доступ запрещен. Почта не совпадает с админской. (Ваша почта: ' . ($payload['email'] ?? 'неизвестна') . ')'
        ]);
        exit;
    }

    // 🎉 Если все проверки пройдены — авторизуем!
    $_SESSION['is_admin'] = true;
    $_SESSION['user_email'] = $payload['email'];
    echo json_encode(['status' => 'success']);

} catch (Exception $e) {
    session_destroy();
    echo json_encode(['status' => 'error', 'message' => 'Ошибка проверки: ' . $e->getMessage()]);
}