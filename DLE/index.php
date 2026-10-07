<?php require_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="/assets/style.css">
<script src="components/registry.js"></script>
<script src="LessonEngine.js"></script>
<script src="components/blocks/grid.js"></script>

<textarea id="lesson-dsl" style="display:none;">
=== LESSON ===
title: Новый урок

=== CARD 1 ===
blocks:
  - type: grid
    target_hh: |x-x-x-x-x-x-x-x-|
    target_snare: |----o-------o---|
    target_kick: |o-------o-------|
    showCheck: false
    show_hh: true
    show_snare: true
    show_kick: true

=== CARD 2 ===
blocks:
  - type: grid
    target_hh: |x-x-x-x-x-x-x-x-|
    target_snare: |----o-------o---|
    target_kick: |o-------o-------|
    showCheck: true
    show_hh: true
    show_snare: true
    show_kick: true
</textarea>
<div id="lesson-root"></div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const dslText = document.getElementById('lesson-dsl').value;
    const lessonData = LessonEngine.parseDSL(dslText);
    window.currentLesson = new LessonPlayer('lesson-root', lessonData);
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>