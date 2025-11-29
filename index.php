<?php require_once __DIR__ . '/includes/seo.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require_once __DIR__ . '/includes/seo.php'; ?>
  <link rel="stylesheet" href="/assets/style.css?v=<?php echo time(); ?>">
</head>
<body>
  <?php require_once __DIR__ . '/includes/header.php'; ?>

  <main class="container">
    <?php include 'form/widget.php'; ?>
  </main>

  <?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>