<?php
include 'inc/conn.php';

$student_id = $_SESSION['student_id'] ?? $_SESSION['user_id'] ?? 1;

// Get leaderboard data
$leaderboard_stmt = $conn->prepare("
    SELECT s.id, s.name, sp.total_score as xp, sp.level
    FROM students s 
    JOIN student_progress sp ON s.id = sp.student_id 
    ORDER BY sp.total_score DESC 
    LIMIT 50
");
$leaderboard_stmt->execute();
$leaderboard_result = $leaderboard_stmt->get_result();
$leaderboard = [];
while ($row = $leaderboard_result->fetch_assoc()) {
    $leaderboard[] = $row;
}

$page_title = "Leaderboard";
$page_description = "See where you rank among all students";
include 'head.php';
?>

<div class="container-fluid" style="padding-bottom: 100px;">
    
    <!-- Header -->
    <div class="row py-3 sticky-top bg-white shadow-sm-custom">
        <div class="col-12">
            <div class="text-center">
                <h1 class="h3 fw-bold mb-1">
                    <i class="bi bi-trophy-fill" style="color: var(--bs-accent);"></i>
                    Leaderboard
                </h1>
                <p class="small text-muted mb-0">Compete with the best students</p>
            </div>
        </div>
    </div>

    <!-- Top 3 Podium -->
    <div class="row mt-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm-custom" style="background: linear-gradient(135deg, var(--bs-primary) 0%, #0d2630 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-end justify-content-center gap-3">
                        
                        <?php if(isset($leaderboard[1])): ?>
                        <!-- Second Place -->
                        <div class="text-center" style="flex: 0 0 auto; width: 90px;">
                            <div class="position-relative mb-2">
                                <div class="avatar rounded-circle mx-auto d-flex align-items-center justify-content-center fw-bold text-white" 
                                     style="width: 70px; height: 70px; background: linear-gradient(135deg, #C0C0C0, #A8A8A8); border: 3px solid #C0C0C0; font-size: 1.5rem;">
                                    <?php echo strtoupper(substr($leaderboard[1]['name'], 0, 1)); ?>
                                </div>
                                <div class="position-absolute top-0 end-0">
                                    <div class="badge rounded-circle d-flex align-items-center justify-content-center fw-bold" 
                                         style="width: 28px; height: 28px; background: #C0C0C0; color: white; font-size: 0.875rem;">
                                        2
                                    </div>
                                </div>
                            </div>
                            <p class="fw-bold small mb-0 text-white text-truncate"><?php echo htmlspecialchars($leaderboard[1]['name']); ?></p>
                            <p class="small mb-0" style="color: #C0C0C0;"><?php echo number_format($leaderboard[1]['xp']); ?> XP</p>
                        </div>
                        <?php endif; ?>

                        <?php if(isset($leaderboard[0])): ?>
                        <!-- First Place -->
                        <div class="text-center" style="flex: 0 0 auto; width: 100px;">
                            <div class="mb-2">
                                <i class="bi bi-trophy-fill d-block mb-2" style="font-size: 2rem; color: var(--bs-accent); filter: drop-shadow(0 2px 8px rgba(254,174,85,0.5));"></i>
                            </div>
                            <div class="position-relative mb-2">
                                <div class="avatar rounded-circle mx-auto d-flex align-items-center justify-content-center fw-bold text-white" 
                                     style="width: 85px; height: 85px; background: linear-gradient(135deg, #FFD700, #FFA500); border: 4px solid var(--bs-accent); font-size: 2rem; box-shadow: 0 4px 12px rgba(254,174,85,0.4);">
                                    <?php echo strtoupper(substr($leaderboard[0]['name'], 0, 1)); ?>
                                </div>
                            </div>
                            <p class="fw-bold mb-0 text-white text-truncate"><?php echo htmlspecialchars($leaderboard[0]['name']); ?></p>
                            <p class="fw-bold mb-0" style="color: var(--bs-accent);"><?php echo number_format($leaderboard[0]['xp']); ?> XP</p>
                        </div>
                        <?php endif; ?>

                        <?php if(isset($leaderboard[2])): ?>
                        <!-- Third Place -->
                        <div class="text-center" style="flex: 0 0 auto; width: 90px;">
                            <div class="position-relative mb-2">
                                <div class="avatar rounded-circle mx-auto d-flex align-items-center justify-content-center fw-bold text-white" 
                                     style="width: 70px; height: 70px; background: linear-gradient(135deg, #CD7F32, #B8792F); border: 3px solid #CD7F32; font-size: 1.5rem;">
                                    <?php echo strtoupper(substr($leaderboard[2]['name'], 0, 1)); ?>
                                </div>
                                <div class="position-absolute top-0 end-0">
                                    <div class="badge rounded-circle d-flex align-items-center justify-content-center fw-bold" 
                                         style="width: 28px; height: 28px; background: #CD7F32; color: white; font-size: 0.875rem;">
                                        3
                                    </div>
                                </div>
                            </div>
                            <p class="fw-bold small mb-0 text-white text-truncate"><?php echo htmlspecialchars($leaderboard[2]['name']); ?></p>
                            <p class="small mb-0" style="color: #CD7F32;"><?php echo number_format($leaderboard[2]['xp']); ?> XP</p>
                        </div>
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Full Rankings List -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex flex-column gap-2">
                <?php 
                foreach($leaderboard as $index => $player):
                    $rank = $index + 1;
                    $is_current = $player['id'] == $student_id;
                    if($rank <= 3) continue; // Skip top 3 already shown
                ?>
                <div class="card border-0 shadow-sm-custom <?php echo $is_current ? 'border-2' : ''; ?>" 
                     style="<?php echo $is_current ? 'border: 2px solid var(--bs-accent) !important; background: linear-gradient(135deg, rgba(254,174,85,0.05), rgba(254,174,85,0.02));' : ''; ?>">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center fw-bold rounded-circle flex-shrink-0" 
                                 style="width: 40px; height: 40px; background: var(--bs-light); color: var(--bs-primary);">
                                <?php echo $rank; ?>
                            </div>
                            <div class="avatar rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0" 
                                 style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--bs-primary), var(--bs-accent)); font-size: 1.25rem;">
                                <?php echo strtoupper(substr($player['name'], 0, 1)); ?>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="min-w-0 flex-grow-1 me-2">
                                        <div class="fw-semibold text-truncate">
                                            <?php echo htmlspecialchars($player['name']); ?>
                                            <?php if($is_current): ?>
                                            <span class="badge ms-1" style="background: var(--bs-accent); font-size: 0.7rem;">You</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="small text-muted">Level <?php echo $player['level']; ?></div>
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <div class="fw-bold" style="color: var(--bs-accent);">
                                            <?php echo number_format($player['xp']); ?>
                                        </div>
                                        <div class="small text-muted">XP</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Your Rank Card (if not in visible list) -->
    <?php 
    $current_rank = 0;
    $current_player = null;
    foreach($leaderboard as $index => $player) {
        if($player['id'] == $student_id) {
            $current_rank = $index + 1;
            $current_player = $player;
            break;
        }
    }
    if($current_rank > 10):
    ?>
    <div class="card mt-3 border-0 shadow-sm-custom" style="border: 2px solid var(--bs-accent) !important; background: linear-gradient(135deg, rgba(254,174,85,0.1), rgba(254,174,85,0.05));">
        <div class="card-body p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center fw-bold rounded-circle flex-shrink-0" 
                     style="width: 50px; height: 50px; background: var(--bs-accent); color: white; font-size: 1.25rem;">
                    #<?php echo $current_rank; ?>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold">Your Current Rank</div>
                    <div class="small text-muted">
                        Keep learning to climb higher! You're in the top <?php echo ceil(($current_rank / count($leaderboard)) * 100); ?>%
                    </div>
                </div>
                <div class="text-end">
                    <div class="fw-bold" style="color: var(--bs-accent);">
                        <?php echo number_format($current_player['xp']); ?>
                    </div>
                    <div class="small text-muted">XP</div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php include 'inc/footer.php'; ?>
