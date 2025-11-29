<?php require_once __DIR__ . '/../includes/seo.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Хэндпан | Орден Перкуссии</title>
  <meta name="description" content="Играйте на виртуальном хэндпане и создавайте ритмы с помощью встроенного секвенсора.">
  <link rel="stylesheet" href="/assets/style.css?v=<?php echo time(); ?>">
  <style>
    /* Локальные стили для ханги и секвенсора */
    .hang-section {
      text-align: center;
      margin-bottom: 32px;
    }

    .hang-container {
      margin: 20px auto;
      width: 420px;
      height: 420px;
      position: relative;
    }

    #hang {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      background: #f0d9b5;
      border: 4px solid #a0522d;
      position: relative;
      cursor: pointer;
      transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
      transform-origin: center;
      touch-action: none;
    }

    .center-zone,
    .note-zone {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      cursor: pointer;
      color: #5c4033;
      font-weight: bold;
    }

    .center-zone {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background: #d2b48c;
      z-index: 10;
    }

    .note-zone {
      position: absolute;
      width: 84px;
      height: 84px;
      border-radius: 50%;
      background: #e0c7a3;
      transition: transform 0.6s;
    }

    .zone-number {
      font-size: 24px;
      line-height: 1.1;
    }

    .note-name {
      font-size: 12px;
      opacity: 0.6;
      margin-top: 2px;
    }

    /* Секвенсор */
    .sequencer-container {
      background: white;
      padding: 20px;
      border-radius: var(--radius);
      border: 1px solid var(--border);
      margin-top: 20px;
    }

    .sequencer-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
      flex-wrap: wrap;
      gap: 12px;
    }

    .controls {
      display: flex;
      gap: 10px;
    }

    .btn-sq {
      padding: 8px 16px;
      font-size: 14px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      min-width: 80px;
    }

    #playBtn {
      background: var(--success);
      color: white;
    }

    #stopBtn {
      background: var(--danger);
      color: white;
    }

    .bpm-control {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .bpm-control input {
      width: 60px;
      padding: 6px;
      border: 1px solid var(--border);
      border-radius: 4px;
    }

    .measures {
      display: flex;
      justify-content: center;
      gap: 16px;
      flex-wrap: wrap;
      margin-top: 12px;
    }

    .measure {
      display: flex;
      gap: 4px;
      padding: 4px;
      border-left: 2px solid var(--border);
      border-right: 2px solid var(--border);
    }

    .measure:first-child {
      border-left: none;
    }

    .measure:last-child {
      border-right: none;
    }

    .step {
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: 4px;
      position: relative;
    }

    .step.active {
      background: var(--primary-light);
      border-color: var(--primary);
    }

    .step select {
      width: 100%;
      height: 100%;
      border: none;
      background: transparent;
      text-align: center;
      cursor: pointer;
      font-size: 14px;
      padding: 0;
      appearance: none;
    }

    .info-box {
      margin-top: 20px;
      padding: 16px;
      background: var(--primary-light);
      border-radius: var(--radius);
      border-left: 4px solid var(--primary);
      color: var(--text);
      font-size: 0.95rem;
    }

    .visual-feedback {
      position: absolute;
      width: 100%;
      height: 100%;
      border-radius: 50%;
      pointer-events: none;
      opacity: 0;
      animation: pulse 0.5s ease-out;
    }

    @keyframes pulse {
      0% { transform: scale(1); opacity: 0.7; }
      100% { transform: scale(1.2); opacity: 0; }
    }

    #initAudioBtn {
      margin: 10px auto;
      padding: 10px 20px;
      font-size: 16px;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }

    #initAudioBtn:hover {
      background: #2b6cb0;
    }

    /* Адаптивность */
    @media (max-width: 768px) {
      .hang-container {
        width: 320px;
        height: 320px;
      }
      .note-zone {
        width: 64px;
        height: 64px;
      }
      .center-zone {
        width: 80px;
        height: 80px;
      }
      .zone-number {
        font-size: 20px;
      }
      .step { width: 30px; height: 30px; }
      .btn-sq { padding: 6px 12px; font-size: 13px; }
      .bpm-control input { width: 50px; }
    }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="container">
    <h1>Handpan D Celtic</h1>

    <div class="hang-section">
      <button id="initAudioBtn">🔊 Включить звук</button>
      <div class="hang-container">
        <div id="hang">
          <div class="center-zone" id="center">
            <div class="zone-number">0</div>
            <div class="note-name">D1</div>
          </div>
          <!-- Зоны добавятся скриптом -->
        </div>
      </div>
      <button id="rotateBtn" class="btn">→ Повернуть</button>
    </div>

    <div class="sequencer-container">
      <h2>Секвенсор</h2>
      <div class="sequencer-header">
        <div class="controls">
          <button id="playBtn" class="btn-sq">▶️ Play</button>
          <button id="stopBtn" class="btn-sq">⏹️ Stop</button>
        </div>
        <div class="bpm-control">
          <label for="bpm">BPM:</label>
          <input type="number" id="bpm" min="40" max="240" value="120">
        </div>
      </div>
      <div class="measures" id="measures"></div>

    </div>
  </main>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/tone/14.8.49/Tone.js"></script>
  <script>
    // === Конфигурация ===
    const zoneData = {
      0: { note: 'D1', sound: 'd1' },
      1: { note: 'A1', sound: 'a1' },
      2: { note: 'C1', sound: 'c1' },
      3: { note: 'D2', sound: 'd2' },
      4: { note: 'E',  sound: 'e'  },
      5: { note: 'F',  sound: 'f'  },
      6: { note: 'G',  sound: 'g'  },
      7: { note: 'A2', sound: 'a2' },
      8: { note: 'C2', sound: 'c2' }
    };

    const zoneLayout = [
      { number: 8, angle: 0   },   // север
      { number: 7, angle: 45  },   // северо-восток
      { number: 5, angle: 90  },   // восток
      { number: 3, angle: 135 },   // юго-восток
      { number: 1, angle: 180 },   // юг
      { number: 2, angle: 225 },   // юго-запад
      { number: 4, angle: 270 },   // запад
      { number: 6, angle: 315 }    // северо-запад
    ];

    const soundMap = {
      d1: "/hang/samples/d1.mp3",
      a1: "/hang/samples/a1.mp3",
      c1: "/hang/samples/c1.mp3",
      d2: "/hang/samples/d2.mp3",
      e:  "/hang/samples/e.mp3",
      f:  "/hang/samples/f.mp3",
      g:  "/hang/samples/g.mp3",
      a2: "/hang/samples/a2.mp3",
      c2: "/hang/samples/c2.mp3"
    };

    const STEPS_COUNT = 16;
    let sequence = Array(STEPS_COUNT).fill('–');
    let initialized = false;
    let players = null;
    let rotationAngle = 0;
    let isPlaying = false;

    // === DOM элементы ===
    const hangEl = document.getElementById('hang');
    const measuresContainer = document.getElementById('measures');

    // === Адаптивное создание зон ===
    function createZones() {
      // Удаляем старые зоны
      document.querySelectorAll('.note-zone').forEach(el => el.remove());

      const container = hangEl.parentElement;
      const size = container.clientWidth; // 420 или 320
      const radius = size * 0.333; // ~140 при 420, ~106 при 320
      const noteSize = size <= 320 ? 64 : 84;
      const centerSize = size <= 320 ? 80 : 100;

      // Обновляем центр
      const center = document.getElementById('center');
      center.style.width = `${centerSize}px`;
      center.style.height = `${centerSize}px`;

      // Внешние зоны
      zoneLayout.forEach(zone => {
        const angleRad = (zone.angle - 90) * Math.PI / 180;
        const x = radius * Math.cos(angleRad) + (size - noteSize) / 2;
        const y = radius * Math.sin(angleRad) + (size - noteSize) / 2;

        const el = document.createElement('div');
        el.className = 'note-zone';
        el.dataset.number = zone.number;
        el.dataset.sound = zoneData[zone.number].sound;
        el.innerHTML = `
          <div class="zone-number">${zone.number}</div>
          <div class="note-name">${zoneData[zone.number].note}</div>
        `;
        el.style.width = `${noteSize}px`;
        el.style.height = `${noteSize}px`;
        el.style.left = `${x}px`;
        el.style.top = `${y}px`;
        hangEl.appendChild(el);
      });
    }

    // === Создание секвенсора ===
    function createSequencer() {
      measuresContainer.innerHTML = '';
      const measuresCount = STEPS_COUNT / 4;
      for (let m = 0; m < measuresCount; m++) {
        const measureEl = document.createElement('div');
        measureEl.className = 'measure';
        for (let s = 0; s < 4; s++) {
          const stepIndex = m * 4 + s;
          const stepEl = document.createElement('div');
          stepEl.className = 'step';
          stepEl.dataset.index = stepIndex;

          const select = document.createElement('select');
          select.innerHTML = `<option value="–">–</option>` +
            Object.keys(zoneData).map(n => `<option value="${n}">${n}</option>`).join('');
          select.value = sequence[stepIndex];
          select.addEventListener('change', (e) => {
            sequence[stepIndex] = e.target.value;
          });
          stepEl.appendChild(select);
          measureEl.appendChild(stepEl);
        }
        measuresContainer.appendChild(measureEl);
      }
    }

    // === Воспроизведение ===
    function playStep(index) {
      const val = sequence[index];
      if (val !== '–') {
        const soundKey = zoneData[parseInt(val)].sound;
        if (players?.[soundKey]) {
          players[soundKey].start();
        }
      }
      document.querySelectorAll('.step').forEach((s, i) => {
        s.classList.toggle('active', i === index);
      });
    }

    function startSequencer() {
      if (!initialized) {
        alert("Сначала включите звук!");
        return;
      }
      if (isPlaying) return;

      const bpm = parseInt(document.getElementById('bpm').value) || 120;
      Tone.Transport.bpm.value = bpm;
      Tone.Transport.loop = true;
      Tone.Transport.loopEnd = `${STEPS_COUNT}n`;

      let stepIndex = 0;
      Tone.Transport.scheduleRepeat((time) => {
        playStep(stepIndex);
        stepIndex = (stepIndex + 1) % STEPS_COUNT;
      }, '16n');

      Tone.Transport.start();
      isPlaying = true;
      document.getElementById('playBtn').disabled = true;
    }

    function stopSequencer() {
      Tone.Transport.stop();
      Tone.Transport.cancel();
      isPlaying = false;
      document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
      document.getElementById('playBtn').disabled = false;
    }

    // === Аудио ===
    async function initAudio() {
      if (initialized) return;
      if (Tone.context.state !== 'running') await Tone.context.resume();

      try {
        players = {};
        const promises = Object.entries(soundMap).map(([key, url]) => {
          const player = new Tone.Player({ url }).toDestination();
          players[key] = player;
          return player.loaded;
        });
        await Promise.all(promises);
        initialized = true;
        document.getElementById('initAudioBtn').style.display = 'none';
      } catch (err) {
        alert("Ошибка загрузки звуков. Проверьте файлы в /hang/samples/");
        console.error(err);
      }
    }

    // === Ручное проигрывание ===
