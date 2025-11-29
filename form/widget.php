<link rel="stylesheet" href="/form/form.css">
<div class="drums-form-widget" 
     itemscope itemtype="https://schema.org/ContactAction">



  <form id="bookingFormWidget" 
        action="/form/form_handler.php" 
        method="POST"
        itemscope itemtype="https://schema.org/ContactForm">

    <!-- Цель: радиокнопки-кнопки -->
    <div class="choice-buttons">
      <label class="choice-btn">
        <input type="radio" name="purpose" value="Пробный урок" required>
        <span class="btn-icon">🎯</span>
        <strong>Пробный урок</strong>
        <small>Попробуйте — первый шаг к ритму</small>
      </label>

      <label class="choice-btn">
        <input type="radio" name="purpose" value="Запись на курс" required>
        <span class="btn-icon">📘</span>
        <strong>Запись на курс</strong>
        <small>Начните обучение с нуля или продолжите</small>
      </label>

      <label class="choice-btn">
        <input type="radio" name="purpose" value="Хочу в Зал Славы" required>
        <span class="btn-icon">🏆</span>
        <strong>Хочу в Зал Славы</strong>
        <small>Моя цель — стать легендой студии</small>
      </label>
    </div>

    <!-- Имя -->
    <div class="form-group">
      <label for="name_widget" itemprop="name">Ваше имя *</label>
      <input type="text" 
             id="name_widget" 
             name="name" 
             placeholder="Иван" 
             required 
             itemprop="name">
    </div>

    <!-- Телефон -->
    <div class="form-group">
      <label for="phone_widget" itemprop="telephone">Телефон *</label>
      <input type="tel" 
             id="phone_widget" 
             name="phone" 
             placeholder="+7 (999) 999-99-99" 
             required 
             itemprop="telephone">
    </div>

    <!-- Скрытое поле: источник -->
    <input type="hidden" name="source" value="drumz.ru/form" />
    <input type="hidden" name="teacher" value="Сергей Щепотин" />
    <input type="hidden" name="location" value="Троицк" />

    <div class="form-group">
      <button type="submit" class="btn-primary" itemprop="target">
        Отправить заявку →
      </button>
    </div>
  </form>

  <div class="form-message drums-form-message"></div>

  <!-- Альтернативный способ связи -->
  <p style="text-align: center; margin-top: 20px; font-size: 14px;">
    Или напишите мне в Telegram: 
    <a href="https://t.me/SchepotinSergey" target="_blank" style="color: #0088cc; font-weight: bold;">@SchepotinSergey</a>
  </p>
</div>
<script src="/form/form.js"></script>