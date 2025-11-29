<?php require_once __DIR__ . '/../includes/seo.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title) ?></title>
<meta name="description" content="<?= htmlspecialchars($description) ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/abcjs@6.5.2/abcjs-audio.min.css">
    <link rel="stylesheet" href="/assets/style.css">
	
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

        /* Выделение сильных долей — тонко, неярко */
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

        /* Эпиграф — твои слова */
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

        /* Методика работы */
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
		
		/* Кнопка поделиться */
#shareBtn {
    display: block;
    margin: 20px auto;
    padding: 12px 24px;
    background-color: #38a169;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    text-align: center;
    border: none;
    cursor: pointer;
}
#shareBtn:hover {
    background-color: #2f855a;
}

    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../includes/header.php'; ?>

    <main class="container">
        <h1>Генератор ритмов</h1>


 <!-- Панель управления -->
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


        <!-- Панель регуляторов частоты -->
        <div class="rhythm-options">
            <div class="rhythm-grid">
                <!-- Заголовки -->
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
                
                <!-- Снэр и бочка -->
                <div class="rhythm-controls-row">
                    <!-- 1 -->
                    <div class="rhythm-control-item strong-beat-bg">
                        <div class="control-label">s</div>
                        <input type="range" class="vertical-slider" id="snare1" min="0" max="1" step="0.05" value="0.9" orient="vertical">
                        <div class="control-value" id="snare1Value">90%</div>
                        <div class="control-label">k</div>
                        <input type="range" class="vertical-slider" id="kick1" min="0" max="1" step="0.05" value="0.9" orient="vertical">
                        <div class="control-value" id="kick1Value">90%</div>
                    </div>
                    
                    <!-- 1+ -->
                    <div class="rhythm-control-item">
                        <div class="control-label">s</div>
                        <input type="range" class="vertical-slider" id="snare2" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                        <div class="control-value" id="snare2Value">30%</div>
                        <div class="control-label">k</div>
                        <input type="range" class="vertical-slider" id="kick2" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                        <div class="control-value" id="kick2Value">30%</div>
                    </div>
                    
                    <!-- 2 -->
                    <div class="rhythm-control-item strong-beat-bg">
                        <div class="control-label">s</div>
                        <input type="range" class="vertical-slider" id="snare3" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                        <div class="control-value" id="snare3Value">30%</div>
                        <div class="control-label">k</div>
                        <input type="range" class="vertical-slider" id="kick3" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                        <div class="control-value" id="kick3Value">30%</div>
                    </div>
                    
                    <!-- 2+ -->
                    <div class="rhythm-control-item">
                        <div class="control-label">s</div>
                        <input type="range" class="vertical-slider" id="snare4" min="0" max="1" step="0.05" value="0.9" orient="vertical">
                        <div class="control-value" id="snare4Value">90%</div>
                        <div class="control-label">k</div>
                        <input type="range" class="vertical-slider" id="kick4" min="0" max="1" step="0.05" value="0.9" orient="vertical">
                        <div class="control-value" id="kick4Value">90%</div>
                    </div>
                    
                    <!-- 3 -->
                    <div class="rhythm-control-item strong-beat-bg">
                        <div class="control-label">s</div>
                        <input type="range" class="vertical-slider" id="snare5" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                        <div class="control-value" id="snare5Value">30%</div>
                        <div class="control-label">k</div>
                        <input type="range" class="vertical-slider" id="kick5" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                        <div class="control-value" id="kick5Value">30%</div>
                    </div>
                    
                    <!-- 3+ -->
                    <div class="rhythm-control-item">
                        <div class="control-label">s</div>
                        <input type="range" class="vertical-slider" id="snare6" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                        <div class="control-value" id="snare6Value">30%</div>
                        <div class="control-label">k</div>
                        <input type="range" class="vertical-slider" id="kick6" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                        <div class="control-value" id="kick6Value">30%</div>
                    </div>
                    
                    <!-- 4 -->
                    <div class="rhythm-control-item strong-beat-bg">
                        <div class="control-label">s</div>
                        <input type="range" class="vertical-slider" id="snare7" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                        <div class="control-value" id="snare7Value">30%</div>
                        <div class="control-label">k</div>
                        <input type="range" class="vertical-slider" id="kick7" min="0" max="1" step="0.05" value="0.3" orient="vertical">
                        <div class="control-value" id="kick7Value">30%</div>
                    </div>
                    
                    <!-- 4+ -->
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

       
	   
	   
	   <!-- Панель управления -->
        <div class="rhythm-controls">
            
            
            <div class="control-group">
                <button id="generateBtn">Сгенерировать ритм</button>
            </div>
            <div class="control-group">
                <button id="resetBtn">🔄 Сброс</button>
            </div>
        </div>
	   
	   
	   
	   
	   

        <!-- Место для отображения нот -->
        <div id="paper"></div>

        <!-- Место для элементов управления воспроизведением -->
        <div id="audio-controls"></div>

        <!-- Кнопка Скачать в PDF -->
        <button id="downloadPdfBtn">📥 Скачать в PDF</button>

    </main>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>

    <!-- Подключение библиотек -->
    <script src="https://cdn.jsdelivr.net/npm/abcjs@6.5.2/dist/abcjs-basic-min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
	
	
	
    <!-- Подключение основного скрипта генератора -->
    <script src="/gen_rhythms/gen_rhythms_v8.js"></script>
    <script>
        // Скрипт для кнопки сброса
        document.getElementById('resetBtn').addEventListener('click', function() {
            // Список всех ID ползунков
            const sliderIds = [
                'snare1', 'snare2', 'snare3', 'snare4', 
                'snare5', 'snare6', 'snare7', 'snare8',
                'kick1', 'kick2', 'kick3', 'kick4', 
                'kick5', 'kick6', 'kick7', 'kick8'
            ];

            // Проходим по всем ползункам и устанавливаем в 0
            sliderIds.forEach(function(id) {
                const slider = document.getElementById(id);
                const valueDisplay = document.getElementById(id + 'Value');
                slider.value = 0;
                valueDisplay.textContent = '0%';
            });
        });
    </script>
</body>
</html>