<?php
require_once 'config/config.php';
session_start();
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['role']) {
        case 'employee': header("Location: employee/dashboard.php"); break;
        case 'officer': header("Location: officer/dashboard.php"); break;
        case 'canteen': header("Location: canteen/dashboard.php"); break;
        case 'admin': header("Location: admin/dashboard.php"); break;
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="NRSC Online Catering System - National Remote Sensing Centre, ISRO. Official portal for catering and media services.">
    <meta name="keywords" content="NRSC, ISRO, Catering, Government of India, Department of Space">
    <meta name="author" content="National Remote Sensing Centre, ISRO">
    <meta name="theme-color" content="#FF9933">
    <title>NRSC Online Catering System | ISRO - Government of India</title>
    <link rel="stylesheet" href="./assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Tricolour Bars -->
    <div class="tricolour-bar top"></div>
    <div class="tricolour-bar bottom"></div>

    <!-- Animated Background -->
    <div class="bg-animation">
        <div class="bg-gradient"></div>
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
            <div class="shape shape-5"></div>
        </div>
        <div class="grid-overlay"></div>
    </div>

    <!-- Tricolour Particles -->
    <div class="particles" id="particles"></div>

    <div class="login-container">
        <!-- Left Panel - Premium Branding -->
        <div class="branding-panel">
            <div class="branding-content">
                <!-- Official NRSC/ISRO Logo -->
                <!-- National Emblem -->
                <div class="main-title-container" style="justify-content: flex-start;">
                    <div class="catering-logo-spinner" style="width: 150px; height: 150px; margin-right: 30px;">
                        <i class="fas fa-utensils" style="font-size: 5rem;"></i>
                    </div>
                    <h1 class="brand-title-large" style="font-size: 8rem; line-height: 1; white-space: nowrap; letter-spacing: 6px; text-shadow: 0 0 30px rgba(255, 153, 51, 0.6);">
                        Online Catering System
                    </h1>
                </div>



                <!-- 3. Identity Section (Logo + Text) -->
                <div class="identity-lockup" style="padding: 40px; width: 100%;">
                    <div class="logo-box" style="width: 200px; padding-right: 40px;">
                        <img src="./assets/nrsc_isro_logo.png" alt="NRSC ISRO Logo" class="side-logo">
                    </div>
                    <div class="org-text-stack">
                        <h2 class="org-name" style="font-size: 4.5rem; line-height: 1.1; letter-spacing: 2px;">National Remote Sensing Centre</h2>
                        <h3 class="org-dept" style="font-size: 2.5rem; margin-top: 15px; letter-spacing: 3px; font-weight: 700;">Indian Space Research Organisation</h3>
                        <p class="org-city" style="font-size: 2rem; margin-top: 10px; letter-spacing: 6px; color: rgba(255,255,255,0.8);">Hyderabad</p>
                    </div>
                </div>
                
                <!-- Feature Cards (Clickable) -->
                <div class="feature-list" style="gap: 25px;">
                    <div class="feature-item" onclick="window.location.href='food_ordering.php'" style="padding: 25px 30px; border-radius: 16px;">
                        <div class="feature-icon" style="font-size: 2.5rem; margin-bottom: 15px;">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <span style="font-size: 2rem; font-weight: 700;">Food Ordering</span>
                    </div>
                    <div class="feature-item" onclick="showFeatureNotification('Event Catering')" style="padding: 25px 30px; border-radius: 16px;">
                        <div class="feature-icon" style="font-size: 2.5rem; margin-bottom: 15px;">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <span style="font-size: 2rem; font-weight: 700;">Event Catering</span>
                    </div>
                    <div class="feature-item" onclick="showFeatureNotification('Media Services')" style="padding: 25px 30px; border-radius: 16px;">
                        <div class="feature-icon" style="font-size: 2.5rem; margin-bottom: 15px;">
                            <i class="fas fa-photo-film"></i>
                        </div>
                        <span style="font-size: 2rem; font-weight: 700;">Media Services</span>
                    </div>
                    <div class="feature-item" onclick="showFeatureNotification('Quick Approvals')" style="padding: 25px 30px; border-radius: 16px;">
                        <div class="feature-icon" style="font-size: 2.5rem; margin-bottom: 15px;">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <span style="font-size: 2rem; font-weight: 700;">Quick Approvals</span>
                    </div>
                </div>

                <!-- Animated Stats -->
                <div class="stats-bar" style="margin-top: 50px;">
                    <div class="stat">
                        <span class="stat-number" data-count="2500" style="font-size: 4.5rem; text-shadow: 0 4px 10px rgba(0,0,0,0.5);">0</span>
                        <span class="stat-label" style="font-size: 1.4rem; letter-spacing: 2px;">Employees</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat">
                        <span class="stat-number" data-count="150" style="font-size: 4.5rem; text-shadow: 0 4px 10px rgba(0,0,0,0.5);">0</span>
                        <span class="stat-label" style="font-size: 1.4rem; letter-spacing: 2px;">Daily Orders</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat">
                        <span class="stat-number" data-count="99" style="font-size: 4.5rem; text-shadow: 0 4px 10px rgba(0,0,0,0.5);">0</span>
                        <span class="stat-label" style="font-size: 1.4rem; letter-spacing: 2px;">% Uptime</span>
                    </div>
                </div>
            </div>
            
            <!-- Footer Text -->
            <div class="branding-footer" style="padding-bottom: 40px;">
                <div class="footer-govt-text" style="font-size: 1.6rem; letter-spacing: 2px;">
                    <i class="fas fa-landmark"></i>
                    <span>Government of India | Department of Space</span>
                </div>
            </div>
        </div>

        <!-- Right Panel Removed as per request -->
    </div>

    <script>
        // Create Tricolour Particles
        function createParticles() {
            const container = document.getElementById('particles');
            const particleCount = 60;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.cssText = `
                    left: ${Math.random() * 100}%;
                    top: ${100 + Math.random() * 20}%;
                    animation-delay: ${Math.random() * 15}s;
                    animation-duration: ${12 + Math.random() * 8}s;
                    width: ${2 + Math.random() * 4}px;
                    height: ${2 + Math.random() * 4}px;
                `;
                container.appendChild(particle);
            }
        }

        // Animated Counter for Stats
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number');
            const observerOptions = {
                threshold: 0.5
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const counter = entry.target;
                        const target = parseInt(counter.getAttribute('data-count'));
                        const duration = 2500;
                        const startTime = performance.now();
                        
                        const updateCounter = (currentTime) => {
                            const elapsed = currentTime - startTime;
                            const progress = Math.min(elapsed / duration, 1);
                            
                            // Easing function for smooth animation
                            const easeOutQuart = 1 - Math.pow(1 - progress, 4);
                            const current = Math.floor(easeOutQuart * target);
                            
                            const label = counter.parentElement.querySelector('.stat-label').textContent;
                            if (label.includes('%')) {
                                counter.textContent = current + '%';
                            } else {
                                counter.textContent = current.toLocaleString() + '+';
                            }
                            
                            if (progress < 1) {
                                requestAnimationFrame(updateCounter);
                            }
                        };
                        
                        requestAnimationFrame(updateCounter);
                        observer.unobserve(counter);
                    }
                });
            }, observerOptions);
            
            counters.forEach(counter => observer.observe(counter));
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
            setTimeout(animateCounters, 800);
        });

        // Feature Notification Toast

        // Feature Notification Toast
        function showFeatureNotification(featureName) {
            // Check if toast already exists
            const existingToast = document.querySelector('.feature-toast');
            if(existingToast) existingToast.remove();

            const toast = document.createElement('div');
            toast.className = 'feature-toast';
            toast.innerHTML = `
                <i class="fas fa-info-circle"></i>
                <span>Please <strong>Sign In</strong> to access ${featureName}</span>
            `;
            document.body.appendChild(toast);

            // Animate in
            setTimeout(() => toast.classList.add('show'), 10);

            // Remove after 3s
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>
