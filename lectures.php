<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'inc/conn.php';

// Validate and sanitize input parameters
$chapter_id = isset($_GET['chapter_id']) ? (int)$_GET['chapter_id'] : 0;
$subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;

// Check if required parameters are provided
if ($chapter_id <= 0 || $subject_id <= 0) {
    die("Error: Missing or invalid chapter_id or subject_id");
}

// Initialize subject variable to prevent undefined variable errors
$subject = ['name' => 'Unknown Subject', 'icon' => 'Book'];

try {
    // Get subject data with prepared statement
    $subject_stmt = $conn->prepare("SELECT name, icon FROM subjects WHERE id = ?");
    if (!$subject_stmt) {
        throw new Exception("Subject prepare failed: " . $conn->error);
    }

    $subject_stmt->bind_param("i", $subject_id);
    if (!$subject_stmt->execute()) {
        throw new Exception("Subject execute failed: " . $subject_stmt->error);
    }

    $subject_result = $subject_stmt->get_result();
    if ($subject_result && $subject_result->num_rows > 0) {
        $subject = $subject_result->fetch_assoc();
    }
    $subject_stmt->close();

    // Get summaries with prepared statement
    $summary_stmt = $conn->prepare("
        SELECT s.id, s.title, s.content, s.reading_time, COUNT(q.id) as question_count 
        FROM summaries s 
        LEFT JOIN questions q ON q.summary_id = s.id 
        WHERE s.chapter_id = ? 
        GROUP BY s.id, s.title, s.content, s.reading_time
    ");

    if (!$summary_stmt) {
        throw new Exception("Summary prepare failed: " . $conn->error);
    }

    $summary_stmt->bind_param("i", $chapter_id);
    if (!$summary_stmt->execute()) {
        throw new Exception("Summary execute failed: " . $summary_stmt->error);
    }

    $summaries_result = $summary_stmt->get_result();
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    die("An error occurred while loading the page. Please try again later.");
}

// Icon mapping
$iconMap = [
    'Calculator' => 'calculate',
    'Atom' => 'science',
    'BookOpen' => 'menu_book',
    'Book' => 'book',
    'MoonStar' => 'dark_mode',
    'SquareRoot' => 'functions',
    'Dna' => 'psychology',
    'MapMarkedAlt' => 'map',
    'University' => 'account_balance',
    'Mosque' => 'account_balance'
];

$subject_icon = $iconMap[$subject['icon']] ?? 'school';

$page_title = "Lessons";
include 'head.php';
?>

<body class="bg-background-light dark:bg-background-dark font-display">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header
            class="sticky top-0 z-10 bg-background-light dark:bg-background-dark/80 backdrop-blur-sm border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between p-4">
                <a href="chapters.php?subject_id=<?php echo $subject_id; ?>"
                    class="flex items-center space-x-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" />
                    </svg>
                    <span class="font-medium text-sm">Back</span>
                </a>

                <div class="flex items-center space-x-2">
                    <div
                        class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-sm"><?php echo $subject_icon; ?></span>
                    </div>
                    <h1 class="font-bold text-slate-900 dark:text-white text-base truncate max-w-[120px]">
                        <?php echo htmlspecialchars($subject['name']); ?>
                    </h1>
                </div>

                <div class="w-8"></div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto pb-20">
            <div class="p-4">
                <!-- Hero Section -->
                <div
                    class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg mb-6 relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-12 -translate-x-12">
                    </div>

                    <div class="relative z-10">
                        <h2 class="text-2xl font-bold mb-2">Ready to Learn?</h2>
                        <p class="text-blue-100 text-sm opacity-90">Explore lessons and master new concepts</p>
                    </div>
                </div>

                <!-- Lessons Header -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Lessons</h3>
                    <span class="text-slate-600 dark:text-slate-400 text-sm font-medium">
                        <?php echo ($summaries_result) ? $summaries_result->num_rows : 0; ?> lessons
                    </span>
                </div>

                <!-- Lessons Grid -->
                <div class="space-y-3">
                    <?php
                    $lesson_count = 0;
                    if ($summaries_result && $summaries_result->num_rows > 0) {
                        while ($row = $summaries_result->fetch_assoc()) {
                            $lesson_count++;
                            $reading_time = $row['reading_time'] ? $row['reading_time'] . ' min read' : 'Quick read';
                            $question_count = $row['question_count'] ?: 0;
                            $has_quiz = $question_count > 0;
                    ?>
                            <a href="summary.php?summary=<?php echo $row['id']; ?>&subject_id=<?php echo $subject_id; ?>&chapter_id=<?php echo $chapter_id; ?>"
                                class="block group transform transition-transform hover:scale-[1.02] active:scale-95">
                                <div
                                    class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-lg border border-slate-200 dark:border-slate-700 hover:shadow-xl transition-all duration-300 relative overflow-hidden">
                                    <!-- Shimmer effect -->
                                    <div
                                        class="absolute inset-0 rounded-2xl bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                                    </div>

                                    <div class="relative z-10 flex items-start space-x-4">
                                        <!-- Lesson Number -->
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30 rounded-xl flex items-center justify-center border border-blue-200 dark:border-blue-800">
                                                <span
                                                    class="text-blue-600 dark:text-blue-400 font-bold text-lg"><?php echo $lesson_count; ?></span>
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between mb-2">
                                                <h4
                                                    class="text-slate-900 dark:text-white font-semibold text-base leading-tight pr-2">
                                                    <?php echo htmlspecialchars($row['title']); ?>
                                                </h4>
                                                <?php if ($has_quiz): ?>
                                                    <div class="flex-shrink-0 bg-green-100 dark:bg-green-900/30 rounded-full p-1">
                                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path
                                                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                                        </svg>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Stats -->
                                            <div class="flex items-center space-x-4">
                                                <div class="flex items-center space-x-1 text-slate-600 dark:text-slate-400">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z" />
                                                    </svg>
                                                    <span class="text-xs font-medium"><?php echo $reading_time; ?></span>
                                                </div>

                                                <div class="flex items-center space-x-1 text-slate-600 dark:text-slate-400">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z" />
                                                    </svg>
                                                    <span class="text-xs font-medium"><?php echo $question_count; ?>
                                                        questions</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Arrow -->
                                        <div
                                            class="flex-shrink-0 text-slate-400 group-hover:text-blue-500 transition-colors mt-1">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </a>
                    <?php
                        }
                        $summary_stmt->close();
                    }
                    ?>

                    <!-- Empty State -->
                    <?php if (!$summaries_result || $summaries_result->num_rows === 0): ?>
                        <div class="text-center py-12">
                            <div
                                class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" />
                                </svg>
                            </div>
                            <h4 class="text-slate-900 dark:text-white font-semibold text-lg mb-2">No Lessons Available</h4>
                            <p class="text-slate-600 dark:text-slate-400 text-sm max-w-xs mx-auto">
                                Check back later for new learning materials in this chapter.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
        <?php
        // Close database connection
        $conn->close();
        include 'inc/footer.php';
        ?>