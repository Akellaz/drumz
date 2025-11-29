//gen_etudes/gen_etudes.js
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
            synthControl = new ABCJS.synth.SynthController();
            const controlParams = {
                displayLoop: false,
                displayRestart: false,
                displayPlay: true,
                displayProgress: true,
                displayClock: true,
                displayWarp: false,
            };

            synthControl.load(audioDiv, null, controlParams);
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