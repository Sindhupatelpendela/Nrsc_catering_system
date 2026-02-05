<?php
/**
 * Create New Catering Request
 */
require_once __DIR__ . '/../includes/auth.php';

// Ensure requireRole exists
if (!function_exists('requireRole')) {
    function requireRole($role) {
        checkAuth($role);
    }
}
requireRole('employee');

$pageTitle = 'New Catering Request';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

// Helper Functions
if (!function_exists('fetchAll')) {
    function fetchAll($sql, $params = [], $types = "") {
        global $pdo;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
if (!function_exists('fetchOne')) {
    function fetchOne($sql, $params = [], $types = "") {
        global $pdo;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
if (!function_exists('insertAndGetId')) {
    function insertAndGetId($sql, $params = [], $types = "") {
        global $pdo;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $pdo->lastInsertId();
    }
}
if (!function_exists('executeQuery')) {
    function executeQuery($sql, $params = [], $types = "") {
        global $pdo;
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }
}
if (!function_exists('sanitize')) {
    function sanitize($input) { return htmlspecialchars(trim($input ?? '')); }
}
if (!function_exists('generateRequestNumber')) {
    function generateRequestNumber() { return 'REQ-' . date('Ymd') . '-' . rand(1000, 9999); }
}
if (!function_exists('redirect')) {
    function redirect($url, $msg, $type = 'success') {
        header("Location: $url?msg=" . urlencode($msg) . "&type=$type");
        exit;
    }
}
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount) { return '₹' . number_format($amount, 2); }
}

if (!defined('CATEGORY_LABELS')) {
    define('CATEGORY_LABELS', [
        'breakfast' => 'Breakfast',
        'lunch'     => 'Lunch',
        'snacks'    => 'Snacks',
        'dinner'    => 'Dinner',
        'beverages' => 'Beverages'
    ]);
}

$menuItems = fetchAll("SELECT * FROM menu_items WHERE is_available = 1 ORDER BY category, item_name");
$categories = CATEGORY_LABELS;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventName = sanitize($_POST['event_name'] ?? '');
    $eventDate = sanitize($_POST['event_date'] ?? '');
    $eventTime = sanitize($_POST['event_time'] ?? '');
    $venue = sanitize($_POST['venue'] ?? '');
    $guestCount = (int)($_POST['guest_count'] ?? 0);
    $purpose = sanitize($_POST['purpose'] ?? '');
    $instructions = sanitize($_POST['special_instructions'] ?? '');
    $items = $_POST['items'] ?? [];
    $quantities = $_POST['quantities'] ?? [];
    
    $errors = [];
    
    if (empty($eventName)) $errors[] = 'Event name is required';
    if (empty($eventDate)) $errors[] = 'Event date is required';
    if (empty($eventTime)) $errors[] = 'Event time is required';
    if (empty($venue)) $errors[] = 'Venue is required';
    if ($guestCount < 1) $errors[] = 'Guest count must be at least 1';
    if (empty($items)) $errors[] = 'Please select at least one menu item';
    
    if (empty($errors)) {
        global $pdo;
        $pdo->beginTransaction();
        try {
            $requestNumber = generateRequestNumber();
            $userId = $_SESSION['user_id'];
            
            $totalAmount = 0;
            foreach ($items as $idx => $itemId) {
                $item = fetchOne("SELECT price FROM menu_items WHERE id = ?", [$itemId], "i");
                if ($item) {
                    $qty = (int)($quantities[$idx] ?? 1);
                    $totalAmount += $item['price'] * $qty;
                }
            }
            
            $requestId = insertAndGetId(
                "INSERT INTO catering_requests (request_number, employee_id, event_name, event_date, event_time, venue, guest_count, purpose, special_instructions, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')",
                [$requestNumber, $userId, $eventName, $eventDate, $eventTime, $venue, $guestCount, $purpose, $instructions, $totalAmount],
                "sissssissd"
            );
            
            foreach ($items as $idx => $itemId) {
                $item = fetchOne("SELECT price FROM menu_items WHERE id = ?", [$itemId], "i");
                if ($item) {
                    $qty = (int)($quantities[$idx] ?? 1);
                    executeQuery("INSERT INTO request_items (request_id, item_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)", [$requestId, $itemId, $qty, $item['price'], $item['price'] * $qty], "iiidd");
                }
            }
            
            $pdo->commit();
            redirect('my_requests.php', 'Request ' . $requestNumber . ' created successfully!', 'success');
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Failed to create request: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="logo-area">
            <div class="logo-icon"><i class="fas fa-utensils"></i></div>
            <div class="logo-text">
                <h2>NRSC CATERING</h2>
                <span>Employee Portal</span>
            </div>
        </div>

        <ul class="nav-menu">
            <div class="menu-category">Menu</div>
            <li><a href="dashboard.php"><i class="fas fa-grid-2"></i> Dashboard</a></li>
            <li><a href="new_request.php" class="active"><i class="fas fa-plus-circle"></i> New Request</a></li>
            <li><a href="my_requests.php"><i class="fas fa-clock-rotate-left"></i> My Requests</a></li>
            
            <div class="menu-category">Account</div>
            <li><a href="#"><i class="fas fa-lock"></i> Change Password</a></li>
            <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <!-- Enterprise Header -->
        <div class="page-header">
             <p class="breadcrumb">Employee Portal / Requests</p>
            <h1>New Catering Request</h1>
        </div>

        <div class="form-page-container">
            <!-- Main Card Wrapper -->
            <form method="POST" action="" class="enterprise-card">
                
                <div class="request-grid">
                    <!-- LEFT: Event Info -->
                    <div class="form-section left">
                        <div class="section-title"><i class="fas fa-info-circle"></i> Event Details</div>
                        
                        <label>Event Name</label>
                        <input type="text" name="event_name" required placeholder="e.g. Annual Budget Review" value="<?php echo $_POST['event_name'] ?? ''; ?>">

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-top: 20px;">
                            <div>
                                <label>Event Date</label>
                                <div style="position: relative;">
                                    <input type="text" name="event_date" required min="<?php echo date('Y-m-d'); ?>" value="<?php echo $_POST['event_date'] ?? ''; ?>" style="background: white;">
                                    <i class="fas fa-calendar-alt calendar-icon"></i>
                                </div>
                            </div>
                            <div>
                                <label>Event Time</label>
                                <div style="position: relative;">
                                    <input type="text" name="event_time" required value="<?php echo $_POST['event_time'] ?? ''; ?>" style="background: white;">
                                    <i class="fas fa-clock calendar-icon"></i>
                                </div>
                            </div>
                        </div>

                        <label>Venue / Location</label>
                        <input type="text" name="venue" required placeholder="Room Number or Building Name" value="<?php echo $_POST['venue'] ?? ''; ?>">

                        <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
                             <div>
                                <label>Number of Guests</label>
                                <input type="number" name="guest_count" required min="1" max="500" value="<?php echo $_POST['guest_count'] ?? '10'; ?>">
                             </div>
                        </div>

                        <label>Purpose / Description</label>
                        <textarea name="purpose" rows="3" placeholder="Briefly describe the purpose of this meeting..."><?php echo $_POST['purpose'] ?? ''; ?></textarea>
                    </div>

                    <!-- RIGHT: Menu -->
                    <div class="form-section right">
                        <div class="section-title"><i class="fas fa-utensils"></i> Menu Selection</div>

                        <label style="margin-top:0;">Add Items</label>
                        <div style="display: flex; gap: 15px; align-items: stretch; margin-bottom: 25px;">
                            <select id="menu-selector" style="flex: 1; margin-bottom: 0;">
                                <option value="">-- Select Item --</option>
                                <?php foreach ($categories as $catKey => $catLabel): ?>
                                    <optgroup label="<?php echo $catLabel; ?>">
                                        <?php foreach ($menuItems as $item): ?>
                                            <?php if ($item['category'] === $catKey): ?>
                                                <option value="<?php echo $item['id']; ?>" data-name="<?php echo htmlspecialchars($item['item_name']); ?>" data-price="<?php echo $item['price']; ?>">
                                                    <?php echo htmlspecialchars($item['item_name']); ?> - <?php echo formatCurrency($item['price']); ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                            
                            <div class="qty-group" style="background: white; border: 2px solid #94A3B8; padding: 5px; margin-right: 0; display: flex; align-items: center; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                                <button type="button" onclick="decreaseManualQty()" style="width: 44px; height: 44px; background: #F1F5F9; border: 2px solid #E2E8F0; border-radius: 10px; color: #64748B; cursor: pointer; font-size: 1.2rem; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-minus"></i>
                                </button>
                                
                                <input type="number" id="manual-qty" value="1" min="1" style="width: 70px; border:none; text-align: center; font-size: 1.5rem; font-weight: 800; color: #1E293B; -moz-appearance: textfield; background: transparent;" placeholder="1">
                                
                                <button type="button" onclick="addDisplayedItem()" class="btn-primary" title="Add to List" style="width: 55px; height: 44px; border-radius: 10px; font-size: 1.4rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(234, 88, 12, 0.25);">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <label>Selected Items <span id="item-count-badge" style="background: #E2E8F0; color: #475569; padding: 2px 8px; border-radius: 12px; font-size: 0.9rem; margin-left: 5px;">0</span></label>
                        <div id="selected-items" class="item-list-container">
                            <p id="no-items-msg" style="color: #94A3B8; text-align: center; margin-top: 40px;">
                                No items added yet.
                            </p>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: white; padding: 15px; border-radius: 10px; border: 1px solid #E2E8F0;">
                            <div style="display: flex; gap: 20px;">
                                <div>
                                    <span style="font-size: 0.9rem; font-weight: 600; color: #64748B; display: block;">Total Qty</span>
                                    <span id="total-qty-display" style="font-size: 1.2rem; font-weight: 700; color: #1E293B;">0</span>
                                </div>
                                <div>
                                    <span style="font-size: 0.9rem; font-weight: 600; color: #64748B; display: block;">Est. Cost</span>
                                    <span id="grand-total" style="font-size: 1.2rem; font-weight: 800; color: #EA580C;">₹0.00</span>
                                </div>
                            </div>
                        </div>

                        <label>Special Instructions</label>
                        <textarea name="special_instructions" rows="2" placeholder="Dietary restrictions (e.g. Veg only, No nuts)"><?php echo $_POST['special_instructions'] ?? ''; ?></textarea>
                    </div>
                </div>

                <!-- Footer Operations -->
                <div class="form-footer">
                    <a href="dashboard.php" class="btn-lg btn-secondary">Cancel</a>
                    <button type="submit" class="btn-lg btn-primary">
                        Submit Request <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
let items = [];


function decreaseManualQty() {
    const qtyInput = document.getElementById('manual-qty');
    let currentValue = parseInt(qtyInput.value) || 1;
    if (currentValue > 1) {
        qtyInput.value = currentValue - 1;
    }
}


function addDisplayedItem() {
    const selector = document.getElementById('menu-selector');
    const qtyInput = document.getElementById('manual-qty');
    const option = selector.options[selector.selectedIndex];

    if (!option.value) {
        alert("Please select a food item first.");
        return;
    }

    const id = option.value;
    const name = option.getAttribute('data-name');
    const price = parseFloat(option.getAttribute('data-price'));
    const qty = parseInt(qtyInput.value) || 1;

    const existing = items.find(i => i.id === id);
    if (existing) {
        existing.qty += qty;
    } else {
        items.push({ id, name, price, qty });
    }
    
    // Reset inputs
    renderItems();
    selector.selectedIndex = 0;
    qtyInput.value = 1;
}

function updateQty(id, delta) {
    const item = items.find(i => i.id === id);
    if (item) {
        item.qty += delta;
        if (item.qty < 1) item.qty = 1;
        renderItems();
    }
}

function removeItem(id) {
    items = items.filter(i => i.id !== id);
    renderItems();
}




function renderItems() {
    const container = document.getElementById('selected-items');
    const countBadge = document.getElementById('item-count-badge');
    const totalQtyDisplay = document.getElementById('total-qty-display');
    const grandTotalDisplay = document.getElementById('grand-total');
    
    // Update unique item count
    countBadge.textContent = items.length;

    if (items.length === 0) {
        container.innerHTML = '<p id="no-items-msg" style="color: #94A3B8; text-align: center; margin-top: 40px;">No items added yet.</p>';
        grandTotalDisplay.textContent = '₹0.00';
        totalQtyDisplay.textContent = '0';
    } else {
        let html = '';
        let totalCost = 0;
        let totalQty = 0;

        items.forEach((item) => {
            const subtotal = item.price * item.qty;
            totalCost += subtotal;
            totalQty += item.qty;
            
            html += `
            <div class="selected-item-card">
                <div class="item-info">
                    <div class="item-name">${item.name}</div>
                    <div class="item-price">Price: ₹${item.price.toFixed(2)}</div>
                    <input type="hidden" name="items[]" value="${item.id}">
                </div>
                
                <div class="item-controls">
                    <!-- Quantity Controls -->
                    <div class="qty-group">
                        <button type="button" class="qty-btn" onclick="updateQty('${item.id}', -1)">
                            <i class="fas fa-minus"></i>
                        </button>
                        
                        <input type="text" name="quantities[]" class="qty-display" value="${item.qty}" readonly>
                        
                        <button type="button" class="qty-btn" onclick="updateQty('${item.id}', 1)">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>

                    <!-- Subtotal -->
                    <div class="item-subtotal">
                        ₹${subtotal.toFixed(2)}
                    </div>
                    
                    <!-- Remove Button -->
                    <button type="button" class="remove-btn" onclick="removeItem('${item.id}')" title="Remove Item">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>`;
        });
        
        container.innerHTML = html;
        grandTotalDisplay.textContent = '₹' + totalCost.toFixed(2);
        totalQtyDisplay.textContent = totalQty;
        
        // Auto-scroll to bottom if list is long
        container.scrollTop = container.scrollHeight;
    }
}
</script>

<script>
    // Initialize Professional Date Picker
    flatpickr("input[name='event_date']", {
        altInput: true,
        altFormat: "F j, Y", // e.g. February 3, 2026
        dateFormat: "Y-m-d",
        minDate: "today",
        disableMobile: "true", // Force custom UI even on mobile/tablet if preferred
        theme: "material_orange"
    });

    // Initialize Professional Time Picker
    flatpickr("input[name='event_time']", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        altInput: true,
        altFormat: "h:i K" // e.g. 02:30 PM
    });
</script>
