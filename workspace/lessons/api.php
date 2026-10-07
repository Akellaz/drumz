<?php
// ═══════════════════════════════════════════════════════════════
// НАСТРОЙКИ: Отключаем вывод HTML-ошибок, чтобы не ломать JSON
// ═══════════════════════════════════════════════════════════════
ini_set('display_errors', 0);
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json; charset=utf-8');

$host = 'localhost';
$db   = 'cl439291_lessons';
$user = 'cl439291_lessons';
$pass = 'dM0fQ0vN5l';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка подключения к базе данных']);
    exit;
}

$action = $_GET['action'] ?? 'list';

// ═══════════════════════════════════════════════════════════════
// ЛОКАЛЬНАЯ ПРОВЕРКА ТОКЕНА (Без запроса к Google)
// ═══════════════════════════════════════════════════════════════
function verifyFirebaseTokenLocally($token) {
    $tokenParts = explode('.', $token);
    if (count($tokenParts) !== 3) {
        return ['valid' => false, 'error' => 'Неверный формат токена (должно быть 3 части)'];
    }

    $base64 = str_replace(['-', '_'], ['+', '/'], $tokenParts[1]);
    $padded = str_pad($base64, strlen($base64) % 4 === 0 ? strlen($base64) : strlen($base64) + (4 - strlen($base64) % 4), '=', STR_PAD_RIGHT);
    
    $payloadJson = base64_decode($padded);
    $payload = json_decode($payloadJson, true);

    if (!$payload) {
        return ['valid' => false, 'error' => 'Не удалось декодировать данные токена'];
    }

    $projectId = 'drumz-c9989';
    $expectedIss = 'https://securetoken.google.com/' . $projectId;

    if (!isset($payload['iss']) || $payload['iss'] !== $expectedIss) {
        return ['valid' => false, 'error' => 'Неверный издатель токена (iss)'];
    }

    if (!isset($payload['aud']) || $payload['aud'] !== $projectId) {
        return ['valid' => false, 'error' => 'Неверная аудитория (aud). Ожидалось: ' . $projectId];
    }

    return [
        'valid' => true,
        'user_id' => $payload['sub'],
        'email' => $payload['email'] ?? 'unknown'
    ];
}

// ═══════════════════════════════════════════════════════════════
// ЛОГИКА АВТОРИЗАЦИИ
// ═══════════════════════════════════════════════════════════════
$publicActions = ['public_list', 'public_get', 'logout'];
$requiresAuth = !in_array($action, $publicActions);
$currentUserId = null; 

if ($requiresAuth) {
    if (isset($_SESSION['user_id'])) {
        $currentUserId = $_SESSION['user_id'];
    } else {
        $token = '';
        
        // 1. Сначала ищем токен в заголовке (для GET запросов из workspace/index.php)
        if (isset($_SERVER['HTTP_AUTHORIZATION']) && strpos($_SERVER['HTTP_AUTHORIZATION'], 'Bearer ') === 0) {
            $token = trim(substr($_SERVER['HTTP_AUTHORIZATION'], 7));
        } 
        // 2. Если нет, ищем в теле запроса (для POST запросов из builder.php)
        else {
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true);
            $token = trim($input['token'] ?? '');
        }

        if (!$token) {
            http_response_code(403);
            echo json_encode(['error' => 'Доступ запрещен: токен не предоставлен']);
            exit;
        }

        $result = verifyFirebaseTokenLocally($token);
        
        if (!$result['valid']) {
            http_response_code(403);
            echo json_encode(['error' => $result['error']]);
            exit;
        }

        $currentUserId = $result['user_id'];
        $_SESSION['user_id'] = $currentUserId;
        $_SESSION['user_email'] = $result['email'];
    }
}

// ═══════════════════════════════════════════════════════════════
// ОБРАБОТКА ЗАПРОСОВ
// ═══════════════════════════════════════════════════════════════

if ($action === 'list') {
    // Добавили content_type в выборку
    $stmt = $pdo->prepare("SELECT id, title, content_type, status, updated_at, data FROM lessons WHERE user_id = ? ORDER BY updated_at DESC");
    $stmt->execute([$currentUserId]);
    $lessons = $stmt->fetchAll();
    
    foreach ($lessons as &$lesson) {
        $lessonData = json_decode($lesson['data'], true);
        $lesson['illustration_code'] = '';
        if (isset($lessonData['illustration']['blocks']) && is_array($lessonData['illustration']['blocks'])) {
            foreach ($lessonData['illustration']['blocks'] as $block) {
                if (in_array($block['type'], ['svg-art', 'css-art']) && !empty($block['code'])) {
                    $lesson['illustration_code'] = $block['code'];
                    break;
                }
            }
        }
        unset($lesson['data']);
    }
    echo json_encode($lessons);
}

