<?php
// api.php — drumz.ru • учёт суммы звёзд
error_reporting(E_ALL);
ini_set('display_errors', 0); // В продакшене: 0

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$host = 'localhost';
$db   = 'cl439291_pro';
$user = 'cl439291_pro';
$pass = 'P6N-5VD-gC8-F6n';
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
    echo json_encode(['error' => 'DB Connection failed']);
    exit;
}

$action = $_GET['action'] ?? '';
$student_id = $_GET['student'] ?? null;

try {
    switch ($action) {
        case 'curriculum':
            $levelsRaw = $pdo->query("SELECT id, name, description, unlock_condition, sort_order FROM levels ORDER BY sort_order")->fetchAll();
            $levels = [];
            foreach ($levelsRaw as $lvl) {
                $stmt = $pdo->prepare("SELECT id, name, notation_url, tags FROM items WHERE level_id = ? ORDER BY sort_order");
                $stmt->execute([$lvl['id']]);
                $items = [];
                foreach ($stmt->fetchAll() as $it) {
                    $tagsRaw = $it['tags'] ?? '[]';
                    $items[] = [
                        'id' => $it['id'],
                        'name' => $it['name'],
                        'notation' => $it['notation_url'],
                        'tags' => is_string($tagsRaw) ? (json_decode($tagsRaw, true) ?? []) : []
                    ];
                }
                $levels[] = [
                    'id' => $lvl['id'], 'name' => $lvl['name'], 'description' => $lvl['description'],
                    'unlock_condition' => $lvl['unlock_condition'], 'items' => $items
                ];
            }
            echo json_encode(['levels' => $levels], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'students':
            $students = $pdo->query("SELECT id, name FROM students ORDER BY name")->fetchAll();
            $result = [];
            foreach ($students as $s) {
                $result[] = array_merge($s, getProgressStats($pdo, $s['id']));
            }
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;
            
        case 'progress':
            if (!$student_id) { http_response_code(400); echo json_encode(['error' => 'Student ID required']); break; }
            $stmt = $pdo->prepare("SELECT item_id, status, date_completed, video_url, rating FROM progress WHERE student_id = ?");
            $stmt->execute([$student_id]);
            $data = [];
            foreach ($stmt->fetchAll() as $row) {
                $data[$row['item_id']] = [
                    'status' => $row['status'],
                    'date_completed' => $row['date_completed'],
                    'video' => $row['video_url'],
                    'rating' => $row['rating'] !== null ? (int)$row['rating'] : null
                ];
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;
            
        case 'update_progress':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['error' => 'Method not allowed']); break; }
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input || !isset($input['student'], $input['item'], $input['status'])) { http_response_code(400); echo json_encode(['error' => 'Invalid data']); break; }
            $rating = isset($input['rating']) ? (int)$input['rating'] : null;
            if ($rating !== null && ($rating < 1 || $rating > 5)) { http_response_code(400); echo json_encode(['error' => 'Rating must be 1–5']); break; }
            $stmt = $pdo->prepare("
                INSERT INTO progress (student_id, item_id, status, date_completed, video_url, rating)
                VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE status=VALUES(status), date_completed=VALUES(date_completed), video_url=VALUES(video_url), rating=VALUES(rating), updated_at=CURRENT_TIMESTAMP
            ");
            $stmt->execute([$input['student'], $input['item'], $input['status'], $input['date_completed'] ?? null, $input['video'] ?? null, $rating]);
            echo json_encode(['success' => true]);
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Unknown action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal error']);
}

function getProgressStats($pdo, $student_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM progress WHERE student_id = ? AND status = 'completed'");
    $stmt->execute([$student_id]);
    $completed = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT SUM(rating), COUNT(rating) FROM progress WHERE student_id = ? AND rating IS NOT NULL");
    $stmt->execute([$student_id]);
    $row = $stmt->fetch(PDO::FETCH_NUM);
    $total_stars = (int)($row[0] ?? 0);
    $rated_count = (int)($row[1] ?? 0);

    return [
        'completed'   => $completed,
        'total_stars' => $total_stars,
        'rated_count' => $rated_count
    ];
}
?>