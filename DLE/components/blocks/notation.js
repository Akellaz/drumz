(function() {
  if (window.BlockRegistry && window.BlockRegistry['notation']) {
    return;
  }

  if (typeof window.Block === 'undefined') {
    console.error('❌ ОШИБКА: window.Block не найден.');
    return;
  }

  class NotationBlock extends window.Block {
    constructor(engine, data) {
      super(engine, data);
      this.synthControl = null;
      this.midiBuffer = null;
      this.lastHighlighted = [];
      this.currentTuneObject = null;
    }

    render() {
      this.element = document.createElement('div');
      this.element.className = 'le-notation-block';
      this.element.id = 'notation-block-' + Math.random().toString(36).substr(2, 9);
      
      let html = '<div class="le-notation" id="paper-' + this.element.id + '"></div>';
      
      // Если включено аудио, добавляем ТОЛЬКО контейнер для кнопки Play (без галочек)
      if (this.data.enable_audio_highlight === 'true') {
        html += `<div id="audio-controls-${this.element.id}"></div>`;
      }
      
      this.element.innerHTML = html;
      this.engine.els.blocksContainer.appendChild(this.element);
      
      this.paperElement = this.element.querySelector(`#paper-${this.element.id}`);
      
      if (this.data.abc) {
        this.renderABC();
      }
    }
    
    renderABC() {
      if (!this.paperElement || typeof ABCJS === 'undefined') {
        console.warn("ABCJS не загружен или контейнер не найден");
        return;
      }
      
      const header = `X:1
M:4/4
L:1/16
K:C clef=perc
Q:60
V:Drums stem=up
%%percmap D pedal-hi-hat x
%%percmap F acoustic-bass-drum
%%percmap G low-floor-tom
%%percmap A high-floor-tom
%%percmap B low-tom
%%percmap c acoustic-snare
%%percmap =c side-stick x
%%percmap d low-mid-tom
%%percmap e hi-mid-tom
%%percmap f high-tom
%%percmap ^f ride-cymbal-1 x
%%percmap g closed-hi-hat x
%%percmap ^g open-hi-hat
%%percmap a crash-cymbal-1 x
`;
      this.paperElement.innerHTML = '';
      
      const renderParams = {
        add_classes: true,
        responsive: "resize",
        scale: 0.8,
        staffwidth: parseInt(this.data.staffwidth, 10) || 600
      };

      const visualObj = ABCJS.renderAbc(this.paperElement, header + this.data.abc, renderParams);
      if (!visualObj || visualObj.length === 0) return;
      
      this.currentTuneObject = visualObj[0];

      if (this.data.enable_audio_highlight === 'true' && ABCJS.synth && ABCJS.synth.supportsAudio()) {
        this.initAudio();
      }
    }

    initAudio() {
      const audioContainer = document.getElementById(`audio-controls-${this.element.id}`);
      if (!audioContainer) return;

      // Читаем настройки из данных блока (а не из DOM-элементов)
      const showCursor = this.data.show_cursor === 'true';
      const showHighlight = this.data.show_highlight === 'true';

      const cursorControl = {
        onStart: () => {},
        onBeat: () => {},
        onEvent: (ev) => {
          // Сброс предыдущей подсветки
          this.lastHighlighted.forEach(el => el.classList.remove("highlight"));
          this.lastHighlighted = [];

          // Подсветка нот (если включена в настройках)
          if (ev && ev.elements && showHighlight) {
            ev.elements.forEach(note => {
              note.forEach(el => {
                el.classList.add("highlight");
                this.lastHighlighted.push(el);
              });
            });
          }

          // Курсор (если включен в настройках)
          let cursor = this.paperElement.querySelector("svg .abcjs-cursor");
          if (!cursor) {
            const svg = this.paperElement.querySelector("svg");
            if (svg) {
              cursor = document.createElementNS("http://www.w3.org/2000/svg", "line");
              cursor.setAttribute("class", "abcjs-cursor");
              svg.appendChild(cursor);
            }
          }

          if (cursor && showCursor && ev && ev.left != null) {
            cursor.setAttribute("x1", ev.left - 2);
            cursor.setAttribute("x2", ev.left - 2);
            cursor.setAttribute("y1", ev.top || 0);
            cursor.setAttribute("y2", (ev.top || 0) + (ev.height || 30));
          } else if (cursor) {
            cursor.setAttribute("x1", -10);
            cursor.setAttribute("x2", -10);
          }
        },
        onFinished: () => {
          this.lastHighlighted.forEach(el => el.classList.remove("highlight"));
          this.lastHighlighted = [];
          const cursor = this.paperElement.querySelector("svg .abcjs-cursor");
          if (cursor) {
            cursor.setAttribute("x1", -10);
            cursor.setAttribute("x2", -10);
          }
        }
      };

      this.synthControl = new ABCJS.synth.SynthController();
      this.synthControl.load(audioContainer, cursorControl, {
        displayLoop: false,
        displayRestart: false,
        displayPlay: true,
        displayProgress: false,
        displayClock: false,
        displayWarp: false,
      });
      this.synthControl.disable(true);

      this.midiBuffer = new ABCJS.synth.CreateSynth();
      const bpm = this.engine.state.bpm || 90;
      const millisecondsPerMeasure = (4 / bpm) * 60 * 1000;

      this.midiBuffer.init({
        visualObj: this.currentTuneObject,
        millisecondsPerMeasure: millisecondsPerMeasure,
      })
      .then(() => this.synthControl.setTune(this.currentTuneObject, false))
      .then(() => this.synthControl.disable(false))
      .catch(e => {
        console.error("Ошибка аудио в NotationBlock:", e);
        if (audioContainer) audioContainer.innerHTML = `<p style="color:#ef4444; font-size:12px;">Ошибка загрузки аудио</p>`;
        if (this.synthControl) this.synthControl.disable(true);
        this.midiBuffer = null;
      });
    }

    destroy() {
      if (this.synthControl) {
        this.synthControl.disable(true);
        this.synthControl = null;
      }
      this.midiBuffer = null;
      this.lastHighlighted.forEach(el => el.classList.remove("highlight"));
      this.lastHighlighted = [];
      super.destroy();
    }
  }

  window.BlockRegistry['notation'] = NotationBlock;
  console.log("✅ notation.js выполнен: блок 'notation' зарегистрирован с поддержкой аудио");
})();