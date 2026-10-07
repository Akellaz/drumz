/**
 * LessonEngine.js v5.0 - Компонентная архитектура
 */

window.BlockRegistry = window.BlockRegistry || {};

const BlockRegistry = {};

class Block {
  constructor(engine, data) {
    this.engine = engine;
    this.data = data;
    this.element = null;
  }
  
  render() {
    throw new Error('Метод render() должен быть реализован');
  }
  
  destroy() {
    if (this.element) this.element.remove();
  }
  
  onEvent(event, data) {}
}
window.Block = Block;

class LessonEngine {
  constructor(containerId, cardData) {
    this.container = document.getElementById(containerId);
    if (!this.container) throw new Error(`Контейнер #${containerId} не найден`);
    
    this.cardData = cardData;
    this.state = {
      bpm: 90,
      steps: 16,
      target: { hh: '', snare: '', kick: '' },
      user: { hh: [], snare: [], kick: [] },
      isReady: false,
      isLocked: false
    };
    
    this.blocks = [];
    this.magentaPlayer = null;
    this.scheduledTimeouts = [];
    
    this.injectStyles();
    this.init();
  }
  
  async init() {
    this.renderWrapper();
    
    // 🔥 УМНАЯ ПРОВЕРКА: нужны ли вообще тяжелые аудио/нотные библиотеки?
    const audioDependentTypes = ['grid', 'notation', 'drumkit-mini', 'drumkit-real'];
    const needsAudio = this.cardData.blocks.some(block => 
      audioDependentTypes.includes(block.type)
    );

    if (needsAudio) {
      console.log("🎵 Обнаружены аудио-блоки. Загружаем Magenta и ABCjs...");
      await this.loadDependencies();
    } else {
      console.log("✅ Карточка текстовая/визуальная. Аудио-движок не загружаем (экономим ресурсы).");
      // Даже не создаем magentaPlayer, чтобы браузер не ругался на AudioContext
      this.magentaPlayer = null; 
    }
    
    await this.loadBlocks();
    this.renderBlocks();
    this.state.isReady = true;
  }
  
  renderWrapper() {
    this.container.innerHTML = `
      <div class="le-wrapper">
        <div class="le-blocks-container" id="leBlocksContainer"></div>
      </div>
    `;
    this.els = {
      blocksContainer: this.container.querySelector('#leBlocksContainer')
    };
  }
  
  async loadDependencies() {
    const deps = [
      'https://cdn.jsdelivr.net/npm/@magenta/music@1.23.1/dist/magentamusic.min.js',
      '/assets/GrooveScribe/js/abc2svg-1.js?v=3',
      '/assets/GrooveScribe/js/groove_utils.js?v=3',
      'https://cdn.jsdelivr.net/npm/abcjs@6.5.2/dist/abcjs-basic-min.js'
    ];
    
    for (const src of deps) {
      const cleanSrc = src.split('?')[0];
      if (document.querySelector(`script[src^="${cleanSrc}"]`)) continue;
      
      await new Promise((resolve, reject) => {
        const s = document.createElement('script');
        s.src = src;
        s.async = false;
        s.onload = resolve;
        s.onerror = () => reject(new Error(`Не удалось загрузить: ${src}`));
        document.head.appendChild(s);
      });
    }
    
    this.magentaPlayer = new mm.SoundFontPlayer('https://storage.googleapis.com/magentadata/js/soundfonts/sgm_plus');
  }
  
  
  
  
  
  async loadBlocks() {
    console.log("🔍 Начинаем загрузку блоков. BlockRegistryMeta:", window.BlockRegistryMeta);
    
    const blockTypes = new Set(this.cardData.blocks.map(b => b.type));
    console.log("📦 Требуемые типы блоков:", Array.from(blockTypes));

    for (const type of blockTypes) {
      if (window.BlockRegistry[type]) {
        console.log(`⏭️ Блок "${type}" уже в реестре, пропускаем.`);
        continue;
      }

      const path = window.BlockRegistryMeta ? window.BlockRegistryMeta[type] : undefined;
      console.log(`🔗 Ищем путь для "${type}":`, path);

      if (!path) {
        console.error(`❌ КРИТИЧЕСКАЯ ОШИБКА: Блок "${type}" не найден в window.BlockRegistryMeta!`);
        continue;
      }

      console.log(`⬇️ Пытаемся загрузить скрипт: ${path}`);
      try {
        await new Promise((resolve, reject) => {
          const s = document.createElement('script');
          s.src = path;
          s.onload = () => {
            console.log(`✅ УСПЕХ: Скрипт загружен и выполнен: ${path}`);
            resolve();
          };
          s.onerror = (e) => {
            console.error(`❌ ПРОВАЛ: Ошибка сети при загрузке ${path}`, e);
            reject(new Error(`Не удалось загрузить блок: ${type}`));
          };
          document.head.appendChild(s);
        });
      } catch (err) {
        console.error("💥 Исключение при загрузке блока:", err);
      }
    }
    console.log("🏁 Загрузка блоков завершена. Итоговый BlockRegistry:", window.BlockRegistry);
  }
  
  
  
  
  
  
  renderBlocks() {
    this.els.blocksContainer.innerHTML = '';
    this.blocks = [];
    
    for (const blockData of this.cardData.blocks) {
      const BlockClass = window.BlockRegistry[blockData.type];
      if (!BlockClass) {
        console.warn(`Блок "${blockData.type}" не зарегистрирован`);
        continue;
      }
      
      const block = new BlockClass(this, blockData);
      block.render();
      this.blocks.push(block);
    }
  }
  
