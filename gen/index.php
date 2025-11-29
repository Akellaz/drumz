<?php require_once __DIR__ . '/../includes/seo.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?= htmlspecialchars($title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($description) ?>">
    <meta name="keywords" content="длительности нот, обучение ритму, музыкальное сольфеджио, уроки барабанов, Троицк, Drumz, ритм-тренажёр, ноты для детей">
    <meta name="author" content="Сергей Щепотин">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://drumz.ru<?= htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES) ?>">
    <!-- Подключаем общий стиль сайта -->
    <link rel="stylesheet" href="/assets/style.css">
    <!-- Встроенные стили для специфичных элементов тренажёра -->
    <style>
        /* Уникальные стили для музыкального тренажера */
        #game-content {
            background: var(--card-bg);
            border-radius: var(--radius);
            padding: 20px;
            margin: 20px 0;
            border: 1px solid var(--border);
        }
        #gameCanvas { 
            border: 2px solid var(--border); 
            background-color: white;
            margin: 20px auto;
            display: block;
            border-radius: 8px;
            width: 100%;
            max-width: 800px;
            height: auto;
        }
        .btn-primary { 
            padding: 15px 25px; 
            margin: 10px;
            font-size: 18px; 
            background-color: var(--primary); /* Используем основной акцент */
            color: white; /* Белый текст на акцентной кнопке */
            border: 1px solid var(--primary); /* Граница та же */
            border-radius: var(--radius);
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
            font-weight: 600;
            text-decoration: none; /* Убираем подчеркивание, если это ссылка */
        }
        .btn-primary:hover {
            background-color: var(--primary-light); /* Светлый акцент при наведении */
            color: var(--text); /* Основной цвет текста при наведении */
            border-color: var(--primary); /* Оставляем границу акцентной */
        }
        #message {
            font-size: 20px;
            min-height: 30px;
            margin: 15px;
            text-align: center;
            padding: 15px;
            border-radius: var(--radius);
        }
        .correct { 
            color: var(--success); /* Используем цвет успеха */
            font-weight: bold; 
            background: rgba(72, 187, 120, 0.1); /* Лёгкий фон успеха */
            border: 1px solid var(--success); /* Граница успеха */
        }
        .incorrect { 
            color: var(--danger); /* Используем цвет ошибки */
            font-weight: bold; 
            background: rgba(229, 62, 62, 0.1); /* Лёгкий фон ошибки */
            border: 1px solid var(--danger); /* Граница ошибки */
        }
        #note-selector {
            margin: 20px 0;
            padding: 20px;
            background: var(--primary-light); /* Лёгкий акцентный фон */
            border: 1px solid var(--primary); /* Граница акцентная */
            border-radius: var(--radius);
        }
        .note-group {
            display: inline-block;
            margin: 8px;
            padding: 12px 16px;
            border: 2px solid var(--border); /* Используем общую границу */
            border-radius: var(--radius);
            background-color: var(--card-bg); /* Используем фон карточки */
            cursor: pointer;
            user-select: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            color: var(--text); /* Используем основной цвет текста */
        }
        .note-group:hover {
            background-color: var(--primary-light); /* Светлый акцент при наведении */
            color: var(--text); /* Основной цвет текста при наведении */
            border-color: var(--primary); /* Акцентная граница при наведении */
        }
        .note-group.selected {
            background-color: var(--primary); /* Акцентный фон при выборе */
            color: white; /* Белый текст при выборе */
            border-color: var(--primary); /* Акцентная граница при выборе */
        }
        #selector-controls {
            margin: 15px 0;
            text-align: center;
        }
        #selector-controls .btn {
            padding: 10px 15px;
            margin: 5px;
            font-size: 16px;
        }
        /* Стили для секвенсора */
        .drum-container {
            padding: 0;
            margin: 20px 0 0 0;
            border-radius: 0; /* Сбрасываем, если нужно */
        }
        .drum-controls {
            background: var(--card-bg); /* Фон как у карточки */
            padding: 20px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            border: 1px solid var(--border); /* Добавляем границу */
        }
        .control-group {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }
        .control-group input, 
        .control-group button, 
        .control-group select {
            padding: 8px 12px;
            border: 1px solid var(--border); /* Используем общую границу */
            border-radius: var(--radius); /* Используем общий радиус */
            font-size: 14px;
            background: var(--bg); /* Используем основной фон */
            color: var(--text); /* Используем основной цвет текста */
        }
        .control-group button:hover {
            background: var(--primary-light); /* Светлый акцент при наведении */
            border-color: var(--primary); /* Акцентная граница при наведении */
        }
        .drum-grid {
            background: var(--card-bg); /* Фон как у карточки */
            border-radius: var(--radius);
            box-shadow: var(--shadow); /* Используем общую тень */
            overflow: hidden;
            margin-bottom: 20px;
            border: 1px solid var(--border); /* Добавляем границу */
        }
        .track-row {
            display: flex;
            border-bottom: 1px solid var(--border); /* Используем общую границу */
        }
        .track-row:last-child {
            border-bottom: none;
        }
        .track-name {
            width: 120px;
            padding: 15px;
            font-weight: bold;
            background: var(--card-bg); /* Фон как у карточки */
            text-align: right;
            border-right: 1px solid var(--border); /* Используем общую границу */
            color: var(--text); /* Используем основной цвет текста */
        }
        .track-steps {
            display: flex;
            flex: 1;
            padding: 5px;
        }
        .step {
            width: 40px;
            height: 40px;
            border: 2px solid var(--border); /* Используем общую границу */
            margin: 2px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            user-select: none;
            border-radius: var(--radius); /* Используем общий радиус */
            transition: all 0.2s ease;
            background: var(--bg); /* Используем основной фон */
            color: var(--text); /* Используем основной цвет текста */
        }
        /* Цветовые группы по 4 ячейки - адаптированы под светлую тему */
        .step.group-0 {
            background-color: #fff3cd; /* Bootstrap warning light */
            border-color: #ffeaa7;
        }
        .step.group-0.active {
            background-color: #ffc107;
            border-color: #e0a800;
            color: #212529;
        }
        .step.group-1 {
            background-color: #d1ecf1; /* Bootstrap info light */
            border-color: #b8daff;
        }
        .step.group-1.active {
            background-color: #17a2b8;
            border-color: #138496;
            color: white;
        }
        .step.group-2 {
            background-color: #f8d7da; /* Bootstrap danger light */
            border-color: #f5c6cb;
        }
        .step.group-2.active {
            background-color: #dc3545;
            border-color: #bd2130;
            color: white;
        }
        .step.group-3 {
            background-color: #d4edda; /* Bootstrap success light */
            border-color: #c3e6cb;
        }
        .step.group-3.active {
            background-color: #28a745;
            border-color: #1e7e34;
            color: white;
        }
        .step:hover {
            background-color: var(--primary-light); /* Светлый акцент при наведении */
            border-color: var(--primary); /* Акцентная граница при наведении */
        }
        .step.active {
            background-color: var(--primary); /* Акцентный фон при активации */
            border-color: var(--primary); /* Акцентная граница при активации */
            color: white; /* Белый текст при активации */
            font-weight: bold;
        }
        .step.playing {
            background-color: var(--danger) !important; /* Цвет ошибки при воспроизведении */
            border-color: #ff5252 !important; /* Более яркая граница */
            transform: scale(1.1);
            color: white;
        }
        /* Адаптивность */
        @media (max-width: 768px) {
            .track-name {
                width: 80px;
                padding: 10px;
                font-size: 12px;
            }
            .step {
                width: 30px;
                height: 30px;
                font-size: 12px;
            }
            .note-group {
                display: block;
                margin: 5px 0;
                padding: 10px;
                text-align: center;
            }
            .control-group {
                flex-direction: column;
                align-items: stretch;
            }
            .control-group button, .control-group input, .control-group span {
                width: 100%;
                max-width: 300px;
                margin: 5px auto;
            }
            #gameCanvas {
                width: 100%;
                height: auto;
            }
        }
        @media (max-width: 480px) {
            .track-name {
                width: 60px;
                padding: 8px;
                font-size: 11px;
            }
            .step {
                width: 25px;
                height: 25px;
                font-size: 10px;
            }
            .btn-primary {
                padding: 12px 20px;
                font-size: 16px;
            }
        }
    </style>
    <?php require_once __DIR__ . '/../includes/seo.php'; ?>
