<!DOCTYPE html>
<html lang="ru" itemscope itemtype="https://schema.org/WebApplication">
<head>
	<?php require_once __DIR__ . '/../../includes/seo.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/abcjs@6.5.2/abcjs-audio.min.css">
    <link rel="stylesheet" href="/assets/style.css?v=<?= filemtime(__DIR__ . '/../../assets/style.css') ?>">
    
    <style>
        /* Панель управления */
        .rhythm-controls {
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

        /* Кнопка сброса */
        #resetBtn {
            padding: 10px 15px;
            background-color: #e2e8f0;
            color: #4a5568;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        #resetBtn:hover {
            background-color: #cbd5e0;
        }

        /* Панель регуляторов частоты */
        .rhythm-options {
            margin-bottom: 20px;
            padding: 15px;
            background-color: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
        }
        .rhythm-options h3 {
            margin-top: 0;
            color: var(--text);
            font-size: 1.1rem;
        }

        /* Горизонтальное расположение */
        .rhythm-grid {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .rhythm-header {
            display: flex;
            justify-content: space-between;
            padding-left: 60px;
            padding-right: 60px;
        }

        .rhythm-header-item {
            width: 60px;
            text-align: center;
            font-weight: bold;
            font-size: 1.2em;
            color: var(--text);
        }

        .strong-beat {
            background-color: rgba(128, 128, 128, 0.12);
            border-radius: 4px;
            padding: 2px 0;
        }

        .strong-beat-bg {
            background-color: rgba(128, 128, 128, 0.12);
            border-radius: 8px;
            padding: 5px 0;
            width: 100%;
        }

        .rhythm-controls-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-left: 60px;
            padding-right: 60px;
        }

        .rhythm-control-item {
            width: 60px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            position: relative;
        }

        .control-label {
            font-size: 0.8em;
            color: var(--text);
            text-align: center;
        }

        .vertical-slider {
            -webkit-appearance: slider-vertical;
            width: 16px;
            height: 100px;
            padding: 0 5px;
            background: var(--border);
            border-radius: 3px;
            outline: none;
        }

        .vertical-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            background: var(--primary);
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .vertical-slider::-moz-range-thumb {
            width: 20px;
            height: 20px;
            background: var(--primary);
            border: 2px solid white;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .control-value {
            font-size: 0.8em;
            color: var(--text);
            font-weight: bold;
            text-align: center;
            min-width: 40px;
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

        .epigraph {
            text-align: center;
            margin: 20px 0;
            font-style: italic;
            color: var(--text);
            opacity: 0.9;
            font-size: 0.95em;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            padding: 15px 0;
        }

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

        /* === Курсор и подсветка === */
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
            margin: 0 12px;
            user-select: none;
            font-size: 0.95em;
            color: var(--text);
        }

        @media (max-width: 768px) {
            .rhythm-controls,
            .rhythm-options-grid {
                flex-direction: column;
                align-items: center;
            }
            .control-group {
                width: 100%;
                max-width: 300px;
            }
            .rhythm-header,
            .rhythm-controls-row {
                padding-left: 30px;
                padding-right: 30px;
            }
            .rhythm-header-item,
            .rhythm-control-item {
                width: 40px;
            }
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../../includes/header.php'; ?>

    <main class="container" itemprop="mainEntity">
        <h1 itemprop="name"><?= htmlspecialchars($title) ?></h1>
        
        <div class="tool-container">
            <div class="rhythm-controls">
                <div class="control-group">
                    <label for="numMeasures">Количество тактов:</label>
                    <input type="number" id="numMeasures" min="1" max="32" value="2">
                </div>
                <div class="control-group">
                    <label for="timeSignature">Размер:</label>
                    <select id="timeSignature">
                        <option value="4/4">4/4</option>
                    </select>
                </div>
                <div class="control-group">
                    <label for="tempo">Темп (BPM):</label>
                    <input type="number" id="tempo" min="60" max="180" value="80">
                </div>
            </div>
<div id="paper"></div>
<div id="audio-controls"></div>
            <div class="rhythm-controls">
                <div class="control-group">
                    <button id="generateBtn">Сгенерировать ритм</button>
                </div>
                <div class="control-group">
                    <button id="resetBtn">🔄 Сброс</button>
                </div>
            </div>
            <div class="rhythm-options">
                <div class="rhythm-grid">
                    <div class="rhythm-header">
                        <div class="rhythm-header-item strong-beat">1</div>
                        <div class="rhythm-header-item">+</div>
                        <div class="rhythm-header-item strong-beat">2</div>
                        <div class="rhythm-header-item">+</div>
                        <div class="rhythm-header-item strong-beat">3</div>
                        <div class="rhythm-header-item">+</div>
                        <div class="rhythm-header-item strong-beat">4</div>
                        <div class="rhythm-header-item">+</div>
                    </div>
                    
                    <div class="rhythm-controls-row">
                        <div class="rhythm-control-item strong-beat-bg">
                            <div class="control-label">s</div>
                            <input type="range" class="vertical-slider" id="snare1" min="0" max="1" step="0.05" value="0.9" orient="vertical">
                            <div class="control-value" id="snare1Value">90%</div>
                            <div class="control-label">k</div>
                            <input type="range" class="vertical-slider" id="kick1" min="0" max="1" step="0.05" value="0.9" orient="vertical">
                            <div class="control-value" id="kick1Value">90%</div>
                        </div>
                        <div class="rhythm-control-item">
                            <div class="control-label">s</div>
                            <input type="range" class="vertical-slider" id="snare2" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                            <div class="control-value" id="snare2Value">30%</div>
                            <div class="control-label">k</div>
                            <input type="range" class="vertical-slider" id="kick2" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                            <div class="control-value" id="kick2Value">30%</div>
                        </div>
                        <div class="rhythm-control-item strong-beat-bg">
                            <div class="control-label">s</div>
                            <input type="range" class="vertical-slider" id="snare3" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                            <div class="control-value" id="snare3Value">30%</div>
                            <div class="control-label">k</div>
                            <input type="range" class="vertical-slider" id="kick3" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                            <div class="control-value" id="kick3Value">30%</div>
                        </div>
                        <div class="rhythm-control-item">
                            <div class="control-label">s</div>
                            <input type="range" class="vertical-slider" id="snare4" min="0" max="1" step="0.05" value="0.9" orient="vertical">
                            <div class="control-value" id="snare4Value">90%</div>
                            <div class="control-label">k</div>
                            <input type="range" class="vertical-slider" id="kick4" min="0" max="1" step="0.05" value="0.9" orient="vertical">
                            <div class="control-value" id="kick4Value">90%</div>
                        </div>
                        <div class="rhythm-control-item strong-beat-bg">
                            <div class="control-label">s</div>
                            <input type="range" class="vertical-slider" id="snare5" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                            <div class="control-value" id="snare5Value">30%</div>
                            <div class="control-label">k</div>
                            <input type="range" class="vertical-slider" id="kick5" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                            <div class="control-value" id="kick5Value">30%</div>
                        </div>
                        <div class="rhythm-control-item">
                            <div class="control-label">s</div>
                            <input type="range" class="vertical-slider" id="snare6" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                            <div class="control-value" id="snare6Value">30%</div>
                            <div class="control-label">k</div>
                            <input type="range" class="vertical-slider" id="kick6" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                            <div class="control-value" id="kick6Value">30%</div>
                        </div>
                        <div class="rhythm-control-item strong-beat-bg">
                            <div class="control-label">s</div>
                            <input type="range" class="vertical-slider" id="snare7" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                            <div class="control-value" id="snare7Value">30%</div>
                            <div class="control-label">k</div>
                            <input type="range" class="vertical-slider" id="kick7" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                            <div class="control-value" id="kick7Value">30%</div>
                        </div>
                        <div class="rhythm-control-item">
                            <div class="control-label">s</div>
                            <input type="range" class="vertical-slider" id="snare8" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                            <div class="control-value" id="snare8Value">30%</div>
                            <div class="control-label">k</div>
                            <input type="range" class="vertical-slider" id="kick8" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                            <div class="control-value" id="kick8Value">30%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Курсор и подсветка -->
            <div class="cursor-nav">
                <label>
                    <input type="checkbox" id="show-cursor" checked> Показывать курсор
                </label>
                <label>
                    <input type="checkbox" id="color-note" checked> Подсвечивать ноты
                </label>
            </div>



            
            
            <button id="downloadPdfBtn">📥 Скачать в PDF</button>
        </div>
        
        <div class="content-section">
            <div class="seo-description" itemprop="description">
                <p><?= htmlspecialchars($description) ?></p>
            </div>
            
            <section class="tool-explanation">
                <h2>О генераторе ритмов для барабанов</h2>
                <p>Интерактивный генератор ритмов для барабанов - это мощный инструмент, который помогает развить чувство времени и ритмическую устойчивость. С помощью настройки вероятности ударов на каждой восьмой доле вы можете создавать уникальные ритмы и тренировать внутренний метроном.</p>
                <p>Этот инструмент идеально подходит для барабанщиков любого уровня - от новичков до профессионалов. Он позволяет гибко настраивать сложность ритмических упражнений и отслеживать прогресс в развитии ритмического слуха.</p>
                <p>Генератор ритмов особенно полезен для тех, кто хочет улучшить своё чувство грува, развить внутренний метроном и научиться играть стабильно в различных темпах и стилях.</p>
            </section>
            
            <section class="instructions">
                <h2>Как использовать генератор ритмов</h2>
                <ol>
                    <li><strong>Настройте параметры:</strong> выберите количество тактов, темп и размер в верхней панели управления</li>
                    <li><strong>Отрегулируйте вероятность:</strong> используйте вертикальные слайдеры для настройки вероятности ударов бочки (k) и малого барабана (s) на каждой доле</li>
                    <li><strong>Сгенерируйте ритм:</strong> нажмите кнопку "Сгенерировать ритм" для создания нового ритмического паттерна</li>
                    <li><strong>Практикуйтесь:</strong> слушайте воспроизведение, повторяйте ритм и скачивайте PDF для дальнейшей практики</li>
                    <li><strong>Экспериментируйте:</strong> изменяйте параметры и создавайте различные ритмы для развития навыков</li>
                </ol>
            </section>
            
            <section class="benefits">
                <h2>Преимущества тренировки с генератором</h2>
                <ul>
                    <li><strong>Развитие внутреннего метронома:</strong> тренировка помогает развить чувство стабильного темпа</li>
                    <li><strong>Улучшение ритмической точности:</strong> работа с вероятностями учит играть точно в такт</li>
                    <li><strong>Формирование чувства грува:</strong> практика с различными ритмическими паттернами развивает музыкальность</li>
                    <li><strong>Индивидуальная настройка сложности:</strong> возможность адаптировать упражнения под свой уровень</li>
                    <li><strong>Практика с различными стилевыми элементами:</strong> работа с роковыми, джазовыми и другими ритмами</li>
                    <li><strong>Отслеживание прогресса:</strong> возможность сохранять и сравнивать различные ритмы</li>
                </ul>
            </section>
            
            <section class="methodology">
                <h2>Методика работы с генератором</h2>
                <ol>
                    <li><strong>Начальный уровень:</strong> начните с высокой вероятностью ударов (80-90%) и простыми паттернами</li>
                    <li><strong>Средний уровень:</strong> экспериментируйте с вероятностями 50-70% для развития координации</li>
                    <li><strong>Продвинутый уровень:</strong> используйте низкие вероятности (20-40%) для сложных ритмических упражнений</li>
                    <li><strong>Регулярная практика:</strong> используйте генератор ежедневно по 15-20 минут</li>
                    <li><strong>Разнообразие:</strong> регулярно генерируйте новые ритмы для поддержания интереса</li>
                </ol>
            </section>
            
            <section class="keywords">
                <h2>Ключевые слова и темы</h2>
                <p>генератор ритмов, тренажёр барабанов, интерактивный инструмент, развитие ритма, внутренний метроном, чувство времени, барабанные упражнения, ритмическая устойчивость, обучение барабанам, drum machine, ритмический слух, генератор барабанных ритмов, онлайн тренажёр ритма, развитие координации барабанщика, упражнения для барабанов, ритмические паттерны, барабанный генератор, музыкальный тренажёр, ритмическая практика, развитие грува</p>
            </section>
        </div>
    </main>

    <?php require_once __DIR__ . '/../../includes/footer.php'; ?>

    <!-- Локальная ABCjs -->
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

    // === Основной скрипт (вместо gen_rhythms_v8.js) ===
    const numMeasuresInput = document.getElementById('numMeasures');
    const timeSignatureSelect = document.getElementById('timeSignature');
    const tempoInput = document.getElementById('tempo');
    const generateBtn = document.getElementById('generateBtn');
    const paperDiv = document.getElementById('paper');
    const audioDiv = document.getElementById('audio-controls');

    const snareSliders = [
        document.getElementById('snare1'), document.getElementById('snare2'),
        document.getElementById('snare3'), document.getElementById('snare4'),
        document.getElementById('snare5'), document.getElementById('snare6'),
        document.getElementById('snare7'), document.getElementById('snare8')
    ];

    const kickSliders = [
        document.getElementById('kick1'), document.getElementById('kick2'),
        document.getElementById('kick3'), document.getElementById('kick4'),
        document.getElementById('kick5'), document.getElementById('kick6'),
        document.getElementById('kick7'), document.getElementById('kick8')
    ];

    const snareValues = snareSliders.map((_, i) => document.getElementById(`snare${i+1}Value`));
    const kickValues = kickSliders.map((_, i) => document.getElementById(`kick${i+1}Value`));

    snareSliders.forEach((slider, index) => {
        slider.addEventListener('input', () => {
            snareValues[index].textContent = `${Math.round(slider.value * 100)}%`;
        });
    });

    kickSliders.forEach((slider, index) => {
        slider.addEventListener('input', () => {
            kickValues[index].textContent = `${Math.round(slider.value * 100)}%`;
        });
    });

    function getProbabilities() {
        const snareProbs = snareSliders.map(slider => parseFloat(slider.value));
        const kickProbs = kickSliders.map(slider => parseFloat(slider.value));
        return { snareProbs, kickProbs };
    }

    let currentTuneObject = null;
    let synthControl = null;
    let midiBuffer = null;

    function generateGroup(pos1, pos2) {
        let note1 = 'g2';
        let note2 = 'g2';
        const probs = getProbabilities();

        if (Math.random() < probs.snareProbs[pos1]) note1 = '[g2c]';
        if (Math.random() < probs.snareProbs[pos2]) note2 = '[g2c]';

        if (Math.random() < probs.kickProbs[pos1]) {
            note1 = note1 === '[g2c]' ? '[g2cF]' : '[g2F]';
        }
        if (Math.random() < probs.kickProbs[pos2]) {
            note2 = note2 === '[g2c]' ? '[g2cF]' : '[g2F]';
        }

        return `${note1}${note2}`;
    }

    function generateMeasure(timeSig) {
        return [
            generateGroup(0, 1),
            generateGroup(2, 3),
            generateGroup(4, 5),
            generateGroup(6, 7)
        ].join(' ');
    }

    function generateRhythmABC(numMeasures, timeSig, tempo) {
        let abc = `X:1
L:1/16
M:4/4
K:C clef=perc
Q:1/4=${tempo}
%%percmap a  crash-cymbal-1  x
%%percmap g  closed-hi-hat x
%%percmap c acoustic-snare
%%percmap F  acoustic-bass-drum
V:ALL stem=up
`;

        const measures = Array.from({length: numMeasures}, () => generateMeasure(timeSig));
        for (let i = 0; i < measures.length; i += 4) {
            abc += measures.slice(i, i + 4).join(' | ') + ' |';
            if (i + 4 < measures.length) abc += '\n';
        }
        return abc + '\n%%\n';
    }

    function loadRhythm(abcString) {
        paperDiv.innerHTML = '';
        audioDiv.innerHTML = '';

        if (synthControl) { try { synthControl.disable(true); } catch (e) {} synthControl = null; }
        if (midiBuffer) { try { midiBuffer.cancel(); } catch (e) {} midiBuffer = null; }

        try {
            const visualObj = ABCJS.renderAbc(paperDiv, abcString, { responsive: "resize" });
            currentTuneObject = visualObj[0];

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
                    millisecondsPerMeasure: msPerMeasure,
                })
                .then(() => synthControl.setTune(currentTuneObject, false))
                .then(() => synthControl.disable(false))
                .catch(e => {
                    console.error("Ошибка аудио:", e);
                    audioDiv.innerHTML = '<p>Ошибка загрузки аудио.</p>';
                    if (synthControl) synthControl.disable(true);
                });
            } else {
                audioDiv.innerHTML = '<p>Аудио не поддерживается.</p>';
            }
        } catch (e) {
            console.error("Ошибка рендеринга:", e);
            paperDiv.innerHTML = '<p>Ошибка отображения.</p>';
        }
    }

    generateBtn.addEventListener('click', () => {
        let numMeasures = parseInt(numMeasuresInput.value, 10);
        const tempo = parseInt(tempoInput.value, 10);

        if (isNaN(numMeasures) || numMeasures < 1 || numMeasures > 32) {
            alert('Количество тактов: от 1 до 32.');
            return;
        }
        if (isNaN(tempo) || tempo < 60 || tempo > 180) {
            alert('Темп: от 60 до 180 BPM.');
            return;
        }

        const abcString = generateRhythmABC(numMeasures, '4/4', tempo);
        loadRhythm(abcString);
    });

    document.getElementById('resetBtn').addEventListener('click', () => {
        const ids = [
            'snare1','snare2','snare3','snare4','snare5','snare6','snare7','snare8',
            'kick1','kick2','kick3','kick4','kick5','kick6','kick7','kick8'
        ];
        ids.forEach(id => {
            const slider = document.getElementById(id);
            const valueEl = document.getElementById(id + 'Value');
            slider.value = 0;
            valueEl.textContent = '0%';
        });
    });

    document.getElementById('downloadPdfBtn').addEventListener('click', () => {
        const numMeasures = numMeasuresInput.value;
        const timeSig = timeSignatureSelect.value;
        const tempo = tempoInput.value;
        const svg = paperDiv.querySelector('svg');
        if (!svg) return alert('Нет ритма для сохранения!');

        const svgData = new XMLSerializer().serializeToString(svg);
        const url = URL.createObjectURL(new Blob([svgData], {type: 'image/svg+xml'}));
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
            const pdf = new jsPDF('p', 'mm', 'a4');
            pdf.setFontSize(16);
            pdf.text('Ритм для барабанов', 105, 15, null, null, 'center');
            pdf.setFontSize(12);
            pdf.text(`Тактов: ${numMeasures} | Размер: ${timeSig} | Темп: ${tempo} BPM`, 105, 25, null, null, 'center');

            const imgData = canvas.toDataURL('image/png');
            const w = 180;
            const h = (canvas.height * w) / canvas.width;
            pdf.addImage(imgData, 'PNG', 15, 35, w, h);
            pdf.save(`Ритм_${numMeasures}m_${timeSig}_${tempo}BPM.pdf`);
            URL.revokeObjectURL(url);
        };

        img.onerror = () => {
            alert('Ошибка при создании PDF.');
            URL.revokeObjectURL(url);
        };
        img.src = url;
    });

    document.addEventListener('DOMContentLoaded', () => {
        showCursor = document.getElementById("show-cursor");
        colorNote = document.getElementById("color-note");

        snareSliders.forEach((slider, i) => snareValues[i].textContent = `${Math.round(slider.value * 100)}%`);
        kickSliders.forEach((slider, i) => kickValues[i].textContent = `${Math.round(slider.value * 100)}%`);
        generateBtn.click();
    });
    </script>
</body>
</html>