  notifyBlocks(event, data) {
    for (const block of this.blocks) {
      if (block.onEvent) block.onEvent(event, data);
    }
  }
  
  async playSequence(target) {
    if (!this.state.isReady) return;
    
    this.stopPlayback();
    
    const seq = this.buildSequenceFromGrid(target);
    
    if (this.magentaPlayer?.context?.state === 'suspended') {
      await this.magentaPlayer.context.resume();
    }
    
    this.magentaPlayer.start(seq);
    this.startVisualTimer(target);
  }
  
  startVisualTimer(target) {
    const stepTimeSec = (60 / this.state.bpm) / 4;
    let currentTime = 0;
    
    for (let i = 0; i < this.state.steps; i++) {
      this.scheduledTimeouts.push(setTimeout(() => {
        this.highlightStep(i);
        this.notifyBlocks('playStep', { step: i, target });
      }, currentTime * 1000));
      currentTime += stepTimeSec;
    }
    
    this.scheduledTimeouts.push(setTimeout(() => {
      this.clearHighlights();
    }, currentTime * 1000 + 100));
  }
  
  buildSequenceFromGrid(gd) {
    const stepTimeSec = (60 / this.state.bpm) / 4;
    const notes = [];
    const pitchMap = { kick: 36, snare: 38, hh: 42 };
    
    for (let i = 0; i < this.state.steps; i++) {
      const time = i * stepTimeSec;
      for (let track of ['kick', 'snare', 'hh']) {
        const ch = gd[track][i + 1];
        if (ch === 'o' || ch === 'x' || ch === 'X') {
          notes.push({
            pitch: pitchMap[track],
            startTime: time,
            endTime: time + 0.1,
            isDrum: true,
            program: 0
          });
        }
      }
    }
    
    return {
      notes,
      tempos: [{ qpm: this.state.bpm, time: 0 }],
      totalTime: this.state.steps * stepTimeSec
    };
  }
  
  highlightStep(stepIndex) {
    this.clearHighlights();
    
    this.container.querySelectorAll(`.le-step[data-step="${stepIndex}"]`).forEach(el => {
      el.classList.add('playing');
    });
    
    this.container.querySelectorAll(`.le-beat-group[data-beat="${Math.floor(stepIndex / 4)}"]`).forEach(el => {
      el.classList.add('playing');
    });
  }
  
  clearHighlights() {
    this.container.querySelectorAll('.playing').forEach(el => el.classList.remove('playing'));
  }
  
  stopPlayback() {
    if (this.magentaPlayer) this.magentaPlayer.stop();
    this.scheduledTimeouts.forEach(clearTimeout);
    this.scheduledTimeouts = [];
    this.clearHighlights();
  }
  
  
  
  
  
  
  
  
  
  
  
