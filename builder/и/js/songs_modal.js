/**
 * songs_modal.js
 * Модальное окно управления списком песен.
 * API: window.SongsModal.init(deps) / .open() / .close() / .refresh()
 * 
 * ОБНОВЛЕНО: Добавлена поддержка async/await для работы с облачным StorageManager.
 */
(function() {
    let getSongsMeta = null;
    let setSongsMeta = null;
    let getSong = null;
    let setSong = null;
    let onSongSwitched = null;

    function pluralSongs(n) {
        const m10 = n % 10, m100 = n % 100;
        return (m10 === 1 && m100 !== 11) ? 'песня' : (m10 >= 2 && m10 <= 4 && (m100 < 10 || m100 >= 20)) ? 'песни' : 'песен';
    }
    function pluralSections(n) {
        const m10 = n % 10, m100 = n % 100;
        return (m10 === 1 && m100 !== 11) ? 'секция' : (m10 >= 2 && m10 <= 4 && (m100 < 10 || m100 >= 20)) ? 'секции' : 'секций';
    }
    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    }

    function renderList() {
        const songsMeta = getSongsMeta();
        const list = document.getElementById('songsList');
        document.getElementById('songsCount').textContent = `${songsMeta.songs.length} ${pluralSongs(songsMeta.songs.length)}`;

        if (songsMeta.songs.length === 0) {
            list.innerHTML = '<div class="empty-songs">Пока нет ни одной песни.<br>Нажмите «+ Новая песня», чтобы начать.</div>';
            return;
        }

        list.innerHTML = songsMeta.songs.map(s => {
            const isActive = s.id === songsMeta.activeSongId;
            const p = s.preview || {};
            const date = new Date(s.updatedAt).toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' });
            return `<div class="song-item ${isActive ? 'active' : ''}" data-id="${s.id}">
                <div class="song-item-info">
                    <div class="song-item-name">${escapeHtml(s.name)}</div>
                    <div class="song-item-meta">${p.sections || 0} ${pluralSections(p.sections || 0)} · ${p.bars || 0} т. · ${p.bpm || 120} BPM · ${date}</div>
                </div>
                <div class="song-item-actions">
                    <button data-act="rename" data-id="${s.id}" title="Переименовать">✏️</button>
                    <button data-act="duplicate" data-id="${s.id}" title="Дублировать">⎘</button>
                    <button data-act="delete" data-id="${s.id}" class="danger" title="Удалить">×</button>
                </div>
            </div>`;
        }).join('');
    }

    // 🔥 ДОБАВЛЕНО: async, так как внутри используются await вызовы к StorageManager
    async function handleListClick(e) {
        const songsMeta = getSongsMeta();
        const item = e.target.closest('.song-item');
        const btn = e.target.closest('[data-act]');

        if (btn) {
            e.stopPropagation();
            const id = btn.dataset.id;
            const act = btn.dataset.act;

            if (act === 'rename') {
                const s = songsMeta.songs.find(x => x.id === id);
                const newName = prompt('Название песни:', s.name);
                if (newName && newName.trim()) {
                    // 🔥 ДОБАВЛЕНО: await
                    const newMeta = await window.StorageManager.renameSong(id, newName);
                    setSongsMeta(newMeta);
                    if (id === songsMeta.activeSongId) {
                        window.dispatchEvent(new CustomEvent('app:song-name-changed'));
                    }
                    renderList();
                }
            }
            if (act === 'duplicate') {
                // 🔥 ДОБАВЛЕНО: await
                const res = await window.StorageManager.duplicateSong(id);
                if (res) { setSongsMeta(res.meta); renderList(); }
            }
            if (act === 'delete') {
                const s = songsMeta.songs.find(x => x.id === id);
                if (confirm(`Удалить песню «${s.name}»?`)) {
                    // 🔥 ДОБАВЛЕНО: await
                    const res = await window.StorageManager.deleteSong(id);
                    setSongsMeta(res.meta);
                    if (res.song) {
                        setSong(res.song);
                        onSongSwitched && onSongSwitched();
                    }
                    renderList();
                }
            }
        } else if (item && !btn) {
            if (item.dataset.id !== songsMeta.activeSongId) {
                // 🔥 ДОБАВЛЕНО: await
                const newSong = await window.StorageManager.switchToSong(item.dataset.id);
                songsMeta.activeSongId = item.dataset.id;
                setSongsMeta(songsMeta);
                setSong(newSong);
                onSongSwitched && onSongSwitched();
                close();
            }
        }
    }

    function open() {
        renderList();
        document.getElementById('songsModal').classList.add('active');
    }
    
    function close() {
        document.getElementById('songsModal').classList.remove('active');
    }

    // 🔥 ДОБАВЛЕНО: async
    async function createNew() {
        // 🔥 ДОБАВЛЕНО: await
        const res = await window.StorageManager.createNewSong(null);
        setSongsMeta(res.meta);
        setSong(res.song);
        onSongSwitched && onSongSwitched();
        open();
    }

    function init(dependencies) {
        getSongsMeta = dependencies.getSongsMeta;
        setSongsMeta = dependencies.setSongsMeta;
        getSong = dependencies.getSong;
        setSong = dependencies.setSong;
        onSongSwitched = dependencies.onSongSwitched;

        document.getElementById('closeSongsModal').addEventListener('click', close);
        document.getElementById('newSongBtn').addEventListener('click', createNew);
        document.getElementById('songsModal').addEventListener('click', (e) => {
            if (e.target.id === 'songsModal') close();
        });
        document.getElementById('songsList').addEventListener('click', handleListClick);
        window.addEventListener('app:songs-click', open);
    }

    window.SongsModal = { init, open, close, refresh: renderList };
})();