(function() {
  if (window.BlockRegistry && window.BlockRegistry['animation']) {
    return;
  }

  if (typeof window.Block === 'undefined') {
    console.error('❌ ОШИБКА: window.Block не найден.');
    return;
  }

  class AnimationBlock extends window.Block {
    constructor(engine, data) {
      super(engine, data);
      this.sequence = (typeof data.sequence === 'string' && data.sequence) 
                      ? data.sequence.split(',').filter(Boolean) 
                      : (data.sequence || []);
      this.bpm = parseInt(data.bpm, 10) || 100;
      this.timer = null;
      this.currentIndex = 0;
    }

    render() {
      this.element = document.createElement('div');
      this.element.className = 'le-animation-block';
      this.element.innerHTML = `
        <div class="le-animation-canvas" id="anim-canvas-${this.element.dataset.index || '0'}"></div>
        <div class="le-animation-controls">
          <button class="le-anim-btn le-anim-play" id="anim-play-${this.element.dataset.index || '0'}">▶ Запустить анимацию</button>
        </div>
      `;
      this.engine.els.blocksContainer.appendChild(this.element);
      
      this.canvasEl = this.element.querySelector('.le-animation-canvas');
      this.playBtn = this.element.querySelector('.le-anim-play');
      
      this.renderSVG();
      
      this.playBtn.onclick = () => this.toggleAnimation();
    }

    renderSVG() {
      if (this.sequence.length === 0) {
        this.canvasEl.innerHTML = '<div style="text-align:center; color:#94a3b8; padding:20px;">Добавьте ноты в редакторе</div>';
        return;
      }

      // Генерируем SVG на основе последовательности
      let notesSVG = '';
      const startX = 100;
      const stepX = 100; // Равномерный шаг для прототипа
      const y = 140; // Средняя линия стана (как в нашем тесте)

      this.sequence.forEach((noteType, index) => {
        const x = startX + (index * stepX);
        if (noteType === 'quarter') {
          notesSVG += `
            <g class="anim-note" data-index="${index}">
              <ellipse fill="#000000" transform="rotate(-25 ${x} ${y})" ry="16" rx="24" cy="${y}" cx="${x}"/>
              <line stroke-linecap="round" stroke-width="5" stroke="#000000" y2="${y-96}" x2="${x+20.5}" y1="${y-8}" x1="${x+20.5}"/>
            </g>`;
        } else if (noteType === 'eighth') {
          notesSVG += `
            <g class="anim-note" data-index="${index}">
              <ellipse fill="#000000" transform="rotate(-25 ${x} ${y})" ry="16" rx="24" cy="${y}" cx="${x}"/>
              <line stroke-linecap="round" stroke-width="5" stroke="#000000" y2="${y-96.5}" x2="${x+20.5}" y1="${y-8.5}" x1="${x+20.5}"/>
              <rect fill="#000000" height="12" width="40" y="${y-103}" x="${x+18}"/>
            </g>`;
        }
      });

      this.canvasEl.innerHTML = `
        <svg viewBox="0 0 600 200" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
          <style>
            .anim-note {
              transform-box: fill-box;
              transform-origin: center;
              transition: transform 0.15s ease-in-out;
            }
            .anim-note.is-pulsing {
              transform: scale(1.2);
            }
            .anim-note.is-pulsing ellipse, .anim-note.is-pulsing line, .anim-note.is-pulsing rect {
              fill: #3b82f6 !important;
              stroke: #2563eb !important;
            }
          </style>
          <!-- Нотный стан -->
          <g stroke="#000000" stroke-width="0.5" stroke-linecap="round">
            <line x1="40" y1="103" x2="560" y2="103" />
            <line x1="40" y1="121.5" x2="560" y2="121.5" />
            <line x1="40" y1="140" x2="560" y2="140" />
            <line x1="40" y1="158.5" x2="560" y2="158.5" />
            <line x1="40" y1="177" x2="560" y2="177" />
          </g>
          ${notesSVG}
        </svg>
      `;
    }

    toggleAnimation() {
      if (this.timer) {
        this.stopAnimation();
      } else {
        this.startAnimation();
      }
    }

    startAnimation() {
      this.playBtn.textContent = '⏹ Остановить';
      this.playBtn.classList.add('active');
      this.currentIndex = 0;
      this.playNext();
    }

    playNext() {
      // Сбрасываем пульсацию со всех нот
      this.canvasEl.querySelectorAll('.anim-note').forEach(el => el.classList.remove('is-pulsing'));

      // Если дошли до конца, начинаем сначала (зацикливаем)
      if (this.currentIndex >= this.sequence.length) {
        this.currentIndex = 0;
      }

      // Добавляем пульсацию текущей ноте
      const currentNote = this.canvasEl.querySelector(`.anim-note[data-index="${this.currentIndex}"]`);
      if (currentNote) {
        currentNote.classList.add('is-pulsing');
      }

      // Расчет времени: 60000 мс / BPM = длительность четверти. 
      // Для простоты прототипа каждый шаг = четверть. (Восьмые можно будет учесть позже)
      const msPerStep = 60000 / this.bpm;

      this.currentIndex++;
      this.timer = setTimeout(() => this.playNext(), msPerStep);
    }

    stopAnimation() {
      if (this.timer) {
        clearTimeout(this.timer);
        this.timer = null;
      }
      this.canvasEl.querySelectorAll('.anim-note').forEach(el => el.classList.remove('is-pulsing'));
      this.playBtn.textContent = '▶ Запустить анимацию';
      this.playBtn.classList.remove('active');
    }

    destroy() {
      this.stopAnimation();
      super.destroy();
    }
  }

  window.BlockRegistry['animation'] = AnimationBlock;
  console.log("✅ animation.js выполнен: блок 'animation' зарегистрирован");
})();