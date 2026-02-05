<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../config/db.php';

checkAuth('employee');

define('PAGE_TITLE', 'New Photography Request');
require_once '../includes/header.php';
?>

<div class="container">
    <div class="dashboard-grid">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
                <li style="color: #666; font-size: 0.85rem; padding: 10px 15px 5px;">MEDIA / PHOTO</li>
                <li><a href="new_media_request.php" class="active"><i class="fas fa-camera"></i> New Photo/Video Req</a></li>
                <li><a href="my_media_requests.php"><i class="fas fa-film"></i> Media History</a></li>
            </ul>
        </aside>

        <main class="dashboard-content">
            <div class="card">
                <h3 style="color: var(--secondary-color);"><i class="fas fa-camera"></i> Photography & Video Request</h3>
                <form action="../handlers/media_handler.php" method="POST">
                    <input type="hidden" name="action" value="create_media_request">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label>Event Name / Meeting Title</label>
                            <input type="text" name="event_name" class="form-control" placeholder="e.g. Annual Space Exhibition" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Occasion</label>
                            <input type="text" name="occasion" class="form-control" placeholder="e.g. Hindi Day, Independence Day">
                        </div>
                        <div class="form-group">
                            <label>Venue / Location</label>
                            <input type="text" name="venue" class="form-control" placeholder="e.g. Auditorium" required>
                        </div>

                        <div class="form-group">
                            <label>Event Date</label>
                            <input type="date" name="event_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label>Event Start Time</label>
                            <input type="time" name="event_time" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Duration</label>
                            <input type="text" name="duration" class="form-control" placeholder="e.g. 2 Hours">
                        </div>
                         <div class="form-group">
                            <label>Quantity (Approx Photos/Sets)</label>
                            <input type="number" name="quantity" class="form-control" value="1">
                        </div>

                        <div class="form-group">
                            <label>Media Type</label>
                            <select name="media_type" class="form-control">
                                <option value="photo">Photography Only</option>
                                <option value="video">Video Recording Only</option>
                                <option value="both">Both Photo & Video</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Output Format</label>
                            <select name="output_format" class="form-control">
                                <option value="softcopy">Softcopy (Digital)</option>
                                <option value="hardcopy">Hardcopy (Prints)</option>
                                <option value="both">Both</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-top: 15px;">
                        <label>Additional Instructions</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Specific angles, VIPs to cover, etc."></textarea>
                    </div>

                    <div style="margin-top: 30px; text-align: right;">
                        <button type="reset" class="btn btn-danger" style="background: #6c757d;">Clear</button>
                        <button type="submit" class="btn btn-primary" style="background-color: var(--secondary-color);">Submit Media Request</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
