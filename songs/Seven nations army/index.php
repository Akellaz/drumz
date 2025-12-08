<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Разбор барабанной партии</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/abcjs@6.5.2/abcjs-audio.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, sans-serif;
      margin: 0;
      background: #f9f9f9;
      height: 100vh;
      overflow: hidden;
    }

    .container {
      display: flex;
      height: 100%;
    }

    .sidebar {
      width: 300px;
      background: #fff;
      border-right: 1px solid #ddd;
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .sidebar-header {
      padding: 15px 20px;
      border-bottom: 1px solid #eee;
      background: #f8f9fa;
    }

    .sidebar-header h3 {
      margin: 0 0 10px 0;
      font-size: 16px;
      color: #333;
    }

    .audio-player {
      padding: 15px 20px;
      border-bottom: 1px solid #eee;
      background: #f8f9fa;
    }

    .audio-player audio {
      width: 100%;
      margin-bottom: 10px;
    }

    .player-title {
      font-size: 12px;
      color: #666;
      margin: 5px 0 0 0;
      text-align: center;
    }

    .fragment-list {
      flex: 1;
      overflow-y: auto;
      padding: 10px 0;
    }

    .fragment-item {
      padding: 12px 20px;
      border-bottom: 1px solid #eee;
      cursor: pointer;
      transition: background 0.2s;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .fragment-item:hover {
      background: #f5f5f5;
    }

    .fragment-item.active {
      background: #007bff;
      color: white;
    }

    .fragment-item.active:hover {
      background: #0069d9;
    }

    .fragment-title {
      font-weight: 500;
      margin: 0;
      font-size: 14px;
    }

    .download-btn {
      background: rgba(255, 255, 255, 0.2);
      border: none;
      color: white;
      padding: 3px 8px;
      border-radius: 3px;
      cursor: pointer;
      font-size: 12px;
      opacity: 0.7;
    }

    .download-btn:hover {
      opacity: 1;
      background: rgba(255, 255, 255, 0.3);
    }

    .resizer {
      width: 5px;
      background: #ccc;
      cursor: col-resize;
      user-select: none;
    }

    .main {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-width: 0;
    }

    #paper {
      background: white;
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      width: 100%;
      margin-bottom: 20px;
      text-align: center;
    }

    #paper svg {
      width: 100%;
      height: auto;
    }

    #audio-controls {
      margin: 10px 0;
    }

    .cursor-nav {
      margin: 15px 0;
      text-align: center;
    }

    .cursor-nav label {
      margin: 0 15px;
      user-select: none;
      font-size: 14px;
      color: #333;
    }

    #downloadPdfBtn {
      display: block;
      margin: 15px auto;
      padding: 10px 20px;
      background-color: #805ad5;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
      text-align: center;
      border: none;
      cursor: pointer;
      font-size: 14px;
    }

    #downloadPdfBtn:hover {
      background-color: #6b46c1;
    }

    /* Стили для подсветки нот */
    .highlight {
      fill: #0a9ecc !important;
    }

    @media (max-width: 768px) {
      .cursor-nav {
        display: flex;
        flex-direction: column;
        gap: 10px;
      }
      .cursor-nav label {
        margin: 5px 0;
      }
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/abcjs@6.5.2/dist/abcjs-basic.min.js"></script>
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <div class="sidebar-header">
        <h3>Фрагменты партии</h3>
      </div>

      <div class="fragment-list" id="fragment-list">
        <!-- Фрагменты будут добавлены здесь через JavaScript -->
      </div>
	  <div class="audio-player">
        <audio id="mp3-player" controls>
          <source src="Seven nations army_plus.mp3" type="audio/mpeg">
          Ваш браузер не поддерживает аудио элемент.
        </audio>
        <div class="player-title">Seven Nations Army</div>
      </div>
    </div>

    <div class="resizer" id="resizer"></div>

    <div class="main">
      <div id="paper"></div>
	        <div class="cursor-nav">
        <label>
          <input type="checkbox" id="show-cursor" checked> Показывать курсор
        </label>
        <label>
          <input type="checkbox" id="color-note" checked> Подсвечивать ноты
        </label>
      </div>
      <div id="audio-controls"></div>
      <button id="downloadPdfBtn">📥 Скачать в PDF</button>

    </div>
  </div>

  <script>
    // === Drag Resizer Logic ===
    const resizer = document.getElementById("resizer");
    const sidebar = document.querySelector(".sidebar");

    resizer.addEventListener("mousedown", (e) => {
      document.addEventListener("mousemove", resize);
      document.addEventListener("mouseup", stopResize);
      e.preventDefault();
    });

    function resize(e) {
      const containerRect = document.querySelector(".container").getBoundingClientRect();
      const newWidth = e.clientX - containerRect.left;
      if (newWidth > 100 && newWidth < containerRect.width - 200) {
        sidebar.style.width = newWidth + "px";
      }
    }

    function stopResize() {
      document.removeEventListener("mousemove", resize);
      document.removeEventListener("mouseup", stopResize);
    }

    // === ABCJS Logic ===
    let synthControl = null;
    let visualObj = null;
    const paperDiv = document.getElementById("paper");
    const fragmentList = document.getElementById("fragment-list");
    const audioDiv = document.getElementById("audio-controls");
    const showCursor = document.getElementById("show-cursor");
    const colorNote = document.getElementById("color-note");
    const downloadPdfBtn = document.getElementById("downloadPdfBtn");

    // Переменные для управления подсветкой
    let lastHighlighted = [];

    // Массив фрагментов (вся партия тоже фрагмент)
    const fragments = [
      {
        id: 0,
        title: "Вся партия",
        abc: `X:1
T:Seven Nations Army
L:1/16
M:4/4
K:C clef=perc
Q:1/4=120
V:Drums stem=down
U:n=!style=x!
%%percmap F  acoustic-bass-drum
%%percmap c  acoustic-snare
%%percmap g  closed-hi-hat x
%%percmap e  high-tom
%%percmap d  hi-mid-tom
%%percmap A  low-tom
%%percmap a  crash-cymbal-1  x
z16 | z16 | z16 | z16 |
|: [F4A] [F4A] [F4A] [F4A] | [F4A] [F4A] [F4A] [F4A] | [F4A] [F4A] [F4A] [F4A] | [F4A] [F4A] [F4A] [F4A] :|
|: [F4A] [F4Ac] [F4A] [F4Ac] | [F4A] [F4Ac] [F4A] [F4Ac] | [F4A] [F4Ac] [F4A] [F4Ac] | [F4A] [F4Ac] [F4A] [F4Ac] :|
[F4a] [A2c][A2c] [A2c][A2c] [A2c][A2c] | [F4a] [A2c][A2c] [A2c][A2c] [A2c][A2c]
|: [F4a] [c4a] [F4a] [c4a] | [F4a] [c4a] [F4a] [c4a] | [F4a] [c4a] [F4a] z2[c2] | [F4a] [c4a] [F4a] [c4a] :|
[F4a] [A2c][A2c] [A2c][A2c] [A2c][A2c] | [F4a] [A2c][A2c] [A2c][A2c] [A2c][A2c] | [F4a] z4 z8
`
      },
      {
        id: 1,
        title: "Вступление",
        abc: `X:1
T:Вступление
L:1/16
M:4/4
K:C clef=perc
Q:1/4=120
V:Drums stem=down
U:n=!style=x!
%%percmap F  acoustic-bass-drum
%%percmap c  acoustic-snare
%%percmap g  closed-hi-hat x
%%percmap e  high-tom
%%percmap d  hi-mid-tom
%%percmap A  low-tom
%%percmap a  crash-cymbal-1  x
z16 | z16 | z16 | z16 |`
      },
      {
        id: 2,
        title: "Куплет 1",
        abc: `X:1
T:Куплет
L:1/16
M:4/4
K:C clef=perc
Q:1/4=120
V:Drums stem=down
U:n=!style=x!
%%percmap F  acoustic-bass-drum
%%percmap c  acoustic-snare
%%percmap g  closed-hi-hat x
%%percmap e  high-tom
%%percmap d  hi-mid-tom
%%percmap A  low-tom
%%percmap a  crash-cymbal-1  x
|: [F4A] [F4A] [F4A] [F4A] | [F4A] [F4A] [F4A] [F4A] | [F4A] [F4A] [F4A] [F4A] | [F4A] [F4A] [F4A] [F4A] :|`
      },
	        {
        id: 3,
        title: "Куплет 2",
        abc: `X:1
T:Куплет
L:1/16
M:4/4
K:C clef=perc
Q:1/4=120
V:Drums stem=down
U:n=!style=x!
%%percmap F  acoustic-bass-drum
%%percmap c  acoustic-snare
%%percmap g  closed-hi-hat x
%%percmap e  high-tom
%%percmap d  hi-mid-tom
%%percmap A  low-tom
%%percmap a  crash-cymbal-1  x
|: [F4A] [F4Ac] [F4A] [F4Ac] | [F4A] [F4Ac] [F4A] [F4Ac] | [F4A] [F4Ac] [F4A] [F4Ac] | [F4A] [F4Ac] [F4A] [F4Ac] :|`
      },
	  	        {
        id: 4,
        title: "Переход",
        abc: `X:1
T:Куплет
L:1/16
M:4/4
K:C clef=perc
Q:1/4=120
V:Drums stem=down
U:n=!style=x!
%%percmap F  acoustic-bass-drum
%%percmap c  acoustic-snare
%%percmap g  closed-hi-hat x
%%percmap e  high-tom
%%percmap d  hi-mid-tom
%%percmap A  low-tom
%%percmap a  crash-cymbal-1  x
[F4a] [A2c][A2c] [A2c][A2c] [A2c][A2c] | [F4a] [A2c][A2c] [A2c][A2c] [A2c][A2c]`
      },
      {
        id: 5,
        title: "Припев",
        abc: `X:1
T:Припев
L:1/16
M:4/4
K:C clef=perc
Q:1/4=120
V:Drums stem=down
U:n=!style=x!
%%percmap F  acoustic-bass-drum
%%percmap c  acoustic-snare
%%percmap g  closed-hi-hat x
%%percmap e  high-tom
%%percmap d  hi-mid-tom
%%percmap A  low-tom
%%percmap a  crash-cymbal-1  x
|: [F4a] [c4a] [F4a] [c4a] | [F4a] [c4a] [F4a] [c4a] | [F4a] [c4a] [F4a] z2[c2] | [F4a] [c4a] [F4a] [c4a] :|`
      },
	  
	  	  	        {
        id: 6,
        title: "Окончание",
        abc: `X:1
T:Куплет
L:1/16
M:4/4
K:C clef=perc
Q:1/4=120
V:Drums stem=down
U:n=!style=x!
%%percmap F  acoustic-bass-drum
%%percmap c  acoustic-snare
%%percmap g  closed-hi-hat x
%%percmap e  high-tom
%%percmap d  hi-mid-tom
%%percmap A  low-tom
%%percmap a  crash-cymbal-1  x
[F4a] [A2c][A2c] [A2c][A2c] [A2c][A2c] | [F4a] [A2c][A2c] [A2c][A2c] [A2c][A2c] | [F4a] z4 z8`
      }
	  
    ];

    let currentFragment = fragments[0]; // По умолчанию показываем всю партию

    function CursorControl() {
      const self = this;
      
      self.onEvent = function(ev) {
        // Очищаем предыдущую подсветку
        lastHighlighted.forEach(el => {
          if (el.classList) {
            el.classList.remove("highlight");
          }
        });
        lastHighlighted = [];

        // Подсвечиваем текущие ноты
        if (ev && ev.elements && colorNote && colorNote.checked) {
          ev.elements.forEach(note => {
            note.forEach(el => {
              if (el.classList) {
                el.classList.add("highlight");
                lastHighlighted.push(el);
              }
            });
          });
        }

        // Показываем курсор
        let cursor = document.querySelector("#paper svg .abcjs-cursor");
        if (!cursor) {
          const svg = document.querySelector("#paper svg");
          if (svg) {
            cursor = document.createElementNS("http://www.w3.org/2000/svg", "line");
            cursor.setAttribute("class", "abcjs-cursor");
            cursor.setAttribute("style", "stroke: red; stroke-width: 2px;");
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
        // Очищаем подсветку
        lastHighlighted.forEach(el => {
          if (el.classList) {
            el.classList.remove("highlight");
          }
        });
        lastHighlighted = [];

        // Скрываем курсор
        const cursor = document.querySelector("#paper svg .abcjs-cursor");
        if (cursor) {
          cursor.setAttribute("x1", -10);
          cursor.setAttribute("x2", -10);
        }
      };
    }

    function renderABC(element, abcString) {
      element.innerHTML = "";
      try {
        const vo = ABCJS.renderAbc(element, abcString, {
          add_classes: true,
          responsive: "resize"
        });
        return vo[0];
      } catch (e) {
        console.error("Render error:", e);
        element.innerHTML = `<p style="color:red;">Ошибка в нотной записи: ${e.message}</p>`;
        return null;
      }
    }

    function createSynthController(visualObj) {
      if (!ABCJS.synth.supportsAudio()) return null;
      
      // Очищаем предыдущий аудио контроллер
      if (synthControl) {
        synthControl.pause();
      }
      
      audioDiv.innerHTML = "";
      
      const cursorControl = new CursorControl();
      synthControl = new ABCJS.synth.SynthController();
      
      synthControl.load(audioDiv, cursorControl, {
        displayLoop: false,
        displayRestart: false,
        displayPlay: true,
        displayProgress: false,
        displayWarp: false
      });
      
      synthControl.disable(true);
      
      const bpmMatch = currentFragment.abc.match(/Q:\s*1\/4\s*=\s*(\d+)/i);
      const bpm = bpmMatch ? parseInt(bpmMatch[1]) : 120;
      const msPerMeasure = (4 / bpm) * 60 * 1000;
      
      const midiBuffer = new ABCJS.synth.CreateSynth();
      midiBuffer.init({
        visualObj: visualObj,
        millisecondsPerMeasure: msPerMeasure
      }).then(() => {
        synthControl.setTune(visualObj, false).then(() => {
          synthControl.disable(false);
        });
      }).catch(err => {
        console.error("Synth init error:", err);
        audioDiv.innerHTML = "<p style='color:red;'>Ошибка инициализации аудио.</p>";
      });
      
      return synthControl;
    }

    function renderFragment(fragment) {
      currentFragment = fragment;
      const visualObj = renderABC(paperDiv, fragment.abc);
      if (visualObj) {
        createSynthController(visualObj);
      }
      renderFragmentList();
    }

    function renderFragmentList() {
      fragmentList.innerHTML = "";
      
      fragments.forEach(fragment => {
        const fragmentItem = document.createElement("div");
        fragmentItem.className = "fragment-item";
        if (currentFragment.id === fragment.id) {
          fragmentItem.classList.add("active");
        }
        
        fragmentItem.innerHTML = `
          <h4 class="fragment-title">${fragment.title}</h4>
          <button class="download-btn" data-id="${fragment.id}">PDF</button>
        `;
        
        fragmentItem.addEventListener("click", (e) => {
          if (!e.target.classList.contains('download-btn')) {
            renderFragment(fragment);
          }
        });
        
        // Обработчик для кнопки скачивания
        const downloadBtn = fragmentItem.querySelector('.download-btn');
        downloadBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          downloadFragmentAsPdf(fragment);
        });
        
        fragmentList.appendChild(fragmentItem);
      });
    }

    function downloadFragmentAsPdf(fragment) {
      const svg = paperDiv.querySelector('svg');
      if (!svg) {
        alert('Нет нот для сохранения!');
        return;
      }

      // Временно отображаем фрагмент для экспорта
      const originalContent = paperDiv.innerHTML;
      const visualObj = renderABC(paperDiv, fragment.abc);
      
      setTimeout(() => {
        const svg = paperDiv.querySelector('svg');
        if (!svg) {
          paperDiv.innerHTML = originalContent;
          return;
        }

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
          pdf.text(fragment.title, 105, 15, null, null, 'center');
          pdf.setFontSize(12);
          
          // Извлекаем темп из ABC
          const bpmMatch = fragment.abc.match(/Q:\s*1\/4\s*=\s*(\d+)/i);
          const bpm = bpmMatch ? `Темп: ${bpmMatch[1]} BPM` : '';
          pdf.text(bpm, 105, 25, null, null, 'center');
          
          const imgData = canvas.toDataURL('image/png');
          const w = 180;
          const h = (canvas.height * w) / canvas.width;
          pdf.addImage(imgData, 'PNG', 15, 35, w, h);
          pdf.save(`${fragment.title.replace(/\s+/g, '_')}.pdf`);
          URL.revokeObjectURL(url);
          
          // Восстанавливаем оригинальный контент
          paperDiv.innerHTML = originalContent;
          if (currentFragment) {
            renderFragment(currentFragment);
          }
        };
        
        img.onerror = () => {
          alert('Ошибка создания PDF.');
          URL.revokeObjectURL(url);
          paperDiv.innerHTML = originalContent;
          if (currentFragment) {
            renderFragment(currentFragment);
          }
        };
        
        img.src = url;
      }, 100);
    }

    function downloadCurrentFragmentAsPdf() {
      downloadFragmentAsPdf(currentFragment);
    }

    document.addEventListener("DOMContentLoaded", () => {
      renderFragmentList();
      renderFragment(currentFragment); // Отображаем фрагмент по умолчанию
      
      let debounceTimer;
      const refreshOptions = () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
          renderFragment(currentFragment);
        }, 100);
      };
      
      showCursor.addEventListener("change", refreshOptions);
      colorNote.addEventListener("change", refreshOptions);
      
      // Обработчик для кнопки скачивания текущего фрагмента
      downloadPdfBtn.addEventListener('click', downloadCurrentFragmentAsPdf);
    });
  </script>
</body>
</html>
