<?php
/**
 * Edit/View Request
 */
require_once __DIR__ . '/../includes/auth.php';
// Use existing auth check
checkAuth('employee');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

// Helper Functions (Inlined for compatibility)
if (!function_exists('formatDate')) {
    function formatDate($date, $format = 'd M Y') {
        return date($format, strtotime($date));
    }
}
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount) {
        return '₹' . number_format($amount, 2);
    }
}

const CATEGORY_LABELS = [
    'breakfast' => 'Breakfast',
    'lunch' => 'Lunch',
    'snacks' => 'Snacks',
    'dinner' => 'Dinner',
    'beverages' => 'Beverages'
];

$requestId = (int)($_GET['id'] ?? 0);
$viewOnly = isset($_GET['view']);

if (!$requestId) {
    header("Location: my_requests.php?error=Invalid request");
    exit;
}

// Get request using PDO
$stmt = $pdo->prepare("SELECT * FROM catering_requests WHERE id = ? AND employee_id = ?");
$stmt->execute([$requestId, $_SESSION['user_id']]);
$request = $stmt->fetch();

if (!$request) {
    header("Location: my_requests.php?error=Request not found");
    exit;
}

// Can only edit pending requests
if ($request['status'] !== 'pending') {
    $viewOnly = true;
}

$pageTitle = $viewOnly ? 'View Request' : 'Edit Request';

// Get request items
$stmt = $pdo->prepare("SELECT ri.*, mi.item_name, mi.category FROM request_items ri 
     JOIN menu_items mi ON ri.item_id = mi.id 
     WHERE ri.request_id = ?");
$stmt->execute([$requestId]);
$requestItems = $stmt->fetchAll();

// Get all menu items for editing
$stmt = $pdo->query("SELECT * FROM menu_items WHERE is_available = 1 ORDER BY category, item_name");
$menuItems = $stmt->fetchAll();
$categories = CATEGORY_LABELS;

// Handle cancel request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_request'])) {
    if ($request['status'] === 'pending') {
        $stmt = $pdo->prepare("UPDATE catering_requests SET status = 'cancelled' WHERE id = ?");
        $stmt->execute([$requestId]);
        header("Location: my_requests.php?msg=Request cancelled successfully");
        exit;
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="form-page">
    <div class="card">
        <div class="card-header">
            <h3><?php echo $viewOnly ? 'Request Details' : 'Edit Request'; ?></h3>
        </div>
        <div class="card-body">
            <div class="flex-between mb-6" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h4 class="mb-0" style="margin-bottom: 0;"><?php echo htmlspecialchars($request['request_number']); ?></h4>
                    <small class="text-muted" style="color: #6c757d;">Created on <?php echo formatDate($request['created_at'], 'd M Y, h:i A'); ?></small>
                </div>
                <!-- Status Badge Styling -->
                <?php
                $statusColors = [
                    'pending' => '#ffc107', // Warning/Yellow
                    'approved' => '#28a745', // Success/Green
                    'rejected' => '#dc3545', // Danger/Red
                    'cancelled' => '#6c757d', // Secondary/Grey
                    'processing' => '#17a2b8', // Info/Blue
                    'completed' => '#28a745'  // Success/Green
                ];
                $bg = $statusColors[$request['status']] ?? '#6c757d';
                ?>
                <span class="badge" style="background-color: <?php echo $bg; ?>; color: white; padding: 8px 16px; border-radius: 20px; font-size: 0.9em; font-weight: 600; text-transform: capitalize;">
                    <?php echo ucfirst(str_replace('_', ' ', $request['status'])); ?>
                </span>
            </div>
            
            <div class="form-section" style="margin-bottom: 2rem;">
                <h4 class="form-section-title" style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; color: #333;">Event Details</h4>
                
                <div class="form-row" style="display: flex; gap: 20px; margin-bottom: 15px;">
                    <div class="form-group" style="flex:1;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Event Name</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($request['event_name']); ?>" readonly style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9;">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Venue</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($request['venue']); ?>" readonly style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9;">
                    </div>
                </div>
                
                <div class="form-row" style="display: flex; gap: 20px; margin-bottom: 15px;">
                    <div class="form-group" style="flex:1;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Date</label>
                        <input type="text" class="form-control" value="<?php echo formatDate($request['event_date']); ?>" readonly style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9;">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Time</label>
                        <input type="text" class="form-control" value="<?php echo date('h:i A', strtotime($request['event_time'])); ?>" readonly style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9;">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Guests</label>
                        <input type="text" class="form-control" value="<?php echo $request['guest_count']; ?>" readonly style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9;">
                    </div>
                </div>
                
                <?php if ($request['purpose']): ?>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Purpose</label>
                    <textarea class="form-control" readonly style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9;"><?php echo htmlspecialchars($request['purpose']); ?></textarea>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="form-section" style="margin-bottom: 2rem;">
                <h4 class="form-section-title" style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; color: #333;">Order Items</h4>
                
                <div class="table-container">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa;">
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Item</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Category</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Qty</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Unit Price</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requestItems as $item): ?>
                            <tr>
                                <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($item['item_name']); ?></td>
                                <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo CATEGORY_LABELS[$item['category']] ?? $item['category']; ?></td>
                                <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo $item['quantity']; ?></td>
                                <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo formatCurrency($item['unit_price']); ?></td>
                                <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo formatCurrency($item['subtotal']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="text-align:right; font-weight:600; padding: 12px;">Total Amount:</td>
                                <td style="padding: 12px; font-weight:700; color:#2c3e50;">
                                    <?php echo formatCurrency($request['total_amount']); ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <?php if ($request['special_instructions']): ?>
            <div class="form-section" style="margin-bottom: 2rem;">
                <h4 class="form-section-title" style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; color: #333;">Special Instructions</h4>
                <p style="background: #f9f9f9; padding: 15px; border-radius: 4px;"><?php echo nl2br(htmlspecialchars($request['special_instructions'])); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if ($request['rejection_reason']): ?>
            <div class="alert alert-error" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <strong>Rejection Reason:</strong> <?php echo htmlspecialchars($request['rejection_reason']); ?>
            </div>
            <?php endif; ?>
            
            <div class="flex-between mt-6" style="display: flex; gap: 10px; margin-top: 30px;">
                <a href="my_requests.php" class="btn btn-secondary" style="background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Back to List</a>
                <?php if ($request['status'] === 'pending'): ?>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel this request?');">
                    <button type="submit" name="cancel_request" class="btn btn-danger" style="background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
                        Cancel Request
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
