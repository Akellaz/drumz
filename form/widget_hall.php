<!-- form/widget.php -->
<link rel="stylesheet" href="/form/form.css">
<div class="drums-form-widget">
  

  <form id="bookingFormWidget" action="/form/form_handler.php" method="POST">
    <!-- Цель: радиокнопки-кнопки -->
    <div class="choice-buttons">


      <label class="choice-btn">
        <input type="radio" name="purpose" value="Хочу в Зал Славы" required>
        <span class="btn-icon">🏆</span>
        <strong>Хочу в Зал Славы!</strong>
      </label>
    </div>

    <!-- Имя -->
    <div class="form-group">
      <label for="name_widget">Ваше имя *</label>
      <input type="text" id="name_widget" name="name" placeholder="Иван" required>
    </div>

    <!-- Телефон -->
    <div class="form-group">
      <label for="phone_widget">Телефон *</label>
      <input type="tel" id="phone_widget" name="phone" placeholder="+7 (999) 999-99-99" required>
    </div>

    <div class="form-group">
      <button type="submit" class="btn-primary">
        Отправить заявку →
      </button>
    </div>
  </form>

  <div class="form-message drums-form-message"></div>
</div>
<script src="/form/form.js"></script>