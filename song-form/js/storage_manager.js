/**
 * storage_manager.js
 * Управление хранилищем: Local-First кэш + явное сохранение в Firestore.
 * БЕЗ миграции из localStorage. Без авто-создания "Моя первая песня".
 */
(function() {
    const DATA_VERSION = 10;

    let memoryMeta = { songs: [], activeSongId: null };
    let memorySongs = {};
    let isCloudMode = false;
    let db = null;
    let auth = null;

    function uid() { return Math.random().toString(36).slice(2, 10); }
    function emptySong() { return { bpm: 120, timeSig: '4/4', blocks: [], version: DATA_VERSION }; }

    async function ensureFirebase() {
        if (db && auth) return true;
        try {
            const { getAuth } = await import("https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js");
            const { getFirestore, collection, getDocs } = await import("https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js");
            const { initializeApp } = await import("https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js");
            
            const firebaseConfig = {
                apiKey: "AIzaSyCb4mcornYDLKr36cfW8oGQtd_NE4Ktjd4",
                authDomain: "drumz-c9989.firebaseapp.com",
                projectId: "drumz-c9989",
                storageBucket: "drumz-c9989.firebasestorage.app",
                messagingSenderId: "51187561111",
                appId: "1:51187561111:web:4c24867e255f2165592fd2"
            };
            
            const app = initializeApp(firebaseConfig);
            auth = getAuth(app);
            db = getFirestore(app);
            return true;
        } catch (e) {
            console.warn("Firebase не загружен", e);
            return false;
        }
    }

    function getCurrentUserId() {
        return auth && auth.currentUser ? auth.currentUser.uid : null;
    }

    async function waitForAuthState() {
        if (!auth) return null;
        if (auth.currentUser) return auth.currentUser.uid;
        
        return new Promise((resolve) => {
            const unsubscribe = auth.onAuthStateChanged((user) => {
                unsubscribe();
                resolve(user ? user.uid : null);
            });
            setTimeout(() => {
                unsubscribe();
                resolve(null);
            }, 3000);
        });
    }

    window.StorageManager = {
        init: async function() {
            const cloudReady = await ensureFirebase();
            const uid = await waitForAuthState();

            if (cloudReady && uid) {
                isCloudMode = true;
                console.log(`☁️ [Storage] Облачный режим. Пользователь: ${uid}`);

                const { getDoc, doc, setDoc, collection, getDocs } = await import("https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js");
                
                const metaSnap = await getDoc(doc(db, 'users', uid, 'settings', 'meta'), { source: 'server' });
                let memoryMetaLocal = metaSnap.exists() ? metaSnap.data() : { songs: [], activeSongId: null };

                const songsSnap = await getDocs(collection(db, 'users', uid, 'songs'));
                const actualSongIds = new Set();
                let metaNeedsUpdate = false;

                songsSnap.forEach(docSnap => {
                    actualSongIds.add(docSnap.id);
                    const songData = docSnap.data();
                    
                    const inMeta = memoryMetaLocal.songs.find(s => s.id === docSnap.id);
                    if (!inMeta) {
                        const songName = songData.name || `Песня ${docSnap.id.slice(0, 8)}`;
                        memoryMetaLocal.songs.push({
                            id: docSnap.id,
                            name: songName,
                            createdAt: songData.createdAt || Date.now(),
                            updatedAt: songData.updatedAt || Date.now(),
                            preview: {
                                bars: songData.blocks ? songData.blocks.reduce((sum, b) => sum + (b.length || 0), 0) : 0,
                                sections: songData.blocks ? songData.blocks.length : 0,
                                bpm: songData.bpm || 120
                            }
                        });
                        console.log(`🔧 [Storage] Найдена и восстановлена песня: ${songName}`);
                        metaNeedsUpdate = true;
                    }
                });

                const originalLength = memoryMetaLocal.songs.length;
                memoryMetaLocal.songs = memoryMetaLocal.songs.filter(s => actualSongIds.has(s.id));
                if (memoryMetaLocal.songs.length !== originalLength) {
                    metaNeedsUpdate = true;
                    console.log(`🧹 [Storage] Очищена meta от несуществующих песен.`);
                }

                if (metaNeedsUpdate) {
                    console.log(`💾 [Storage] Сохраняем исправленную meta...`);
                    await setDoc(doc(db, 'users', uid, 'settings', 'meta'), memoryMetaLocal);
                }

                memoryMeta = memoryMetaLocal;
                console.log(`✅ [Storage] Мета актуализирована. Песен: ${memoryMeta.songs.length}`);

                // БЕЗ авто-создания "Моя первая песня"
                if (memoryMeta.songs.length === 0) {
                    return { meta: memoryMeta, song: emptySong() };
                }

                if (!memoryMeta.activeSongId || !memoryMeta.songs.find(s => s.id === memoryMeta.activeSongId)) {
                    memoryMeta.activeSongId = memoryMeta.songs[0].id;
                    await setDoc(doc(db, 'users', uid, 'settings', 'meta'), memoryMeta);
                }

                const songSnap = await getDoc(doc(db, 'users', uid, 'songs', memoryMeta.activeSongId), { source: 'server' });
                const currentSong = songSnap.exists() ? songSnap.data() : emptySong();
                memorySongs[memoryMeta.activeSongId] = currentSong;

                return { meta: memoryMeta, song: currentSong };
            } else {
                // Демо-режим: только в памяти, без localStorage
                isCloudMode = false;
                console.log("💾 [Storage] Демо-режим (только в памяти)");
                memoryMeta = { songs: [], activeSongId: null };
                return { meta: memoryMeta, song: emptySong() };
            }
        },

        loadSong: function(id) {
            if (isCloudMode) {
                return memorySongs[id] || emptySong();
            }
            return emptySong();
        },

        saveSong: async function(id, song, meta) {
            if (isCloudMode) {
                memorySongs[id] = song;
                memoryMeta = meta;
                
                try {
                    const { setDoc, doc } = await import("https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js");
                    const userId = getCurrentUserId();
                    
                    const songMeta = meta.songs.find(s => s.id === id);
                    if (songMeta) {
                        song.name = songMeta.name;
                        song.updatedAt = Date.now();
                    }
                    
                    const cleanSong = JSON.parse(JSON.stringify(song));
                    const cleanMeta = JSON.parse(JSON.stringify(meta));

                    await setDoc(doc(db, 'users', userId, 'songs', id), cleanSong);
                    await setDoc(doc(db, 'users', userId, 'settings', 'meta'), cleanMeta);
                } catch (e) {
                    console.error("❌ [Storage] Ошибка сохранения:", e);
                    throw e;
                }
            }
            // В демо-режиме ничего не сохраняем
        },

        createNewSong: async function(name = null) {
            if (isCloudMode) {
                const { setDoc, doc } = await import("https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js");
                const uid = getCurrentUserId();
                const id = `${Date.now()}_${uid.slice(0,4)}`; 
                const songName = name || `Песня ${memoryMeta.songs.length + 1}`;
                const newSong = emptySong();
                
                newSong.name = songName;
                newSong.createdAt = Date.now();
                newSong.updatedAt = Date.now();

                memorySongs[id] = newSong;
                memoryMeta.songs.unshift({ id, name: songName, createdAt: Date.now(), updatedAt: Date.now(), preview: { bars: 0, sections: 0, bpm: 120 } });
                memoryMeta.activeSongId = id;
                
                await setDoc(doc(db, 'users', uid, 'songs', id), newSong);
                await setDoc(doc(db, 'users', uid, 'settings', 'meta'), memoryMeta);
                
                return { meta: memoryMeta, song: newSong, id };
            } else {
                // Демо-режим: только в памяти
                const id = uid();
                const songName = name || `Песня ${memoryMeta.songs.length + 1}`;
                const newSong = emptySong();
                memorySongs[id] = newSong;
                memoryMeta.songs.unshift({ id, name: songName, createdAt: Date.now(), updatedAt: Date.now(), preview: { bars: 0, sections: 0, bpm: 120 } });
                memoryMeta.activeSongId = id;
                return { meta: memoryMeta, song: newSong, id };
            }
        },

        switchToSong: async function(id) {
            if (isCloudMode) {
                const { getDoc, doc, setDoc } = await import("https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js");
                const uid = getCurrentUserId();
                
                const songSnap = await getDoc(doc(db, 'users', uid, 'songs', id), { source: 'server' });
                if (songSnap.exists()) {
                    memorySongs[id] = songSnap.data();
                }
                
                memoryMeta.activeSongId = id;
                await setDoc(doc(db, 'users', uid, 'settings', 'meta'), memoryMeta);
                return memorySongs[id] || emptySong();
            } else {
                memoryMeta.activeSongId = id;
                return memorySongs[id] || emptySong();
            }
        },

        deleteSong: async function(id) {
            if (isCloudMode) {
                const { deleteDoc, doc, setDoc } = await import("https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js");
                const uid = getCurrentUserId();
                
                delete memorySongs[id];
                memoryMeta.songs = memoryMeta.songs.filter(x => x.id !== id);
                
                await deleteDoc(doc(db, 'users', uid, 'songs', id));
                
                if (memoryMeta.activeSongId === id) {
                    if (memoryMeta.songs.length === 0) {
                        memoryMeta.activeSongId = null;
                        await setDoc(doc(db, 'users', uid, 'settings', 'meta'), memoryMeta);
                        return { meta: memoryMeta, song: emptySong(), newId: null };
                    } else {
                        memoryMeta.activeSongId = memoryMeta.songs[0].id;
                        await setDoc(doc(db, 'users', uid, 'settings', 'meta'), memoryMeta);
                        return { meta: memoryMeta, song: this.loadSong(memoryMeta.activeSongId), newId: memoryMeta.activeSongId };
                    }
                }
                await setDoc(doc(db, 'users', uid, 'settings', 'meta'), memoryMeta);
                return { meta: memoryMeta, song: null, newId: null };
            } else {
                delete memorySongs[id];
                memoryMeta.songs = memoryMeta.songs.filter(x => x.id !== id);
                if (memoryMeta.activeSongId === id) {
                    if (memoryMeta.songs.length === 0) {
                        memoryMeta.activeSongId = null;
                        return { meta: memoryMeta, song: emptySong(), newId: null };
                    } else {
                        memoryMeta.activeSongId = memoryMeta.songs[0].id;
                        return { meta: memoryMeta, song: memorySongs[memoryMeta.activeSongId] || emptySong(), newId: memoryMeta.activeSongId };
                    }
                }
                return { meta: memoryMeta, song: null, newId: null };
            }
        },

        renameSong: async function(id, newName) {
            if (isCloudMode) {
                const { setDoc, doc } = await import("https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js");
                const s = memoryMeta.songs.find(x => x.id === id);
                if (s) {
                    s.name = newName.trim();
                    s.updatedAt = Date.now();
                    
                    const song = memorySongs[id];
                    if (song) {
                        song.name = newName.trim();
                        song.updatedAt = Date.now();
                        await setDoc(doc(db, 'users', getCurrentUserId(), 'songs', id), song);
                    }
                    
                    await setDoc(doc(db, 'users', getCurrentUserId(), 'settings', 'meta'), memoryMeta);
                }
            } else {
                const s = memoryMeta.songs.find(x => x.id === id);
                if (s) {
                    s.name = newName.trim();
                }
            }
            return memoryMeta;
        },

        duplicateSong: async function(id) {
            const s = memoryMeta.songs.find(x => x.id === id);
            if (!s) return null;
            const src = this.loadSong(id);
            const newId = isCloudMode ? `${Date.now()}_${getCurrentUserId().slice(0,4)}` : uid();
            const copy = JSON.parse(JSON.stringify(src));
            copy.blocks.forEach(b => { b.id = uid(); b.collapsed = false; b.pageBreak = !!b.pageBreak; b.breakAfter = !!b.breakAfter; });
            
            copy.name = s.name + ' (копия)';
            copy.createdAt = Date.now();
            copy.updatedAt = Date.now();
            
            if (isCloudMode) {
                const { setDoc, doc } = await import("https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js");
                const uid = getCurrentUserId();
                memorySongs[newId] = copy;
                memoryMeta.songs.unshift({ id: newId, name: s.name + ' (копия)', createdAt: Date.now(), updatedAt: Date.now(), preview: { ...s.preview } });
                await setDoc(doc(db, 'users', uid, 'songs', newId), copy);
                await setDoc(doc(db, 'users', uid, 'settings', 'meta'), memoryMeta);
            } else {
                memorySongs[newId] = copy;
                memoryMeta.songs.unshift({ id: newId, name: s.name + ' (копия)', createdAt: Date.now(), updatedAt: Date.now(), preview: { ...s.preview } });
            }
            return { meta: memoryMeta, newId };
        },

        importSong: async function(parsedData, fileName) {
            const newId = isCloudMode ? `${Date.now()}_${getCurrentUserId().slice(0,4)}` : uid();
            const newSong = { 
                bpm: parsedData.bpm || 120, 
                timeSig: parsedData.timeSig || '4/4', 
                blocks: parsedData.blocks, 
                version: DATA_VERSION 
            };
            newSong.blocks.forEach(b => { 
                b.id = uid(); b.collapsed = false; b.pageBreak = !!b.pageBreak; b.breakAfter = !!b.breakAfter; 
                if (!b.patterns) b.patterns = {}; 
            });
            
            const songName = parsedData.songName || fileName.replace(/\.[^/.]+$/, "") || 'Импортированная песня';
            
            newSong.name = songName;
            newSong.createdAt = Date.now();
            newSong.updatedAt = Date.now();
            
            if (isCloudMode) {
                const { setDoc, doc } = await import("https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js");
                const uid = getCurrentUserId();
                memorySongs[newId] = newSong;
                memoryMeta.songs.unshift({ id: newId, name: songName, createdAt: Date.now(), updatedAt: Date.now(), preview: { bars: newSong.blocks.reduce((sum, b) => sum + b.length, 0), sections: newSong.blocks.length, bpm: newSong.bpm } });
                memoryMeta.activeSongId = newId;
                await setDoc(doc(db, 'users', uid, 'songs', newId), newSong);
                await setDoc(doc(db, 'users', uid, 'settings', 'meta'), memoryMeta);
            } else {
                memorySongs[newId] = newSong;
                memoryMeta.songs.unshift({ id: newId, name: songName, createdAt: Date.now(), updatedAt: Date.now(), preview: { bars: newSong.blocks.reduce((sum, b) => sum + b.length, 0), sections: newSong.blocks.length, bpm: newSong.bpm } });
                memoryMeta.activeSongId = newId;
            }
            return { meta: memoryMeta, song: newSong, newId, songName };
        }
    };
})();