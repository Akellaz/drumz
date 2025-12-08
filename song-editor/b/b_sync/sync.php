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

$filePath = __DIR__ . '/sessions/' . $session . '.json';
$dir = dirname($filePath);
if (!is_dir($dir)) {
    if (!mkdir($dir, 0755, true)) {
        http_response_code(500);
        echo json_encode(['error' => 'Cannot create sessions dir']);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['fragments'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data']);
        exit;
    }
    $input['lastUpdate'] = time();
    file_put_contents($filePath, json_encode($input, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    echo json_encode(['ok' => true]);
    exit;
}

// GET
$since = (int)($_GET['since'] ?? 0);
if (!file_exists($filePath)) {
    echo json_encode(['state' => null]);
    exit;
}

$state = json_decode(file_get_contents($filePath), true);
if (!$state) {
    echo json_encode(['state' => null]);
    exit;
}

$lastUpdate = $state['lastUpdate'] ?? 0;
if ($lastUpdate > $since) {
    echo json_encode(['state' => $state]);
} else {
    echo json_encode(['state' => null]);
}