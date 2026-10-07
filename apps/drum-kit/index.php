<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ударная установка — Drumz</title>
<link rel="stylesheet" href="/assets/style.css">
<style>
    /* =========================================
       УДАРНАЯ УСТАНОВКА — СВЕТЛАЯ ВЕРСИЯ
       ========================================= */
    .drum-app {
        padding: 20px 0;
    }
    
    .drum-app__header {
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border);
    }
    
    .drum-app__title {
        font-size: 15px;
        font-weight: 600;
        color: var(--text);
        margin: 0 0 4px;
    }
    
    .drum-app__subtitle {
        font-size: var(--font-size-small);
        color: var(--text-light);
        margin: 0;
    }

    .drum-kit-stage {
        position: relative; 
        width: 100%; 
        max-width: 800px; 
        aspect-ratio: 2.4/1; 
        margin: 0 auto;
        background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: var(--radius); 
        border: 1px solid var(--border); 
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .drum-part { 
        position: absolute; 
        cursor: pointer; 
        transition: transform 0.15s ease-out, filter 0.15s ease-out; 
        -webkit-tap-highlight-color: transparent; 
    }
    
    .drum-part:hover { 
        transform: scale(1.05); 
        filter: brightness(1.08);
    }
    
    .drum-part:active {
        transform: scale(0.95); 
        filter: brightness(0.95);
    }

    .drum-head-wrapper { position: relative; width: 100%; padding-bottom: 100%; }
    
    .cymbal-head {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        border-radius: 50%;
        background: 
            radial-gradient(circle at 35% 35%, rgba(255,255,255,0.7) 0%, transparent 25%),
            repeating-radial-gradient(circle at center, #fcd34d 0px, #d97706 2px, #fcd34d 4px);
        box-shadow: 
            0 4px 8px rgba(0,0,0,0.15), 
            inset 0 2px 4px rgba(255,255,255,0.6),
            inset 0 -2px 4px rgba(0,0,0,0.2);
    }
    .cymbal-head::after {
        content: ''; position: absolute; top: 50%; left: 50%; 
        transform: translate(-50%, -50%);
        width: 22%; height: 22%; border-radius: 50%;
        background: radial-gradient(circle at 30% 30%, #fde047, #b45309);
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.3), 0 2px 4px rgba(0,0,0,0.2);
    }

    .drum-head {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        border-radius: 50%;
        background: radial-gradient(circle at 50% 50%, #ffffff 0%, #f1f5f9 60%, #e2e8f0 100%);
        box-shadow: inset 0 0 10px rgba(0,0,0,0.08), 0 4px 8px rgba(0,0,0,0.1);
    }

    /* Хромированный обод для малого и всех томов */
    [data-part="snare-drum"] .drum-head,
    [data-part="tom1"] .drum-head,
    [data-part="tom2"] .drum-head,
    [data-part="floor-tom"] .drum-head {
        border: 6px solid #cbd5e1;
        outline: 3px solid #94a3b8;
        outline-offset: -9px;
    }

    /* Малый барабан - матовый белый пластик */
    [data-part="snare-drum"] .drum-head {
        background: radial-gradient(circle at 50% 50%, #ffffff 0%, #f8fafc 100%);
    }

    /* Тёмные головы для томов */
    [data-part="tom1"] .drum-head,
    [data-part="tom2"] .drum-head,
    [data-part="floor-tom"] .drum-head {
        background: radial-gradient(circle at 50% 50%, #64748b 0%, #475569 100%);
    }

    /* Бочка (Bass Drum) */
    [data-part="bass"] .drum-head {
        background: radial-gradient(circle at 50% 50%, #64748b 0%, #475569 100%);
        border: 8px solid #cbd5e1;
        outline: 4px solid #94a3b8;
        outline-offset: -12px;
    }
    [data-part="bass"] .drum-head::after {
        content: 'DRUMZ'; position: absolute; top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        font-size: 14px; font-weight: 900; color: rgba(255,255,255,0.2);
        letter-spacing: 2px;
    }

    [data-part="bass"]       { width: 26%; bottom: 8%;  left: calc(50% - 13%); z-index: 10; }
    [data-part="floor-tom"]  { width: 20%; right: 10%;  bottom: 10%; z-index: 20; }
    [data-part="tom1"]       { width: 15%; left: 33%;   top: 18%;    z-index: 20; }
    [data-part="tom2"]       { width: 15%; right: 33%;  top: 18%;    z-index: 20; }
    [data-part="ride"]       { width: 24%; right: 8%;   top: 8%;     z-index: 25; }
    [data-part="snare-drum"] { width: 16%; left: 24%;   bottom: 22%; z-index: 30; }
    [data-part="hihat"]      { width: 18%; left: 6%;    top: 40%;    z-index: 40; }
    [data-part="crash"]      { width: 22%; left: 8%;    top: 5%;     z-index: 45; }

    .drum-hit .drum-head { 
        animation: drum-vibrate 0.1s ease-out; 
        filter: brightness(1.15);
    }
    
    .cymbal-hit .cymbal-head { 
        animation: cymbal-sway 1.2s cubic-bezier(.36,0,.66,-.56) forwards; 
    }
    
    @keyframes drum-vibrate {
        0% { transform: scale(1); }
        40% { transform: scale(0.94) translateY(4px); }
        100% { transform: scale(1); }
    }
    
    @keyframes cymbal-sway {
        0% { transform: rotate(0) scale(1); filter: brightness(1.3); }
        15% { transform: rotate(8deg) scale(1.05); filter: brightness(1.15); }
        30% { transform: rotate(-6deg) scale(1.02); filter: brightness(1.05); }
        100% { transform: rotate(0) scale(1); filter: brightness(1); }
    }

    /* =========================================
       НОТНЫЙ СТАН
       ========================================= */
    .staff-stage {
        position: relative; 
        width: 100%; 
        max-width: 800px; 
        aspect-ratio: 2.4/1;
        margin: 20px auto 0;
        background: #ffffff;
        border-radius: var(--radius); 
        border: 1px solid var(--border); 
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .staff-lines {
        position: absolute;
        top: 50%;
        left: 10%;
        right: 10%;
        height: 40%;
        transform: translateY(-50%);
        background: repeating-linear-gradient(
            to bottom,
            #cbd5e1 0px,
            #cbd5e1 2px,
            transparent 2px,
            transparent calc(25% - 1px),
            #cbd5e1 calc(25% - 1px),
            #cbd5e1 25%
        );
    }

    .notes-container {
        position: absolute;
        top: 0;
        left: 10%;
        right: 10%;
        height: 100%;
        pointer-events: none;
    }

    .staff-note {
        position: absolute;
        width: 40px;
        height: 22px;
        border-radius: 50%;
        border-top: 2px solid #475569;
        border-bottom: 2px solid #475569;
        border-left: 8px solid #475569;
        border-right: 8px solid #475569;
        background: transparent;
        transform: translate(-50%, -50%) rotate(-25deg);
        animation: note-appear-oval 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    .staff-note.cymbal-note {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 0;
        background: transparent;
        transform: translate(-50%, -50%);
        animation: note-appear 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    .staff-note.cymbal-note::before {
        content: '';
        position: absolute;
        top: 50%; left: 50%;
        width: 6px; height: 100%;
        background: #475569;
        border-radius: 3px;
        transform: translate(-50%, -50%) rotate(45deg);
    }

    .staff-note.cymbal-note::after {
        content: '';
        position: absolute;
        top: 50%; left: 50%;
        width: 6px; height: 100%;
        background: #475569;
        border-radius: 3px;
        transform: translate(-50%, -50%) rotate(-45deg);
    }

    @keyframes note-appear {
        0% { transform: translate(-50%, -50%) scale(0); opacity: 0; }
        100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
    }
    
    @keyframes note-appear-oval {
        0% { transform: translate(-50%, -50%) rotate(-25deg) scale(0); opacity: 0; }
        100% { transform: translate(-50%, -50%) rotate(-25deg) scale(1); opacity: 1; }
    }

    .clear-btn {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 6px 12px;
        font-size: var(--font-size-small);
        font-weight: 600;
        background: var(--bg-panel);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        cursor: pointer;
        color: var(--text);
        transition: all 0.15s;
    }
    .clear-btn:hover { 
        background: var(--danger); 
        border-color: var(--danger);
        color: #fff;
    }
</style>
</head>
<body>

<?php require_once __DIR__ . '/../../includes/header.php'; ?>

<main class="container">
    <div class="drum-app">
        <div class="drum-app__header">
            <h1 class="drum-app__title">Ударная установка</h1>
            <p class="drum-app__subtitle">Нажимайте на элементы установки для воспроизведения звуков и записи нот</p>
        </div>

        <!-- УДАРНАЯ УСТАНОВКА -->
        <div class="drum-kit-stage">
            <div class="drum-part" data-part="crash"><div class="drum-head-wrapper"><div class="cymbal-head"></div></div></div>
            <div class="drum-part" data-part="tom1"><div class="drum-head-wrapper"><div class="drum-head"></div></div></div>
            <div class="drum-part" data-part="tom2"><div class="drum-head-wrapper"><div class="drum-head"></div></div></div>
            <div class="drum-part" data-part="hihat"><div class="drum-head-wrapper"><div class="cymbal-head"></div></div></div>
            <div class="drum-part" data-part="snare-drum"><div class="drum-head-wrapper"><div class="drum-head"></div></div></div>
            <div class="drum-part" data-part="ride"><div class="drum-head-wrapper"><div class="cymbal-head"></div></div></div>
            <div class="drum-part" data-part="bass"><div class="drum-head-wrapper"><div class="drum-head"></div></div></div>
            <div class="drum-part" data-part="floor-tom"><div class="drum-head-wrapper"><div class="drum-head"></div></div></div>
        </div>

        <!-- НОТНЫЙ СТАН -->
        <div class="staff-stage">
            <div class="staff-lines"></div>
            <div class="notes-container" id="notesContainer"></div>
            <button class="clear-btn" id="clearBtn">Очистить</button>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<script>
    const soundMap = {
        'crash': 'sound/Crash.mp3',
        'ride': 'sound/Ride.mp3',
        'hihat': 'sound/Hi Hat Normal.mp3',
        'tom1': 'sound/10 Tom.mp3',
        'tom2': 'sound/16 Tom.mp3',
        'snare-drum': 'sound/Snare Normal.mp3',
        'bass': 'sound/Kick.mp3',
        'floor-tom': 'sound/Floor Tom.mp3'
    };

    const notePositions = {
        'crash': 20,
        'ride': 30,
        'hihat': 25,
        'tom1': 35,
        'tom2': 40,
        'snare-drum': 45,
        'floor-tom': 55,
        'bass': 65
    };
    
    const notesContainer = document.getElementById('notesContainer');
    const clearBtn = document.getElementById('clearBtn');
    let noteCounter = 0;
    const noteStep = 55;
    
    function playDrumSound(part) {
        const soundFile = soundMap[part];
        if (!soundFile) return;
        
        const audio = new Audio(soundFile);
        audio.volume = 0.8;
        
        audio.play().catch(error => {
            console.warn("Не удалось воспроизвести звук:", error);
        });
    }

    document.querySelectorAll('.drum-part').forEach(el => {
        const triggerDrum = () => {
            const part = el.getAttribute('data-part');
            
            const isC = ['crash', 'ride', 'hihat'].includes(part);
            const ac = isC ? 'cymbal-hit' : 'drum-hit';
            el.classList.remove(ac); void el.offsetWidth; el.classList.add(ac);
            
            playDrumSound(part);
            addNote(part, isC);
        };

        el.addEventListener('mousedown', triggerDrum);
        el.addEventListener('touchstart', (e) => {
            e.preventDefault();
            triggerDrum();
        }, { passive: false });
    });
    
    function addNote(part, isCymbal) {
        const note = document.createElement('div');
        note.className = isCymbal ? 'staff-note cymbal-note' : 'staff-note';
        
        const yPos = notePositions[part];
        const xPos = 20 + (noteCounter * noteStep); 
        
        note.style.top = yPos + '%';
        note.style.left = xPos + 'px';
        
        notesContainer.appendChild(note);
        noteCounter++;
        
        if (xPos > 600) {
            notesContainer.style.transform = `translateX(-${xPos - 600}px)`;
            notesContainer.style.transition = 'transform 0.3s ease-out';
        }
    }
    
    clearBtn.addEventListener('click', () => {
        notesContainer.innerHTML = '';
        notesContainer.style.transform = 'translateX(0)';
        noteCounter = 0;
    });
</script>

</body>
</html>