  injectStyles() {
    if (document.getElementById('lesson-engine-styles-v5')) return;
    
    const style = document.createElement('style');
    style.id = 'lesson-engine-styles-v5';
    style.textContent = `
      /* Контейнер урока - центрирование и ограничение ширины */
      #lesson-root {
        max-width: 800px;
        margin: 40px auto;
        padding: 0 20px;
      }
      
      .le-wrapper { 
        background: #fff; 
        border: 1px solid #e2e8f0; 
        border-radius: 12px; 
        padding: 32px; 
      }
      
      .le-blocks-container { 
        display: flex; 
        flex-direction: column; 
        gap: 24px; 
      }
      
      .le-text-heading { 
        font-size: 28px; 
        font-weight: 700; 
        color: #1e293b; 
        margin: 0; 
        line-height: 1.3;
      }
      
      .le-text-paragraph { 
        font-size: 16px; 
        color: #64748b; 
        line-height: 1.6; 
        margin: 0; 
      }
      
      .le-grid-composite { 
        display: flex; 
        flex-direction: column; 
        gap: 16px; 
      }
      
      .le-grid { 
        display: flex; 
        flex-direction: column; 
        gap: 10px; 
      }
      
      .le-track-row { 
        display: flex; 
        align-items: center; 
        gap: 12px; 
      }
      
      .le-track-name { 
        width: 36px; 
        font-size: 12px; 
        font-weight: 700; 
        text-align: right; 
        color: #94a3b8; 
      }
      
      .le-track-steps { 
        display: flex; 
        gap: 6px; 
      }
      
      .le-beat-group { 
        display: flex; 
        gap: 3px; 
        padding: 4px; 
        border-radius: 6px; 
        border: 1px solid transparent; 
        transition: all .2s ease; 
      }
      
      .le-beat-group.playing { 
        background: #eff6ff; 
        border-color: #bfdbfe; 
      }
      
      .le-beat-group.correct { 
        background: rgba(34,197,94,.08); 
        border-color: rgba(34,197,94,.3); 
      }
      
      .le-step { 
        width: 32px; 
        height: 32px; 
        border-radius: 6px; 
        border: 1px solid #e2e8f0; 
        background: #fff; 
        cursor: pointer; 
        transition: all .1s ease; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-size: 18px; 
        color: #007bff; 
      }
      
      .le-step:hover { 
        border-color: #93c5fd; 
        background: #f8fafc; 
      }
      
      .le-step.active { 
        background: #007bff; 
        border-color: #007bff; 
        color: #fff; 
      }
      
      .le-step.playing { 
        background: #f97316; 
        border-color: #ea580c; 
        color: #fff; 
        transform: scale(1.05); 
      }
      
      .le-step.correct { 
        background: #22c55e; 
        border-color: #16a34a; 
        color: #fff; 
      }
      
      .le-step.error { 
        background: #ef4444; 
        border-color: #dc2626; 
        color: #fff; 
      }
      
      .le-notation { 
        background: #fff; 
        border: 1px solid #e2e8f0; 
        border-radius: 8px; 
        padding: 20px; 
        overflow-x: auto; 
      }
      
      .le-notation svg { 
        max-width: 100%; 
        height: auto; 
        display: block; 
      }
      
      .le-btn { 
        display: inline-flex; 
        align-items: center; 
        justify-content: center; 
        padding: 10px 16px; 
        font-size: 14px; 
        font-weight: 600; 
        border-radius: 8px; 
        border: 1px solid #cbd5e1; 
        background: #fff; 
        color: #1e293b; 
        cursor: pointer; 
        transition: all .15s ease; 
      }
      
      .le-btn:hover:not(:disabled) { 
        border-color: #007bff; 
        color: #007bff; 
      }
      
      .le-btn:disabled { 
        opacity: .4; 
        cursor: not-allowed; 
      }
      
      .le-btn-play { 
        background: #007bff; 
        color: #fff; 
        border-color: #007bff; 
      }
      
      .le-btn-play:hover:not(:disabled) { 
        background: #0056b3; 
        color: #fff; 
      }
      
      /* Навигация урока */
      .lesson-player-wrapper {
        width: 100%;
      }
      
      .lesson-progress-bar {
        height: 4px;
        background: #e2e8f0;
        border-radius: 2px;
        margin-bottom: 8px;
        overflow: hidden;
      }
      
      .lesson-progress-fill {
        height: 100%;
        background: #007bff;
        transition: width 0.3s ease;
      }
      
      .lesson-progress-text {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 24px;
      }
      
      .lesson-navigation {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid #e2e8f0;
      }
      
      .lesson-nav-btn {
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #1e293b;
        cursor: pointer;
        transition: all 0.15s ease;
      }
      
      .lesson-nav-btn:hover:not(:disabled) {
        border-color: #007bff;
        color: #007bff;
      }
      
      .lesson-nav-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
      }
      
      .lesson-nav-primary {
        background: #007bff;
        color: #fff;
        border-color: #007bff;
      }
      
      .lesson-nav-primary:hover:not(:disabled) {
        background: #0056b3;
        color: #fff;
      }
      
      .lesson-complete {
        text-align: center;
        padding: 60px 20px;
      }
      
      .lesson-complete h2 {
        font-size: 32px;
        margin-bottom: 16px;
        color: #1e293b;
      }
      
      .lesson-complete p {
        color: #64748b;
        margin-bottom: 24px;
      }
	  
	  
	        .le-css-art-block {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 16px 0;
      }
	  
	  
	  
      
	  
	  
	  
	  
	  
	  
	  
	  
	  
      /* Стили для аудио-плеера и подсветки нот */
      .highlight {
        fill: #0a9ecc !important;
      }
      .abcjs-cursor {
        stroke: red;
        stroke-width: 2;
      }
      
      /* === ИДЕАЛЬНАЯ КНОПКА PLAY/STOP (на основе реального HTML) === */
      [id^="audio-controls-"] {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        margin-top: 16px !important;
        width: 100% !important;
      }
      
      [id^="audio-controls-"] .abcjs-inline-audio {
        background: transparent !important;
        padding: 0 !important;
        height: auto !important;
        border: none !important;
        margin: 0 auto !important;
      }
      
      /* Сама кнопка-круг */
      [id^="audio-controls-"] .abcjs-btn {
        width: 40px !important;
        height: 40px !important;
        border-radius: 50% !important;
        background: transparent !important;
        border: 1.5px solid var(--primary, #007bff) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 !important;
        margin: 0 !important;
        box-shadow: none !important;
        opacity: 0.5 !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
      }
      
      [id^="audio-controls-"] .abcjs-btn:hover {
        opacity: 1 !important;
        background: rgba(0, 123, 255, 0.06) !important;
      }
      
      [id^="audio-controls-"] .abcjs-btn:active {
        transform: scale(0.93) !important;
      }
      
      /* Жесткое центрирование и размер SVG внутри кнопки */
      [id^="audio-controls-"] .abcjs-btn svg {
        width: 16px !important;
        height: 16px !important;
        display: block !important;
      }

      /* Красим треугольник Play (polygon) и палочки Pause (rect) в синий */
      [id^="audio-controls-"] .abcjs-btn polygon,
      [id^="audio-controls-"] .abcjs-btn rect {
        fill: var(--primary, #007bff) !important;
        stroke: none !important;
      }
      
      /* Логика переключения: по умолчанию виден только Play */
      [id^="audio-controls-"] .abcjs-btn .abcjs-pause-svg { 
        display: none !important; 
      }
      [id^="audio-controls-"] .abcjs-btn .abcjs-loading-svg { 
        display: none !important; 
      }
      
      /* Логика переключения: при нажатии (.abcjs-pushed) видна только Pause */
      [id^="audio-controls-"] .abcjs-btn.abcjs-pushed .abcjs-play-svg { 
        display: none !important; 
      }
      [id^="audio-controls-"] .abcjs-btn.abcjs-pushed .abcjs-pause-svg { 
        display: block !important; 
      }
	  
    `;
    document.head.appendChild(style);
  }
  
  
  
  
  
  
  
  
  
  
  destroy() {
    for (const block of this.blocks) {
      if (block.destroy) block.destroy();
    }
    this.stopPlayback();
  }
  
