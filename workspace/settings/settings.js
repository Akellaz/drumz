import { auth, db } from '/assets/auth.js'; // logout убрали отсюда, он в ui.js
import { doc, getDoc, setDoc } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js";
import { onAuthStateChanged } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";

const form = document.getElementById('settingsForm');
const statusEl = document.getElementById('settingsStatus');
const displayNameInput = document.getElementById('displayName');
const showHintsCheckbox = document.getElementById('showHints');

let currentUser = null;

onAuthStateChanged(auth, async (user) => {
  if (!user) {
    window.location.href = '/';
    return;
  }
  
  currentUser = user;
  await loadSettings();
});

async function loadSettings() {
  try {
    const userDoc = await getDoc(doc(db, "users", currentUser.uid));
    const data = userDoc.data();
    
    if (data.displayName) {
      displayNameInput.value = data.displayName;
    }
    
    if (data.showHints !== undefined) {
      showHintsCheckbox.checked = data.showHints;
    } else {
      showHintsCheckbox.checked = true;
    }
    
    showStatus('Настройки загружены', 'info');
  } catch (error) {
    console.error("Ошибка загрузки настроек:", error);
    showStatus('Не удалось загрузить настройки', 'error');
  }
}

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  
  if (!currentUser) return;
  
  const settings = {
    displayName: displayNameInput.value.trim(),
    showHints: showHintsCheckbox.checked,
    updatedAt: new Date()
  };
  
  try {
    await setDoc(
      doc(db, "users", currentUser.uid),
      { settings },
      { merge: true }
    );
    
    showStatus('Настройки сохранены', 'success');
  } catch (error) {
    console.error("Ошибка сохранения:", error);
    showStatus('Не удалось сохранить настройки', 'error');
  }
});

function showStatus(message, type = 'info') {
  statusEl.textContent = message;
  statusEl.className = `settings-status settings-status--${type}`;
  
  setTimeout(() => {
    statusEl.textContent = '';
    statusEl.className = 'settings-status';
  }, 3000);
}