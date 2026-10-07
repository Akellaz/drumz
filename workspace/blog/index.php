<?php
  $base = dirname(__DIR__, 2);
  $title = "Управление блогом | Drumz";
  $description = "Управление записями блога Drumz.";
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
    /* Минимальные стили только для админки блога (десктоп) */
    .blog-admin-wrap { max-width: 900px; margin: 0 auto; padding: 20px 0; }
    .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .btn { padding: 10px 20px; border-radius: 6px; border: none; font-weight: 600; cursor: pointer; font-size: 0.95rem; transition: 0.2s; }
    .btn-primary { background: #3b82f6; color: white; }
    .btn-primary:hover { background: #2563eb; }
    .btn-danger { background: #ef4444; color: white; padding: 6px 12px; font-size: 0.85rem; }
    .btn-edit { background: #f59e0b; color: white; padding: 6px 12px; font-size: 0.85rem; margin-right: 8px; }
    .btn-ghost { background: transparent; color: #666; border: 1px solid #ddd; }
    .btn-ghost:hover { background: #f9f9f9; }

    .post-list { list-style: none; padding: 0; margin: 0; }
    .post-item { background: #fff; border: 1px solid #eaeaea; border-radius: 8px; padding: 16px 20px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; }
    .post-item-info { flex: 1; }
    .post-item-title { font-weight: 700; font-size: 1.05rem; margin-bottom: 4px; color: #1a1a1a; }
    .post-item-meta { font-size: 0.85rem; color: #666; }

    /* Раскрывающаяся форма */
    .editor-panel { 
      display: none; 
      background: #f8f9fa; 
      border: 1px solid #eaeaea; 
      border-radius: 8px; 
      padding: 24px; 
      margin-bottom: 24px; 
    }
    .editor-panel.active { display: block; }
    .form-row { display: flex; gap: 16px; margin-bottom: 16px; }
    .form-group { flex: 1; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9rem; color: #333; }
    .form-control { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.95rem; font-family: inherit; box-sizing: border-box; background: #fff; }
    .form-control:focus { outline: none; border-color: #3b82f6; }
    textarea.form-control { min-height: 250px; resize: vertical; font-family: monospace; line-height: 1.5; }
    .editor-actions { display: flex; gap: 12px; margin-top: 8px; }
  </style>
</head>
<body>
  <?php require_once $base . '/includes/header.php'; ?>

  <div class="workspace-layout">
    <?php require_once __DIR__ . '/../sidebar.php'; ?>
    
    <main class="workspace-main">
      <div class="workspace-content blog-admin-wrap">
        <div class="admin-header">
          <h1>Управление блогом</h1>
          <button class="btn btn-primary" id="btnNewPost">+ Новая запись</button>
        </div>

        <!-- Раскрывающаяся форма редактора -->
        <div id="editorPanel" class="editor-panel">
          <h3 id="editorTitle" style="margin-top:0; margin-bottom: 20px;">Новая запись</h3>
          <input type="hidden" id="postId">
          
          <div class="form-row">
            <div class="form-group" style="flex: 0 0 250px;">
              <label>ID записи (латиница)</label>
              <input type="text" id="postIdInput" class="form-control" placeholder="my-new-post">
            </div>
            <div class="form-group">
              <label>Дата</label>
              <input type="date" id="postDate" class="form-control">
            </div>
          </div>
          
          <div class="form-group" style="margin-bottom: 16px;">
            <label>Заголовок</label>
            <input type="text" id="postTitle" class="form-control" placeholder="Заголовок записи">
          </div>
          
          <div class="form-group" style="margin-bottom: 16px;">
            <label>Краткое описание (Excerpt)</label>
            <input type="text" id="postExcerpt" class="form-control" placeholder="Пару строк для карточки на главной">
          </div>

          <div class="form-group">
            <label>Содержимое (HTML)</label>
            <textarea id="postContent" class="form-control" placeholder="<h2>Глава 1</h2><p>Текст записи...</p>"></textarea>
          </div>

          <div class="editor-actions">
            <button class="btn btn-primary" id="btnSave">Сохранить</button>
            <button class="btn btn-ghost" id="btnCancel">Отмена</button>
          </div>
        </div>

        <!-- Список постов -->
        <ul id="postsList" class="post-list">
          <li style="text-align:center; padding: 40px; color: #666;">Загрузка записей...</li>
        </ul>
      </div>
    </main>
  </div>

  <?php require_once $base . '/includes/footer.php'; ?>
  
  <script type="module" src="/workspace/blog/blog.js"></script>
</body>
</html>