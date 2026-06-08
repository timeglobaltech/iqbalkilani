<?php
require_once 'config.php';

header('Content-Type: application/json');

// Login check
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;

if ($course_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid course']);
    exit;
}

// Get user info from DB
$user_stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$user_stmt->execute([$_SESSION['user_id']]);
$user = $user_stmt->fetch();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

// Check if course exists
$course_stmt = $pdo->prepare("SELECT id, title_en FROM courses WHERE id = ?");
$course_stmt->execute([$course_id]);
$course = $course_stmt->fetch();

if (!$course) {
    echo json_encode(['success' => false, 'message' => 'Course not found']);
    exit;
}

$lesson = sanitize($_POST['lesson'] ?? '');

// Check if already enrolled
$check_stmt = $pdo->prepare("SELECT id FROM enrollments WHERE course_id = ? AND student_email = ?");
$check_stmt->execute([$course_id, $user['email']]);
if ($check_stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'already_enrolled']);
    exit;
}

// Save enrollment
$insert_stmt = $pdo->prepare("INSERT INTO enrollments (course_id, student_name, student_email, lesson) VALUES (?, ?, ?, ?)");
$insert_stmt->execute([$course_id, $user['name'], $user['email'], $lesson]);

echo json_encode(['success' => true, 'message' => 'Enrolled successfully in ' . $course['title_en']]);
?>
