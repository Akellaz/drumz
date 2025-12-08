<?php
// sandbox/index.php
$title = 'Песочница — Drumz';
$description = 'Экспериментальные и разрабатываемые инструменты.';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title) ?></title>
  <meta name="description" content="<?= htmlspecialchars($description) ?>">
  <link rel="stylesheet" href="/assets/style.css?v=<?= time() ?>">
  <style>
    .dev-panel {
      background: #fdfdfd;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 20px;
      font-family: 'SF Mono', 'Consolas', monospace;
      font-size: 0.875rem;
      max-width: 650px;
      margin-top: 24px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .dev-panel h2 {
      margin-top: 0;
      font-size: 1.1rem;
      color: #222;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .dev-links {
      list-style: none;
      padding: 0;
      margin: 16px 0 0;
    }

    .dev-links li {
      margin-bottom: 12px;
      line-height: 1.5;
      display: flex;
      align-items: flex-start;
      gap: 8px;
    }

    .dev-links a {
      color: #0066cc;
      text-decoration: none;
      font-weight: 600;
    }

    .dev-links a:hover {
      text-decoration: underline;
    }

    .status {
      font-weight: normal;
      font-size: 0.8125rem;
    }

    .status.experimental { color: #cc6600; }
    .status.in-progress { color: #cc0000; }
    .status.ready { color: #008800; }

    .footer-note {
      margin-top: 32px;
      font-size: 0.8125rem;
      color: #777;
    }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="container">
    <h1>Песочница</h1>

    <div class="dev-panel">
      <h2>🛠️ В разработке</h2>
      <ul class="dev-links">
        <li>
          <a href="/abc-editor/" target="_blank">ABC-редактор</a>
          <span class="status ready">✅</span>
        </li>
        <li>
          <a href="/hang/" target="_blank">Ханг</a>
          <span class="status experimental">🧪</span>
        </li>
        <li>
          <a href="/timpani/" target="_blank">Литавры</a>
          <span class="status in-progress">🚧</span>
        </li>
		        <li>
          <a href="/glukofon/" target="_blank">Глюкофон</a>
          <span class="status in-progress">🚧</span>
        </li>
		
		
		
		 <li>
          <a href="/songs/Seven_nations_army/" target="_blank">Песни</a>
          <span class="status in-progress">🚧</span>
        </li>
		<li>
          <a href="/drum_book/" target="_blank">Книга барабанщика</a>
          <span class="status in-progress">🚧</span>
        </li>
		<li>
          <a href="/gen/" target="_blank">Секвенсор</a>
          <span class="status in-progress">🚧</span>
        </li>
				<li>
          <a href="/orden/" target="_blank">Система Орден</a>
          <span class="status in-progress">🧪</span>
        </li>
				<li>
          <a href="/art/" target="_blank">Статья</a>
          <span class="status in-progress">🚧</span>
        </li>
						<li>
          <a href="/song-editor/" target="_blank">Редактор песен</a>
          <span class="status in-progress">🧪</span>
        </li>
		
								<li>
          <a href="/library/" target="_blank">Библиотека</a>
          <span class="status in-progress">🧪</span>
        </li>
										<li>
           <a href="/tests/" target="_blank">Математика Ритма</a>
          <span class="status in-progress">🧪</span>
        </li>
      </ul>
    </div>

    <p class="footer-note">Эти инструменты находятся в разработке и могут изменяться.</p>
  </main>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
