import { auth } from '/assets/auth.js';
import { onAuthStateChanged } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";

let currentToken = null;
let isEditing = false;

// Элементы DOM
const editorPanel = document.getElementById('editorPanel');
const postsList = document.getElementById('postsList');
const btnNewPost = document.getElementById('btnNewPost');
const btnCancel = document.getElementById('btnCancel');
const btnSave = document.getElementById('btnSave');

// 1. Проверка авторизации при загрузке
onAuthStateChanged(auth, async (user) => {
  console.log("🔍 [Auth] Состояние изменилось. User:", user ? "Есть" : "Нет");
  
  if (!user) {
    console.warn("⚠️ [Auth] Пользователь не найден, редирект на /");
    window.location.href = '/';
    return;
  }
  
  console.log("✅ [Auth] Пользователь есть. Получаем токен...");
  currentToken = await user.getIdToken();
  console.log("🔑 [Auth] Токен получен, длина:", currentToken.length);
  
  console.log("📡 [Auth] Отправляем запрос на verify_auth.php...");
  const verifyRes = await fetch('/workspace/verify_auth.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ token: currentToken })
  });
  
  const verifyData = await verifyRes.json();
  console.log("📬 [Auth] Ответ от verify_auth.php:", verifyData);
  
  loadPosts();
});

// 2. Загрузка списка постов
async function loadPosts() {
  try {
    const res = await fetch('/blog/api.php?action=list');
    const posts = await res.json();
    postsList.innerHTML = '';

    if (posts.length === 0) {
      postsList.innerHTML = '<li style="text-align:center; padding: 40px; color: #666;">Записей пока нет. Создайте первую!</li>';
      return;
    }

    posts.forEach(post => {
      const li = document.createElement('li');
      li.className = 'post-item';
      li.innerHTML = `
        <div class="post-item-info">
          <div class="post-item-title">${escapeHtml(post.title)}</div>
          <div class="post-item-meta">ID: ${post.id} • ${new Date(post.date).toLocaleDateString('ru-RU')}</div>
        </div>
        <div>
          <button class="btn btn-edit" onclick="window.editPost('${post.id}')">Изменить</button>
          <button class="btn btn-danger" onclick="window.deletePost('${post.id}')">Удалить</button>
        </div>
      `;
      postsList.appendChild(li);
    });
  } catch (e) {
    postsList.innerHTML = '<li style="text-align:center; padding: 40px; color: red;">Ошибка загрузки данных.</li>';
  }
}

// 3. Управление формой
btnNewPost.addEventListener('click', () => {
  isEditing = false;
  document.getElementById('editorTitle').textContent = 'Новая запись';
  document.getElementById('postId').value = '';
  document.getElementById('postIdInput').value = 'post-' + Date.now();
  document.getElementById('postDate').value = new Date().toISOString().split('T')[0];
  document.getElementById('postTitle').value = '';
  document.getElementById('postExcerpt').value = '';
  document.getElementById('postContent').value = '';
  editorPanel.classList.add('active');
});

btnCancel.addEventListener('click', () => {
  editorPanel.classList.remove('active');
});

// Делаем функции доступными глобально для onclick в HTML
window.editPost = async function(id) {
  try {
    const res = await fetch(`/blog/api.php?action=get&id=${id}`);
    const post = await res.json();
    
    isEditing = true;
    document.getElementById('editorTitle').textContent = 'Редактирование записи';
    document.getElementById('postId').value = post.id;
    document.getElementById('postIdInput').value = post.id;
    document.getElementById('postDate').value = post.date;
    document.getElementById('postTitle').value = post.title;
    document.getElementById('postExcerpt').value = post.excerpt || '';
    document.getElementById('postContent').value = post.content || '';
    
    editorPanel.classList.add('active');
    // Прокрутка к форме
    editorPanel.scrollIntoView({ behavior: 'smooth' });
  } catch (e) {
    alert('Ошибка загрузки записи');
  }
};

window.deletePost = async function(id) {
  if (!confirm('Удалить эту запись безвозвратно?')) return;

  try {
    const res = await fetch('/blog/api.php?action=delete', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id })
    });
    const result = await res.json();
    if (result.status === 'success') {
      loadPosts();
    } else {
      alert('Ошибка: ' + result.error);
    }
  } catch (e) {
    alert('Ошибка сети при удалении');
  }
};

// 4. Сохранение (Создание или Обновление)
btnSave.addEventListener('click', async () => {
  const id = document.getElementById('postId').value; // Скрытое поле с оригинальным ID
  const newId = document.getElementById('postIdInput').value.trim();
  const action = (isEditing && id) ? 'update' : 'create';
  
  const data = {
    id: newId || ('post-' + Date.now()),
    title: document.getElementById('postTitle').value.trim(),
    date: document.getElementById('postDate').value,
    excerpt: document.getElementById('postExcerpt').value.trim(),
    visualType: 'abstract', // Жёстко задано, как просили
    content: document.getElementById('postContent').value
  };

  if (!data.title || !data.content) {
    alert('Заполните заголовок и содержание');
    return;
  }

  btnSave.textContent = 'Сохранение...';
  btnSave.disabled = true;

  try {
    const res = await fetch('/blog/api.php?action=' + action, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    const result = await res.json();
    
    if (result.status === 'success') {
      editorPanel.classList.remove('active');
      loadPosts();
    } else {
      alert('Ошибка сервера: ' + result.error);
    }
  } catch (e) {
    alert('Ошибка сети при сохранении');
  } finally {
    btnSave.textContent = 'Сохранить';
    btnSave.disabled = false;
  }
});

// Утилита для безопасности вывода
function escapeHtml(text) {
  if (!text) return '';
  return text
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}