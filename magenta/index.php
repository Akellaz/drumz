<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Тренировка слуха — Drumz</title>
    
    <link rel="stylesheet" href="/assets/style.css">
    
    <script src="https://cdn.jsdelivr.net/npm/@magenta/music@1.23.1/dist/magentamusic.min.js"></script>
    
    <script src="../GrooveScribe/MIDI.js/js/MIDI/AudioDetect.js"></script>
    <script src="../GrooveScribe/MIDI.js/js/MIDI/LoadPlugin.js"></script>
    <script src="../GrooveScribe/MIDI.js/js/MIDI/Plugin.js"></script>
    <script src="../GrooveScribe/MIDI.js/js/MIDI/Player.js"></script>
    <script src="../GrooveScribe/MIDI.js/inc/DOMLoader.XMLHttp.js"></script>
    <script src="../GrooveScribe/MIDI.js/inc/Base64.js"></script>
    <script src="../GrooveScribe/MIDI.js/inc/base64binary.js"></script>
    <script src="../GrooveScribe/js/abc2svg-1.js"></script>
    <script src="../GrooveScribe/js/groove_utils.js"></script>

    <style>
        :root {
            --primary: #007bff;
            --primary-bg: #e7f1ff;
            --text: #1e293b;
            --text-light: #64748b;
            --border: #cbd5e1;
            --border-light: #e2e8f0;
            --bg-panel: #ffffff;
            --danger: #ef4444;
            --success: #22c55e;
            --ai: #6366f1;
            --ai-bg: #eef2ff;
            --gap: 8px;
            --radius: 6px;
            --font-size: 14px;
            --font-size-small: 12px;
        }

        body { margin: 0; font-family: system-ui, -apple-system, sans-serif; background: #f8fafc; color: var(--text); }
        .container { max-width: 900px; margin: 0 auto; padding: 16px; }

        .et-header { margin-bottom: 16px; border-bottom: 1px solid var(--border); padding-bottom: 12px; }
        .et-title { font-size: 20px; font-weight: 700; margin: 0 0 4px; }
        .et-desc { margin: 0; font-size: var(--font-size-small); color: var(--text-light); }

        .et-controls { display: flex; flex-wrap: wrap; gap: var(--gap); align-items: center; margin-bottom: 12px; }

        .btn {
            padding: 10px 16px; font-size: var(--font-size); border-radius: var(--radius);
            border: 1px solid var(--border); background: var(--bg-panel);
            color: var(--text); cursor: pointer; transition: all 0.15s; font-weight: 600;
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        }
        .btn:active { transform: scale(0.96); }
        .btn-primary { background: var(--primary); color: #fff; border-color: var(--primary); }
        .btn-primary:hover:not(:disabled) { background: #0056b3; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-ghost { background: transparent; border: none; color: var(--primary); padding: 4px 8px; font-size: 13px; }
        .btn-ghost:hover { text-decoration: underline; }
        .btn-settings {
            background: var(--ai-bg); border-color: var(--ai); color: var(--ai);
            font-size: 13px; padding: 8px 12px;
        }
        .btn-settings:hover { background: #dde1ff; }
        .btn-settings.open { background: var(--ai); color: #fff; }

        .setting-group { display: flex; align-items: center; gap: 8px; }
        .setting-label { font-size: 13px; font-weight: 600; color: var(--ai); white-space: nowrap; }
        .setting-slider { width: 120px; accent-color: var(--ai); }
        .setting-value { font-weight: 700; color: var(--ai); min-width: 32px; text-align: center; font-size: 13px; }

        .settings-panel {
            display: none; flex-wrap: wrap; gap: 12px; align-items: center;
            padding: 12px 14px; background: var(--ai-bg); border: 1px solid var(--ai);
            border-radius: var(--radius); margin-bottom: 12px;
        }
        .settings-panel.open { display: flex; }

        .measure-toggle {
            display: flex; background: var(--border-light); border-radius: var(--radius);
            padding: 3px; gap: 3px;
        }
        .measure-option {
            padding: 6px 12px; font-size: 12px; font-weight: 600; border-radius: 4px;
            cursor: pointer; transition: all 0.2s; color: var(--text-light); border: none; background: transparent;
        }
        .measure-option.active { background: #fff; color: var(--ai); box-shadow: 0 1px 3px rgba(0,0,0,0.1); }

        .checkbox-wrapper { display: flex; align-items: center; gap: 6px; }
        .checkbox-wrapper input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--ai); cursor: pointer; }
        .checkbox-wrapper label { font-size: 13px; font-weight: 600; color: var(--ai); cursor: pointer; user-select: none; }

        .et-status { font-size: var(--font-size-small); color: var(--text-light); margin-bottom: 16px; min-height: 20px; font-weight: 500; }
        .et-status.error { color: var(--danger); }
        .et-status.loading { color: var(--ai); }
        .et-status.countdown { color: var(--ai); font-size: 24px; font-weight: 700; text-align: center; }

        .sequencer-section {
            background: var(--bg-panel); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 16px; position: relative;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .success-badge {
            position: absolute; top: -12px; right: 16px;
            background: var(--success); color: #fff; padding: 6px 16px; border-radius: 20px;
            font-weight: 700; font-size: var(--font-size-small);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
            display: none; align-items: center; gap: 6px; z-index: 10;
        }
        .success-badge.show { display: flex; animation: popIn 0.3s ease-out; }
        @keyframes popIn { 0% { transform: scale(0.8); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }

        .track-row { display: flex; align-items: center; margin-bottom: 8px; }
        .track-name {
            width: 36px; font-weight: 700; font-size: 11px; text-align: right;
            padding-right: 8px; color: var(--text-light); flex-shrink: 0; user-select: none;
        }
        
        .track-steps-wrapper { overflow-x: auto; padding-bottom: 4px; }
        .track-steps { display: flex; gap: 4px; min-width: max-content; }

        .beat-group {
            display: flex; gap: 2px; padding: 4px; border-radius: 6px;
            background: #f1f5f9; border: 1px solid transparent; transition: all 0.15s;
        }
        .beat-group.playing { background: var(--primary-bg); border-color: var(--primary); }
        .beat-group.correct { background: rgba(34, 197, 94, 0.15); border-color: var(--success); }
        .beat-group.correct .step { background: rgba(34, 197, 94, 0.1); border-color: var(--success); }
        .beat-group.correct .step.active { background: var(--success); border-color: #15803d; color: #fff; }

        .step {
            width: 28px; height: 28px; border-radius: 4px;
            border: 1px solid var(--border); background: #fff;
            cursor: pointer; transition: all 0.1s;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; color: var(--primary); flex-shrink: 0; user-select: none;
        }
        .step:active { transform: scale(0.9); }
        .step.active { background: var(--primary); border-color: #0056b3; color: #fff; }
        .step.playing { background: #f97316; border-color: #c2410c; color: #fff; transform: scale(1.1); }
        .step.correct-reveal { background: var(--success); border-color: #15803d; color: #fff; }

        .sequencer-section.two-measures .step { width: 18px; height: 18px; font-size: 12px; }
        .sequencer-section.two-measures .beat-group { padding: 3px; gap: 2px; }
        .sequencer-section.two-measures .track-steps { gap: 3px; }
        .sequencer-section.two-measures .beat-group[data-beat="4"] {
            border-left: 3px solid var(--ai); margin-left: 8px;
        }

        .auxiliary-panel {
            margin-top: 24px; padding-top: 20px; border-top: 1px dashed var(--border);
            display: flex; flex-direction: column; gap: 20px;
        }
        .aux-header { display: flex; justify-content: space-between; align-items: center; }
        .aux-title { font-size: 13px; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.5px; }

        .notation-container {
            display: none; background: #fff; border: 1px solid var(--border-light);
            border-radius: var(--radius); padding: 12px; overflow-x: auto;
        }
        .notation-container.visible { display: block; }
        #notationPaper svg { max-width: 100%; height: auto; display: block; margin: 0 auto; }

        .drum-kit-stage {
            position: relative; width: 100%; max-width: 800px; margin: 0 auto;
            aspect-ratio: 1.1 / 1; background: rgba(241, 245, 249, 0.5);
            border-radius: 2rem; border: 1px solid #e2e8f0; overflow: hidden;
        }
        @media (min-width: 768px) { .drum-kit-stage { aspect-ratio: 2.4 / 1; } }

        .drum-part { position: absolute; cursor: pointer; -webkit-tap-highlight-color: transparent; }
        .drum-head-wrapper { position: relative; width: 100%; padding-bottom: 100%; }
        .drum-head, .cymbal-head {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            border-radius: 50%; transition: transform 0.1s;
        }
        .drum-head {
            background: #ffffff;
            box-shadow: inset 0 0 30px rgba(0,0,0,0.05), 0 10px 20px rgba(0,0,0,0.1);
        }
        .cymbal-head {
            background: linear-gradient(to bottom right, #fef08a, #eab308, #a16207);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
            border-bottom: 4px solid rgba(133, 77, 14, 0.4);
        }

        [data-part="bass"] { width: 30%; bottom: 10%; left: 50%; transform: translateX(-50%); z-index: 10; }
        @media (min-width: 768px) { [data-part="bass"] { width: 26%; } }
        [data-part="bass"] .drum-head { border: 12px solid #1e293b; background: #ffffff; }

        [data-part="snare-drum"] { width: 18%; left: 22%; bottom: 24%; z-index: 30; }
        @media (min-width: 768px) { [data-part="snare-drum"] { width: 15%; } }
        [data-part="snare-drum"] .drum-head { border: 8px solid #94a3b8; background: #f8fafc; }

        [data-part="floor-tom"] { width: 24%; right: 5%; bottom: 8%; z-index: 20; }
        [data-part="floor-tom"] .drum-head { border: 10px solid #1e293b; background: #ffffff; }

        [data-part="tom1"] { width: 16%; left: 30%; top: 10%; z-index: 20; }
        [data-part="tom1"] .drum-head { border: 8px solid #1e293b; background: #ffffff; }

        [data-part="tom2"] { width: 16%; right: 30%; top: 10%; z-index: 20; }
        [data-part="tom2"] .drum-head { border: 8px solid #1e293b; background: #ffffff; }

        [data-part="hihat"] { width: 20%; left: 2%; top: 42%; z-index: 40; }
        [data-part="crash"] { width: 26%; left: 2%; top: 2%; z-index: 45; }
        [data-part="ride"] { width: 28%; right: 2%; top: 5%; z-index: 25; }

        .drum-hit .drum-head { animation: drum-vibrate 0.1s ease-out; }
        .cymbal-hit .cymbal-head { animation: cymbal-sway 1.2s cubic-bezier(0.36, 0, 0.66, -0.56) forwards; }

        @keyframes drum-vibrate {
            0% { transform: scale(1); } 50% { transform: scale(0.92) translateY(5px); filter: brightness(1.5); } 100% { transform: scale(1); }
        }
        @keyframes cymbal-sway {
            0% { transform: rotate(0) scale(1); } 15% { transform: rotate(6deg) scale(1.05); filter: brightness(1.2); } 30% { transform: rotate(-5deg); } 100% { transform: rotate(0); }
        }

        #gs-hidden-player { display: none !important; }

        @media (max-width: 600px) {
            .container { padding: 12px; }
            .et-controls { gap: 8px; }
            .btn { padding: 8px 12px; font-size: 13px; flex: 1; }
            .settings-panel.open { width: 100%; flex-direction: column; align-items: stretch; }
            .setting-group { width: 100%; justify-content: space-between; }
            .setting-slider { flex: 1; max-width: none; }
            .measure-toggle { width: 100%; }
            .measure-option { flex: 1; text-align: center; }
            .step { width: 26px; height: 26px; font-size: 14px; }
            .sequencer-section.two-measures .step { width: 16px; height: 16px; font-size: 10px; }
            .track-name { width: 28px; font-size: 10px; }
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../includes/header.php'; ?>

    <main class="container">
        <header class="et-header">
            <h1 class="et-title">Тренировка слуха</h1>
            <p class="et-desc">Прослушайте ритм, сгенерированный ИИ, и повторите его на сетке.</p>
        </header>

        <div class="et-controls">
            <button class="btn btn-primary" id="btnNew" disabled>🎲 Новый ритм</button>
            <button class="btn" id="btnRepeat" disabled>▶ Повторить</button>
            <button class="btn" id="btnReveal" disabled>💡 Ответ</button>
            
            <div class="setting-group" style="margin-left: auto;">
                <span class="setting-label">BPM:</span>
                <input type="range" class="setting-slider" id="bpmSlider" min="40" max="180" value="90">
                <span class="setting-value" id="bpmValue">90</span>
            </div>

            <button class="btn btn-settings" id="btnSettings">⚙️ Настройки</button>
        </div>

        <div class="settings-panel" id="settingsPanel">
            <div class="setting-group">
                <span class="setting-label">🎛️ Креативность:</span>
                <input type="range" class="setting-slider" id="tempSlider" min="0.5" max="2.0" step="0.1" value="1.0">
                <span class="setting-value" id="tempValue">1.0</span>
            </div>
            <div class="setting-group">
                <span class="setting-label">Длина:</span>
                <div class="measure-toggle">
                    <button class="measure-option active" data-measures="1" id="measure1">1 такт</button>
                    <button class="measure-option" data-measures="2" id="measure2">2 такта</button>
                </div>
            </div>
            <div class="checkbox-wrapper">
                <input type="checkbox" id="countdownCheck" checked>
                <label for="countdownCheck">Затакт</label>
            </div>
            <div class="checkbox-wrapper">
                <input type="checkbox" id="bassCheck">
                <label for="bassCheck">Бас</label>
            </div>
            <div class="checkbox-wrapper">
                <input type="checkbox" id="keysCheck">
                <label for="keysCheck">Аккомпанемент</label>
            </div>
        </div>

        <div class="et-status" id="etStatus">⏳ Загрузка ИИ и звуков...</div>

        <div class="sequencer-section" id="sequencerSection">
            <div class="success-badge" id="successBadge">
                <span>✓</span> <span id="successText">Ритм угадан!</span>
            </div>
            <div id="drumGrid"></div>

            <div class="auxiliary-panel">
                <div class="aux-header">
                    <span class="aux-title">Визуализация</span>
                    <button class="btn btn-ghost" id="btnToggleNotation">🎼 Показать ноты</button>
                </div>

                <div class="notation-container" id="notationContainer">
                    <div id="notationPaper"></div>
                </div>

                <div class="drum-kit-stage" id="drumKitStage">
                    <div class="drum-part" data-part="crash"><div class="drum-head-wrapper"><div class="cymbal-head"></div></div></div>
                    <div class="drum-part" data-part="tom1"><div class="drum-head-wrapper"><div class="drum-head"></div></div></div>
                    <div class="drum-part" data-part="tom2"><div class="drum-head-wrapper"><div class="drum-head"></div></div></div>
                    <div class="drum-part" data-part="hihat"><div class="drum-head-wrapper"><div class="cymbal-head"></div></div></div>
                    <div class="drum-part" data-part="snare-drum"><div class="drum-head-wrapper"><div class="drum-head"></div></div></div>
                    <div class="drum-part" data-part="ride"><div class="drum-head-wrapper"><div class="cymbal-head"></div></div></div>
                    <div class="drum-part" data-part="bass"><div class="drum-head-wrapper"><div class="drum-head"></div></div></div>
                    <div class="drum-part" data-part="floor-tom"><div class="drum-head-wrapper"><div class="drum-head"></div></div></div>
                </div>
            </div>
        </div>
    </main>
    
    <div id="gs-hidden-player"></div>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>

    <script>
    class EarTrainer {
        constructor() {
            this.measures = 1;
            this.STEPS_PER_BEAT = 4;
            this.bpm = 90;
            this.temperature = 1.0;
            this.countdown = true;
            this.includeBass = false;
            this.includeKeys = false;
            this.isRevealed = false;
            this.isModelReady = false;
            this.hasPattern = false;
            this.showNotation = false;
            this.scheduledTimeouts = [];
            
            // Модели
            this.drumModel = null;
            this.trioModel = null;
            this.trioModelLoading = false;
            
            // Плеер Magenta
            this.magentaPlayer = null;
            this.SOUNDFONT_URL = 'https://storage.googleapis.com/magentadata/js/soundfonts/sgm_plus';
            
            // Данные
            this.gu = null;
            this.grooveData = null;
            this.target = { hh: '', snare: '', kick: '' };
            this.targetBass = [];
            this.targetKeys = [];
            this.user = { hh: [], snare: [], kick: [] };

            this.initUI();
            this.updateSteps();
            this.renderGrid();
            this.initGrooveUtils();
            this.loadDrumModel();
            this.initMagentaPlayer();
        }

        updateSteps() {
            this.STEPS = this.measures * 16;
            this.BEATS = this.measures * 4;
            this.user = { 
                hh: Array(this.STEPS).fill('-'), 
                snare: Array(this.STEPS).fill('-'), 
                kick: Array(this.STEPS).fill('-') 
            };
        }

        initUI() {
            this.els = {
                grid: document.getElementById('drumGrid'),
                notationContainer: document.getElementById('notationContainer'),
                paper: document.getElementById('notationPaper'),
                status: document.getElementById('etStatus'),
                badge: document.getElementById('successBadge'),
                successText: document.getElementById('successText'),
                btnNew: document.getElementById('btnNew'),
                btnRepeat: document.getElementById('btnRepeat'),
                btnReveal: document.getElementById('btnReveal'),
                btnToggleNotation: document.getElementById('btnToggleNotation'),
                btnSettings: document.getElementById('btnSettings'),
                settingsPanel: document.getElementById('settingsPanel'),
                bpmSlider: document.getElementById('bpmSlider'),
                bpmValue: document.getElementById('bpmValue'),
                tempSlider: document.getElementById('tempSlider'),
                tempValue: document.getElementById('tempValue'),
                measure1: document.getElementById('measure1'),
                measure2: document.getElementById('measure2'),
                countdownCheck: document.getElementById('countdownCheck'),
                bassCheck: document.getElementById('bassCheck'),
                keysCheck: document.getElementById('keysCheck'),
                drumKit: document.getElementById('drumKitStage'),
                sequencerSection: document.getElementById('sequencerSection')
            };

            this.els.btnNew.onclick = () => this.generatePattern();
            this.els.btnRepeat.onclick = () => this.playPattern();
            this.els.btnReveal.onclick = () => this.revealAnswer();
            this.els.btnToggleNotation.onclick = () => this.toggleNotation();
            
            this.els.btnSettings.onclick = () => {
                this.els.settingsPanel.classList.toggle('open');
                this.els.btnSettings.classList.toggle('open');
            };
            
            this.els.bpmSlider.oninput = (e) => {
                this.bpm = parseInt(e.target.value);
                this.els.bpmValue.textContent = this.bpm;
                if (this.grooveData) this.grooveData.tempo = this.bpm;
                if (this.showNotation) this.updateNotation();
            };

            this.els.tempSlider.oninput = (e) => {
                this.temperature = parseFloat(e.target.value);
                this.els.tempValue.textContent = this.temperature.toFixed(1);
            };

            this.els.measure1.onclick = () => this.setMeasures(1);
            this.els.measure2.onclick = () => this.setMeasures(2);

            this.els.countdownCheck.onchange = (e) => { this.countdown = e.target.checked; };
            
            // Галочки работают как фильтры воспроизведения
            const updateTrioNeed = () => {
                const needTrio = this.includeBass || this.includeKeys;
                if (needTrio && !this.trioModel && !this.trioModelLoading) {
                    this.loadTrioModel();
                }
            };
            this.els.bassCheck.onchange = (e) => {
                this.includeBass = e.target.checked;
                updateTrioNeed();
            };
            this.els.keysCheck.onchange = (e) => {
                this.includeKeys = e.target.checked;
                updateTrioNeed();
            };

            this.els.drumKit.querySelectorAll('.drum-part').forEach(el => {
                el.addEventListener('mousedown', () => this.hitDrum(el.getAttribute('data-part')));
                el.addEventListener('touchstart', (e) => {
                    e.preventDefault();
                    this.hitDrum(el.getAttribute('data-part'));
                }, { passive: false });
            });
        }

        setMeasures(m) {
            this.measures = m;
            this.els.measure1.classList.toggle('active', m === 1);
            this.els.measure2.classList.toggle('active', m === 2);
            this.els.sequencerSection.classList.toggle('two-measures', m === 2);
            this.updateSteps();
            this.renderGrid();
            if (this.hasPattern) this.checkBeats();
        }

        async loadDrumModel() {
            this.els.status.className = 'et-status loading';
            this.els.status.textContent = '⏳ Загрузка модели барабанов (~18 МБ)...';
            try {
                this.drumModel = new mm.MusicVAE('https://storage.googleapis.com/magentadata/js/checkpoints/music_vae/drums_2bar_lokl_small');
                await this.drumModel.initialize();
                this.isModelReady = true;
                this.checkReady();
            } catch (error) {
                console.error('Ошибка загрузки модели барабанов:', error);
                this.els.status.className = 'et-status error';
                this.els.status.textContent = '❌ Ошибка загрузки модели барабанов.';
            }
        }

        async loadTrioModel() {
            if (this.trioModel || this.trioModelLoading) return;
            this.trioModelLoading = true;
            this.els.status.className = 'et-status loading';
            this.els.status.textContent = '⏳ Загрузка модели баса/аккомпанемента (~17 МБ)...';
            try {
                this.trioModel = new mm.MusicVAE('https://storage.googleapis.com/magentadata/js/checkpoints/music_vae/trio_4bar');
                await this.trioModel.initialize();
                this.trioModelLoading = false;
                this.els.status.className = 'et-status';
                this.els.status.textContent = '✅ Модель баса/аккомпанемента готова';
                this.checkReady();
            } catch (error) {
                console.error('Ошибка загрузки модели trio:', error);
                this.trioModelLoading = false;
                this.els.status.className = 'et-status error';
                this.els.status.textContent = '❌ Ошибка загрузки модели trio.';
            }
        }

        initMagentaPlayer() {
            this.magentaPlayer = new mm.SoundFontPlayer(this.SOUNDFONT_URL);
        }

        checkReady() {
            if (this.isModelReady) {
                this.els.status.className = 'et-status';
                this.els.status.textContent = '✅ Готово. Нажмите «Новый ритм».';
                this.els.btnNew.disabled = false;
            }
        }

        initGrooveUtils() {
            this.gu = new GrooveUtils();
            this.grooveData = new this.gu.grooveDataNew();
            this.grooveData.timeDivision = 16;
            this.grooveData.tempo = this.bpm;
            this.grooveData.numberOfMeasures = this.measures;
            this.grooveData.numBeats = 4;
            this.grooveData.noteValue = 4;
            this.grooveData.notesPerMeasure = 16;

            this.gu.AddMidiPlayerToPage('gs-hidden-player', 16, false);
            this.gu.setTempo(this.bpm);
            this.gu.oneTimeInitializeMidi();
        }

        toggleNotation() {
            this.showNotation = !this.showNotation;
            if (this.showNotation) {
                this.els.notationContainer.classList.add('visible');
                this.els.btnToggleNotation.textContent = '🙈 Скрыть ноты';
                this.updateNotation();
            } else {
                this.els.notationContainer.classList.remove('visible');
                this.els.btnToggleNotation.textContent = '🎼 Показать ноты';
            }
        }

        async generatePattern() {
            if (!this.drumModel) return;

            if (this.magentaPlayer && this.magentaPlayer.isPlaying()) {
                this.magentaPlayer.stop();
            }

            this.isRevealed = false;
            this.hasPattern = true;
            this.hideSuccess();
            if (this.showNotation) this.toggleNotation(); 
            
            this.els.status.className = 'et-status loading';
            this.els.status.textContent = '⏳ ИИ генерирует ритм...';
            this.els.btnNew.disabled = true;

            try {
                // 1. Генерируем барабаны
                const drumSamples = await this.drumModel.sample(1, this.temperature);
                this.target = this.convertDrumsToGS(drumSamples[0]);
                
                // 2. Если trio загружена — генерируем бас и акк ВСЕГДА (независимо от галочек)
                this.targetBass = [];
                this.targetKeys = [];
                if (this.trioModel) {
                    this.els.status.textContent = '⏳ Генерируем дополнительные дорожки...';
                    const trioSamples = await this.trioModel.sample(1, this.temperature);
                    this.targetBass = this.extractBassFromTrio(trioSamples[0]);
                    this.targetKeys = this.extractKeysFromTrio(trioSamples[0]);
                }
                
                this.finalizeGeneration();
            } catch (error) {
                console.error('Ошибка генерации:', error);
                this.els.status.className = 'et-status error';
                this.els.status.textContent = '❌ Ошибка генерации. Попробуйте еще раз.';
                this.els.btnNew.disabled = false;
            }
        }

        convertDrumsToGS(noteSequence) {
            let hStr = '|', sStr = '|', kStr = '|';
            for (let step = 0; step < this.STEPS; step++) {
                let hasHH = false, hasSnare = false, hasKick = false;
                for (let note of noteSequence.notes) {
                    if (note.quantizedStartStep === step) {
                        if (note.pitch === 35 || note.pitch === 36) hasKick = true;
                        if (note.pitch === 38 || note.pitch === 40) hasSnare = true;
                        if (note.pitch === 42 || note.pitch === 44 || note.pitch === 46) hasHH = true;
                    }
                }
                kStr += hasKick ? 'o' : '-';
                sStr += hasSnare ? 'o' : '-';
                hStr += hasHH ? 'x' : '-';
            }
            return { hh: hStr + '|', snare: sStr + '|', kick: kStr + '|' };
        }

        extractBassFromTrio(noteSequence) {
            const bassNotes = [];
            const stepTimeSec = (60 / this.bpm) / 4;
            
            for (let step = 0; step < this.STEPS; step++) {
                for (let note of noteSequence.notes) {
                    if (note.quantizedStartStep === step && 
                        !note.isDrum && 
                        note.program >= 32 && 
                        note.program <= 39) {
                        const duration = note.quantizedEndStep 
                            ? (note.quantizedEndStep - note.quantizedStartStep) * stepTimeSec
                            : stepTimeSec * 2;
                        bassNotes.push({ step, pitch: note.pitch, duration });
                    }
                }
            }
            return bassNotes;
        }

        extractKeysFromTrio(noteSequence) {
            const keysNotes = [];
            const stepTimeSec = (60 / this.bpm) / 4;
            
            for (let step = 0; step < this.STEPS; step++) {
                for (let note of noteSequence.notes) {
                    if (note.quantizedStartStep === step && 
                        !note.isDrum && 
                        (note.program < 32 || note.program > 39)) {
                        const duration = note.quantizedEndStep 
                            ? (note.quantizedEndStep - note.quantizedStartStep) * stepTimeSec
                            : stepTimeSec * 2;
                        keysNotes.push({ step, pitch: note.pitch, duration, program: note.program });
                    }
                }
            }
            return keysNotes;
        }

        finalizeGeneration() {
            this.user = { 
                hh: Array(this.STEPS).fill('-'), 
                snare: Array(this.STEPS).fill('-'), 
                kick: Array(this.STEPS).fill('-') 
            };
            this.renderGrid();
            this.checkBeats();
            
            this.els.btnRepeat.disabled = false;
            this.els.btnReveal.disabled = false;
            this.els.btnNew.disabled = false;
            this.els.status.className = 'et-status';
            
            const extras = [];
            if (this.targetBass.length > 0) extras.push(`${this.targetBass.length} нот баса`);
            if (this.targetKeys.length > 0) extras.push(`${this.targetKeys.length} нот аккомп.`);
            const extrasInfo = extras.length > 0 ? ` (+ ${extras.join(', ')})` : '';
            
            this.els.status.textContent = `Эталон готов${extrasInfo}. Воспроизведение...`;
            
            setTimeout(() => this.playPattern(), 150);
        }

        updateNotation() {
            if (!this.hasPattern) return;
            this.grooveData.numberOfMeasures = this.measures;
            this.grooveData.hh_array = this.gu.noteArraysFromURLData('H', this.target.hh, 16, this.measures);
            this.grooveData.snare_array = this.gu.noteArraysFromURLData('S', this.target.snare, 16, this.measures);
            this.grooveData.kick_array = this.gu.noteArraysFromURLData('K', this.target.kick, 16, this.measures);
            const abc = this.gu.createABCFromGrooveData(this.grooveData, 600);
            this.els.paper.innerHTML = this.gu.renderABCtoSVG(abc).svg;
            this.gu.setGrooveData(this.grooveData);
        }

        renderGrid() {
            this.els.grid.innerHTML = '';
            const tracks = [{ id: 'hh', name: 'HH' }, { id: 'snare', name: 'SN' }, { id: 'kick', name: 'KT' }];
            tracks.forEach(track => {
                const row = document.createElement('div');
                row.className = 'track-row';
                const name = document.createElement('div');
                name.className = 'track-name';
                name.textContent = track.name;
                row.appendChild(name);
                
                const stepsWrapper = document.createElement('div');
                stepsWrapper.className = 'track-steps-wrapper';
                const steps = document.createElement('div');
                steps.className = 'track-steps';
                
                for (let b = 0; b < this.BEATS; b++) {
                    const group = document.createElement('div');
                    group.className = 'beat-group';
                    group.dataset.beat = b;
                    for (let i = 0; i < this.STEPS_PER_BEAT; i++) {
                        const idx = b * this.STEPS_PER_BEAT + i;
                        const step = document.createElement('div');
                        step.className = 'step';
                        step.dataset.track = track.id;
                        step.dataset.step = idx;
                        if (this.user[track.id][idx] !== '-') {
                            step.classList.add('active');
                            step.textContent = '●';
                        }
                        step.onclick = () => this.toggleStep(track.id, idx, step);
                        group.appendChild(step);
                    }
                    steps.appendChild(group);
                }
                stepsWrapper.appendChild(steps);
                row.appendChild(stepsWrapper);
                this.els.grid.appendChild(row);
            });
        }

        toggleStep(trackId, idx, el) {
            if (this.isRevealed) return;
            this.user[trackId][idx] = this.user[trackId][idx] === '-' ? 'o' : '-';
            el.classList.toggle('active');
            el.textContent = this.user[trackId][idx] === 'o' ? '●' : '';
            this.checkBeats();
        }

        hasNote(str, idx) { return str[idx + 1] !== '-'; }
        hasUserNote(trackId, idx) { return this.user[trackId][idx] !== '-'; }

        checkBeats() {
            let correctCount = 0;
            for (let b = 0; b < this.BEATS; b++) {
                const start = b * this.STEPS_PER_BEAT;
                let isBeatCorrect = true;
                let hasUserInput = false;
                
                for (let i = 0; i < this.STEPS_PER_BEAT; i++) {
                    const idx = start + i;
                    if (this.hasUserNote('hh', idx) || this.hasUserNote('snare', idx) || this.hasUserNote('kick', idx)) {
                        hasUserInput = true;
                    }
                    if (this.hasUserNote('hh', idx) !== this.hasNote(this.target.hh, idx)) isBeatCorrect = false;
                    if (this.hasUserNote('snare', idx) !== this.hasNote(this.target.snare, idx)) isBeatCorrect = false;
                    if (this.hasUserNote('kick', idx) !== this.hasNote(this.target.kick, idx)) isBeatCorrect = false;
                }
                
                const groups = document.querySelectorAll(`.beat-group[data-beat="${b}"]`);
                groups.forEach(group => group.classList.toggle('correct', hasUserInput && isBeatCorrect));
                if (hasUserInput && isBeatCorrect) correctCount++;
            }
            
            if (correctCount === this.BEATS && this.hasPattern) {
                this.showSuccess(false);
                if (!this.showNotation) this.toggleNotation();
            } else {
                this.els.status.textContent = `Угадано долей: ${correctCount} из ${this.BEATS}`;
            }
        }

        hitDrum(partName) {
            const el = this.els.drumKit.querySelector(`[data-part="${partName}"]`);
            if (!el) return;
            const isCymbal = ['crash', 'ride', 'hihat'].includes(partName);
            const animClass = isCymbal ? 'cymbal-hit' : 'drum-hit';
            el.classList.remove(animClass);
            void el.offsetWidth;
            el.classList.add(animClass);
            setTimeout(() => { el.classList.remove(animClass); }, isCymbal ? 1200 : 100);
        }

        async playPattern() {
            if (!this.hasPattern) return;

            this.scheduledTimeouts.forEach(clearTimeout);
            this.scheduledTimeouts = [];
            this.clearHighlights();
            if (this.magentaPlayer && this.magentaPlayer.isPlaying()) {
                this.magentaPlayer.stop();
            }

            const seq = this.buildPlaybackSequence();
            
            this.els.status.textContent = '⏳ Подготовка звуков...';
            try {
                await this.magentaPlayer.loadSamples(seq);
            } catch (err) {
                console.warn('Не удалось загрузить все сэмплы:', err);
            }

            this.magentaPlayer.start(seq);
            this.els.status.textContent = '▶ Воспроизведение...';

            this.startVisualTimer();

            const totalDuration = seq.totalTime * 1000 + 200;
            const endTid = setTimeout(() => {
                this.clearHighlights();
                this.els.status.className = 'et-status';
                this.els.status.textContent = 'Воспроизведение завершено. Соберите ритм на сетке!';
            }, totalDuration);
            this.scheduledTimeouts.push(endTid);
        }

        buildPlaybackSequence() {
            const beatTimeSec = 60 / this.bpm;
            const stepTimeSec = beatTimeSec / 4;
            const notes = [];
            let timeOffset = 0;

            if (this.countdown) {
                for (let i = 0; i < 4; i++) {
                    const t = i * beatTimeSec;
                    notes.push({
                        pitch: 42, velocity: 100,
                        startTime: t, endTime: t + 0.1,
                        program: 0, isDrum: true
                    });
                }
                timeOffset = 4 * beatTimeSec;
            }

            for (let i = 0; i < this.STEPS; i++) {
                const t = timeOffset + i * stepTimeSec;
                if (this.target.hh[i + 1] === 'x') {
                    notes.push({ pitch: 42, velocity: 100, startTime: t, endTime: t + 0.1, program: 0, isDrum: true });
                }
                if (this.target.snare[i + 1] === 'o') {
                    notes.push({ pitch: 38, velocity: 95, startTime: t, endTime: t + 0.1, program: 0, isDrum: true });
                }
                if (this.target.kick[i + 1] === 'o') {
                    notes.push({ pitch: 36, velocity: 110, startTime: t, endTime: t + 0.15, program: 0, isDrum: true });
                }
            }

            // Бас — только если галочка включена И данные есть
            if (this.includeBass && this.targetBass.length > 0) {
                for (let bNote of this.targetBass) {
                    const t = timeOffset + bNote.step * stepTimeSec;
                    const dur = (bNote.duration && bNote.duration > 0) ? bNote.duration : stepTimeSec * 2;
                    notes.push({
                        pitch: bNote.pitch, velocity: 100,
                        startTime: t, endTime: t + dur,
                        program: 33, isDrum: false
                    });
                }
            }

            // Аккомпанемент — только если галочка включена И данные есть
            if (this.includeKeys && this.targetKeys.length > 0) {
                for (let kNote of this.targetKeys) {
                    const t = timeOffset + kNote.step * stepTimeSec;
                    const dur = (kNote.duration && kNote.duration > 0) ? kNote.duration : stepTimeSec * 2;
                    let program = kNote.program;
                    if (program === undefined || program < 0 || program > 127) program = 25;
                    notes.push({
                        pitch: kNote.pitch, velocity: 90,
                        startTime: t, endTime: t + dur,
                        program: program, isDrum: false
                    });
                }
            }

            const totalTime = timeOffset + this.STEPS * stepTimeSec;
            return {
                notes,
                tempos: [{ qpm: this.bpm, time: 0 }],
                totalTime: totalTime
            };
        }

        startVisualTimer() {
            const beatTimeSec = 60 / this.bpm;
            const stepTimeSec = beatTimeSec / 4;
            let currentTime = 0;

            if (this.countdown) {
                for (let i = 1; i <= 4; i++) {
                    const delayMs = currentTime * 1000;
                    const tid = setTimeout(() => {
                        this.els.status.className = 'et-status countdown';
                        this.els.status.textContent = i;
                        this.hitDrum('hihat');
                    }, delayMs);
                    this.scheduledTimeouts.push(tid);
                    currentTime += beatTimeSec;
                }
                const resetTid = setTimeout(() => {
                    this.els.status.className = 'et-status';
                    this.els.status.textContent = '';
                }, currentTime * 1000);
                this.scheduledTimeouts.push(resetTid);
            }

            for (let i = 0; i < this.STEPS; i++) {
                const delayMs = currentTime * 1000;
                const tid = setTimeout(() => {
                    this.highlightStep(i);
                    if (this.target.hh[i + 1] === 'x') this.hitDrum('hihat');
                    if (this.target.snare[i + 1] === 'o') this.hitDrum('snare-drum');
                    if (this.target.kick[i + 1] === 'o') this.hitDrum('bass');
                }, delayMs);
                this.scheduledTimeouts.push(tid);
                currentTime += stepTimeSec;
            }
        }

        highlightStep(step) {
            this.clearHighlights();
            document.querySelectorAll(`.step[data-step="${step}"]`).forEach(el => el.classList.add('playing'));
            const beat = Math.floor(step / this.STEPS_PER_BEAT);
            document.querySelectorAll(`.beat-group[data-beat="${beat}"]`).forEach(el => el.classList.add('playing'));
        }

        clearHighlights() {
            document.querySelectorAll('.playing').forEach(el => el.classList.remove('playing'));
        }

        revealAnswer() {
            this.isRevealed = true;
            for (let i = 0; i < this.STEPS; i++) {
                this.user.hh[i] = this.target.hh[i + 1];
                this.user.snare[i] = this.target.snare[i + 1];
                this.user.kick[i] = this.target.kick[i + 1];
            }
            this.renderGrid();
            document.querySelectorAll('.step.active').forEach(el => el.classList.add('correct-reveal'));
            document.querySelectorAll('.beat-group').forEach(el => el.classList.add('correct'));
            this.els.status.className = 'et-status';
            this.els.status.textContent = 'Ответ открыт. Нажмите «Новый ритм» для следующего примера.';
            this.showSuccess(true);
            if (!this.showNotation) this.toggleNotation();
            this.els.btnRepeat.disabled = true;
        }

        showSuccess(isReveal = false) {
            this.successText.textContent = isReveal ? 'Вот правильный ответ' : 'Ритм угадан!';
            this.els.badge.classList.add('show');
        }
        hideSuccess() { this.els.badge.classList.remove('show'); }
    }

    document.addEventListener('DOMContentLoaded', () => {
        window.trainer = new EarTrainer();
    });
    </script>
</body>
</html>