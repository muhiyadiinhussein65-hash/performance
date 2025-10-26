<?php
include 'inc/conn.php';

// Validate and sanitize inputs
$summary_id = filter_var($_GET['summary'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
$subject_id = filter_var($_GET['subject_id'] ?? 0, FILTER_VALIDATE_INT) ?: 0;

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("SELECT title, content, reading_time FROM summaries WHERE id = ?");
$stmt->bind_param("i", $summary_id);
$stmt->execute();
$summary = $stmt->get_result()->fetch_assoc();

$stmt = $conn->prepare("SELECT name FROM subjects WHERE id = ?");
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$subject = $stmt->get_result()->fetch_assoc();

// Get question count
$stmt = $conn->prepare("SELECT COUNT(*) as question_count FROM questions WHERE summary_id = ?");
$stmt->bind_param("i", $summary_id);
$stmt->execute();
$question_data = $stmt->get_result()->fetch_assoc();
$question_count = $question_data['question_count'] ?? 0;

$page_title = "Sumarry Page";
include 'head.php'; 
?>

<body class="font-display bg-background-light dark:bg-background-dark min-h-screen">
    <div class="flex flex-col min-h-screen">
        <!-- Header -->
        <header
            class="quiz-header sticky top-0 z-50 bg-white/80 dark:bg-background-dark/80 border-b border-gray-200/50 dark:border-gray-700/50 backdrop-blur-sm">
            <div class="container-mobile mx-auto px-4">
                <div class="flex items-center justify-between py-3">
                    <!-- Back Button -->
                    <button class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <a href="chapters.php?subject_id=<?php echo $subject_id; ?>"><span
                                class="material-symbols-outlined text-gray-600 dark:text-gray-400">arrow_back</span>
                        </a></button>

                    <!-- Title -->
                    <h1 class="text-lg font-bold text-gray-900 dark:text-white px-2 text-center flex-1 truncate">
                        <?php echo htmlspecialchars($subject['name'] ?? 'Unknown Subject'); ?>
                    </h1>
                    <!-- Spacer for balance -->
                    <div class="w-10"></div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow container-mobile mx-auto px-4 py-6">
            <!-- Quiz Hero Section -->
            <div
                class="quiz-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
                <!-- Subject Icon -->
                <div class="flex justify-center mb-4">
                    <div
                        class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg">
                        <svg fill="white" height="32" viewBox="0 0 256 256" width="32">
                            <path
                                d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Z">
                            </path>
                            <path
                                d="M173.66,90.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,140.69l50.34-50.35A8,8,0,0,1,173.66,90.34Z">
                            </path>
                        </svg>
                    </div>
                </div>

                <!-- Quiz Title -->
                <h2 class="text-mobile-lg font-bold text-center text-gray-900 dark:text-white mb-2">
                    <?php echo htmlspecialchars($summary['title']); ?>
                </h2>

                <!-- Quiz Description -->
                <p class="text-base text-gray-600 dark:text-gray-400 text-center leading-relaxed mb-6">
                    <?php echo htmlspecialchars($summary['content']); ?>
                </p>

                <!-- Quiz Stats -->
                <div class="flex justify-center items-center gap-6 mb-6">
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg fill="currentColor" height="16" viewBox="0 0 256 256" width="16">
                            <path
                                d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Z">
                            </path>
                            <path
                                d="M128,72a8,8,0,0,0-8,8v56a8,8,0,0,0,16,0V80A8,8,0,0,0,128,72Zm-8.49,88.49a12,12,0,1,0,12,12A12,12,0,0,0,119.51,160.49Z">
                            </path>
                        </svg>
                        <span><?php echo $question_count; ?> Questions</span>
                    </div>

                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg fill="currentColor" height="16" viewBox="0 0 256 256" width="16">
                            <path
                                d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Z">
                            </path>
                            <path
                                d="M175.43,131.72l-45.71,32a8,8,0,0,1-4.72,1.56,8.1,8.1,0,0,1-4.16-1.16,8,8,0,0,1-4-7V88a8,8,0,0,1,12.88-6.34l45.71,32a8,8,0,0,1,0,12.68Z">
                            </path>
                        </svg>
                        <span><?php echo htmlspecialchars($summary['reading_time'] ?? '15'); ?> mins</span>
                    </div>
                </div>

                <!-- Beautiful Action Button -->
                <button class="btn-beautiful w-full rounded-xl text-white transition-all duration-300 pulse-animation"
                    id="startQuizBtn">
                    <span class="btn-text">Start Quiz Now</span>
                    <span class="btn-sparkle">
                        <svg fill="currentColor" height="20" viewBox="0 0 24 24" width="20">
                            <path
                                d="M12,17.27L18.18,21l-1.64-7.03L22,9.24l-7.19-0.61L12,2L9.19,8.63L2,9.24l5.46,4.73L5.82,21L12,17.27z" />
                        </svg>
                    </span>
                </button>
            </div>

            <div
                class="quiz-card bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Your Progress</h3>
                    <span class="text-sm font-medium text-primary">0%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-primary h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Complete this quiz to unlock advanced topics
                </p>
            </div>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const startButton = document.getElementById('startQuizBtn');

        if (startButton) {
            startButton.addEventListener('click', function() {
                // Add loading state
                this.classList.add('btn-loading');
                this.classList.remove('pulse-animation');

                // Ripple effect
                createRipple(this, event);

                // Navigate to quiz after animation
                setTimeout(() => {
                    window.location.href =
                        `quiz.php?summary_id=<?php echo $summary_id; ?>&subject_id=<?php echo $subject_id; ?>`;
                }, 800);
            });
        }

        // Add touch feedback for mobile
        const cards = document.querySelectorAll('.quiz-card');
        cards.forEach(card => {
            card.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.99)';
            });

            card.addEventListener('touchend', function() {
                this.style.transform = '';
            });
        });

        // Ripple effect function
        function createRipple(button, event) {
            const circle = document.createElement("span");
            const diameter = Math.max(button.clientWidth, button.clientHeight);
            const radius = diameter / 2;

            const rect = button.getBoundingClientRect();
            const x = (event.clientX || event.touches[0].clientX) - rect.left - radius;
            const y = (event.clientY || event.touches[0].clientY) - rect.top - radius;

            circle.style.width = circle.style.height = `${diameter}px`;
            circle.style.left = `${x}px`;
            circle.style.top = `${y}px`;
            circle.style.position = "absolute";
            circle.style.borderRadius = "50%";
            circle.style.backgroundColor = "rgba(255, 255, 255, 0.6)";
            circle.style.transform = "scale(0)";
            circle.style.animation = "ripple 600ms linear";
            circle.style.pointerEvents = "none";

            button.style.position = "relative";
            button.style.overflow = "hidden";
            button.appendChild(circle);

            setTimeout(() => {
                circle.remove();
            }, 600);
        }

        // Add ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    });
    </script>
</body>

</html>