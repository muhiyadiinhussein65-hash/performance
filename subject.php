<?php
include 'inc/conn.php'; 

$subject_result = $conn->query("SELECT
    s.id,
    s.name,
    s.icon,
    COUNT(c.subject_id) AS total_chapters
FROM
    subjects s
LEFT JOIN chapters c ON
    c.subject_id = s.id
GROUP BY
    s.id,
    s.name,
    s.icon
ORDER BY
    s.id");

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

$colorClasses = [
    'primary', 'info', 'success', 'warning', 'danger', 'purple'
];

$page_title = "Subjects";
$page_description = "Explore all available subjects and start learning";
include 'head.php'; 
?>

<div class="container-fluid" style="padding-bottom: 100px;">
    
    <!-- Header -->
    <div class="row py-3 sticky-top bg-white shadow-sm-custom">
        <div class="col-12">
            <div class="text-center">
                <h1 class="h3 fw-bold mb-0 text-primary">📚 All Subjects</h1>
                <p class="text-muted small mb-0">Choose a subject to start learning</p>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="input-group input-group-lg">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control border-start-0 ps-0" placeholder="Search subjects..." id="searchInput">
            </div>
        </div>
    </div>

    <!-- Subjects Grid -->
    <div class="row g-3 mt-2" id="subjectsGrid">
        <?php
        $count = 0;
        
        while ($row = $subject_result->fetch_assoc()) {
            $count++;
            $iconName = $row['icon'];
            $subject_id = $row['id'];
            $subject_name = htmlspecialchars($row['name']);
            $total_chapters = $row['total_chapters'];
            $progress = rand(40, 95);
            
            $iconClass = $iconMap[$iconName] ?? $iconMap['BookOpen'];
            $colorClass = $colorClasses[($count - 1) % count($colorClasses)];
            
            $borderColors = [
                'primary' => '#1b4a5a',
                'info' => '#3b82f6',
                'success' => '#22c55e',
                'warning' => '#f59e0b',
                'danger' => '#ef4444',
                'purple' => '#8b5cf6'
            ];
            
            $borderColor = $borderColors[$colorClass];
            ?>

        <div class="col-12 subject-item" data-name="<?php echo strtolower($subject_name); ?>">
            <a href="chapters.php?subject_id=<?php echo $subject_id; ?>" class="text-decoration-none">
                <div class="subject-card" style="border-left-color: <?php echo $borderColor; ?>;">
                    <div class="d-flex align-items-center gap-3">
                        <!-- Icon -->
                        <div class="subject-icon stat-icon-<?php echo $colorClass; ?>" style="width: 64px; height: 64px; font-size: 2rem;">
                            <i class="<?php echo $iconClass; ?>"></i>
                        </div>

                        <!-- Content -->
                        <div class="flex-grow-1">
                            <h3 class="h5 fw-bold mb-2"><?php echo $subject_name; ?></h3>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-journal-text"></i> <?php echo $total_chapters; ?> chapters
                            </p>

                            <!-- Progress Bar -->
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" style="width: <?php echo $progress; ?>%"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="text-muted small">Progress</span>
                                <span class="text-primary small fw-semibold"><?php echo $progress; ?>%</span>
                            </div>
                        </div>

                        <!-- Arrow -->
                        <div>
                            <i class="bi bi-chevron-right fs-4 text-muted"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <?php } ?>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="text-center mt-5 d-none">
        <i class="bi bi-search fs-1 text-muted"></i>
        <p class="text-muted mt-3">No subjects found</p>
    </div>

</div>

<script>
// Search functionality
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const subjectItems = document.querySelectorAll('.subject-item');
    let visibleCount = 0;
    
    subjectItems.forEach(item => {
        const subjectName = item.getAttribute('data-name');
        if (subjectName.includes(searchTerm)) {
            item.classList.remove('d-none');
            visibleCount++;
        } else {
            item.classList.add('d-none');
        }
    });
    
    document.getElementById('emptyState').classList.toggle('d-none', visibleCount > 0);
});
</script>

<?php include 'inc/footer.php'; ?>
