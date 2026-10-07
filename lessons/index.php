<?php
  $base = dirname(__DIR__);
  $title = "Уроки | Drumz";
  $description = "Интерактивные уроки игры на барабанах от Drumz.";
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
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    .lessons-page { 
      flex: 1;
      width: 100%;
      max-width: 1100px; 
      margin: 0 auto; 
      padding: 40px 20px 60px; 
    }
    .lessons-page h1 { font-size: 2rem; margin-bottom: 8px; }
    .lessons-page .lead { color: #64748b; margin-bottom: 32px; font-size: 1.05rem; }
    
    .lessons-grid { 
      display: grid; 
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
      gap: 20px; 
    }
    
    .lesson-card {
      display: flex;
      flex-direction: column;
      background: #fff; 
      border: 1px solid #e2e8f0; 
      border-radius: 12px;
      overflow: hidden;
      text-decoration: none; 
      color: inherit;
      transition: all 0.2s ease;
    }
    .lesson-card:hover {
      border-color: #94a3b8;
      box-shadow: 0 8px 20px rgba(0,0,0,0.06);
      transform: translateY(-3px);
    }
    
    /* Обложка карточки */
    .lesson-card-cover {
      width: 100%;
      height: 160px;
      background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      border-bottom: 1px solid #e2e8f0;
    }
    .lesson-card-cover svg {
      width: 70%;
      height: 70%;
      max-width: 100%;
      max-height: 100%;
      display: block;
    }
    .lesson-card-cover.empty {
      color: #cbd5e1;
      font-size: 2.5rem;
      background: #f8fafc;
    }
    
    /* Текстовая часть */
    .lesson-card-body {
      padding: 16px 20px 20px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .lesson-card-title { 
      font-size: 1.1rem; 
      font-weight: 700; 
      color: #0f172a; 
      margin: 0 0 8px;
      line-height: 1.3;
    }
    .lesson-card-meta { 
      font-size: 0.8rem; 
      color: #94a3b8; 
      margin-top: auto;
      padding-top: 12px;
    }
    
    .empty-state {
      text-align: center; padding: 60px 20px; color: #94a3b8;
      background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;
      grid-column: 1 / -1;
    }
    .loading { text-align: center; padding: 40px; color: #94a3b8; grid-column: 1 / -1; }
  </style>
</head>
<body>
  <?php require_once $base . '/includes/header.php'; ?>

  <div class="lessons-page">
    <h1>Уроки</h1>
    <p class="lead">Интерактивные материалы для самостоятельного изучения.</p>
    <div id="lessonsContainer" class="lessons-grid">
      <div class="loading">Загрузка уроков...</div>
    </div>
  </div>

  <?php require_once $base . '/includes/footer.php'; ?>

  <script>
    async function loadPublicLessons() {
      const container = document.getElementById('lessonsContainer');
      try {
        const res = await fetch('/workspace/lessons/api.php?action=public_list');
        const lessons = await res.json();

        if (!Array.isArray(lessons) || lessons.length === 0) {
          container.innerHTML = '<div class="empty-state">Пока нет опубликованных уроков. Загляните позже!</div>';
          return;
        }

        container.innerHTML = '';
        lessons.forEach(lesson => {
          const date = new Date(lesson.updated_at).toLocaleDateString('ru-RU', {
            day: 'numeric', month: 'long', year: 'numeric'
          });
          
          // Обложка: SVG или иконка-заглушка
          const coverHtml = lesson.illustration_code 
            ? `<div class="lesson-card-cover">${lesson.illustration_code}</div>`
            : `<div class="lesson-card-cover empty">🥁</div>`;

          const card = document.createElement('a');
          card.className = 'lesson-card';
          card.href = `/lessons/view.php?id=${lesson.id}`;
          card.innerHTML = `
            ${coverHtml}
            <div class="lesson-card-body">
              <div class="lesson-card-title">${escapeHtml(lesson.title)}</div>
              <div class="lesson-card-meta">${date}</div>
            </div>
          `;
          container.appendChild(card);
        });
      } catch (e) {
        container.innerHTML = '<div class="empty-state" style="color:#ef4444;">Не удалось загрузить уроки.</div>';
      }
    }

    function escapeHtml(text) {
      if (!text) return '';
      const d = document.createElement('div');
      d.textContent = text;
      return d.innerHTML;
    }

    document.addEventListener('DOMContentLoaded', loadPublicLessons);
  </script>
</body>
</html>