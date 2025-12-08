<?php
// Устанавливаем SEO-данные
$title = "Библиотека партий — Drumz";
$description = "Открытая библиотека ритмических упражнений, этюдов и партий для ударных. Используйте в обучении или создавайте свои.";

// Подключаем SEO
require_once __DIR__ . '/../includes/seo.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title) ?></title>
  <meta name="description" content="<?= htmlspecialchars($description) ?>">
  <link rel="stylesheet" href="/assets/style.css?v=<?= time(); ?>">
  <style>
    .tree-view {
      font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Consolas', 'Courier New', monospace;
      font-size: 13px;
      background: #f6f8fa;
      padding: 8px 10px;
      border-radius: 4px;
      margin-top: 12px;
      line-height: 1.4;
    }
    .tree-view ul {
      list-style: none;
      padding-left: 16px;
      margin: 0;
    }
    .tree-view li {
      margin: 1px 0;
    }
    .folder, .file {
      display: flex;
      align-items: center;
      gap: 4px;
    }
    .folder > strong {
      font-weight: 600;
      color: #1e40af;
    }
    .file > a.filename {
      color: #1e293b;
      text-decoration: none;
    }
    .file > a.filename:hover {
      text-decoration: underline;
    }
    .short-link {
      margin-left: 6px;
      font-size: 11px;
      color: #64748b;
    }
    .short-link a {
      color: #4f46e5;
      text-decoration: none;
    }
    .short-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="container">
    <h1>📁 Библиотека</h1>

    <?php
    // === Короткие ссылки: /library/Name → редирект в редактор ===
    if (!empty($_GET['exercise'])) {
        $exerciseName = basename($_GET['exercise']);
        $found = null;

        $dirs = array_filter(glob(__DIR__ . '/*'), 'is_dir');
        foreach ($dirs as $dir) {
            $path = $dir . '/' . $exerciseName . '.json';
            if (file_exists($path)) {
                $subdir = basename($dir);
                $found = "/$subdir/$exerciseName.json";
                break;
            }
        }

        if ($found) {
            $editorUrl = '/song-editor/?load=' . urlencode('/library' . $found);
            header("Location: $editorUrl", true, 302);
            exit;
        } else {
            echo "<p>Упражнение «" . htmlspecialchars($exerciseName) . "» не найдено.</p>";
            echo '<p><a href="/library/">← К библиотеке</a></p>';
            require_once __DIR__ . '/../includes/footer.php';
            exit;
        }
    }

    // === Построение дерева ===
    function renderDirTree($path) {
        $items = [];
        foreach (scandir($path) as $item) {
            if ($item === '.' || $item === '..') continue;
            $fullPath = $path . DIRECTORY_SEPARATOR . $item;
            if (is_dir($fullPath)) {
                $items[$item] = [
                    'type' => 'dir',
                    'children' => renderDirTree($fullPath)
                ];
            } elseif (pathinfo($item, PATHINFO_EXTENSION) === 'json') {
                $content = @file_get_contents($fullPath);
                $data = $content ? json_decode($content, true) : null;
                $items[$item] = [
                    'type' => 'file',
                    'webPath' => '/library/' . str_replace(DIRECTORY_SEPARATOR, '/', substr($fullPath, strlen(__DIR__) + 1)),
                    'title' => ($data['title'] ?? $item),
                    'short' => basename($item, '.json')
                ];
            }
        }
        ksort($items);
        return $items;
    }

    function renderTreeHtml($items) {
        if (empty($items)) {
            echo "<p>Библиотека пуста.</p>";
            return;
        }
        echo "<div class='tree-view'><ul>\n";
        foreach ($items as $name => $item) {
            if ($item['type'] === 'dir') {
                echo "<li><div class='folder'><span>📁</span> <strong>" . htmlspecialchars($name) . "</strong></div>\n";
                renderTreeHtml($item['children']);
                echo "</li>\n";
            } else {
                echo "<li><div class='file'>";
                echo "<a href='/song-editor/?load=" . urlencode($item['webPath']) . "' class='filename' target='_blank'>";
                echo "📄 " . htmlspecialchars($item['title']);
                echo "</a> <span class='short-link'><a href='/library/" . urlencode($item['short']) . "'>🔗</a></span>";
                echo "</div></li>\n";
            }
        }
        echo "</ul></div>\n";
    }

    $tree = renderDirTree(__DIR__);
    renderTreeHtml($tree);
    ?>
  </main>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>