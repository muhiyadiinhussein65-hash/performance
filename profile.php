<?php
session_start();
include 'inc/conn.php';

// Get user data from session
$user_id = $_SESSION['user_id'] ?? 1; // Fallback for demo

// Fetch user profile data
$user_stmt = $conn->prepare("
    SELECT s.name, s.email, sp.total_score, sp.streak_days, sp.level
    FROM students s 
    LEFT JOIN student_progress sp ON s.id = sp.student_id 
    WHERE s.id = ?
");

if ($user_stmt) {
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user = $user_result->fetch_assoc();
    $user_stmt->close();
} else {
    $user = [
        'name' => 'Student',
        'email' => 'student@example.com',
        'total_score' => 0,
        'streak_days' => 0,
        'level' => 1
    ];
}

// Fetch recent activities
$activity_stmt = $conn->prepare("
    SELECT sa.answered_at, q.question_text, sub.name as subject_name, sa.points_earned
    FROM student_answers sa 
    JOIN questions q ON sa.question_id = q.id 
    JOIN summaries sm ON q.summary_id = sm.id 
    JOIN chapters c ON sm.chapter_id = c.id 
    JOIN subjects sub ON c.subject_id = sub.id 
    WHERE sa.student_id = ? 
    ORDER BY sa.answered_at DESC 
    LIMIT 5
");

$recent_activities = [];
if ($activity_stmt) {
    $activity_stmt->bind_param("i", $user_id);
    $activity_stmt->execute();
    $activity_result = $activity_stmt->get_result();
    while ($row = $activity_result->fetch_assoc()) {
        $recent_activities[] = $row;
    }
    $activity_stmt->close();
}

// Fetch badges count
$badge_stmt = $conn->prepare("SELECT COUNT(*) as badge_count FROM student_badges WHERE student_id = ?");
$badge_count = 0;
if ($badge_stmt) {
    $badge_stmt->bind_param("i", $user_id);
    $badge_stmt->execute();
    $badge_result = $badge_stmt->get_result();
    $badge_data = $badge_result->fetch_assoc();
    $badge_count = $badge_data['badge_count'] ?? 0;
    $badge_stmt->close();
}

// Calculate user data with fallbacks
$streak_days = $user['streak_days'] ?? 0;
$level = $user['level'] ?? 1;
$total_xp = $user['total_score'] ?? 0;
$user_name = $user['name'] ?? 'Student';
$email = $user['email'] ?? 'student@example.com';
$username = explode('@', $email)[0] ?? 'student';

// Calculate level progress
$current_level_xp = $total_xp % 1000;
$progress_percentage = ($current_level_xp / 1000) * 100;

$page_title = "Profile";
include 'head.php'; 
?>

<div class="container-fluid" style="padding-bottom: 100px;">
    
    <!-- Header -->
    <div class="row py-3 sticky-top bg-white shadow-sm-custom">
        <div class="col-12">
            <div class="text-center">
                <h1 class="h3 fw-bold mb-0">My Profile</h1>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            
            <!-- Profile Header Card -->
            <div class="card mb-3 border-0 shadow-sm-custom" style="background: linear-gradient(135deg, var(--bs-primary) 0%, #0d2630 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="position-relative">
                            <div class="avatar-large d-flex align-items-center justify-content-center text-white fw-bold" 
                                 style="width: 80px; height: 80px; background: var(--bs-accent); font-size: 2rem; border: 4px solid rgba(255,255,255,0.3);">
                                <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                            </div>
                            <div class="position-absolute bottom-0 end-0 bg-success rounded-circle" 
                                 style="width: 24px; height: 24px; border: 3px solid white;">
                            </div>
                        </div>
                        <div class="flex-grow-1 text-white">
                            <h2 class="h4 mb-1 fw-bold"><?php echo htmlspecialchars($user_name); ?></h2>
                            <p class="mb-1 opacity-75">@<?php echo htmlspecialchars($username); ?></p>
                            <div class="d-flex align-items-center gap-2 small">
                                <i class="bi bi-award-fill text-accent"></i>
                                <span>Level <?php echo $level; ?> Student</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="row g-3 mb-3">
                <div class="col-4">
                    <div class="card border-0 shadow-sm-custom h-100">
                        <div class="card-body p-3 text-center">
                            <div class="mb-2">
                                <i class="bi bi-star-fill" style="font-size: 1.5rem; color: var(--bs-accent);"></i>
                            </div>
                            <div class="h5 fw-bold mb-1"><?php echo number_format($total_xp); ?></div>
                            <div class="small text-muted">Total XP</div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card border-0 shadow-sm-custom h-100">
                        <div class="card-body p-3 text-center">
                            <div class="mb-2">
                                <i class="bi bi-fire" style="font-size: 1.5rem; color: #ff6b6b;"></i>
                            </div>
                            <div class="h5 fw-bold mb-1"><?php echo $streak_days; ?></div>
                            <div class="small text-muted">Day Streak</div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card border-0 shadow-sm-custom h-100">
                        <div class="card-body p-3 text-center">
                            <div class="mb-2">
                                <i class="bi bi-trophy-fill" style="font-size: 1.5rem; color: var(--bs-accent);"></i>
                            </div>
                            <div class="h5 fw-bold mb-1"><?php echo $badge_count; ?></div>
                            <div class="small text-muted">Badges</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Level Progress Card -->
            <div class="card mb-3 border-0 shadow-sm-custom">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h3 class="h6 fw-bold mb-0">
                            <i class="bi bi-graph-up-arrow text-primary"></i>
                            Level Progress
                        </h3>
                        <span class="badge" style="background: var(--bs-primary);">Level <?php echo $level; ?></span>
                    </div>
                    <div class="mb-2">
                        <div class="progress" style="height: 12px; background-color: var(--bs-light);">
                            <div class="progress-bar" 
                                 style="width: <?php echo $progress_percentage; ?>%; background: linear-gradient(90deg, var(--bs-primary), var(--bs-accent));"
                                 role="progressbar">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span><?php echo $current_level_xp; ?> / 1,000 XP</span>
                        <span>Next: Level <?php echo $level + 1; ?></span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Card -->
            <div class="card mb-3 border-0 shadow-sm-custom">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="h6 fw-bold mb-0">
                            <i class="bi bi-clock-history text-primary"></i>
                            Recent Activity
                        </h3>
                        <?php if(!empty($recent_activities)): ?>
                        <a href="#" class="small fw-semibold" style="color: var(--bs-primary);">View All</a>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($recent_activities)): ?>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach($recent_activities as $activity): ?>
                            <div class="p-2 rounded-3 d-flex align-items-start gap-2" style="background-color: var(--bs-light);">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 36px; height: 36px; background: rgba(27, 74, 90, 0.1);">
                                        <i class="bi bi-check-circle-fill" style="color: var(--bs-primary);"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="small fw-semibold text-truncate">
                                        <?php echo htmlspecialchars($activity['subject_name']); ?> Quiz
                                    </div>
                                    <div class="small text-muted">
                                        <span class="fw-semibold" style="color: var(--bs-accent);">
                                            +<?php echo $activity['points_earned']; ?> XP
                                        </span>
                                        • <?php echo date('M j, g:i A', strtotime($activity['answered_at'])); ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <div class="mb-2">
                                <i class="bi bi-inbox" style="font-size: 2.5rem; color: var(--bs-secondary); opacity: 0.3;"></i>
                            </div>
                            <p class="text-muted small mb-0">No recent activity</p>
                            <p class="text-muted small">Start learning to see your progress here!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row g-3">
                <div class="col-6">
                    <a href="subject.php" class="card border-0 shadow-sm-custom text-decoration-none h-100">
                        <div class="card-body p-3 text-center">
                            <div class="mb-2">
                                <i class="bi bi-book-fill" style="font-size: 2rem; color: var(--bs-primary);"></i>
                            </div>
                            <div class="small fw-semibold" style="color: var(--bs-dark);">Continue Learning</div>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="leaderboard.php" class="card border-0 shadow-sm-custom text-decoration-none h-100">
                        <div class="card-body p-3 text-center">
                            <div class="mb-2">
                                <i class="bi bi-bar-chart-fill" style="font-size: 2rem; color: var(--bs-accent);"></i>
                            </div>
                            <div class="small fw-semibold" style="color: var(--bs-dark);">Leaderboard</div>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>

<?php include 'inc/footer.php'; ?>
