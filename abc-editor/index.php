<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Редактор перкуссии с курсором и перетаскиванием</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/abcjs@6.5.2/abcjs-audio.min.css">
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
      padding: 20px;
      background: #fff;
      border-right: 1px solid #ddd;
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
    }

    .sidebar textarea {
      flex: 1;
      font-family: monospace;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      resize: none;
    }

    .sidebar button {
      margin-top: 10px;
      padding: 10px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
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
      margin-top: 10px;
    }

    /* Стили для выделения при перетаскивании */
    .abcjs-dragging {
      stroke: blue !important;
      fill: blue !important;
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/abcjs@6.5.2/dist/abcjs-basic.min.js"></script>
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <textarea id="abc" spellcheck="false">X:1
T:Drumz
L:1/16
M:4/4
K:C clef=perc
Q:1/4=90
V:Drums stem=down
U:n=!style=x!
%%percmap F  acoustic-bass-drum
%%percmap c  acoustic-snare
%%percmap g  closed-hi-hat x
%%percmap e  high-tom
%%percmap d  hi-mid-tom
%%percmap A  low-tom
%%percmap a  crash-cymbal-1  x
g4 g4 cccc cccc | [F2g2]g2 [c2g2]g2 [F2g2]g2 [c2g2]g2 |</textarea>
      <button id="renderBtn">Обновить</button>
    </div>

    <div class="resizer" id="resizer"></div>

    <div class="main">
      <div id="paper"></div>
      <div id="audio-controls"></div>
      <div class="cursor-nav">
        <label>
          <input type="checkbox" id="show-cursor" checked> Показывать курсор
        </label>
      </div>
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
    let abcString = document.getElementById("abc").value;
    const abcTextarea = document.getElementById("abc");
    const paperDiv = document.getElementById("paper");
    const audioDiv = document.getElementById("audio-controls");
    const showCursor = document.getElementById("show-cursor");
    const renderBtn = document.getElementById("renderBtn");

    // Порядок перкусионных нот для перетаскивания
    const percNotes = ['F', 'c', 'g', 'e', 'd', 'A', 'a'];

    function modifyPercNote(note, step) {
      const index = percNotes.indexOf(note);
      if (index === -1) return note;
      const newIndex = Math.max(0, Math.min(percNotes.length - 1, index - step));
      return percNotes[newIndex];
    }

    function tokenize(str) {
      // Разбиваем строку на токены: ноты, декорации, аккорды и т.д.
      const arr = str.split(/(!.+?!|".+?")/);
      const output = [];
      for (let i = 0; i < arr.length; i++) {
        const token = arr[i];
        if (token.length > 0) {
          if (token[0] !== '"' && token[0] !== '!') {
            const arr2 = token.split(/([A-Ga-g][,']*)/);
            output.push(...arr2.filter(t => t.length > 0));
          } else {
            output.push(token);
          }
        }
      }
      return output;
    }

    function clickListener(abcelem, tuneNumber, classes, analysis, drag, mouseEvent) {
      if (drag && drag.step && abcelem.el_type === 'note' && abcelem.startChar >= 0 && abcelem.endChar >= 0) {
        const originalText = abcString.substring(abcelem.startChar, abcelem.endChar);
        const tokens = tokenize(originalText);
        let modified = false;
        
        // Модифицируем только ноты
        for (let i = 0; i < tokens.length; i++) {
          if (percNotes.includes(tokens[i])) {
            tokens[i] = modifyPercNote(tokens[i], drag.step);
            modified = true;
          }
        }
        
        if (modified) {
          const newText = tokens.join("");
          abcString = abcString.substring(0, abcelem.startChar) + newText + abcString.substring(abcelem.endChar);
          abcTextarea.value = abcString;
          refreshTune();
        }
      }
    }

    function CursorControl() {
      const self = this;
      self.onEvent = function(ev) {
        let cursor = document.querySelector("#paper svg .abcjs-cursor");
        if (!cursor) {
          const svg = document.querySelector("#paper svg");
          if (svg) {
            cursor = document.createElementNS("http://www.w3.org/2000/svg", "line");
            cursor.setAttribute("class", "abcjs-cursor");
            svg.appendChild(cursor);
          }
        }
        if (cursor && showCursor.checked && ev && ev.left != null) {
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
        const cursor = document.querySelector("#paper svg .abcjs-cursor");
        if (cursor) {
          cursor.setAttribute("x1", -10);
          cursor.setAttribute("x2", -10);
        }
      };
    }

    function refreshTune() {
      paperDiv.innerHTML = "";
      audioDiv.innerHTML = "";
      abcString = abcTextarea.value.trim();

      if (!abcString) return;

      try {
        const options = {
          add_classes: true,
          responsive: "resize",
          dragging: true,
          clickListener: clickListener
        };

        const vo = ABCJS.renderAbc(paperDiv, abcString, options);
        if (!vo || vo.length === 0) throw new Error("Render failed");
        visualObj = vo[0];

        if (ABCJS.synth.supportsAudio()) {
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

          const bpmMatch = abcString.match(/Q:\s*1\/4\s*=\s*(\d+)/i);
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
        }
      } catch (e) {
        console.error("Render error:", e);
        paperDiv.innerHTML = `<p style="color:red;">Ошибка в нотной записи: ${e.message}</p>`;
      }
    }

    document.addEventListener("DOMContentLoaded", () => {
      refreshTune();
      renderBtn.addEventListener("click", refreshTune);
      let debounceTimer;
      abcTextarea.addEventListener("input", () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(refreshTune, 1000);
      });
    });
  </script>
</body>
</html>
