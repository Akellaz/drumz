<header class="site-header">
  <div class="site-title"><a href="/">Drumz</a></div>
  <nav class="main-nav" role="navigation">
    <a href="/gen_etudes/" class="<?php echo $_SERVER['REQUEST_URI'] === '/gen_etudes/' ? 'active' : ''; ?>">Этюды</a>
    <a href="/gen_rhythms/" class="<?php echo $_SERVER['REQUEST_URI'] === '/gen_rhythms/' ? 'active' : ''; ?>">Ритмы</a>
    <a href="/time-feel/" class="<?php echo $_SERVER['REQUEST_URI'] === '/time-feel/' ? 'active' : ''; ?>">Время</a>
	<a href="/pattern/" class="<?php echo $_SERVER['REQUEST_URI'] === '/pattern/' ? 'active' : ''; ?>">Паттерны</a>
	<a href="/tests/" class="<?php echo $_SERVER['REQUEST_URI'] === '/tests/' ? 'active' : ''; ?>">Тесты</a>
	<a href="/song-editor/" class="<?php echo $_SERVER['REQUEST_URI'] === '/song-editor/' ? 'active' : ''; ?>">Редактор</a>
	<a href="/library/" class="<?php echo $_SERVER['REQUEST_URI'] === '/library/' ? 'active' : ''; ?>">Библиотека</a>
  </nav>
</header>
