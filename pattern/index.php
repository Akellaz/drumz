<?php require_once __DIR__ . '/../includes/seo.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
<title><?= htmlspecialchars($title) ?></title>
<meta name="description" content="<?= htmlspecialchars($description) ?>">
<meta name="keywords" content="паттерны для барабанов, ритмические паттерны, барабаны, уроки барабанов, Троицк, рок, джаз, фанк, брейки, филлы">
<meta name="author" content="Сергей Щепотин">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://drumz.ru<?= htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES) ?>">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/assets/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/abcjs@6.5.2/abcjs-audio.css">
  <style>
    .pattern-controls {
      background: var(--card-bg);
      padding: var(--spacing);
      border-radius: var(--radius);
      margin: var(--spacing) 0;
      border: 1px solid var(--border);
    }
    .pattern-controls label {
      display: block;
      margin: 12px 0 6px;
      font-weight: 500;
      color: var(--text);
    }
    .pattern-controls select,
    .pattern-controls input[type="number"],
    .pattern-controls button {
      padding: 10px 14px;
      margin: 5px 10px 10px 0;
      font-size: 1rem;
      border: 1px solid var(--border);
      border-radius: 8px;
      background: white;
      color: var(--text);
    }
    .pattern-controls button {
      background: var(--primary);
      color: white;
      border: none;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s;
    }
    .pattern-controls button:hover {
      background: #2b6cb0;
    }
    #paper {
      margin: 20px 0;
      border: 1px solid var(--border);
      background: white;
      padding: 16px;
      border-radius: 8px;
      overflow-x: auto;
    }
    #audio-controls {
      margin: 20px 0;
      padding: 12px;
      background: var(--card-bg);
      border-radius: 8px;
      border: 1px solid var(--border);
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

    /* Вертикальное расположение тактов */
    .measures-horizontal {
      display: flex;
      flex-wrap: nowrap;
      gap: 20px;
      overflow-x: auto;
      padding: 8px 0;
      scrollbar-width: thin; /* для Firefox */
    }
    .measures-horizontal::-webkit-scrollbar {
      height: 6px;
    }
    .measures-horizontal::-webkit-scrollbar-thumb {
      background: #ccc;
      border-radius: 3px;
    }
    .measures-horizontal::-webkit-scrollbar-thumb:hover {
      background: #999;
    }

    .measure-row {
      display: flex;
      flex-direction: column;
      min-width: 160px;
      flex: 0 0 auto;
      background: var(--card-bg);
      padding: 10px;
      border-radius: 6px;
      border: 1px solid var(--border);
    }
    .measure-row label {
      font-weight: 500;
      margin-bottom: 8px;
      white-space: nowrap;
      color: var(--text);
      font-size: 0.9rem;
      text-align: center;
    }
    
    .mode-selector {
      margin-top: 8px;
      display: flex;
      flex-direction: column;
      gap: 4px;
    }
    
    .mode-option {
      display: flex;
      align-items: center;
      font-size: 0.8rem;
      cursor: pointer;
    }
    
    .mode-option input[type="radio"] {
      margin-right: 6px;
      width: 14px;
      height: 14px;
    }
    
    .mode-option label {
      margin: 0;
      font-size: 0.8rem;
      cursor: pointer;
      font-weight: normal;
    }
    
    .measure-row select {
      width: 100%;
      margin: 0;
    }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="container">
    <h1>Библиотека ритмических рисунков</h1>

    <div class="pattern-controls">
      <label>Количество тактов (1–4):</label>
      <select id="measureCount">
        <option value="1">1 такт</option>
        <option value="2">2 такта</option>
        <option value="3">3 такта</option>
        <option value="4" selected>4 такта</option>
      </select>

      <div id="measuresContainer" class="measures-horizontal"></div>

      <label>Темп (BPM):</label>
      <input type="number" id="tempo" value="100" min="60" max="180" />

      <button id="applyPatternBtn">Применить паттерн</button>
    </div>

    <div id="paper"></div>
    <div id="audio-controls"></div>
    <button id="downloadPdfBtn">📥 Скачать в PDF</button>
  </main>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/abcjs@6.5.2/dist/abcjs-basic.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

  <script>
  const allPatterns = [

    // Basic
    { id: 'gb1',  name: 'Rock Basic',           abc: '[g2F]g2 [g2c]g2 [g2F]g2 [g2c]g2' },
    { id: 'gb2',  name: 'Disco Beat',     		abc: '[g2F]g2 [g2Fc]g2 [g2F]g2 [g2Fc]g2' },
	{ id: 'gb4',  name: 'Arena Rock',      		abc: '[g2F][g2F] [g2c]g2 [g2F][g2F] [g2c]g2' },
    { id: 'gb3',  name: 'Arena Pop', 			abc: '[g2F]g2 [g2c]g2 [g2F][g2F] [g2c]g2' },
	{ id: 'gb9',  name: 'Arena Pop Ending',     abc: '[g2F]g2 [g2c]g2 [g2F][g2F] [g2c][g2F]' },
    { id: 'gb5',  name: 'Ballad',               abc: '[g2F]g2 [g2c][g2F] [g2F]g2 [g2c]g2' },
    { id: 'gb6',  name: 'Pop Ballad',			abc: '[g2F]g2 [g2c][g2F] [g2F][g2F] [g2c]g2' },
    { id: 'gb7',  name: 'Arena Ballad',         abc: '[g2F][g2F] [g2c][g2F] [g2F]g2 [g2c]g2' },
    { id: 'gb8',  name: 'Arena Rock Ballad',    abc: '[g2F][g2F] [g2c][g2F] [g2F][g2F] [g2c]g2' },
    { id: 'gb10', name: 'Pop Ballad Ending',    abc: '[g2F]g2 [g2c][g2F] [g2F][g2F] [g2c][g2F]' },
    { id: 'gb11', name: 'Heavy Rock',           abc: '[g2F][g2F] [g2c][g2F] [g2F][g2F] [g2c][g2F]' },
    { id: 'gb12', name: 'Synco Pop',  			abc: '[g2F]g2 [g2c][g2F] g2g2 [g2c]g2' },
    { id: 'gb13', name: 'Synco Rock',     		abc: '[g2F]g2 [g2c]g2 g2[g2F] [g2c]g2' },
    { id: 'gb14', name: 'Synco Pop Rock',  		abc: '[g2F]g2 [g2c][g2F] g2[g2F] [g2c]g2' },
    { id: 'gb15', name: 'Synco Arena',      	abc: '[g2F][g2F] [g2c][g2F] g2[g2F] [g2c]g2' },
    { id: 'gb16', name: 'Synco Pop Ending',     abc: '[g2F]g2 [g2c][g2F] g2[g2F] [g2c][g2F]' },
    
    // Брейки
    { id: 'b1', name: 'Break: 4 c4', abc: 'c4 c4 c4 c4' },
    { id: 'b2', name: 'Break: пауза', abc: 'z16' },
    { id: 'b3', name: 'Break: F4na + паузы', abc: '[F4na] z4 z8' },
    { id: 'b4', name: 'Break: F4na → c4', abc: '[F4na] z4 z4 c4' },
    { id: 'b5', name: 'Break: c4 → F2 c2F2', abc: 'c4 z4 z2 F2 c2F2' },
    { id: 'b6', name: 'Break: сложный №1', abc: '[g2F][g2F] [g2c][g2F] [F4na] [c4na]' },
    { id: 'b7', name: 'Break: том-ролл', abc: '[F4na] !<(![c2A][c2A] [c2A][c2A] [c2A]!<)![c2A]' },
    { id: 'b8', name: 'Break: томы + краш', abc: '[F4na] z2 ee d2d2 A2A2' },
    { id: 'b9', name: 'Amen Break', abc: 'c2c2 [F2c][F2c] c2c2 [F2c]F2' },
    { id: 'b10', name: 'Funky Drummer', abc: '[g2F][g2F] [g2c][g2F] [F4na] [c4na]' },
    { id: 'b11', name: 'Think Break', abc: '[F2c][F2c] F2[F2c] [F2c][F2c] F4' },
    { id: 'b12', name: 'Apache Break', abc: 'c4 c4 c4 c4' },
    
    // Advanced
    { id: 'a1', name: 'Advanced 1', abc: '[g4F] c2c2 [g4F] c2c2' },
    { id: 'a2', name: 'Advanced 2', abc: '[g2F][g2c] [g2F][g2c] [g2F][g2c] [g2F][g2c]' },
    { id: 'a3', name: 'Advanced 3', abc: '[g3F]g [g3c]g [g3F]g [g3c]g' },
    { id: 'a4', name: 'Advanced 4', abc: '[g2F][g3c]g [g2F][g3c]g [g2F][g3c]g [g2F][g3c]g' },
    { id: 'a5', name: 'Advanced 5', abc: '[g4F] [g2c][g2F] [g4F] [g2c][g2F]' },
    { id: 'a6', name: 'Advanced Syncopation', abc: 'g2[g2F] g2[g2c] g2[g2F] g2[g2c]' },
    { id: 'a7', name: 'Advanced Flam', abc: '[g2F][g2F]c [g2c][g2c]F [g2F][g2F]c [g2c][g2c]F' },
    { id: 'a8', name: 'Advanced Paradiddle', abc: '[g2F][g2c][g2F]g [g2c][g2F][g2c]c [g2F][g2c][g2F]g [g2c][g2F][g2c]c' },
    { id: 'a9', name: 'Linear Groove', abc: '[g4F] c2c2 [g4F] c2c2' },
    { id: 'a10', name: 'Double Time', abc: '[g2F][g2c] [g2F][g2c] [g2F][g2c] [g2F][g2c]' },
    { id: 'a11', name: 'Syncopation', abc: '[g3F]g [g3c]g [g3F]g [g3c]g' },
    { id: 'a12', name: 'Paradiddle Groove', abc: '[g2F][g2c][g2F]g [g2c][g2F][g2c]c [g2F][g2c][g2F]g [g2c][g2F][g2c]c' },
    
    // Fill
    { id: 'f1', name: 'Fill 1', abc: 'g2c2 g2c2 g2c2 g4' },
    { id: 'f2', name: 'Fill 2', abc: 'g2[g2F] c2[g2c] g2[g2F] c4' },
    { id: 'f3', name: 'Fill 3', abc: '[g2F][g2c] [g2F][g2c] [g2F][g2c] g4' },
    { id: 'f4', name: 'Fill 4', abc: 'g2c2 [g2F][g2c][g2F]g g2c2 g4' },
    { id: 'f5', name: 'Fill 5', abc: '[g3F]g [g3c]g [g2F][g2F] g4' },
    { id: 'f6', name: 'Fill 6', abc: 'g2[g2F]c g2[g2c]F g2[g2F]c g4' },
    { id: 'f7', name: 'Fill 7', abc: '[g2F][g2c][g2F]g [g2c][g2F][g2c]c g2c2 g4' },
    { id: 'f8', name: 'Fill 8', abc: 'g2c2 [g2F][g2c][g2F]g g2c2 g4' },
    { id: 'f9', name: 'Basic Fill', abc: 'g2c2 g2c2 g2c2 g4' },
    { id: 'f10', name: 'Tom-Tom Fill', abc: 'g2[g2F] c2[g2c] g2[g2F] c4' },
    { id: 'f11', name: 'Paradiddle Fill', abc: '[g2F][g2c] [g2F][g2c] [g2F][g2c] g4' },
    { id: 'f12', name: 'Flam Fill', abc: 'g2c2 [g2F][g2c][g2F]g g2c2 g4' },
    
    // Jazz
    { id: 'j1', name: 'Jazz Walk', abc: '[g2F][g2c] [g2F][g2c] [g2F][g2c] [g2F][g2c]' },
    { id: 'j2', name: 'Brush Swing', abc: '[g3F]g [g3c]g [g3F]g [g3c]g' },
    { id: 'j3', name: 'Jazz Waltz', abc: '[g2F]g2 [g2c]g2 [g2F]g2' },
    { id: 'j4', name: 'Brush Ballad', abc: '[g4F] z4 [g2c][g2F] z4' },
    { id: 'j5', name: 'Latin Jazz', abc: '[g2F][g2c] [g2F][g2c] [g2F][g2c] [g2F][g2c]' },
    { id: 'j6', name: 'Bebop Groove', abc: '[g2F]g2 [g2c]g2 g2[g2F] [g2c]g2' },
    { id: 'j7', name: 'Bossa Nova1', abc: '[g2cF]g2 g2[g2cF] [g2F]g2 [g2c][g2F]' },
	{ id: 'j8', name: 'Bossa Nova2', abc: '[g2F]g2 [g2c][g2F] [g2F][g2c] g2[g2F]' },
	
    // Latin
    { id: 'l1', name: 'Samba Basic', abc: '[g2F]g2 [g2c]g2 [g2F]g2 [g2c]g2' },
    { id: 'l2', name: 'Bossa Nova', abc: '[g4F] z4 [g2c][g2F] z4' },
    { id: 'l3', name: 'Rumba', abc: '[g2F]g2 [g2c][g2F] [g2F]g2 [g2c]g2' },
    { id: 'l4', name: 'Cha-Cha', abc: '[g2F][g2F] [g2c]g2 [g2F][g2F] [g2c]g2' },
    { id: 'l5', name: 'Mambo', abc: '[g2F]g2 [g2c][g2F] g2[g2F] [g2c]g2' }
  ];

  const measureCountSelect = document.getElementById('measureCount');
  const measuresContainer = document.getElementById('measuresContainer');
  const applyBtn = document.getElementById('applyPatternBtn');
  const tempoInput = document.getElementById('tempo');
  const paperDiv = document.getElementById('paper');
  const audioDiv = document.getElementById('audio-controls');
  const downloadPdfBtn = document.getElementById('downloadPdfBtn');

  let currentTuneObject = null;
  let synthControl = null;
  let midiBuffer = null;

  function createMeasureSelector(measureIndex) {
    const div = document.createElement('div');
    div.className = 'measure-row';

    const label = document.createElement('label');
    label.textContent = `Такт ${measureIndex + 1}`;

    const select = document.createElement('select');
    select.dataset.index = measureIndex;

    const modeSelector = document.createElement('div');
    modeSelector.className = 'mode-selector';

    const modes = [
      { id: 'basic', name: 'Basic', prefix: 'gb' },
      // Категория "Ритм" удалена
      { id: 'break', name: 'Брейк', prefix: 'b' },
      { id: 'advanced', name: 'Advanced', prefix: 'a' },
      { id: 'fill', name: 'Fill', prefix: 'f' },
      { id: 'jazz', name: 'Jazz', prefix: 'j' },
      { id: 'latin', name: 'Latin', prefix: 'l' }
    ];

    const radioGroupName = `mode-${measureIndex}`;

    modes.forEach((mode, index) => {
      const modeOption = document.createElement('div');
      modeOption.className = 'mode-option';

      const radio = document.createElement('input');
      radio.type = 'radio';
      radio.name = radioGroupName;
      radio.value = mode.id;
      radio.id = `${radioGroupName}-${mode.id}`;
      if (index === 0) radio.checked = true;

      const radioLabel = document.createElement('label');
      radioLabel.textContent = mode.name;
      radioLabel.htmlFor = `${radioGroupName}-${mode.id}`;

      modeOption.appendChild(radio);
      modeOption.appendChild(radioLabel);
      modeSelector.appendChild(modeOption);

      radio.addEventListener('change', () => {
        if (radio.checked) {
          updateOptions(select, mode.prefix);
        }
      });
    });

    function updateOptions(selectElement, prefix) {
      selectElement.innerHTML = '';
      const filtered = allPatterns.filter(p => p.id.startsWith(prefix));
      filtered.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = p.name;
        selectElement.appendChild(opt);
      });
    }

    updateOptions(select, 'gb');

    div.appendChild(label);
    div.appendChild(select);
    div.appendChild(modeSelector);
    return div;
  }

  function updateMeasuresUI() {
    const count = parseInt(measureCountSelect.value, 10);
    measuresContainer.innerHTML = '';
    for (let i = 0; i < count; i++) {
      measuresContainer.appendChild(createMeasureSelector(i));
    }
  }

  function getPatternById(id) {
    return allPatterns.find(p => p.id === id);
  }

  function buildAbcString(tempo) {
    const count = parseInt(measureCountSelect.value, 10);
    const abcParts = [];

    for (let i = 0; i < count; i++) {
      const select = measuresContainer.querySelector(`select[data-index="${i}"]`);
      const pattern = getPatternById(select.value);
      abcParts.push(pattern ? pattern.abc : 'z16');
    }

    return `X:1
L:1/16
M:4/4
K:C clef=perc
Q:1/4=${tempo}
U:n=!style=x!
%%percmap F  acoustic-bass-drum
%%percmap c  acoustic-snare
%%percmap g  closed-hi-hat x
%%percmap e  high-tom
%%percmap d  hi-mid-tom
%%percmap A  low-tom
%%percmap a  crash-cymbal-1  x
V:ALL stem=up
${abcParts.join(' | ')} |
%%`;
  }

  function renderPattern(abcString, tempo) {
    paperDiv.innerHTML = '';
    audioDiv.innerHTML = '';

    if (synthControl) { try { synthControl.disable(true); } catch (e) {} synthControl = null; }
    if (midiBuffer) { try { midiBuffer.cancel(); } catch (e) {} midiBuffer = null; }

    try {
      const visualObj = ABCJS.renderAbc(paperDiv, abcString, { responsive: "resize" });
      currentTuneObject = visualObj[0];

      if (ABCJS.synth && ABCJS.synth.supportsAudio()) {
        synthControl = new ABCJS.synth.SynthController();
        synthControl.load(audioDiv, null, {
          displayLoop: false,
          displayRestart: false,
          displayPlay: true,
          displayProgress: true,
          displayClock: false,
          displayWarp: false,
        });
        synthControl.disable(true);

        midiBuffer = new ABCJS.synth.CreateSynth();
        const msPerMeasure = (4 / tempo) * 60 * 1000;

        midiBuffer.init({
          visualObj: currentTuneObject,
          millisecondsPerMeasure: msPerMeasure
        })
        .then(() => synthControl.setTune(currentTuneObject, false))
        .then(() => synthControl.disable(false))
        .catch(e => {
          console.error("Audio error:", e);
          audioDiv.innerHTML = '<p>Ошибка инициализации аудио.</p>';
        });
      } else {
        audioDiv.innerHTML = '<p>Аудио не поддерживается.</p>';
      }
    } catch (e) {
      console.error("Render error:", e);
      paperDiv.innerHTML = '<p>Ошибка отображения.</p>';
    }
  }

  applyBtn.addEventListener('click', () => {
    const tempo = parseInt(tempoInput.value, 10);
    if (isNaN(tempo) || tempo < 60 || tempo > 180) {
      alert('Темп должен быть от 60 до 180 BPM.');
      return;
    }
    const abc = buildAbcString(tempo);
    renderPattern(abc, tempo);
  });

  downloadPdfBtn.addEventListener('click', () => {
    const svg = paperDiv.querySelector('svg');
    if (!svg) {
      alert('Нет нот для экспорта!');
      return;
    }

    const bbox = svg.getBBox();
    const scale = 2;
    const canvas = document.createElement('canvas');
    canvas.width = Math.max(bbox.width * scale, 800);
    canvas.height = bbox.height * scale;
    const ctx = canvas.getContext('2d');

    const img = new Image();
    const serializer = new XMLSerializer();
    const svgData = serializer.serializeToString(svg);
    const blob = new Blob([svgData], { type: 'image/svg+xml' });
    const url = URL.createObjectURL(blob);

    img.onload = () => {
      ctx.fillStyle = 'white';
      ctx.fillRect(0, 0, canvas.width, canvas.height);
      ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

      const { jsPDF } = window.jspdf;
      const pdf = new jsPDF('p', 'mm', 'a4');
      const imgData = canvas.toDataURL('image/png');
      const imgWidth = 180;
      const imgHeight = (canvas.height * imgWidth) / canvas.width;
      pdf.addImage(imgData, 'PNG', 15, 15, imgWidth, imgHeight);
      pdf.save('Ритм_паттерн.pdf');
      URL.revokeObjectURL(url);
    };

    img.onerror = () => {
      alert('Ошибка при создании PDF.');
      URL.revokeObjectURL(url);
    };

    img.src = url;
  });

  document.addEventListener('DOMContentLoaded', () => {
    measureCountSelect.addEventListener('change', updateMeasuresUI);
    updateMeasuresUI();
    applyBtn.click();
  });
  </script>
</body>
</html>