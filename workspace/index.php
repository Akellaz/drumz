<?php
  $title = "Рабочее пространство | Drumz";
  $description = "Личное рабочее пространство пользователя Drumz.";
  $base = dirname(__DIR__);
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
    .lessons-section { margin-top: 32px; }
    .lessons-section h2 { font-size: 1.25rem; margin-bottom: 16px; }
    .lessons-list { list-style: none; padding: 0; margin: 0; }
    
    .lesson-item {
      background: #fff; 
      border: 1px solid #e2e8f0; 
      border-radius: 10px;
      padding: 16px; 
      margin-bottom: 12px;
      display: flex; 
      align-items: center; 
      gap: 16px;
      transition: box-shadow 0.15s, border-color 0.15s;
    }
    .lesson-item:hover { 
      box-shadow: 0 4px 12px rgba(0,0,0,0.04); 
      border-color: #cbd5e1;
    }
    
    .lesson-illustration-thumb {
      width: 120px; height: 80px; background: #f8fafc; border: 1px solid #e2e8f0;
      border-radius: 8px; display: flex; align-items: center; justify-content: center;
      overflow: hidden; flex-shrink: 0;
    }
    .lesson-illustration-thumb svg { max-width: 100%; max-height: 100%; width: auto; height: auto; display: block; }
    .lesson-illustration-thumb.empty { color: #94a3b8; font-size: 12px; border-style: dashed; background: #fff; }

    .lesson-info { flex: 1; min-width: 0; }
    .lesson-title-row { display: flex; align-items: center; gap: 10px; margin-bottom: 4px; flex-wrap: wrap; }
    .lesson-title { font-weight: 700; font-size: 1rem; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .lesson-meta { font-size: 0.8rem; color: #94a3b8; }
    
    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; flex-shrink: 0; }
    .badge-draft { background: #f1f5f9; color: #64748b; }
    .badge-published { background: #dcfce7; color: #16a34a; }
    
    .lesson-actions { display: flex; gap: 6px; flex-shrink: 0; margin-left: auto; }
    .btn-sm {
      padding: 6px 12px; font-size: 12px; border-radius: 6px; border: 1px solid #e2e8f0; cursor: pointer; font-weight: 500;
      text-decoration: none; display: inline-flex; align-items: center; gap: 4px; background: #fff; color: #475569; transition: all 0.15s;
    }
    .btn-sm:hover { border-color: #94a3b8; color: #0f172a; }
    .btn-view { color: #3b82f6; border-color: #bfdbfe; }
    .btn-view:hover { background: #eff6ff; border-color: #3b82f6; }
    .btn-publish { color: #16a34a; border-color: #bbf7d0; }
    .btn-publish:hover { background: #f0fdf4; border-color: #16a34a; }
    .btn-unpublish { color: #f59e0b; border-color: #fde68a; }
    .btn-unpublish:hover { background: #fffbeb; border-color: #f59e0b; }
    .btn-delete { color: #ef4444; border-color: #fecaca; }
    .btn-delete:hover { background: #fef2f2; border-color: #ef4444; }
    
    .empty-state { text-align: center; padding: 40px 20px; color: #94a3b8; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1; }
    .empty-state a { color: #3b82f6; text-decoration: none; }
    .empty-state a:hover { text-decoration: underline; }

    @media (max-width: 600px) {
      .lesson-item { flex-direction: column; align-items: flex-start; gap: 12px; }
      .lesson-illustration-thumb { width: 100%; height: 120px; }
      .lesson-actions { width: 100%; justify-content: flex-end; margin-left: 0; margin-top: 8px; flex-wrap: wrap; }
    }
  </style>
</head>
<body>
  <?php require_once $base . '/includes/header.php'; ?>

  <div class="workspace-layout">
    <?php require_once __DIR__ . '/sidebar.php'; ?>
    
    <main class="workspace-main">
      <div class="workspace-content">
        <h1>Рабочее пространство</h1>
        <p style="color: var(--text-light); margin-top: 8px;">Управление уроками и материалами.</p>
        
        <div class="lessons-section">
        
          <ul id="lessonsList" class="lessons-list">
            <li class="empty-state">Загрузка...</li>
          </ul>
        </div>
      </div>
    </main>
  </div>

  <?php require_once $base . '/includes/footer.php'; ?>
  <script type="module" src="/assets/auth.js"></script>
  
  <script>
    // Словарь для красивого отображения типов
    const TYPE_CONFIG = {
        'lesson': { title: '📚 Уроки', badge: 'Урок' },
        'rudiment': { title: '🥁 Рудименты', badge: 'Рудимент' },
        'article': { title: '📝 Статьи', badge: 'Статья' },
        'other': { title: '📦 Прочее', badge: 'Материал' }
    };

    async function loadLessons() {
      const listEl = document.getElementById('lessonsList');
      try {
        const token = typeof window.getFirebaseToken === 'function' ? await window.getFirebaseToken() : null;
        const headers = { 'Content-Type': 'application/json' };
        if (token) headers['Authorization'] = 'Bearer ' + token;

        const res = await fetch('/workspace/lessons/api.php?action=list', { method: 'GET', headers: headers });

        if (res.status === 403) {
          listEl.innerHTML = '<li class="empty-state">Пожалуйста, <a href="/">войдите в систему</a>.</li>';
          return;
        }

        const items = await res.json();
        if (!Array.isArray(items) || items.length === 0) {
          listEl.innerHTML = '<li class="empty-state">Материалов пока нет. <a href="/DLE/builder.php">Создать первый</a></li>';
          return;
        }

        listEl.innerHTML = '';
        
        // Группируем элементы по content_type
        const grouped = {};
        items.forEach(item => {
            const type = item.content_type || 'lesson';
            if (!grouped[type]) grouped[type] = [];
            grouped[type].push(item);
        });

        // Рендерим каждую группу
        for (const [type, lessons] of Object.entries(grouped)) {
            const config = TYPE_CONFIG[type] || TYPE_CONFIG['other'];
            
            // Заголовок группы
            const groupHeader = document.createElement('h2');
            groupHeader.className = 'lessons-section';
            groupHeader.style.marginTop = '32px';
            groupHeader.textContent = config.title;
            listEl.appendChild(groupHeader);

            const ul = document.createElement('ul');
            ul.className = 'lessons-list';

            lessons.forEach(lesson => {
                const date = new Date(lesson.updated_at).toLocaleDateString('ru-RU', { day: 'numeric', month: 'short', year: 'numeric' });
                const isPublished = lesson.status === 'published';
                const statusBadgeClass = isPublished ? 'badge-published' : 'badge-draft';
                const statusBadgeText = isPublished ? 'Опубликован' : 'Черновик';
                
                const publishBtn = isPublished
                    ? `<button class="btn-sm btn-unpublish" onclick="togglePublish(${lesson.id}, false)">Снять</button>`
                    : `<button class="btn-sm btn-publish" onclick="togglePublish(${lesson.id}, true)">Опубликовать</button>`;

                const illustrationHtml = lesson.illustration_code 
                    ? `<div class="lesson-illustration-thumb">${lesson.illustration_code}</div>`
                    : `<div class="lesson-illustration-thumb empty">Нет иллюстрации</div>`;

                const li = document.createElement('li');
                li.className = 'lesson-item';
                li.innerHTML = `
                    ${illustrationHtml}
                    <div class="lesson-info">
                        <div class="lesson-title-row">
                            <span class="lesson-title">${escapeHtml(lesson.title)}</span>
                            <span class="badge" style="background: #e0f2fe; color: #0369a1;">${config.badge}</span>
                            <span class="badge ${statusBadgeClass}">${statusBadgeText}</span>
                        </div>
                        <div class="lesson-meta">Обновлён: ${date}</div>
                    </div>
                    <div class="lesson-actions">
                        <a href="/lessons/view.php?id=${lesson.id}" target="_blank" class="btn-sm btn-view" title="Открыть">👁️</a>
                        <a href="/DLE/builder.php?lesson_id=${lesson.id}" class="btn-sm">Ред.</a>
                        ${publishBtn}
                        <button class="btn-sm btn-delete" onclick="deleteLesson(${lesson.id})" title="Удалить">×</button>
                    </div>
                `;
                ul.appendChild(li);
            });
            listEl.appendChild(ul);
        }

      } catch (e) {
        console.error("Ошибка загрузки:", e);
        listEl.innerHTML = '<li class="empty-state" style="color:#ef4444;">Ошибка загрузки.</li>';
      }
    }

    async function togglePublish(id, publish) {
      const action = publish ? 'publish' : 'unpublish';
      try {
        const token = typeof window.getFirebaseToken === 'function' ? await window.getFirebaseToken() : null;
        const headers = { 'Content-Type': 'application/json' };
        if (token) headers['Authorization'] = 'Bearer ' + token;

        const res = await fetch(`/workspace/lessons/api.php?action=${action}`, {
          method: 'POST',
          headers: headers,
          body: JSON.stringify({ id, token: token })
        });
        const result = await res.json();
        if (result.status === 'success') {
          loadLessons(); // Перезагружаем список при успехе
        } else {
          alert('Ошибка: ' + (result.error || 'Неизвестная ошибка'));
        }
      } catch (e) {
        console.error(e);
        alert('Ошибка сети');
      }
    }

    async function deleteLesson(id) {
      if (!confirm('Удалить этот материал безвозвратно?')) return;
      try {
        const token = typeof window.getFirebaseToken === 'function' ? await window.getFirebaseToken() : null;
        const headers = { 'Content-Type': 'application/json' };
        if (token) headers['Authorization'] = 'Bearer ' + token;

        const res = await fetch('/workspace/lessons/api.php?action=delete', {
          method: 'POST',
          headers: headers,
          body: JSON.stringify({ id, token: token })
        });
        const result = await res.json();
        if (result.status === 'success') {
          loadLessons(); // Перезагружаем список при успехе
        } else {
          alert('Ошибка: ' + (result.error || 'Неизвестная ошибка'));
        }
      } catch (e) {
        console.error(e);
        alert('Ошибка сети');
      }
    }

    function escapeHtml(text) {
      if (!text) return '';
      const d = document.createElement('div');
      d.textContent = text;
      return d.innerHTML;
    }

    document.addEventListener('DOMContentLoaded', loadLessons);
  </script>
</body>
</html>