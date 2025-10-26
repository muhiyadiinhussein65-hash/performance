<?php
include 'inc/conn.php';

$summary_id = $_GET['summary_id'] ?? 1;
$student_id = $_SESSION['student_id'] ?? $_SESSION['user_id'] ?? 1;

// Get quiz data
$quiz_stmt = $conn->prepare("
    SELECT q.id, q.question_type, q.question_text, q.points
    FROM questions q
    WHERE q.summary_id = ?
    ORDER BY RAND()
    LIMIT 10
");
$quiz_stmt->bind_param("i", $summary_id);
$quiz_stmt->execute();
$questions_result = $quiz_stmt->get_result();
$questions = [];
while($q = $questions_result->fetch_assoc()) {
    $questions[] = $q;
}

$page_title = "Quiz";
$page_description = "Test your knowledge";
include 'head.php';
?>

<style>
.question-container {
    min-height: 60vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.fade-enter {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.progress-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: conic-gradient(var(--primary-color) 0deg, var(--light-color) 0deg);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.progress-circle::before {
    content: '';
    position: absolute;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: white;
}

.progress-circle-text {
    position: relative;
    z-index: 1;
    font-weight: bold;
    font-size: 0.875rem;
}
</style>

<div class="container-fluid" style="padding-bottom: 100px; max-width: 800px;">
    
    <!-- Header with Progress -->
    <div class="row py-3 sticky-top bg-white shadow-sm-custom">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <a href="javascript:history.back()" class="btn btn-light rounded-circle" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-x-lg"></i>
                </a>
                <div class="flex-grow-1 mx-3">
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar" id="progressBar" style="width: 0%"></div>
                    </div>
                    <div class="text-center small text-muted mt-1">
                        Question <span id="currentQuestion">1</span> of <span id="totalQuestions"><?php echo count($questions); ?></span>
                    </div>
                </div>
                <div class="progress-circle" id="timerCircle">
                    <span class="progress-circle-text" id="timerText">30</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quiz Container -->
    <div id="quizContainer" class="question-container mt-4">
        <!-- Questions will be loaded here via JavaScript -->
    </div>

</div>

<script>
const questions = <?php echo json_encode($questions); ?>;
let currentQuestionIndex = 0;
let score = 0;
let timer = 30;
let timerInterval;
let answers = [];

function showQuestion() {
    const question = questions[currentQuestionIndex];
    const progress = ((currentQuestionIndex + 1) / questions.length) * 100;
    
    document.getElementById('progressBar').style.width = progress + '%';
    document.getElementById('currentQuestion').textContent = currentQuestionIndex + 1;
    
    const container = document.getElementById('quizContainer');
    container.innerHTML = `
        <div class="card question-card fade-enter">
            <div class="card-body p-4">
                <!-- Question Number Badge -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge badge-primary">Question ${currentQuestionIndex + 1}</span>
                    <span class="badge badge-light">${question.points || 10} points</span>
                </div>
                
                <!-- Question Text -->
                <h3 class="h5 fw-bold mb-4">${question.question_text}</h3>
                
                <!-- Answer Options (Demo) -->
                <div class="d-grid gap-3" id="optionsContainer">
                    ${generateOptions(question)}
                </div>
            </div>
        </div>
    `;
    
    // Reset timer
    resetTimer();
}

function generateOptions(question) {
    // Demo options - in real app, fetch from database
    const demoOptions = [
        'Option A - First answer',
        'Option B - Second answer',
        'Option C - Third answer',
        'Option D - Fourth answer'
    ];
    
    return demoOptions.map((opt, index) => `
        <button class="option-btn" onclick="selectOption(${index})">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-primary" style="width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                    ${String.fromCharCode(65 + index)}
                </div>
                <span>${opt}</span>
            </div>
        </button>
    `).join('');
}

function selectOption(optionIndex) {
    clearInterval(timerInterval);
    
    // Mark option as selected
    const options = document.querySelectorAll('.option-btn');
    options[optionIndex].classList.add('selected');
    
    // Demo: mark as correct/incorrect
    const isCorrect = optionIndex === 0; // Demo: first option is always correct
    setTimeout(() => {
        if(isCorrect) {
            options[optionIndex].classList.add('correct');
            score += questions[currentQuestionIndex].points || 10;
        } else {
            options[optionIndex].classList.add('incorrect');
            options[0].classList.add('correct'); // Show correct answer
        }
        
        // Save answer
        answers.push({
            question_id: questions[currentQuestionIndex].id,
            selected: optionIndex,
            correct: isCorrect
        });
        
        // Move to next question after delay
        setTimeout(() => {
            if(currentQuestionIndex < questions.length - 1) {
                currentQuestionIndex++;
                showQuestion();
            } else {
                finishQuiz();
            }
        }, 2000);
    }, 500);
}

function resetTimer() {
    clearInterval(timerInterval);
    timer = 30;
    document.getElementById('timerText').textContent = timer;
    updateTimerCircle();
    
    timerInterval = setInterval(() => {
        timer--;
        document.getElementById('timerText').textContent = timer;
        updateTimerCircle();
        
        if(timer <= 0) {
            clearInterval(timerInterval);
            selectOption(-1); // Auto-submit with wrong answer
        }
    }, 1000);
}

function updateTimerCircle() {
    const percentage = (timer / 30) * 360;
    const circle = document.getElementById('timerCircle');
    circle.style.background = `conic-gradient(var(--primary-color) ${percentage}deg, var(--light-color) ${percentage}deg)`;
}

function finishQuiz() {
    clearInterval(timerInterval);
    
    // Save results to backend (demo)
    const container = document.getElementById('quizContainer');
    const percentage = Math.round((score / (questions.length * 10)) * 100);
    
    container.innerHTML = `
        <div class="card text-center fade-enter">
            <div class="card-body p-5">
                <div class="mb-4">
                    <i class="bi bi-trophy-fill text-warning" style="font-size: 5rem;"></i>
                </div>
                <h2 class="h3 fw-bold mb-2">Quiz Complete!</h2>
                <p class="text-muted mb-4">Great job on completing the quiz</p>
                
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="stat-card">
                            <div class="h4 fw-bold mb-1 text-primary">${score}</div>
                            <div class="small text-muted">Points Earned</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <div class="h4 fw-bold mb-1 text-accent">${percentage}%</div>
                            <div class="small text-muted">Score</div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="index.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-house-fill"></i> Back to Dashboard
                    </a>
                    <a href="leaderboard.php" class="btn btn-outline-primary">
                        <i class="bi bi-trophy"></i> View Leaderboard
                    </a>
                </div>
            </div>
        </div>
    `;
}

// Initialize
showQuestion();
</script>

<?php include 'inc/footer.php'; ?>
