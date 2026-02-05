<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Dynamic Base URL
$base_url = defined('BASE_URL') ? BASE_URL : './';
// CSS Path Prefix (for nested folders)
$css_prefix = (basename(dirname($_SERVER['PHP_SELF'])) == 'NRSC-Catering-System' || basename($_SERVER['PHP_SELF']) == 'index.php') ? './' : '../';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo defined('PAGE_TITLE') ? PAGE_TITLE . ' - ' : ''; ?> NRSC Catering</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Flatpickr (Custom Calendar) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <!-- Unified Main CSS (Cache Busted) -->
    <link rel="stylesheet" href="<?php echo $css_prefix; ?>assets/css/main.css?v=<?php echo time() + 330; ?>">
</head>
<body>
<!-- Header content removed to allow specific Sidebar Layouts in Dashboards -->
<!-- This file now serves as a central <head> manager -->
