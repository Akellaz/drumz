(function() {
  if (window.BlockRegistry && window.BlockRegistry['grid']) {
    console.log("⏭️ Блок 'grid' уже зарегистрирован, пропускаем.");
    return;
  }

  if (typeof window.Block === 'undefined') {
    console.error('❌ ОШИБКА: window.Block не найден. Проверь LessonEngine.js');
    return;
  }

  class GridBlock extends window.Block {
    render() {
      this._injectGridStyles();
      
      this.element = document.createElement('div');
      this.element.className = 'le-grid-composite';
      
      this.engine.state.target = {
        hh: this.normalizePattern(this.data.target_hh || ''),
        snare: this.normalizePattern(this.data.target_snare || ''),
        kick: this.normalizePattern(this.data.target_kick || '')
      };

      const isExercise = this.data.showCheck === 'true';

      if (isExercise) {
        this.engine.state.user = { hh: Array(16).fill('-'), snare: Array(16).fill('-'), kick: Array(16).fill('-') };
      } else {
        this.engine.state.user = {
          hh: this.patternToArray(this.engine.state.target.hh),
          snare: this.patternToArray(this.engine.state.target.snare),
          kick: this.patternToArray(this.engine.state.target.kick)
        };
      }
      
      this.renderGrid(isExercise);
      this.renderNotation();
      this.renderPlayButton();
      
      if (isExercise) {
        this.renderCheckButton();
      }
      
      this.engine.els.blocksContainer.appendChild(this.element);
    }




    _injectGridStyles() {
      if (document.getElementById('dle-grid-styles')) return;
      const style = document.createElement('style');
      style.id = 'dle-grid-styles';
      style.textContent = `
        .le-grid { display: flex; flex-direction: column; gap: 16px; }
        .le-track-row { display: flex; align-items: center; gap: 12px; transition: opacity 0.2s; }
        .le-track-name { width: 40px; font-size: 13px; font-weight: 700; text-align: right; color: #64748b; display: flex; align-items: center; justify-content: flex-end; gap: 6px; }
        .le-track-steps { display: flex; gap: 0px; flex-wrap: wrap; }
        .le-track-steps.disabled { pointer-events: none; opacity: 0.35; }
        .le-beat-group { display: flex; gap: 4px; }

        /* ═══ ЯЧЕЙКА СЕТКИ (невидимая, только кликабельная область) ═══ */
        .le-step {
          width: 24px; height: 24px;
          border: none;
          background: transparent;
          cursor: pointer;
          transition: all 0.12s ease;
          display: flex; align-items: center; justify-content: center;
          transform: scale(1);
          position: relative;
        }
        .le-step:hover { background: #f1f5f9; border-radius: 50%; }
        .le-step:active { transform: scale(0.9); }

        /* ═══ ХЭТ: SVG-КРЕСТИК "ОТ РУКИ" (заполняет ячейку) ═══ */
        .le-step[data-track="hh"].active {
          background: transparent;
          background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><path d='M 3 5 C 8 9, 16 15, 21 19' stroke='%231e293b' stroke-width='1.8' fill='none' stroke-linecap='round'/><path d='M 21 4 C 15 8, 9 16, 4 20' stroke='%231e293b' stroke-width='2.2' fill='none' stroke-linecap='round'/></svg>");
          background-size: 100% 100%;
          background-repeat: no-repeat;
          background-position: center;
        }
        .le-step[data-track="hh"].active::before,
        .le-step[data-track="hh"].active::after {
          content: none;
        }

        /* ═══ БАРАБАНЫ: SVG-ГОЛОВКА С ПУЛЬСАЦИЕЙ (заполняет ячейку) ═══ */
        .le-step[data-track="snare"].active,
        .le-step[data-track="kick"].active {
          background: transparent;
          background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><ellipse cx='12' cy='12' rx='10' ry='7' fill='%231e293b' transform='rotate(-20 12 12)'><animate attributeName='rx' values='10;10.5;10' dur='2s' repeatCount='indefinite'/><animate attributeName='ry' values='7;7.3;7' dur='2s' repeatCount='indefinite'/></ellipse></svg>");
          background-size: 100% 100%;
          background-repeat: no-repeat;
          background-position: center;
        }
        .le-step[data-track="snare"].active::before,
        .le-step[data-track="kick"].active::before {
          content: none;
        }

        /* ═══ ПРОВЕРКА: CORRECT / ERROR ═══ */
        .le-step.correct {
          background: #22c55e !important;
          border-radius: 50%;
        }
        .le-step.correct::before,
        .le-step.correct::after { display: none; }

        .le-step.error {
          background: #ef4444 !important;
          border-radius: 50%;
        }
        .le-step.error::before,
        .le-step.error::after { display: none; }
      `;
      document.head.appendChild(style);
    }
	
	
	
	
	
	
    renderGrid(isExercise) {
      const grid = document.createElement('div');
      grid.className = 'le-grid';
      
      const tracks = [
        { id: 'hh', name: 'HH', show: this.data.show_hh !== 'false' },
        { id: 'snare', name: 'SN', show: this.data.show_snare !== 'false' },
        { id: 'kick', name: 'KT', show: this.data.show_kick !== 'false' }
      ];
      
      tracks.forEach(track => {
        if (!track.show) return;
        
        const row = document.createElement('div');
        row.className = 'le-track-row';
        row.innerHTML = `<div class="le-track-name">${track.name}</div>`;
        
        const stepsContainer = document.createElement('div');
        stepsContainer.className = 'le-track-steps';
        
        for (let b = 0; b < 4; b++) {
          const beatGroup = document.createElement('div');
          beatGroup.className = 'le-beat-group';
          beatGroup.dataset.beat = b;
          
          for (let i = 0; i < 4; i++) {
            const idx = b * 4 + i;
            const step = document.createElement('div');
            step.className = 'le-step';
            step.dataset.track = track.id;
            step.dataset.step = idx;
            
            if (!isExercise && this.engine.state.user[track.id][idx] !== '-') {
              step.classList.add('active');
            } else {
              step.onclick = () => this.toggleStep(track.id, idx, step);
            }
            
            beatGroup.appendChild(step);
          }
          stepsContainer.appendChild(beatGroup);
        }
        row.appendChild(stepsContainer);
        grid.appendChild(row);
      });
      
      this.element.appendChild(grid);
    }
    
    renderNotation() {
      if (typeof GrooveUtils === 'undefined') return;
      
      const gu = new GrooveUtils();
      const grooveData = new gu.grooveDataNew();
      grooveData.timeDivision = 16;
      grooveData.tempo = this.engine.state.bpm;
      grooveData.numberOfMeasures = 1;
      
      const showHH = this.data.show_hh !== 'false';
      const showSN = this.data.show_snare !== 'false';
      const showKT = this.data.show_kick !== 'false';
      
      const hhData = showHH ? this.engine.state.target.hh : '|----------------|';
      const snareData = showSN ? this.engine.state.target.snare : '|----------------|';
      const kickData = showKT ? this.engine.state.target.kick : '|----------------|';
      
      grooveData.hh_array = gu.noteArraysFromURLData('H', hhData, 16, 1);
      grooveData.snare_array = gu.noteArraysFromURLData('S', snareData, 16, 1);
      grooveData.kick_array = gu.noteArraysFromURLData('K', kickData, 16, 1);
      
      const abc = gu.createABCFromGrooveData(grooveData, 600);
      const svg = gu.renderABCtoSVG(abc).svg;
      
      const notation = document.createElement('div');
      notation.className = 'le-notation';
      notation.innerHTML = svg;
      this.element.appendChild(notation);
    }
    
    renderPlayButton() {
      const btn = document.createElement('button');
      btn.className = 'le-btn le-btn-play';
      btn.textContent = '▶ Слушать';
      btn.onclick = () => { this.engine.playSequence(this.engine.state.target); };
      this.element.appendChild(btn);
    }
    
    renderCheckButton() {
      const btn = document.createElement('button');
      btn.className = 'le-btn';
      btn.textContent = '✓ Проверить';
      btn.onclick = () => this.checkAnswer();
      this.element.appendChild(btn);
      this.checkBtn = btn;
    }
    
    toggleStep(trackId, idx, el) {
      if (this.engine.state.isLocked) return;
      const next = this.engine.state.user[trackId][idx] === '-' ? 'o' : '-';
      this.engine.state.user[trackId][idx] = next;
      
      if (next === 'o') {
        el.classList.add('active');
      } else {
        el.classList.remove('active', 'correct', 'error');
      }
    }
    
    checkAnswer() {
      let errors = 0;
      let perfect = true;
      
      const tracksToCheck = [
        { id: 'hh', show: this.data.show_hh !== 'false' },
        { id: 'snare', show: this.data.show_snare !== 'false' },
        { id: 'kick', show: this.data.show_kick !== 'false' }
      ];
      
      tracksToCheck.forEach(t => {
        if (!t.show) return;
        for (let i = 0; i < 16; i++) {
          const target = (this.engine.state.target[t.id][i + 1] || '-') !== '-';
          const user = this.engine.state.user[t.id][i] === 'o';
          
          if (target !== user) {
            errors++;
            perfect = false;
            const stepEl = this.element.querySelector(`.le-step[data-track="${t.id}"][data-step="${i}"]`);
            if (stepEl) stepEl.classList.add('error');
          }
        }
      });
      
      if (perfect) {
        this.engine.state.isLocked = true;
        this.element.querySelectorAll('.le-step.active').forEach(el => el.classList.add('correct'));
        this.element.querySelectorAll('.le-beat-group').forEach(el => el.classList.add('correct'));
        if (this.checkBtn) {
          this.checkBtn.disabled = true;
          this.checkBtn.textContent = '✅ Отлично!';
        }
      } else {
        if (this.checkBtn) {
          this.checkBtn.textContent = `⚠️ Ошибок: ${errors}`;
          setTimeout(() => {
            this.checkBtn.textContent = '✓ Проверить';
            this.element.querySelectorAll('.le-step.error').forEach(el => el.classList.remove('error'));
          }, 2000);
        }
      }
    }
    
    normalizePattern(str) {
      const len = 16;
      if (!str) return '|' + '-'.repeat(len) + '|';
      let clean = str.replace(/^\||\|$/g, '');
      clean = clean.padEnd(len, '-').slice(0, len);
      return '|' + clean + '|';
    }

    patternToArray(pattern) {
      const clean = pattern.replace(/^\||\|$/g, '');
      return clean.split('');
    }
  }

  window.BlockRegistry['grid'] = GridBlock;
  console.log("✅ grid.js выполнен: блок 'grid' зарегистрирован в BlockRegistry");
})();