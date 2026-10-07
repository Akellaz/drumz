// Функция инициализации, которая будет пытаться зарегистрировать блок, пока не будет готов движок
function initCssArtBlock() {
  // Ждем, пока LessonEngine инициализирует window.Block и window.BlockRegistry
  if (typeof window.Block === 'undefined' || !window.BlockRegistry) {
    console.warn('⏳ Ожидание инициализации LessonEngine (BlockRegistry)...');
    setTimeout(initCssArtBlock, 50); // Пробуем снова через 50мс
    return;
  }

  if (window.BlockRegistry['css-art']) {
    return; // Уже зарегистрирован, выходим
  }

  // === ПРЕСЕТЫ CSS-АРТ ===
  window.CssArtPresets = [
    {
      name: "📋 ИНСТРУКЦИЯ ДЛЯ ИИ (скопируй в чат)",
      code: `<!-- 
╔═══════════════════════════════════════════════════════════════╗
║  ИНСТРУКЦИЯ ДЛЯ СОЗДАНИЯ CSS-ИЛЛЮСТРАЦИЙ ДЛЯ ПРОЕКТА DRUMZ  ║
╚═══════════════════════════════════════════════════════════════╝
... (твой оригинальный код инструкции) ...
-->`
    },
    {
      name: "— Выбрать пресет —",
      code: ""
    },
    {
      name: "Целая → 2 половинные (click)",
      code: `<style>
  .dz-canvas {
    position: relative; width: 100%; max-width: 600px; aspect-ratio: 2.4/1;
    margin: 20px auto;
    background: radial-gradient(circle at 50% 80%, #ffffff 0%, #e2e8f0 70%);
    border-radius: 1.5rem; border: 1px solid #cbd5e1;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
    display: flex; justify-content: center; align-items: center;
    cursor: pointer; transition: transform 0.2s ease; user-select: none;
  }
  .dz-canvas:hover { transform: scale(1.01); }
  .dz-whole-note {
    position: absolute; width: 180px; height: 100px; border-radius: 50%;
    border-top: 6px solid #1e293b; border-bottom: 6px solid #1e293b;
    border-left: 35px solid #1e293b; border-right: 35px solid #1e293b;
    background: transparent;
    transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    z-index: 2;
  }
  .dz-canvas.is-split .dz-whole-note { transform: scale(0.5); opacity: 0; }
  .dz-half-note {
    position: absolute; width: 160px; height: 90px; opacity: 0; z-index: 1;
    transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
  }
  .dz-canvas.is-split .dz-half-note.left { opacity: 1; transform: translateX(-150px); }
  .dz-canvas.is-split .dz-half-note.right { opacity: 1; transform: translateX(150px); }
  .dz-half-note::before {
    content: ''; position: absolute; bottom: 0; left: 0;
    width: 160px; height: 90px; border-radius: 50%;
    border-top: 20px solid #1e293b; border-bottom: 20px solid #1e293b;
    border-left: 8px solid #1e293b; border-right: 8px solid #1e293b;
    background: transparent; transform: rotate(-25deg);
  }
  .dz-half-note::after {
    content: ''; position: absolute; bottom: 79px; right: -13px;
    width: 8px; height: 180px; background: #1e293b; border-radius: 2px;
  }
  .dz-hint {
    position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%);
    font-family: system-ui, sans-serif; font-size: 14px; color: #64748b;
    opacity: 0.8; pointer-events: none;
  }
</style>
<div class="dz-canvas" onclick="this.classList.toggle('is-split');">
  <div class="dz-whole-note"></div>
  <div class="dz-half-note left"></div>
  <div class="dz-half-note right"></div>
  <div class="dz-hint">Кликни по ноте, чтобы разделить её</div>
</div>`
    },
    {
      name: "Целая → 4 четвертные (click)",
      code: `<style>
  .dz-canvas {
    position: relative; width: 100%; max-width: 700px; aspect-ratio: 3/1;
    margin: 20px auto;
    background: radial-gradient(circle at 50% 80%, #ffffff 0%, #e2e8f0 70%);
    border-radius: 1.5rem; border: 1px solid #cbd5e1;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
    display: flex; justify-content: center; align-items: center;
    cursor: pointer; transition: transform 0.2s ease; user-select: none;
  }
  .dz-canvas:hover { transform: scale(1.01); }
  .dz-whole-note {
    position: absolute; width: 180px; height: 100px; border-radius: 50%;
    border-top: 6px solid #1e293b; border-bottom: 6px solid #1e293b;
    border-left: 35px solid #1e293b; border-right: 35px solid #1e293b;
    background: transparent;
    transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    z-index: 2;
  }
  .dz-whole-note.is-split { transform: scale(0.3); opacity: 0; }
  .dz-quarter-note {
    position: absolute; width: 100px; height: 60px; opacity: 0; z-index: 1;
    transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
  }
  .dz-canvas.is-split-4 .dz-quarter-note.q1 { opacity: 1; transform: translateX(-220px); }
  .dz-canvas.is-split-4 .dz-quarter-note.q2 { opacity: 1; transform: translateX(-75px); }
  .dz-canvas.is-split-4 .dz-quarter-note.q3 { opacity: 1; transform: translateX(75px); }
  .dz-canvas.is-split-4 .dz-quarter-note.q4 { opacity: 1; transform: translateX(220px); }
  .dz-quarter-note::before {
    content: ''; position: absolute; bottom: 0; left: 0;
    width: 100px; height: 60px; border-radius: 50%;
    border: 12px solid #1e293b; background: #1e293b;
    transform: rotate(-25deg);
  }
  .dz-quarter-note::after {
    content: ''; position: absolute; bottom: 50px; right: -8px;
    width: 8px; height: 140px; background: #1e293b; border-radius: 2px;
  }
  .dz-hint {
    position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%);
    font-family: system-ui, sans-serif; font-size: 14px; color: #64748b;
    opacity: 0.8; pointer-events: none;
  }
</style>
<div class="dz-canvas" onclick="this.classList.toggle('is-split-4'); this.querySelector('.dz-whole-note').classList.toggle('is-split');">
  <div class="dz-whole-note"></div>
  <div class="dz-quarter-note q1"></div>
  <div class="dz-quarter-note q2"></div>
  <div class="dz-quarter-note q3"></div>
  <div class="dz-quarter-note q4"></div>
  <div class="dz-hint">Кликни, чтобы разделить на 4 четверти</div>
</div>`
    },
    {
      name: "Статичная целая нота (без анимации)",
      code: `<style>
  .dz-canvas {
    position: relative; width: 100%; max-width: 400px; aspect-ratio: 2/1;
    margin: 20px auto;
    background: radial-gradient(circle at 50% 80%, #ffffff 0%, #e2e8f0 70%);
    border-radius: 1.5rem; border: 1px solid #cbd5e1;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
    display: flex; justify-content: center; align-items: center;
  }
  .dz-whole-note {
    width: 180px; height: 100px; border-radius: 50%;
    border-top: 6px solid #1e293b; border-bottom: 6px solid #1e293b;
    border-left: 35px solid #1e293b; border-right: 35px solid #1e293b;
    background: transparent;
  }
</style>
<div class="dz-canvas">
  <div class="dz-whole-note"></div>
</div>`
    },
    {
      name: "Нота с hover-эффектом",
      code: `<style>
  .dz-canvas {
    position: relative; width: 100%; max-width: 400px; aspect-ratio: 2/1;
    margin: 20px auto;
    background: radial-gradient(circle at 50% 80%, #ffffff 0%, #e2e8f0 70%);
    border-radius: 1.5rem; border: 1px solid #cbd5e1;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
    display: flex; justify-content: center; align-items: center;
  }
  .dz-whole-note {
    width: 180px; height: 100px; border-radius: 50%;
    border-top: 6px solid #1e293b; border-bottom: 6px solid #1e293b;
    border-left: 35px solid #1e293b; border-right: 35px solid #1e293b;
    background: transparent;
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
  }
  .dz-canvas:hover .dz-whole-note {
    transform: scale(1.15) rotate(-5deg);
    border-color: #007bff;
  }
  .dz-hint {
    position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%);
    font-family: system-ui, sans-serif; font-size: 14px; color: #64748b;
    opacity: 0.8;
  }
</style>
<div class="dz-canvas">
  <div class="dz-whole-note"></div>
  <div class="dz-hint">Наведи курсор на ноту</div>
</div>`
    },
    {
      name: "Метроном (авто-пульс)",
      code: `<style>
  .dz-canvas {
    position: relative; width: 100%; max-width: 400px; aspect-ratio: 2/1;
    margin: 20px auto;
    background: radial-gradient(circle at 50% 80%, #ffffff 0%, #e2e8f0 70%);
    border-radius: 1.5rem; border: 1px solid #cbd5e1;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
    display: flex; justify-content: center; align-items: center;
    flex-direction: column; gap: 20px;
  }
  .dz-pulse {
    width: 80px; height: 80px; border-radius: 50%;
    background: #007bff;
    box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7);
    animation: dz-beat 1s infinite;
  }
  @keyframes dz-beat {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7); }
    30% { transform: scale(1.15); box-shadow: 0 0 0 20px rgba(0, 123, 255, 0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
  }
  .dz-bpm-label {
    font-family: system-ui, sans-serif;
    font-size: 14px; color: #64748b;
    font-weight: 600; letter-spacing: 0.5px;
  }
</style>
<div class="dz-canvas">
  <div class="dz-pulse"></div>
  <div class="dz-bpm-label">60 BPM</div>
</div>`
    },
    {
      name: "Тактовый размер 4/4 ↔ 3/4",
      code: `<style>
  .dz-canvas {
    position: relative; width: 100%; max-width: 500px; aspect-ratio: 2.2/1;
    margin: 20px auto;
    background: radial-gradient(circle at 50% 80%, #ffffff 0%, #e2e8f0 70%);
    border-radius: 1.5rem; border: 1px solid #cbd5e1;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
    display: flex; justify-content: center; align-items: center;
    flex-direction: column; gap: 20px;
    cursor: pointer; user-select: none;
    transition: transform 0.2s ease;
  }
  .dz-canvas:hover { transform: scale(1.01); }
  .dz-beats {
    display: flex; gap: 16px;
  }
  .dz-beat {
    width: 50px; height: 50px; border-radius: 50%;
    background: #cbd5e1;
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
  }
  .dz-beat.strong {
    background: #007bff;
    width: 60px; height: 60px;
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.4);
  }
  .dz-label {
    font-family: system-ui, sans-serif;
    font-size: 24px; font-weight: 700;
    color: #1e293b;
    transition: all 0.3s ease;
  }
  .dz-hint {
    position: absolute; bottom: 15px; left: 50%;
    transform: translateX(-50%);
    font-family: system-ui, sans-serif;
    font-size: 12px; color: #64748b;
    opacity: 0.7;
  }
  .dz-beat.hidden { display: none; }
</style>
<div class="dz-canvas" onclick="this.classList.toggle('is-3-4');">
  <div class="dz-label">4/4</div>
  <div class="dz-beats">
    <div class="dz-beat strong"></div>
    <div class="dz-beat"></div>
    <div class="dz-beat"></div>
    <div class="dz-beat dz-4th"></div>
  </div>
  <div class="dz-hint">Кликни, чтобы переключить на 3/4</div>
</div>
<style>
  .dz-canvas.is-3-4 .dz-label { color: #007bff; }
  .dz-canvas.is-3-4 .dz-4th { display: none; }
  .dz-canvas.is-3-4 .dz-hint { opacity: 0; }
</style>`
    },
    {
      name: "Удар по тарелке",
      code: `<style>
  .dz-canvas {
    position: relative; width: 100%; max-width: 400px; aspect-ratio: 1.5/1;
    margin: 20px auto;
    background: radial-gradient(circle at 50% 80%, #ffffff 0%, #e2e8f0 70%);
    border-radius: 1.5rem; border: 1px solid #cbd5e1;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
    display: flex; justify-content: center; align-items: center;
    cursor: pointer; user-select: none;
    overflow: hidden;
  }
  .dz-cymbal {
    width: 160px; height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #fbbf24 0%, #d97706 50%, #92400e 100%);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2), inset 0 2px 4px rgba(255, 255, 255, 0.4);
    position: relative;
    transition: transform 0.1s ease;
  }
  .dz-cymbal::before {
    content: ''; position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    width: 30px; height: 14px;
    border-radius: 50%;
    background: radial-gradient(circle, #fde68a 0%, #d97706 100%);
    box-shadow: inset 0 -2px 3px rgba(0, 0, 0, 0.3);
  }
  .dz-wave {
    position: absolute;
    width: 40px; height: 40px;
    border: 3px solid #007bff;
    border-radius: 50%;
    opacity: 0;
    pointer-events: none;
  }
  .dz-canvas.is-hit .dz-cymbal {
    animation: dz-shake 0.5s ease;
  }
  .dz-canvas.is-hit .dz-wave {
    animation: dz-ripple 0.8s ease-out;
  }
  .dz-canvas.is-hit .dz-wave.w2 { animation-delay: 0.15s; }
  .dz-canvas.is-hit .dz-wave.w3 { animation-delay: 0.3s; }
  
  @keyframes dz-shake {
    0%, 100% { transform: rotate(0deg) scale(1); }
    20% { transform: rotate(-3deg) scale(1.05); }
    40% { transform: rotate(3deg) scale(0.98); }
    60% { transform: rotate(-2deg) scale(1.02); }
    80% { transform: rotate(1deg) scale(0.99); }
  }
  @keyframes dz-ripple {
    0% { transform: scale(0.5); opacity: 0.9; }
    100% { transform: scale(6); opacity: 0; }
  }
  .dz-hint {
    position: absolute; bottom: 15px; left: 50%;
    transform: translateX(-50%);
    font-family: system-ui, sans-serif;
    font-size: 13px; color: #64748b;
    opacity: 0.8;
  }
</style>
<div class="dz-canvas" onclick="this.classList.remove('is-hit'); void this.offsetWidth; this.classList.add('is-hit');">
  <div class="dz-wave w1"></div>
  <div class="dz-wave w2"></div>
  <div class="dz-wave w3"></div>
  <div class="dz-cymbal"></div>
  <div class="dz-hint">Кликни по тарелке</div>
</div>`
    }
  ];

  class CssArtBlock extends window.Block {
    render() {
      this.element = document.createElement('div');
      this.element.className = 'le-css-art-block';
      
      // Создаем изолированный Shadow DOM
      this.shadow = this.element.attachShadow({ mode: 'open' });
      
      // ДОБАВЛЕНА ПРОВЕРКА: this.data может быть не определен в некоторых сценариях
      if (this.data && this.data.code) {
        this.shadow.innerHTML = this.data.code;
      } else {
        this.shadow.innerHTML = '<p style="color: #ef4444; padding: 20px; text-align: center; font-family: sans-serif;">Код иллюстрации не указан</p>';
      }
      
      // Безопасное добавление в контейнер
      if (this.engine && this.engine.els && this.engine.els.blocksContainer) {
        this.engine.els.blocksContainer.appendChild(this.element);
      }
    }
    
    destroy() {
      if (this.element) this.element.remove();
    }
  }

  window.BlockRegistry['css-art'] = CssArtBlock;
  console.log("✅ css-art.js выполнен: блок 'css-art' зарегистрирован, пресеты загружены:", window.CssArtPresets.length);
}

// Запускаем процесс инициализации
initCssArtBlock();