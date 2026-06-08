<?php
require_once 'config.php';

try {
    $pdo->exec("ALTER TABLE subjects ADD COLUMN book_id INT DEFAULT NULL;");
    $pdo->exec("ALTER TABLE subjects ADD CONSTRAINT fk_subject_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE SET NULL;");
    echo "Added book_id to subjects.\n";
} catch (PDOException $e) { echo "Subjects update error (likely already exists): " . $e->getMessage() . "\n"; }

try {
    $pdo->exec("ALTER TABLE topics ADD COLUMN book_id INT DEFAULT NULL;");
    $pdo->exec("ALTER TABLE topics ADD CONSTRAINT fk_topic_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE SET NULL;");
    echo "Added book_id to topics.\n";
} catch (PDOException $e) { echo "Topics update error (likely already exists): " . $e->getMessage() . "\n"; }

try {
    $pdo->exec("ALTER TABLE concepts ADD COLUMN book_id INT DEFAULT NULL;");
    $pdo->exec("ALTER TABLE concepts ADD CONSTRAINT fk_concept_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE SET NULL;");
    echo "Added book_id to concepts.\n";
} catch (PDOException $e) { echo "Concepts update error (likely already exists): " . $e->getMessage() . "\n"; }
?>
