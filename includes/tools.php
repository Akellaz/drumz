<?php /* Блок «Инструменты» для главной страницы drumz.ru */ ?>

<section class="dz-tools" id="tools">
  <div class="dz-tools__head">
    <h2>Инструменты</h2>
    <p>Тренажёры и помощники для барабанщиков</p>
  </div>

  <div class="dz-tools__grid">

    <!-- ┌────────────────────────────────────────────────┐
         │ ЭТЮДЫ                                          │
         │ Панель: белая 85% | Скос: 24px, вправо         │
         └────────────────────────────────────────────────┘ -->
    <a class="dz-card dz-card--brand" href="/gen_etudes/"
        style="--panel-alpha:.85; --img-position: center 20%; --img-scale: 1.0;">
      <div class="dz-card__media dz-card__media--empty">

	  </div>
      <div class="dz-card__body"><div class="dz-card__inner">
        <h3 class="dz-card__title">Этюды</h3>
        <p class="dz-card__desc">Генератор этюдов для практики техники и координации.</p>
      </div></div>
    </a>

    <!-- ┌────────────────────────────────────────────────┐
         │ ВРЕМЯ                                          │
         │ Панель: белая 85% | Скос: 24px, вправо         │
         └────────────────────────────────────────────────┘ -->
    <a class="dz-card dz-card--flip" href="/time-feel/"
       style="--panel-alpha:.85;">
      <div class="dz-card__media dz-card__media--empty"></div>
      <div class="dz-card__body"><div class="dz-card__inner">
        <h3 class="dz-card__title">Время</h3>
        <p class="dz-card__desc">Тренировка чувства времени и внутреннего пульса.</p>
      </div></div>
    </a>

    <!-- ┌────────────────────────────────────────────────┐
         │ Математика Ритма                                          │
         │ Панель: тёмная (пресет invert)                 │
         └────────────────────────────────────────────────┘ -->
    <a class="dz-card dz-card--invert" href="/tests/">
      <div class="dz-card__media dz-card__media--empty"></div>
      <div class="dz-card__body"><div class="dz-card__inner">
        <h3 class="dz-card__title">Математика Ритма</h3>
        <p class="dz-card__desc">Проверка знаний ритмики, нотации и теории.</p>
      </div></div>
    </a>

    <!-- ┌────────────────────────────────────────────────┐
         │ Группы нот                                      │
         │ Панель: фирменная синяя (пресет brand)         │
         └────────────────────────────────────────────────┘ -->
    <a class="dz-card dz-card--brand dz-card--flip" href="/stest/gen-auto.php">
      <div class="dz-card__media dz-card__media--empty"></div>
      <div class="dz-card__body"><div class="dz-card__inner">
        <h3 class="dz-card__title">Группы нот</h3>
        <p class="dz-card__desc">Базовые сочетания длительностей.</p>
      </div></div>
    </a>

    <!-- ┌────────────────────────────────────────────────┐
         │ БИБЛИОТЕКА                                     │
         │ Панель: белая 85% | Скос: 24px, вправо         │
         └────────────────────────────────────────────────┘ -->
    <a class="dz-card" href="/library/"
       style="--panel-alpha:.85;">
      <div class="dz-card__media dz-card__media--empty"></div>
      <div class="dz-card__body"><div class="dz-card__inner">
        <h3 class="dz-card__title">Библиотека</h3>
        <p class="dz-card__desc">Коллекция грувов, рудиментов и паттернов.</p>
      </div></div>
    </a>
	
	
	    <!-- ┌────────────────────────────────────────────────┐
         │ ИИ                                     │
         │ Панель: белая 85% | Скос: 24px, вправо         │
         └────────────────────────────────────────────────┘ -->
    <a class="dz-card" href="/magenta/"
       style="--panel-alpha:.85;">
      <div class="dz-card__media dz-card__media--empty"></div>
      <div class="dz-card__body"><div class="dz-card__inner">
        <h3 class="dz-card__title">Тренировка с ИИ</h3>
        <p class="dz-card__desc">Развитие слуха.</p>
      </div></div>
    </a>

  </div>

  <!-- Отдельный блок для Builder -->
  <div class="builder-section">
    <h3>Экспериментальные инструменты</h3>
    <div class="tool-grid">
      <a href="/builder/" class="work-item">
        <span class="work-item-badge">бета</span>
        <span class="work-item-icon">🎼</span>
        <span class="work-item-name">Drumz Builder</span>
      </a>

    </div>
  </div>
</section>