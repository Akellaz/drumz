<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: https://drumz.ru');
header('Access-Control-Allow-Methods: GET, POST');

$session = $_GET['session'] ?? null;
if (!$session || !preg_match('/^[a-zA-Z0-9_-]{6,20}$/', $session)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid session ID']);
    exit;
}

$sessionsDir = __DIR__ . '/sessions/';
if (!is_dir($sessionsDir)) {
    if (!mkdir($sessionsDir, 0755, true)) {
        http_response_code(500);
        echo json_encode(['error' => 'Cannot create sessions dir']);
        exit;
    }
}

// === Автоочистка: удаляем сессии старше 24 часов ===
$now = time();
$cutoff = $now - 24 * 3600;
foreach (glob($sessionsDir . '*.json') as $file) {
    if (filemtime($file) < $cutoff) {
        @unlink($file);
    }
}

$filePath = $sessionsDir . $session . '.json';

// === POST: обновление состояния ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['fragments'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data']);
        exit;
    }
    $input['lastUpdate'] = $now;
    file_put_contents($filePath, json_encode($input, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    echo json_encode(['ok' => true]);
    exit;
}

// === GET: получение состояния ===
$since = (int)($_GET['since'] ?? 0);
if (!file_exists($filePath)) {
    echo json_encode(['state' => null]);
    exit;
}

$state = json_decode(file_get_contents($filePath), true);
if (!$state || !isset($state['lastUpdate'])) {
    echo json_encode(['state' => null]);
    exit;
}

if ($state['lastUpdate'] > $since) {
    echo json_encode(['state' => $state]);
} else {
    echo json_encode(['state' => null]);
}