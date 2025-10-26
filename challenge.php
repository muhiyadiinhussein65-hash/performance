<?php
include 'inc/conn.php';

$student_id = $_SESSION['student_id'] ?? $_SESSION['user_id'] ?? 1;
$action = $_GET['action'] ?? 'home';

// Handle challenge creation
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_challenge'])) {
    $subject_id = $_POST['subject_id'] ?? 0;
    $chapter_id = $_POST['chapter_id'] ?? null;
    $question_types = isset($_POST['question_types']) ? implode(',', $_POST['question_types']) : 'multiple_choice';
    $num_questions = $_POST['num_questions'] ?? 10;
    
    // Generate unique match key
    $match_key = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
    
    $stmt = $conn->prepare("INSERT INTO challenges (match_key, creator_id, subject_id, chapter_id, question_types, num_questions, status) VALUES (?, ?, ?, ?, ?, ?, 'waiting')");
    $stmt->bind_param("siiisi", $match_key, $student_id, $subject_id, $chapter_id, $question_types, $num_questions);
    $stmt->execute();
    
    header("Location: challenge.php?action=view&key=$match_key");
    exit;
}

// Handle joining challenge
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_challenge'])) {
    $match_key = strtoupper($_POST['match_key'] ?? '');
    
    $stmt = $conn->prepare("SELECT id FROM challenges WHERE match_key = ? AND status = 'waiting'");
    $stmt->bind_param("s", $match_key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        header("Location: challenge.php?action=view&key=$match_key");
        exit;
    } else {
        $error = "Challenge not found or already started";
    }
}

$page_title = "Challenge Arena";
$page_description = "Challenge other students to quiz battles";
include 'head.php';

// Get subjects for dropdown
$subjects = $conn->query("SELECT id, name, icon FROM subjects ORDER BY name");
?>

