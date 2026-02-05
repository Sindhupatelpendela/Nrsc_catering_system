<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../config/db.php';

checkAuth('employee');

// Fetch Media Requests
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM media_requests WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$requests = $stmt->fetchAll();

define('PAGE_TITLE', 'My Media Requests');
require_once '../includes/header.php';
?>

<div class="container">
    <div class="dashboard-grid">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
                <li style="color: #666; font-size: 0.85rem; padding: 10px 15px 5px;">MEDIA / PHOTO</li>
                <li><a href="new_media_request.php"><i class="fas fa-camera"></i> New Photo/Video Req</a></li>
                <li><a href="my_media_requests.php" class="active"><i class="fas fa-film"></i> Media History</a></li>
            </ul>
        </aside>

        <main class="dashboard-content">
            <div class="card">
                <h3 style="color: var(--secondary-color);">My Media Request History</h3>
                <?php if(isset($_GET['msg'])): ?>
                    <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                        <?php echo htmlspecialchars($_GET['msg']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (count($requests) > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Date/Time</th>
                                <th>Type</th>
                                <th>Format</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($requests as $req): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($req['event_name']); ?></strong>
                                        <?php if($req['occasion']): ?><br><small>Occasion: <?php echo htmlspecialchars($req['occasion']); ?></small><?php endif; ?>
                                    </td>
                                    <td><?php echo $req['event_date']; ?><br><?php echo $req['event_time']; ?></td>
                                    <td><?php echo ucfirst($req['media_type']); ?></td>
                                    <td><?php echo ucfirst($req['output_format']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $req['status']; ?>">
                                            <?php echo ucfirst($req['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No media requests found. <a href="new_media_request.php">Create one now</a>.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
