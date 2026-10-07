import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
import { 
  getAuth, 
  signInWithPopup, 
  GoogleAuthProvider, 
  onAuthStateChanged, 
  signOut 
} from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";
import { 
  getFirestore, 
  doc, 
  setDoc, 
  getDoc, 
  serverTimestamp 
} from "https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js";

// ═══════════════════════════════════════════════════════════════
// 1. Инициализация Firebase
// ═══════════════════════════════════════════════════════════════
const firebaseConfig = {
  apiKey: "AIzaSyCb4mcornYDLKr36cfW8oGQtd_NE4Ktjd4",
  authDomain: "drumz-c9989.firebaseapp.com",
  projectId: "drumz-c9989",
  storageBucket: "drumz-c9989.firebasestorage.app",
  messagingSenderId: "51187561111",
  appId: "1:51187561111:web:4c24867e255f2165592fd2"
};

const app = initializeApp(firebaseConfig);
export const auth = getAuth(app);
export const db = getFirestore(app);
export const provider = new GoogleAuthProvider();

// ═══════════════════════════════════════════════════════════════
// 2. Логика работы с Firestore
// ═══════════════════════════════════════════════════════════════
async function saveUserToFirestore(user) {
  const userDocRef = doc(db, "users", user.uid);
  const docSnap = await getDoc(userDocRef);
  
  if (!docSnap.exists()) {
    await setDoc(userDocRef, {
      name: user.displayName,
      email: user.email,
      photoURL: user.photoURL,
      provider: "google",
      createdAt: serverTimestamp(),
      lastLogin: serverTimestamp()
    });
  } else {
    await setDoc(userDocRef, { lastLogin: serverTimestamp() }, { merge: true });
  }
}

export async function logout() {
  // 1. Сообщаем серверу, что нужно уничтожить PHP-сессию
  try {
      await fetch('/workspace/lessons/api.php?action=logout', { method: 'POST' });
  } catch (e) {
      console.warn('Не удалось очистить сессию на сервере', e);
  }
  
  // 2. Очищаем локальный кэш
  localStorage.removeItem('user_cache');
  
  // 3. Выходим из Firebase
  await signOut(auth);
  
  // 4. Перенаправляем на главную
  window.location.href = '/';
}

// ═══════════════════════════════════════════════════════════════
// 3. UI: Мгновенный рендер из кэша
// ═══════════════════════════════════════════════════════════════
const authContainer = document.getElementById('authContainer');
let isRenderedFromCache = false;

function renderFromCache() {
  const cached = localStorage.getItem('user_cache');
  if (!cached) return false;
  
  try {
    const userData = JSON.parse(cached);
    if (!userData.photoURL) return false;
    
    authContainer.innerHTML = `
      <a href="/workspace" class="user-avatar-link">
        <img src="${userData.photoURL}" alt="Аватар" class="user-avatar">
      </a>
    `;
    isRenderedFromCache = true;
    return true;
  } catch (e) {
    console.error('Ошибка чтения кэша:', e);
    return false;
  }
}

renderFromCache();

// ═══════════════════════════════════════════════════════════════
// 4. UI: Обновление при изменении состояния авторизации
// ═══════════════════════════════════════════════════════════════
onAuthStateChanged(auth, async (user) => {
  if (user) {
    await saveUserToFirestore(user);
    localStorage.setItem('user_cache', JSON.stringify({
      photoURL: user.photoURL,
      displayName: user.displayName
    }));
  } else {
    localStorage.removeItem('user_cache');
  }
  
  renderAvatar(user);
});

function renderAvatar(user) {
  if (!user) {
    authContainer.innerHTML = `
      <button id="loginBtn" class="btn-login">
        <svg viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
          <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" fill="#4285F4"/>
          <path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z" fill="#34A853"/>
          <path d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
          <path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z" fill="#EA4335"/>
        </svg>
        Войти
      </button>
    `;

    document.getElementById('loginBtn').addEventListener('click', async () => {
      try {
        await signInWithPopup(auth, provider);
      } catch (error) {
        console.error("Ошибка входа:", error);
        alert("Не удалось войти. Проверьте настройки или попробуйте позже.");
      }
    });
    return;
  }
  
  if (isRenderedFromCache) {
    const img = authContainer.querySelector('.user-avatar');
    if (img && img.src !== user.photoURL) {
      img.src = user.photoURL;
    }
    return;
  }
  
  authContainer.innerHTML = `
    <a href="/workspace" class="user-avatar-link">
      <img src="${user.photoURL}" alt="Аватар" class="user-avatar">
    </a>
  `;
}

// ═══════════════════════════════════════════════════════════════
// 5. UI: Обработка кнопки "Выйти" в сайдбаре
// ═══════════════════════════════════════════════════════════════
const logoutBtn = document.getElementById('sidebarLogoutBtn');
if (logoutBtn) {
  logoutBtn.addEventListener('click', async () => {
    await logout();
  });
}

// ═══════════════════════════════════════════════════════════════
// 6. Глобальный помощник для получения токена (для builder.php и workspace)
// ═══════════════════════════════════════════════════════════════
window.getFirebaseToken = async function() {
    if (typeof auth !== 'undefined' && auth.currentUser) {
        const token = await auth.currentUser.getIdToken();
        return token;
    }
    return null;
};