elseif ($action === 'public_list') {
    // Фильтрация по типу контента. По умолчанию 'lesson', чтобы не сломать старый раздел /lessons/
    $contentType = $_GET['type'] ?? 'lesson'; 
    
    $stmt = $pdo->prepare("SELECT id, title, content_type, updated_at, data FROM lessons WHERE status = 'published' AND content_type = ? ORDER BY updated_at DESC");
    $stmt->execute([$contentType]);
    $lessons = $stmt->fetchAll();
    
    foreach ($lessons as &$lesson) {
        $lessonData = json_decode($lesson['data'], true);
        $lesson['illustration_code'] = '';
        if (isset($lessonData['illustration']['blocks']) && is_array($lessonData['illustration']['blocks'])) {
            foreach ($lessonData['illustration']['blocks'] as $block) {
                if (in_array($block['type'], ['svg-art', 'css-art']) && !empty($block['code'])) {
                    $lesson['illustration_code'] = $block['code'];
                    break;
                }
            }
        }
        unset($lesson['data']);
    }
    echo json_encode($lessons);
}

elseif ($action === 'get') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) { http_response_code(400); echo json_encode(['error' => 'Не указан ID урока']); exit; }
    $stmt = $pdo->prepare("SELECT id, title, content_type, status, data FROM lessons WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $currentUserId]);
    $lesson = $stmt->fetch();
    if ($lesson) { echo json_encode($lesson); } 
    else { http_response_code(404); echo json_encode(['error' => 'Урок не найден или нет прав']); }
}

elseif ($action === 'public_get') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) { http_response_code(400); echo json_encode(['error' => 'Не указан ID урока']); exit; }
    $stmt = $pdo->prepare("SELECT id, title, data FROM lessons WHERE id = ? AND status = 'published'");
    $stmt->execute([$id]);
    $lesson = $stmt->fetch();
    if ($lesson) { echo json_encode($lesson); } 
    else { http_response_code(404); echo json_encode(['error' => 'Урок не найден или не опубликован']); }
}

elseif ($action === 'save') {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    $title = trim($input['title'] ?? 'Новый материал');
    $lessonData = $input['data'] ?? null;
    $contentType = $input['content_type'] ?? ($input['data']['content_type'] ?? 'lesson');
    $id = isset($input['id']) ? (int)$input['id'] : 0;

    if (!$lessonData) { http_response_code(400); echo json_encode(['error' => 'Отсутствуют данные']); exit; }

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE lessons SET title = ?, content_type = ?, data = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $contentType, json_encode($lessonData, JSON_UNESCAPED_UNICODE), $id, $currentUserId]);
        if ($stmt->rowCount() === 0) {
            http_response_code(403);
            echo json_encode(['error' => 'Отказано в доступе']);
            exit;
        }
        echo json_encode(['status' => 'success', 'id' => $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO lessons (title, content_type, data, user_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $contentType, json_encode($lessonData, JSON_UNESCAPED_UNICODE), $currentUserId]);
        echo json_encode(['status' => 'success', 'id' => (int)$pdo->lastInsertId()]);
    }
}

elseif ($action === 'publish') {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    $id = (int)($input['id'] ?? 0);
    if (!$id) { http_response_code(400); echo json_encode(['error' => 'Не указан ID']); exit; }
    $stmt = $pdo->prepare("UPDATE lessons SET status = 'published' WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $currentUserId]);
    echo json_encode(['status' => 'success']);
}

elseif ($action === 'unpublish') {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    $id = (int)($input['id'] ?? 0);
    if (!$id) { http_response_code(400); echo json_encode(['error' => 'Не указан ID']); exit; }
    $stmt = $pdo->prepare("UPDATE lessons SET status = 'draft' WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $currentUserId]);
    echo json_encode(['status' => 'success']);
}

elseif ($action === 'delete') {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    $id = (int)($input['id'] ?? 0);
    if (!$id) { http_response_code(400); echo json_encode(['error' => 'Не указан ID для удаления']); exit; }
    $stmt = $pdo->prepare("DELETE FROM lessons WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $currentUserId]);
    echo json_encode(['status' => 'success']);
}

elseif ($action === 'logout') {
    session_destroy();
    echo json_encode(['status' => 'success']);
}

else {
    http_response_code(400);
    echo json_encode(['error' => 'Неверный запрос']);
}