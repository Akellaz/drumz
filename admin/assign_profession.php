<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)$_POST['student_id'];
    $profession = $_POST['profession_slug'];
    $allowed = ['math-of-rhythm', 'session-drummer', 'stage-drummer', 'jazz-master'];
    if (in_array($profession, $allowed)) {
        $pdo->prepare("UPDATE students SET profession_slug = ? WHERE id = ?")
            ->execute([$profession, $student_id]);
    }
}
header("Location: progress.php?student=" . $student_id);
exit;