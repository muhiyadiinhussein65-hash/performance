<?php
include 'inc/conn.php';
$subject_id = $_GET['subject_id'] ?? 0;

// Get subject data
$subject_stmt = $conn->prepare("SELECT id, name, icon FROM subjects WHERE id = ?");
$subject_stmt->bind_param("i", $subject_id);
$subject_stmt->execute();
$subject_result = $subject_stmt->get_result();
$subject = $subject_result->fetch_assoc();

// Get chapters
$chapters_stmt = $conn->prepare("
    SELECT c.id, c.title as title, c.chapter_number
    FROM chapters c
    WHERE c.subject_id = ?
    ORDER BY c.chapter_number, c.id
");
$chapters_stmt->bind_param("i", $subject_id);
$chapters_stmt->execute();
$chapters_result = $chapters_stmt->get_result();

$page_title = htmlspecialchars($subject['name'] ?? 'Chapters');
$page_description = "Explore chapters and lessons";
include 'head.php';

$iconMap = [
    'Calculator' => 'bi-calculator-fill',
    'Atom' => 'bi-atom',
    'BookOpen' => 'bi-book-half',
    'Book' => 'bi-book-fill',
    'MoonStar' => 'bi-moon-stars-fill',
    'SquareRoot' => 'bi-square-root-alt',
    'Dna' => 'bi-diagram-3-fill',
    'MapMarkedAlt' => 'bi-geo-alt-fill',
    'University' => 'bi-bank2',
    'Mosque' => 'bi-building'
];

$subject_icon = $iconMap[$subject['icon'] ?? 'Book'] ?? 'bi-book-fill';
?>

<div class="container-fluid" style="padding-bottom: 100px;">

    <!-- Header -->
    <div class="row py-3 sticky-top bg-white shadow-sm-custom">
        <div class="col-12">
            <div class="d-flex align-items-center gap-3">
                <a href="subject.php" class="btn btn-light rounded-circle" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div class="stat-icon stat-icon-primary" style="width: 48px; height: 48px; font-size: 1.5rem;">
                    <i class="<?php echo $subject_icon; ?>"></i>
                </div>
                <div>
                    <h1 class="h5 fw-bold mb-0"><?php echo htmlspecialchars($subject['name']); ?></h1>
                    <p class="text-muted small mb-0">Choose a chapter to begin</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject Overview Card -->
    <div class="card card-primary mt-3">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="<?php echo $subject_icon; ?> fs-1"></i>
                </div>
                <div>
                    <h2 class="h5 fw-bold mb-1"><?php echo htmlspecialchars($subject['name']); ?></h2>
                    <p class="mb-0 opacity-75">Master the fundamentals and advance your knowledge</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Card -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h3 class="h6 fw-bold mb-0">📊 Your Progress</h3>
                <span class="badge badge-light"><?php echo $chapters_result->num_rows; ?> chapters</span>
            </div>
            <div class="progress mb-2" style="height: 10px;">
                <div class="progress-bar" style="width: 35%"></div>
            </div>
            <div class="d-flex justify-content-between">
                <span class="small text-muted">Overall Completion</span>
                <span class="small fw-semibold text-primary">35%</span>
            </div>
        </div>
    </div>

    <!-- Chapters List -->
    <div class="mt-4">
        <h3 class="h6 fw-bold mb-3 text-primary">📖 All Chapters</h3>

        <div class="row g-3">
            <?php
            $chapter_count = 0;
            while ($chapter = $chapters_result->fetch_assoc()):
                $chapter_count++;
                $progress = rand(0, 100);
                $is_completed = $progress >= 100;
                $is_started = $progress > 0 && $progress < 100;
                $is_locked = $chapter_count > 3;
            ?>
                <div class="col-12">
                    <a href="<?php echo $is_locked ? '#' : 'lectures.php?chapter_id=' . $chapter['id'] . '&subject_id=' . $subject_id; ?>"
                        class="text-decoration-none <?php echo $is_locked ? 'pe-none' : ''; ?>">
                        <div class="card <?php echo $is_locked ? 'opacity-50' : ''; ?>">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-3">
                                    <!-- Chapter Number Badge -->
                                    <div class="<?php echo $is_completed ? 'stat-icon-success' : 'stat-icon-primary'; ?>"
                                        style="width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                                        <?php if ($is_locked): ?>
                                            <i class="bi bi-lock-fill fs-4"></i>
                                        <?php elseif ($is_completed): ?>
                                            <i class="bi bi-check-circle-fill fs-4"></i>
                                        <?php else: ?>
                                            <span class="fw-bold fs-5"><?php echo $chapter_count; ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Chapter Content -->
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h4 class="h6 fw-bold mb-0"><?php echo htmlspecialchars($chapter['title']); ?></h4>
                                            <?php if ($is_completed && !$is_locked): ?>
                                                <span class="badge bg-success">Completed</span>
                                            <?php elseif ($is_started && !$is_locked): ?>
                                                <span class="badge badge-accent">In Progress</span>
                                            <?php elseif ($is_locked): ?>
                                                <span class="badge bg-secondary">Locked</span>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (!$is_locked): ?>
                                            <!-- Progress Bar -->
                                            <div class="progress mb-2" style="height: 6px;">
                                                <div class="progress-bar bg-gradient" style="width: <?php echo $progress; ?>%"></div>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted small">
                                                    <i class="bi bi-journal-text"></i>
                                                    <?php echo rand(5, 15); ?> lessons
                                                </span>
                                                <span class="text-primary small fw-semibold"><?php echo $progress; ?>%</span>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-muted small mb-0">Complete previous chapters to unlock</p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Arrow Icon -->
                                    <?php if (!$is_locked): ?>
                                        <i class="bi bi-chevron-right fs-4 text-muted"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</div>

<?php include 'inc/footer.php'; ?>