<div class="container-fluid" style="padding-bottom: 100px;">
    
    <!-- Header -->
    <div class="row py-3 sticky-top bg-white shadow-sm-custom">
        <div class="col-12">
            <div class="text-center">
                <h1 class="h3 fw-bold mb-1">⚔️ Challenge Arena</h1>
                <p class="text-muted small mb-0">Compete with friends in quiz battles</p>
            </div>
        </div>
    </div>

    <!-- Hero Card -->
    <div class="card challenge-card mt-3">
        <div class="card-body text-center py-5">
            <i class="bi bi-lightning-charge-fill" style="font-size: 4rem; margin-bottom: 1rem;"></i>
            <h2 class="h4 fw-bold mb-2">Battle Mode</h2>
            <p class="mb-0 opacity-90">Challenge friends or join existing matches</p>
        </div>
    </div>

    <?php if($action === 'home'): ?>
    
    <!-- Action Buttons -->
    <div class="row g-3 mt-3">
        <div class="col-12">
            <button class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center gap-2" 
                    data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Create Challenge</span>
            </button>
        </div>
        <div class="col-12">
            <button class="btn btn-accent btn-lg w-100 d-flex align-items-center justify-content-center gap-2"
                    data-bs-toggle="modal" data-bs-target="#joinModal">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Join with Code</span>
            </button>
        </div>
    </div>

    <!-- How It Works -->
    <div class="card mt-4">
        <div class="card-body">
            <h3 class="h5 fw-bold mb-3">📝 How It Works</h3>
            <div class="d-flex flex-column gap-3">
                <div class="d-flex gap-3">
                    <div class="stat-icon-primary" style="width: 40px; height: 40px; flex-shrink: 0; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <strong>1</strong>
                    </div>
                    <div>
                        <div class="fw-semibold">Create a Challenge</div>
                        <div class="small text-muted">Choose subject, chapter, and question types</div>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <div class="stat-icon-accent" style="width: 40px; height: 40px; flex-shrink: 0; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <strong>2</strong>
                    </div>
                    <div>
                        <div class="fw-semibold">Share the Code</div>
                        <div class="small text-muted">Send the match key to your friends</div>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <div class="stat-icon-success" style="width: 40px; height: 40px; flex-shrink: 0; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <strong>3</strong>
                    </div>
                    <div>
                        <div class="fw-semibold">Battle & Win</div>
                        <div class="small text-muted">Answer questions and see who scores higher</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Challenges -->
    <div class="card mt-4">
        <div class="card-body">
            <h3 class="h5 fw-bold mb-3">⏱️ Recent Challenges</h3>
            <div class="text-center text-muted py-4">
                <i class="bi bi-inbox fs-1 opacity-50"></i>
                <p class="mt-2">No challenges yet. Create one to get started!</p>
            </div>
        </div>
    </div>

    <?php elseif($action === 'view' && isset($_GET['key'])): 
        $match_key = $_GET['key'];
        $stmt = $conn->prepare("SELECT c.*, s.name as subject_name FROM challenges c JOIN subjects s ON c.subject_id = s.id WHERE c.match_key = ?");
        $stmt->bind_param("s", $match_key);
        $stmt->execute();
        $challenge = $stmt->get_result()->fetch_assoc();
        
        if($challenge):
    ?>
    
    <!-- Challenge Details -->
    <div class="card mt-3">
        <div class="card-body text-center">
            <h3 class="h5 fw-bold mb-3">Match Key</h3>
            <div class="match-key mb-3">
                <?php echo htmlspecialchars($match_key); ?>
            </div>
            <button class="btn btn-sm btn-light" onclick="navigator.clipboard.writeText('<?php echo $match_key; ?>')">
                <i class="bi bi-clipboard"></i> Copy Code
            </button>
        </div>
    </div>

    <!-- Challenge Info -->
    <div class="card mt-3">
        <div class="card-body">
            <h3 class="h6 fw-bold mb-3">Challenge Details</h3>
            <div class="row g-2">
                <div class="col-6">
                    <div class="small text-muted">Subject</div>
                    <div class="fw-semibold"><?php echo htmlspecialchars($challenge['subject_name']); ?></div>
                </div>
                <div class="col-6">
                    <div class="small text-muted">Questions</div>
                    <div class="fw-semibold"><?php echo $challenge['num_questions']; ?></div>
                </div>
                <div class="col-12">
                    <div class="small text-muted">Question Types</div>
                    <div class="fw-semibold"><?php echo ucwords(str_replace(',', ', ', $challenge['question_types'])); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Button -->
    <div class="mt-3">
        <a href="quiz.php?challenge_key=<?php echo $match_key; ?>" class="btn btn-primary btn-lg w-100">
            <i class="bi bi-play-fill"></i> Start Battle
        </a>
    </div>

    <?php else: ?>
    <div class="alert alert-warning mt-3">Challenge not found</div>
    <?php endif; ?>
    
    <?php endif; ?>

</div>

<!-- Create Challenge Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Create New Challenge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Subject</label>
                        <select class="form-select" name="subject_id" required>
                            <option value="">Choose a subject...</option>
                            <?php while($subject = $subjects->fetch_assoc()): ?>
                            <option value="<?php echo $subject['id']; ?>"><?php echo htmlspecialchars($subject['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Number of Questions</label>
                        <select class="form-select" name="num_questions">
                            <option value="5">5 Questions</option>
                            <option value="10" selected>10 Questions</option>
                            <option value="15">15 Questions</option>
                            <option value="20">20 Questions</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Question Types</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="question_types[]" value="multiple_choice" id="mc" checked>
                            <label class="form-check-label" for="mc">Multiple Choice</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="question_types[]" value="true_false" id="tf">
                            <label class="form-check-label" for="tf">True/False</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="question_types[]" value="direct" id="direct">
                            <label class="form-check-label" for="direct">Direct Answer</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_challenge" class="btn btn-primary">Create Challenge</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Join Challenge Modal -->
<div class="modal fade" id="joinModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Join Challenge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Enter Match Key</label>
                        <input type="text" class="form-control form-control-lg text-center" name="match_key" 
                               placeholder="ABC123" maxlength="6" style="letter-spacing: 0.2em; text-transform: uppercase;" required>
                        <div class="form-text">Enter the 6-character code shared by your friend</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="join_challenge" class="btn btn-accent">Join Battle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
