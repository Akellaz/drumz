<!DOCTYPE html>
<html lang="ru">
<head>
  <?php require_once __DIR__ . '/../../includes/seo.php'; ?>
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
      scrollbar-width: thin;
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

    /* === Стили курсора и подсветки === */
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
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../../includes/header.php'; ?>

  <main class="container">
    <h1>Паттерны ритмических рисунков</h1>

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

    <!-- Управление курсором -->
    <div class="cursor-nav">
      <label>
        <input type="checkbox" id="show-cursor"> Показывать курсор
      </label>
      <label>
        <input type="checkbox" id="color-note" checked> Подсвечивать ноты
      </label>
    </div>

    <div id="paper"></div>
    <div id="audio-controls"></div>
    <button id="downloadPdfBtn">📥 Скачать в PDF</button>
  </main>

  <?php require_once __DIR__ . '/../../includes/footer.php'; ?>

  <!-- Локальная ABCjs (исправлен путь на абсолютный от корня) -->
  <script src="/assets/abcjs-main/dist/abcjs-basic.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

  <script>
  // === Cursor Control (только курсор + подсветка) ===
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
  const allPatterns = [
    // Basic
    { id: 'gb1',  name: 'Rock Basic',           abc: '[g2F]g2 [g2c]g2 [g2F]g2 [g2c]g2' },
    { id: 'gb2',  name: 'Disco Beat',     		abc: '[g2F]g2 [g2Fc]g2 [g2F]g2 [g2Fc]g2' },
    { id: 'gb4',  name: 'Arena Rock',      		abc: '[g2F][g2F] [g2c]g2 [g2F][g2F] [g2c]g2' },
    { id: 'gb3',  name: 'Arena Pop', 			abc: '[g2F]g2 [g2c]g2 [g2F][g2F] [g2c]g2' },
    { id: 'gb9',  name: 'Arena Pop Ending',     abc: '[g2F]g2 [g2c]g2 [g2F][g2F] [g2c][g2F]' },
    { id: 'gb5',  name: 'Ballad',               abc: '[g2F]g2 [g2c][g2F] [g2F]g2 [g2c]g2' },
	{ id: 'gb7',  name: 'Ballad Rock',         abc: '[g2F][g2F] [g2c][g2F] [g2F]g2 [g2c]g2' },
    { id: 'gb6',  name: 'Ballad Arena Pop',			abc: '[g2F]g2 [g2c][g2F] [g2F][g2F] [g2c]g2' },
    { id: 'gb8',  name: 'Ballad Arena Rock',    abc: '[g2F][g2F] [g2c][g2F] [g2F][g2F] [g2c]g2' },
    { id: 'gb10', name: 'Ballad Pop Ending',    abc: '[g2F]g2 [g2c][g2F] [g2F][g2F] [g2c][g2F]' },
    { id: 'gb11', name: 'Heavy Rock',           abc: '[g2F][g2F] [g2c][g2F] [g2F][g2F] [g2c][g2F]' },
    { id: 'gb12', name: 'Synco Pop',  			abc: '[g2F]g2 [g2c][g2F] g2g2 [g2c]g2' },
    { id: 'gb13', name: 'Synco Rock',     		abc: '[g2F]g2 [g2c]g2 g2[g2F] [g2c]g2' },
    { id: 'gb14', name: 'Synco Pop Rock',  		abc: '[g2F]g2 [g2c][g2F] g2[g2F] [g2c]g2' },
    { id: 'gb15', name: 'Synco Arena',      	abc: '[g2F][g2F] [g2c][g2F] g2[g2F] [g2c]g2' },
    { id: 'gb16', name: 'Synco Pop Ending',     abc: '[g2F]g2 [g2c][g2F] g2[g2F] [g2c][g2F]' },
    
    // Fill (ранее "Брейки")
    { id: 'b1', name: 'Fill: 4 c4', abc: 'c4 c4 c4 c4' },
    { id: 'b2', name: 'Fill: пауза', abc: 'z16' },
    { id: 'b3', name: 'Fill: F4na + паузы', abc: '[F4na] z4 z8' },
    { id: 'b4', name: 'Fill: F4na → c4', abc: '[F4na] z4 z4 c4' },
    { id: 'b5', name: 'Fill: c4 → F2 c2F2', abc: 'c4 z4 z2 F2 c2F2' },
    { id: 'b7', name: 'Fill: том-ролл', abc: '[F4na] !<(![c2A][c2A] [c2A][c2A] [c2A]!<)![c2A]' },
    { id: 'b8', name: 'Fill: томы + краш', abc: '[F4na] z2 ee d2d2 A2A2' },
    { id: 'b9', name: 'Amen Break', abc: 'c2c2 [F2c][F2c] c2c2 [F2c]F2' },
    { id: 'b10', name: 'Funky Drummer', abc: '[g2F][g2F] [g2c][g2F] [F4na] [c4na]' },
    { id: 'b11', name: 'Think Break', abc: '[F2c][F2c] F2[F2c] [F2c][F2c] F4' },
    { id: 'b12', name: 'Reggaeton break', abc: '[F2g]gc [F2g][g2c] (3[LcF]!pp!cc(3cc!f!Le [LA2F]Lc2' },
    
	 
    // Advanced
    { id: 'a1', name: 'Advanced 1', abc: '[F2g2]g2 [c2g2]gc [F2g2]g2 [c2g2]g2 ' },
    { id: 'a2', name: 'Advanced 2', abc: '[F2g2]g2 [c2g2]gc [F2g2][g2F] [c2g2]g2  ' },
    { id: 'a3', name: 'Advanced 3', abc: '[F2g2]g2 [c2g2]gc [F2g2]g2 [c2g2]gc' },
    { id: 'a4', name: 'Advanced 4', abc: '[F2g2]g2 [c2g2]gc [F2g2][g2F] [c2g2][g2F]' },
    { id: 'a5', name: 'Advanced 5', abc: '[F2g2]g2 [c2g2]gc [Fg]c[F2g] [c2g2]g2 ' },
    { id: 'a6', name: 'Advanced 6', abc: '[F2g2][F2g] [c2g2]gc [Fg]c[F2g] [c2g2]g2 ' },
    { id: 'a7', name: 'Advanced 7', abc: '[F2g2][F2g] [c2g2]gc [Fg]c[F2g] [c2g2][g2c2]' },
    { id: 'a8', name: 'Advanced 8', abc: '[F2g2][F2g] [c2g2]gc [Fg]c[F2g] [c2g2][g2F2]' },

    
    // Jazz
    { id: 'j1', name: 'Jazz Walk', abc: '[g2F][g2c] [g2F][g2c] [g2F][g2c] [g2F][g2c]' },
    { id: 'j2', name: 'Brush Swing', abc: '[g3F]g [g3c]g [g3F]g [g3c]g' },
    { id: 'j3', name: 'Jazz Waltz', abc: '[g2F]g2 [g2c]g2 [g2F]g2' },
    { id: 'j4', name: 'Brush Ballad', abc: '[g4F] z4 [g2c][g2F] z4' },
    { id: 'j5', name: 'Latin Jazz', abc: '[g2F][g2c] [g2F][g2c] [g2F][g2c] [g2F][g2c]' },
    { id: 'j6', name: 'Bebop Groove', abc: '[g2F]g2 [g2c]g2 g2[g2F] [g2c]g2' },
    { id: 'j7', name: 'Bossa Nova1', abc: '[g2cF]g2 [g2D][g2cF] [g2F]g2 [g2cD][g2F]' },
    { id: 'j8', name: 'Bossa Nova2', abc: '[g2F]g2 [g2cD][g2F] [g2F][g2c] [g2D][g2F]' },
    
    // Latin
	{ id: 'l1', name: 'Samba Basic', abc: '[g2F]g2 [g2c]g2 [g2F]g2 [g2c]g2' },
    { id: 'l3', name: 'Rumba', abc: '[g2F]g2 [g2c][g2F] [g2F]g2 [g2c]g2' },
    { id: 'l4', name: 'Cha-Cha', abc: '[g2F][g2F] [g2c]g2 [g2F][g2F] [g2c]g2' },
    { id: 'l5', name: 'Mambo', abc: '[g2F]g2 [g2c][g2F] g2[g2F] [g2c]g2' },
	{ id: 'l6', name: 'Reggaeton', abc: '[F2g]gc [F2g][g2c] [F2g]gc [F2g][g2c]' },
	{ id: 'l7', name: 'Reggaeton2', abc: '[F2g]gc [F2g][g2c] [Fg]cgc [F2g][g2c]' },
	{ id: 'l8', name: 'New Brazilian(Moreira)', abc: 'L[Fg]g[Fg]g cgg[Fg] L[Fg]ggc gLg2[Fg]' },
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

  // === Инициализация чекбоксов курсора после DOM ready ===
  document.addEventListener('DOMContentLoaded', () => {
    showCursor = document.getElementById("show-cursor");
    colorNote = document.getElementById("color-note");

    measureCountSelect.addEventListener('change', updateMeasuresUI);
    updateMeasuresUI();
    applyBtn.click();
  });

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
      { id: 'fill', name: 'Fill', prefix: 'b' },
      { id: 'advanced', name: 'Advanced', prefix: 'a' },
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
%%percmap D  pedal-hi-hat x
%%percmap =c side stick x
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
        const cursorControl = new CursorControl();
        synthControl = new ABCJS.synth.SynthController();
        synthControl.load(audioDiv, cursorControl, {
          displayLoop: true,
          displayRestart: false,
          displayPlay: true,
          displayProgress: false,
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
  </script>
</body>
</html>