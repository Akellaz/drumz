/**
 * header_ui.js
 * Автономный модуль верхней панели управления.
 */
(function() {
    const styles = `
        header {
            padding: 10px 20px; background: #24242c; border-bottom: 1px solid #333;
            display: flex; align-items: center; gap: 16px; flex-wrap: wrap; flex-shrink: 0;
        }
        header h1 { font-size: 16px; font-weight: 600; color: #e6e6e6; margin: 0; }
        .current-song-name {
            font-size: 13px; color: #4a7bd1; font-weight: 600; padding: 3px 10px;
            background: #1a1a1f; border-radius: 4px; border: 1px solid #333;
            max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; cursor: pointer;
        }
        .current-song-name:hover { border-color: #4a7bd1; }
        .settings { display: flex; gap: 12px; align-items: center; font-size: 12px; color: #e6e6e6; }
        .settings label { display: flex; gap: 5px; align-items: center; }
        .settings input, .settings select {
            background: #1a1a1f; color: #e6e6e6; border: 1px solid #444;
            border-radius: 4px; padding: 3px 6px; font-size: 12px; width: 60px;
        }
        .actions { margin-left: auto; display: flex; gap: 12px; align-items: center; }
        .header-btn {
            background: #3a3a45; color: #e6e6e6; border: 1px solid #4a4a55;
            border-radius: 4px; padding: 5px 10px; cursor: pointer; font-size: 12px;
            white-space: nowrap;
        }
        .header-btn:hover { background: #4a4a55; }
        .header-btn.danger { background: #a04040; border-color: #b05050; }
        .header-btn.danger:hover { background: #b05050; }
        .header-btn.warning { background: #d19a4a; border-color: #e1aa5a; }
        .header-btn.warning:hover { background: #e1aa5a; }
        .header-btn.primary { background: #4a7bd1; border-color: #5a8be1; }
        .header-btn.primary:hover { background: #5a8be1; }
        
        .export-dropdown { position: relative; display: inline-block; }
        .export-dropdown .dropdown-menu {
            display: none; position: absolute; top: 100%; right: 0; margin-top: 4px;
            background: #2a2a34; border: 1px solid #4a4a55; border-radius: 6px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.5); min-width: 180px; z-index: 1000; overflow: hidden;
        }
        .export-dropdown.open .dropdown-menu { display: block; }
        .dropdown-menu button {
            display: block; width: 100%; text-align: left; background: transparent; border: none;
            color: #e6e6e6; padding: 10px 14px; font-size: 13px; cursor: pointer; border-bottom: 1px solid #333;
        }
        .dropdown-menu button:last-child { border-bottom: none; }
        .dropdown-menu button:hover { background: #4a7bd1; }
        .dropdown-menu button .hint { display: block; font-size: 10px; color: #888; margin-top: 2px; font-weight: normal; }
        .dropdown-menu button:hover .hint { color: #d0d0e0; }

        .auth-container {
            display: flex; align-items: center; gap: 8px;
            margin-left: 12px; padding-left: 12px; border-left: 1px solid #333;
        }
        .auth-loading { font-size: 11px; color: #888; }
        .btn-login-auth {
            display: flex; align-items: center; gap: 6px; background: transparent; border: 1px solid transparent;
            color: #aaa; font-size: 12px; padding: 4px 8px; border-radius: 4px; cursor: pointer; transition: all 0.15s;
        }
        .btn-login-auth:hover { color: #4a7bd1; background: rgba(74, 123, 209, 0.08); border-color: rgba(74, 123, 209, 0.2); }
        .btn-login-auth svg { width: 14px; height: 14px; }
        .user-profile-auth { display: flex; align-items: center; gap: 6px; position: relative; }
        .user-avatar-auth {
            width: 24px; height: 24px; border-radius: 50%; object-fit: cover;
            opacity: 0.8; transition: opacity 0.15s, box-shadow 0.15s; cursor: default;
        }
        .user-profile-auth:hover .user-avatar-auth { opacity: 1; box-shadow: 0 0 0 2px rgba(74, 123, 209, 0.4); }
        .user-name-auth { display: none; }
        .btn-logout-auth {
            width: 16px; height: 16px; display: flex; align-items: center; justify-content: center;
            background: transparent; border: none; color: #666; font-size: 14px; line-height: 1;
            cursor: pointer; opacity: 0; transition: opacity 0.15s, color 0.15s;
        }
        .user-profile-auth:hover .btn-logout-auth { opacity: 1; }
        .btn-logout-auth:hover { color: #fc8181; }
    `;
    const styleEl = document.createElement('style');
    styleEl.textContent = styles;
    document.head.appendChild(styleEl);

    const headerHTML = `
        <header>
            <h1>Drumz Builder</h1>
            <div class="current-song-name" id="currentSongName" title="Открыть список песен">Мои песни</div>
            <div class="settings">
                <label>BPM: <input type="number" id="bpm" value="120" min="30" max="300"></label>
                <label>Размер:
                    <select id="timeSig">
                        <option value="4/4">4/4</option>
                        <option value="3/4">3/4</option>
                        <option value="6/8">6/8</option>
                        <option value="2/4">2/4</option>
                    </select>
                </label>
            </div>
            <div class="actions">
                <button id="timelineZoomBtn" class="header-btn" title="Переключить масштаб тактов">🔍 Масштаб: 100%</button>
                <div class="export-dropdown" id="exportDropdown">
                    <button id="exportBtn" class="header-btn">💾 Экспорт ▾</button>
                    <div class="dropdown-menu">
                        <button data-format="json">💾 JSON <span class="hint">Проект для редактирования</span></button>
                        <button data-format="midi">🎵 MIDI <span class="hint">Для DAW (Ableton, FL Studio)</span></button>
                        <button data-format="pdf">📄 PDF <span class="hint">Нотация для печати</span></button>
                    </div>
                </div>
                <button id="importBtn" class="header-btn" title="Загрузить песню (JSON или MIDI)">📂 Импорт</button>
                <button id="fullNotationBtn" class="header-btn primary">🎼 Полная нотация</button>
                <button id="saveBtn" class="header-btn primary" title="Сохранить изменения в облако" style="display: none;">💾 Сохранить</button>
                
                <div id="authContainer" class="auth-container">
                    <span class="auth-loading">Загрузка...</span>
                </div>
            </div>
            <input type="file" id="importInput" accept=".json, .mid, .midi" style="display: none;">
        </header>
    `;
    document.body.insertAdjacentHTML('afterbegin', headerHTML);

    const bpmInput = document.getElementById('bpm');
    const timeSigSelect = document.getElementById('timeSig');
    const fullNotationBtn = document.getElementById('fullNotationBtn');
    const saveBtn = document.getElementById('saveBtn');
    const currentSongName = document.getElementById('currentSongName');
    const timelineZoomBtn = document.getElementById('timelineZoomBtn');
    
    const exportDropdown = document.getElementById('exportDropdown');
    const exportBtn = document.getElementById('exportBtn');
    const importBtn = document.getElementById('importBtn');
    const importInput = document.getElementById('importInput');

    bpmInput.addEventListener('input', (e) => {
        const val = Math.max(30, Math.min(300, parseInt(e.target.value) || 120));
        window.dispatchEvent(new CustomEvent('app:bpm-change', { detail: { bpm: val } }));
    });

    timeSigSelect.addEventListener('change', (e) => {
        window.dispatchEvent(new CustomEvent('app:timesig-change', { detail: { timeSig: e.target.value } }));
    });

    fullNotationBtn.addEventListener('click', () => window.dispatchEvent(new CustomEvent('app:full-notation-click')));
    
    currentSongName.addEventListener('click', () => {
        window.dispatchEvent(new CustomEvent('app:songs-click'));
    });

    let isTimelineCompact = false;
    timelineZoomBtn.addEventListener('click', () => {
        isTimelineCompact = !isTimelineCompact;
        timelineZoomBtn.textContent = isTimelineCompact ? '🔍 Масштаб: 50%' : '🔍 Масштаб: 100%';
        window.dispatchEvent(new CustomEvent('app:timeline-zoom', { detail: { compact: isTimelineCompact } }));
    });

    exportBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        exportDropdown.classList.toggle('open');
    });
    
    document.addEventListener('click', () => {
        exportDropdown.classList.remove('open');
    });
    
    exportDropdown.querySelectorAll('.dropdown-menu button').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const format = btn.dataset.format;
            exportDropdown.classList.remove('open');
            window.dispatchEvent(new CustomEvent('app:export', { detail: { format } }));
        });
    });

    importBtn.addEventListener('click', () => {
        importInput.click();
    });

    importInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            window.dispatchEvent(new CustomEvent('app:import', { detail: { file: e.target.files[0] } }));
            e.target.value = '';
        }
    });

    saveBtn.addEventListener('click', () => {
        window.dispatchEvent(new CustomEvent('app:save-click'));
    });

    window.HeaderUI = {
        updateSongName: (name) => {
            currentSongName.textContent = name || 'Мои песни';
        },
        setBpm: (bpm) => { bpmInput.value = bpm; },
        setTimeSig: (timeSig) => { timeSigSelect.value = timeSig; },
        showSaveButton: () => { saveBtn.style.display = 'block'; },
        hideSaveButton: () => { saveBtn.style.display = 'none'; }
    };
})();