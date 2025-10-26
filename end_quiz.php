<?php
session_start();
include 'inc/conn.php';

// Get parameters
$summary_id = filter_var($_GET['summary_id'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
$subject_id = filter_var($_GET['subject_id'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
$student_id = $_SESSION['student_id'] ?? 1;

// Fetch quiz result data from database
$result_stmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_questions,
        SUM(CASE WHEN sa.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers,
        SUM(sa.points_earned) as total_points,
        COUNT(DISTINCT sa.id) as attempt_number
    FROM student_answers sa
    JOIN questions q ON sa.question_id = q.id
    WHERE q.summary_id = ? AND sa.student_id = ?
    AND sa.answered_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
");
$result_stmt->bind_param("ii", $summary_id, $student_id);
$result_stmt->execute();
$result_data = $result_stmt->get_result()->fetch_assoc();
$result_stmt->close();

// Fetch subject info
$subject_stmt = $conn->prepare("SELECT name FROM subjects WHERE id = ?");
$subject_stmt->bind_param("i", $subject_id);
$subject_stmt->execute();
$subject = $subject_stmt->get_result()->fetch_assoc();
$subject_stmt->close();

// Fetch student info
$student_stmt = $conn->prepare("SELECT name FROM students WHERE id = ?");
$student_stmt->bind_param("i", $student_id);
$student_stmt->execute();
$student = $student_stmt->get_result()->fetch_assoc();
$student_stmt->close();

// Calculate percentages and stats
$total_questions = $result_data['total_questions'] ?? 0;
$correct_answers = $result_data['correct_answers'] ?? 0;
$total_points = $result_data['total_points'] ?? 0;
$attempt_number = $result_data['attempt_number'] ?? 1;
$percentage = $total_questions > 0 ? round(($correct_answers / $total_questions) * 100) : 0;

// Determine performance message
if ($percentage >= 90) {
    $performance_message = "Outstanding! You're a natural! 🎯";
    $performance_class = "text-green-600";
} elseif ($percentage >= 70) {
    $performance_message = "Great job! You're doing amazing! 👍";
    $performance_class = "text-blue-600";
} elseif ($percentage >= 50) {
    $performance_message = "Good effort! Keep practicing! 💪";
    $performance_class = "text-yellow-600";
} else {
    $performance_message = "Keep learning! You'll get it next time! 📚";
    $performance_class = "text-orange-600";
}

$page_title = "Quiz Results - " . ($subject['name'] ?? 'Unknown Subject');
include 'head.php';
?>

<body class="bg-background-light dark:bg-background-dark font-display">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header
            class="sticky top-0 z-10 bg-background-light dark:bg-background-dark/80 backdrop-blur-sm border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between p-4">
                <a href="summary.php?summary=<?php echo $summary_id; ?>&subject_id=<?php echo $subject_id; ?>"
                    class="flex items-center space-x-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" />
                    </svg>
                    <span class="font-medium text-sm">Back</span>
                </a>

                <h1 class="font-bold text-slate-900 dark:text-white text-lg">Quiz Results</h1>

                <div class="w-8"></div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-4 space-y-6">
            <!-- Hero Section -->
            <div class="text-center">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">
                    Great job, <?php echo htmlspecialchars($student['name'] ?? 'Student'); ?>!
                </h2>
                <p class="text-slate-600 dark:text-slate-400">
                    You've completed the <?php echo htmlspecialchars($subject['name'] ?? 'Quiz'); ?> quiz!
                </p>
            </div>

            <!-- Score Card -->
            <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl p-6 text-white text-center shadow-lg">
                <div class="text-5xl font-bold mb-2"><?php echo $percentage; ?>%</div>
                <div class="text-blue-100 text-lg font-medium mb-4">Overall Score</div>

                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-2xl font-bold"><?php echo $correct_answers; ?></div>
                        <div class="text-blue-200 text-sm">Correct</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold"><?php echo $total_questions - $correct_answers; ?></div>
                        <div class="text-blue-200 text-sm">Incorrect</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold"><?php echo $total_questions; ?></div>
                        <div class="text-blue-200 text-sm">Total</div>
                    </div>
                </div>
            </div>

            <!-- Performance Message -->
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-lg border border-slate-200 dark:border-slate-700 text-center">
                <p class="text-lg font-semibold <?php echo $performance_class; ?> mb-2">
                    <?php echo $performance_message; ?>
                </p>
                <p class="text-slate-600 dark:text-slate-400 text-sm">
                    Attempt #<?php echo $attempt_number; ?>
                </p>
            </div>

            <!-- XP Earned -->
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg border border-slate-200 dark:border-slate-700 text-center">
                <div class="flex items-center justify-center space-x-3 mb-3">
                    <svg class="w-8 h-8 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                            XP Earned
                        </p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white">
                            +<?php echo $total_points; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Badge Unlocked -->
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg border border-slate-200 dark:border-slate-700 text-center">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Achievement Unlocked!</h3>

                <div class="relative inline-block mb-4">
                    <!-- Animated burst effect -->
                    <div class="absolute inset-0 animate-ping bg-yellow-400 rounded-full opacity-20"></div>

                    <!-- Badge -->
                    <div
                        class="relative w-24 h-24 rounded-full mx-auto bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center border-4 border-yellow-500 shadow-xl">
                        <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                    </div>
                </div>

                <div>
                    <p class="text-lg font-bold text-slate-900 dark:text-white mb-1">Quiz Champion</p>
                    <p class="text-slate-600 dark:text-slate-400 text-sm">
                        Completed with <?php echo $percentage; ?>% accuracy
                    </p>
                </div>
            </div>

            <!-- Progress Stats -->
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-lg border border-slate-200 dark:border-slate-700">
                <h4 class="font-bold text-slate-900 dark:text-white mb-3">Performance Breakdown</h4>

                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-600 dark:text-slate-400">Accuracy</span>
                            <span
                                class="font-semibold text-slate-900 dark:text-white"><?php echo $percentage; ?>%</span>
                        </div>
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                            <div class="h-2 rounded-full bg-gradient-to-r from-green-500 to-blue-500"
                                style="width: <?php echo $percentage; ?>%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-600 dark:text-slate-400">Questions Answered</span>
                            <span
                                class="font-semibold text-slate-900 dark:text-white"><?php echo $total_questions; ?></span>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-600 dark:text-slate-400">Total XP Earned</span>
                            <span
                                class="font-semibold text-slate-900 dark:text-white"><?php echo $total_points; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Action Buttons -->
        <footer
            class="sticky bottom-0 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 p-4 space-y-3">
            <a href="quiz.php?summary_id=<?php echo $summary_id; ?>&subject_id=<?php echo $subject_id; ?>"
                class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold text-lg text-center hover:bg-blue-700 transition-colors duration-300 block">
                Take Quiz Again
            </a>

            <a href="summary.php?summary=<?php echo $summary_id; ?>&subject_id=<?php echo $subject_id; ?>"
                class="w-full bg-slate-600 text-white py-4 rounded-xl font-bold text-lg text-center hover:bg-slate-700 transition-colors duration-300 block">
                Back to Lessons
            </a>

            <a href="subjects.php"
                class="w-full bg-transparent text-slate-600 dark:text-slate-400 py-4 rounded-xl font-bold text-lg text-center hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors duration-300 block border border-slate-300 dark:border-slate-600">
                Browse Subjects
            </a>
        </footer>
    </div>

    <style>
    @keyframes burst {
        0% {
            transform: scale(0.8);
            opacity: 0;
        }

        50% {
            transform: scale(1.1);
            opacity: 1;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .animate-burst {
        animation: burst 0.6s ease-out forwards;
    }
    </style>
</body>

</html>