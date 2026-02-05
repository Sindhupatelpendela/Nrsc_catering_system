<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../config/db.php';

checkAuth('officer');

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // ACTION: Remove Profile Picture
    if (isset($_POST['action']) && $_POST['action'] === 'remove_photo') {
        try {
            // Get current image to delete file
            $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $curr = $stmt->fetch();
            
            if ($curr && $curr['profile_image']) {
                $file_path = "../uploads/profiles/" . $curr['profile_image'];
                if (file_exists($file_path)) {
                    unlink($file_path); // Delete file
                }
            }

            // Update DB
            $stmt = $pdo->prepare("UPDATE users SET profile_image = NULL WHERE id = ?");
            $stmt->execute([$user_id]);
            
            $message = "Profile picture removed.";
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
    
    // ACTION: Update Details / Upload Image
    else {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        
        // Handle Cropped Image (Base64)
        $profile_image = null;
        if (!empty($_POST['cropped_image'])) {
            $data = $_POST['cropped_image'];
            
            // Extract Base64 Data
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            
            $new_filename = "profile_" . $user_id . "_" . time() . ".png";
            $upload_dir = "../uploads/profiles/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            
            if (file_put_contents($upload_dir . $new_filename, $data)) {
                $profile_image = $new_filename;
            } else {
                $error = "Failed to save cropped image.";
            }
        }

        if (!$error) {
            try {
                if ($profile_image) {
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ?, profile_image = ? WHERE id = ?");
                    $stmt->execute([$name, $phone, $profile_image, $user_id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
                    $stmt->execute([$name, $phone, $user_id]);
                }
                $message = "Profile details updated successfully!";
                $_SESSION['full_name'] = $name;
            } catch (PDOException $e) {
                $error = "Database Error: " . $e->getMessage();
            }
        }
    }
}

// Fetch User Data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Sidebar Image Logic
$sidebar_image = $user['profile_image'] ? "../uploads/profiles/" . $user['profile_image'] : "";
$initials = strtoupper(substr($user['name'], 0, 2));

define('PAGE_TITLE', 'My Profile');
include '../includes/header.php';
?>

<!-- Cropper.js Dependencies -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

<style>
    /* --- ANIMATIONS & LAYOUT --- */
    :root {
        --primary-color: #EA580C;
        --secondary-color: #1E293B;
        --text-muted: #64748B;
        --bg-light: #F8FAFC;
    }

    /* Modal Styles */
    .crop-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.85); /* Darker overlay */
        backdrop-filter: blur(5px);
        align-items: center;
        justify-content: center;
    }
    .crop-container {
        background: white;
        padding: 30px;
        border-radius: 20px;
        width: 95%;
        max-width: 650px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    .img-container {
        height: 500px;
        margin-bottom: 25px;
        background: #0f172a;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }
    .img-container img {
        display: block;
        max-width: 100%;
    }
    .controls {
        display: flex;
        gap: 20px;
        justify-content: center;
        margin-bottom: 25px;
    }

    /* --- SIDEBAR ENHANCEMENTS (Local Override) --- */
    .sidebar .nav-item {
        font-size: 1.6rem !important; /* Huge Menu Links */
        padding: 22px 30px !important;
        margin-bottom: 15px !important;
        font-weight: 700 !important;
        letter-spacing: 0.5px;
        color: #1E3A8A !important; /* Dark Blue */
        border-radius: 16px;
    }
    .sidebar .nav-item.active {
        background: #EFF6FF !important;
        color: #1E40AF !important; /* Slightly lighter blue for active */
    }
    .sidebar .nav-item i {
        color: #1E3A8A !important; /* Icons Dark Blue too */
        font-size: 1.6rem !important;
        width: 35px;
    }
    .sidebar .profile-name {
        font-size: 2.2rem !important; /* Huge Profile Name */
        margin-top: 25px;
        margin-bottom: 10px;
        color: #172554 !important; /* Very Dark Blue */
    }
    .sidebar .profile-role-badge {
        font-size: 1.2rem !important;
        padding: 10px 20px !important;
        background: #DBEAFE !important;
        color: #1E3A8A !important; /* Dark Blue Text */
        font-weight: 800 !important;
    }
    .sidebar .btn-profile-sm {
        font-size: 1.2rem !important;
        padding: 12px 30px !important;
        color: #1E3A8A !important;
        border: 2px solid #BFDBFE !important;
        font-weight: 700 !important;
    }

    /* --- PROFESSIONAL FORM STYLING --- */
    .form-label-lg {
        font-size: 1.8rem !important; /* Increased from 1.6rem */
        color: var(--secondary-color) !important;
        font-weight: 700 !important;
        margin-bottom: 15px !important;
        display: block;
        letter-spacing: 0.5px;
    }
    .form-input-lg {
        width: 100%;
        padding: 24px 30px !important; /* More comfortable padding */
        font-size: 1.8rem !important; /* Larger input text */
        color: #334155 !important;
        border: 2px solid #E2E8F0 !important;
        border-radius: 16px !important;
        background: #F8FAFC;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    .form-input-lg:focus {
        border-color: var(--primary-color) !important;
        background: #fff;
        box-shadow: 0 0 0 5px rgba(234, 88, 12, 0.1);
        outline: none;
        transform: translateY(-2px);
    }
    .readonly-field {
        background: #F1F5F9 !important;
        color: #94A3B8 !important;
        cursor: not-allowed;
        border-color: #E2E8F0 !important;
    }
    
    /* Layout Cards */
    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="dashboard-container">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="logo-area">
            <img src="../assets/nrsc_logo_circle.png" alt="NRSC Logo" class="logo-img">
            <div class="logo-text">
                <h2>NRSC CATERING</h2>
            </div>
        </div>

        <div class="user-profile-sidebar">
            <?php if($sidebar_image): ?>
                <img src="<?php echo $sidebar_image; ?>" class="profile-circle" style="object-fit:cover; border: 4px solid #3B82F6;">
            <?php else: ?>
                <div class="profile-circle"><?php echo $initials; ?></div>
            <?php endif; ?>
            
            <div class="profile-name"><?php echo htmlspecialchars($current_user['name']); ?></div>
            <div class="profile-role-badge">APPROVING OFFICER</div>
            <button class="btn-profile-sm" onclick="window.location.href='profile.php'">My Profile</button>
        </div>

        <ul class="nav-menu">
            <li><a href="dashboard.php" class="nav-item">
                <i class="fas fa-th-large"></i> Dashboard
            </a></li>
            <li><a href="dashboard.php?tab=approved" class="nav-item">
                <i class="fas fa-check-circle"></i> Approved Orders
            </a></li>
            <li><a href="dashboard.php?tab=completed" class="nav-item">
                <i class="fas fa-clipboard-check"></i> Completed Orders
            </a></li>
            <div style="flex: 1;"></div> 
            <li><a href="../auth/change_password.php" class="nav-item"><i class="fas fa-key"></i> Change Password</a></li>
            <li><a href="../auth/logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header" style="margin-bottom: 60px;">
            <div>
                 <p class="breadcrumb" style="font-size: 2.5rem; letter-spacing: 2px;">MY PROFILE</p>
                <h1 style="font-size: 6.5rem; letter-spacing: -2px;">Manage Account</h1>
            </div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <span style="color: #16A34A; font-weight: 800; font-size: 2.5rem; text-transform: uppercase; letter-spacing: 1px;">Approving Officer</span>
            </div>
        </header>

        <div class="form-page" style="max-width: 1600px;">
            <?php if($message): ?>
                <div style="background: #F0FDF4; color: #166534; padding: 25px; border-radius: 16px; font-size: 1.6rem; margin-bottom: 40px; border: 1px solid #BBF7D0; display: flex; align-items: center; gap: 15px;">
                    <i class="fas fa-check-circle" style="font-size: 2rem;"></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div style="background: #FEF2F2; color: #991B1B; padding: 25px; border-radius: 16px; font-size: 1.6rem; margin-bottom: 40px; border: 1px solid #FECACA; display: flex; align-items: center; gap: 15px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem;"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 550px 1fr; gap: 80px;">
                <!-- Left Column: Image -->
                <div class="card card-hover" style="padding: 80px 40px; text-align: center; border-radius: 30px; border: 1px solid #E2E8F0;">
                    <div style="position: relative; display: inline-block; margin-bottom: 50px;">
                         <?php if($sidebar_image): ?>
                            <!-- Updated Image Style: Soft Rounded, Contain Fit, Light Background -->
                            <img src="<?php echo $sidebar_image; ?>" style="width: 380px; height: 380px; border-radius: 40px; object-fit: contain; background: #F8FAFC; border: 1px solid #E2E8F0; box-shadow: 0 15px 40px rgba(0,0,0,0.08);">
                        <?php else: ?>
                            <div style="width: 380px; height: 380px; border-radius: 40px; background: #EFF6FF; color: #3B82F6; display: flex; align-items: center; justify-content: center; font-size: 12rem; font-weight: 800; margin: 0 auto; border: 1px solid #DBEAFE;">
                                <?php echo $initials; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Action Buttons -->
                        <div style="position: absolute; bottom: -30px; left: 0; right: 0; display: flex; justify-content: center; gap: 25px;">
                            <!-- Change Photo -->
                            <label for="profile_upload" title="Change Photo" style="background: #0F172A; color: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 15px 30px rgba(15, 23, 42, 0.3); font-size: 2rem; transition: transform 0.2s; border: 5px solid white;">
                                <i class="fas fa-camera"></i>
                            </label>

                            <!-- Remove Photo -->
                            <?php if($sidebar_image): ?>
                            <button type="button" onclick="removePhoto()" title="Remove Photo" style="background: #DC2626; color: white; width: 80px; height: 80px; border-radius: 50%; border: 5px solid white; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 15px 30px rgba(220, 38, 38, 0.3); font-size: 2rem; transition: transform 0.2s;">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <h2 style="font-size: 4.5rem; margin-top: 60px; margin-bottom: 15px; color: #0F172A; font-weight: 800; letter-spacing: -1.5px; line-height: 1.1;"><?php echo htmlspecialchars($user['name']); ?></h2>
                    <p style="font-size: 2rem; color: #64748B; font-weight: 700; margin-bottom: 35px; letter-spacing: 2px; text-transform: uppercase;"><?php echo strtoupper($user['role']); ?></p>
                    
                    <div style="border-top: 1px solid #E2E8F0; padding-top: 35px; font-size: 1.4rem; color: #94A3B8; font-weight: 500;">
                        Profile Status: <span style="color: #15803D; font-weight: 800; background: #DCFCE7; padding: 10px 25px; border-radius: 40px; margin-left: 12px; letter-spacing: 0.5px;">ACTIVE</span>
                    </div>
                </div>

                <!-- Right Column: Form -->
                <div class="card card-hover" style="padding: 70px; border-radius: 30px; border: 1px solid #E2E8F0;">
                    <form id="profileForm" action="" method="POST">
                        <input type="hidden" name="action" id="formAction" value="update">
                        <input type="hidden" name="cropped_image" id="cropped_image">
                        <input type="file" id="profile_upload" accept="image/*" style="display: none;">
                        
                        <div style="margin-bottom: 50px;">
                            <label class="form-label-lg">Full Name</label>
                            <input type="text" id="inputName" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required class="form-input-lg" readonly style="background-color: #F8FAFC; color: #64748B;">
                        </div>

                        <div style="margin-bottom: 50px;">
                            <label class="form-label-lg">Phone Number</label>
                            <input type="text" id="inputPhone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="+91 99999 99999" class="form-input-lg" readonly style="background-color: #F8FAFC; color: #64748B;">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px; margin-bottom: 60px;">
                            <div>
                                <label class="form-label-lg">Department</label>
                                <input type="text" value="<?php echo htmlspecialchars($user['department']); ?>" readonly class="form-input-lg readonly-field">
                            </div>
                            <div>
                                <label class="form-label-lg">User ID</label>
                                <input type="text" value="<?php echo htmlspecialchars($user['userid']); ?>" readonly class="form-input-lg readonly-field">
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 25px; margin-top: 50px;">
                            <button type="button" onclick="enableEdit()" class="btn-secondary" style="flex: 1; padding: 30px; font-size: 2rem; font-weight: 800; border-radius: 20px; text-transform: uppercase; letter-spacing: 1.5px; background: #334155; color: white; border: none; cursor: pointer; box-shadow: 0 15px 40px rgba(51, 65, 85, 0.3); transition: all 0.3s;">
                                <i class="fas fa-edit" style="margin-right: 15px;"></i> Edit Changes
                            </button>
                            <button type="submit" id="saveBtn" class="btn-success" disabled style="flex: 1; padding: 30px; font-size: 2rem; font-weight: 800; border-radius: 20px; text-transform: uppercase; letter-spacing: 1.5px; background: #16A34A; color: white; border: none; box-shadow: none; opacity: 0.5; cursor: not-allowed; transition: all 0.3s;">
                                <i class="fas fa-save" style="margin-right: 15px;"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <footer style="text-align: center; color: #94A3B8; font-size: 0.9rem; margin-top: 50px;">
                &copy; 2026 National Remote Sensing Centre. All rights reserved.
            </footer>
        </div>
    </main>
</div>

<!-- CROPPER MODAL -->
<div class="crop-modal" id="cropModal">
    <div class="crop-container">
        <h3 style="font-size: 2rem; margin-bottom: 20px; color: #1E293B;">Adjust Profile Picture</h3>
        <div class="img-container">
            <img id="imageToCrop" src="">
        </div>
        <div class="controls">
            <button type="button" class="btn-secondary btn-lg" onclick="cropper.zoom(0.1)" title="Zoom In"><i class="fas fa-search-plus"></i></button>
            <button type="button" class="btn-secondary btn-lg" onclick="cropper.zoom(-0.1)" title="Zoom Out"><i class="fas fa-search-minus"></i></button>
            <button type="button" class="btn-secondary btn-lg" onclick="cropper.rotate(90)" title="Rotate Right"><i class="fas fa-redo"></i></button>
            <button type="button" class="btn-secondary btn-lg" onclick="cropper.rotate(-90)" title="Rotate Left"><i class="fas fa-undo"></i></button>
            <button type="button" class="btn-secondary btn-lg" onclick="cropper.reset()" title="Reset"><i class="fas fa-sync"></i></button>
        </div>
        <div style="display: flex; gap: 20px; margin-top: auto;">
            <button type="button" class="btn-lg btn-secondary" onclick="closeModal()" style="flex:1;">Cancel</button>
            <button type="button" class="btn-lg btn-primary" onclick="cropAndSave()" style="flex:1;">Crop & Save</button>
        </div>
    </div>
</div>

<script>
    let cropper;
    const modal = document.getElementById('cropModal');
    const image = document.getElementById('imageToCrop');
    const input = document.getElementById('profile_upload');
    const hiddenInput = document.getElementById('cropped_image');
    const form = document.getElementById('profileForm');

    input.addEventListener('change', function(e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const file = files[0];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                // Set src triggers browser load
                image.src = e.target.result;
                modal.style.display = 'flex';
                
                // Wait for image to load to avoid cropper init issues
                image.onload = function() {
                     if(cropper) {
                        cropper.destroy();
                    }
                    
                    cropper = new Cropper(image, {
                        aspectRatio: 1, // Square for profile
                        viewMode: 0,    // No restrictions
                        dragMode: 'move',
                        autoCropArea: 1, // Try to crop full image
                        zoomOnWheel: true,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                        minContainerHeight: 400,
                    });
                }
            };
            reader.readAsDataURL(file);
        }
    });

    function closeModal() {
        modal.style.display = 'none';
        if(cropper) cropper.destroy();
        input.value = ''; // Reset
    }

    function removePhoto() {
        if(confirm('Are you sure you want to remove your profile picture?')) {
            document.getElementById('formAction').value = 'remove_photo';
            document.getElementById('profileForm').submit();
        }
    }

    function enableEdit() {
        const nameInput = document.getElementById('inputName');
        const phoneInput = document.getElementById('inputPhone');
        const saveBtn = document.getElementById('saveBtn');
        
        // Remove Readonly
        nameInput.removeAttribute('readonly');
        phoneInput.removeAttribute('readonly');
        
        // Visual Feedback
        nameInput.style.backgroundColor = '#FFFFFF';
        nameInput.style.color = '#334155';
        nameInput.style.borderColor = '#16A34A'; // Green Border
        
        phoneInput.style.backgroundColor = '#FFFFFF';
        phoneInput.style.color = '#334155';
        phoneInput.style.borderColor = '#16A34A'; // Green Border
        
        // Enable Save Button
        saveBtn.disabled = false;
        saveBtn.style.opacity = '1';
        saveBtn.style.cursor = 'pointer';
        saveBtn.style.background = '#16A34A'; // Ensure Green
        saveBtn.style.boxShadow = '0 10px 30px rgba(22, 163, 74, 0.4)'; // Green Shadow
        
        // Focus first field
        nameInput.focus();
    }

    function cropAndSave() {
        if (!cropper) return;
        
        // Get cropped canvas
        const canvas = cropper.getCroppedCanvas({
            width: 500,  // Higher res
            height: 500,
            fillColor: '#ffffff', // WHITE BACKGROUND
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });
        
        // Output as Data URL
        const base64Image = canvas.toDataURL('image/png');
        hiddenInput.value = base64Image;
        
        // Submit the form
        form.submit();
    }

</body>
</html>

