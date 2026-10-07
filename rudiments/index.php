<?php
$base = dirname(__DIR__);
$title = "Рудименты | Drumz";
$description = "Справочник барабанных рудиментов с интерактивными примерами и нотами.";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $title; ?></title>
  <meta name="description" content="<?php echo $description; ?>">
  <link rel="stylesheet" href="/assets/style.css?v=<?php echo time(); ?>">
  <style>
    .rudiments-page {
      max-width: 1200px;
      margin: 0 auto;
      padding: 40px 20px;
    }
    .rudiments-page h1 {
      font-size: 2rem;
      margin-bottom: 8px;
    }
    .rudiments-page .lead {
      color: #64748b;
      margin-bottom: 32px;
    }
    .rudiments-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 24px;
    }
    .rudiment-card {
      background: #fff;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      overflow: hidden;
      transition: transform 0.2s, box-shadow 0.2s;
      text-decoration: none;
      color: inherit;
      display: flex;
      flex-direction: column;
    }
    .rudiment-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }
    .rudiment-card-cover {
      width: 100%;
      height: 180px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }
    .rudiment-card-cover svg,
    .rudiment-card-cover .css-art {
      max-width: 100%;
      max-height: 100%;
    }
    .rudiment-card-body {
      padding: 16px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .rudiment-card-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 8px;
    }
    .rudiment-card-meta {
      color: #94a3b8;
      font-size: 0.85rem;
      margin-top: auto;
    }
    .loading {
      text-align: center;
      padding: 40px;
      color: #94a3b8;
      grid-column: 1 / -1;
    }
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #94a3b8;
      grid-column: 1 / -1;
    }
    .empty-state a {
      color: #3b82f6;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <?php require_once $base . '/includes/header.php'; ?>

  <div class="rudiments-page">
    <h1>🥁 Рудименты</h1>
    <p class="lead">Справочник барабанных рудиментов с интерактивными примерами и нотами.</p>
    
    <div id="rudimentsContainer" class="rudiments-grid">
      <div class="loading">Загрузка рудиментов...</div>
    </div>
  </div>

  <?php require_once $base . '/includes/footer.php'; ?>

  <script>
    async function loadPublicRudiments() {
      const container = document.getElementById('rudimentsContainer');
      
      try {
        const res = await fetch('/workspace/lessons/api.php?action=public_list&type=rudiment');
        
        if (!res.ok) throw new Error('Ошибка загрузки');
        
        const rudiments = await res.json();
        
        if (!Array.isArray(rudiments) || rudiments.length === 0) {
          container.innerHTML = '<div class="empty-state">Рудиментов пока нет.</div>';
          return;
        }
        
        container.innerHTML = '';
        
        rudiments.forEach(rudiment => {
          const date = new Date(rudiment.updated_at).toLocaleDateString('ru-RU', { 
            day: 'numeric', 
            month: 'short', 
            year: 'numeric' 
          });
          
          // Извлекаем обложку из данных рудимента
          let coverHtml = '';
          if (rudiment.illustration_code) {
            if (rudiment.illustration_code.startsWith('<svg')) {
              coverHtml = rudiment.illustration_code;
            } else {
              coverHtml = `<div class="css-art">${rudiment.illustration_code}</div>`;
            }
          }
          
          const card = document.createElement('a');
          card.className = 'rudiment-card';
          card.href = `/lessons/view.php?id=${rudiment.id}`;
          
          card.innerHTML = `
            <div class="rudiment-card-cover">
              ${coverHtml || '<span style="font-size: 3rem;">🥁</span>'}
            </div>
            <div class="rudiment-card-body">
              <div class="rudiment-card-title">${escapeHtml(rudiment.title)}</div>
              <div class="rudiment-card-meta">${date}</div>
            </div>
          `;
          
          container.appendChild(card);
        });
      } catch (e) {
        container.innerHTML = '<div class="empty-state" style="color:#ef4444;">Не удалось загрузить рудименты.</div>';
      }
    }
    
    function escapeHtml(text) {
      if (!text) return '';
      const d = document.createElement('div');
      d.textContent = text;
      return d.innerHTML;
    }
    
    document.addEventListener('DOMContentLoaded', loadPublicRudiments);
  </script>
</body>
</html>