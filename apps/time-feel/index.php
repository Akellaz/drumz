<!DOCTYPE html>
<html lang="ru" itemscope itemtype="https://schema.org/WebApplication">
<head>
	<?php require_once __DIR__ . '/../../includes/seo.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/abcjs@6.5.2/abcjs-audio.min.css">
    <link rel="stylesheet" href="/assets/style.css?v=<?= filemtime(__DIR__ . '/../../assets/style.css') ?>">
    
    <style>
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
        .control-group input[type="number"],
        .control-group input[type="range"] {
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
        .presets {
            display: flex;
            gap: 8px;
            margin-top: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .presets button {
            padding: 6px 10px;
            font-size: 0.85em;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 6px;
            cursor: pointer;
            color: var(--text);
        }
        .presets button:hover {
            background: var(--primary-light);
        }
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
            text-align: center;
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
            justify-content: center;
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
        #audio-controls {
            margin: 20px 0;
            text-align: center;
        }
        #probValue {
            margin-top: 5px;
            font-size: 0.9em;
            color: var(--text-light);
        }
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

        /* Стили курсора и подсветки */
        .highlight {
            fill: #0a9ecc !important;
        }
        .abcjs-cursor {
            stroke: red;
            stroke-width: 2;
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
        .intro-text {
            text-align: center;
            margin-bottom: 20px;
            color: var(--text-light);
        }
        .intro-text small {
            font-size: 0.85em;
        }
        
        @media (max-width: 768px) {
            .etude-controls, .note-options-grid {
                flex-direction: column;
                align-items: center;
            }
            .column {
                width: 100%;
                max-width: none;
            }
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
    <?php require_once __DIR__ . '/../../includes/header.php'; ?>

    <main class="container" itemprop="mainEntity">
        <h1 itemprop="name"><?= htmlspecialchars($title) ?></h1>
        
        <!-- ИНСТРУМЕНТ -->
        <div class="tool-container">
            <div class="intro-text">
                <p>Выберите количество тактов, темп и частоту нот.<br>
                Нажмите «Создать», затем ▶️ — и постарайтесь идти в ногу со временем.<br>
                <small>(Оно, кстати, не ждёт.)</small></p>
            </div>

            <!-- Панель выбора паттернов -->
            <div class="note-options">
                <h3>Разрешённые паттерны</h3>
                <div class="buttons-row">
                    <button id="clearAllBtn">Очистить</button>
                    <button id="selectAllBtn">Выбрать все</button>
                </div>
                <div class="note-options-grid">
                    <div class="column">
                        <label class="checkbox-label">
                            <input type="checkbox" id="pat-c4" checked> Четверти
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" id="pat-c2c2"> Две восьмушки
                        </label>
                    </div>
                    <div class="column">
                        <label class="checkbox-label">
                            <input type="checkbox" id="pat-z2c2"> На «и»
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" id="pat-z2cc"> На «ди-ми»
                        </label>
                    </div>
                    <div class="column">
                        <label class="checkbox-label">
                            <input type="checkbox" id="pat-zccc"> На «ка-да-ми»
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" id="pat-z4" checked> Только пауза
                        </label>
                    </div>
                </div>
            </div>

            <div class="etude-controls">
                <div class="control-group">
                    <label for="numMeasures">Тактов:</label>
                    <input type="number" id="numMeasures" min="1" max="32" value="4">
                </div>
                <div class="control-group">
                    <label for="tempo">Темп (BPM):</label>
                    <input type="number" id="tempo" min="20" max="180" value="80">
                </div>
                <div class="control-group">
                    <label for="patternProbability">Частота нот:</label>
                    <input type="range" id="patternProbability" min="0" max="100" value="40" step="5">
                    <output id="probValue">40%</output>
                    <div class="presets">
                        <button type="button" data-value="10">Тишина</button>
                        <button type="button" data-value="40">Покой</button>
                        <button type="button" data-value="90">Плотность</button>
                    </div>
                </div>
                <div class="control-group">
                    <button id="generateBtn">Создать</button>
                </div>
            </div>

            <!-- Чекбоксы управления воспроизведением (без "скрыть такты") -->
            <div class="cursor-nav">
                <label>
                    <input type="checkbox" id="show-cursor"> Показывать курсор
                </label>
                <label>
                    <input type="checkbox" id="color-note" checked> Подсвечивать ноты
                </label>
            </div>

            <div id="audio-controls"></div>
            <div id="paper"></div>
            <button id="downloadPdfBtn">📥 Скачать в PDF</button>
        </div>
    </main>

    <?php require_once __DIR__ . '/../../includes/footer.php'; ?>

    <!-- Локальная версия ABCjs -->
    <script src="https://cdn.jsdelivr.net/npm/abcjs@6.5.2/dist/abcjs-basic-min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
    // === Cursor Control (без скрытия тактов) ===
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

    // === Основной код ===
    const numMeasuresInput = document.getElementById('numMeasures');
    const tempoInput = document.getElementById('tempo');
    const probSlider = document.getElementById('patternProbability');
    const probOutput = document.getElementById('probValue');
    const generateBtn = document.getElementById('generateBtn');
    const paperDiv = document.getElementById('paper');
    const audioDiv = document.getElementById('audio-controls');

    let currentTuneObject = null;
    let synthControl = null;
    let midiBuffer = null;

    // Чекбоксы паттернов
    const patChecks = {
        'c4': document.getElementById('pat-c4'),
        'c2c2': document.getElementById('pat-c2c2'),
        'z2c2': document.getElementById('pat-z2c2'),
        'z2cc': document.getElementById('pat-z2cc'),
        'zccc': document.getElementById('pat-zccc'),
        'z4': document.getElementById('pat-z4')
    };

    probSlider.addEventListener('input', () => {
        probOutput.textContent = probSlider.value + '%';
    });

    document.querySelectorAll('.presets button').forEach(btn => {
        btn.addEventListener('click', () => {
            const val = parseInt(btn.dataset.value);
            probSlider.value = val;
            probOutput.textContent = val + '%';
        });
    });

    function getActivePatterns() {
        const active = [];
        if (patChecks['c4'].checked) active.push('c4');
        if (patChecks['c2c2'].checked) active.push('c2c2');
        if (patChecks['z2c2'].checked) active.push('z2c2');
        if (patChecks['z2cc'].checked) active.push('z2cc');
        if (patChecks['zccc'].checked) active.push('zccc');
        if (patChecks['z4'].checked) active.push('z4');
        return active.length ? active : ['z4'];
    }

    function generateBeat(probability, patterns) {
        if (Math.random() * 100 < probability) {
            const nonRest = patterns.filter(p => p !== 'z4');
            if (nonRest.length > 0) {
                return nonRest[Math.floor(Math.random() * nonRest.length)];
            }
        }
        return 'z4';
    }

    function generateMeasure(probability, patterns) {
        const beats = [];
        for (let i = 0; i < 4; i++) {
            beats.push(generateBeat(probability, patterns));
        }
        return beats.join(' ');
    }

    function generateNonEmptyMeasure(patterns) {
        const nonRest = patterns.filter(p => p !== 'z4');
        if (nonRest.length === 0) return "c4 z4 z4 z4";
        const position = Math.floor(Math.random() * 4);
        const pattern = nonRest[Math.floor(Math.random() * nonRest.length)];
        const beats = ['z4', 'z4', 'z4', 'z4'];
        beats[position] = pattern;
        return beats.join(' ');
    }

    function generateEtudeABC(numMeasures, tempo, probability) {
        let abc = `X:1
L:1/16
M:4/4
K:C clef=perc
Q:1/4=${tempo}
V:ALL stem=up
%%percmap c acoustic-snare
%%percmap F acoustic-bass-drum
%%percmap g closed-hi-hat x
%%percmap ^g open-hi-hat
`;

        const patterns = getActivePatterns();
        const measures = [];

        if (numMeasures === 1) {
            measures.push("c4 c4 c4 c4");
        } else {
            measures.push("c4 c4 c4 c4");
            for (let i = 1; i < numMeasures - 1; i++) {
                measures.push(generateMeasure(probability, patterns));
            }
            measures.push(generateNonEmptyMeasure(patterns));
        }

        for (let i = 0; i < measures.length; i += 4) {
            abc += measures.slice(i, i + 4).join(' | ') + ' | \n';
        }
        abc += '%%\n';
        return abc;
    }

    function loadEtude(abcString) {
        paperDiv.innerHTML = '';
        audioDiv.innerHTML = '';

        try {
            const vo = ABCJS.renderAbc(paperDiv, abcString, { responsive: "resize" });
            if (!vo || vo.length === 0) throw new Error("Render failed");
            currentTuneObject = vo[0];

            if (ABCJS.synth.supportsAudio()) {
                const cursorControl = new CursorControl();
                synthControl = new ABCJS.synth.SynthController();
                synthControl.load(audioDiv, cursorControl, {
                    displayLoop: false,
                    displayRestart: false,
                    displayPlay: true,
                    displayProgress: false,
                    displayClock: false,
                    displayWarp: false,
                });
                synthControl.disable(true);

                const msPerMeasure = (4 / parseInt(tempoInput.value)) * 60 * 1000;
                midiBuffer = new ABCJS.synth.CreateSynth();
                midiBuffer.init({
                    visualObj: currentTuneObject,
                    millisecondsPerMeasure: msPerMeasure
                })
                .then(() => {
                    synthControl.setTune(currentTuneObject, false);
                    synthControl.disable(false);
                })
                .catch(e => {
                    console.error("Audio error:", e);
                    audioDiv.innerHTML = `<p style="color:var(--danger)">Ошибка аудио.</p>`;
                    if (synthControl) synthControl.disable(true);
                });
            }
        } catch (e) {
            console.error(e);
            paperDiv.innerHTML = `<p style="color:var(--danger)">Ошибка: ${e.message}</p>`;
        }
    }

    generateBtn.addEventListener('click', () => {
        const num = parseInt(numMeasuresInput.value);
        const tempo = parseInt(tempoInput.value);
        const prob = parseFloat(probSlider.value);

        if (isNaN(num) || num < 1 || num > 32) {
            alert('Количество тактов: от 1 до 32.');
            return;
        }
        if (isNaN(tempo) || tempo < 20 || tempo > 180) {
            alert('Темп: от 20 до 180 BPM.');
            return;
        }

        const abc = generateEtudeABC(num, tempo, prob);
        loadEtude(abc);
    });

    document.addEventListener('DOMContentLoaded', () => {
        // Инициализация чекбоксов курсора
        showCursor = document.getElementById("show-cursor");
        colorNote = document.getElementById("color-note");

        const checkboxes = document.querySelectorAll('.note-options input[type="checkbox"]');
        document.getElementById('clearAllBtn').onclick = () => checkboxes.forEach(c => c.checked = false);
        document.getElementById('selectAllBtn').onclick = () => checkboxes.forEach(c => c.checked = true);
        generateBtn.click();
    });

    document.getElementById('downloadPdfBtn').onclick = () => {
        const svg = paperDiv.querySelector('svg');
        if (!svg) return alert('Нет нот для сохранения!');
        const numMeasures = numMeasuresInput.value;
        const tempo = tempoInput.value;

        const s = new XMLSerializer();
        const url = URL.createObjectURL(new Blob([s.serializeToString(svg)], {type: 'image/svg+xml'}));
        const img = new Image();
        img.onload = () => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const bbox = svg.getBBox();
            canvas.width = Math.max(bbox.width * 2, 800);
            canvas.height = bbox.height * 2;
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
            pdf.setFontSize(16);
            pdf.text('Тренажёр «Чувство времени»', 105, 15, null, null, 'center');
            pdf.setFontSize(12);
            pdf.text(`Тактов: ${numMeasures} | Темп: ${tempo} BPM`, 105, 25, null, null, 'center');
            const imgData = canvas.toDataURL('image/png');
            const w = 180;
            const h = (canvas.height * w) / canvas.width;
            pdf.addImage(imgData, 'PNG', 15, 35, w, h);
            pdf.save(`Chuvstvo_vremeni_${numMeasures}t_${tempo}BPM.pdf`);
            URL.revokeObjectURL(url);
        };
        img.onerror = () => {
            alert('Ошибка создания PDF.');
            URL.revokeObjectURL(url);
        };
        img.src = url;
    };
    </script>
</body>
</html>