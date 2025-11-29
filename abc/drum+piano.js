// exercises.js

// Пример объекта с упражнениями. Можно загружать из JSON-файла.
const exercises = [
    {
        id: 'melody-with-snare-perc',
        title: 'Мелодия с малым барабаном (перкусионный ключ)',
        abc: `X:1
T:Маленькое Скерцо (Д. Кобалевский)
L:1/16
M:2/4
K:C
Q:1/4=100
%%MIDI program 0 0  # Piano для мелодии
V:Melody stem=up
c2e2g2f2 | e2g2f2e2 | d2f2e2d2 | c2e2d2e2
c2e2g2f2 | e2g2f2e2 | d2f2e2d2 | c4e4
B2d2f2d2 | B2d2f2d2 | A2c2e2c2 | A2c2e2c2
G2B2d2B2 | G2B2d2B2 | c4 c4 | c8
%%MIDI program 1 33  # Acoustic Bass для баса (пример)
V:Bass stem=down
E2G2B2A2 | G2B2A2G2 | F2A2G2F2 | E2G2F2G2
c2e2g2f2 | e2g2f2e2 | d2f2e2d2 | c4e4
B2d2f2d2 | B2d2f2d2 | A2c2e2c2 | A2c2e2c2
G2B2d2B2 | G2B2d2B2 | c4 c4 | c8
V:Drums stem=down clef=perc
%%percmap c acoustic-snare x
zccc  z2cc | (3cccc2 c4  | c3c  c2c2 | c2c2  c2(6ccc
c4  c2c2 | cccc  c2c2 | cccc  c2c2 | c4 c4
c3c  c2c2 | ccc2 c2c2 | c2cc c2c2 | ccc2 ccc2
c3c  c2c2 | cccc  c2c2 | cccc cccc | c8


`
    }

];

// Ссылки на DOM-элементы
const exerciseSelect = document.getElementById('exerciseSelect');
const paperDiv = document.getElementById('paper');
const audioDiv = document.getElementById('audio-controls');

// Синтезатор и контроллер abcjs
let synthControl = null;
let midiBuffer = null;
let currentTuneObject = null;

// Функция для отображения и настройки воспроизведения ABC строки
function loadExercise(abcString) {
    // Очистка предыдущего нотного стана и аудио
    paperDiv.innerHTML = '';
    audioDiv.innerHTML = '';

    if (synthControl) {
        synthControl.disable(true); // Отключаем предыдущий контроллер
    }
    if (midiBuffer) {
        midiBuffer.cancel(); // Отменяем предыдущее воспроизведение
    }

    // Параметры отображения
    const renderParams = {
        responsive: "resize",
        // staffwidth: 800, // Можно регулировать ширину
        // drum: "dddd 76 77 77 77 80 60 60 60", // Опционально, можно настроить по-другому
    };

    try {
        // Рендеринг нотного стана
        const visualObj = ABCJS.renderAbc(paperDiv, abcString, renderParams);
        currentTuneObject = visualObj[0]; // Сохраняем объект нотного стана

        // Инициализация воспроизведения
        if (ABCJS.synth.supportsAudio()) {
            synthControl = new ABCJS.synth.SynthController();
            const controlParams = {
                displayLoop: true,    // Показывать кнопку Loop
                displayRestart: true, // Показывать кнопку Restart
                displayPlay: true,    // Показывать кнопку Play/Pause
                displayProgress: true, // Показывать прогресс
                displayClock: true,   // Показывать таймер
                displayWarp: true,    // Показывать регулятор темпа
                // loop: false,       // Начальное состояние Loop (false по умолчанию)
            };

            synthControl.load(audioDiv, null, controlParams);
            synthControl.disable(true); // Сначала отключено

            midiBuffer = new ABCJS.synth.CreateSynth();

            midiBuffer.init({
                visualObj: currentTuneObject,
                millisecondsPerMeasure: 2400, // Примерная настройка скорости (для Q:1/4=100 и M:4/4)
                // Опционально: настройки синтеза звука
                // options: { soundFontUrl: "path/to/soundfont/" }
            }).then(() => {
                return synthControl.setTune(currentTuneObject, false, {
                    // Опционально: настройки воспроизведения
                });
            }).then(() => {
                // После успешной инициализации разрешаем воспроизведение
                synthControl.disable(false);
            }).catch(e => {
                console.error("Ошибка инициализации синтеза MIDI:", e);
                audioDiv.innerHTML = '<p>Ошибка загрузки аудио.</p>';
            });
        } else {
            console.warn("Воспроизведение аудио не поддерживается в этом браузере.");
            audioDiv.innerHTML = '<p>Воспроизведение аудио не поддерживается.</p>';
        }
    } catch (e) {
        console.error("Ошибка рендеринга ABC:", e);
        paperDiv.innerHTML = '<p>Ошибка отображения нотного стана.</p>';
        audioDiv.innerHTML = ''; // Очищаем аудио, если ошибка
    }
}

// Заполнение списка упражнений
function populateExerciseList() {
    exerciseSelect.innerHTML = ''; // Очищаем перед заполнением
    exercises.forEach((ex, index) => {
        const option = document.createElement('option');
        option.value = index; // Используем индекс как значение
        option.textContent = ex.title;
        exerciseSelect.appendChild(option);
    });

    // Загружаем первое упражнение по умолчанию
    if (exercises.length > 0) {
        loadExercise(exercises[0].abc);
    }
}

// Обработчик изменения выбора упражнения
exerciseSelect.addEventListener('change', function() {
    const selectedExIndex = parseInt(this.value, 10);
    if (!isNaN(selectedExIndex) && exercises[selectedExIndex]) {
        loadExercise(exercises[selectedExIndex].abc);
    }
});

// Инициализация страницы
document.addEventListener('DOMContentLoaded', function() {
    populateExerciseList();
});