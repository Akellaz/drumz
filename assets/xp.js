import { auth, db } from './auth.js';
import { 
  doc, 
  setDoc, 
  collection, 
  addDoc, 
  serverTimestamp, 
  increment 
} from "https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js";

// Словарь наград. Легко менять в одном месте!
const XP_REWARDS = {
  'rhythm_sequencer': 15, // Опыт за прохождение паттерна в секвенсоре
  'etude_completed': 50,
  'setlist_created': 10
};

export async function awardXP(toolId, context = {}) {
  const user = auth.currentUser;
  
  // Если пользователь не вошел, молча выходим (или можно показать toast "Войдите, чтобы сохранять прогресс")
  if (!user) {
    console.log("Пользователь не авторизован, XP не начислен.");
    return;
  }

  const xpAmount = XP_REWARDS[toolId] || 10; // Запасной вариант, если инструмента нет в списке

  try {
    // 1. Записываем событие в историю (для админки и аналитики)
    await addDoc(collection(db, `users/${user.uid}/xp_logs`), {
      toolId: toolId,
      amount: xpAmount,
      context: context, // Например: { bpm: 120, patternLength: 16 }
      timestamp: serverTimestamp()
    });

    // 2. Обновляем общий счетчик пользователя
    // increment() безопасно добавит значение к существующему, или создаст поле totalXP, если его нет
    await setDoc(doc(db, "users", user.uid), {
      totalXP: increment(xpAmount)
    }, { merge: true });

    // 3. Сообщаем интерфейсу об успехе (чтобы показать красивое уведомление)
    window.dispatchEvent(new CustomEvent('xp-awarded', { 
      detail: { toolId, amount: xpAmount } 
    }));

  } catch (error) {
    console.error("Ошибка начисления XP:", error);
  }
}