(function() {
  if (window.BlockRegistry && window.BlockRegistry['drumkit-real']) {
    return;
  }

  if (typeof window.Block === 'undefined') {
    console.error('❌ ОШИБКА: window.Block не найден.');
    return;
  }

  class DrumKitRealBlock extends window.Block {
    constructor(engine, data) {
      super(engine, data);
      this.options = {
        activePart: data.activePart || null,
        dimOthers: data.dimOthers === 'true',
        showStaff: data.showStaff === 'true',
        sounds: {
          'crash': '/DLE/components/sound/Crash.mp3',
          'ride': '/DLE/components/sound/Ride.mp3',
          'hihat': '/DLE/components/sound/Hi Hat Normal.mp3',
          'tom1': '/DLE/components/sound/10 Tom.mp3',
          'tom2': '/DLE/components/sound/16 Tom.mp3',
          'snare-drum': '/DLE/components/sound/Snare Normal.mp3',
          'bass': '/DLE/components/sound/Kick.mp3',
          'floor-tom': '/DLE/components/sound/Floor Tom.mp3'
        }
      };
      this.callbacks = { onHit: (part) => { console.log('Real Kit Hit:', part); } };
      this.stageEl = null;
      this.staffEl = null;
      this.notesContainer = null;
      this.noteCounter = 0;
      this.noteStep = 55;
      this.notePositions = {
        'crash': 20, 'ride': 30, 'hihat': 25,
        'tom1': 35, 'tom2': 40, 'snare-drum': 45,
        'floor-tom': 55, 'bass': 65
      };
    }

    render() {
      this._injectStyles();
      this.stageEl = document.createElement('div');
      this.stageEl.className = 'dkr-stage';
      this.stageEl.innerHTML =
        '<div class="dkr-part" data-part="crash"><div class="dkr-wrap"><div class="dkr-cymbal"></div></div></div>' +
        '<div class="dkr-part" data-part="tom1"><div class="dkr-wrap"><div class="dkr-head"></div></div></div>' +
        '<div class="dkr-part" data-part="tom2"><div class="dkr-wrap"><div class="dkr-head"></div></div></div>' +
        '<div class="dkr-part" data-part="hihat"><div class="dkr-wrap"><div class="dkr-cymbal"></div></div></div>' +
        '<div class="dkr-part" data-part="snare-drum"><div class="dkr-wrap"><div class="dkr-head"></div></div></div>' +
        '<div class="dkr-part" data-part="ride"><div class="dkr-wrap"><div class="dkr-cymbal"></div></div></div>' +
        '<div class="dkr-part" data-part="bass"><div class="dkr-wrap"><div class="dkr-head"></div></div></div>' +
        '<div class="dkr-part" data-part="floor-tom"><div class="dkr-wrap"><div class="dkr-head"></div></div></div>';
      
      this.engine.els.blocksContainer.appendChild(this.stageEl);

      if (this.options.activePart && this.options.dimOthers) this._applyHighlight();

      if (this.options.showStaff) {
        this.staffEl = document.createElement('div');
        this.staffEl.className = 'dkr-staff';
        this.staffEl.innerHTML =
          '<div class="dkr-staff-lines"></div>' +
          '<div class="dkr-notes"></div>' +
          '<button class="dkr-clear">Очистить</button>';
        this.engine.els.blocksContainer.appendChild(this.staffEl);
        this.notesContainer = this.staffEl.querySelector('.dkr-notes');
        this.staffEl.querySelector('.dkr-clear').addEventListener('click', () => this.clearStaff());
      }
      this._bindEvents();
    }

    _applyHighlight() {
      this.stageEl.querySelectorAll('.dkr-part').forEach(p => {
        if (p.dataset.part === this.options.activePart) p.classList.add('dkr-highlighted');
        else p.classList.add('dkr-dimmed');
      });
    }

    _bindEvents() {
      this.stageEl.querySelectorAll('.dkr-part').forEach(el => {
        const fire = () => {
          if (el.classList.contains('dkr-dimmed')) return;
          const part = el.dataset.part;
          const isC = ['crash', 'ride', 'hihat'].includes(part);
          const cls = isC ? 'dkr-cymbal-hit' : 'dkr-drum-hit';
          el.classList.remove(cls); void el.offsetWidth; el.classList.add(cls);
          this._playSound(part);
          if (this.options.showStaff) this._addNote(part, isC);
          if (this.callbacks.onHit) this.callbacks.onHit(part);
        };
        el.addEventListener('mousedown', fire);
        el.addEventListener('touchstart', e => { e.preventDefault(); fire(); }, { passive: false });
      });
    }

    _playSound(part) {
      const f = this.options.sounds[part];
      if (!f) return;
      const a = new Audio(f); 
      a.volume = 0.8;
      a.play().catch(() => {});
    }

    _addNote(part, isCymbal) {
      if (!this.notesContainer) return;
      const n = document.createElement('div');
      n.className = isCymbal ? 'dkr-note dkr-note-x' : 'dkr-note';
      n.style.top = this.notePositions[part] + '%';
      const x = 20 + this.noteCounter * this.noteStep;
      n.style.left = x + 'px';
      this.notesContainer.appendChild(n);
      this.noteCounter++;
      if (x > 600) {
        this.notesContainer.style.transform = 'translateX(-' + (x - 600) + 'px)';
        this.notesContainer.style.transition = 'transform 0.3s ease-out';
      }
    }

    clearStaff() {
      if (!this.notesContainer) return;
      this.notesContainer.innerHTML = '';
      this.notesContainer.style.transform = 'translateX(0)';
      this.noteCounter = 0;
    }

    destroy() {
      if (this.stageEl) this.stageEl.remove();
      if (this.staffEl) this.staffEl.remove();
    }

    _injectStyles() {
      if (document.getElementById('dkr-styles')) return;
      const s = document.createElement('style');
      s.id = 'dkr-styles';
      s.textContent = `
.dkr-stage{position:relative;width:100%;max-width:800px;aspect-ratio:2.4/1;margin:20px auto;background:linear-gradient(180deg,#fff 0%,#f8f9fa 100%);border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.06)}
.dkr-part{position:absolute;cursor:pointer;transition:opacity .3s;-webkit-tap-highlight-color:transparent}
.dkr-part:hover:not(.dkr-dimmed) .dkr-head,.dkr-part:hover:not(.dkr-dimmed) .dkr-cymbal{transform:scale(1.05);filter:brightness(1.08)}
.dkr-part:active:not(.dkr-dimmed) .dkr-head,.dkr-part:active:not(.dkr-dimmed) .dkr-cymbal{transform:scale(.95);filter:brightness(.95)}
.dkr-part.dkr-dimmed{opacity:.2;filter:grayscale(1);pointer-events:none}
.dkr-part.dkr-highlighted{animation:dkr-pulse 1.5s ease-in-out infinite}
@keyframes dkr-pulse{0%,100%{filter:drop-shadow(0 0 8px rgba(59,130,246,.4))}50%{filter:drop-shadow(0 0 20px rgba(59,130,246,.8))}}
.dkr-wrap{position:relative;width:100%;padding-bottom:100%}
.dkr-cymbal{position:absolute;top:0;left:0;width:100%;height:100%;border-radius:50%;transition:transform .15s,filter .15s;background:radial-gradient(circle at 35% 35%,rgba(255,255,255,.7) 0%,transparent 25%),repeating-radial-gradient(circle at center,#fcd34d 0px,#d97706 2px,#fcd34d 4px);box-shadow:0 4px 8px rgba(0,0,0,.15),inset 0 2px 4px rgba(255,255,255,.6),inset 0 -2px 4px rgba(0,0,0,.2)}
.dkr-cymbal::after{content:'';position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:22%;height:22%;border-radius:50%;background:radial-gradient(circle at 30% 30%,#fde047,#b45309);box-shadow:inset 0 2px 4px rgba(0,0,0,.3),0 2px 4px rgba(0,0,0,.2)}
.dkr-head{position:absolute;top:0;left:0;width:100%;height:100%;border-radius:50%;transition:transform .15s,filter .15s;background:radial-gradient(circle at 50% 50%,#fff 0%,#f1f5f9 60%,#e2e8f0 100%);box-shadow:inset 0 0 10px rgba(0,0,0,.08),0 4px 8px rgba(0,0,0,.1)}
.dkr-stage [data-part="snare-drum"] .dkr-head,.dkr-stage [data-part="tom1"] .dkr-head,.dkr-stage [data-part="tom2"] .dkr-head,.dkr-stage [data-part="floor-tom"] .dkr-head{border:6px solid #cbd5e1;outline:3px solid #94a3b8;outline-offset:-9px}
.dkr-stage [data-part="snare-drum"] .dkr-head{background:radial-gradient(circle at 50% 50%,#fff 0%,#f8fafc 100%)}
.dkr-stage [data-part="tom1"] .dkr-head,.dkr-stage [data-part="tom2"] .dkr-head,.dkr-stage [data-part="floor-tom"] .dkr-head{background:radial-gradient(circle at 50% 50%,#64748b 0%,#475569 100%)}
.dkr-stage [data-part="bass"] .dkr-head{background:radial-gradient(circle at 50% 50%,#64748b 0%,#475569 100%);border:8px solid #cbd5e1;outline:4px solid #94a3b8;outline-offset:-12px}
.dkr-stage [data-part="bass"] .dkr-head::after{content:'DRUMZ';position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:14px;font-weight:900;color:rgba(255,255,255,.2);letter-spacing:2px}
.dkr-stage [data-part="bass"]{width:30%;bottom:10%;left:50%;transform:translateX(-50%);z-index:10}
.dkr-stage [data-part="floor-tom"]{width:20%;right:10%;bottom:10%;z-index:20}
.dkr-stage [data-part="tom1"]{width:15%;left:33%;top:18%;z-index:20}
.dkr-stage [data-part="tom2"]{width:15%;right:33%;top:18%;z-index:20}
.dkr-stage [data-part="ride"]{width:24%;right:8%;top:8%;z-index:25}
.dkr-stage [data-part="snare-drum"]{width:16%;left:24%;bottom:22%;z-index:30}
.dkr-stage [data-part="hihat"]{width:18%;left:6%;top:40%;z-index:40}
.dkr-stage [data-part="crash"]{width:22%;left:8%;top:5%;z-index:45}
.dkr-drum-hit .dkr-head{animation:dkr-vib .1s ease-out;filter:brightness(1.15)}
.dkr-cymbal-hit .dkr-cymbal{animation:dkr-sway 1.2s cubic-bezier(.36,0,.66,-.56) forwards}
@keyframes dkr-vib{0%{transform:scale(1)}40%{transform:scale(.94) translateY(4px)}100%{transform:scale(1)}}
@keyframes dkr-sway{0%{transform:rotate(0) scale(1);filter:brightness(1.3)}15%{transform:rotate(8deg) scale(1.05);filter:brightness(1.15)}30%{transform:rotate(-6deg) scale(1.02)}100%{transform:rotate(0) scale(1);filter:brightness(1)}}
.dkr-staff{position:relative;width:100%;max-width:800px;aspect-ratio:2.4/1;margin:20px auto;background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.06)}
.dkr-staff-lines{position:absolute;top:50%;left:10%;right:10%;height:40%;transform:translateY(-50%);background:repeating-linear-gradient(to bottom,#cbd5e1 0px,#cbd5e1 2px,transparent 2px,transparent calc(25% - 1px),#cbd5e1 calc(25% - 1px),#cbd5e1 25%)}
.dkr-notes{position:absolute;top:0;left:10%;right:10%;height:100%;pointer-events:none}
.dkr-note{position:absolute;width:40px;height:22px;border-radius:50%;border-top:2px solid #475569;border-bottom:2px solid #475569;border-left:8px solid #475569;border-right:8px solid #475569;background:transparent;transform:translate(-50%,-50%) rotate(-25deg);animation:dkr-na .4s cubic-bezier(.68,-.55,.265,1.55)}
.dkr-note.dkr-note-x{width:32px;height:32px;border:none;border-radius:0;transform:translate(-50%,-50%)}
.dkr-note.dkr-note-x::before,.dkr-note.dkr-note-x::after{content:'';position:absolute;top:50%;left:50%;width:6px;height:100%;background:#475569;border-radius:3px}
.dkr-note.dkr-note-x::before{transform:translate(-50%,-50%) rotate(45deg)}
.dkr-note.dkr-note-x::after{transform:translate(-50%,-50%) rotate(-45deg)}
@keyframes dkr-na{0%{transform:translate(-50%,-50%) rotate(-25deg) scale(0);opacity:0}100%{transform:translate(-50%,-50%) rotate(-25deg) scale(1);opacity:1}}
.dkr-note.dkr-note-x{animation-name:dkr-nax}
@keyframes dkr-nax{0%{transform:translate(-50%,-50%) scale(0);opacity:0}100%{transform:translate(-50%,-50%) scale(1);opacity:1}}
.dkr-clear{position:absolute;top:12px;right:12px;padding:6px 12px;font-size:12px;font-weight:600;background:#fff;border:1px solid #e2e8f0;border-radius:6px;cursor:pointer;color:#475569;transition:all .15s}
.dkr-clear:hover{background:#ef4444;border-color:#ef4444;color:#fff}`;
      document.head.appendChild(s);
    }
  }

  window.BlockRegistry['drumkit-real'] = DrumKitRealBlock;
  console.log("✅ drum-kit-real.js выполнен: блок зарегистрирован");
})();