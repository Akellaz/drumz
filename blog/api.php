<?php
// 1. Сессия должна быть начата ДО любого вывода
session_start();
header('Content-Type: application/json; charset=utf-8');

$host = 'localhost';
$db   = 'cl439291_blog';
$user = 'cl439291_blog';
$pass = 'fJ2yS5oS8w'; // Не забудь сменить пароль в панели хостинга позже!
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

// 2. ПРОВЕРКА БЕЗОПАСНОСТИ: Если действие меняет данные, требуем авторизацию
$requiresAuth = in_array($action, ['create', 'update', 'delete']);
if ($requiresAuth && !isset($_SESSION['is_admin'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Доступ запрещен. Необходимо авторизоваться.']);
    exit;
}

// 3. ОБРАБОТКА ЗАПРОСОВ
if ($action === 'list') {
    // Отдаём только список без полного текста для быстрой загрузки главной
    $stmt = $pdo->query("SELECT id, title, date, excerpt, visual_type AS visualType, gradient FROM posts ORDER BY date DESC");
    echo json_encode($stmt->fetchAll());
    
} elseif ($action === 'get') {
    $id = $_GET['id'] ?? '';
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Не указан ID поста']);
        exit;
    }
    
    $stmt = $pdo->prepare("SELECT id, title, date, excerpt, visual_type AS visualType, gradient, content FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    
    if ($post) {
        echo json_encode($post);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Пост не найден']);
    }

} elseif ($action === 'create' || $action === 'update') {
    // Читаем JSON из тела запроса
    $data = json_decode(file_get_contents('php://input'), true);
    
    $id = $data['id'] ?? uniqid('post_');
    $title = trim($data['title'] ?? '');
    $date = $data['date'] ?? date('Y-m-d');
    $excerpt = trim($data['excerpt'] ?? '');
    $visual_type = $data['visualType'] ?? 'abstract';
    $content = $data['content'] ?? '';

    if (empty($title) || empty($content)) {
        http_response_code(400);
        echo json_encode(['error' => 'Заголовок и содержание обязательны']);
        exit;
    }

    if ($action === 'update') {
        $stmt = $pdo->prepare("UPDATE posts SET title=?, date=?, excerpt=?, visual_type=?, content=? WHERE id=?");
        $stmt->execute([$title, $date, $excerpt, $visual_type, $content, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO posts (id, title, date, excerpt, visual_type, content) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id, $title, $date, $excerpt, $visual_type, $content]);
    }
    
    echo json_encode(['status' => 'success', 'id' => $id]);

} elseif ($action === 'delete') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? '';
    
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['error' => 'Не указан ID для удаления']);
        exit;
    }
    
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode(['status' => 'success']);

} else {
    http_response_code(400);
    echo json_encode(['error' => 'Неверный запрос (unknown action)']);
}