<?php

include 'inc/conn.php';

// Get student data
$student_id = $_SESSION['student_id'] ?? $_SESSION['user_id'] ?? 1;

// Fetch student profile
$student_stmt = $conn->prepare("SELECT name, email FROM students WHERE id = ?");
$student_stmt->bind_param("i", $student_id);
$student_stmt->execute();
$student_result = $student_stmt->get_result();
$student = $student_result->fetch_assoc();
$student_name = $student ? explode(' ', $student['name'])[0] : 'Student';

// Fetch student progress
$progress_stmt = $conn->prepare("SELECT total_score, streak_days, level FROM student_progress WHERE student_id = ?");
$progress_stmt->bind_param("i", $student_id);
$progress_stmt->execute();
$progress_result = $progress_stmt->get_result();
$progress = $progress_result->fetch_assoc();

$total_xp = $progress['total_score'] ?? 12500;
$streak_days = $progress['streak_days'] ?? 5;
$level = $progress['level'] ?? 4;

// Fetch leaderboard top 3
$leaderboard_stmt = $conn->prepare("
    SELECT s.id, s.name, sp.total_score as xp 
    FROM students s 
    JOIN student_progress sp ON s.id = sp.student_id 
    ORDER BY sp.total_score DESC 
    LIMIT 3
");
$leaderboard_stmt->execute();
$leaderboard_result = $leaderboard_stmt->get_result();
$leaderboard = [];
while ($row = $leaderboard_result->fetch_assoc()) {
    $leaderboard[] = $row;
}

// Fetch subject progress
$subject_progress_stmt = $conn->prepare("
    SELECT s.id, s.name, COUNT(DISTINCT c.id) as total_chapters
    FROM subjects s
    LEFT JOIN chapters c ON s.id = c.subject_id
    GROUP BY s.id, s.name
    LIMIT 4
");
$subject_progress_stmt->execute();
$subject_progress_result = $subject_progress_stmt->get_result();
$subject_progress = [];
while ($row = $subject_progress_result->fetch_assoc()) {
    $subject_progress[] = $row;
}

// Fetch badge count
$badge_stmt = $conn->prepare("SELECT COUNT(*) as badge_count FROM student_badges WHERE student_id = ?");
$badge_stmt->bind_param("i", $student_id);
$badge_stmt->execute();
$badge_result = $badge_stmt->get_result();
$badge_count = $badge_result->fetch_assoc()['badge_count'] ?? 8;

$page_title = "Dashboard";
$page_description = "Your personalized learning dashboard";
include 'head.php';
?>

<!-- Main Container -->
<div class="container-fluid" style="padding-bottom: 100px;">
    
    <!-- Header -->
    <div class="row py-3 sticky-top bg-white shadow-sm-custom">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student_name); ?>&background=feae55&color=1b4a5a&size=56" 
                         alt="Avatar" class="avatar" style="width: 56px; height: 56px;">
                    <div>
                        <h1 class="h4 mb-0 fw-bold text-primary">Hi, <?php echo htmlspecialchars($student_name); ?>! 👋</h1>
                        <p class="text-muted small mb-0">Ready to learn today?</p>
                    </div>
                </div>
                <button class="btn btn-light rounded-circle p-2 position-relative" style="width: 48px; height: 48px;">
                    <i class="bi bi-bell-fill fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                        <span class="visually-hidden">New notifications</span>
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-3 mt-2">
        <div class="col-6">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon stat-icon-accent">
                        <i class="bi bi-stars"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total XP</div>
                        <div class="h4 fw-bold mb-0"><?php echo number_format($total_xp); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="stat-card" style="border-left-color: #ef4444;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);">
                        <i class="bi bi-fire"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Streak</div>
                        <div class="h4 fw-bold mb-0"><?php echo $streak_days; ?> days</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="stat-card" style="border-left-color: #22c55e;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon stat-icon-success">
                        <i class="bi bi-shield-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Level</div>
                        <div class="h4 fw-bold mb-0"><?php echo $level; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="stat-card" style="border-left-color: #8b5cf6;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Badges</div>
                        <div class="h4 fw-bold mb-0"><?php echo $badge_count; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row g-3 mt-3">
        <div class="col-6">
            <a href="subject.php" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-play-circle-fill"></i>
                <span>Continue Learning</span>
            </a>
        </div>
        <div class="col-6">
            <a href="challenge.php" class="btn btn-accent btn-lg w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-lightning-charge-fill"></i>
                <span>Challenge</span>
            </a>
        </div>
    </div>

    <!-- Leaderboard Preview -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h5 fw-bold mb-0">🏆 Top Players</h3>
                <a href="leaderboard.php" class="text-primary small text-decoration-none fw-semibold">View All →</a>
            </div>
            <div class="row g-3">
                <?php foreach($leaderboard as $index => $player): 
                    $rank_class = $index == 0 ? 'rank-1' : ($index == 1 ? 'rank-2' : 'rank-3');
                    $is_current = $player['id'] == $student_id;
                ?>
                <div class="col-12">
                    <div class="leaderboard-item <?php echo $is_current ? 'current-user' : ''; ?>">
                        <div class="rank-badge <?php echo $rank_class; ?>">
                            <?php echo $index + 1; ?>
                        </div>
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($player['name']); ?>&background=random&size=48" 
                             alt="<?php echo htmlspecialchars($player['name']); ?>" class="avatar">
                        <div class="flex-grow-1">
                            <div class="fw-semibold"><?php echo htmlspecialchars($player['name']); ?></div>
                            <div class="text-accent small fw-bold"><?php echo number_format($player['xp']); ?> XP</div>
                        </div>
                        <?php if($is_current): ?>
                        <span class="badge badge-accent">You</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Your Subjects -->
    <div class="card mt-4">
        <div class="card-body">
            <h3 class="h5 fw-bold mb-3">📚 Your Subjects</h3>
            <div class="row g-3">
                <?php foreach($subject_progress as $subject): 
                    $progress_percent = rand(40, 95);
                ?>
                <div class="col-12">
                    <a href="chapters.php?subject_id=<?php echo $subject['id']; ?>" class="text-decoration-none">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light-custom rounded-xl">
                            <div class="stat-icon stat-icon-primary" style="width: 48px; height: 48px; font-size: 1.5rem;">
                                <i class="bi bi-book-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold text-dark"><?php echo htmlspecialchars($subject['name']); ?></div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar" style="width: <?php echo $progress_percent; ?>%"></div>
                                </div>
                                <div class="text-muted small mt-1"><?php echo $progress_percent; ?>% Complete</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Daily Goal -->
    <div class="card card-primary mt-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="bi bi-target fs-2"></i>
                </div>
                <div>
                    <h3 class="h5 fw-bold mb-0">Daily Goal</h3>
                    <p class="mb-0 opacity-75 small">Complete 5 quizzes today</p>
                </div>
            </div>
            <div class="progress" style="height: 12px; background-color: rgba(255,255,255,0.3);">
                <div class="progress-bar bg-white" style="width: 60%"></div>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <span class="small opacity-75">3 of 5 completed</span>
                <span class="small fw-semibold">60%</span>
            </div>
        </div>
    </div>

</div>

<?php include 'inc/footer.php'; ?>
