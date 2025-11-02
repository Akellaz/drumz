<?php
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$page = $path ?: '';

$seo = [
  '' => [
    'title' => 'Уроки барабанов в Троицке | Drumz',
    'description' => 'Профессиональные уроки игры на барабанах в Троицке для детей и взрослых. Опытный преподаватель Сергей Щепотин. Запишитесь на пробный урок!'
  ],
  'about' => [
    'title' => 'Барабанная школа в Троицке | Drumz',
    'description' => 'Обучение игре на барабанах в Троицке. Индивидуальный подход, удобное расписание, профессиональное оборудование. Преподаватель — Сергей Щепотин.'
  ],
  'sequencer' => [
    'title' => 'Онлайн-секвенсор для барабанов | Drumz',
    'description' => 'Интерактивный секвенсор для создания барабанных партий. Учитесь ритму и груву онлайн — бесплатно и без установки.'
  ],
  'exercises' => [
    'title' => 'Упражнения для барабанщиков | Drumz',
    'description' => 'Базовые и продвинутые упражнения для развития координации, скорости и чувства ритма. Для начинающих и опытных барабанщиков.'
  ],
  'songs' => [
    'title' => 'Разборы песен на барабанах | Drumz',
    'description' => 'Пошаговые разборы известных композиций: Queen, The Beatles, Red Hot Chili Peppers и др. Учитесь играть как профессионалы.'
  ],
  'notes' => [
    'title' => 'Ноты и партитуры для барабанов | Drumz',
    'description' => 'Бесплатные ноты и PDF-партитуры для барабанщиков всех уровней. Рок, джаз, фанк, латина — всё в одном месте.'
  ],
  'articles' => [
    'title' => 'Статьи о барабанах и обучении | Drumz',
    'description' => 'Полезные статьи: как выбрать палочки, как играть с метрономом, основы рудиментов и советы от преподавателя из Троицка.'
  ]
];

$current = $seo[$page] ?? $seo[''];
$title = $current['title'];
$description = $current['description'];
?>
<title><?= htmlspecialchars($title) ?></title>
<meta name="description" content="<?= htmlspecialchars($description) ?>">
<meta name="keywords" content="барабаны, уроки барабанов, Троицк, обучение барабанам, Сергей Щепотин">
<meta name="author" content="Сергей Щепотин">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://drumz.ru<?= $_SERVER['REQUEST_URI'] ?>">

<meta property="og:title" content="<?= htmlspecialchars($title) ?>">
<meta property="og:description" content="<?= htmlspecialchars($description) ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="https://drumz.ru<?= $_SERVER['REQUEST_URI'] ?>">
<meta property="og:site_name" content="Drumz.ru">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "MusicSchool",
  "name": "Drumz — уроки барабанов в Троицке",
  "url": "https://drumz.ru",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Микрорайон В, 59 ст1",
    "addressLocality": "Троицк",
    "addressRegion": "Московская область",
    "postalCode": "142190",
    "addressCountry": "RU"
  },
  "telephone": "+79309930503",
  "email": "info@drumz.ru",
  "openingHoursSpecification": {
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": [
      "https://schema.org/Monday",
      "https://schema.org/Tuesday",
      "https://schema.org/Wednesday",
      "https://schema.org/Thursday",
      "https://schema.org/Friday",
      "https://schema.org/Saturday",
      "https://schema.org/Sunday"
    ],
    "opens": "08:00",
    "closes": "23:00"
  },
  "priceRange": "500–1500 ₽",
  "description": "Профессиональные уроки игры на барабанах в Троицке для детей и взрослых"
}
</script>