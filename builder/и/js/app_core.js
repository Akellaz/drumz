/**
 * app_core.js
 * Основная логика приложения: рендер, секвенсор, нотация, связка модулей.
 * ОБНОВЛЕНО: Убрано автосохранение. Сохранение только по кнопке в хедере.
 */
(function() {
    const SECTIONS = [
        { type: 'intro', name: 'Вступление', color: 'intro' },
        { type: 'verse', name: 'Куплет', color: 'verse' },
        { type: 'chorus', name: 'Припев', color: 'chorus' },
        { type: 'bridge', name: 'Бридж', color: 'bridge' },
        { type: 'solo', name: 'Соло', color: 'solo' },
        { type: 'outro', name: 'Аутро', color: 'outro' },
    ];

    const TRACKS = [
        { id: 'crash', name: 'Crash', symbol: 'c', cssClass: 'crash' },
        { id: 'ride', name: 'Ride', symbol: 'r', cssClass: 'ride' },
        { id: 'hihat', name: 'Hi-Hat', symbol: 'x', cssClass: 'hihat' },
        { id: 'hihat_open', name: 'OpenHH', symbol: 'o', cssClass: 'hihat-open' },
        { id: 'snare', name: 'Snare', symbol: 'o', cssClass: 'snare' },
        { id: 'kick', name: 'Kick', symbol: 'o', cssClass: 'kick' },
        { id: 'tom1', name: 'Tom 1', symbol: 'o', cssClass: 'tom1' },
        { id: 'tom2', name: 'Tom 2', symbol: 'o', cssClass: 'tom2' },
        { id: 'tom3', name: 'Tom 3', symbol: 'o', cssClass: 'tom3' }
    ];

    const STEPS = 16;
    const BARS_PER_PHRASE = 4;
    const BAR_SIZE = 32;
    const BAR_GAP = 2;

    let songsMeta = { songs: [], activeSongId: null };
    let song = { bpm: 120, timeSig: '4/4', blocks: [] };
    let selectedBar = null;
    let sequencerMode = 'compact';
    let clipboardPattern = null;
    let timelineCompact = false;
    let isPainting = false;
    let notationChunksMap = [];
    let paintValue = 1;

    function migrateSong(s) {
        if (!s || !Array.isArray(s.blocks)) return s;
        s.blocks.forEach(block => {
            if (!block.patterns) return;
            Object.keys(block.patterns).forEach(barIdx => {
                const pattern = block.patterns[barIdx];
                TRACKS.forEach(t => {
                    if (!pattern[t.id] || !Array.isArray(pattern[t.id])) {
                        pattern[t.id] = new Array(STEPS).fill(0);
                    } else if (pattern[t.id].length !== STEPS) {
                        const newArr = new Array(STEPS).fill(0);
                        for (let i = 0; i < Math.min(STEPS, pattern[t.id].length); i++) {
                            newArr[i] = pattern[t.id][i];
                        }
                        pattern[t.id] = newArr;
                    }
                });
            });
        });
        return s;
    }

    function beatsPerBar() { return parseInt(song.timeSig.split('/')[0]); }
    function barDurationSec() { return beatsPerBar() * (60 / song.bpm); }
    function formatTime(sec) { const m = Math.floor(sec / 60); const s = Math.floor(sec % 60); return `${m}:${s.toString().padStart(2, '0')}`; }
    function phraseCount(bars) { return Math.ceil(bars / BARS_PER_PHRASE); }
    function nextLabel(type) { const count = song.blocks.filter(b => b.type === type).length + 1; return `${SECTIONS.find(s => s.type === type).name} ${count}`; }

    function emptyBarPattern() { const p = {}; TRACKS.forEach(t => p[t.id] = new Array(STEPS).fill(0)); return p; }
    function getBarPattern(blockId, barIndex) {
        const block = song.blocks.find(b => b.id === blockId);
        if (!block) return null;
        if (!block.patterns) block.patterns = {};
        if (!block.patterns[barIndex]) block.patterns[barIndex] = emptyBarPattern();
        const pattern = block.patterns[barIndex];
        TRACKS.forEach(t => {
            if (!pattern[t.id] || !Array.isArray(pattern[t.id])) {
                pattern[t.id] = new Array(STEPS).fill(0);
            }
        });
        return pattern;
    }
    function isPatternFilled(pattern) {
        if (!pattern) return false;
        return TRACKS.some(t => pattern[t.id] && pattern[t.id].some(v => v));
    }

    function getTabChar(trackId, val) {
        if (val === 0) return '-';
        if (trackId === 'hihat') return val === 2 ? 'X' : 'x';
        if (trackId === 'hihat_open') return 'o';
        if (trackId === 'snare') return val === 2 ? 'O' : 'o';
        if (trackId === 'crash') return 'c';
        if (trackId === 'ride') return 'r';
        return 'o';
    }

    function buildHHString(hihat, hihat_open, crash, ride) {
        let s = '|';
        for (let i = 0; i < STEPS; i++) {
            if (crash[i]) s += 'c';
            else if (ride[i]) s += 'r';
            else if (hihat_open[i]) s += 'o';
            else s += getTabChar('hihat', hihat[i]);
        }
        return s + '|';
    }

    function stepsToTab(trackId, steps) {
        return '|' + steps.map(v => getTabChar(trackId, v)).join('') + '|';
    }

    function calcBlockWidth(numBars, compact = false) {
        const barSize = compact ? 16 : BAR_SIZE;
        const gap = compact ? 1 : BAR_GAP;
        const phraseGap = compact ? 2 : 6;
        const padding = compact ? 16 : 20;
        return padding + numBars * barSize + (numBars - 1) * gap + Math.floor((numBars - 1) / BARS_PER_PHRASE) * phraseGap;
    }

    function exportBlockMIDI(blockId) {
        const block = song.blocks.find(b => b.id === blockId);
        if (!block) return;
        const hasAnyNotes = Array.from({ length: block.length }, (_, i) => block.patterns && block.patterns[i])
            .some(p => p && TRACKS.some(t => p[t.id] && p[t.id].some(v => v)));
        if (!hasAnyNotes) {
            alert(`В секции «${block.label}» нет ни одной ноты для экспорта.`);
            return;
        }
        const isolatedBlock = JSON.parse(JSON.stringify(block));
        const tempSong = { bpm: song.bpm, timeSig: song.timeSig, blocks: [isolatedBlock] };
        if (window.IOManager && window.IOManager.exportMIDI) {
            const safeName = block.label.replace(/[^a-z0-9а-яё]/gi, '_');
            window.IOManager.exportMIDI(tempSong, safeName);
        } else {
            alert('Ошибка: модуль экспорта MIDI (IOManager) не найден.');
        }
    }

    function updateCurrentSongName() {
        const s = songsMeta.songs.find(x => x.id === songsMeta.activeSongId);
        if (window.HeaderUI) window.HeaderUI.updateSongName(s ? s.name : 'Без названия');
    }

    function renderPalette() {
        const pal = document.getElementById('palette');
        pal.innerHTML = '';
        SECTIONS.forEach(sec => {
            const el = document.createElement('div');
            el.className = `palette-item color-${sec.color}`;
            el.textContent = sec.name;
            el.setAttribute('data-type', sec.type);
            pal.appendChild(el);
        });
    }

    function renderTimeline() {
        const tl = document.getElementById('timeline');
        if (timelineCompact) tl.classList.add('compact');
        else tl.classList.remove('compact');

        tl.innerHTML = '';
        song.blocks.forEach((block, idx) => {
            const sec = SECTIONS.find(s => s.type === block.type);
            const el = document.createElement('div');
            el.className = `block color-${sec.color}${block.collapsed ? ' collapsed' : ''}`;

            if (!block.collapsed) el.style.width = calcBlockWidth(block.length, timelineCompact) + 'px';
            el.setAttribute('data-id', block.id);
            el.title = `${block.length} такт. · ${phraseCount(block.length)} стр. · ${formatTime(block.length * barDurationSec())}`;

            let barsHtml = '';
            if (!block.collapsed) {
                for (let i = 0; i < block.length; i++) {
                    const isSelected = selectedBar && selectedBar.blockId === block.id && selectedBar.barIndex === i;
                    const filled = isPatternFilled(block.patterns && block.patterns[i]);
                    barsHtml += `<div class="bar${isSelected ? ' selected' : ''}${filled ? ' filled' : ''}" data-block="${block.id}" data-bar="${i}"><span class="bar-number">${i + 1}</span></div>`;
                    if ((i + 1) % BARS_PER_PHRASE === 0 && i < block.length - 1) barsHtml += '<div class="phrase-divider"></div>';
                }
            }

            el.innerHTML = `<div class="block-header"><span>${block.label}</span></div><div class="block-bars">${barsHtml}</div>
                <div class="block-controls">
                    <button data-act="minus" title="Уменьшить">−</button>
                    <button data-act="plus" title="Увеличить">+</button>
                    <button data-act="dup" title="Дублировать блок">⎘</button>
                    <button data-act="break" class="${block.breakAfter ? 'active-page-break' : ''}" title="Перенести следующий блок на новую строку">↵</button>
                    <button data-act="pagebreak" class="${block.pageBreak ? 'active-page-break' : ''}" title="Разрыв страницы при печати">📄</button>
                    <button data-act="export-midi" title="Экспорт MIDI только этой секции">🎵</button>
                    <button data-act="del" title="Удалить">×</button>
                </div>`;

            el.querySelector('.block-header').addEventListener('dblclick', (e) => {
                e.stopPropagation();
                block.collapsed = !block.collapsed;
                render();
            });

            if (!block.collapsed) {
                el.querySelectorAll('.bar').forEach(barEl => {
                    barEl.addEventListener('mousedown', e => e.stopPropagation());
                    barEl.addEventListener('click', e => { e.stopPropagation(); selectBar(barEl.dataset.block, parseInt(barEl.dataset.bar)); });
                    barEl.addEventListener('contextmenu', (e) => {
                        e.preventDefault(); e.stopPropagation();
                        const menu = document.getElementById('contextMenu');
                        menu.style.display = 'block';
                        menu.style.left = e.pageX + 'px'; menu.style.top = e.pageY + 'px';
                        menu.dataset.blockId = barEl.dataset.block;
                        menu.dataset.barIndex = barEl.dataset.bar;
                        const pasteItem = menu.querySelector('[data-action="paste"]');
                        if (!clipboardPattern) pasteItem.classList.add('disabled');
                        else pasteItem.classList.remove('disabled');
                    });
                });
            }

            el.addEventListener('click', e => {
                const act = e.target.dataset.act;
                if (!act) return;
                if (act === 'plus') block.length++;
                if (act === 'minus') {
                    block.length = Math.max(1, block.length - 1);
                    if (block.patterns) Object.keys(block.patterns).forEach(k => { if (parseInt(k) >= block.length) delete block.patterns[k]; });
                }
                if (act === 'dup') {
                    const copy = { ...block, id: Math.random().toString(36).slice(2, 10), label: nextLabel(block.type), patterns: {}, collapsed: false, pageBreak: false, breakAfter: false };
                    if (block.patterns) {
                        copy.patterns = {};
                        Object.keys(block.patterns).forEach(k => {
                            copy.patterns[k] = {};
                            TRACKS.forEach(t => copy.patterns[k][t.id] = [...block.patterns[k][t.id]]);
                        });
                    }
                     song.blocks.push(copy);
                }
                if (act === 'break') block.breakAfter = !block.breakAfter;
                if (act === 'pagebreak') block.pageBreak = !block.pageBreak;
                if (act === 'export-midi') {
                    exportBlockMIDI(block.id);
                    return;
                }
                if (act === 'del') {
                    song.blocks = song.blocks.filter(b => b.id !== block.id);
                    if (selectedBar && selectedBar.blockId === block.id) selectedBar = null;
                }
                render();
            });
            tl.appendChild(el);

            if (block.breakAfter) {
                const breakEl = document.createElement('div');
                breakEl.className = 'timeline-break';
                tl.appendChild(breakEl);
            }
        });
    }

    function selectBar(blockId, barIndex) {
        selectedBar = { blockId, barIndex };
        const panel = document.getElementById('bottomPanel');
        panel.classList.remove('collapsed');
        panel.classList.add('expanded');
        render();
    }

    function renderMiniTimeline() {
        const mini = document.getElementById('miniTimeline');
        if (!mini) return;
        if (song.blocks.length === 0) {
            mini.innerHTML = '<div class="mini-empty">Добавьте секции для навигации</div>';
            return;
        }
        let html = '';
        song.blocks.forEach(block => {
            const sec = SECTIONS.find(s => s.type === block.type);
            html += `<div class="mini-block color-${sec.color}" title="${block.label}">`;
            for (let i = 0; i < block.length; i++) {
                const isSelected = selectedBar && selectedBar.blockId === block.id && selectedBar.barIndex === i;
                const filled = isPatternFilled(block.patterns && block.patterns[i]);
                html += `<div class="mini-bar${isSelected ? ' selected' : ''}${filled ? ' filled' : ''}" data-block="${block.id}" data-bar="${i}"></div>`;
            }
            html += '</div>';
        });
        mini.innerHTML = html;
        mini.querySelectorAll('.mini-bar').forEach(bar => {
            bar.addEventListener('click', () => selectBar(bar.dataset.block, parseInt(bar.dataset.bar)));
        });
        const selectedEl = mini.querySelector('.mini-bar.selected');
        if (selectedEl) selectedEl.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }

    function navigateBar(direction) {
        if (!selectedBar) return;
        const block = song.blocks.find(b => b.id === selectedBar.blockId);
        if (!block) return;
        let newBlockId = selectedBar.blockId;
        let newBarIndex = selectedBar.barIndex + direction;
        if (newBarIndex < 0) {
            const blockIdx = song.blocks.findIndex(b => b.id === selectedBar.blockId);
            if (blockIdx > 0) { newBlockId = song.blocks[blockIdx - 1].id; newBarIndex = song.blocks[blockIdx - 1].length - 1; } else return;
        } else if (newBarIndex >= block.length) {
            const blockIdx = song.blocks.findIndex(b => b.id === selectedBar.blockId);
            if (blockIdx < song.blocks.length - 1) { newBlockId = song.blocks[blockIdx + 1].id; newBarIndex = 0; } else return;
        }
        selectBar(newBlockId, newBarIndex);
    }

    function renderSequencer() {
        const container = document.getElementById('sequencer');
        const ctx = document.getElementById('bottomContext');
        const toggleBtn = document.getElementById('seqModeToggle');

        if (!selectedBar) {
            container.innerHTML = '<div class="seq-placeholder">Кликните по такту в кирпичике, чтобы редактировать</div>';
            ctx.innerHTML = 'Такт не выбран';
            if (toggleBtn) toggleBtn.style.visibility = 'hidden';
            return;
        }
        if (toggleBtn) {
            toggleBtn.style.visibility = 'visible';
            toggleBtn.textContent = sequencerMode === 'compact' ? 'Полный вид' : 'Компактный вид';
        }

        const block = song.blocks.find(b => b.id === selectedBar.blockId);
        if (!block) { selectedBar = null; renderSequencer(); return; }

        const pattern = getBarPattern(selectedBar.blockId, selectedBar.barIndex);
        ctx.innerHTML = `Редактируется: <b>${block.label}</b> · такт <b>${selectedBar.barIndex + 1}</b>/${block.length}
            <span class="nav-buttons">
                <button class="nav-btn" id="prevBarBtn" title="Предыдущий такт (←)">◀</button>
                <button class="nav-btn" id="nextBarBtn" title="Следующий такт (→)">▶</button>
            </span>`;

        document.getElementById('prevBarBtn').onclick = () => navigateBar(-1);
        document.getElementById('nextBarBtn').onclick = () => navigateBar(1);

        let beatNumsHtml = '<div></div>';
        for (let i = 0; i < STEPS; i++) {
            const isMain = i % 4 === 0;
            beatNumsHtml += `<div class="seq-beat-num${isMain ? ' main' : ''}">${isMain ? (i/4 + 1) : '·'}</div>`;
        }

        const activeTracks = sequencerMode === 'compact'
            ? TRACKS.filter(t => ['hihat', 'snare', 'kick'].includes(t.id))
            : TRACKS;

        let tracksHtml = '';
        activeTracks.forEach(track => {
            let stepsHtml = '';
            for (let i = 0; i < STEPS; i++) {
                const val = pattern[track.id][i];
                const activeClass = val === 1 ? ' active' : (val === 2 ? ' active accent' : '');
                const beatStart = i % 4 === 0 ? ' beat-start' : '';
                let tooltip = 'ЛКМ: нота';
                if (track.id === 'hihat' || track.id === 'snare') {
                    tooltip = 'ЛКМ: нота, ПКМ: акцент';
                }
                stepsHtml += `<div class="seq-step ${track.cssClass}${activeClass}${beatStart}" data-track="${track.id}" data-step="${i}" title="${tooltip}"></div>`;
            }
            tracksHtml += `<div class="seq-row"><div class="seq-label" data-preset-track="${track.id}" title="Клик — заполнить паттерн" style="cursor:pointer;">${track.name}</div>${stepsHtml}</div>`;
        });

        container.innerHTML = `<div class="seq-container"><div class="seq-beat-nums">${beatNumsHtml}</div>${tracksHtml}
            <div class="seq-actions">
                <button data-seq-act="clear">Очистить</button>
                <button data-seq-act="random">Случайно</button>
                <button data-seq-act="copy-all">Заполнить до конца блока</button>
            </div></div>`;

        container.querySelectorAll('[data-preset-track]').forEach(label => {
            label.addEventListener('click', () => {
                const trackId = label.dataset.presetTrack;
                const currentPattern = getBarPattern(selectedBar.blockId, selectedBar.barIndex);
                const current = currentPattern[trackId];
                const filledCount = current.filter(v => v).length;
                const isQuarter = filledCount === 4 && current[0] && current[4] && current[8] && current[12];
                const isEighth = filledCount === 8 && current[0] && current[2] && current[4] && current[6] && current[8] && current[10] && current[12] && current[14];
                const isSixteenth = filledCount === 16;

                if (isSixteenth) current.fill(0);
                else if (isEighth) current.forEach((_, i) => current[i] = 1);
                else if (isQuarter) current.forEach((_, i) => current[i] = i % 2 === 0 ? 1 : 0);
                else current.forEach((_, i) => current[i] = i % 4 === 0 ? 1 : 0);

                renderSequencer(); updateNotation(); updateBarInTimeline(); updateFullNotation();
                // 🔥 УБРАНО: saveCurrentSong();
            });
        });

        container.querySelectorAll('[data-seq-act]').forEach(btn => {
            btn.addEventListener('click', () => {
                const act = btn.dataset.seqAct;
                const currentPattern = getBarPattern(selectedBar.blockId, selectedBar.barIndex);

                if (act === 'clear') {
                    TRACKS.forEach(t => currentPattern[t.id].fill(0));
                    renderSequencer(); updateNotation(); updateBarInTimeline(); updateFullNotation();
                    // 🔥 УБРАНО: saveCurrentSong();
                }
                if (act === 'random') {
                    currentPattern.hihat = currentPattern.hihat.map((_, i) => (i % 2 === 0 || Math.random() > 0.6) ? 1 : 0);
                    currentPattern.hihat_open.fill(0);
                    currentPattern.snare.fill(0); currentPattern.snare[4] = 1; currentPattern.snare[12] = 1; if (Math.random() > 0.5) currentPattern.snare[10] = 1;
                    currentPattern.kick.fill(0); currentPattern.kick[0] = 1; currentPattern.kick[8] = 1; if (Math.random() > 0.5) currentPattern.kick[6] = 1;
                    renderSequencer(); updateNotation(); updateBarInTimeline(); updateFullNotation();
                    // 🔥 УБРАНО: saveCurrentSong();
                }
                if (act === 'copy-all') {
                    const src = JSON.parse(JSON.stringify(currentPattern));
                    for (let i = selectedBar.barIndex + 1; i < block.length; i++) block.patterns[i] = JSON.parse(JSON.stringify(src));
                    renderTimeline(); updateFullNotation();
                    // 🔥 УБРАНО: saveCurrentSong();
                }
            });
        });
    }

    function initSequencerEvents() {
        const container = document.getElementById('sequencer');
        container.addEventListener('mousedown', (e) => {
            if (e.target.classList.contains('seq-step')) {
                e.preventDefault();
                isPainting = true;
                const t = e.target.dataset.track;
                const s = parseInt(e.target.dataset.step);
                const currentPattern = getBarPattern(selectedBar.blockId, selectedBar.barIndex);
                const currentVal = currentPattern[t][s];

                if (e.button === 2) {
                    if (t === 'hihat' || t === 'snare') {
                        paintValue = (currentVal === 2) ? 1 : 2;
                    } else {
                        paintValue = (currentVal === 1) ? 0 : 1;
                    }
                } else {
                    paintValue = (currentVal > 0) ? 0 : 1;
                }
                applyPaint(e.target);
            }
        });

        container.addEventListener('mouseover', (e) => {
            if (isPainting && e.target.classList.contains('seq-step')) {
                const t = e.target.dataset.track;
                if (e.buttons === 2 && (t === 'hihat' || t === 'snare')) {
                    paintValue = 2;
                } else {
                    paintValue = 1;
                }
                applyPaint(e.target);
            }
        });

        container.addEventListener('contextmenu', (e) => {
            if (e.target.classList.contains('seq-step')) {
                e.preventDefault();
            }
        });
    }

    function applyPaint(target) {
        if (!target.classList.contains('seq-step') || !selectedBar) return;
        const currentPattern = getBarPattern(selectedBar.blockId, selectedBar.barIndex);
        if (!currentPattern) return;
        const t = target.dataset.track;
        const s = parseInt(target.dataset.step);

        currentPattern[t][s] = paintValue;

        target.classList.remove('active', 'accent');
        if (paintValue === 1) target.classList.add('active');
        if (paintValue === 2) target.classList.add('active', 'accent');

        updateNotation();
        updateBarInTimeline();
        updateFullNotation();
        // 🔥 УБРАНО: saveCurrentSong();
    }

    function updateBarInTimeline() {
        if (!selectedBar) return;
        const barEl = document.querySelector(`.bar[data-block="${selectedBar.blockId}"][data-bar="${selectedBar.barIndex}"]`);
        if (barEl) barEl.classList.toggle('filled', isPatternFilled(getBarPattern(selectedBar.blockId, selectedBar.barIndex)));
    }

    function updateNotation() {
        const errorBox = document.getElementById('notationError');
        const paper = document.getElementById('notationPaper');
        errorBox.innerHTML = '';
        if (!selectedBar) { paper.innerHTML = '<span class="placeholder">Выберите такт</span>'; return; }
        if (typeof GrooveUtils === 'undefined') {
            paper.innerHTML = '<span class="placeholder">GrooveScribe не загружен</span>';
            errorBox.innerHTML = '<div class="notation-error">Проверьте, что файлы js/abc2svg-1.js и js/groove_utils.js существуют</div>';
            return;
        }
        try {
            const pattern = getBarPattern(selectedBar.blockId, selectedBar.barIndex);
            const gu = new GrooveUtils();
            const data = new gu.grooveDataNew();
            data.timeDivision = 16; data.tempo = song.bpm; data.numberOfMeasures = 1; data.numBeats = beatsPerBar(); data.noteValue = 4;
            data.notesPerMeasure = data.timeDivision * data.numBeats / data.noteValue;
            data.hh_array = gu.noteArraysFromURLData('H', buildHHString(pattern.hihat, pattern.hihat_open, pattern.crash, pattern.ride), data.notesPerMeasure, 1);
            data.snare_array = gu.noteArraysFromURLData('S', stepsToTab('snare', pattern.snare), data.notesPerMeasure, 1);
            data.kick_array = gu.noteArraysFromURLData('K', stepsToTab('kick', pattern.kick), data.notesPerMeasure, 1);
            data.toms_array = [
                gu.noteArraysFromURLData('T1', stepsToTab('tom1', pattern.tom1), data.notesPerMeasure, 1),
                gu.noteArraysFromURLData('T2', stepsToTab('tom2', pattern.tom2), data.notesPerMeasure, 1),
                gu.noteArraysFromURLData('T3', stepsToTab('tom3', pattern.tom3), data.notesPerMeasure, 1),
                gu.noteArraysFromURLData('T4', '|----------------|', data.notesPerMeasure, 1)
            ];
            paper.innerHTML = gu.renderABCtoSVG(gu.createABCFromGrooveData(data, 400)).svg;
        } catch (e) {
            console.error(e);
            errorBox.innerHTML = `<div class="notation-error">Ошибка: ${e.message}</div>`;
            paper.innerHTML = '<span class="placeholder">Ошибка рендера</span>';
        }
    }

    function updateFullNotation() {
        const content = document.getElementById('fullNotationContent');
        const panel = document.getElementById('fullNotationPanel');
        if (panel.classList.contains('collapsed')) return;
        const totalBars = song.blocks.reduce((sum, b) => sum + b.length, 0);
        if (totalBars === 0) { content.innerHTML = '<span class="placeholder">Добавьте секции для генерации нотации</span>'; return; }
        if (typeof GrooveUtils === 'undefined') { content.innerHTML = '<span class="placeholder" style="color:#fc8181;">GrooveScribe не загружен</span>'; return; }

        const chunks = [];
        song.blocks.forEach(block => {
            const sec = SECTIONS.find(s => s.type === block.type);
            const blockBars = [];
            for (let i = 0; i < block.length; i++) {
                blockBars.push({ pattern: (block.patterns && block.patterns[i]) ? block.patterns[i] : emptyBarPattern(), blockLabel: block.label, sectionName: sec.name });
            }
            for (let i = 0; i < blockBars.length; i += BARS_PER_PHRASE) {
                chunks.push({
                    bars: blockBars.slice(i, i + BARS_PER_PHRASE),
                    blockId: block.id,
                    startBarIndex: i,
                    blockLabel: block.label,
                    sectionName: sec.name,
                    isFirstChunkOfBlock: i === 0,
                    pageBreak: block.pageBreak
                });
            }
        });

        notationChunksMap = [];
        let html = '', lastBlockLabel = '';
        chunks.forEach((chunk, chunkIndex) => {
            notationChunksMap.push({
                blockId: chunk.blockId,
                startBarIndex: chunk.startBarIndex,
                barCount: chunk.bars.length
            });

            if (chunk.blockLabel !== lastBlockLabel) {
                const breakClass = (chunk.isFirstChunkOfBlock && chunk.pageBreak) ? ' page-break-before' : '';
                html += `<div class="section-divider${breakClass}" data-pagebreak="${chunk.pageBreak && chunk.isFirstChunkOfBlock ? 'true' : 'false'}"><span class="section-divider-label">${chunk.sectionName}: ${chunk.blockLabel}</span></div>`;
                lastBlockLabel = chunk.blockLabel;
            }
            
            try {
                let hStr = '', sStr = '', kStr = '', t1Str = '', t2Str = '', t3Str = '', t4Str = '';
                chunk.bars.forEach(b => {
                    const p = b.pattern;
                    hStr += '|'; sStr += '|'; kStr += '|'; t1Str += '|'; t2Str += '|'; t3Str += '|'; t4Str += '|';
                    for (let step = 0; step < 16; step++) {
                        if (p.crash[step]) hStr += 'c';
                        else if (p.ride[step]) hStr += 'r';
                        else if (p.hihat_open[step]) hStr += 'o';
                        else hStr += getTabChar('hihat', p.hihat[step]);
                        sStr += getTabChar('snare', p.snare[step]);
                        kStr += getTabChar('kick', p.kick[step]);
                        t1Str += getTabChar('tom1', p.tom1[step]);
                        t2Str += getTabChar('tom2', p.tom2[step]);
                        t3Str += getTabChar('tom3', p.tom3[step]);
                        t4Str += '-';
                    }
                    hStr += '|'; sStr += '|'; kStr += '|'; t1Str += '|'; t2Str += '|'; t3Str += '|'; t4Str += '|';
                });
                
                const gu = new GrooveUtils();
                const data = new gu.grooveDataNew();
                data.timeDivision = 16; data.tempo = song.bpm; data.numberOfMeasures = chunk.bars.length;
                data.numBeats = beatsPerBar(); data.noteValue = 4;
                data.notesPerMeasure = data.timeDivision * data.numBeats / data.noteValue;
                data.hh_array = gu.noteArraysFromURLData('H', hStr, data.notesPerMeasure, data.numberOfMeasures);
                data.snare_array = gu.noteArraysFromURLData('S', sStr, data.notesPerMeasure, data.numberOfMeasures);
                data.kick_array = gu.noteArraysFromURLData('K', kStr, data.notesPerMeasure, data.numberOfMeasures);
                data.toms_array = [
                    gu.noteArraysFromURLData('T1', t1Str, data.notesPerMeasure, data.numberOfMeasures),
                    gu.noteArraysFromURLData('T2', t2Str, data.notesPerMeasure, data.numberOfMeasures),
                    gu.noteArraysFromURLData('T3', t3Str, data.notesPerMeasure, data.numberOfMeasures),
                    gu.noteArraysFromURLData('T4', t4Str, data.notesPerMeasure, data.numberOfMeasures)
                ];
                
                const svgContent = gu.renderABCtoSVG(gu.createABCFromGrooveData(data, 800)).svg;
                const overlayTargets = chunk.bars.map((_, i) => {
                    const globalBarIndex = chunk.startBarIndex + i;
                    return `<div class="measure-click-target" data-block="${chunk.blockId}" data-bar="${globalBarIndex}" title="Такт ${globalBarIndex + 1}" style="flex: 1; cursor: pointer;"></div>`;
                }).join('');

                html += `
                    <div class="notation-chunk" data-chunk-index="${chunkIndex}" style="position: relative;">
                        <div class="notation-chunk-content" style="position: relative; z-index: 1;">
                            ${svgContent}
                        </div>
                        <div class="notation-click-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; z-index: 20;">
                            ${overlayTargets}
                        </div>
                    </div>`;
            } catch (e) {
                html += `<div class="notation-chunk"><div class="notation-chunk-content" style="color: #fc8181; font-size: 12px; padding: 20px;">Ошибка рендера: ${e.message}</div></div>`;
            }
        });
        content.innerHTML = `<div class="full-notation-wrapper">${html}</div>`;
        attachNotationClickHandlers(content);
    }

    function attachNotationClickHandlers(content) {
        content.querySelectorAll('.measure-click-target').forEach(target => {
            target.addEventListener('click', (e) => {
                e.stopPropagation();
                const blockId = target.dataset.block;
                const barIndex = parseInt(target.dataset.bar, 10);
                const panel = document.getElementById('fullNotationPanel');

                if (document.fullscreenElement === panel) {
                    document.exitFullscreen().then(() => {
                        setTimeout(() => { selectBar(blockId, barIndex); }, 50);
                    }).catch(err => {
                        console.error('Ошибка при выходе из полноэкранного режима:', err);
                        selectBar(blockId, barIndex);
                    });
                } else {
                    selectBar(blockId, barIndex);
                }
            });
        });
    }

    function renderStats() {
        const totalBars = song.blocks.reduce((s, b) => s + b.length, 0);
        document.getElementById('statBars').textContent = totalBars;
        document.getElementById('statPhrases').textContent = song.blocks.reduce((s, b) => s + phraseCount(b.length), 0);
        document.getElementById('statTime').textContent = formatTime(totalBars * barDurationSec());
        document.getElementById('statSections').textContent = song.blocks.length;
    }

    async function exportToPDF() {
        const wrapper = document.querySelector('.full-notation-wrapper');
        if (!wrapper || wrapper.children.length === 0) {
            alert('Нет нот для экспорта. Сначала откройте «Полная нотация».');
            return;
        }

        const s = songsMeta.songs.find(x => x.id === songsMeta.activeSongId);
        const safeName = (s ? s.name : 'song').replace(/[^a-z0-9а-яё]/gi, '_');
        const fileName = `${safeName}.pdf`;

        const btn = document.getElementById('downloadPdfBtn');
        const originalText = btn.textContent;
        btn.textContent = '⏳';
        btn.disabled = true;

        const loadingMsg = document.createElement('div');
        loadingMsg.style.cssText = 'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:rgba(0,0,0,0.85);color:white;padding:20px 30px;border-radius:8px;z-index:9999;font-size:16px;text-align:center;';
        loadingMsg.innerHTML = 'Генерация PDF, пожалуйста, подождите...<br><span style="font-size:12px;color:#aaa;">(Для очень длинных песен это может занять 10-20 сек)</span>';
        document.body.appendChild(loadingMsg);

        const originalBg = wrapper.style.backgroundColor;
        wrapper.style.backgroundColor = '#ffffff';

        try {
            const wrapperRect = wrapper.getBoundingClientRect();
            const wrapperHeight = wrapperRect.height;
            const pageBreaksDom = [];
            document.querySelectorAll('.section-divider[data-pagebreak="true"]').forEach(div => {
                const rect = div.getBoundingClientRect();
                const domY = rect.top - wrapperRect.top;
                if (domY > 0) pageBreaksDom.push(domY);
            });

            const canvas = await html2canvas(wrapper, {
                scale: 1.5, backgroundColor: '#ffffff', useCORS: true, logging: false, allowTaint: true
            });

            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');
            const pdfWidth = 210;
            const pdfHeight = 297;
            const imgHeight = (canvas.height * pdfWidth) / canvas.width;
            const pageBreaksMm = pageBreaksDom.map(y => (y / wrapperHeight) * imgHeight).sort((a, b) => a - b);

            let currentY = 0;
            let isFirstPage = true;

            while (currentY < imgHeight - 0.5) {
                if (!isFirstPage) pdf.addPage();
                isFirstPage = false;

                let pageEndY = Math.min(currentY + pdfHeight, imgHeight);
                const breakInPage = pageBreaksMm.find(b => b > currentY + 1 && b < pageEndY - 1);
                if (breakInPage !== undefined) pageEndY = breakInPage;

                const sliceHeightMm = pageEndY - currentY;
                if (sliceHeightMm < 5 && pageEndY < imgHeight) {
                    currentY = pageEndY;
                    continue;
                }

                const sliceTopPx = (currentY / imgHeight) * canvas.height;
                const sliceBottomPx = (pageEndY / imgHeight) * canvas.height;
                const sliceHeightPx = sliceBottomPx - sliceTopPx;

                const sliceCanvas = document.createElement('canvas');
                sliceCanvas.width = canvas.width;
                sliceCanvas.height = Math.max(1, Math.round(sliceHeightPx));
                const ctx = sliceCanvas.getContext('2d');
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, sliceCanvas.width, sliceCanvas.height);
                ctx.drawImage(canvas, 0, Math.round(sliceTopPx), canvas.width, Math.round(sliceHeightPx), 0, 0, sliceCanvas.width, sliceCanvas.height);

                const sliceData = sliceCanvas.toDataURL('image/png');
                pdf.addImage(sliceData, 'PNG', 0, 0, pdfWidth, sliceHeightMm);
                currentY = pageEndY;
            }
            pdf.save(fileName);
        } catch (err) {
            console.error('PDF export error:', err);
            alert('Ошибка при создании PDF. Попробуйте использовать кнопку "🖨 Печать".\nДетали: ' + err.message);
        } finally {
            wrapper.style.backgroundColor = originalBg;
            btn.textContent = originalText;
            btn.disabled = false;
            if (document.body.contains(loadingMsg)) document.body.removeChild(loadingMsg);
        }
    }

    function render() {
        if (window.HeaderUI) { window.HeaderUI.setBpm(song.bpm); window.HeaderUI.setTimeSig(song.timeSig); }
        renderTimeline(); renderMiniTimeline(); renderSequencer(); updateNotation(); renderStats();
    }

    // ==========================================
    // СОБЫТИЯ
    // ==========================================
    window.addEventListener('app:bpm-change', (e) => { song.bpm = e.detail.bpm; render(); updateFullNotation(); });
    window.addEventListener('app:timesig-change', (e) => { song.timeSig = e.detail.timeSig; render(); updateFullNotation(); });

    window.addEventListener('app:reset-click', () => {
        const s = songsMeta.songs.find(x => x.id === songsMeta.activeSongId);
        if (confirm(`Очистить песню «${s ? s.name : ''}»? Все секции будут удалены.`)) {
            song = { bpm: 120, timeSig: '4/4', blocks: [] };
            selectedBar = null;
            document.getElementById('bottomPanel').classList.add('collapsed');
            document.getElementById('bottomPanel').classList.remove('expanded');
            render();
        }
    });

    window.addEventListener('app:full-notation-click', () => {
        document.getElementById('bottomPanel').classList.add('collapsed');
        document.getElementById('bottomPanel').classList.remove('expanded');
        selectedBar = null;
        const panel = document.getElementById('fullNotationPanel');
        panel.classList.remove('collapsed'); panel.classList.add('expanded');
        updateFullNotation();
    });

    window.addEventListener('app:export', (e) => {
        const format = e.detail.format;
        const s = songsMeta.songs.find(x => x.id === songsMeta.activeSongId);
        const name = s ? s.name : 'song';
        if (format === 'json') window.IOManager.exportJSON(song, name);
        else if (format === 'midi') window.IOManager.exportMIDI(song, name);
        else if (format === 'pdf') {
            const totalBars = song.blocks.reduce((sum, b) => sum + b.length, 0);
            if (totalBars === 0) { alert('Нет тактов для экспорта'); return; }
            document.getElementById('bottomPanel').classList.add('collapsed');
            document.getElementById('bottomPanel').classList.remove('expanded');
            selectedBar = null;
            const panel = document.getElementById('fullNotationPanel');
            panel.classList.remove('collapsed'); panel.classList.add('expanded');
            updateFullNotation();
            setTimeout(() => { exportToPDF(); }, 500);
        }
    });

    window.addEventListener('app:import', async (e) => {
        const file = e.detail.file;
        try {
            let parsedData;
            if (file.name.endsWith('.mid') || file.name.endsWith('.midi')) {
                parsedData = await window.IOManager.parseMIDI(file);
            } else {
                parsedData = await window.IOManager.parseJSON(file);
            }
            parsedData = migrateSong(parsedData);
            const result = await window.StorageManager.importSong(parsedData, file.name);
            songsMeta = result.meta;
            song = migrateSong(result.song);
            selectedBar = null;
            document.getElementById('bottomPanel').classList.add('collapsed');
            document.getElementById('bottomPanel').classList.remove('expanded');
            updateCurrentSongName();
            render();
            if (window.SongsModal) window.SongsModal.open();
            alert(`Песня «${result.songName}» успешно загружена!`);
        } catch (err) {
            alert('Ошибка при чтении файла: ' + err.message);
        }
    });

    window.addEventListener('app:timeline-zoom', (e) => {
        timelineCompact = e.detail.compact;
        renderTimeline();
    });

    document.getElementById('closeBottomBtn').addEventListener('click', () => {
        document.getElementById('bottomPanel').classList.add('collapsed');
        document.getElementById('bottomPanel').classList.remove('expanded');
        selectedBar = null; render();
    });
    document.getElementById('seqModeToggle').addEventListener('click', () => {
        sequencerMode = sequencerMode === 'compact' ? 'full' : 'compact';
        renderSequencer();
    });
    document.getElementById('closeFullNotation').addEventListener('click', () => {
        const panel = document.getElementById('fullNotationPanel');
        if (document.fullscreenElement === panel) {
            document.exitFullscreen().then(() => {
                panel.classList.add('collapsed');
                panel.classList.remove('expanded');
            }).catch(err => {
                console.error('Ошибка при выходе из полноэкранного режима:', err);
                panel.classList.add('collapsed');
                panel.classList.remove('expanded');
            });
        } else {
            panel.classList.add('collapsed');
            panel.classList.remove('expanded');
        }
    });
    document.getElementById('fullscreenNotationBtn').addEventListener('click', () => {
        const panel = document.getElementById('fullNotationPanel');
        if (!document.fullscreenElement) panel.requestFullscreen().catch(err => console.error(err));
        else document.exitFullscreen();
    });
    document.getElementById('printNotationBtn').addEventListener('click', () => { window.print(); });
    document.getElementById('downloadPdfBtn').addEventListener('click', () => { exportToPDF(); });

    document.addEventListener('keydown', (e) => {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
        if (!selectedBar) return;
        if (e.key === 'ArrowLeft') { e.preventDefault(); navigateBar(-1); }
        if (e.key === 'ArrowRight') { e.preventDefault(); navigateBar(1); }
        if (e.key === 'Delete' || e.key === 'Backspace') {
            e.preventDefault();
            const pattern = getBarPattern(selectedBar.blockId, selectedBar.barIndex);
            TRACKS.forEach(t => pattern[t.id].fill(0));
            renderSequencer(); updateNotation(); updateBarInTimeline(); updateFullNotation();
            // 🔥 УБРАНО: saveCurrentSong();
        }
    });

    document.addEventListener('click', () => {
        document.getElementById('contextMenu').style.display = 'none';
    });

    document.querySelectorAll('.context-menu-item').forEach(item => {
        item.addEventListener('click', (e) => {
            const action = e.target.dataset.action;
            const menu = document.getElementById('contextMenu');
            const blockId = menu.dataset.blockId;
            const barIndex = parseInt(menu.dataset.barIndex);

            if (action === 'copy') {
                clipboardPattern = JSON.parse(JSON.stringify(getBarPattern(blockId, barIndex)));
            } else if (action === 'paste' && clipboardPattern) {
                const targetPattern = getBarPattern(blockId, barIndex);
                TRACKS.forEach(t => { targetPattern[t.id] = [...clipboardPattern[t.id]]; });
                renderTimeline();
                if (selectedBar && selectedBar.blockId === blockId && selectedBar.barIndex === barIndex) {
                    renderSequencer(); updateNotation(); updateFullNotation();
                }
                // 🔥 УБРАНО: saveCurrentSong();
            }
            menu.style.display = 'none';
        });
    });

    window.addEventListener('mouseup', () => { isPainting = false; });

    // 🔥 ДОБАВЛЕНО: Обработчик явного сохранения по кнопке в хедере
    window.addEventListener('app:save-click', async () => {
        if (!songsMeta.activeSongId) {
            alert('Нет активной песни для сохранения');
            return;
        }
        
        const saveBtn = document.getElementById('saveBtn');
        const originalText = saveBtn.textContent;
        saveBtn.textContent = '⏳ Сохранение...';
        saveBtn.disabled = true;
        
        try {
            await window.StorageManager.saveSong(songsMeta.activeSongId, song, songsMeta);
            saveBtn.textContent = '✅ Сохранено!';
            setTimeout(() => {
                saveBtn.textContent = originalText;
                saveBtn.disabled = false;
            }, 1500);
        } catch (e) {
            console.error('Ошибка сохранения:', e);
            saveBtn.textContent = '❌ Ошибка';
            setTimeout(() => {
                saveBtn.textContent = originalText;
                saveBtn.disabled = false;
            }, 2000);
        }
    });

    window.addEventListener('vk-auth-state-change', () => {
        console.log("Auth state changed, reloading app...");
        window.location.reload();
    });

    // ==========================================
    // SORTABLE JS ИНТЕГРАЦИЯ
    // ==========================================
    function initSortable() {
        if (typeof Sortable === 'undefined') {
            console.error('SortableJS НЕ ЗАГРУЖЕН.');
            return;
        }
        const paletteEl = document.getElementById('palette');
        const timelineEl = document.getElementById('timeline');

        if (window.paletteSortable) window.paletteSortable.destroy();
        if (window.timelineSortable) window.timelineSortable.destroy();

        window.paletteSortable = Sortable.create(paletteEl, { group: { name: 'blocks', pull: 'clone', put: false }, sort: false, animation: 150 });
        window.timelineSortable = Sortable.create(timelineEl, {
            group: 'blocks', animation: 150, ghostClass: 'dragging',
            onAdd: function (evt) {
                const type = evt.item.getAttribute('data-type');
                if (!type) { evt.item.remove(); return; }
                song.blocks.splice(evt.newIndex, 0, { id: Math.random().toString(36).slice(2, 10), type, label: nextLabel(type), length: BARS_PER_PHRASE, patterns: {}, collapsed: false, pageBreak: false, breakAfter: false });
                evt.item.remove(); render();
            },
            onUpdate: function (evt) {
                const [moved] = song.blocks.splice(evt.oldIndex, 1);
                song.blocks.splice(evt.newIndex, 0, moved);
                evt.item.remove(); render();
            }
        });
    }

    // ==========================================
    // СТАРТ (Асинхронная инициализация)
    // ==========================================
    async function startApp() {
        if (window.StorageManager) {
            const init = await window.StorageManager.init();
            songsMeta = init.meta;
            song = migrateSong(init.song);
        }
        
        renderPalette();
        initSortable();
        initSequencerEvents();

        if (window.SongsModal) {
            window.SongsModal.init({
                getSongsMeta: () => songsMeta,
                setSongsMeta: (m) => { songsMeta = m; },
                getSong: () => song,
                setSong: (s) => { song = s; },
                onSongSwitched: () => {
                    selectedBar = null;
                    document.getElementById('bottomPanel').classList.add('collapsed');
                    document.getElementById('bottomPanel').classList.remove('expanded');
                    updateCurrentSongName();
                    render();
                }
            });
        }

        render();
    }

    startApp();
})();