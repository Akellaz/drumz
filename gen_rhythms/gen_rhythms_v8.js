// === Ссылки на DOM-элементы ===
const numMeasuresInput = document.getElementById('numMeasures');
const timeSignatureSelect = document.getElementById('timeSignature');
const tempoInput = document.getElementById('tempo');
const generateBtn = document.getElementById('generateBtn');
const paperDiv = document.getElementById('paper');
const audioDiv = document.getElementById('audio-controls');

// === Слайдеры для каждой восьмой ноты ===
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

// === Элементы значений ===
const snareValues = [
    document.getElementById('snare1Value'), document.getElementById('snare2Value'),
    document.getElementById('snare3Value'), document.getElementById('snare4Value'),
    document.getElementById('snare5Value'), document.getElementById('snare6Value'),
    document.getElementById('snare7Value'), document.getElementById('snare8Value')
];

const kickValues = [
    document.getElementById('kick1Value'), document.getElementById('kick2Value'),
    document.getElementById('kick3Value'), document.getElementById('kick4Value'),
    document.getElementById('kick5Value'), document.getElementById('kick6Value'),
    document.getElementById('kick7Value'), document.getElementById('kick8Value')
];

// === Обновляем значения при движении слайдеров ===
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

// === Функция получения текущих значений ===
function getProbabilities() {
    const snareProbs = snareSliders.map(slider => parseFloat(slider.value));
    const kickProbs = kickSliders.map(slider => parseFloat(slider.value));
    return { snareProbs, kickProbs };
}

// === Глобальные переменные для ABCJS ===
let currentTuneObject = null;
let synthControl = null;
let midiBuffer = null;

// === Генерация группы: две восьмых ===
function generateGroup(pos1, pos2) {
    let note1 = 'g2';
    let note2 = 'g2';

    // === Получаем текущие вероятности ===
    const probs = getProbabilities();

    // === Снэр ===
    if (Math.random() < probs.snareProbs[pos1]) {
        note1 = '[g2c]';
    }

    if (Math.random() < probs.snareProbs[pos2]) {
        note2 = '[g2c]';
    }

    // === Бочка ===
    if (Math.random() < probs.kickProbs[pos1]) {
        if (note1 === '[g2c]') {
            note1 = '[g2cF]'; // снэр + бочка
        } else {
            note1 = '[g2F]';
        }
    }

    if (Math.random() < probs.kickProbs[pos2]) {
        if (note2 === '[g2c]') {
            note2 = '[g2cF]'; // снэр + бочка
        } else {
            note2 = '[g2F]';
        }
    }

    return `${note1}${note2}`;
}

// === Генерация одного такта — 4 группы ===
function generateMeasure(timeSig) {
    const group1 = generateGroup(0, 1); // 1, 1+
    const group2 = generateGroup(2, 3); // 2, 2+
    const group3 = generateGroup(4, 5); // 3, 3+
    const group4 = generateGroup(6, 7); // 4, 4+
    return `${group1} ${group2} ${group3} ${group4}`;
}

// === Генерация всей ABC-строки ===
function generateRhythmABC(numMeasures, timeSig, tempo) {
    const abcString = `X:1
T:Ритм
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

    let measures = [];
    for (let m = 0; m < numMeasures; m++) {
        measures.push(generateMeasure(timeSig));
    }

    // === Формируем строки по 4 такта ===
    let abcLines = '';
    const measuresPerLine = 4;

    for (let i = 0; i < measures.length; i += measuresPerLine) {
        const lineMeasures = measures.slice(i, i + measuresPerLine);
        const line = lineMeasures.join(' | ');
        
        abcLines += line;
        
        if (i + measuresPerLine < measures.length) {
            abcLines += ' |\n';
        } else {
            abcLines += ' |';
        }
    }

    return abcString + abcLines + '\n%%\n';
}

// === Функция отображения ===
function loadRhythm(abcString) {
    paperDiv.innerHTML = '';
    audioDiv.innerHTML = '';

    if (synthControl) {
        try { synthControl.disable(true); } catch (e) {}
        synthControl = null;
    }
    if (midiBuffer) {
        try { midiBuffer.cancel(); } catch (e) {}
        midiBuffer = null;
    }

    const renderParams = {
        responsive: "resize",
    };

    try {
        const visualObj = ABCJS.renderAbc(paperDiv, abcString, renderParams);
        currentTuneObject = visualObj[0];

        if (ABCJS.synth.supportsAudio()) {
            synthControl = new ABCJS.synth.SynthController();
            const controlParams = {
                displayLoop: true,
                displayRestart: true,
                displayPlay: true,
                displayProgress: true,
                displayClock: true,
                displayWarp: true,
            };

            synthControl.load(audioDiv, null, controlParams);
            synthControl.disable(true);

            midiBuffer = new ABCJS.synth.CreateSynth();
            const beatsPerMeasure = 4;
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
                if (audioDiv) audioDiv.innerHTML = '<p>Ошибка загрузки аудио.</p>';
                if (synthControl) synthControl.disable(true);
                midiBuffer = null;
            });
        } else {
            audioDiv.innerHTML = '<p>Воспроизведение аудио не поддерживается.</p>';
        }
    } catch (e) {
        console.error("Ошибка рендеринга:", e);
        paperDiv.innerHTML = '<p>Ошибка отображения нотного стана.</p>';
    }
}

// === Обработчик кнопки "Сгенерировать" ===
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

    const abcString = generateRhythmABC(numMeasures, timeSig, tempo);
    loadRhythm(abcString);
});

// === Инициализация ===
document.addEventListener('DOMContentLoaded', function() {
    // === Обновляем отображение значений при загрузке ===
    snareSliders.forEach((slider, index) => {
        snareValues[index].textContent = `${Math.round(slider.value * 100)}%`;
    });
    
    kickSliders.forEach((slider, index) => {
        kickValues[index].textContent = `${Math.round(slider.value * 100)}%`;
    });

    // === Генерация по умолчанию ===
    generateBtn.click();
});

// === Обработчик кнопки "Скачать в PDF" ===
document.getElementById('downloadPdfBtn').addEventListener('click', function () {
    const numMeasures = numMeasuresInput.value;
    const timeSig = timeSignatureSelect.value;
    const tempo = tempoInput.value;
    
    const svgElement = paperDiv.querySelector('svg');
    if (!svgElement) {
        alert('Нет ритма для сохранения!');
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
        pdf.text('Ритм для барабанов', 105, 15, null, null, 'center');
        pdf.setFontSize(12);
        pdf.text(`Тактов: ${numMeasures} | Размер: ${timeSig} | Темп: ${tempo} BPM`, 105, 25, null, null, 'center');
        
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
        
        pdf.save(`Ритм_${numMeasures}m_${timeSig}_${tempo}BPM.pdf`);
        URL.revokeObjectURL(svgUrl);
    };
    
    img.onerror = function() {
        alert('Ошибка при создании PDF. Попробуйте еще раз.');
        URL.revokeObjectURL(svgUrl);
    };
    
    img.src = svgUrl;
});
