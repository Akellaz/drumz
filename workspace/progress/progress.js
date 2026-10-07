import { auth, db } from '/assets/auth.js';
import { doc, getDoc, collection, query, orderBy, limit, getDocs } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js";
import { onAuthStateChanged } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";

const loadingEl = document.getElementById('progressLoading');
const contentEl = document.getElementById('progressContent');
const errorEl = document.getElementById('progressError');
const totalXPEl = document.getElementById('totalXP');
const xpLogListEl = document.getElementById('xpLogList');

onAuthStateChanged(auth, async (user) => {
  if (!user) {
    window.location.href = '/';
    return;
  }
  
  await loadProgress(user.uid);
});

async function loadProgress(uid) {
  try {
    // 1. Загружаем общий XP
    const userDoc = await getDoc(doc(db, "users", uid));
    const userData = userDoc.data();
    const totalXP = userData.totalXP || 0;
    
    totalXPEl.textContent = totalXP;
    
    // 2. Загружаем последние 50 событий
    const xpLogsQuery = query(
      collection(db, `users/${uid}/xp_logs`),
      orderBy('timestamp', 'desc'),
      limit(50)
    );
    
    const querySnapshot = await getDocs(xpLogsQuery);
    const logs = [];
    
    querySnapshot.forEach(doc => {
      logs.push(doc.data());
    });
    
    // 3. Рендерим список
    if (logs.length === 0) {
      xpLogListEl.innerHTML = '<p style="color: var(--text-light);">Пока нет активности. Начните тренировку, чтобы заработать опыт!</p>';
    } else {
      xpLogListEl.innerHTML = logs.map(log => {
        const date = log.timestamp ? log.timestamp.toDate() : new Date();
        const dateStr = date.toLocaleDateString('ru-RU', { 
          day: '2-digit', 
          month: '2-digit', 
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        });
        
        const toolName = getToolName(log.toolId);
        const contextInfo = formatContext(log.context);
        
        return `
          <div class="xp-log-item">
            <div class="xp-log-main">
              <div class="xp-log-tool">${toolName}</div>
              <div class="xp-log-date">${dateStr}</div>
            </div>
            <div class="xp-log-amount">+${log.amount} XP</div>
            ${contextInfo ? `<div class="xp-log-context">${contextInfo}</div>` : ''}
          </div>
        `;
      }).join('');
    }
    
    // Показываем контент, скрываем загрузку
    loadingEl.style.display = 'none';
    contentEl.style.display = 'block';
    
  } catch (error) {
    console.error("Ошибка загрузки прогресса:", error);
    loadingEl.style.display = 'none';
    errorEl.style.display = 'block';
  }
}

function getToolName(toolId) {
  const names = {
    'rhythm_sequencer': 'Ритм-секвенсор',
    'etude_completed': 'Этюд',
    'setlist_created': 'Создание сетлиста'
  };
  return names[toolId] || toolId;
}

function formatContext(context) {
  if (!context) return '';
  
  const parts = [];
  if (context.bpm) parts.push(`BPM: ${context.bpm}`);
  if (context.steps) parts.push(`Шагов: ${context.steps}`);
  if (context.difficulty) parts.push(`Сложность: ${context.difficulty}`);
  
  return parts.join(' • ');
}