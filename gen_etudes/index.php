<!DOCTYPE html>
<html lang="ru" itemscope itemtype="https://schema.org/WebApplication">
<head>
	<?php require_once __DIR__ . '/../includes/seo.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/abcjs@6.5.2/abcjs-audio.min.css">
    <link rel="stylesheet" href="/assets/style.css?v=<?= filemtime(__DIR__ . '/../assets/style.css') ?>">
    
    <style>
        /* Панель управления */
        .etude-controls {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            margin-bottom: 20px;
            padding: 15px;
            background-color: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
        }
        .control-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 120px;
        }
        .control-group label {
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 0.9em;
            color: var(--text);
        }
        .control-group input,
        .control-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
            background-color: var(--bg);
            color: var(--text);
        }
        .control-group button {
            padding: 10px 20px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 22px;
        }
        .control-group button:hover {
            background-color: #3182ce;
        }

        /* Панель опций нотных групп */
        .note-options {
            margin-bottom: 20px;
            padding: 15px;
            background-color: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
        }
        .note-options h3 {
            margin-top: 0;
            color: var(--text);
            font-size: 1.1rem;
        }
        .checkbox-label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.9em;
            color: var(--text);
            cursor: pointer;
        }
        .checkbox-label input[type="checkbox"] {
            margin-right: 8px;
        }

        /* Область отображения нот */
        #paper {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 4px;
            background-color: var(--bg);
            text-align: center;
            min-height: 200px;
        }
        #paper svg {
            width: 100%;
            height: auto;
            max-width: 100%;
        }

        /* Элементы управления воспроизведением */
        #audio-controls {
            margin: 20px 0;
            text-align: center;
        }

        /* Сетка опций нот — теперь 3 колонки */
        .note-options-grid {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .column {
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex: 1;
            min-width: 180px;
            max-width: 220px;
        }

        .buttons-row {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .buttons-row button {
            padding: 8px 12px;
            background-color: var(--bg);
            border: 1px solid var(--border);
            border-radius: 4px;
            cursor: pointer;
            color: var(--text);
        }
        .buttons-row button:hover {
            background-color: var(--primary-light);
        }

        /* Кнопка скачивания PDF */
        #downloadPdfBtn {
            display: block;
            margin: 20px auto;
            padding: 12px 24px;
            background-color: #805ad5;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            border: none;
            cursor: pointer;
        }
        #downloadPdfBtn:hover {
            background-color: #6b46c1;
        }

        /* Адаптивность */
        @media (max-width: 768px) {
            .etude-controls,
            .note-options-grid {
                flex-direction: column;
                align-items: center;
            }
            .note-options-grid {
                align-items: flex-start;
            }
            .column {
                width: 100%;
                max-width: none;
            }
            .buttons-row {
                flex-direction: column;
            }
            .control-group {
                width: 100%;
                max-width: 300px;
            }
        }

        /* Стили для методики работы */
        .methodology {
            margin-top: 25px;
            padding: 20px;
            background-color: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            font-size: 0.95em;
            line-height: 1.6;
        }
        .methodology h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: var(--text);
            text-align: center;
            font-size: 1.2em;
        }
        .methodology ol {
            padding-left: 20px;
            margin: 10px 0;
        }
        .methodology li {
            margin-bottom: 10px;
        }

        /* Секция контента */
        .content-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }
        
        .content-section h2 {
            color: var(--text);
            margin-top: 30px;
            margin-bottom: 15px;
        }
        
        .content-section p {
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .content-section ul, 
        .content-section ol {
            margin-bottom: 20px;
            padding-left: 20px;
        }
        
        .content-section li {
            margin-bottom: 10px;
            line-height: 1.5;
        }
        
        .keywords {
            background-color: var(--card-bg);
            padding: 15px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            font-size: 0.85em;
            color: var(--text-secondary);
        }
        
        /* Стили курсора и подсветки */
        .highlight {
            fill: #0a9ecc !important;
        }
        .abcjs-cursor {
            stroke: red;
            stroke-width: 2;
        }
        
        .cursor-nav {
            margin: 15px 0;
            text-align: center;
        }
        .cursor-nav label {
            margin: 0 10px;
            user-select: none;
            font-size: 0.95em;
            color: var(--text);
        }
        
        @media (max-width: 768px) {
            .cursor-nav {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            .cursor-nav label {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../includes/header.php'; ?>

    <main class="container" itemprop="mainEntity">
        <h1 itemprop="name"><?= htmlspecialchars($title) ?></h1>
        
        <!-- ИНСТРУМЕНТ -->
        <div class="tool-container">
            <!-- Панель опций нотных групп -->
            <div class="note-options">
                <h3>Выбор ритмических фигур</h3>
                <div class="buttons-row">
                    <button id="clearAllBtn">Очистить</button>
                    <button id="selectAllBtn">Выбрать все</button>
                </div>

                <div class="note-options-grid">
                    <!-- Левый столбец -->
                    <div class="column">
                        <label class="checkbox-label">
                            <input type="checkbox" id="group-quarter" checked> Четверти
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" id="group-eighth-pair" checked> Восьмушки
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" id="group-sixteenth-quartet" checked> Шестнадцатые
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" id="group-eighth-sixteenth-pair"> Галоп
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" id="group-sixteenth-pair-eighth"> Обратный галоп
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" id="group-sixteenth-eighth-sixteenth"> Синкопа
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" id="group-eight-dot-sixteenth"> Пунктир
                        </label>
                    </div>

                    <!-- Средний столбец -->
                    <div class="column">
                        <label class="checkbox-label">
                            <input type="checkbox" id="group-Triplets"> Триоли
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" id="group-Sextoles"> Секстоли
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" id="group-triplet-then-eighth"> Секстоль + восьмая
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" id="group-eighth-then-triplet"> Восьмая + секстоль
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" id="group-sixteenth-then-triplet"> Шестнадцатые + секстоль
                        </label>
                    </div>

                    <!-- Правый (новый) столбец -->
                    <div class="column">
                        <label class="checkbox-label">
                            <input type="checkbox" id="group-QuarterZ" checked> Пауза
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" id="group-rest-eighth-note"> Пауза + восьмая
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" id="group-rest-sixteenth"> Пауза + 2 Шестнадцатые
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" id="group-rest-three-sixteenths"> Пауза + 3 шестнадцатых
                        </label>
                    </div>
                </div>
            </div>

            <!-- Панель управления -->
            <div class="etude-controls">
                <div class="control-group">
                    <label for="numMeasures">Количество тактов:</label>
                    <input type="number" id="numMeasures" min="1" max="32" value="8">
                </div>
                <div class="control-group">
                    <label for="timeSignature">Размер:</label>
                    <select id="timeSignature">
                        <option value="4/4">4/4</option>
                        <option value="3/4">3/4</option>
                        <option value="2/4">2/4</option>
                    </select>
                </div>
                <div class="control-group">
                    <label for="tempo">Темп (BPM):</label>
                    <input type="number" id="tempo" min="60" max="180" value="80">
                </div>
                <div class="control-group">
                    <button id="generateBtn">Сгенерировать этюд</button>
                </div>
            </div>
            
            <!-- Чекбоксы управления воспроизведением -->
            <div class="cursor-nav">
                <label>
                    <input type="checkbox" id="show-cursor"> Показывать курсор
                </label>
                <label>
                    <input type="checkbox" id="color-note" checked> Подсвечивать ноты
                </label>
            </div>

            <!-- Место для отображения нот -->
            <div id="paper"></div>

            <!-- Место для элементов управления воспроизведением -->
            <div id="audio-controls"></div>

            <!-- Кнопка Скачать в PDF -->
            <button id="downloadPdfBtn">📥 Скачать в PDF</button>
        </div>

        
    </main>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>

    <!-- Подключение библиотек -->
    <script src="https://cdn.jsdelivr.net/npm/abcjs@6.5.2/dist/abcjs-basic-min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    
    <script>
    // === Cursor Control ===
    var lastHighlighted = [];
    var showCursor = null;
    var colorNote = null;

    function CursorControl() {
        var self = this;

        self.onStart = function() {};
        self.onBeat = function() {};

        self.onEvent = function(ev) {
            // Подсветка нот
            lastHighlighted.forEach(el => el.classList.remove("highlight"));
            lastHighlighted = [];

            if (ev && ev.elements && colorNote && colorNote.checked) {
                ev.elements.forEach(note => {
                    note.forEach(el => {
                        el.classList.add("highlight");
                        lastHighlighted.push(el);
                    });
                });
            }

            // Курсор
            var cursor = document.querySelector("#paper svg .abcjs-cursor");
            if (!cursor) {
                var svg = document.querySelector("#paper svg");
                if (svg) {
                    cursor = document.createElementNS("http://www.w3.org/2000/svg", "line");
                    cursor.setAttribute("class", "abcjs-cursor");
                    svg.appendChild(cursor);
                }
            }

            if (cursor && showCursor && showCursor.checked && ev && ev.left != null) {
                cursor.setAttribute("x1", ev.left - 2);
                cursor.setAttribute("x2", ev.left - 2);
                cursor.setAttribute("y1", ev.top || 0);
                cursor.setAttribute("y2", (ev.top || 0) + (ev.height || 30));
            } else if (cursor) {
                cursor.setAttribute("x1", -10);
                cursor.setAttribute("x2", -10);
            }
        };

        self.onFinished = function() {
            lastHighlighted.forEach(el => el.classList.remove("highlight"));
            lastHighlighted = [];

            var cursor = document.querySelector("#paper svg .abcjs-cursor");
            if (cursor) {
                cursor.setAttribute("x1", -10);
                cursor.setAttribute("x2", -10);
            }
        };
    }

    // Ссылки на DOM-элементы
    const numMeasuresInput = document.getElementById('numMeasures');
    const timeSignatureSelect = document.getElementById('timeSignature');
    const tempoInput = document.getElementById('tempo');
    const generateBtn = document.getElementById('generateBtn');
    const paperDiv = document.getElementById('paper');
    const audioDiv = document.getElementById('audio-controls');

    // Ссылки на чекбоксы групп нот
    const checkboxQuarter = document.getElementById('group-quarter');
    const checkboxEighthPair = document.getElementById('group-eighth-pair');
    const checkboxSixteenthQuartet = document.getElementById('group-sixteenth-quartet');
    const checkboxSixteenthPairEighth = document.getElementById('group-sixteenth-pair-eighth');
    const checkboxEighthSixteenthPair = document.getElementById('group-eighth-sixteenth-pair');
    const checkboxSixteenthEighthSixteenth = document.getElementById('group-sixteenth-eighth-sixteenth');
    const checkboxEightDotSixteenth = document.getElementById('group-eight-dot-sixteenth');
    const checkboxQuarterZ = document.getElementById('group-QuarterZ');
    const checkboxTriplets = document.getElementById('group-Triplets');
    const checkboxSextoles = document.getElementById('group-Sextoles');
    const checkboxRestEighthNote = document.getElementById('group-rest-eighth-note');
    const checkboxRestSixteenth = document.getElementById('group-rest-sixteenth');
    const checkboxRestThreeSixteenths = document.getElementById('group-rest-three-sixteenths');
    const checkboxTripletThenEighth = document.getElementById('group-triplet-then-eighth');
    const checkboxEighthThenTriplet = document.getElementById('group-eighth-then-triplet');
    const checkboxSixteenthThenTriplet = document.getElementById('group-sixteenth-then-triplet');

    // Глобальные переменные для ABCJS
    let currentTuneObject = null;
    let synthControl = null;
    let midiBuffer = null;

    // Валидные паттерны для одной доли (1/4 нота) с описанием
    const allNotePatterns = [
        { id: 'quarter', pattern: 'c4', description: 'Четверть' },
        { id: 'eighth_pair', pattern: 'c2c2', description: 'Две восьмушки' },
        { id: 'sixteenth_quartet', pattern: 'cccc', description: 'Четыре шестнадцатые' },
        { id: 'sixteenth_pair_eighth', pattern: 'ccc2', description: '16,16,8' },
        { id: 'eighth_sixteenth_pair', pattern: 'c2cc', description: '8,16,16' },
        { id: 'sixteenth_eighth_sixteenth', pattern: 'cc2c', description: '16,8,16' },
        { id: 'eight_dot_sixteenth', pattern: 'c3c', description: '8,.,16' },
        { id: 'QuarterZ', pattern: 'z4', description: 'Пауза' },
        { id: 'Triplets', pattern: '(3c2c2c2', description: 'Триоль' },
        { id: 'Sextoles', pattern: '(3ccc(3ccc', description: 'Sextoles' },
        { id: 'rest_eighth_note', pattern: 'z2c2', description: 'z2 c2 (Пауза + восьмая)' },
        { id: 'rest-sixteenth', pattern: 'z2cc', description: 'z2cc (Пауза + шестнадцатые)' },
        { id: 'rest-three-sixteenths', pattern: 'zccc', description: 'zccc (Пауза + 3 шестнадцатых)' },
        { id: 'triplet-then-eighth', pattern: '(3cccc2', description: '(3cccc2 (Триоль + восьмая)' },
        { id: 'eighth-then-triplet', pattern: 'c2(3ccc', description: 'c2(3ccc (Восьмая + триоль)' },
        { id: 'sixteenth-then-triplet', pattern: 'cc (3ccc', description: 'cc(3ccc (шестнадцатые + триоль)' },
    ];

    // Функция для получения текущих активных паттернов на основе чекбоксов
    function getActivePatterns() {
        const activePatterns = [];
        if (checkboxQuarter.checked) activePatterns.push(allNotePatterns.find(p => p.id === 'quarter').pattern);
        if (checkboxEighthPair.checked) activePatterns.push(allNotePatterns.find(p => p.id === 'eighth_pair').pattern);
        if (checkboxSixteenthQuartet.checked) activePatterns.push(allNotePatterns.find(p => p.id === 'sixteenth_quartet').pattern);
        if (checkboxSixteenthPairEighth.checked) activePatterns.push(allNotePatterns.find(p => p.id === 'sixteenth_pair_eighth').pattern);
        if (checkboxEighthSixteenthPair.checked) activePatterns.push(allNotePatterns.find(p => p.id === 'eighth_sixteenth_pair').pattern);
        if (checkboxSixteenthEighthSixteenth.checked) activePatterns.push(allNotePatterns.find(p => p.id === 'sixteenth_eighth_sixteenth').pattern);
        if (checkboxEightDotSixteenth.checked) activePatterns.push(allNotePatterns.find(p => p.id === 'eight_dot_sixteenth').pattern);
        if (checkboxQuarterZ.checked) activePatterns.push(allNotePatterns.find(p => p.id === 'QuarterZ').pattern);
        if (checkboxTriplets.checked) activePatterns.push(allNotePatterns.find(p => p.id === 'Triplets').pattern);
        if (checkboxSextoles.checked) activePatterns.push(allNotePatterns.find(p => p.id === 'Sextoles').pattern);
        if (checkboxRestEighthNote.checked) activePatterns.push(allNotePatterns.find(p => p.id === 'rest_eighth_note').pattern);
        if (checkboxRestSixteenth.checked) activePatterns.push(allNotePatterns.find(p => p.id === 'rest-sixteenth').pattern);
        if (checkboxRestThreeSixteenths.checked) activePatterns.push(allNotePatterns.find(p => p.id === 'rest-three-sixteenths').pattern);
        if (checkboxTripletThenEighth.checked) activePatterns.push(allNotePatterns.find(p => p.id === 'triplet-then-eighth').pattern);
        if (checkboxEighthThenTriplet.checked) activePatterns.push(allNotePatterns.find(p => p.id === 'eighth-then-triplet').pattern);
        if (checkboxSixteenthThenTriplet.checked) activePatterns.push(allNotePatterns.find(p => p.id === 'sixteenth-then-triplet').pattern);
        return activePatterns;
    }

    // Функция генерации одного такта
    function generateMeasure(timeSig) {
        const [beats] = timeSig.split('/').map(Number);
        const targetBeats = beats;
        let generatedBeats = 0;
        let measureContent = '';
        const activePatterns = getActivePatterns();

        if (activePatterns.length === 0) {
            return 'z16';
        }

        while (generatedBeats < targetBeats) {
            const pattern = activePatterns[Math.floor(Math.random() * activePatterns.length)];
            const patternBeats = 1;

            if (generatedBeats + patternBeats <= targetBeats) {
                measureContent += pattern + ' ';
                generatedBeats += patternBeats;
            } else {
                break;
            }
        }

        return measureContent.trim();
    }

    // Функция генерации ABC строки для этюда
    function generateEtudeABC(numMeasures, timeSig, tempo) {
        let abcString = `X:1
L:1/16
M:${timeSig}
K:C clef=perc
Q:1/4=${tempo}
V:ALL stem=up
%%percmap c acoustic-snare
%%percmap F acoustic-bass-drum
%%percmap g closed-hi-hat x
%%percmap ^g open-hi-hat
`;

        let measures = [];
        for (let i = 0; i < numMeasures; i++) {
            measures.push(generateMeasure(timeSig));
        }

        const measuresPerLine = 4;
        for (let i = 0; i < measures.length; i += measuresPerLine) {
            const lineMeasures = measures.slice(i, i + measuresPerLine);
            abcString += lineMeasures.join(' | ') + ' | \n';
        }

        abcString += '%%\n';
        return abcString;
    }

    // Функция для отображения и настройки воспроизведения ABC строки
    function loadEtude(abcString) {
        paperDiv.innerHTML = '';
        audioDiv.innerHTML = '';

        const renderParams = {
            responsive: "resize",
        };

        try {
            const visualObj = ABCJS.renderAbc(paperDiv, abcString, renderParams);
            if (!visualObj || visualObj.length === 0) {
                throw new Error("Ошибка рендеринга.");
            }
            currentTuneObject = visualObj[0];

            if (ABCJS.synth.supportsAudio()) {
                const cursorControl = new CursorControl();
                synthControl = new ABCJS.synth.SynthController();
                const controlParams = {
                    displayLoop: false,
                    displayRestart: false,
                    displayPlay: true,
                    displayProgress: false,
                    displayClock: false,
                    displayWarp: false,
                };

                synthControl.load(audioDiv, cursorControl, controlParams);
                synthControl.disable(true);

                midiBuffer = new ABCJS.synth.CreateSynth();
                const beatsPerMeasure = parseInt(timeSignatureSelect.value.split('/')[0]);
                const tempoValue = parseInt(tempoInput.value);
                const millisecondsPerMeasure = (beatsPerMeasure / tempoValue) * 60 * 1000;

                midiBuffer.init({
                    visualObj: currentTuneObject,
                    millisecondsPerMeasure: millisecondsPerMeasure,
                })
                .then(() => synthControl.setTune(currentTuneObject, false))
                .then(() => synthControl.disable(false))
                .catch(e => {
                    console.error("Ошибка аудио:", e);
                    audioDiv.innerHTML = `<p>Ошибка загрузки аудио.</p>`;
                    if (synthControl) synthControl.disable(true);
                    midiBuffer = null;
                });
            }
        } catch (e) {
            console.error("Ошибка:", e);
            paperDiv.innerHTML = `<p>Ошибка отображения: ${e.message}</p>`;
        }
    }

    // Обработчик нажатия на кнопку генерации с полной проверкой
    generateBtn.addEventListener('click', function() {
        let numMeasures = parseInt(numMeasuresInput.value, 10);
        const timeSig = timeSignatureSelect.value;
        let tempo = parseInt(tempoInput.value, 10);

        if (isNaN(numMeasures) || numMeasures < 1) {
            alert('Количество тактов должно быть от 1 до 32.');
            return;
        }
        if (numMeasures > 32) {
            numMeasures = 32;
            numMeasuresInput.value = 32;
        }

        if (isNaN(tempo) || tempo < 60) {
            alert('Темп должен быть от 60 до 180 BPM.');
            return;
        }
        if (tempo > 180) {
            tempo = 180;
            tempoInput.value = 180;
        }

        const abcString = generateEtudeABC(numMeasures, timeSig, tempo);
        loadEtude(abcString);
    });

    // Инициализация кнопок "Очистить" и "Выбрать все"
    document.addEventListener('DOMContentLoaded', function() {
        // Инициализация чекбоксов курсора
        showCursor = document.getElementById("show-cursor");
        colorNote = document.getElementById("color-note");
        
        const clearAllBtn = document.getElementById('clearAllBtn');
        const selectAllBtn = document.getElementById('selectAllBtn');
        const checkboxes = document.querySelectorAll('.note-options input[type="checkbox"]');

        clearAllBtn.addEventListener('click', () => checkboxes.forEach(cb => cb.checked = false));
        selectAllBtn.addEventListener('click', () => checkboxes.forEach(cb => cb.checked = true));

        // Генерация этюда по умолчанию при загрузке
        generateBtn.click();
    });

    // Обработчик кнопки "Скачать в PDF"
    document.getElementById('downloadPdfBtn').addEventListener('click', function () {
        const numMeasures = numMeasuresInput.value;
        const timeSig = timeSignatureSelect.value;
        const tempo = tempoInput.value;
        
        const svgElement = paperDiv.querySelector('svg');
        if (!svgElement) {
            alert('Нет нот для сохранения!');
            return;
        }
        
        const svgData = new XMLSerializer().serializeToString(svgElement);
        const svgBlob = new Blob([svgData], {type: 'image/svg+xml;charset=utf-8'});
        const svgUrl = URL.createObjectURL(svgBlob);
        
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        
        const bbox = svgElement.getBBox();
        canvas.width = Math.max(bbox.width * 2, 800);
        canvas.height = bbox.height * 2;
        
        const img = new Image();
        img.onload = function() {
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a4'
            });
            
            pdf.setFontSize(16);
            pdf.text('Etude for Drums', 105, 15, null, null, 'center');
            pdf.setFontSize(12);
            pdf.text(`Measures: ${numMeasures} | Time: ${timeSig} | Tempo: ${tempo} BPM`, 105, 25, null, null, 'center');
            
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = 180;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            const yPos = 35;
            
            if (yPos + imgHeight > 250) {
                pdf.addPage();
                pdf.addImage(imgData, 'PNG', 15, 15, imgWidth, imgHeight);
            } else {
                pdf.addImage(imgData, 'PNG', 15, yPos, imgWidth, imgHeight);
            }
            
            pdf.save(`Etude_${numMeasures}m_${timeSig}_${tempo}BPM.pdf`);
            URL.revokeObjectURL(svgUrl);
        };
        
        img.onerror = function() {
            alert('Ошибка при создании PDF. Попробуйте еще раз.');
            URL.revokeObjectURL(svgUrl);
        };
        
        img.src = svgUrl;
    });
    </script>
</body>
</html>
