<header class="site-header">
  <div class="container">
    <h1><a href="/">🥁 Drumz — уроки барабанов в Троицке</a></h1>
    <nav class="main-nav">
      <a href="/sequencer/" <?= basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') === 'sequencer' ? 'class="active"' : '' ?>>Секвенсор</a>
      <a href="/about/" <?= basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') === 'about' ? 'class="active"' : '' ?>>Школа</a>
	    <a href="/gen/" <?= basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') === 'gen' ? 'class="active"' : '' ?>>Gen</a>
	    <a href="/drum_book/" <?= basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') === 'drum_book' ? 'class="active"' : '' ?>>Drum book</a>
    </nav>
  </div>
</header>