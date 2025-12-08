<?php
// В начале /library/tree.php
if (empty($_SERVER['HTTP_REFERER']) || !str_starts_with($_SERVER['HTTP_REFERER'], 'https://drumz.ru/')) {
    http_response_code(403);
    exit;
}
header('Content-Type: application/json; charset=utf-8');

// Защита: только для AJAX-запросов или с вашего домена
$referer = $_SERVER['HTTP_REFERER'] ?? '';
if (!str_contains($referer, '://drumz.ru/')) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}


function buildDirTree($path) {
    $items = [];
    foreach (scandir($path) as $item) {
        if ($item === '.' || $item === '..') continue;
        $fullPath = $path . DIRECTORY_SEPARATOR . $item;
        if (is_dir($fullPath)) {
            $items[$item] = [
                'type' => 'dir',
                'children' => buildDirTree($fullPath)
            ];
        } elseif (pathinfo($item, PATHINFO_EXTENSION) === 'json') {
            $content = @file_get_contents($fullPath);
            $data = $content ? json_decode($content, true) : null;
            $items[$item] = [
                'type' => 'file',
                'url' => '/library/' . str_replace(DIRECTORY_SEPARATOR, '/', substr($fullPath, strlen(__DIR__) + 1)),
                'title' => $data['title'] ?? basename($item, '.json')
            ];
        }
    }
    ksort($items);
    return $items;
}

echo json_encode(buildDirTree(__DIR__), JSON_UNESCAPED_UNICODE);