<?php
  $base = dirname(__DIR__);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Drumz Lesson Engine</title>
    <link rel="stylesheet" href="/assets/style.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/abcjs@6.5.2/abcjs-audio.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="/assets/GrooveScribe/js/abc2svg-1.js?v=3"></script>
    <script src="/assets/GrooveScribe/js/groove_utils.js?v=3"></script>
    
    <style>
        :root {
            --bg-app: #f1f5f9; --bg-panel: #ffffff; --border: #e2e8f0;
            --primary: #0f172a; --primary-hover: #1e293b; --text-main: #0f172a;
            --text-muted: #64748b; --danger: #ef4444; --danger-hover: #dc2626;
        }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: var(--bg-app); color: var(--text-main); min-height: 100vh; display: flex; flex-direction: column; }
        .builder-app-wrapper { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .builder-header { height: 64px; background: var(--bg-panel); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 24px; gap: 16px; flex-shrink: 0; z-index: 10; }
        .back-to-workspace { display: inline-flex; align-items: center; gap: 6px; color: var(--text-muted); text-decoration: none; font-size: 13px; font-weight: 500; padding: 6px 10px; border-radius: 6px; transition: all 0.15s; }
        .back-to-workspace:hover { color: var(--primary); background: rgba(0,0,0,0.04); }
        .app-title { font-size: 16px; font-weight: 700; margin-right: 16px; border-left: 1px solid var(--border); padding-left: 16px; }
        .lesson-title-input { flex: 1; max-width: 400px; padding: 8px 12px; font-size: 14px; font-weight: 500; border: 1px solid var(--border); border-radius: 6px; background: var(--bg-app); color: var(--text-main); outline: none; transition: border-color 0.15s ease; }
        .lesson-title-input:focus { border-color: var(--primary); background: #fff; }
        
        .btn-save {
            display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; font-size: 13px; font-weight: 500;
            border-radius: 6px; border: 1px solid var(--border); background: transparent; color: var(--text-muted);
            cursor: pointer; transition: all 0.15s ease;
        }
        .btn-save:hover { border-color: var(--primary); color: var(--primary); background: rgba(0,0,0,0.02); }
        .btn-save:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-save.saved { border-color: #16a34a; color: #16a34a; }
        
        .workspace { display: flex; flex: 1; overflow: hidden; }
        .sidebar-left { width: 240px; background: var(--bg-panel); border-right: 1px solid var(--border); display: flex; flex-direction: column; flex-shrink: 0; }
        
        .cover-thumbnail-wrapper {
            padding: 12px; border-bottom: 1px solid var(--border); cursor: pointer; transition: background 0.15s;
        }
        .cover-thumbnail-wrapper:hover { background: #f8fafc; }
        .cover-thumbnail-wrapper.active { background: #f1f5f9; border-left: 3px solid var(--primary); }
        .cover-thumbnail {
            width: 100%; height: 80px; background: #f8fafc; border: 1px dashed #cbd5e1;
            border-radius: 6px; display: flex; align-items: center; justify-content: center;
            overflow: hidden; margin-bottom: 6px; transition: all 0.15s;
        }
        .cover-thumbnail svg { width: 80%; height: 80%; display: block; }
        .cover-label { font-size: 11px; font-weight: 600; color: var(--text-muted); text-align: center; text-transform: uppercase; letter-spacing: 0.03em; }

        .sidebar-header { padding: 16px; border-bottom: 1px solid var(--border); font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em; display: flex; justify-content: space-between; align-items: center; }
        .btn-add-card { background: none; border: none; color: var(--primary); font-size: 20px; cursor: pointer; padding: 0 4px; line-height: 1; }
        .btn-add-card:hover { color: var(--primary-hover); }
        
        .thumbnails-list { flex: 1; overflow-y: auto; padding: 12px; display: flex; flex-direction: column; gap: 8px; }
        .thumbnail { 
            padding: 12px; border: 1px solid var(--border); border-radius: 8px; background: #fff; 
            cursor: grab;
            transition: all 0.15s ease; position: relative; 
        }
        .thumbnail:hover { border-color: #94a3b8; }
        .thumbnail.active { border-color: var(--primary); background: #f8fafc; box-shadow: 0 0 0 1px var(--primary); }
        .thumbnail.sortable-ghost { opacity: 0.4; background: #f1f5f9; border: 1px dashed #94a3b8; }
        
        .thumb-number { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px; }
        .thumb-preview { font-size: 13px; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .thumb-delete { position: absolute; top: 8px; right: 8px; background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 16px; opacity: 0; transition: opacity 0.15s; z-index: 2; }
        .thumbnail:hover .thumb-delete { opacity: 1; }
        .thumb-delete:hover { color: var(--danger); }
        
        .canvas-area { flex: 1; background: var(--bg-app); overflow-y: auto; padding: 32px; display: flex; justify-content: center; align-items: flex-start; }
        .canvas-sheet { width: 100%; max-width: 800px; background: #fff; border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); min-height: 400px; padding: 32px; height: fit-content; }
        .canvas-empty { text-align: center; padding: 60px 20px; color: var(--text-muted); border: 2px dashed var(--border); border-radius: 8px; }
        .canvas-toolbar { display: flex; justify-content: center; gap: 4px; margin-bottom: 20px; background: var(--bg-app); padding: 4px; border-radius: 8px; width: fit-content; margin-left: auto; margin-right: auto; }
        .toolbar-btn { padding: 6px 16px; font-size: 13px; font-weight: 600; color: var(--text-muted); background: transparent; border: none; border-radius: 6px; cursor: pointer; transition: all 0.15s ease; display: flex; align-items: center; gap: 6px; }
        .toolbar-btn:hover { color: var(--text-main); background: rgba(0,0,0,0.05); }
        .toolbar-btn.active { background: #fff; color: var(--primary); box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        
        #preview-container { display: none; width: 100%; }
        #preview-container .lesson-player-wrapper { max-width: 100%; }
        
        .canvas-block { border: 1px solid transparent; border-radius: 8px; padding: 16px; margin-bottom: 16px; position: relative; transition: all 0.15s ease; background: #fff; }
        .canvas-block:hover { border-color: #cbd5e1; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .canvas-block.sortable-ghost { opacity: 0.4; background: #f1f5f9; border: 1px dashed #94a3b8; }
        .block-ui-bar { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #f1f5f9; }
        .drag-handle { cursor: grab; color: #cbd5e1; font-size: 18px; padding: 0 4px; user-select: none; }
        .drag-handle:active { cursor: grabbing; color: var(--text-muted); }
        .block-type-badge { font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em; flex: 1; }
        .btn-delete-block { background: none; border: none; color: #cbd5e1; font-size: 18px; cursor: pointer; padding: 0 4px; opacity: 0; transition: all 0.15s; }
        .canvas-block:hover .btn-delete-block { opacity: 1; }
        .btn-delete-block:hover { color: var(--danger); }
        .block-fields { display: flex; flex-direction: column; gap: 10px; }
        .block-fields input, .block-fields textarea, .block-fields select { width: 100%; padding: 8px 12px; font-size: 14px; border: 1px solid var(--border); border-radius: 6px; background: #fff; color: var(--text-main); outline: none; font-family: inherit; transition: border-color 0.15s; }
        .block-fields input:focus, .block-fields textarea:focus, .block-fields select:focus { border-color: var(--primary); }
        .block-fields textarea { resize: vertical; min-height: 70px; }
        .field-row { display: flex; gap: 12px; }
        .field-row > * { flex: 1; }
        .checkbox-label { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-main); cursor: pointer; }
        .checkbox-label input { width: auto; accent-color: var(--primary); }
        .staffwidth-control { display: flex; align-items: center; gap: 12px; margin-top: 8px; }
        .staffwidth-control label { font-size: 12px; color: var(--text-muted); font-weight: 600; min-width: 80px; }
        .staffwidth-control input[type="range"] { flex: 1; height: 6px; cursor: pointer; }
        .staffwidth-control .value-display { min-width: 50px; text-align: right; font-size: 13px; font-weight: 600; color: var(--primary); font-family: "Courier New", monospace; }
        .css-art-presets { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 10px; }
        .css-art-preset-tag { padding: 5px 12px; font-size: 12px; font-weight: 500; color: var(--text-muted); background: var(--bg-app); border: 1px solid var(--border); border-radius: 16px; cursor: pointer; transition: all 0.15s ease; outline: none; }
        .css-art-preset-tag:hover { border-color: var(--primary); color: var(--primary); background: #fff; }
        .css-art-preset-tag:active { transform: scale(0.96); background: #e3f2fd; }
        
        .sidebar-right { width: 260px; background: var(--bg-panel); border-left: 1px solid var(--border); display: flex; flex-direction: column; flex-shrink: 0; }
        .palette-list { padding: 16px; display: flex; flex-direction: column; gap: 8px; }
        .palette-item { padding: 12px 16px; background: #fff; border: 1px solid var(--border); border-radius: 8px; cursor: grab; display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 500; color: var(--text-main); transition: all 0.15s ease; }
        .palette-item:hover { border-color: var(--primary); box-shadow: 0 2px 4px rgba(0,0,0,0.05); transform: translateY(-1px); }
        .palette-item:active { cursor: grabbing; }
        .palette-icon { font-size: 18px; }
        
        .toast { position: fixed; bottom: 32px; left: 50%; transform: translateX(-50%) translateY(100px); background: var(--primary); color: #fff; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 500; box-shadow: 0 4px 12px rgba(0,0,0,0.15); opacity: 0; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 100; }
        .toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
        .etude-generator { margin-top: 12px; padding: 10px; background: #f8fafc; border: 1px solid var(--border); border-radius: 6px; }
        .etude-generator-title { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.05em; }
        .etude-icons-row { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 10px; align-items: center; }
        .etude-icon-label { display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border: 1px solid var(--border); border-radius: 4px; background: #fff; cursor: pointer; transition: all 0.15s; position: relative; }
        .etude-icon-label:hover { border-color: var(--primary); background: #f1f5f9; }
        .etude-icon-label input { position: absolute; opacity: 0; width: 0; height: 0; }
        .etude-icon-label.selected { border-color: var(--primary); background: #e0f2fe; box-shadow: 0 0 0 1px var(--primary); }
        .etude-icon-label img { height: 24px; width: auto; opacity: 0.4; transition: opacity 0.15s; }
        .etude-icon-label.selected img { opacity: 1; }
        .etude-controls-row { display: flex; gap: 10px; align-items: center; }
        .etude-input-group { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-muted); }
        .etude-input-group input { width: 50px; padding: 4px 6px; font-size: 12px; text-align: center; border: 1px solid var(--border); border-radius: 4px; }
        .btn-generate { margin-left: auto; padding: 6px 12px; font-size: 12px; font-weight: 600; background: var(--primary); color: #fff; border: none; border-radius: 4px; cursor: pointer; display: flex; align-items: center; gap: 4px; transition: background 0.15s; }
        .btn-generate:hover { background: var(--primary-hover); }
    </style>
</head>
<body>
    <?php require_once $base . '/includes/header.php'; ?>
    <div class="builder-app-wrapper">
        <header class="builder-header">
            <a href="/workspace/" class="back-to-workspace">← В Рабочее пространство</a>
            <div class="app-title">DLE</div>
            <input type="text" class="lesson-title-input" id="lesson-title-input" placeholder="Название урока" oninput="updateLessonTitle(this.value)">
            <button class="btn-save" id="btn-save-db" onclick="saveLessonToDB()">
                <span>💾</span> <span id="save-db-text">Сохранить</span>
            </button>
        </header>
        <div class="workspace">
            <div class="sidebar-left">
                <div class="cover-thumbnail-wrapper" id="coverThumbnailWrapper" onclick="setMode('cover')" title="Редактировать обложку урока">
                    <div class="cover-thumbnail" id="coverThumbnail">
                        <span style="font-size: 24px; color: #cbd5e1;">🖼️</span>
                    </div>
                    <div class="cover-label">Обложка урока</div>
                </div>
                
                <div class="sidebar-header" id="cardsSidebarHeader">
                    <span>Карточки</span>
                    <button class="btn-add-card" onclick="addCard()" title="Добавить карточку">+</button>
                </div>
                <div class="thumbnails-list" id="thumbnails-list"></div>
            </div>
            
            <div class="canvas-area">
                <div class="canvas-sheet" id="canvas-sheet">
                    <div class="canvas-toolbar" id="canvasToolbar"></div>
                    <div id="canvas-blocks"></div>
                    <div id="preview-container"></div>
                </div>
            </div>
            
            <div class="sidebar-right" id="sidebarRight">
                <div class="sidebar-header"><span>Палитра блоков</span></div>
                <div class="palette-list" id="palette-list">
                    <div class="palette-item" data-type="text"><span class="palette-icon">📝</span> Текст</div>
                    <div class="palette-item" data-type="notation"><span class="palette-icon">🎼</span> Ноты (ABC)</div>
                    <div class="palette-item" data-type="css-art"><span class="palette-icon">🎨</span> CSS-арт</div>
                    <div class="palette-item" data-type="svg-art"><span class="palette-icon">🖼️</span> SVG-арт</div>
					<div class="palette-item" data-type="animation"><span class="palette-icon">🎬</span> Анимация</div>
                    <div class="palette-item" data-type="grid"><span class="palette-icon">🎹</span> Сетка</div>
                    <div class="palette-item" data-type="drumkit-mini"><span class="palette-icon">🥁</span> Установка (Мини)</div>
                    <div class="palette-item" data-type="drumkit-real"><span class="palette-icon">🥁</span> Установка (Реал)</div>
                </div>
            </div>
        </div>
    </div>
    <div class="toast" id="toast">Сохранено!</div>

    <script src="components/registry.js"></script>
    <script src="LessonEngine.js"></script>
    <script src="components/blocks/grid.js?v=4"></script>
    <script src="components/blocks/css-art.js"></script>
    <script src="components/blocks/svg-art.js"></script>
    <script>
        let lessonData = { title: "Новый урок", illustration: { blocks: [] }, cards: [{ blocks: [] }] };
        let selectedCardIndex = 0;
        let canvasSortable = null;
        let paletteSortable = null;
        let cardsSortable = null;
        let currentMode = 'edit'; 
        let previewPlayerInstance = null;

        // === НОВОЕ: Палитра цветов для карточек (null = дефолтный, далее 3 мягких цвета) ===
        const CARD_COLORS = [null, '#dbeafe', '#dcfce7', '#fef3c7']; // Дефолт, мягкий синий, мягкий зелёный, мягкий жёлтый

        function cycleCardColor(index) {
            const currentColor = lessonData.cards[index].color || null;
            let currentIndex = CARD_COLORS.indexOf(currentColor);
            if (currentIndex === -1) currentIndex = 0; // Защита на случай некорректных данных
            
            const nextIndex = (currentIndex + 1) % CARD_COLORS.length;
            const newColor = CARD_COLORS[nextIndex];
            
            // Чистим JSON от лишних null-значений для экономии места в БД
            if (newColor === null) {
                delete lessonData.cards[index].color;
            } else {
                lessonData.cards[index].color = newColor;
            }
            
            renderThumbnails();
            updateDSL();
        }

        function getCurrentBlocks() {
            return (currentMode === 'cover' || currentMode === 'preview_cover') 
                ? lessonData.illustration.blocks 
                : lessonData.cards[selectedCardIndex].blocks;
        }

        function setMode(mode) {
            currentMode = mode;
            const toolbar = document.getElementById('canvasToolbar');
            const canvasBlocks = document.getElementById('canvas-blocks');
            const previewContainer = document.getElementById('preview-container');
            const sidebarRight = document.getElementById('sidebarRight');
            const coverWrapper = document.getElementById('coverThumbnailWrapper');

            coverWrapper.classList.remove('active');

            if (mode === 'edit') {
                toolbar.innerHTML = `
                    <button class="toolbar-btn active">✏️ Редактор</button>
                    <button class="toolbar-btn" onclick="setMode('preview')">👁 Предпросмотр</button>
                `;
                canvasBlocks.style.display = 'block';
                previewContainer.style.display = 'none';
                sidebarRight.style.display = 'flex';
                if (previewPlayerInstance) {
                    if (previewPlayerInstance.currentEngine) previewPlayerInstance.currentEngine.destroy();
                    previewPlayerInstance = null;
                }
                previewContainer.innerHTML = '';
                renderActiveArea();
            } 
            else if (mode === 'cover') {
                coverWrapper.classList.add('active');
                toolbar.innerHTML = `
                    <button class="toolbar-btn active">✏️ Редактор</button>
                    <button class="toolbar-btn" onclick="setMode('preview_cover')">👁 Предпросмотр</button>
                `;
                canvasBlocks.style.display = 'block';
                previewContainer.style.display = 'none';
                sidebarRight.style.display = 'flex';
                renderActiveArea();
            } 
            else if (mode === 'preview') {
                toolbar.innerHTML = `
                    <button class="toolbar-btn" onclick="setMode('edit')">✏️ Редактор</button>
                    <button class="toolbar-btn active">👁 Предпросмотр</button>
                `;
                canvasBlocks.style.display = 'none';
                previewContainer.style.display = 'block';
                sidebarRight.style.display = 'flex';
                previewContainer.innerHTML = '';
                setTimeout(() => {
                    try {
                        previewPlayerInstance = new LessonPlayer('preview-container', lessonData);
                        previewPlayerInstance.loadCard(selectedCardIndex);
                    } catch (e) {
                        previewContainer.innerHTML = `<div class="canvas-empty" style="color: var(--danger);">Ошибка рендера: ${e.message}</div>`;
                    }
                }, 50);
            }
            else if (mode === 'preview_cover') {
                toolbar.innerHTML = `
                    <button class="toolbar-btn" onclick="setMode('cover')">✏️ Редактор</button>
                    <button class="toolbar-btn active">👁 Предпросмотр</button>
                `;
                canvasBlocks.style.display = 'none';
                previewContainer.style.display = 'block';
                sidebarRight.style.display = 'flex';
                previewContainer.innerHTML = '';
                setTimeout(() => {
                    try {
                        const coverLesson = { title: lessonData.title, cards: [lessonData.illustration] };
                        previewPlayerInstance = new LessonPlayer('preview-container', coverLesson);
                        previewPlayerInstance.loadCard(0);
                    } catch (e) {
                        previewContainer.innerHTML = `<div class="canvas-empty" style="color: var(--danger);">Ошибка рендера: ${e.message}</div>`;
                    }
                }, 50);
            }
        }

        function renderActiveArea() {
            const container = document.getElementById('canvas-blocks');
            container.innerHTML = '';
            const blocks = getCurrentBlocks();
            
            if (blocks.length === 0) {
                const msg = (currentMode === 'cover' || currentMode === 'preview_cover') 
                    ? 'Перетащите блоки сюда, чтобы создать обложку' 
                    : 'Перетащите блоки сюда из палитры справа';
                container.innerHTML = `<div class="canvas-empty">${msg}</div>`;
                return;
            }

            blocks.forEach((block, index) => {
                const el = document.createElement('div');
                el.className = 'canvas-block';
                el.setAttribute('data-index', index);
                let fieldsHTML = '';
                
                if (block.type === 'text') {
                    fieldsHTML = `
                        <div class="field-row" style="margin-bottom: 8px;">
                            <label class="checkbox-label"><input type="radio" name="variant-${index}" value="heading" ${block.variant === 'heading' ? 'checked' : ''} onchange="updateBlock(${index}, 'variant', 'value')"> Заголовок</label>
                            <label class="checkbox-label"><input type="radio" name="variant-${index}" value="paragraph" ${block.variant === 'paragraph' ? 'checked' : ''} onchange="updateBlock(${index}, 'variant', 'value')"> Параграф</label>
                        </div>
                        ${block.variant === 'heading' 
                            ? `<input type="text" value="${block.text || ''}" oninput="updateBlock(${index}, 'text', this.value)" placeholder="Текст заголовка">`
                            : `<textarea oninput="updateBlock(${index}, 'text', this.value)" placeholder="Введите текст...">${block.text || ''}</textarea>`
                        }
                    `;
                } else if (block.type === 'grid') {
                    const showHH = String(block.show_hh || 'true') === 'true';
                    const showSN = String(block.show_snare || 'true') === 'true';
                    const showKT = String(block.show_kick || 'true') === 'true';
                    const isExercise = String(block.showCheck || 'false') === 'true';
                    const renderBuilderGridRow = (trackId, trackName, symbol, currentPattern, isShown) => {
                        const cleanPattern = currentPattern.replace(/^\||\|$/g, '').padEnd(16, '-').slice(0, 16);
                        let cellsHTML = '';
                        for (let i = 0; i < 16; i++) {
                            const isActive = cleanPattern[i] === symbol || cleanPattern[i] === 'o' || cleanPattern[i] === 'x';
                            const isBeatStart = i % 4 === 0;
                            const activeClass = isActive ? 'active' : '';
                            if (isBeatStart) cellsHTML += `<div class="le-beat-group">`;
                            cellsHTML += `<div class="le-step ${activeClass}" data-track="${trackId}" data-index="${i}" data-symbol="${symbol}" onclick="toggleBuilderGridCell(this, ${index})"></div>`;
                            if (i % 4 === 3) cellsHTML += `</div>`;
                        }
                        const stepsClass = isShown ? 'le-track-steps' : 'le-track-steps disabled';
                        return `<div class="le-track-row" style="opacity: ${isShown ? '1' : '0.4'};"><label class="le-track-name"><input type="checkbox" ${isShown ? 'checked' : ''} onchange="toggleGridTrackVisibility(${index}, '${trackId}', this.checked)" style="pointer-events: auto; width: auto;">${trackName}</label><div class="${stepsClass}">${cellsHTML}</div></div>`;
                    };
                    let notationPreviewHTML = '';
                    if (typeof GrooveUtils !== 'undefined') {
                        try {
                            const gu = new GrooveUtils();
                            const grooveData = new gu.grooveDataNew();
                            grooveData.timeDivision = 16; grooveData.tempo = 90; grooveData.numberOfMeasures = 1;
                            const hhData = showHH ? (block.target_hh || '|----------------|') : '|----------------|';
                            const snData = showSN ? (block.target_snare || '|----------------|') : '|----------------|';
                            const ktData = showKT ? (block.target_kick || '|----------------|') : '|----------------|';
                            grooveData.hh_array = gu.noteArraysFromURLData('H', hhData, 16, 1);
                            grooveData.snare_array = gu.noteArraysFromURLData('S', snData, 16, 1);
                            grooveData.kick_array = gu.noteArraysFromURLData('K', ktData, 16, 1);
                            const abc = gu.createABCFromGrooveData(grooveData, 600);
                            notationPreviewHTML = `<div style="margin-top: 16px; padding: 12px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px;"><div style="font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Предпросмотр нот</div><div class="le-notation builder-notation-preview" style="overflow-x: auto;">${gu.renderABCtoSVG(abc).svg}</div></div>`;
                        } catch(e) { console.warn("Не удалось сгенерировать предпросмотр нот:", e); }
                    }
                    fieldsHTML = `<div style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0;"><div style="font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.05em;">Визуальный редактор ритма</div>${renderBuilderGridRow('hh', 'HH', 'x', block.target_hh || '|----------------|', showHH)}${renderBuilderGridRow('snare', 'SN', 'o', block.target_snare || '|----------------|', showSN)}${renderBuilderGridRow('kick', 'KT', 'o', block.target_kick || '|----------------|', showKT)}</div>${notationPreviewHTML}<div style="margin-top: 16px; display: flex; gap: 12px; align-items: center; padding: 12px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px;"><label class="checkbox-label" style="font-weight: 600; font-size: 14px;"><input type="checkbox" ${isExercise ? 'checked' : ''} onchange="updateBlock(${index}, 'showCheck', this.checked ? 'true' : 'false')">Режим упражнения (скрыть расстановку, показать кнопку "Проверить")</label></div>`;
                
                } else if (block.type === 'notation') {
                    const isManualMode = block.notation_mode === 'manual';
                    const currentAbc = block.abc || '';
                    const measures = block.generator_measures || 1;
                    
                    const notePatterns = [
                        { id: 'quarter', pattern: 'c4', icon: '4n.png', label: 'Четверть' },
                        { id: 'eighth_pair', pattern: 'c2c2', icon: '8n.png', label: 'Две восьмые' },
                        { id: 'sixteenth_quartet', pattern: 'cccc', icon: '16n.png', label: 'Четыре шестнадцатые' },
                        { id: 'sixteenth_pair_eighth', pattern: 'ccc2', icon: 'ccc2.png', label: '2шестн+восьм' },
                        { id: 'eighth_sixteenth_pair', pattern: 'c2cc', icon: 'c2cc.png', label: 'восьм+2шестн' },
                        { id: 'sixteenth_eighth_sixteenth', pattern: 'cc2c', icon: 'cc2c.png', label: 'шестн+восьм+шестн' },
                        { id: 'eighth_dot_sixteenth', pattern: 'c3c', icon: 'c3cn.png', label: 'пунктир' },
                        { id: 'z2c2', pattern: 'z2c2', icon: 'z2c2.png', label: 'синкопа' },
                        { id: 'z4', pattern: 'z4', icon: 'z4.png', label: 'Пауза' }
                    ];
                    
                    const generatorIconsHTML = notePatterns.map(p => {
                        const isSelected = block.generator_groups 
                            ? block.generator_groups.includes(p.id) 
                            : (p.id === 'quarter' || p.id === 'eighth_pair');
                        return `
                            <label class="etude-icon-label ${isSelected ? 'selected' : ''}" title="${p.label}" onclick="toggleEtudeIcon(this, '${p.id}')">
                                <input type="checkbox" value="${p.id}" ${isSelected ? 'checked' : ''}>
                                <img src="components/blocks/pic/${p.icon}" alt="${p.id}">
                            </label>
                        `;
                    }).join('');

                    const manualIconsHTML = notePatterns.map(p => `
                        <label class="etude-icon-label" title="${p.label}" onclick="addManualNote(${index}, '${p.pattern}')">
                            <img src="components/blocks/pic/${p.icon}" alt="${p.id}">
                        </label>
                    `).join('');

                    const tabsHTML = `
                        <div style="display: flex; gap: 0; margin-bottom: 12px; border-bottom: 1px solid var(--border);">
                            <button class="notation-tab" onclick="switchNotationMode(${index}, 'manual')" style="padding: 6px 12px; font-size: 12px; font-weight: 600; border: none; background: transparent; color: ${isManualMode ? 'var(--primary)' : 'var(--text-muted)'}; border-bottom: 2px solid ${isManualMode ? 'var(--primary)' : 'transparent'}; cursor: pointer; transition: all 0.15s;">Ручной ввод</button>
                            <button class="notation-tab" onclick="switchNotationMode(${index}, 'generator')" style="padding: 6px 12px; font-size: 12px; font-weight: 600; border: none; background: transparent; color: ${!isManualMode ? 'var(--primary)' : 'var(--text-muted)'}; border-bottom: 2px solid ${!isManualMode ? 'var(--primary)' : 'transparent'}; cursor: pointer; transition: all 0.15s;">Генератор</button>
                        </div>
                    `;

                    const generatorHTML = `
                        <div class="etude-generator">
                            <div class="etude-generator-title">Генератор этюда</div>
                            <div class="etude-icons-row" id="etude-icons-${index}">${generatorIconsHTML}</div>
                            <div class="etude-controls-row" style="flex-wrap: wrap; gap: 12px;">
                                <div class="etude-input-group">
                                    <span>Тактов:</span>
                                    <input type="number" id="etude-measures-${index}" value="${measures}" min="1" max="32" onchange="updateBlock(${index}, 'generator_measures', this.value)">
                                </div>
                                <label class="checkbox-label" style="font-size: 12px; margin: 0;">
                                    <input type="checkbox" id="etude-wrap-${index}" ${block.generator_wrap === 'true' ? 'checked' : ''} onchange="updateBlock(${index}, 'generator_wrap', this.checked ? 'true' : 'false')">
                                    Переносить такты
                                </label>
                                <label class="checkbox-label" style="font-size: 12px; margin: 0;">
                                    <input type="checkbox" id="etude-append-${index}" ${block.generator_append === 'true' ? 'checked' : ''} onchange="updateBlock(${index}, 'generator_append', this.checked ? 'true' : 'false')">
                                    Добавлять
                                </label>
                                <button class="btn-generate" onclick="generateEtude(${index})" style="margin-left: auto;">🎲 Сгенерировать</button>
                                <button onclick="clearManualNotation(${index})" style="padding: 6px 12px; font-size: 11px; background: #fff; border: 1px solid var(--border); border-radius: 4px; cursor: pointer; color: var(--text-muted);">🧹 Очистить</button>
                            </div>
                        </div>
                    `;

                    const manualHTML = `
                        <div style="padding: 12px; background: #f8fafc; border-radius: 8px; border: 1px solid var(--border);">
                            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Ручной ввод</div>
                            <div class="etude-icons-row" style="margin-bottom: 12px;">
                                ${manualIconsHTML}
                                <label class="etude-icon-label" title="Тактовая черта" onclick="addBarLine(${index})" style="font-size: 20px; font-weight: 700; color: var(--primary);">|</label>
                            </div>
                            <button onclick="clearManualNotation(${index})" style="padding: 4px 10px; font-size: 11px; background: #fff; border: 1px solid var(--border); border-radius: 4px; cursor: pointer; color: var(--text-muted);">🧹 Очистить</button>
                        </div>
                    `;

                    fieldsHTML = `
                        ${tabsHTML}
                        ${isManualMode ? manualHTML : generatorHTML}
                        <textarea id="abc-textarea-${index}" oninput="updateBlock(${index}, 'abc', this.value)" placeholder="ABC нотация (можно редактировать вручную)" style="min-height: 80px; margin-top: 12px;">${currentAbc}</textarea>
                        <div class="staffwidth-control">
                            <label>Ширина стана:</label>
                            <input type="range" min="300" max="1200" step="50" value="${block.staffwidth || '600'}" oninput="updateStaffwidth(${index}, this.value)">
                            <span class="value-display" id="staffwidth-display-${index}">${block.staffwidth || '600'}px</span>
                        </div>
						
						
						
						
						<label class="checkbox-label" style="margin-top: 16px; padding: 12px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px;">
							<input type="checkbox" 
								   ${block.enable_audio_highlight === 'true' ? 'checked' : ''} 
								   onchange="updateBlock(${index}, 'enable_audio_highlight', this.checked ? 'true' : 'false')">
							<span style="font-weight: 600;">Включить аудио-плеер и подсветку нот</span>
						</label>
						
						<div style="margin-top: 12px; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
							<div style="font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Настройки воспроизведения</div>
							<label class="checkbox-label" style="margin-bottom: 8px;">
								<input type="checkbox" 
									   ${block.show_cursor === 'true' ? 'checked' : ''} 
									   onchange="updateBlock(${index}, 'show_cursor', this.checked ? 'true' : 'false')">
								<span>Показывать курсор</span>
							</label>
							<label class="checkbox-label">
								<input type="checkbox" 
									   ${block.show_highlight === 'true' ? 'checked' : ''} 
									   onchange="updateBlock(${index}, 'show_highlight', this.checked ? 'true' : 'false')">
								<span>Подсвечивать ноты</span>
							</label>
						</div>						
						
						
                    `;
                } else if (block.type === 'drumkit-mini' || block.type === 'drumkit-real') {
                    fieldsHTML = `<div style="color: var(--text-muted); font-size: 13px; padding: 8px 0;">Настройки установки задаются в коде компонента. Блок готов к использованию.</div>`;
                } else if (block.type === 'css-art') {
                    const presets = window.CssArtPresets || [];
                    const validPresets = presets.filter(p => p.code && p.code.trim() !== '');
                    const tagsHTML = validPresets.map((preset) => `<button type="button" class="css-art-preset-tag" onclick="applyArtPreset(${index}, ${presets.indexOf(preset)}, 'css')">${preset.name}</button>`).join('');
                    fieldsHTML = `<label style="font-size: 12px; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 4px; margin-top: 12px;">HTML / CSS / JS код иллюстрации:</label><textarea oninput="updateBlock(${index}, 'code', this.value)" placeholder="Вставьте сюда код или нажмите на шаблон выше" style="min-height: 200px; font-family: monospace; font-size: 12px; line-height: 1.5;">${block.code || ''}</textarea><label style="font-size: 12px; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 6px;">Быстрые шаблоны:</label><div class="css-art-presets">${tagsHTML}</div>`;
                } else if (block.type === 'svg-art') {
                    const presets = window.SvgArtPresets || [];
                    const validPresets = presets.filter(p => p.code && p.code.trim() !== '');
                    const tagsHTML = validPresets.map((preset) => `<button type="button" class="css-art-preset-tag" onclick="applyArtPreset(${index}, ${presets.indexOf(preset)}, 'svg')">${preset.name}</button>`).join('');
                    fieldsHTML = `<label style="font-size: 12px; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 4px; margin-top: 12px;">SVG-код иллюстрации:</label><textarea oninput="updateBlock(${index}, 'code', this.value)" placeholder="Вставьте сюда SVG-код или нажмите на шаблон выше" style="min-height: 200px; font-family: monospace; font-size: 12px; line-height: 1.5;">${block.code || ''}</textarea><label style="font-size: 12px; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 6px;">Быстрые шаблоны:</label><div class="css-art-presets">${tagsHTML}</div>`;
                               } else if (block.type === 'animation') {
                    const seqStr = block.sequence || "";
                    const seq = seqStr ? seqStr.split(',') : [];
                    
                    const tagsHTML = seq.map((note, idx) => `
                        <span style="display:inline-flex; align-items:center; gap:4px; padding:4px 8px; background:#e0f2fe; border:1px solid #bae6fd; border-radius:12px; font-size:12px; margin:2px;">
                            ${note === 'quarter' ? 'Четверть' : 'Восьмая'}
                            <span style="cursor:pointer; color:#ef4444; font-weight:bold;" onclick="removeAnimNote(${index}, ${idx})">×</span>
                        </span>
                    `).join('');

                    fieldsHTML = `
                        <div style="display:flex; gap:12px; margin-bottom:12px;">
                            <div style="flex:1;">
                                <label style="font-size:12px; color:var(--text-muted); font-weight:600; display:block; margin-bottom:4px;">Темп (BPM)</label>
                                <input type="number" value="${block.bpm || 100}" min="40" max="200" onchange="updateBlock(${index}, 'bpm', this.value)">
                            </div>
                        </div>
                        <div style="margin-bottom:12px;">
                            <label style="font-size:12px; color:var(--text-muted); font-weight:600; display:block; margin-bottom:6px;">Добавить ноту:</label>
                            <div style="display:flex; gap:6px;">
                                <button type="button" class="le-btn" style="padding:6px 12px; font-size:12px;" onclick="addAnimNote(${index}, 'quarter')">+ Четверть</button>
                                <button type="button" class="le-btn" style="padding:6px 12px; font-size:12px;" onclick="addAnimNote(${index}, 'eighth')">+ Восьмая</button>
                            </div>
                        </div>
                        <div style="padding:12px; background:#f8fafc; border:1px solid var(--border); border-radius:6px; min-height:40px;">
                            ${tagsHTML || '<span style="color:#94a3b8; font-size:12px;">Последовательность пуста</span>'}
                        </div>
                    `;
                }

                const typeName = { 'text': 'Текст', 'grid': 'Сетка', 'notation': 'Ноты ABC', 'drumkit-mini': 'Установка (Мини)', 'drumkit-real': 'Установка (Реал)', 'css-art': 'CSS-арт', 'svg-art': 'SVG-арт', 'animation': 'Анимация' }[block.type] || block.type;
                el.innerHTML = `<div class="block-ui-bar"><span class="drag-handle">⋮⋮</span><span class="block-type-badge">${typeName}</span><button class="btn-delete-block" onclick="removeBlock(${index})" title="Удалить блок">×</button></div><div class="block-fields">${fieldsHTML}</div>`;
                container.appendChild(el);
            });
        }

        function renderCoverThumbnail() {
            const thumb = document.getElementById('coverThumbnail');
            let artCode = '';
            if (lessonData.illustration && Array.isArray(lessonData.illustration.blocks)) {
                for (const block of lessonData.illustration.blocks) {
                    if ((block.type === 'svg-art' || block.type === 'css-art') && block.code) {
                        artCode = block.code;
                        break;
                    }
                }
            }
            if (artCode && artCode.trim().toLowerCase().includes('<svg')) {
                thumb.innerHTML = artCode;
                thumb.style.border = '1px solid var(--border)';
                thumb.style.background = '#fff';
            } else {
                thumb.innerHTML = '<span style="font-size: 24px; color: #cbd5e1;">🖼️</span>';
                thumb.style.border = '1px dashed #cbd5e1';
                thumb.style.background = '#f8fafc';
            }
        }

        function renderThumbnails() {
            const list = document.getElementById('thumbnails-list');
            list.innerHTML = '';
            lessonData.cards.forEach((card, index) => {
                const div = document.createElement('div');
                const isActive = index === selectedCardIndex && currentMode !== 'cover' && currentMode !== 'preview_cover';
                div.className = `thumbnail ${isActive ? 'active' : ''}`;
                
                // === НОВОЕ: Применяем цвет карточки, если он задан ===
                if (card.color) {
                    div.style.backgroundColor = card.color;
                } else {
                    div.style.backgroundColor = ''; // Сброс к CSS-значению по умолчанию
                }

                // === НОВОЕ: Обработка правого клика (ПКМ) ===
                div.oncontextmenu = (e) => {
                    e.preventDefault(); // Запрещаем стандартное контекстное меню браузера
                    cycleCardColor(index);
                };

                div.onclick = (e) => {
                    if (!e.target.classList.contains('thumb-delete')) {
                        selectedCardIndex = index;
                        if (currentMode === 'preview' && previewPlayerInstance) {
                            previewPlayerInstance.loadCard(selectedCardIndex);
                            const listEl = document.getElementById('thumbnails-list');
                            listEl.querySelectorAll('.thumbnail').forEach((thumb, i) => {
                                thumb.classList.toggle('active', i === selectedCardIndex);
                            });
                        } else {
                            setMode('edit');
                            renderAll();
                        }
                    }
                };
                
                let previewText = 'Пустая карточка';
                if (card.blocks.length > 0) {
                    const blockNames = card.blocks.map(b => {
                        switch(b.type) {
                            case 'text': return 'Текст'; case 'notation': return 'Ноты'; case 'grid': return 'Сетка';
                            case 'drumkit-mini': return 'Уст. (Мини)'; case 'drumkit-real': return 'Уст. (Реал)';
                            case 'css-art': return 'CSS-арт'; case 'svg-art': return 'SVG-арт'; default: return 'Блок';
                        }
                    });
                    previewText = blockNames.join(', ');
                }
                
                div.innerHTML = `
                    <div class="thumb-number">Карточка ${index + 1}</div>
                    <div class="thumb-preview">${previewText}</div>
                    ${lessonData.cards.length > 1 ? `<button class="thumb-delete" onclick="removeCard(${index}, event)" title="Удалить карточку">×</button>` : ''}
                `;
                list.appendChild(div);
            });
        }

        function renderAll() { 
            renderThumbnails(); 
            renderActiveArea(); 
            updateDSL(); 
        }

        const urlParams = new URLSearchParams(window.location.search);
        const lessonIdFromUrl = urlParams.get('lesson_id');

        document.addEventListener('DOMContentLoaded', async () => {
            if (lessonIdFromUrl) {
                await loadLessonFromDB(lessonIdFromUrl);
            } else {
                document.getElementById('lesson-title-input').value = lessonData.title;
                initSortable();
                renderAll();
                renderCoverThumbnail();
                setMode('edit');
            }
        });

        async function loadLessonFromDB(id) {
            try {
                const res = await fetch(`/workspace/lessons/api.php?action=get&id=${id}`);
                const lesson = await res.json();
                if (lesson.error) { alert('Ошибка: ' + lesson.error); window.location.href = '/workspace'; return; }

                lessonData = JSON.parse(lesson.data);
                if (!lessonData.illustration || !Array.isArray(lessonData.illustration.blocks)) {
                    lessonData.illustration = { blocks: [] };
                }
                lessonData.id = lesson.id;
                
                document.getElementById('lesson-title-input').value = lessonData.title || 'Новый урок';
                initSortable();
                renderAll();
                renderCoverThumbnail();
                setMode('edit');
            } catch (e) {
                console.error("Ошибка загрузки урока:", e);
                alert('Не удалось загрузить урок из базы данных.');
                window.location.href = '/workspace';
            }
        }

       









	  async function saveLessonToDB() {
    const btn = document.getElementById('btn-save-db');
    const text = document.getElementById('save-db-text');
    const originalText = text.textContent;
    
    // 1. Запрашиваем токен у Firebase через нашу новую глобальную функцию
    const token = await window.getFirebaseToken();
	
	
    console.log("🔍 ПЕРЕД ОТПРАВКОЙ: Тип токена:", typeof token, "Длина:", token ? token.length : 0, "Начало:", token ? token.substring(0, 30) : "НЕТ ТОКЕНА");
    
	
	
    if (!token) {
        alert('Сначала нужно войти в систему через Google!');
        return;
    }

    btn.disabled = true; 
    text.textContent = 'Сохранение...';

    try {
        // 2. Формируем полезную нагрузку, добавляя туда токен
        const payload = { 
            id: lessonData.id || 0, 
            title: lessonData.title || 'Новый урок', 
            data: lessonData,
            token: token // <-- Вот это ключевое добавление
        };
        
        const res = await fetch('/workspace/lessons/api.php?action=save', {
            method: 'POST', 
            headers: { 'Content-Type': 'application/json' }, 
            body: JSON.stringify(payload)
        });
        
        const result = await res.json();
        
        if (result.status === 'success') {
            if (!lessonData.id) lessonData.id = result.id;
            btn.classList.add('saved'); 
            text.textContent = 'Сохранено ✓';
            showToast('✅ Урок сохранён');
            renderCoverThumbnail();
            setTimeout(() => { 
                btn.classList.remove('saved'); 
                text.textContent = originalText; 
            }, 2000);
        } else {
            alert('Ошибка сервера: ' + (result.error || 'Неизвестная ошибка')); 
            text.textContent = originalText;
        }
    } catch (e) {
        console.error("Ошибка сохранения:", e); 
        alert('Ошибка сети при сохранении.'); 
        text.textContent = originalText;
    } finally {
        btn.disabled = false;
    }
}

        function initSortable() {
            paletteSortable = new Sortable(document.getElementById('palette-list'), {
                group: { name: 'blocks', pull: 'clone', put: false }, sort: false, animation: 150
            });
            
            canvasSortable = new Sortable(document.getElementById('canvas-blocks'), {
                group: 'blocks', animation: 150, handle: '.drag-handle', ghostClass: 'sortable-ghost',
                onAdd: function (evt) {
                    const type = evt.item.getAttribute('data-type');
                    if (!type) { evt.item.remove(); return; }
                    
                    evt.item.remove();
                    const newBlock = createDefaultBlock(type);
                    const blocks = getCurrentBlocks();
                    blocks.splice(evt.newIndex, 0, newBlock);
                    renderActiveArea();
                    updateDSL();
                },
                onEnd: function (evt) {
                    if (evt.from === evt.to) {
                        const blocks = getCurrentBlocks();
                        const movedItem = blocks.splice(evt.oldIndex, 1)[0];
                        blocks.splice(evt.newIndex, 0, movedItem);
                        renderActiveArea();
                        updateDSL();
                    }
                }
            });

            cardsSortable = new Sortable(document.getElementById('thumbnails-list'), {
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function (evt) {
                    const movedCard = lessonData.cards.splice(evt.oldIndex, 1)[0];
                    lessonData.cards.splice(evt.newIndex, 0, movedCard);
                    
                    if (selectedCardIndex === evt.oldIndex) {
                        selectedCardIndex = evt.newIndex;
                    } else if (selectedCardIndex > evt.oldIndex && selectedCardIndex <= evt.newIndex) {
                        selectedCardIndex--;
                    } else if (selectedCardIndex < evt.oldIndex && selectedCardIndex >= evt.newIndex) {
                        selectedCardIndex++;
                    }
                    renderThumbnails();
                    updateDSL();
                }
            });
        }

        function createDefaultBlock(type) {
            switch(type) {
                case 'text': return { type: "text", variant: "paragraph", text: "" };
                case 'css-art': return { type: "css-art", code: "" };
                case 'svg-art': return { type: "svg-art", code: "" };
                case 'grid': return { type: "grid", target_hh: "|x-x-x-x-x-x-x-x-|", target_snare: "|----o-------o---|", target_kick: "|o-------o-------|", showCheck: "false", show_hh: "true", show_snare: "true", show_kick: "true" };
                case 'notation': return { type: "notation", abc: "", staffwidth: "600" };
                case 'drumkit-mini': return { type: "drumkit-mini" };
                case 'drumkit-real': return { type: "drumkit-real" };
				case 'animation': return { type: "animation", sequence: "", bpm: 100 };
                default: return { type: "text", variant: "paragraph", text: "" };
            }
        }

        function addCard() { 
            lessonData.cards.push({ blocks: [] }); 
            selectedCardIndex = lessonData.cards.length - 1; 
            setMode('edit');
            renderAll(); 
        }
        function removeCard(index, event) {
            event.stopPropagation();
            if (lessonData.cards.length <= 1) return;
            lessonData.cards.splice(index, 1);
            if (selectedCardIndex >= lessonData.cards.length) selectedCardIndex = lessonData.cards.length - 1;
            renderAll();
        }
        function removeBlock(index) { 
            const blocks = getCurrentBlocks();
            blocks.splice(index, 1); 
            renderActiveArea(); 
            updateDSL(); 
        }

        function updateBlock(index, key, value) {
            if (key === 'variant') {
                const radios = document.getElementsByName(`variant-${index}`);
                for (const radio of radios) { if (radio.checked) { value = radio.value; break; } }
            }
            const blocks = getCurrentBlocks();
            blocks[index][key] = String(value);
            
            if ((currentMode === 'cover' || currentMode === 'preview_cover') && (key === 'code')) {
                renderCoverThumbnail();
            }
            
            updateDSL();
            if (key === 'variant' || key === 'showCheck') renderActiveArea();
        }

        function updateStaffwidth(index, value) {
            const blocks = getCurrentBlocks();
            blocks[index].staffwidth = value;
            document.getElementById(`staffwidth-display-${index}`).textContent = value + 'px';
            updateDSL();
        }

        function applyArtPreset(blockIndex, presetIndex, type) {
            const presets = type === 'css' ? (window.CssArtPresets || []) : (window.SvgArtPresets || []);
            const preset = presets[presetIndex];
            if (!preset || !preset.code) return;
            const blocks = getCurrentBlocks();
            blocks[blockIndex].code = preset.code;
            if ((currentMode === 'cover' || currentMode === 'preview_cover') && type === 'svg') {
                renderCoverThumbnail();
            }
            updateDSL(); 
            renderActiveArea();
        }

        function updateLessonTitle(value) { lessonData.title = value; updateDSL(); }

        function updateDSL() {
            let dsl = `=== LESSON ===\ntitle: ${lessonData.title}\n\n`;
            lessonData.cards.forEach((card, index) => {
                dsl += `=== CARD ${index + 1} ===\n`;
                // === НОВОЕ: Добавляем цвет в DSL, если он есть ===
                if (card.color) {
                    dsl += `color: ${card.color}\n`;
                }
                dsl += `blocks:\n`;
                card.blocks.forEach(block => {
                    dsl += `  - type: ${block.type}\n`;
                    Object.keys(block).forEach(key => {
                        if (key !== 'type' && block[key] !== undefined && block[key] !== '') {
                            let val = Array.isArray(block[key]) ? block[key].join(',') : String(block[key]);
                            val = val.replace(/\n/g, '\\n');
                            dsl += `    ${key}: ${val}\n`;
                        }
                    });
                });
                dsl += `\n`;
            });
            window.currentDSL = dsl.trim();
        }

        function parseDSL(dsl) {
            const lesson = { title: "Новый урок", illustration: { blocks: [] }, cards: [] };
            const lines = dsl.split('\n');
            let currentCard = null;
            let currentBlock = null;
            for (let i = 0; i < lines.length; i++) {
                const line = lines[i];
                if (line.startsWith('title:')) { lesson.title = line.substring(6).trim(); } 
                // === НОВОЕ: Парсим цвет карточки ===
                else if (line.startsWith('color:') && currentCard) { currentCard.color = line.substring(6).trim(); }
                else if (line.startsWith('=== CARD ')) { currentCard = { blocks: [] }; lesson.cards.push(currentCard); } 
                else if (line.trim().startsWith('- type:')) {
                    currentBlock = { type: line.trim().substring(7).trim() };
                    if (currentCard) currentCard.blocks.push(currentBlock);
                } else if (line.startsWith('    ') && currentBlock) {
                    const match = line.trim().match(/^([^:]+):\s*(.*)$/);
                    if (match) {
                        const key = match[1].trim();
                        let value = match[2].trim().replace(/\\n/g, '\n');
                        currentBlock[key] = value;
                    }
                }
            }
            if (lesson.cards.length === 0) lesson.cards.push({ blocks: [] });
            lesson.cards.forEach(card => {
                card.blocks.forEach(block => {
                    if (block.type === 'grid') {
                        if (block.show_hh === undefined) block.show_hh = 'true';
                        if (block.show_snare === undefined) block.show_snare = 'true';
                        if (block.show_kick === undefined) block.show_kick = 'true';
                        if (block.showCheck === undefined) block.showCheck = 'false';
                        if (!block.target_hh) block.target_hh = '|----------------|';
                        if (!block.target_snare) block.target_snare = '|----------------|';
                        if (!block.target_kick) block.target_kick = '|----------------|';
                    }
                });
            });
            return lesson;
        }

        function showToast(message = 'Сохранено!') {
            const toast = document.getElementById('toast'); toast.textContent = message; toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 2500);
        }
		
        window.etudeGeneratorState = {};
        window.toggleEtudeIcon = function(label, patternId) {
            const checkbox = label.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked; label.classList.toggle('selected', checkbox.checked);
        };
        
        window.generateEtude = function(blockIndex) {
            const blocks = getCurrentBlocks();
            const block = blocks[blockIndex];
            const iconContainer = document.getElementById(`etude-icons-${blockIndex}`);
            const checkboxes = iconContainer.querySelectorAll('input[type="checkbox"]:checked');
            if (checkboxes.length === 0) { showToast('⚠️ Выберите хотя бы одну группу нот!'); return; }
            const measuresCount = parseInt(document.getElementById(`etude-measures-${blockIndex}`).value) || 1;
            const wrapMeasures = document.getElementById(`etude-wrap-${blockIndex}`).checked;
            const appendMode = document.getElementById(`etude-append-${blockIndex}`).checked;
            
            block.generator_groups = Array.from(checkboxes).map(cb => cb.value);
            block.generator_measures = measuresCount; 
            block.generator_wrap = wrapMeasures ? 'true' : 'false';
            block.generator_append = appendMode ? 'true' : 'false';
            
            const patternMap = {
                'quarter': 'c4', 'eighth_pair': 'c2c2', 'sixteenth_quartet': 'cccc', 
                'sixteenth_pair_eighth': 'ccc2', 'eighth_sixteenth_pair': 'c2cc', 
                'sixteenth_eighth_sixteenth': 'cc2c', 'eighth_dot_sixteenth': 'c3c', 
                'z2c2': 'z2c2', 'z4': 'z4'
            };
            const availablePatterns = Array.from(checkboxes).map(cb => patternMap[cb.value]);
            
            let abcMeasures = [];
            for (let i = 0; i < measuresCount; i++) {
                let measure = [];
                for (let beat = 0; beat < 4; beat++) { 
                    measure.push(availablePatterns[Math.floor(Math.random() * availablePatterns.length)]); 
                }
                abcMeasures.push(measure.join(' '));
            }
            
            let generatedABC = '';
            abcMeasures.forEach((measure, i) => {
                if (i > 0) {
                    if (wrapMeasures) {
                        generatedABC += ' |\n';
                    } else if (i % 4 === 0) {
                        generatedABC += ' |\n';
                    } else {
                        generatedABC += ' | ';
                    }
                }
                generatedABC += measure;
            });
            generatedABC += ' |';
            
            if (appendMode && block.abc) {
                block.abc = block.abc.trim() + ' | ' + generatedABC;
            } else {
                block.abc = generatedABC;
            }
            
            const textarea = document.getElementById(`abc-textarea-${blockIndex}`);
            if (textarea) textarea.value = block.abc;
            updateDSL(); 
        };

        function toggleBuilderGridCell(cellElement, blockIdx) {
            const trackId = cellElement.dataset.track;
            const idx = parseInt(cellElement.dataset.index);
            const symbol = cellElement.dataset.symbol;
            const blocks = getCurrentBlocks();
            const currentPattern = blocks[blockIdx][`target_${trackId}`] || '|----------------|';
            let pattern = currentPattern.replace(/^\||\|$/g, '').padEnd(16, '-').slice(0, 16);
            const currentChar = pattern[idx];
            const newChar = (currentChar === symbol || currentChar === 'x' || currentChar === 'o') ? '-' : symbol;
            pattern = pattern.substring(0, idx) + newChar + pattern.substring(idx + 1);
            blocks[blockIdx][`target_${trackId}`] = '|' + pattern + '|';
            if (newChar !== '-') { cellElement.classList.add('active'); } else { cellElement.classList.remove('active'); }
            updateDSL();
            refreshGridNotationPreview(blockIdx);
        }

        function toggleGridTrackVisibility(blockIdx, trackId, isChecked) {
            const blocks = getCurrentBlocks();
            blocks[blockIdx][`show_${trackId}`] = isChecked ? 'true' : 'false';
            updateDSL();
            const row = event.target.closest('.le-track-row');
            if (row) {
                row.style.opacity = isChecked ? '1' : '0.4';
                const stepsContainer = row.querySelector('.le-track-steps');
                if (stepsContainer) {
                    if (isChecked) { stepsContainer.classList.remove('disabled'); } else { stepsContainer.classList.add('disabled'); }
                }
            }
            refreshGridNotationPreview(blockIdx);
        }

        function refreshGridNotationPreview(blockIdx) {
            if (typeof GrooveUtils === 'undefined') return;
            const blocks = getCurrentBlocks();
            const block = blocks[blockIdx];
            if (!block || block.type !== 'grid') return;
            const blockEls = document.querySelectorAll('.canvas-block');
            const blockEl = blockEls[blockIdx]; 
            if (!blockEl) return;
            const notationContainer = blockEl.querySelector('.builder-notation-preview');
            if (!notationContainer) return;
            
            const showHH = String(block.show_hh || 'true') === 'true';
            const showSN = String(block.show_snare || 'true') === 'true';
            const showKT = String(block.show_kick || 'true') === 'true';
            try {
                const gu = new GrooveUtils();
                const grooveData = new gu.grooveDataNew();
                grooveData.timeDivision = 16; grooveData.tempo = 90; grooveData.numberOfMeasures = 1;
                const hhData = showHH ? (block.target_hh || '|----------------|') : '|----------------|';
                const snData = showSN ? (block.target_snare || '|----------------|') : '|----------------|';
                const ktData = showKT ? (block.target_kick || '|----------------|') : '|----------------|';
                grooveData.hh_array = gu.noteArraysFromURLData('H', hhData, 16, 1);
                grooveData.snare_array = gu.noteArraysFromURLData('S', snData, 16, 1);
                grooveData.kick_array = gu.noteArraysFromURLData('K', ktData, 16, 1);
                const abc = gu.createABCFromGrooveData(grooveData, 600);
                notationContainer.innerHTML = gu.renderABCtoSVG(abc).svg;
            } catch(e) { console.warn("Не удалось обновить предпросмотр нот:", e); }
        }

        window.switchNotationMode = function(blockIdx, mode) {
            const blocks = getCurrentBlocks();
            blocks[blockIdx].notation_mode = mode;
            updateDSL();
            renderActiveArea();
        };

        window.addManualNote = function(blockIdx, pattern) {
            const blocks = getCurrentBlocks();
            const block = blocks[blockIdx];
            let currentAbc = block.abc || '';
            currentAbc += (currentAbc.length > 0 ? ' ' : '') + pattern;
            
            const textarea = document.getElementById(`abc-textarea-${blockIdx}`);
            if (textarea) {
                textarea.value = currentAbc;
                updateBlock(blockIdx, 'abc', currentAbc);
            }
        };

        window.addBarLine = function(blockIdx) {
            const blocks = getCurrentBlocks();
            const block = blocks[blockIdx];
            let currentAbc = block.abc || '';
            currentAbc += (currentAbc.length > 0 ? ' | ' : '');
            
            const textarea = document.getElementById(`abc-textarea-${blockIdx}`);
            if (textarea) {
                textarea.value = currentAbc;
                updateBlock(blockIdx, 'abc', currentAbc);
            }
        };

        window.clearManualNotation = function(blockIdx) {
            const blocks = getCurrentBlocks();
            blocks[blockIdx].abc = '';
            const textarea = document.getElementById(`abc-textarea-${blockIdx}`);
            if (textarea) {
                textarea.value = '';
                updateBlock(blockIdx, 'abc', '');
            }
        };
		
		
		
window.addAnimNote = function(blockIdx, type) {
    const blocks = getCurrentBlocks();
    let seq = blocks[blockIdx].sequence ? blocks[blockIdx].sequence.split(',') : [];
    seq.push(type);
    updateBlock(blockIdx, 'sequence', seq.join(','));
};

window.removeAnimNote = function(blockIdx, noteIdx) {
    const blocks = getCurrentBlocks();
    let seq = blocks[blockIdx].sequence ? blocks[blockIdx].sequence.split(',') : [];
    seq.splice(noteIdx, 1);
    updateBlock(blockIdx, 'sequence', seq.join(','));
};
		
		
		
    </script>
</body>
</html>