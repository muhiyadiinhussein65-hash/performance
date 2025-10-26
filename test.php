<?php
include 'inc/conn.php';

echo "Testing database connection...\n";

if (!$conn) {
    die("❌ Connection failed: " . mysqli_connect_error());
}

echo "✅ Connected successfully\n";

// Test if table exists
$table_check = $conn->query("SHOW TABLES LIKE 'student_answers'");
if ($table_check->num_rows > 0) {
    echo "✅ student_answers table exists\n";
} else {
    echo "❌ student_answers table does NOT exist\n";
}

// Test insertion
$test_sql = "INSERT INTO student_answers (student_id, question_id, answer_text, is_correct, points_earned) 
             VALUES (1, 3, 'test answer', 1, 10)";

if ($conn->query($test_sql)) {
    echo "✅ Test insertion successful! ID: " . $conn->insert_id . "\n";
} else {
    echo "❌ Test insertion failed: " . $conn->error . "\n";
}

// Count records
$result = $conn->query("SELECT COUNT(*) as count FROM student_answers");
if ($result) {
    $row = $result->fetch_assoc();
    echo "📊 Total records in student_answers: " . $row['count'] . "\n";
} else {
    echo "❌ Count failed: " . $conn->error . "\n";
}

$conn->close();
?>