</head>
<body>
    <?php require_once __DIR__ . '/../includes/header.php'; ?>
    <main class="container">
        <div class="card"> <!-- Обернули в общую карточку -->
            <h1>🎵 Изучаем длительности нот 🎵</h1>
            <div id="note-selector">
                <h3>Выберите группы нот:</h3>
                <div class="note-group" onclick="toggleNoteType('quarter')">Четвертная</div>
                <div class="note-group" onclick="toggleNoteType('eighth_pair')">Восьмые</div>
                <div class="note-group" onclick="toggleNoteType('sixteenth_quartet')">Шестнадцатые</div>
                <div class="note-group" onclick="toggleNoteType('sixteenth_pair_eighth')">2 шестнадцатых + восьмая</div>
                <div class="note-group" onclick="toggleNoteType('eighth_sixteenth_pair')">Восьмая + две шестнадцатых</div>
                <div class="note-group" onclick="toggleNoteType('sixteenth_eighth_sixteenth')">Шестнадцатая + восьмая + шестнадцатая</div>
                <!-- УБРАНО: <div class="note-group" onclick="toggleNoteType('eighth_dotted_sixteenth')">Восьмая с точкой + 16</div> -->
                <div id="selector-controls">
                    <button class="btn" onclick="selectAll()">Выбрать все</button>
                    <button class="btn" onclick="deselectAll()">Снять выбор</button>
                </div>
            </div>
            <div id="game-content">
                <div style="text-align: center; margin: 20px 0;">
                    <button class="btn-primary" id="newQuestionBtn">Новый пример</button>
                </div>
                <canvas id="gameCanvas" width="800" height="300"></canvas>
                <div id="message"></div>
            </div>
            <!-- Секвенсор теперь внутри карточки, под нотами -->
            <div class="drum-container">
                <h2 style="margin: 0 0 15px 0; font-size: 1.4em; color: var(--primary);">🥁 Попробуй сыграть этот ритм</h2>
                <div class="drum-grid" id="drumGrid"></div>
                <div class="drum-controls">
                    <div class="control-group">
                        <button onclick="playPattern()" id="playButton" class="btn">▶️ Проиграть</button>
                        <button onclick="clearPattern()" id="clearButton" class="btn">🧹 Очистить</button>
                        <input type="range" id="bpm" min="40" max="240" value="60">
                        <span>BPM: <span id="bpmValue">60</span></span>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
    <script>
        /* --------------------------------------------------------------
           Объединённый JavaScript (обновлён)
           --------------------------------------------------------------*/
        /* ------------------------------------------------------------------------
           Game Logic
           ------------------------------------------------------------------------*/
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        const messageDiv = document.getElementById('message');
        const noteDurations = {
            "quarter": {name: "Четвертная", beats: 1},
            "eighth_pair": {name: "Восьмые", beats: 1},
            "sixteenth_quartet": {name: "Шестнадцатые", beats: 1},
            "sixteenth_pair_eighth": {name: "Две шестнадцатых + восьмая", beats: 2},
            "eighth_sixteenth_pair": {name: "Восьмая + две шестнадцатых", beats: 2},
            "sixteenth_eighth_sixteenth": {name: "Шестнадцатая + восьмая + шестнадцатая", beats: 2}
            // "eighth_dotted_sixteenth": {name: "Восьмая с точкой + шестнадцатая", beats: 2} // УБРАНО
        };
        let currentNotes = [];
        // УБРАНО: 'eighth_dotted_sixteenth' из начального набора
        let selectedNoteTypes = new Set([
            'quarter', 
            'eighth_pair', 
            'sixteenth_quartet', 
            'sixteenth_pair_eighth',
            'eighth_sixteenth_pair',
            'sixteenth_eighth_sixteenth'
        ]);
        // Функция для выбора/отмены выбора типа нот
        function toggleNoteType(noteType) {
            const noteNames = {
                "quarter": "Четвертная",
                "eighth_pair": "Восьмые",
                "sixteenth_quartet": "Шестнадцатые",
                "sixteenth_pair_eighth": "2 шестнадцатых + восьмая",
                "eighth_sixteenth_pair": "Восьмая + две шестнадцатых",
                "sixteenth_eighth_sixteenth": "Шестнадцатая + восьмая + шестнадцатая"
                // "eighth_dotted_sixteenth": "Восьмая с точкой + шестнадцатая" // УБРАНО
            };
            const elements = document.querySelectorAll('.note-group');
            elements.forEach(element => {
                if (element.textContent.includes(noteNames[noteType])) {
                    if (selectedNoteTypes.has(noteType)) {
                        selectedNoteTypes.delete(noteType);
                        element.classList.remove('selected');
                    } else {
                        selectedNoteTypes.add(noteType);
                        element.classList.add('selected');
                    }
                }
            });
        }
        // Выбрать все типы нот
        function selectAll() {
            selectedNoteTypes = new Set([
                'quarter', 
                'eighth_pair', 
                'sixteenth_quartet', 
                'sixteenth_pair_eighth',
                'eighth_sixteenth_pair',
                'sixteenth_eighth_sixteenth'
                // 'eighth_dotted_sixteenth' // УБРАНО
            ]);
            const elements = document.querySelectorAll('.note-group');
            elements.forEach(element => element.classList.add('selected'));
        }
        // Снять выбор со всех типов нот
        function deselectAll() {
            selectedNoteTypes.clear();
            const elements = document.querySelectorAll('.note-group');
            elements.forEach(element => element.classList.remove('selected'));
        }
        // Класс для рисования нот
        class MusicNotes {
            constructor(ctx) {
                this.ctx = ctx;
                this.ovalWidth = 16;
                this.ovalHeight = 10;
                this.stemHeight = 55;
                this.lineY = 190; // ⬅️ вернули на 3-ю линию
            }
            clear() {
                this.ctx.clearRect(0, 0, canvas.width, canvas.height);
                this.drawStaff();
            }
            drawStaff() {
                const y = 150;
                this.ctx.strokeStyle = '#333';
                this.ctx.lineWidth = 1;
                for (let i = 0; i < 5; i++) {
                    this.ctx.beginPath();
                    this.ctx.moveTo(50, y + i * 20);
                    this.ctx.lineTo(750, y + i * 20);
                    this.ctx.stroke();
                }
            }
            // Четвертная нота
            drawQuarterNote(x, y, color = 'black') {
                const stemTopY = this.lineY - this.stemHeight;
                const stemX = x + this.ovalWidth;
                this.ctx.beginPath();
                this.ctx.moveTo(stemX, this.lineY - 5);
                this.ctx.lineTo(stemX, stemTopY);
                this.ctx.lineWidth = 2;
                this.ctx.strokeStyle = color;
                this.ctx.stroke();
                this.ctx.beginPath();
                this.ctx.ellipse(x, this.lineY, this.ovalWidth, this.ovalHeight, 0, 0, 2 * Math.PI);
                this.ctx.fillStyle = color;
                this.ctx.fill();
            }
            // Пара восьмых нот
            drawEighthPair(x, y, color = 'black') {
                const stemTopY = this.lineY - this.stemHeight;
                const stemX1 = x - 8 + this.ovalWidth;
                const stemX2 = x + 48 + this.ovalWidth;
                const beamY = stemTopY + 5;
                this.ctx.beginPath();
                this.ctx.moveTo(stemX1, this.lineY - 5);
                this.ctx.lineTo(stemX1, beamY);
                this.ctx.lineWidth = 2;
                this.ctx.strokeStyle = color;
                this.ctx.stroke();
                this.ctx.beginPath();
                this.ctx.ellipse(x - 8, this.lineY, this.ovalWidth, this.ovalHeight, 0, 0, 2 * Math.PI);
                this.ctx.fillStyle = color;
                this.ctx.fill();
                this.ctx.beginPath();
                this.ctx.moveTo(stemX2, this.lineY - 5);
                this.ctx.lineTo(stemX2, beamY);
                this.ctx.lineWidth = 2;
                this.ctx.strokeStyle = color;
                this.ctx.stroke();
                this.ctx.beginPath();
                this.ctx.ellipse(x + 48, this.lineY, this.ovalWidth, this.ovalHeight, 0, 0, 2 * Math.PI);
                this.ctx.fillStyle = color;
                this.ctx.fill();
                this.ctx.beginPath();
                this.ctx.moveTo(stemX1, beamY);
                this.ctx.lineTo(stemX2, beamY);
                this.ctx.lineWidth = 5;
                this.ctx.strokeStyle = color;
                this.ctx.stroke();
            }
            // Четверка шестнадцатых нот
            drawSixteenthQuartet(x, y, color = 'black') {
                const stemTopY = this.lineY - this.stemHeight;
                const beamY1 = stemTopY + 3; // ⬅️ было +5
                const beamY2 = stemTopY + 18; // ⬅️ было +20
                const ovalPositions = [-8, 24, 56, 88];
                const stemPositions = ovalPositions.map(pos => pos + this.ovalWidth);
                for (let i = 0; i < 4; i++) {
                    const ovalX = x + ovalPositions[i];
                    const stemX = x + stemPositions[i];
                    this.ctx.beginPath();
                    this.ctx.moveTo(stemX, this.lineY - 5);
                    this.ctx.lineTo(stemX, stemTopY);
                    this.ctx.lineWidth = 2;
                    this.ctx.strokeStyle = color;
                    this.ctx.stroke();
                    this.ctx.beginPath();
                    this.ctx.ellipse(ovalX, this.lineY, this.ovalWidth, this.ovalHeight, 0, 0, 2 * Math.PI);
                    this.ctx.fillStyle = color;
                    this.ctx.fill();
                }
                this.ctx.beginPath();
                this.ctx.moveTo(x + stemPositions[0], beamY1);
                this.ctx.lineTo(x + stemPositions[3], beamY1);
                this.ctx.lineWidth = 5;
                this.ctx.strokeStyle = color;
                this.ctx.stroke();
                this.ctx.beginPath();
                this.ctx.moveTo(x + stemPositions[0], beamY2);
                this.ctx.lineTo(x + stemPositions[3], beamY2);
                this.ctx.lineWidth = 5;
                this.ctx.strokeStyle = color;
                this.ctx.stroke();
                for (let i = 0; i < 4; i++) {
                    const stemX = x + stemPositions[i];
                    this.ctx.beginPath();
                    this.ctx.moveTo(stemX, beamY1);
                    this.ctx.lineTo(stemX, beamY2);
                    this.ctx.lineWidth = 2;
                    this.ctx.strokeStyle = color;
                    this.ctx.stroke();
                }
            }
            // Две шестнадцатых + одна восьмая
            drawSixteenthPairEighth(x, y, color = 'black') {
                const stemTopY = this.lineY - this.stemHeight;
                const beamY1 = stemTopY + 3; // ⬅️ было +5
                const beamY2 = stemTopY + 18; // ⬅️ было +20
                const eighthBeamY = beamY1; // ⬅️ теперь балка к восьмой на уровне верхней шестнадцатой
                const ovalPositions = [0, 32, 80];
                const stemPositions = ovalPositions.map(pos => pos + this.ovalWidth);
                for (let i = 0; i < 3; i++) {
                    const ovalX = x + ovalPositions[i];
                    const stemX = x + stemPositions[i];
                    this.ctx.beginPath();
                    this.ctx.moveTo(stemX, this.lineY - 5);
                    this.ctx.lineTo(stemX, stemTopY);
                    this.ctx.lineWidth = 2;
                    this.ctx.strokeStyle = color;
                    this.ctx.stroke();
                    this.ctx.beginPath();
                    this.ctx.ellipse(ovalX, this.lineY, this.ovalWidth, this.ovalHeight, 0, 0, 2 * Math.PI);
                    this.ctx.fillStyle = color;
                    this.ctx.fill();
                }
                const firstStemX = x + stemPositions[0];
                const secondStemX = x + stemPositions[1];
                this.ctx.beginPath();
                this.ctx.moveTo(firstStemX, beamY1);
                this.ctx.lineTo(secondStemX, beamY1);
                this.ctx.lineWidth = 5;
                this.ctx.strokeStyle = color;
                this.ctx.stroke();
                this.ctx.beginPath();
                this.ctx.moveTo(firstStemX, beamY2);
                this.ctx.lineTo(secondStemX, beamY2);
                this.ctx.lineWidth = 5;
                this.ctx.strokeStyle = color;
                this.ctx.stroke();
                for (let i = 0; i < 2; i++) {
                    const stemX = x + stemPositions[i];
                    this.ctx.beginPath();
                    this.ctx.moveTo(stemX, beamY1);
                    this.ctx.lineTo(stemX, beamY2);
                    this.ctx.lineWidth = 2;
                    this.ctx.strokeStyle = color;
                    this.ctx.stroke();
                }
                const thirdStemX = x + stemPositions[2];
                this.ctx.beginPath();
                this.ctx.moveTo(thirdStemX, eighthBeamY);
                this.ctx.lineTo(secondStemX, eighthBeamY);
                this.ctx.lineWidth = 5;
                this.ctx.strokeStyle = color;
                this.ctx.stroke();
            }
            // Восьмая + две шестнадцатых
            drawEighthSixteenthPair(x, y, color = 'black') {
                const stemTopY = this.lineY - this.stemHeight;
                const beamY1 = stemTopY + 3; // ⬅️ было +5
                const beamY2 = stemTopY + 18; // ⬅️ было +20
                const positions = [32, 83, 116];
                const ovalPositions = [16, 69, 100];
                const stemPositions = positions;
                for (let i = 0; i < 3; i++) {
                    const ovalX = x + ovalPositions[i];
                    const stemX = x + stemPositions[i];
                    this.ctx.beginPath();
                    this.ctx.moveTo(stemX, this.lineY - 5);
                    this.ctx.lineTo(stemX, stemTopY);
                    this.ctx.lineWidth = 2;
                    this.ctx.strokeStyle = color;
                    this.ctx.stroke();
                    this.ctx.beginPath();
                    this.ctx.ellipse(ovalX, this.lineY, this.ovalWidth, this.ovalHeight, 0, 0, 2 * Math.PI);
                    this.ctx.fillStyle = color;
                    this.ctx.fill();
                }
                const firstStemX = x + stemPositions[0];
                const secondStemX = x + stemPositions[1];
                const thirdStemX = x + stemPositions[2];
                this.ctx.beginPath();
                this.ctx.moveTo(firstStemX, beamY1);
                this.ctx.lineTo(thirdStemX, beamY1);
                this.ctx.lineWidth = 5;
                this.ctx.strokeStyle = color;
                this.ctx.stroke();
                this.ctx.beginPath();
                this.ctx.moveTo(secondStemX, beamY2);
                this.ctx.lineTo(thirdStemX, beamY2);
                this.ctx.lineWidth = 5;
                this.ctx.strokeStyle = color;
                this.ctx.stroke();
                this.ctx.beginPath();
                this.ctx.moveTo(firstStemX, beamY1);
                this.ctx.lineTo(firstStemX, beamY2 - 15);
                this.ctx.lineWidth = 2;
                this.ctx.strokeStyle = color;
                this.ctx.stroke();
                this.ctx.beginPath();
                this.ctx.moveTo(secondStemX, beamY1);
                this.ctx.lineTo(secondStemX, beamY2);
                this.ctx.lineWidth = 2;
                this.ctx.strokeStyle = color;
                this.ctx.stroke();
                this.ctx.beginPath();
                this.ctx.moveTo(thirdStemX, beamY1);
                this.ctx.lineTo(thirdStemX, beamY2);
                this.ctx.lineWidth = 2;
                this.ctx.strokeStyle = color;
                this.ctx.stroke();
            }
            // Шестнадцатая + восьмая + шестнадцатая
            drawSixteenthEighthSixteenth(x, y, color = 'black') {
                const stemTopY = this.lineY - this.stemHeight;
                const beamY1 = stemTopY + 3; // ⬅️ было +5
                const beamY2 = stemTopY + 18; // ⬅️ было +20
                const positions = [16, 48, 96];
                const ovalPositions = [0, 32, 80];
                const stemPositions = positions;
                for (let i = 0; i < 3; i++) {
                    const ovalX = x + ovalPositions[i];
                    const stemX = x + stemPositions[i];
                    this.ctx.beginPath();
                    this.ctx.moveTo(stemX, this.lineY - 5);
                    this.ctx.lineTo(stemX, stemTopY);
                    this.ctx.lineWidth = 2;
                    this.ctx.strokeStyle = color;
                    this.ctx.stroke();
                    this.ctx.beginPath();
                    this.ctx.ellipse(ovalX, this.lineY, this.ovalWidth, this.ovalHeight, 0, 0, 2 * Math.PI);
                    this.ctx.fillStyle = color;
                    this.ctx.fill();
                }
                const firstStemX = x + stemPositions[0];
                const secondStemX = x + stemPositions[1];
                const thirdStemX = x + stemPositions[2];
                this.ctx.beginPath();
                this.ctx.moveTo(firstStemX, beamY1);
                this.ctx.lineTo(thirdStemX, beamY1);
                this.ctx.lineWidth = 5;
                this.ctx.strokeStyle = color;
                this.ctx.stroke();
                this.ctx.beginPath();
                this.ctx.moveTo(firstStemX, beamY2);
                this.ctx.lineTo(firstStemX + 16, beamY2);
                this.ctx.lineWidth = 5;
                this.ctx.strokeStyle = color;
                this.ctx.stroke();
                this.ctx.beginPath();
                this.ctx.moveTo(thirdStemX - 16, beamY2);
                this.ctx.lineTo(thirdStemX, beamY2);
                this.ctx.lineWidth = 5;
                this.ctx.strokeStyle = color;
                this.ctx.stroke();
                this.ctx.beginPath();
                this.ctx.moveTo(firstStemX, beamY1);
                this.ctx.lineTo(firstStemX, beamY2);
                this.ctx.lineWidth = 2;
                this.ctx.strokeStyle = color;
                this.ctx.stroke();
                this.ctx.beginPath();
                this.ctx.moveTo(secondStemX, beamY1);
                this.ctx.lineTo(secondStemX, beamY2 - 15);
                this.ctx.lineWidth = 2;
                this.ctx.strokeStyle = color;
                this.ctx.stroke();
                this.ctx.beginPath();
                this.ctx.moveTo(thirdStemX, beamY1);
                this.ctx.lineTo(thirdStemX, beamY2);
                this.ctx.lineWidth = 2;
                this.ctx.strokeStyle = color;
                this.ctx.stroke();
            }
            // УБРАНО: drawEighthDottedSixteenth
            drawNote(noteType, x, y) {
                switch(noteType) {
                    case 'quarter':
                        this.drawQuarterNote(x, y);
                        break;
                    case 'eighth_pair':
                        this.drawEighthPair(x, y);
                        break;
                    case 'sixteenth_quartet':
                        this.drawSixteenthQuartet(x, y);
                        break;
                    case 'sixteenth_pair_eighth':
                        this.drawSixteenthPairEighth(x, y);
                        break;
                    case 'eighth_sixteenth_pair':
                        this.drawEighthSixteenthPair(x, y);
                        break;
                    case 'sixteenth_eighth_sixteenth':
                        this.drawSixteenthEighthSixteenth(x, y);
                        break;
                    // case 'eighth_dotted_sixteenth': // УБРАНО
                    //     this.drawEighthDottedSixteenth(x, y);
                    //     break;
                }
            }
        }
        const notes = new MusicNotes(ctx);
        // Глобальная функция newQuestion
        function newQuestion() {
            currentNotes = [];
            messageDiv.textContent = "";
            messageDiv.className = "";
            // Проверяем, есть ли выбранные типы нот
            if (selectedNoteTypes.size === 0) {
                messageDiv.textContent = "Пожалуйста, выберите хотя бы один тип нот!";
                messageDiv.className = "incorrect";
                return;
            }
            // Генерируем 4 случайные группы нот из выбранных типов
            const noteKeys = Array.from(selectedNoteTypes);
            const positions = [120, 280, 440, 600];
            for (let i = 0; i < 4; i++) {
                const noteType = noteKeys[Math.floor(Math.random() * noteKeys.length)];
                currentNotes.push({
                    type: noteType,
                    position: positions[i],
                    beats: noteDurations[noteType].beats
                });
            }
            drawNotes();
            // Очищаем секвенсор
            if (window.drumSequencer) {
                drumSequencer.clearPattern();
            }
        }
        function drawNotes() {
            notes.clear();
            // Рисуем все 4 группы нот на третьей линии
            for (let i = 0; i < currentNotes.length; i++) {
                const note = currentNotes[i];
                switch(note.type) {
                    case 'eighth_pair':
                        notes.drawNote(note.type, note.position - 32, 0);
                        break;
                    case 'sixteenth_quartet':
                        notes.drawNote(note.type, note.position - 48, 0); // как и было
                        break;
                    case 'sixteenth_pair_eighth':
                        notes.drawNote(note.type, note.position - 40, 0);
                        break;
                    case 'eighth_sixteenth_pair':
                        notes.drawNote(note.type, note.position - 48, 0);
                        break;
                    case 'sixteenth_eighth_sixteenth':
                        notes.drawNote(note.type, note.position - 48, 0);
                        break;
                    // case 'eighth_dotted_sixteenth': // УБРАНО
                    //     notes.drawNote(note.type, note.position - 48, 0); // ⬅️ теперь как у 4 шестнадцатых
                    //     break;
                    default:
                        notes.drawNote(note.type, note.position, 0);
                        break;
                }
            }
        }
        // Инициализация выбора
        selectAll();
        /* ------------------------------------------------------------------------
           Drum Sequencer (обновлён)
           ------------------------------------------------------------------------*/
        class DrumSequencer {
            constructor() {
                /* ---------- Настройки ---------- */
                this.track = 'snare';     // только один трек, теперь просто "барабан"
                this.steps = 16;          // количество шагов (16‑16‑th нот)
                this.bpm   = 60;         // начальное BPM
                /* ---------- Состояние ---------- */
                this.pattern       = new Array(this.steps).fill(0); // простой массив для одного трека
                this.isPlaying     = false;            // сейчас играет?
                this.currentStep   = 0;                // текущий шаг во время воспроизведения
                this.intervalId    = null;             // ID setInterval
                this.audioContext  = null;             // Web Audio API context
                this.samples       = {};               // загруженные AudioBuffer‑ы
                this.audioReady    = false;            // true, когда все сэмплы загружены
                /* ---------- Инициализация ---------- */
                this.renderGrid();        // визуальная сетка
                this.setupEventListeners();
                // сразу начинаем загрузку звуков
                this.initAudio();
            }
            /* ------------------------------------------------------------------------
               AUDIO – инициализация и загрузка сэмплов
               ------------------------------------------------------------------------ */
            async initAudio() {
                try {
                    this.audioContext = new (window.AudioContext ||
                                           window.webkitAudioContext)();
                    // Загружаем только snare
                    const url = 'sounds/snare.wav';
                    console.log('Загрузка барабана...');
                    const resp = await fetch(url);
                    if (!resp.ok) {
                        throw new Error(`HTTP ${resp.status}`);
                    }
                    const arrayBuf = await resp.arrayBuffer();
                    this.samples.snare = await this.audioContext.decodeAudioData(arrayBuf);
                    console.log('✔️ барабан загружен');
                    this.audioReady = true;
                    console.log('Сэмпл готов к использованию');
                } catch (e) {
                    console.error('Ошибка загрузки аудио:', e);
                }
            }
            playSound(name) {
                if (!this.audioReady) {
                    console.warn('Звук ещё не загружен');
                    return;
                }
                const buffer = this.samples[name];
                if (!buffer) {
                    console.warn(`Сэмпл "${name}" не найден`);
                    return;
                }
                try {
                    const src = this.audioContext.createBufferSource();
                    src.buffer = buffer;
                    src.connect(this.audioContext.destination);
                    src.start(0);
                } catch (e) {
                    console.error(`Ошибка воспроизведения ${name}:`, e);
                }
            }
            /* ------------------------------------------------------------------------
               GRID – работа с визуализацией
               ------------------------------------------------------------------------ */
            renderGrid() {
                const grid = document.getElementById('drumGrid');
                if (!grid) {
                    console.error('#drumGrid элемент не найден в DOM');
                    return;
                }
                grid.innerHTML = '';
                const row = document.createElement('div');
                row.className = 'track-row';
                const nameDiv = document.createElement('div');
                nameDiv.className = 'track-name';
                nameDiv.textContent = 'Барабан'; // ПОМЕНЯНО: было 'snare'
                row.appendChild(nameDiv);
                const stepsDiv = document.createElement('div');
                stepsDiv.className = 'track-steps';
                this.pattern.forEach((v, i) => {
                    const stepDiv = document.createElement('div');
                    stepDiv.className = `step ${v ? 'active' : ''}`;
                    // Добавляем класс для выделения групп по 4
                    const groupIndex = Math.floor(i / 4);
                    stepDiv.classList.add(`group-${groupIndex % 4}`);
                    stepDiv.dataset.step = i;
                    stepDiv.textContent = v ? '●' : '';
                    stepDiv.addEventListener('click', () => this.toggleStep(i));
                    stepsDiv.appendChild(stepDiv);
                });
                row.appendChild(stepsDiv);
                grid.appendChild(row);
            }
            // Добавляем недостающий метод
            toggleStep(step) {
                this.pattern[step] = this.pattern[step] ? 0 : 1;
                this.renderGrid();
            }
            /* ------------------------------------------------------------------------
               PLAYBACK – воспроизведение
               ------------------------------------------------------------------------ */
            playPattern() {
                if (this.isPlaying) {
                    this.stopPattern();
                    return;
                }
                // Если контекст был «заморожен», разблокируем его
                if (this.audioContext && this.audioContext.state === 'suspended') {
                    this.audioContext.resume();
                }
                if (!this.audioReady) {
                    alert('Звук ещё не загружен. Пожалуйста, подождите.');
                    return;
                }
                this.isPlaying = true;
                document.getElementById('playButton').textContent = '⏹️ Стоп';
                const stepTime = (60 / this.bpm) / 4 * 1000; // 16‑ти нотный шаг
                this.intervalId = setInterval(() => {
                    this.highlightStep(this.currentStep);
                    // Играем snare если активен
                    if (this.pattern[this.currentStep]) {
                        this.playSound(this.track);
                    }
                    this.currentStep = (this.currentStep + 1) % this.steps;
                }, stepTime);
            }
            stopPattern() {
                this.isPlaying = false;
                clearInterval(this.intervalId);
                document.getElementById('playButton').textContent = '▶️ Проиграть';
                this.clearHighlights();
                this.currentStep = 0;
            }
            highlightStep(step) {
                this.clearHighlights();
                const cell = document.querySelector(`.step[data-step="${step}"]`);
                if (cell) cell.classList.add('playing');
            }
            clearHighlights() {
                const cells = document.querySelectorAll('.step.playing');
                cells.forEach(c => c.classList.remove('playing'));
            }
            clearPattern() {
                this.pattern = new Array(this.steps).fill(0);
                this.renderGrid();
                this.stopPattern();
            }
            /* ------------------------------------------------------------------------
               UI – обработчики элементов
               ------------------------------------------------------------------------ */
            setBPM(val) {
                this.bpm = val;
                if (this.isPlaying) {
                    this.stopPattern();
                    this.playPattern();
                }
            }
            setupEventListeners() {
                // BPM‑слайдер
                const bpmSlider = document.getElementById('bpm');
                const bpmVal = document.getElementById('bpmValue');
                if (bpmSlider && bpmVal) {
                    bpmSlider.addEventListener('input', () => {
                        this.setBPM(parseInt(bpmSlider.value, 10));
                        bpmVal.textContent = this.bpm;
                    });
                }
            }
        }
        /* ------------------------------------------------------------------------
           Инициализация после загрузки DOM
           ------------------------------------------------------------------------ */
        document.addEventListener('DOMContentLoaded', () => {
            window.drumSequencer = new DrumSequencer();
            // Глобальные функции для HTML
            window.playPattern = () => drumSequencer.playPattern();
            window.clearPattern = () => drumSequencer.clearPattern();
            // Привязываем обработчик к кнопке "Новый пример"
            document.getElementById('newQuestionBtn').addEventListener('click', newQuestion);
        });
    </script>
</body>
</html>