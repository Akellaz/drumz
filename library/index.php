<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php require_once __DIR__ . '/../includes/seo.php'; ?>
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
    .tree-view ul { list-style: none; padding-left: 16px; margin: 0; }
    .tree-view li { margin: 1px 0; }
    .folder, .file { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; }
    .folder > strong { font-weight: 600; color: #1e40af; }
    .file > a.filename { color: #1e293b; text-decoration: none; }
    .file > a.filename:hover { text-decoration: underline; }
    .folder-item summary { list-style: none; cursor: pointer; }
    .folder-item summary:focus { outline: none; }
    .folder-item > summary::marker,
    .folder-item > summary::-webkit-details-marker { display: none; }

    /* ===== Панель фильтров (минимализм) ===== */
    .category-filter {
      margin: 16px 0;
      padding: 14px 16px;
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    .filter-row {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }
    .filter-label {
      font-weight: 500;
      color: #6b7280;
      font-size: 13px;
      letter-spacing: 0.02em;
    }

    /* Чипы-ноты */
    .chips { display: flex; gap: 8px; flex-wrap: wrap; }
    .chip {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 6px 12px;
      border-radius: 999px;
      background: #fff;
      border: 1.5px solid #e5e7eb;
      cursor: pointer;
      user-select: none;
      transition: all 0.15s ease;
      min-width: 44px;
      height: 36px;
    }
    .chip input { position: absolute; opacity: 0; pointer-events: none; }
    .chip:hover { border-color: #9ca3af; transform: translateY(-1px); }
    .chip.active {
      border-width: 2px;
      background: #fafafa;
    }
    .chip.active[data-cat="четверти"]     { border-color: #16a34a; background: #f0fdf4; }
    .chip.active[data-cat="восьмые"]      { border-color: #2563eb; background: #eff6ff; }
    .chip.active[data-cat="шестнадцатые"] { border-color: #d97706; background: #fffbeb; }
    .chip.active[data-cat="синкопы"]      { border-color: #db2777; background: #fdf2f8; }
    .chip.active[data-cat="галоп"]        { border-color: #7c3aed; background: #f5f3ff; }
    .chip.active[data-cat="обратный галоп"]         { border-color: #0891b2; background: #ecfeff; }
    .chip.active[data-cat="восьмая с точкой"]       { border-color: #ca8a04; background: #fefce8; }
    .chip.active[data-cat="шестнадцатая-восьмая-шестнадцатая"] { border-color: #be185d; background: #fdf2f8; }

    .chip img {
      height: 22px;
      width: auto;
      display: block;
      pointer-events: none;
    }
    .chip .chip-text {
      font-size: 12px;
      color: #4b5563;
      white-space: nowrap;
    }
    .chip.active .chip-text { color: #111827; font-weight: 500; }

    /* Компактный переключатель режима (прилеплен к чипам длительностей) */
    .mode-toggle {
      display: inline-flex;
      border: 1px solid #e5e7eb;
      border-radius: 6px;
      overflow: hidden;
      background: #fff;
      margin-left: auto;
    }
    .mode-toggle label {
      padding: 3px 8px;
      font-size: 11px;
      cursor: pointer;
      color: #9ca3af;
      border-right: 1px solid #e5e7eb;
      transition: all 0.15s;
      white-space: nowrap;
    }
    .mode-toggle label:last-child { border-right: none; }
    .mode-toggle input { display: none; }
    .mode-toggle label.active { background: #f9fafb; color: #6b7280; font-weight: 500; }
    .mode-toggle label:hover { color: #6b7280; }

    .filter-stats { margin-left: auto; font-size: 13px; color: #6b7280; }
    .filter-reset {
      font-size: 13px;
      color: #ef4444;
      background: none;
      border: none;
      cursor: pointer;
      padding: 4px 8px;
      border-radius: 6px;
    }
    .filter-reset:hover { background: #fee2e2; }

    /* ===== Теги категорий ===== */
    .category-tag {
      display: inline-block;
      font-size: 10px;
      padding: 2px 7px;
      margin-left: 4px;
      border-radius: 3px;
      font-weight: 500;
      text-transform: lowercase;
      white-space: nowrap;
      background: #f1f5f9;
      color: #64748b;
    }
    .category-tag[data-cat="четверти"]     { background: #dcfce7; color: #166534; }
    .category-tag[data-cat="восьмые"]      { background: #dbeafe; color: #1e40af; }
    .category-tag[data-cat="шестнадцатые"] { background: #fef3c7; color: #92400e; }
    .category-tag[data-cat="синкопы"]      { background: #fce7f3; color: #9d174d; }

    /* ===== Звёзды сложности ===== */
    .difficulty { font-size: 12px; letter-spacing: 1px; margin-left: 6px; white-space: nowrap; }
    .stars-filled { color: #f59e0b; }
    .stars-empty  { color: #d1d5db; }

    /* Звёзды-чипы (фильтр) — компактные, без пустых звёзд */
    .star-chips { display: flex; gap: 6px; flex-wrap: wrap; }
    .star-chip {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 6px 10px;
      border-radius: 999px;
      background: #fff;
      border: 1.5px solid #e5e7eb;
      cursor: pointer;
      user-select: none;
      transition: all 0.15s ease;
      height: 32px;
      font-size: 12px;
      position: relative;
    }
    .star-chip input { position: absolute; opacity: 0; pointer-events: none; }
    .star-chip:hover { border-color: #f59e0b; transform: translateY(-1px); }
    .star-chip.active {
      border-width: 2px;
      border-color: #f59e0b;
      background: #fffbeb;
    }
    .star-chip .star-filled { color: #f59e0b; }

    /* Пустое состояние */
    .no-results {
      display: none;
      padding: 32px;
      text-align: center;
      color: #64748b;
      background: #fefce8;
      border-radius: 8px;
      margin-top: 12px;
    }
    .no-results.visible { display: block; }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="container">
    <h1>📁 Библиотека</h1>

    <?php
    // === Словарь картинок для категорий ===
    $noteImages = [
        'четверти'                              => '4n.png',
        'восьмые'                               => '8n.png',
        'шестнадцатые'                          => '16n.png',
        'синкопы'                               => 'z2c2.png',
        'галоп'                                 => 'c2cc.png',
        'обратный галоп'                        => 'ccc2.png',
        'восьмая с точкой'                      => 'c3cn.png',
        'шестнадцатая-восьмая-шестнадцатая'     => 'cc2c.png',
    ];
    $noteImagesDir = '/assets/pic/';

    function normalizeCategories($raw) {
        $result = [];
        if (is_array($raw)) {
            foreach ($raw as $c) {
                if (is_string($c) && trim($c) !== '') $result[] = mb_strtolower(trim($c));
            }
        } elseif (is_string($raw) && trim($raw) !== '') {
            $result[] = mb_strtolower(trim($raw));
        }
        return empty($result) ? ['other'] : array_values(array_unique($result));
    }

    function normalizeDifficulty($raw) {
        if (is_numeric($raw)) {
            return max(1, min(5, (int)$raw));
        }
        $map = [
            'beginner' => 1, 'очень легко' => 1,
            'easy' => 2, 'легко' => 2,
            'intermediate' => 3, 'средне' => 3, 'средняя' => 3,
            'advanced' => 4, 'hard' => 4, 'сложно' => 4,
            'expert' => 5, 'очень сложно' => 5,
        ];
        if (is_string($raw)) {
            $key = mb_strtolower(trim($raw));
            if (isset($map[$key])) return $map[$key];
        }
        return null;
    }

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
                    'type'       => 'file',
                    'webPath'    => '/library/' . str_replace(DIRECTORY_SEPARATOR, '/', substr($fullPath, strlen(__DIR__) + 1)),
                    'title'      => ($data['title'] ?? $item),
                    'category'   => normalizeCategories($data['category'] ?? null),
                    'difficulty' => normalizeDifficulty($data['difficulty'] ?? null),
                    'folder'     => basename($path)
                ];
            }
        }
        ksort($items);
        return $items;
    }

    function collectCategories($items) {
        $cats = [];
        foreach ($items as $item) {
            if ($item['type'] === 'dir') {
                $cats = array_merge($cats, collectCategories($item['children']));
            } else {
                foreach ($item['category'] as $c) {
                    if ($c !== 'other') $cats[] = $c;
                }
            }
        }
        return array_values(array_unique($cats));
    }

    function renderTreeHtml($items, $depth = 0) {
        if (empty($items)) {
            if ($depth === 0) echo "<p>Библиотека пуста.</p>";
            return;
        }
        echo "<ul>\n";
        foreach ($items as $name => $item) {
            if ($item['type'] === 'dir') {
                $isOpen = false;
                if ($depth === 0) {
                    $isOpen = in_array(mb_strtolower($name), ['books', 'intro', 'фрагменты', 'песни']);
                }
                echo "<li class='folder-li'><details class='folder-item'" . ($isOpen ? ' open' : '') . ">";
                echo "<summary class='folder'><span>📁</span> <strong>" . htmlspecialchars($name) . "</strong></summary>\n";
                renderTreeHtml($item['children'], $depth + 1);
                echo "</details></li>\n";
            } else {
                $categories = $item['category'] ?? ['other'];
                $categoriesJson = htmlspecialchars(json_encode($categories, JSON_UNESCAPED_UNICODE));
                $diffVal = $item['difficulty'] !== null ? (int)$item['difficulty'] : '';

                echo "<li class='file-item' "
                   . "data-categories='" . $categoriesJson . "' "
                   . "data-difficulty='" . $diffVal . "'>";
                echo "<div class='file'>";
                echo "<a href='/song-editor/?load=" . urlencode($item['webPath']) . "' class='filename' target='_blank'>";
                echo "📄 " . htmlspecialchars($item['title']);
                echo "</a>";

                if (!empty($item['difficulty'])) {
                    $d = $item['difficulty'];
                    $filled = str_repeat('★', $d);
                    $empty = str_repeat('☆', 5 - $d);
                    echo " <span class='difficulty' title='Сложность: $d из 5'>";
                    echo "<span class='stars-filled'>$filled</span><span class='stars-empty'>$empty</span></span>";
                }

                foreach ($categories as $cat) {
                    echo " <span class='category-tag' data-cat='" . htmlspecialchars($cat) . "'>"
                       . htmlspecialchars($cat) . "</span>";
                }

                echo "</div></li>\n";
            }
        }
        echo "</ul>\n";
    }

    $tree = renderDirTree(__DIR__);

    $allCategories = collectCategories($tree);
    $catOrder = [
        'четверти' => 1, 'восьмые' => 2, 'шестнадцатые' => 3, 'синкопы' => 4,
        'галоп' => 5, 'обратный галоп' => 6,
        'восьмая с точкой' => 7, 'шестнадцатая-восьмая-шестнадцатая' => 8,
    ];
    usort($allCategories, function ($a, $b) use ($catOrder) {
        $oa = $catOrder[$a] ?? 100;
        $ob = $catOrder[$b] ?? 100;
        return $oa <=> $ob ?: strcasecmp($a, $b);
    });
    ?>

    <!-- ===== Панель фильтров ===== -->
    <div class="category-filter">
        <div class="filter-row">
            <span class="filter-label">Длительности</span>
            <div class="chips" id="chips">
                <?php foreach ($allCategories as $cat):
                    $imgFile = $noteImages[$cat] ?? null;
                ?>
                    <label class="chip" data-cat="<?= htmlspecialchars($cat) ?>" title="<?= htmlspecialchars($cat) ?>">
                        <input type="checkbox" class="duration-cb" value="<?= htmlspecialchars($cat) ?>">
                        <?php if ($imgFile): ?>
                            <img src="<?= htmlspecialchars($noteImagesDir . $imgFile) ?>" alt="<?= htmlspecialchars($cat) ?>">
                        <?php else: ?>
                            <span class="chip-text"><?= htmlspecialchars($cat) ?></span>
                        <?php endif; ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <div class="mode-toggle" id="mode-toggle">
                <label data-mode="any" class="active"><input type="radio" name="filter-mode" value="any" checked> любая</label>
                <label data-mode="all"><input type="radio" name="filter-mode" value="all"> только эти</label>
            </div>
        </div>
        <div class="filter-row">
            <span class="filter-label">Сложность</span>
            <div class="star-chips" id="star-chips">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <label class="star-chip" data-stars="<?= $i ?>" title="Сложность <?= $i ?>">
                        <input type="checkbox" class="difficulty-cb" value="<?= $i ?>">
                        <span class="star-filled"><?= str_repeat('★', $i) ?></span>
                    </label>
                <?php endfor; ?>
            </div>
            <button class="filter-reset" id="filter-reset" onclick="resetFilter()" style="display:none;">✕ Сбросить всё</button>
            <span class="filter-stats" id="filter-stats"></span>
        </div>
    </div>

    <div id="library-content">
        <div class="tree-view">
            <?php renderTreeHtml($tree); ?>
        </div>
    </div>

    <div class="no-results" id="no-results">
        <p>🤷 Ничего не найдено. Попробуй другую комбинацию.</p>
    </div>
  </main>

  <script>
    function getDeclension(n, forms) {
        const abs = Math.abs(n) % 100;
        const n1 = abs % 10;
        if (abs > 10 && abs < 20) return forms[2];
        if (n1 > 1 && n1 < 5) return forms[1];
        if (n1 === 1) return forms[0];
        return forms[2];
    }

    function applyFilters() {
        const checkedCats = Array.from(document.querySelectorAll('.duration-cb:checked'))
                                 .map(cb => cb.value.toLowerCase());
        const catMode = document.querySelector('input[name="filter-mode"]:checked').value;

        const checkedStars = Array.from(document.querySelectorAll('.difficulty-cb:checked'))
                                  .map(cb => parseInt(cb.value));

        const fileItems   = document.querySelectorAll('.file-item');
        const statsEl     = document.getElementById('filter-stats');
        const resetBtn    = document.getElementById('filter-reset');
        const noResults   = document.getElementById('no-results');
        const libraryEl   = document.getElementById('library-content');

        const hasActiveFilters = checkedCats.length > 0 || checkedStars.length > 0;
        resetBtn.style.display = hasActiveFilters ? 'inline-block' : 'none';

        let count = 0;
        fileItems.forEach(item => {
            let cats = [];
            try {
                cats = JSON.parse(item.getAttribute('data-categories') || '[]')
                           .map(c => c.toLowerCase())
                           .filter(c => c !== 'other');
            } catch (e) { cats = []; }

            let catMatch;
            if (checkedCats.length === 0) {
                catMatch = true;
            } else if (catMode === 'all') {
                catMatch = (checkedCats.length === cats.length) && checkedCats.every(c => cats.includes(c));
            } else {
                catMatch = checkedCats.some(c => cats.includes(c));
            }

            const rawDiff = item.getAttribute('data-difficulty');
            const itemDifficulty = rawDiff === '' ? null : parseInt(rawDiff);

            let diffMatch;
            if (checkedStars.length === 0) {
                diffMatch = true;
            } else {
                diffMatch = itemDifficulty !== null && checkedStars.includes(itemDifficulty);
            }

            const match = catMatch && diffMatch;
            item.style.display = match ? '' : 'none';
            if (match) count++;
        });

        document.querySelectorAll('.folder-item').forEach(folder => {
            if (!hasActiveFilters) {
                folder.closest('li').style.display = '';
                return;
            }
            const hasVisible = Array.from(folder.querySelectorAll('.file-item'))
                                    .some(f => f.style.display !== 'none');
            if (hasVisible) {
                folder.closest('li').style.display = '';
                folder.setAttribute('open', '');
            } else {
                folder.closest('li').style.display = 'none';
            }
        });

        if (hasActiveFilters && count === 0) {
            libraryEl.style.display = 'none';
            noResults.classList.add('visible');
            statsEl.textContent = '';
        } else {
            libraryEl.style.display = '';
            noResults.classList.remove('visible');
            statsEl.textContent = hasActiveFilters
                ? `Найдено: ${count} ${getDeclension(count, ['файл', 'файла', 'файлов'])}`
                : '';
        }

        sortAllFolders(hasActiveFilters);
    }

    function sortAllFolders(doSort) {
        document.querySelectorAll('.folder-item, .tree-view').forEach(container => {
            const ul = container.querySelector(':scope > ul');
            if (!ul) return;

            if (!container._originalOrder) {
                container._originalOrder = Array.from(ul.children);
            }

            if (doSort) {
                const subfolders = container._originalOrder.filter(el => el.matches('.folder-li'));
                const files      = container._originalOrder.filter(el => el.matches('.file-item'));

                files.sort((a, b) => {
                    const da = parseInt(a.dataset.difficulty);
                    const db = parseInt(b.dataset.difficulty);
                    const va = isNaN(da) ? 999 : da;
                    const vb = isNaN(db) ? 999 : db;
                    return va - vb;
                });

                [...subfolders, ...files].forEach(child => ul.appendChild(child));
            } else {
                container._originalOrder.forEach(child => ul.appendChild(child));
            }
        });
    }

    function resetFilter() {
        document.querySelectorAll('.duration-cb').forEach(cb => {
            cb.checked = false;
            cb.closest('.chip').classList.remove('active');
        });
        document.querySelectorAll('.difficulty-cb').forEach(cb => {
            cb.checked = false;
            cb.closest('.star-chip').classList.remove('active');
        });
        applyFilters();
    }

    document.querySelectorAll('.chip input').forEach(cb => {
        cb.addEventListener('change', () => {
            cb.closest('.chip').classList.toggle('active', cb.checked);
            applyFilters();
        });
    });

    document.querySelectorAll('#mode-toggle label').forEach(label => {
        label.addEventListener('click', () => {
            document.querySelectorAll('#mode-toggle label').forEach(l => l.classList.remove('active'));
            label.classList.add('active');
            label.querySelector('input').checked = true;
            applyFilters();
        });
    });

    document.querySelectorAll('.star-chip input').forEach(cb => {
        cb.addEventListener('change', () => {
            cb.closest('.star-chip').classList.toggle('active', cb.checked);
            applyFilters();
        });
    });
  </script>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>