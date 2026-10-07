<?php
$currentPath = rtrim($_SERVER['REQUEST_URI'], '/');
if ($currentPath === '') {
    $currentPath = '/';
}

$menuItems = [
  ['label' => 'Рабочее пространство', 'href' => '/workspace'],
  ['label' => 'Создать урок', 'href' => '/DLE/builder.php'],
  ['label' => 'Прогресс', 'href' => '/workspace/progress'],
  ['label' => 'Блог', 'href' => '/workspace/blog'],
  ['label' => 'Настройки', 'href' => '/workspace/settings'],
];
?>

<aside class="workspace-sidebar">
  <nav class="sidebar-nav">
    <?php foreach ($menuItems as $item): ?>
      <?php 
        $isActive = ($currentPath === $item['href']); 
      ?>
      <a href="<?= $item['href'] ?>" class="sidebar-link <?= $isActive ? 'is-active' : '' ?>">
        <?= $item['label'] ?>
      </a>
    <?php endforeach; ?>
    
    <div class="sidebar-logout-wrapper">
      <button id="sidebarLogoutBtn" class="sidebar-link">
        Выйти
      </button>
    </div>
  </nav>
</aside>