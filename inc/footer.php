<?php
$current_page = basename($_SERVER['PHP_SELF']);

function isActive($page_name, $current_page)
{
    return $page_name === $current_page ? 'active' : '';
}
?>

<!-- Bottom Navigation -->
<nav class="bottom-nav">
    <div class="container-fluid">
        <div class="row text-center">
            <div class="col">
                <a href="index.php" class="bottom-nav-item <?php echo isActive('index.php', $current_page); ?>">
                    <i class="bi bi-house-fill bottom-nav-icon"></i>
                    <span class="bottom-nav-label">Home</span>
                </a>
            </div>
            <div class="col">
                <a href="subject.php" class="bottom-nav-item <?php echo isActive('subject.php', $current_page); ?>">
                    <i class="bi bi-book-fill bottom-nav-icon"></i>
                    <span class="bottom-nav-label">Subjects</span>
                </a>
            </div>
            <div class="col">
                <a href="challenge.php" class="bottom-nav-item <?php echo isActive('challenge.php', $current_page); ?>">
                    <i class="bi bi-trophy-fill bottom-nav-icon"></i>
                    <span class="bottom-nav-label">Challenge</span>
                </a>
            </div>
            <div class="col">
                <a href="leaderboard.php" class="bottom-nav-item <?php echo isActive('leaderboard.php', $current_page); ?>">
                    <i class="bi bi-bar-chart-fill bottom-nav-icon"></i>
                    <span class="bottom-nav-label">Ranks</span>
                </a>
            </div>
            <!-- <div class="col">
                <a href="profile.php" class="bottom-nav-item <?php // echo isActive('profile.php', $current_page); 
                                                                ?>">
                    <i class="bi bi-person-fill bottom-nav-icon"></i>
                    <span class="bottom-nav-label">Profile</span>
                </a>
            </div> -->
        </div>
    </div>
</nav>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom Scripts -->
<script>
    // Add smooth scroll behavior
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add fade-in animation to cards
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.card, .stat-card, .subject-card').forEach(el => {
        observer.observe(el);
    });
</script>

</body>

</html>
