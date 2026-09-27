/**
 * io_manager.js
 * Импорт и экспорт данных (JSON, MIDI).
 * (Обновлено: добавлен hihat_open с MIDI-нотой 46)
 */
(function() {
    const TRACKS_EXPORT = [
        { id: 'crash', note: 49 }, { id: 'ride', note: 51 },
        { id: 'hihat', note: 42 }, { id: 'hihat_open', note: 46 },
        { id: 'snare', note: 38 }, { id: 'kick', note: 36 },
        { id: 'tom1', note: 48 }, { id: 'tom2', note: 45 }, { id: 'tom3', note: 41 }
    ];
    const STEPS = 16;

    function getSafeFileName(name) {
        return (name || 'song').replace(/[^a-z0-9а-яё]/gi, '_');
    }

    function downloadBlob(blob, filename) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url; a.download = filename;
        document.body.appendChild(a); a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    function emptyBarPattern() {
        const p = {};
        TRACKS_EXPORT.forEach(t => p[t.id] = new Array(STEPS).fill(0));
        return p;
    }

    window.IOManager = {
        exportJSON: function(song, songName) {
            const exportData = { ...song, songName: songName || 'Без названия', exportDate: new Date().toISOString() };
            downloadBlob(new Blob([JSON.stringify(exportData, null, 2)], { type: 'application/json' }), `${getSafeFileName(songName)}.json`);
        },

        parseJSON: function(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    try {
                        const data = JSON.parse(e.target.result);
                        if (!data.blocks || !Array.isArray(data.blocks)) throw new Error('Неверный формат: отсутствуют блоки.');
                        resolve(data);
                    } catch (err) { reject(err); }
                };
                reader.onerror = () => reject(new Error('Ошибка чтения файла'));
                reader.readAsText(file);
            });
        },

        parseMIDI: function(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    try {
                        const buffer = e.target.result;
                        const view = new DataView(buffer);
                        let offset = 0;

                        const readString = (len) => { let str = ''; for (let i = 0; i < len; i++) str += String.fromCharCode(view.getUint8(offset++)); return str; };
                        const readVLQ = () => {
                            let value = 0, byte;
                            do { byte = view.getUint8(offset++); value = (value << 7) | (byte & 0x7F); } while (byte & 0x80);
                            return value;
                        };

                        if (readString(4) !== 'MThd') throw new Error('Неверный формат MIDI');
                        offset += 4;
                        const format = view.getUint16(offset); offset += 2;
                        const numTracks = view.getUint16(offset); offset += 2;
                        const ticksPerQuarter = view.getUint16(offset); offset += 2;

                        const STEP_TICKS = Math.max(1, Math.round(ticksPerQuarter / 4));
                        const bars = {};
                        const DRUM_MAP = {
                            36: 'kick', 38: 'snare',
                            42: 'hihat', 46: 'hihat_open',
                            48: 'tom1', 45: 'tom2', 41: 'tom3',
                            49: 'crash', 51: 'ride'
                        };

                        for (let t = 0; t < numTracks; t++) {
                            if (readString(4) !== 'MTrk') throw new Error('Неверный трек');
                            const trackLength = view.getUint32(offset); offset += 4;
                            const trackEnd = offset + trackLength;
                            let currentTime = 0, runningStatus = 0;

                            while (offset < trackEnd) {
                                const delta = readVLQ();
                                currentTime += delta;
                                let status = view.getUint8(offset);
                                if (status < 0x80) { status = runningStatus; } else { runningStatus = status; offset++; }

                                const eventType = status & 0xF0;
                                const channel = status & 0x0F;

                                if (channel === 9 && (eventType === 0x90 || eventType === 0x80)) {
                                    const note = view.getUint8(offset++);
                                    const velocity = view.getUint8(offset++);
                                    if (eventType === 0x90 && velocity > 0) {
                                        const trackId = DRUM_MAP[note];
                                        if (trackId) {
                                            const barIndex = Math.floor(currentTime / (16 * STEP_TICKS));
                                            const step = Math.round((currentTime % (16 * STEP_TICKS)) / STEP_TICKS);
                                            const safeStep = Math.max(0, Math.min(15, step));
                                            if (!bars[barIndex]) {
                                                bars[barIndex] = {};
                                                TRACKS_EXPORT.forEach(tr => bars[barIndex][tr.id] = new Array(16).fill(0));
                                            }
                                            const isAccent = (trackId === 'hihat' || trackId === 'snare') && velocity > 110;
                                            bars[barIndex][trackId][safeStep] = isAccent ? 2 : 1;
                                        }
                                    }
                                } else if (eventType === 0xFF) {
                                    const metaType = view.getUint8(offset++);
                                    const metaLen = readVLQ();
                                    offset += metaLen;
                                    if (metaType === 0x2F) break;
                                } else if (eventType === 0xC0 || eventType === 0xD0) { offset += 1; } else { offset += 2; }
                            }
                            offset = Math.max(offset, trackEnd);
                        }

                        const newBlocks = [];
                        const barIndices = Object.keys(bars).map(Number).sort((a, b) => a - b);
                        if (barIndices.length === 0) throw new Error('В MIDI-файле не найдено нот на 10-м канале (ударные).');

                        for (let i = 0; i < barIndices.length; i += 4) {
                            const chunk = barIndices.slice(i, i + 4);
                            const newBlock = { id: 'temp', type: 'verse', label: `Импорт ${Math.floor(i/4) + 1}`, length: chunk.length, patterns: {}, collapsed: false, pageBreak: false, breakAfter: false };
                            chunk.forEach((barIdx, localIdx) => { newBlock.patterns[localIdx] = bars[barIdx]; });
                            newBlocks.push(newBlock);
                        }
                        resolve({ bpm: 120, timeSig: '4/4', blocks: newBlocks });
                    } catch (err) { reject(err); }
                };
                reader.onerror = () => reject(new Error('Ошибка чтения файла'));
                reader.readAsArrayBuffer(file);
            });
        },

        exportMIDI: function(song, songName) {
            const TICKS_PER_QUARTER = 24, STEP_TICKS = 6;
            const NOTE_VELOCITY = 100;
            const ACCENT_VELOCITY = 120;

            const allPatterns = [];
            song.blocks.forEach(block => {
                for (let i = 0; i < block.length; i++) {
                    allPatterns.push((block.patterns && block.patterns[i]) ? block.patterns[i] : emptyBarPattern());
                }
            });
            if (allPatterns.length === 0) { alert('Нет тактов для экспорта'); return; }

            const events = [];
            allPatterns.forEach((pattern, barIndex) => {
                const barStartTick = barIndex * 16 * STEP_TICKS;
                TRACKS_EXPORT.forEach(track => {
                    if (!pattern[track.id]) return; // защита от старых данных
                    for (let step = 0; step < 16; step++) {
                        const val = pattern[track.id][step];
                        if (val > 0) {
                            const noteStartTick = barStartTick + step * STEP_TICKS;
                            const velocity = val === 2 ? ACCENT_VELOCITY : NOTE_VELOCITY;
                            events.push({ time: noteStartTick, type: 'on', note: track.note, velocity: velocity });
                            events.push({ time: noteStartTick + STEP_TICKS - 1, type: 'off', note: track.note, velocity: 0 });
                        }
                    }
                });
            });
            events.sort((a, b) => a.time !== b.time ? a.time - b.time : (a.type === 'off' ? -1 : 1));

            const trackData = [];
            const microsecondsPerBeat = Math.round(60000000 / song.bpm);
            trackData.push(0x00, 0xFF, 0x51, 0x03, (microsecondsPerBeat >> 16) & 0xFF, (microsecondsPerBeat >> 8) & 0xFF, microsecondsPerBeat & 0xFF);
            const [numBeats, beatUnit] = song.timeSig.split('/').map(Number);
            trackData.push(0x00, 0xFF, 0x58, 0x04, numBeats, Math.round(Math.log2(beatUnit)), 24, 8);

            let lastTime = 0; const channel = 9;
            const encodeVLQ = (value, arr) => {
                if (value === 0) { arr.push(0); return; }
                const bytes = [];
                while (value > 0) { bytes.unshift(value & 0x7F); value >>= 7; }
                for (let i = 0; i < bytes.length - 1; i++) bytes[i] |= 0x80;
                bytes.forEach(b => arr.push(b));
            };

            events.forEach(ev => {
                encodeVLQ(ev.time - lastTime, trackData); lastTime = ev.time;
                trackData.push((ev.type === 'on' ? 0x90 : 0x80) | channel, ev.note, ev.velocity);
            });
            encodeVLQ(0, trackData); trackData.push(0xFF, 0x2F, 0x00);

            const header = [0x4D, 0x54, 0x68, 0x64, 0x00, 0x00, 0x00, 0x06, 0x00, 0x00, 0x00, 0x01, (TICKS_PER_QUARTER >> 8) & 0xFF, TICKS_PER_QUARTER & 0xFF];
            const trackHeader = [0x4D, 0x54, 0x72, 0x6B, (trackData.length >> 24) & 0xFF, (trackData.length >> 16) & 0xFF, (trackData.length >> 8) & 0xFF, trackData.length & 0xFF];
            downloadBlob(new Blob([new Uint8Array([...header, ...trackHeader, ...trackData])], { type: 'audio/midi' }), `${getSafeFileName(songName)}.mid`);
        }
    };
})();