<?php
// Start output buffering to catch any unwanted output
ob_start();

include 'inc/conn.php';

// Check if session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get parameters
$summary_id = filter_var($_GET['summary_id'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
$subject_id = filter_var($_GET['subject_id'] ?? 0, FILTER_VALIDATE_INT) ?: 0;

// API endpoint for AJAX calls
if (isset($_GET['action']) && $_GET['action'] === 'get_questions') {
    // Clear any previous output
    ob_clean();
    header('Content-Type: application/json');
    
    try {
        // Always load fresh questions for retakes
        $questions_query = "
            SELECT 
                q.id, q.question_type, q.question_text, q.difficulty_level, q.points,
                qa.correct_answer_text,
                GROUP_CONCAT(
                    CONCAT(qo.id, '::', qo.option_text, '::', qo.is_correct) 
                    ORDER BY qo.id SEPARATOR '||'
                ) as options
            FROM questions q
            LEFT JOIN question_answers qa ON qa.question_id = q.id
            LEFT JOIN question_options qo ON qo.question_id = q.id
            WHERE q.summary_id = ?
            GROUP BY q.id
            ORDER BY FIELD(q.question_type, 'multiple_choice', 'direct', 'true_false'), RAND()
        ";
        
        $stmt = $conn->prepare($questions_query);
        if (!$stmt) {
            throw new Exception("Failed to prepare query: " . $conn->error);
        }
        
        $stmt->bind_param("i", $summary_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute query: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        $questions = [];
        while ($row = $result->fetch_assoc()) {
            $options = [];
            if ($row['options']) {
                $option_pairs = explode('||', $row['options']);
                foreach ($option_pairs as $pair) {
                    list($opt_id, $opt_text, $is_correct) = explode('::', $pair, 3);
                    $options[] = [
                        'id' => $opt_id,
                        'text' => $opt_text,
                        'is_correct' => (bool)$is_correct
                    ];
                }
            }
            
            $questions[] = [
                'id' => $row['id'],
                'type' => $row['question_type'],
                'text' => $row['question_text'],
                'difficulty' => $row['difficulty_level'],
                'points' => $row['points'] ?: 10.0,
                'correct_answer' => $row['correct_answer_text'],
                'options' => $options
            ];
        }
        
        $stmt->close();
        
        // Track quiz attempts
        $attempt_number = ($_SESSION['quiz_attempts'][$summary_id] ?? 0) + 1;
        $_SESSION['quiz_attempts'][$summary_id] = $attempt_number;
        
        $_SESSION['quiz_data'] = [
            'summary_id' => $summary_id,
            'questions' => $questions,
            'current_index' => 0,
            'score' => 0,
            'total_points' => 0,
            'start_time' => time(),
            'attempt_number' => $attempt_number
        ];
        
        echo json_encode($_SESSION['quiz_data']);
        
    } catch (Exception $e) {
        error_log("Quiz Error: " . $e->getMessage());
        echo json_encode(['error' => 'Failed to load questions', 'debug' => $e->getMessage()]);
    }
    
    $conn->close();
    exit;
}

// Handle answer submission via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_answer') {
    // Clear any previous output
    ob_clean();
    header('Content-Type: application/json');
    
    try {
        // Check if quiz data exists
        if (!isset($_SESSION['quiz_data'])) {
            throw new Exception("No active quiz session");
        }
        
        $quiz_data = $_SESSION['quiz_data'];
        $current_index = $quiz_data['current_index'];
        
        // Check if current question exists
        if (!isset($quiz_data['questions'][$current_index])) {
            throw new Exception("Invalid question index");
        }
        
        $current_question = $quiz_data['questions'][$current_index];
        $user_answer = trim($_POST['answer'] ?? '');
        $is_correct = false;
        $student_id = $_SESSION['student_id'] ?? 1; // Use session student_id

        // Validate user answer
        if (empty($user_answer)) {
            throw new Exception("No answer provided");
        }

        switch ($current_question['type']) {
            case 'multiple_choice':
                $selected_option_id = null;
                foreach ($current_question['options'] as $option) {
                    if ($option['id'] == $user_answer) {
                        $selected_option_id = $option['id'];
                        if ($option['is_correct']) {
                            $is_correct = true;
                        }
                        break;
                    }
                }
                break;
                
            case 'true_false':
                $correct_answer = strtolower($current_question['correct_answer']) === 'true';
                $is_correct = ($user_answer === 'true' && $correct_answer) || 
                             ($user_answer === 'false' && !$correct_answer);
                $selected_option_id = null;
                break;
                
            case 'direct':
                $is_correct = strtolower($user_answer) === strtolower(trim($current_question['correct_answer']));
                $selected_option_id = null;
                break;
                
            default:
                throw new Exception("Unknown question type: " . $current_question['type']);
        }
        
        $points_earned = $is_correct ? $current_question['points'] : 0;
        
        if ($is_correct) {
            $quiz_data['score']++;
            $quiz_data['total_points'] += $points_earned;
        }

        // STORE ANSWER IN DATABASE
        $storage_success = false;
        $database_error = null;
        
        $insert_query = "
            INSERT INTO student_answers 
            (student_id, question_id, answer_text, option_id, is_correct, points_earned, answered_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ";
        
        if ($conn) {
            $stmt = $conn->prepare($insert_query);
            
            if ($stmt) {
                $answer_text = ($current_question['type'] === 'direct') ? $user_answer : null;
                $option_id = ($current_question['type'] !== 'direct' && isset($selected_option_id)) ? $selected_option_id : null;
                
                $stmt->bind_param(
                    "iisiii", 
                    $student_id, 
                    $current_question['id'], 
                    $answer_text,
                    $option_id,
                    $is_correct,
                    $points_earned
                );
                
                $storage_success = $stmt->execute();
                
                if (!$storage_success) {
                    $database_error = $stmt->error;
                    error_log("Database insert error: " . $database_error);
                }
                
                $stmt->close();
            } else {
                $database_error = $conn->error;
                error_log("Database prepare error: " . $database_error);
            }
        } else {
            $database_error = 'No database connection';
            error_log("No database connection");
        }

        // Update session
        $_SESSION['quiz_data'] = $quiz_data;
        
        // Prepare response
        $response = [
            'success' => true,
            'correct' => $is_correct,
            'points' => $points_earned,
            'score' => $quiz_data['score'],
            'total_points' => $quiz_data['total_points'],
            'current_index' => $current_index,
            'total_questions' => count($quiz_data['questions']),
            'is_completed' => ($current_index + 1 >= count($quiz_data['questions'])),
            'correct_answer' => $current_question['correct_answer'],
            'question_type' => $current_question['type'],
            'storage_success' => $storage_success,
            'database_error' => $database_error
        ];
        
        echo json_encode($response);
        
    } catch (Exception $e) {
        error_log("Answer submission error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    
    if ($conn) $conn->close();
    exit;
}

// Handle moving to next question via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'next_question') {
    ob_clean();
    header('Content-Type: application/json');
    
    try {
        if (!isset($_SESSION['quiz_data'])) {
            throw new Exception("No active quiz session");
        }
        
        $quiz_data = $_SESSION['quiz_data'];
        $quiz_data['current_index']++;
        
        // Check if quiz is completed
        if ($quiz_data['current_index'] >= count($quiz_data['questions'])) {
            $quiz_data['completed'] = true;
            $quiz_data['completion_time'] = time() - $quiz_data['start_time'];
            
            // Store completion data for results
            $_SESSION['last_quiz_result'] = [
                'score' => $quiz_data['score'],
                'total_questions' => count($quiz_data['questions']),
                'total_points' => $quiz_data['total_points'],
                'attempt_number' => $quiz_data['attempt_number'],
                'completion_time' => $quiz_data['completion_time']
            ];
        }
        
        $_SESSION['quiz_data'] = $quiz_data;
        
        echo json_encode([
            'success' => true,
            'current_index' => $quiz_data['current_index'],
            'is_completed' => $quiz_data['completed'] ?? false
        ]);
        
    } catch (Exception $e) {
        error_log("Next question error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    
    if ($conn) $conn->close();
    exit;
}

// Handle quiz restart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'restart_quiz') {
    ob_clean();
    header('Content-Type: application/json');
    
    try {
        // Clear current quiz data to allow fresh start
        unset($_SESSION['quiz_data']);
        
        echo json_encode(['success' => true]);
        
    } catch (Exception $e) {
        error_log("Restart quiz error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    
    if ($conn) $conn->close();
    exit;
}

// Clear output buffer for normal page load
ob_end_clean();

$page_title = "Quiz - " . ($subject['name'] ?? 'Unknown Subject');
include 'head.php'; 
?>



<body class="font-sans bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="flex flex-col min-h-screen">
        <!-- Header -->
        <header
            class="fixed top-0 left-0 right-0 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm z-50 border-b border-gray-200 dark:border-gray-700">
            <div class="container mx-auto px-4 py-3">
                <div class="flex items-center justify-between">
                    <button onclick="history.back()"
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <span class="material-symbols-outlined text-gray-600 dark:text-gray-400">arrow_back</span>
                    </button>

                    <div class="flex-1 mx-4">
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full transition-all duration-300" id="progressBar">
                            </div>
                        </div>
                    </div>

                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400 min-w-[40px] text-right"
                        id="progressText">
                        0/0
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area - Dynamically Updated -->
        <main class="flex-grow container mx-auto px-4 pt-20 pb-24" id="quizContent">
            <div class="max-w-2xl mx-auto text-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                <p class="text-gray-600 dark:text-gray-400">Loading quiz...</p>
            </div>
        </main>

        <!-- Footer - Dynamically Updated -->
        <footer id="quizFooter"></footer>
    </div>

    <script>
    class QuizManager {
        constructor() {
            this.quizData = null;
            this.currentQuestionIndex = 0;
            this.isSubmitting = false;
            this.feedbackShown = false;
        }

        async init() {
            await this.loadQuestions();
            if (this.quizData) {
                this.renderCurrentQuestion();
            }
        }

        async loadQuestions() {
            try {
                const response = await fetch(
                    `?summary_id=<?php echo $summary_id; ?>&subject_id=<?php echo $subject_id; ?>&action=get_questions`
                );

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                this.quizData = await response.json();

            } catch (error) {
                console.error('Error loading questions:', error);
                this.showError('Failed to load quiz questions. Please try again.');
            }
        }

        renderCurrentQuestion() {
            if (!this.quizData || !this.quizData.questions) {
                this.showError('No quiz data available');
                return;
            }

            if (this.quizData.completed) {
                this.showResultsScreen();
                return;
            }

            const question = this.quizData.questions[this.currentQuestionIndex];

            // Update progress
            this.updateProgress();

            const questionHTML = `
                <div class="max-w-2xl mx-auto fade-in">
                    <!-- Question Header -->
                    <div class="mb-6">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium 
                                    ${this.getDifficultyClass(question.difficulty)}">
                            ${question.difficulty.charAt(0).toUpperCase() + question.difficulty.slice(1)} • ${question.points} XP
                        </span>
                        ${this.quizData.attempt_number > 1 ? `
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 ml-2">
                                Attempt ${this.quizData.attempt_number}
                            </span>
                        ` : ''}
                    </div>

                    <!-- Question Text -->
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-8 leading-tight">
                        ${this.escapeHtml(question.text)}
                    </h1>

                    <!-- Answer Area -->
                    <div id="answerArea">
                        ${this.renderAnswerArea(question)}
                    </div>

                    <!-- Feedback Notification Area -->
                    <div id="feedbackArea"></div>

                    <!-- Next Question Button (Initially Hidden) -->
                    <div id="nextQuestionArea" class="mt-6 hidden">
                        <button onclick="quiz.nextQuestion()" 
                                id="nextQuestionBtn"
                                class="w-full bg-green-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-green-700 transition-colors duration-300">
                            Next Question →
                        </button>
                    </div>
                </div>
            `;

            const footerHTML = `
                <div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 p-4">
                    <button onclick="quiz.submitAnswer()" 
                            id="submitBtn"
                            class="w-full bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 py-4 rounded-xl font-bold text-lg transition-colors duration-300"
                            disabled>
                        SUBMIT ANSWER
                    </button>
                </div>
            `;

            // Safely update DOM
            const quizContent = document.getElementById('quizContent');
            const quizFooter = document.getElementById('quizFooter');

            if (quizContent) quizContent.innerHTML = questionHTML;
            if (quizFooter) quizFooter.innerHTML = footerHTML;

            // Reset feedback state
            this.feedbackShown = false;

            // Auto-focus on textarea for direct questions
            const directAnswer = document.getElementById('directAnswer');
            if (directAnswer) {
                directAnswer.focus();
            }
        }

        renderAnswerArea(question) {
            if (!question) return '<p>Error: Question data not available</p>';

            switch (question.type) {
                case 'multiple_choice':
                    if (!question.options || !Array.isArray(question.options)) {
                        return '<p>Error: No options available</p>';
                    }
                    return `
                        <div class="space-y-3">
                            ${question.options.map(option => `
                                <div class="option-card border-2 border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-800 cursor-pointer transition-all duration-200"
                                     onclick="quiz.selectOption(this, '${option.id}')">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full border-2 border-gray-300 dark:border-gray-600 mr-3 flex items-center justify-center transition-colors option-radio">
                                            <div class="w-3 h-3 rounded-full bg-transparent transition-colors"></div>
                                        </div>
                                        <span class="text-gray-900 dark:text-white font-medium">${this.escapeHtml(option.text)}</span>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                        <input type="hidden" id="selectedAnswer">
                    `;

                case 'true_false':
                    return `
                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" onclick="quiz.selectTrueFalse('true')" 
                                    class="true-false-btn border-2 border-gray-200 dark:border-gray-700 rounded-xl p-6 bg-white dark:bg-gray-800 text-center cursor-pointer transition-all duration-200 hover:border-blue-500">
                                <span class="text-4xl mb-2">👍</span>
                                <div class="text-lg font-bold text-gray-900 dark:text-white">True</div>
                            </button>
                            <button type="button" onclick="quiz.selectTrueFalse('false')" 
                                    class="true-false-btn border-2 border-gray-200 dark:border-gray-700 rounded-xl p-6 bg-white dark:bg-gray-800 text-center cursor-pointer transition-all duration-200 hover:border-blue-500">
                                <span class="text-4xl mb-2">👎</span>
                                <div class="text-lg font-bold text-gray-900 dark:text-white">False</div>
                            </button>
                        </div>
                        <input type="hidden" id="selectedAnswer">
                    `;

                case 'direct':
                    return `
                        <div class="bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 p-4">
                            <textarea id="directAnswer" 
                                      class="w-full h-32 p-3 bg-transparent text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none resize-none"
                                      placeholder="Type your answer here..."></textarea>
                        </div>
                    `;

                default:
                    return '<p>Unknown question type</p>';
            }
        }

        selectOption(element, value) {
            document.querySelectorAll('.option-card').forEach(card => {
                card.classList.remove('selected');
                const radio = card.querySelector('.option-radio');
                if (radio) {
                    radio.classList.remove('bg-blue-500', 'border-blue-500');
                    const dot = radio.querySelector('div');
                    if (dot) dot.classList.remove('bg-white');
                }
            });

            element.classList.add('selected');
            const radio = element.querySelector('.option-radio');
            if (radio) {
                radio.classList.add('bg-blue-500', 'border-blue-500');
                const dot = radio.querySelector('div');
                if (dot) dot.classList.add('bg-white');
            }

            document.getElementById('selectedAnswer').value = value;
            this.enableSubmit();
        }

        selectTrueFalse(value) {
            document.querySelectorAll('.true-false-btn').forEach(btn => {
                btn.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
            });

            const selectedBtn = value === 'true' ?
                document.querySelector('.true-false-btn:first-child') :
                document.querySelector('.true-false-btn:last-child');

            if (selectedBtn) {
                selectedBtn.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
            }

            document.getElementById('selectedAnswer').value = value;
            this.enableSubmit();
        }

        enableSubmit() {
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('bg-gray-300', 'dark:bg-gray-700', 'text-gray-500',
                    'dark:text-gray-400');
                submitBtn.classList.add('btn-primary', 'text-white');
            }
        }

        async submitAnswer() {
            if (this.isSubmitting || this.feedbackShown) return;

            this.isSubmitting = true;
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.innerHTML =
                    '<div class="animate-spin rounded-full h-6 w-6 border-b-2 border-white mx-auto"></div>';
                submitBtn.disabled = true;
            }

            const question = this.quizData.questions[this.currentQuestionIndex];
            let userAnswer = '';

            if (question.type === 'direct') {
                const directAnswer = document.getElementById('directAnswer');
                userAnswer = directAnswer ? directAnswer.value.trim() : '';
            } else {
                const selectedAnswer = document.getElementById('selectedAnswer');
                userAnswer = selectedAnswer ? selectedAnswer.value : '';
            }

            console.log('🔄 Submitting answer:', {
                questionId: question.id,
                questionType: question.type,
                userAnswer: userAnswer,
                questionText: question.text.substring(0, 50) + '...'
            });

            try {
                const formData = new FormData();
                formData.append('action', 'submit_answer');
                formData.append('answer', userAnswer);

                console.log('📤 Sending AJAX request...');
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                console.log('📥 Received response:', result);

                // Log debug info to console
                if (result.debug) {
                    console.group('🔍 DEBUG INFORMATION');
                    console.log('🧪 Answer Processing:', result.debug);
                    if (result.database_error) {
                        console.error('❌ Database Error:', result.database_error);
                    }
                    if (result.storage_success) {
                        console.log('✅ Database Storage: SUCCESS');
                        if (result.debug.database && result.debug.database.insert_id) {
                            console.log('📝 Inserted record ID:', result.debug.database.insert_id);
                        }
                    } else {
                        console.error('❌ Database Storage: FAILED');
                    }
                    console.groupEnd();
                }

                this.showFeedback(result, userAnswer, question);

            } catch (error) {
                console.error('💥 Error submitting answer:', error);
                this.showError('Failed to submit answer. Please try again.');

                // Re-enable submit button on error
                if (submitBtn) {
                    submitBtn.innerHTML = 'SUBMIT ANSWER';
                    submitBtn.disabled = false;
                }
            } finally {
                this.isSubmitting = false;
            }
        }

        showFeedback(result, userAnswer, question) {
            this.feedbackShown = true;

            // Hide submit button
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) submitBtn.style.display = 'none';

            // Show next question button
            const nextQuestionArea = document.getElementById('nextQuestionArea');
            if (nextQuestionArea) nextQuestionArea.classList.remove('hidden');

            let feedbackHTML = '';

            if (result.correct) {
                feedbackHTML = `
                    <div class="slide-up mb-6 p-6 rounded-xl border-l-4 border-green-500 bg-green-50 dark:bg-green-900/20">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="font-bold text-green-800 dark:text-green-300 text-lg mb-1">
                                    Correct! Well done! 🎉
                                </p>
                                <p class="text-green-700 dark:text-green-400">
                                    +${result.points} XP earned • Great job!
                                </p>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                let correctAnswerDisplay = '';

                switch (question.type) {
                    case 'multiple_choice':
                        const correctOption = question.options.find(opt => opt.is_correct);
                        correctAnswerDisplay = correctOption ? correctOption.text : result.correct_answer;
                        break;
                    case 'true_false':
                        correctAnswerDisplay = result.correct_answer === 'true' ? 'True' : 'False';
                        break;
                    case 'direct':
                        correctAnswerDisplay = result.correct_answer;
                        break;
                }

                feedbackHTML = `
                    <div class="slide-up mb-6 p-6 rounded-xl border-l-4 border-red-500 bg-red-50 dark:bg-red-900/20">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="font-bold text-red-800 dark:text-red-300 text-lg mb-1">
                                    Not quite right
                                </p>
                                <p class="text-red-700 dark:text-red-400 mb-2">
                                    The correct answer is: <strong>${this.escapeHtml(correctAnswerDisplay)}</strong>
                                </p>
                                <p class="text-red-600 dark:text-red-400 text-sm">
                                    Keep learning! You'll get it next time.
                                </p>
                            </div>
                        </div>
                    </div>
                `;
            }

            const feedbackArea = document.getElementById('feedbackArea');
            if (feedbackArea) {
                feedbackArea.innerHTML = feedbackHTML;
            }

            // Update progress
            this.quizData.score = result.score;
            this.quizData.total_points = result.total_points;
            this.updateProgress();
        }

        async nextQuestion() {
            try {
                const formData = new FormData();
                formData.append('action', 'next_question');

                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.is_completed) {
                    this.showResultsScreen();
                } else {
                    this.currentQuestionIndex = result.current_index;
                    this.renderCurrentQuestion();
                }
            } catch (error) {
                console.error('Error moving to next question:', error);
                this.showError('Failed to load next question');
            }
        }

        showResultsScreen() {
            // Redirect to end_quiz.php with quiz data
            const quizData = this.quizData;
            const resultData = {
                score: quizData.score,
                total_questions: quizData.questions.length,
                total_points: quizData.total_points,
                attempt_number: quizData.attempt_number,
                summary_id: quizData.summary_id,
                subject_id: <?php echo $subject_id; ?>
            };

            // Store in session storage for the results page
            sessionStorage.setItem('quizResult', JSON.stringify(resultData));

            // Redirect to end_quiz.php
            window.location.href =
                `end_quiz.php?summary_id=${quizData.summary_id}&subject_id=<?php echo $subject_id; ?>`;
        }

        async restartQuiz() {
            try {
                const formData = new FormData();
                formData.append('action', 'restart_quiz');

                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    // Reload the page to start fresh
                    window.location.href =
                        '?summary_id=<?php echo $summary_id; ?>&subject_id=<?php echo $subject_id; ?>';
                }
            } catch (error) {
                console.error('Error restarting quiz:', error);
                this.showError('Failed to restart quiz');
            }
        }

        updateProgress() {
            const progressText = document.getElementById('progressText');
            const progressBar = document.getElementById('progressBar');

            if (this.quizData && this.quizData.questions && progressText && progressBar) {
                const progress = ((this.currentQuestionIndex + 1) / this.quizData.questions.length) * 100;
                progressText.textContent = `${this.currentQuestionIndex + 1}/${this.quizData.questions.length}`;
                progressBar.style.width = `${progress}%`;
            }
        }

        getDifficultyClass(difficulty) {
            const classes = {
                'easy': 'bg-green-100 text-green-800',
                'medium': 'bg-yellow-100 text-yellow-800',
                'hard': 'bg-red-100 text-red-800'
            };
            return classes[difficulty] || classes.medium;
        }

        escapeHtml(unsafe) {
            if (!unsafe) return '';
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        showError(message) {
            const quizContent = document.getElementById('quizContent');
            if (!quizContent) {
                console.error('Quiz content element not found:', message);
                return;
            }

            const errorHTML = `
                <div class="max-w-2xl mx-auto text-center py-12">
                    <div class="w-16 h-16 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-white text-2xl">error</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Error</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">${message}</p>
                    <button onclick="location.reload()" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        Try Again
                    </button>
                </div>
            `;
            quizContent.innerHTML = errorHTML;
        }
    }

    // Initialize quiz when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        window.quiz = new QuizManager();
        window.quiz.init();
    });

    // Handle direct answer input
    document.addEventListener('input', function(e) {
        if (e.target.id === 'directAnswer') {
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                if (e.target.value.trim().length > 0) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('bg-gray-300', 'dark:bg-gray-700', 'text-gray-500',
                        'dark:text-gray-400');
                    submitBtn.classList.add('btn-primary', 'text-white');
                } else {
                    submitBtn.disabled = true;
                    submitBtn.classList.remove('btn-primary', 'text-white');
                    submitBtn.classList.add('bg-gray-300', 'dark:bg-gray-700', 'text-gray-500',
                        'dark:text-gray-400');
                }
            }
        }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.ctrlKey) {
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn && !submitBtn.disabled && e.target.id !== 'directAnswer') {
                window.quiz.submitAnswer();
            }
        }
    });
    </script>
</body>

</html>