  static parseDSL(text) {
    const lessonData = { title: '', cards: [] };
    const sections = {};
    const sectionRegex = /===\s*([^=]+?)\s*===/g;
    let match;
    const markers = [];
    
    while ((match = sectionRegex.exec(text)) !== null) {
      markers.push({ name: match[1].trim(), index: match.index, end: match.index + match[0].length });
    }
    
    for (let i = 0; i < markers.length; i++) {
      const start = markers[i].end;
      const end = i + 1 < markers.length ? markers[i + 1].index : text.length;
      sections[markers[i].name] = text.substring(start, end).trim();
    }
    
    if (sections.LESSON) {
      sections.LESSON.split('\n').forEach(line => {
        line = line.split('#')[0].trim();
        if (!line) return;
        const ci = line.indexOf(':');
        if (ci === -1) return;
        const k = line.substring(0, ci).trim();
        const v = line.substring(ci + 1).trim();
        if (k === 'title') lessonData.title = v;
      });
    }
    
    for (let i = 0; i < markers.length; i++) {
      const name = markers[i].name;
      if (name.startsWith('CARD ')) {
        lessonData.cards.push(this.parseCardSection(sections[name]));
      }
    }
    
    return lessonData;
  }
  
  
  
  
  static parseCardSection(text) {
    const card = { blocks: [] };
    let currentBlock = null;
    let isCodeBlock = false;

    text.split('\n').forEach(line => {
      const trimmed = line.trim();
      if (!trimmed || trimmed.startsWith('#')) return;

      if (trimmed === 'blocks:') return;

      if (trimmed.startsWith('- type:')) {
        if (currentBlock) card.blocks.push(currentBlock);
        currentBlock = { type: trimmed.substring(7).trim() };
        isCodeBlock = false;
      } else if (currentBlock) {
        // 🔥 ИСПРАВЛЕНИЕ: ловим code, abc и text и сразу расшифровываем \n в реальные переносы строк
        if (trimmed.startsWith('code:') || trimmed.startsWith('abc:') || trimmed.startsWith('text:')) {
          isCodeBlock = true;
          const ci = trimmed.indexOf(':');
          const key = trimmed.substring(0, ci).trim();
          let value = trimmed.substring(ci + 1).trim();
          
          // Заменяем экранированные \n на реальные символы переноса строки
          currentBlock[key] = value.replace(/\\n/g, '\n');
        } else if (isCodeBlock) {
          // На случай, если в файле всё же есть реальные переносы строк
          currentBlock.code += '\n' + trimmed;
        } else if (trimmed.includes(':')) {
          const ci = trimmed.indexOf(':');
          const k = trimmed.substring(0, ci).trim();
          const v = trimmed.substring(ci + 1).trim();
          currentBlock[k] = v;
        }
      }
    });

    if (currentBlock) card.blocks.push(currentBlock);
    return card;
  }
  
  
  
  
  
  
  
}

