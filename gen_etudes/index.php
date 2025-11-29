<?php require_once __DIR__ . '/../includes/seo.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($description) ?>">
    <meta name="keywords" content="генератор этюдов, барабаны, ритмические этюды, уроки барабанов, Троицк, Сергей Щепотин, обучение барабанам">
    <meta name="author" content="Сергей Щепотин">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://drumz.ru<?= htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES) ?>">

    <!-- Open Graph / Telegram / VK -->
    <meta property="og:title" content="<?= htmlspecialchars($title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($description) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://drumz.ru<?= htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES) ?>">
    <meta property="og:site_name" content="Drumz.ru">
    <meta property="og:image" content="https://drumz.ru/assets/tool-preview-gen_etudes.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Стили -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/abcjs@6.5.2/abcjs-audio.min.css">
    <link rel="stylesheet" href="/assets/style.css?v=20251124">
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
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../includes/header.php'; ?>

    <main class="container">
        <h1>🎵 Генератор этюдов 🥁</h1>

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

        <!-- Место для отображения нот -->
        <div id="paper"></div>

        <!-- Место для элементов управления воспроизведением -->
        <div id="audio-controls"></div>

        <!-- Кнопка Скачать в PDF -->
        <button id="downloadPdfBtn">📥 Скачать в PDF</button>

        <!-- Методика (опционально) -->
        <div class="methodology">
            <h3>Как работать с генератором</h3>
            <ol>
                <li>Выберите разрешённые ритмические фигуры.</li>
                <li>Укажите количество тактов, размер и темп.</li>
                <li>Нажмите «Сгенерировать этюд» — появятся ноты и кнопка воспроизведения.</li>
                <li>Прослушайте, сыграйте вместе, скачайте PDF для занятий.</li>
            </ol>
        </div>
    </main>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>

    <!-- Подключение библиотек -->
    <script src="https://cdn.jsdelivr.net/npm/abcjs@6.5.2/dist/abcjs-basic-min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="/gen_etudes/gen_etudes.js?v=20251124"></script>
</body>
</html>