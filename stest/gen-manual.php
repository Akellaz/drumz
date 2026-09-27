<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/style.css?v=<?= filemtime(__DIR__ . '/../assets/style.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/abcjs@6.5.2/dist/abcjs-basic.min.js"></script>
    <style>
        .mode-switcher {
            display: flex;
            gap: 0;
            margin-bottom: 14px;
            padding-bottom: 2px;
            border-bottom: 1px solid var(--border, #e2e8f0);
            font-size: 13px;
        }

        .mode-link {
            padding: 6px 14px;
            color: var(--text-secondary, #718096);
            text-decoration: none;
            border-radius: 6px 6px 0 0;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
            user-select: none;
            margin-bottom: -2px;
        }

        .mode-link:hover {
            color: var(--primary, #3182ce);
            background: var(--primary-light, rgba(49, 130, 206, 0.08));
        }

        .mode-link.active {
            color: var(--primary, #3182ce);
            font-weight: 600;
            border-bottom-color: var(--primary, #3182ce);
            background: var(--primary-light, rgba(49, 130, 206, 0.06));
        }

        .top-controls {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin: 15px 0;
        }
        
        .note-selector {
            flex: 1;
            padding: 10px;
            background: var(--primary-light);
            border: 1px solid var(--primary);
            border-radius: var(--radius);
            min-width: 0;
        }
        
        .note-groups {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }
        
        .note-group {
            display: flex;
            align-items: center;
            cursor: pointer;
            user-select: none;
            transition: opacity 0.2s, transform 0.1s;
        }
        
        .note-group:hover:not(.disabled) {
            opacity: 0.7;
        }

        .note-group:active:not(.disabled) {
            transform: scale(0.94);
        }

        .note-group input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
            pointer-events: none;
        }

        .note-group img {
            height: 32px;
            width: auto;
            display: block;
            filter: brightness(0) saturate(100%);
            transition: filter 0.2s, opacity 0.2s;
            opacity: 1;
        }

        .note-group.disabled {
            opacity: 0.25;
            cursor: not-allowed;
            pointer-events: none;
        }

        .btn-primary { 
            padding: 10px 18px; 
            font-size: 14px; 
            background-color: #3182ce;
            color: #ffffff;
            border: 2px solid #2b6cb0;
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(49, 130, 206, 0.3);
            white-space: nowrap;
            flex-shrink: 0;
            align-self: center;
        }
        
        .btn-primary:hover:not(:disabled) {
            background-color: #2b6cb0;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-primary:disabled {
            opacity: 0.45;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            padding: 10px 18px;
            font-size: 14px;
            background-color: #ffffff;
            color: var(--text);
            border: 2px solid #e2e8f0;
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            white-space: nowrap;
            flex-shrink: 0;
            align-self: center;
        }

        .btn-secondary:hover:not(:disabled) {
            background-color: #f0f4f8;
            border-color: var(--primary);
            transform: translateY(-1px);
        }

        .buttons-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex-shrink: 0;
            align-self: center;
        }

        .notation-container {
            background: white;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid var(--border);
            margin-bottom: 15px;
        }
        
        #notationPaper {
            background: white;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid var(--border);
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        #notationPaper svg {
            max-width: 100%;
            height: auto;
            display: block;
        }
        
        #notationPaper .placeholder {
            color: var(--text-secondary, #718096);
            font-size: 13px;
        }
        
        details {
            margin-top: 10px;
        }
        
        summary {
            cursor: pointer;
            font-size: 12px;
            color: var(--text-secondary, #718096);
            user-select: none;
        }
        
        #abcText {
            width: 100%;
            min-height: 40px;
            margin-top: 6px;
            padding: 8px;
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 13px;
            border: 1px solid var(--border);
            border-radius: 4px;
            resize: vertical;
            background: var(--bg, #f7fafc);
            color: var(--text);
            font-weight: 600;
        }

        .sequencer-section {
            margin-top: 15px;
            padding: 12px;
            background: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            position: relative;
        }

        .success-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: #ffffff;
            padding: 8px 18px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 4px 14px rgba(72, 187, 120, 0.45);
            display: none;
            align-items: center;
            gap: 8px;
            z-index: 10;
            animation: popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            user-select: none;
        }
        
        .success-badge.show {
            display: flex;
        }
        
        .success-badge .badge-icon {
            font-size: 1.4em;
            line-height: 1;
        }
        
        @keyframes popIn {
            0% { transform: scale(0) rotate(-20deg); opacity: 0; }
            60% { transform: scale(1.15) rotate(5deg); opacity: 1; }
            100% { transform: scale(1) rotate(0); opacity: 1; }
        }

        .beat-numbers {
            display: flex;
            gap: 8px;
            margin-left: 53px;
            margin-bottom: 6px;
        }
        
        .beat-num {
            width: 110px;
            text-align: center;
            font-size: 16px;
            font-weight: 800;
            color: #2b6cb0;
            padding: 4px 0;
            border-radius: 6px 6px 0 0;
            letter-spacing: 0.5px;
            transition: all 0.15s;
        }
        
        .beat-num.playing {
            color: #ffffff;
            background: linear-gradient(180deg, #3182ce 0%, #2b6cb0 100%);
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(49, 130, 206, 0.4);
        }

        .track-row {
            display: flex;
            align-items: stretch;
            margin-bottom: 6px;
        }
        
        .track-name {
            width: 45px;
            font-weight: 700;
            font-size: 12px;
            text-align: right;
            padding: 6px 8px 6px 0;
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
        
        .track-steps {
            display: flex;
            gap: 8px;
        }
        
        .beat-group {
            display: flex;
            gap: 2px;
            padding: 4px;
            border-radius: 8px;
            border: 2px solid transparent;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .beat-group.beat-even {
            background: linear-gradient(180deg, rgba(49, 130, 206, 0.06) 0%, rgba(49, 130, 206, 0.10) 100%);
            border-color: rgba(49, 130, 206, 0.15);
        }
        
        .beat-group.beat-odd {
            background: linear-gradient(180deg, rgba(49, 130, 206, 0.13) 0%, rgba(49, 130, 206, 0.20) 100%);
            border-color: rgba(49, 130, 206, 0.25);
        }
        
        .beat-group.playing {
            background: linear-gradient(180deg, rgba(49, 130, 206, 0.30) 0%, rgba(49, 130, 206, 0.45) 100%) !important;
            border-color: #3182ce !important;
            box-shadow: 0 4px 14px rgba(49, 130, 206, 0.5);
            transform: translateY(-1px);
        }

        .beat-group.correct {
            background: linear-gradient(180deg, rgba(72, 187, 120, 0.18) 0%, rgba(72, 187, 120, 0.30) 100%) !important;
            border-color: #48bb78 !important;
            box-shadow: 0 3px 10px rgba(72, 187, 120, 0.35);
        }

        .beat-group.correct .step.active {
            background: linear-gradient(180deg, #68d391 0%, #48bb78 100%) !important;
            border-color: #2f855a !important;
            box-shadow: 0 2px 6px rgba(72, 187, 120, 0.6);
        }
        
        .step {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            transition: all 0.1s;
            display: flex;
            align-items: center;
            justify-content: center;
            user-select: none;
            font-size: 11px;
            color: var(--text-secondary, #718096);
        }
        
        .step:hover {
            transform: scale(1.1);
            border-color: var(--primary);
            background: #ffffff;
        }
        
        .step.active {
            background: linear-gradient(180deg, #63b3ed 0%, #3182ce 100%);
            border-color: #2b6cb0;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(49, 130, 206, 0.5);
            font-size: 14px;
            font-weight: 700;
        }
        
        .step.playing {
            background: linear-gradient(180deg, #f6ad55 0%, #ed8936 100%) !important;
            border-color: #c05621 !important;
            color: #ffffff !important;
            box-shadow: 0 0 10px rgba(237, 137, 54, 0.8);
            transform: scale(1.15);
        }

        .step.correct-reveal {
            background: linear-gradient(180deg, #68d391 0%, #38a169 100%) !important;
            border-color: #276749 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(56, 161, 105, 0.6);
        }

        .playback-controls {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
            margin-top: 12px;
            padding: 10px;
            background: var(--bg, #f0f4f8);
            border-radius: var(--radius);
        }
        
        .playback-controls button {
            padding: 8px 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 13px;
            background: var(--card-bg);
            color: var(--text);
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .playback-controls button:hover {
            background: var(--primary-light);
            border-color: var(--primary);
        }
        
        .playback-controls input[type="range"] {
            width: 100px;
            accent-color: var(--primary);
        }
        
        .playback-controls .bpm-display {
            font-weight: 600;
            color: var(--text);
            font-size: 13px;
        }

        @media (max-width: 768px) {
            .top-controls {
                flex-direction: column;
                align-items: stretch;
            }
            .buttons-group {
                flex-direction: row;
                align-self: stretch;
            }
            .buttons-group .btn-primary,
            .buttons-group .btn-secondary {
                flex: 1;
                text-align: center;
            }
            .step {
                width: 20px;
                height: 20px;
            }
            .beat-num {
                width: 92px;
                font-size: 14px;
            }
            .beat-numbers {
                margin-left: 48px;
                gap: 6px;
            }
            .track-steps {
                gap: 6px;
            }
            .beat-group {
                padding: 3px;
            }
            .track-name {
                width: 40px;
                font-size: 10px;
            }
            .success-badge {
                font-size: 12px;
                padding: 6px 14px;
                top: 8px;
                right: 8px;
            }
            .note-group img {
                height: 26px;
            }
        }
        
        @media (max-width: 480px) {
            .step {
                width: 18px;
                height: 18px;
                font-size: 9px;
            }
            .step.active {
                font-size: 12px;
            }
            .beat-num {
                width: 82px;
                font-size: 12px;
            }
            .beat-numbers {
                margin-left: 43px;
                gap: 4px;
            }
            .track-steps {
                gap: 4px;
            }
            .beat-group {
                padding: 2px;
            }
            .track-name {
                width: 35px;
                font-size: 9px;
            }
            .note-group img {
                height: 22px;
            }
            .mode-link {
                padding: 5px 10px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../includes/header.php'; ?>
    
    <main class="container">
        
        <div class="tool-container">
            <div class="card">
                
                <div class="mode-switcher">
                    <a href="gen-auto.php" class="mode-link">Автогенерация</a>
                    <a href="gen-manual.php" class="mode-link active">Ручной ввод</a>
                </div>
            
                <div class="top-controls">
                    <div class="buttons-group">
                        <button class="btn-primary" id="newQuestionBtn" disabled>Новый пример</button>
                        <button class="btn-secondary" id="showAnswerBtn">Показать ответ</button>
                    </div>
                    <div class="note-selector">
                        <div class="note-groups" id="noteGroups">
                            <label class="note-group">
                                <input type="checkbox" value="quarter">
                                <img src="../assets/pic/4n.png" alt="Четвертная">
                            </label>
                            <label class="note-group">
                                <input type="checkbox" value="eighth_pair">
                                <img src="../assets/pic/8n.png" alt="Восьмые">
                            </label>
                            <label class="note-group">
                                <input type="checkbox" value="sixteenth_quartet">
                                <img src="../assets/pic/16n.png" alt="Шестнадцатые">
                            </label>
                            <label class="note-group">
                                <input type="checkbox" value="sixteenth_pair_eighth">
                                <img src="../assets/pic/ccc2.png" alt="2шестн+восьм">
                            </label>
                            <label class="note-group">
                                <input type="checkbox" value="eighth_sixteenth_pair">
                                <img src="../assets/pic/c2cc.png" alt="восьм+2шестн">
                            </label>
                            <label class="note-group">
                                <input type="checkbox" value="sixteenth_eighth_sixteenth">
                                <img src="../assets/pic/cc2c.png" alt="шестн+восьм+шестн">
                            </label>
                            <label class="note-group">
                                <input type="checkbox" value="eighth_dot_sixteenth">
                                <img src="../assets/pic/c3cn.png" alt="восьм·+шестн">
                            </label>
                            <label class="note-group">
                                <input type="checkbox" value="z2c2">
                                <img src="../assets/pic/z2c2.png" alt="синк">
                            </label>
							<label class="note-group">
                                <input type="checkbox" value="z4">
                                <img src="../assets/pic/z4.png" alt="пауза">
                            </label>
                        </div>
                    </div>
                </div>

                <div class="sequencer-section">
                    <div class="success-badge" id="successBadge">
                        <span class="badge-icon">✓</span>
                        <span class="badge-text">Правильно!</span>
                    </div>

                    <div class="notation-container">
                        <div id="notationPaper">
                            <span class="placeholder">Кликайте на ноты выше, чтобы составить ритм (макс. 4 доли)</span>
                        </div>
                        <details>
                            <summary>Показать ABC-код</summary>
                            <textarea id="abcText" spellcheck="false"></textarea>
                        </details>
                    </div>
                    
                    <div class="beat-numbers" id="beatNumbers"></div>
                    <div id="drumGrid"></div>
                    
                    <div class="playback-controls">
                        <button onclick="playPattern()" id="playButton">▶️ Играть</button>
                        <button onclick="clearPattern()">🧹</button>
                        <input type="range" id="bpm" min="40" max="240" value="90">
                        <span class="bpm-display">BPM: <span id="bpmValue">90</span></span>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
    
    <script>
    class DrumSequencer {
        constructor() {
            this.STEPS = 16;
            this.STEPS_PER_BEAT = 4;
            this.BEATS = 4;
            
            this.TRACK = { 
                id: 'snare', 
                name: 'Snare', 
                abc: 'c'
            };
            
            this.state = {
                snare: Array(this.STEPS).fill(0)
            };
            
            this.solution = null;       // эталон (составляется через селектор)
            this.currentBeatFill = 0;   // сколько долей эталона заполнено
            this.isSolved = false;
            this.isRevealed = false;
            
            this.bpm = 90;
            this.isPlaying = false;
            this.currentStep = 0;
            this.intervalId = null;
            
            this.audioContext = null;
            this.audioReady = false;
            
            this.gridEl = document.getElementById('drumGrid');
            this.beatNumbersEl = document.getElementById('beatNumbers');
            this.notationPaper = document.getElementById('notationPaper');
            this.abcText = document.getElementById('abcText');
            this.successBadge = document.getElementById('successBadge');
            this.showAnswerBtn = document.getElementById('showAnswerBtn');
            
            this.notePatterns = {
                'quarter': [1,0,0,0],
                'eighth_pair': [1,0,1,0],
                'sixteenth_quartet': [1,1,1,1],
                'sixteenth_pair_eighth': [1,1,1,0],
                'eighth_sixteenth_pair': [1,0,1,1],
                'sixteenth_eighth_sixteenth': [1,1,0,1],
                'eighth_dot_sixteenth': [1,0,0,1],
                'z2c2': [0,0,1,0],
				'z4': [0,0,0,0]
            };
            
            this.init();
        }
        
        init() {
            this.renderBeatNumbers();
            this.renderGrid();
            this.updateNotation();
            this.initAudio();
            this.setupEventListeners();
            this.setupNoteSelector();
            this.updateSelectorState();
        }
        
        setupNoteSelector() {
            const labels = document.querySelectorAll('#noteGroups .note-group');
            labels.forEach(label => {
                const checkbox = label.querySelector('input[type="checkbox"]');
                
                checkbox.addEventListener('click', e => e.preventDefault());
                checkbox.addEventListener('change', e => e.preventDefault());
                
                label.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.addNote(checkbox.value);
                });
            });
        }
        
        addNote(noteKey) {
            if (this.currentBeatFill >= this.BEATS) return;
            
            const pattern = this.notePatterns[noteKey];
            if (!pattern) return;
            
            if (!this.solution) {
                this.solution = Array(this.STEPS).fill(0);
            }
            
            const start = this.currentBeatFill * this.STEPS_PER_BEAT;
            for (let i = 0; i < this.STEPS_PER_BEAT; i++) {
                this.solution[start + i] = pattern[i];
            }
            
            this.currentBeatFill++;
            this.isSolved = false;
            this.isRevealed = false;
            this.hideSuccess();
            
            this.updateNotation();
            this.updateSelectorState();
        }
        
        updateSelectorState() {
            const labels = document.querySelectorAll('#noteGroups .note-group');
            const isFull = this.currentBeatFill >= this.BEATS;
            labels.forEach(label => {
                if (isFull) {
                    label.classList.add('disabled');
                } else {
                    label.classList.remove('disabled');
                }
            });
        }
        
        initAudio() {
            try {
                this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                this.audioReady = true;
            } catch(e) {
                console.error('Web Audio API недоступен:', e);
            }
        }
        
        playSound() {
            if (!this.audioReady || !this.audioContext) return;
            if (this.audioContext.state === 'suspended') this.audioContext.resume();
            
            const now = this.audioContext.currentTime;
            this._playSnare(now);
        }
        
        _playSnare(time) {
            const osc = this.audioContext.createOscillator();
            const oscGain = this.audioContext.createGain();
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(200, time);
            osc.connect(oscGain);
            oscGain.connect(this.audioContext.destination);
            oscGain.gain.setValueAtTime(0.7, time);
            oscGain.gain.exponentialRampToValueAtTime(0.01, time + 0.1);
            osc.start(time);
            osc.stop(time + 0.1);
            
            const bufferSize = this.audioContext.sampleRate * 0.2;
            const buffer = this.audioContext.createBuffer(1, bufferSize, this.audioContext.sampleRate);
            const data = buffer.getChannelData(0);
            for (let i = 0; i < bufferSize; i++) {
                data[i] = Math.random() * 2 - 1;
            }
            const noise = this.audioContext.createBufferSource();
            noise.buffer = buffer;
            
            const noiseFilter = this.audioContext.createBiquadFilter();
            noiseFilter.type = 'highpass';
            noiseFilter.frequency.value = 1000;
            
            const noiseGain = this.audioContext.createGain();
            noise.connect(noiseFilter);
            noiseFilter.connect(noiseGain);
            noiseGain.connect(this.audioContext.destination);
            noiseGain.gain.setValueAtTime(0.8, time);
            noiseGain.gain.exponentialRampToValueAtTime(0.01, time + 0.15);
            
            noise.start(time);
            noise.stop(time + 0.2);
        }
        
        renderBeatNumbers() {
            this.beatNumbersEl.innerHTML = '';
            for (let b = 0; b < this.BEATS; b++) {
                const num = document.createElement('div');
                num.className = 'beat-num';
                num.textContent = (b + 1);
                num.dataset.beat = b;
                this.beatNumbersEl.appendChild(num);
            }
        }
        
        renderGrid() {
            this.gridEl.innerHTML = '';
            
            const row = document.createElement('div');
            row.className = 'track-row';
            
            const name = document.createElement('div');
            name.className = 'track-name';
            name.textContent = this.TRACK.name;
            row.appendChild(name);
            
            const steps = document.createElement('div');
            steps.className = 'track-steps';
            
            for (let b = 0; b < this.BEATS; b++) {
                const beatGroup = document.createElement('div');
                beatGroup.className = 'beat-group ' + (b % 2 === 0 ? 'beat-even' : 'beat-odd');
                beatGroup.dataset.beat = b;
                
                for (let i = 0; i < this.STEPS_PER_BEAT; i++) {
                    const stepIndex = b * this.STEPS_PER_BEAT + i;
                    const step = document.createElement('div');
                    step.className = 'step ' + this.TRACK.id;
                    step.dataset.step = stepIndex;
                    step.dataset.track = this.TRACK.id;
                    
                    if (this.state[this.TRACK.id][stepIndex]) {
                        step.classList.add('active');
                        step.textContent = '●';
                    }
                    
                    step.addEventListener('click', () => {
                        this.state[this.TRACK.id][stepIndex] = this.state[this.TRACK.id][stepIndex] ? 0 : 1;
                        step.classList.toggle('active');
                        step.textContent = this.state[this.TRACK.id][stepIndex] ? '●' : '';
                        this.updateNotation();
                        this.checkSolution();
                    });
                    
                    beatGroup.appendChild(step);
                }
                
                steps.appendChild(beatGroup);
            }
            
            row.appendChild(steps);
            this.gridEl.appendChild(row);
            
            if (this.solution) {
                this.updateBeatHighlights();
            }
        }
        
        buildTokens(steps) {
            const tokens = [];
            let i = 0;
            
            while (i < this.STEPS) {
                const isActive = steps[i];
                const currentBeat = Math.floor(i / this.STEPS_PER_BEAT);
                const beatEnd = (currentBeat + 1) * this.STEPS_PER_BEAT;
                
                if (!isActive) {
                    let len = 0;
                    while (i + len < beatEnd && !steps[i + len]) len++;
                    tokens.push({ type: 'rest', duration: len, endPos: i + len });
                    i += len;
                } else {
                    let len = 0;
                    while (i + len < beatEnd) {
                        if (len > 0 && steps[i + len]) break;
                        len++;
                    }
                    tokens.push({ type: 'note', duration: len, abc: this.TRACK.abc, endPos: i + len });
                    i += len;
                }
            }
            
            return tokens;
        }
        
        generateNotationOnly(steps) {
            const tokens = this.buildTokens(steps);
            let abc = '';
            for (let t = 0; t < tokens.length; t++) {
                const tok = tokens[t];
                const dur = tok.duration > 1 ? tok.duration : '';
                abc += tok.type === 'rest' ? 'z' + dur : tok.abc + dur;
                if (t < tokens.length - 1 && tok.endPos % this.STEPS_PER_BEAT === 0) {
                    abc += ' ';
                }
            }
            return abc;
        }
        
        generateFullABC(steps) {
            const notation = this.generateNotationOnly(steps);
            return 'X:1\nM:4/4\nL:1/16\nQ:1/4=' + this.bpm + '\nK:C clef=perc\n' + notation;
        }
        
        updateNotation() {
            const displaySteps = (this.solution && !this.isSolved && !this.isRevealed)
                ? this.solution
                : this.state[this.TRACK.id];
            
            const notationOnly = this.generateNotationOnly(displaySteps);
            const fullABC = this.generateFullABC(displaySteps);
            
            this.abcText.value = notationOnly;
            
            if (!this.solution && !displaySteps.some(s => s)) {
                this.notationPaper.innerHTML = '<span class="placeholder">Кликайте на ноты выше, чтобы составить ритм (макс. 4 доли)</span>';
                return;
            }
            
            this.notationPaper.innerHTML = '';
            try {
                ABCJS.renderAbc(this.notationPaper, fullABC, {
                    add_classes: true,
                    responsive: 'resize',
                    padding: [10, 5, 10, 5],
                });
            } catch(e) {
                console.error('ABC render error:', e);
                this.notationPaper.innerHTML = '<span style="color:red;">Ошибка рендера</span>';
            }
        }
        
        checkBeat(beatIndex) {
            if (!this.solution) return false;
            const start = beatIndex * this.STEPS_PER_BEAT;
            for (let i = 0; i < this.STEPS_PER_BEAT; i++) {
                if (this.state[this.TRACK.id][start + i] !== this.solution[start + i]) {
                    return false;
                }
            }
            return true;
        }
        
        updateBeatHighlights() {
            if (!this.solution) return;
            for (let b = 0; b < this.BEATS; b++) {
                const group = document.querySelector('.beat-group[data-beat="' + b + '"]');
                if (!group) continue;
                if (this.checkBeat(b)) {
                    group.classList.add('correct');
                } else {
                    group.classList.remove('correct');
                }
            }
        }
        
        checkSolution() {
            if (!this.solution) return;
            
            this.updateBeatHighlights();
            
            let allCorrect = true;
            for (let b = 0; b < this.BEATS; b++) {
                if (!this.checkBeat(b)) {
                    allCorrect = false;
                    break;
                }
            }
            
            if (allCorrect) {
                if (!this.isSolved && !this.isRevealed) {
                    this.isSolved = true;
                    this.showSuccess(false);
                }
            } else {
                if (this.isSolved || this.isRevealed) {
                    this.isSolved = false;
                    this.isRevealed = false;
                    this.hideSuccess();
                }
            }
        }
        
        showSuccess(isReveal = false) {
            if (!this.successBadge) return;
            const icon = this.successBadge.querySelector('.badge-icon');
            const text = this.successBadge.querySelector('.badge-text');
            if (isReveal) {
                icon.textContent = '💡';
                text.textContent = 'Вот ответ!';
            } else {
                icon.textContent = '✓';
                text.textContent = 'Правильно!';
            }
            this.successBadge.classList.add('show');
        }
        
        hideSuccess() {
            if (!this.successBadge) return;
            this.successBadge.classList.remove('show');
        }
        
        showAnswer() {
            if (!this.solution) {
                alert('Сначала составьте ритм, кликая на ноты!');
                return;
            }
            this.state[this.TRACK.id] = [...this.solution];
            this.isRevealed = true;
            this.isSolved = false;
            this.renderGrid();
            this.updateNotation();
            this.showSuccess(true);
            
            document.querySelectorAll('.step').forEach(step => {
                const idx = parseInt(step.dataset.step, 10);
                if (this.solution[idx]) {
                    step.classList.add('correct-reveal');
                }
            });
        }
        
        playPattern() {
            if (this.isPlaying) {
                this.stopPattern();
                return;
            }
            if (this.audioContext && this.audioContext.state === 'suspended') {
                this.audioContext.resume();
            }
            
            this.isPlaying = true;
            document.getElementById('playButton').textContent = '⏹️ Стоп';
            
            const stepTime = (60 / this.bpm) / 4 * 1000;
            
            this.intervalId = setInterval(() => {
                this.highlightStep(this.currentStep);
                
                if (this.state[this.TRACK.id][this.currentStep]) {
                    this.playSound();
                }
                
                this.currentStep = (this.currentStep + 1) % this.STEPS;
            }, stepTime);
        }
        
        stopPattern() {
            this.isPlaying = false;
            clearInterval(this.intervalId);
            document.getElementById('playButton').textContent = '▶️ Играть';
            this.clearHighlights();
            this.currentStep = 0;
        }
        
        highlightStep(step) {
            this.clearHighlights();
            
            document.querySelectorAll('.step[data-step="' + step + '"]').forEach(cell => {
                cell.classList.add('playing');
            });
            
            const beatIndex = Math.floor(step / this.STEPS_PER_BEAT);
            document.querySelectorAll('.beat-group[data-beat="' + beatIndex + '"]').forEach(group => {
                group.classList.add('playing');
            });
            
            document.querySelectorAll('.beat-num[data-beat="' + beatIndex + '"]').forEach(num => {
                num.classList.add('playing');
            });
        }
        
        clearHighlights() {
            document.querySelectorAll('.step.playing').forEach(c => c.classList.remove('playing'));
            document.querySelectorAll('.beat-group.playing').forEach(c => c.classList.remove('playing'));
            document.querySelectorAll('.beat-num.playing').forEach(c => c.classList.remove('playing'));
        }
        
        clearPattern() {
            this.state.snare = Array(this.STEPS).fill(0);
            this.solution = null;
            this.currentBeatFill = 0;
            this.isSolved = false;
            this.isRevealed = false;
            this.hideSuccess();
            this.renderGrid();
            this.updateNotation();
            this.updateSelectorState();
            if (this.isPlaying) this.stopPattern();
        }
        
        setupEventListeners() {
            const bpmSlider = document.getElementById('bpm');
            const bpmVal = document.getElementById('bpmValue');
            bpmSlider.addEventListener('input', () => {
                this.bpm = parseInt(bpmSlider.value, 10);
                bpmVal.textContent = this.bpm;
                if (this.isPlaying) {
                    this.stopPattern();
                    this.playPattern();
                }
            });
        }
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        window.drumSequencer = new DrumSequencer();
        
        window.playPattern = () => window.drumSequencer.playPattern();
        window.clearPattern = () => window.drumSequencer.clearPattern();
        
        document.getElementById('showAnswerBtn').addEventListener('click', () => {
            window.drumSequencer.showAnswer();
        });
    });
    </script>
</body>
</html>