class LessonPlayer {
  constructor(containerId, lessonData) {
    this.container = document.getElementById(containerId);
    this.lessonData = lessonData;
    this.currentCardIndex = 0;
    this.currentEngine = null;
    this.init();
  }
  
  init() {
    this.renderUI();
    this.loadCard(0);
  }
  
  renderUI() {
    this.container.innerHTML = `
      <div class="lesson-player-wrapper">
        <div class="lesson-progress-bar"><div class="lesson-progress-fill" id="progressFill"></div></div>
        <div class="lesson-progress-text" id="progressText"></div>
        <div id="cardContainer"></div>
        <div class="lesson-navigation">
          <button class="lesson-nav-btn" id="prevBtn" disabled>← Назад</button>
          <button class="lesson-nav-btn lesson-nav-primary" id="nextBtn">Дальше →</button>
        </div>
      </div>
    `;
    
    this.els = {
      progressFill: this.container.querySelector('#progressFill'),
      progressText: this.container.querySelector('#progressText'),
      cardContainer: this.container.querySelector('#cardContainer'),
      prevBtn: this.container.querySelector('#prevBtn'),
      nextBtn: this.container.querySelector('#nextBtn')
    };
    
    this.els.prevBtn.onclick = () => this.prevCard();
    this.els.nextBtn.onclick = () => this.nextCard();
    this.updateProgress();
  }
  
  loadCard(index) {
    if (this.currentEngine) this.currentEngine.destroy();
    
    this.currentCardIndex = index;
    this.els.cardContainer.innerHTML = '';
    this.currentEngine = new LessonEngine('cardContainer', this.lessonData.cards[index]);
    this.updateProgress();
    this.updateNavigation();
  }
  
  prevCard() {
    if (this.currentCardIndex > 0) this.loadCard(this.currentCardIndex - 1);
  }
  
  nextCard() {
    if (this.currentCardIndex < this.lessonData.cards.length - 1) {
      this.loadCard(this.currentCardIndex + 1);
    } else {
      this.finishLesson();
    }
  }
  
  updateProgress() {
    const total = this.lessonData.cards.length;
    const current = this.currentCardIndex + 1;
    this.els.progressFill.style.width = `${(current / total) * 100}%`;
    this.els.progressText.textContent = `Карточка ${current} из ${total}`;
  }
  
  updateNavigation() {
    this.els.prevBtn.disabled = this.currentCardIndex === 0;
    this.els.nextBtn.textContent = this.currentCardIndex === this.lessonData.cards.length - 1 ? 'Завершить 🎉' : 'Дальше →';
  }
  
  finishLesson() {
    this.els.cardContainer.innerHTML = `
      <div class="lesson-complete" style="text-align: center; padding: 40px;">
        <h2 style="margin-bottom: 16px;">🎉 Урок пройден!</h2>
      </div>
    `;
    this.els.nextBtn.disabled = true;
  }
}

// 🔥 ИСПРАВЛЕНИЕ: Явно делаем классы доступными глобально
window.LessonEngine = LessonEngine;
window.LessonPlayer = LessonPlayer;

console.log("✅ LessonEngine.js загружен: LessonEngine и LessonPlayer доступны глобально.");