function setupClicks() {
  // Один обработчик на весь ханг
  hangEl.addEventListener('click', function(e) {
    if (!initialized) return;

    // Клик по центру
    if (e.target.closest('#center')) {
      players['d1'].start();
      showVisualFeedback(true);
      return;
    }

    // Клик по любой зоне
    const zone = e.target.closest('.note-zone');
    if (zone) {
      e.stopPropagation();
      const sound = zone.dataset.sound;
      players[sound].start();
      showVisualFeedback(false);
    }
  });
}

    // === Визуальная обратная связь ===
    function showVisualFeedback(isCenter) {
      const fb = document.createElement('div');
      fb.className = 'visual-feedback';
      fb.style.background = isCenter
        ? 'radial-gradient(circle, rgba(210,180,140,0.8) 0%, transparent 70%)'
        : 'radial-gradient(circle, rgba(224,199,163,0.8) 0%, transparent 70%)';
      hangEl.appendChild(fb);
      setTimeout(() => fb.remove(), 500);
    }

    // === Вращение ===
    function rotateHang() {
      rotationAngle = (rotationAngle + 45) % 360;
      hangEl.style.transform = `rotate(${rotationAngle}deg)`;
      document.querySelectorAll('.note-zone').forEach(note => {
        note.style.transform = `rotate(${-rotationAngle}deg)`;
      });
    }

    // === Мультитач ===
    const activeTouches = new Set();
    function handleTouchStart(e) {
      e.preventDefault();
      for (let touch of e.changedTouches) {
        const el = document.elementFromPoint(touch.clientX, touch.clientY);
        if (!el) continue;
        const sound = el.dataset?.sound;
        const isCenter = el.id === 'center';
        if ((sound || isCenter) && !activeTouches.has(touch.identifier)) {
          activeTouches.add(touch.identifier);
          if (initialized) {
            players[isCenter ? 'd1' : sound].start();
            showVisualFeedback(isCenter);
          }
        }
      }
    }
    function handleTouchEnd(e) {
      for (let touch of e.changedTouches) {
        activeTouches.delete(touch.identifier);
      }
    }

    // === Обработка изменения размера окна ===
    let resizeTimer;
    function handleResize() {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        createZones();
        if (rotationAngle !== 0) {
          hangEl.style.transform = `rotate(${rotationAngle}deg)`;
          document.querySelectorAll('.note-zone').forEach(note => {
            note.style.transform = `rotate(${-rotationAngle}deg)`;
          });
        }
      }, 150);
    }

    // === Инициализация ===
    document.addEventListener('DOMContentLoaded', () => {
      createZones();
      createSequencer();
      setupClicks();

      hangEl.addEventListener('touchstart', handleTouchStart, { passive: false });
      hangEl.addEventListener('touchend', handleTouchEnd);
      hangEl.addEventListener('touchcancel', handleTouchEnd);

      window.addEventListener('resize', handleResize);

      document.getElementById('rotateBtn').addEventListener('click', rotateHang);
      document.getElementById('initAudioBtn').addEventListener('click', initAudio);
      document.getElementById('playBtn').addEventListener('click', startSequencer);
      document.getElementById('stopBtn').addEventListener('click', stopSequencer);
    });
  </script>
</body>
</html>