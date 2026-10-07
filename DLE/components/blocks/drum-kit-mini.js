(function() {
  if (window.BlockRegistry && window.BlockRegistry['drumkit-mini']) {
    return;
    }

  if (typeof window.Block === 'undefined') {
    console.error('❌ ОШИБКА: window.Block не найден.');
    return;
  }

  class DrumKitMiniBlock extends window.Block {
    constructor(engine, data) {
      super(engine, data);
      this.options = {
        activePart: data.activePart || null,
        dimOthers: data.dimOthers === 'true'
      };
      this.callbacks = {
        onHit: (part) => { console.log('Mini Kit Hit:', part); }
      };
      this.stageEl = null;
    }

    render() {
      this._injectStyles();
      this.stageEl = document.createElement('div');
      this.stageEl.className = 'dkm-stage';
      this.stageEl.innerHTML =
        '<div class="dkm-part" data-part="crash"><div class="dkm-wrap"><div class="dkm-cymbal"></div></div></div>' +
        '<div class="dkm-part" data-part="tom1"><div class="dkm-wrap"><div class="dkm-head"></div></div></div>' +
        '<div class="dkm-part" data-part="tom2"><div class="dkm-wrap"><div class="dkm-head"></div></div></div>' +
        '<div class="dkm-part" data-part="hihat"><div class="dkm-wrap"><div class="dkm-cymbal"></div></div></div>' +
        '<div class="dkm-part" data-part="snare-drum"><div class="dkm-wrap"><div class="dkm-head"></div></div></div>' +
        '<div class="dkm-part" data-part="ride"><div class="dkm-wrap"><div class="dkm-cymbal"></div></div></div>' +
        '<div class="dkm-part" data-part="bass"><div class="dkm-wrap"><div class="dkm-head"></div></div></div>' +
        '<div class="dkm-part" data-part="floor-tom"><div class="dkm-wrap"><div class="dkm-head"></div></div></div>';
      
      this.engine.els.blocksContainer.appendChild(this.stageEl);
      
      if (this.options.activePart && this.options.dimOthers) this._applyHighlight();
      this._bindEvents();
    }

    _applyHighlight() {
      this.stageEl.querySelectorAll('.dkm-part').forEach(p => {
        if (p.dataset.part === this.options.activePart) p.classList.add('dkm-hl');
        else p.classList.add('dkm-dim');
      });
    }

    _bindEvents() {
      this.stageEl.querySelectorAll('.dkm-part').forEach(el => {
        const fire = () => {
          if (el.classList.contains('dkm-dim')) return;
          const part = el.dataset.part;
          const isC = ['crash', 'ride', 'hihat'].includes(part);
          const cls = isC ? 'dkm-chit' : 'dkm-dhit';
          el.classList.remove(cls); void el.offsetWidth; el.classList.add(cls);
          if (this.callbacks.onHit) this.callbacks.onHit(part);
        };
        el.addEventListener('mousedown', fire);
        el.addEventListener('touchstart', e => { e.preventDefault(); fire(); }, { passive: false });
      });
    }

    destroy() { 
      if (this.stageEl) this.stageEl.remove(); 
    }

    _injectStyles() {
      if (document.getElementById('dkm-styles')) return;
      const s = document.createElement('style');
      s.id = 'dkm-styles';
      s.textContent = `
.dkm-stage{position:relative;width:100%;max-width:800px;margin:20px auto 0;aspect-ratio:1.1/1;background:rgba(241,245,249,.5);border-radius:2rem;border:1px solid #e2e8f0;overflow:hidden}
@media(min-width:768px){.dkm-stage{aspect-ratio:2.4/1}}
.dkm-part{position:absolute;cursor:pointer;-webkit-tap-highlight-color:transparent;transition:opacity .3s}
.dkm-part.dkm-dim{opacity:.2;filter:grayscale(1);pointer-events:none}
.dkm-part.dkm-hl{animation:dkm-pulse 1.5s ease-in-out infinite}
@keyframes dkm-pulse{0%,100%{filter:drop-shadow(0 0 8px rgba(59,130,246,.4))}50%{filter:drop-shadow(0 0 20px rgba(59,130,246,.8))}}
.dkm-wrap{position:relative;width:100%;padding-bottom:100%}
.dkm-head,.dkm-cymbal{position:absolute;top:0;left:0;width:100%;height:100%;border-radius:50%;transition:transform .1s}
.dkm-head{background:#fff;box-shadow:inset 0 0 30px rgba(0,0,0,.05),0 10px 20px rgba(0,0,0,.1)}
.dkm-cymbal{background:linear-gradient(to bottom right,#fef08a,#eab308,#a16207);box-shadow:0 10px 20px rgba(0,0,0,.15);border-bottom:4px solid rgba(133,77,14,.4)}
.dkm-stage [data-part="bass"]{width:30%;bottom:10%;left:50%;transform:translateX(-50%);z-index:10}
@media(min-width:768px){.dkm-stage [data-part="bass"]{width:26%}}
.dkm-stage [data-part="bass"] .dkm-head{border:12px solid #1e293b;background:#fff}
.dkm-stage [data-part="snare-drum"]{width:18%;left:22%;bottom:24%;z-index:30}
@media(min-width:768px){.dkm-stage [data-part="snare-drum"]{width:15%}}
.dkm-stage [data-part="snare-drum"] .dkm-head{border:8px solid #94a3b8;background:#f8fafc}
.dkm-stage [data-part="floor-tom"]{width:24%;right:5%;bottom:8%;z-index:20}
.dkm-stage [data-part="floor-tom"] .dkm-head{border:10px solid #1e293b;background:#fff}
.dkm-stage [data-part="tom1"]{width:16%;left:30%;top:10%;z-index:20}
.dkm-stage [data-part="tom1"] .dkm-head{border:8px solid #1e293b;background:#fff}
.dkm-stage [data-part="tom2"]{width:16%;right:30%;top:10%;z-index:20}
.dkm-stage [data-part="tom2"] .dkm-head{border:8px solid #1e293b;background:#fff}
.dkm-stage [data-part="hihat"]{width:20%;left:2%;top:42%;z-index:40}
.dkm-stage [data-part="crash"]{width:26%;left:2%;top:2%;z-index:45}
.dkm-stage [data-part="ride"]{width:28%;right:2%;top:5%;z-index:25}
.dkm-dhit .dkm-head{animation:dkm-vib .1s ease-out}
.dkm-chit .dkm-cymbal{animation:dkm-sway 1.2s cubic-bezier(.36,0,.66,-.56) forwards}
@keyframes dkm-vib{0%{transform:scale(1)}50%{transform:scale(.92) translateY(5px);filter:brightness(1.5)}100%{transform:scale(1)}}
@keyframes dkm-sway{0%{transform:rotate(0) scale(1)}15%{transform:rotate(6deg) scale(1.05);filter:brightness(1.2)}30%{transform:rotate(-5deg)}100%{transform:rotate(0)}}`;
      document.head.appendChild(s);
    }
  }

  window.BlockRegistry['drumkit-mini'] = DrumKitMiniBlock;
  console.log("✅ drum-kit-mini.js выполнен: блок зарегистрирован");
})();