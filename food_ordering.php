<?php
require_once 'config/config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Ordering Login - NRSC Catering</title>
    <link rel="stylesheet" href="./assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Specific overrides for the role selection screen */
        .role-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .role-card:hover {
            background: rgba(255, 153, 51, 0.1);
            border-color: var(--saffron);
            transform: translateX(5px);
        }

        .role-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--saffron);
            border: 1px solid rgba(255, 153, 51, 0.3);
        }

        .role-info h3 {
            font-family: 'Rajdhani', sans-serif;
            font-size: 1.4rem;
            color: #fff;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .role-info p {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .role-arrow {
            margin-left: auto;
            color: var(--text-muted);
            transition: 0.3s;
        }

        .role-card:hover .role-arrow {
            color: var(--saffron);
            transform: translateX(5px);
        }
        
        /* Back Button */
        .back-nav {
            position: absolute;
            top: 30px;
            left: 30px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-size: 1.4rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: 0.3s;
            z-index: 100;
            background: rgba(0,0,0,0.3);
            padding: 10px 20px;
            border-radius: 30px;
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(5px);
        }
        .back-nav:hover {
            color: var(--saffron);
        }
    </style>
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

    <div class="login-container" style="display: flex; width: 100vw; height: 100vh; overflow: hidden;">
        <!-- Left Panel - Branding (Food Ordering Specific) -->
        <div class="branding-panel" style="width: 50%; border-right: 2px solid rgba(255,255,255,0.1); position: relative;">
            <a href="index.php" class="back-nav" style="font-size: 1.8rem; text-decoration: none; color: white;"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            
            <div class="branding-content" style="width: 100%; max-width: 1200px; padding-left: 0;">
                <div class="main-title-container" style="justify-content: flex-start; width: 100%; margin-left: -120px;">
                    <div class="catering-logo-spinner" style="margin-right: 15px; width: 150px; height: 150px;">
                        <i class="fas fa-utensils" style="font-size: 5rem;"></i>
                    </div>
                    <div style="flex: 1;">
                        <h1 class="brand-title-large" style="font-size: 8rem; line-height: 1; white-space: nowrap; letter-spacing: 6px; text-shadow: 0 0 30px rgba(255, 153, 51, 0.6);">
                            FOOD ORDERING PORTAL
                        </h1>
                    </div>
                </div>

                <div class="identity-lockup" style="width: 100%; padding: 30px; margin-top: 40px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 25px; backdrop-filter: blur(12px); display: flex; align-items: center; box-shadow: 0 15px 35px rgba(0,0,0,0.2); max-width: 95%;">
                    <div class="logo-box" style="width: 140px; min-width: 140px; padding-right: 25px;">
                        <img src="./assets/nrsc_isro_logo.png" alt="NRSC ISRO Logo" class="side-logo" style="width: 100%; height: auto;">
                    </div>
                    <div class="org-text-stack" style="flex: 1;">
                        <h2 class="org-name" style="font-size: 3.5rem; line-height: 1.1; letter-spacing: 1.5px; margin-bottom: 8px; font-weight: 800; text-transform: uppercase;">National Remote Sensing Centre</h2>
                        <h3 class="org-dept" style="font-size: 2rem; letter-spacing: 2px; font-weight: 600; color: #EA580C; margin-bottom: 8px;">Indian Space Research Organisation</h3>
                        <p class="org-city" style="font-size: 1.6rem; letter-spacing: 4px; color: rgba(255,255,255,0.7); font-weight: 500;">Hyderabad</p>
                    </div>
                </div>
                
                <div class="stats-bar" style="margin-top: 50px; justify-content: flex-start; background: none; border: none; padding-left: 0;">
                    <div class="stat" style="text-align: left;">
                        <span class="stat-number" style="font-size: 4.5rem; text-align: left; text-shadow: 0 4px 10px rgba(0,0,0,0.5);">Secure Access</span>
                        <span class="stat-label" style="text-align: left; font-size: 1.6rem; letter-spacing: 3px; font-weight: 600;">Authorized Personnel Only</span>
                    </div>
                </div>
            </div>
            
            <div class="branding-footer" style="left: 40px; transform: none;">
                <div class="footer-govt-text" style="font-size: 1.8rem;">
                    <i class="fas fa-landmark"></i>
                    <span>Government of India | Department of Space</span>
                </div>
            </div>
        </div>

        <!-- Right Panel - Role Selection -->
        <div class="form-panel" style="
            width: 50%; 
            background: 
                linear-gradient(rgba(2, 6, 23, 0.85), rgba(15, 23, 42, 0.8)),
                url('./assets/rocket_statue.jpg');
            background-size: cover;
            background-position: center;
            border-left: 1px solid rgba(255,255,255,0.1);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        ">
            <div class="form-wrapper" style="max-width: 600px; width: 85%;">
                <div class="form-header" style="text-align: left; margin-bottom: 50px;">
                    <div class="welcome-badge" style="margin-bottom: 25px; padding: 15px 35px; font-size: 1.6rem;">
                        <i class="fas fa-user-lock"></i>
                        <span>Select Login Type</span>
                    </div>
                    <h2 style="font-size: 7rem; text-align: left; font-weight: 900; text-transform: uppercase; letter-spacing: 3px; margin-bottom: 20px; line-height: 1;">Who are you?</h2>
                    <p style="text-align: left; font-size: 2rem; color: #b0bec5; font-weight: 500;">Please select your role to proceed to login</p>
                </div>

                <!-- 1. Employee Login -->
                <div class="role-card" onclick="openLogin('employee')" style="padding: 35px; margin-bottom: 30px; border-width: 2px;">
                    <div class="role-icon" style="width: 100px; height: 100px; font-size: 3rem; min-width: 100px;">
                        <i class="fas fa-id-badge"></i>
                    </div>
                    <div class="role-info">
                        <h3 style="font-size: 3.5rem; margin-bottom: 10px; font-weight: 800;">Employee Login</h3>
                        <p style="font-size: 1.8rem;">For Staff & General Users</p>
                    </div>
                    <div class="role-arrow" style="font-size: 3rem;">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>

                <!-- 2. Approval Login -->
                <div class="role-card" onclick="openLogin('officer')" style="padding: 35px; margin-bottom: 30px; border-width: 2px;">
                    <div class="role-icon" style="width: 100px; height: 100px; font-size: 3rem; min-width: 100px;">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="role-info">
                        <h3 style="font-size: 3.5rem; margin-bottom: 10px; font-weight: 800;">APPROVAL LOGIN</h3>
                        <p style="font-size: 1.8rem;">For Reporting Officers</p>
                    </div>
                    <div class="role-arrow" style="font-size: 3rem;">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>

                <!-- 3. Catering Approval Login -->
                <div class="role-card" onclick="openLogin('canteen')" style="padding: 35px; margin-bottom: 30px; border-width: 2px;">
                    <div class="role-icon" style="width: 100px; height: 100px; font-size: 3rem; min-width: 100px;">
                        <i class="fas fa-concierge-bell"></i>
                    </div>
                    <div class="role-info">
                        <h3 style="font-size: 3.5rem; margin-bottom: 10px; font-weight: 800;">CATERING ADMIN</h3>
                        <p style="font-size: 1.8rem;">For Canteen Management</p>
                    </div>
                    <div class="role-arrow" style="font-size: 3rem;">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>

                <!-- Hidden Login Form Container -->
                <div id="loginFormContainer" style="display: none; margin-top: 30px; animation: fadeInUp 0.5s ease;">
                    <div style="display: flex; align-items: center; margin-bottom: 40px; gap: 20px; cursor: pointer;" onclick="closeLogin()">
                        <i class="fas fa-arrow-left" style="color: var(--saffron); font-size: 2.2rem;"></i>
                        <span style="color: var(--text-secondary); font-size: 2.2rem; font-weight: 700;">Back to Roles</span>
                    </div>

                    <h3 id="formTitle" style="color: white; font-family: 'Rajdhani', sans-serif; font-size: 4.5rem; margin-bottom: 40px; font-weight: 800;">Login</h3>

                    <form action="auth/login.php" method="POST" class="login-form">
                        <input type="hidden" name="role" id="loginRole" value="">
                        
                        <div class="input-group" style="margin-bottom: 40px;">
                            <label for="username" id="usernameLabel" style="font-size: 2.2rem; margin-bottom: 15px; display: block; font-weight: 600;">
                                <i class="fas fa-id-card"></i> ID / Username
                            </label>
                            <div class="input-wrapper">
                                <input type="text" name="username" class="form-input" placeholder="Enter ID" required style="padding: 25px; font-size: 2rem; border-radius: 16px;">
                            </div>
                        </div>
                        
                        <div class="input-group" style="margin-bottom: 50px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <label for="password" style="margin-bottom: 0; font-size: 2.2rem; font-weight: 600;"> <i class="fas fa-lock"></i> Password </label>
                                <a href="auth/forgot_password.php" class="forgot-link" style="color: var(--saffron); text-decoration: none; transition: 0.3s; opacity: 0.9; font-size: 1.8rem;">Forgot Password?</a>
                            </div>
                            <div class="input-wrapper" style="position: relative;">
                                <input type="password" name="password" id="passwordInput" class="form-input" placeholder="Enter Password" required style="padding: 25px; font-size: 2rem; border-radius: 16px;">
                                <i class="fas fa-eye" id="togglePassword" style="position: absolute; right: 25px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #1E293B; z-index: 10; font-size: 2rem;"></i>
                            </div>
                        </div>

                        <button type="submit" class="btn-login" style="padding: 25px; font-size: 2.4rem; border-radius: 18px;">
                            <span class="btn-text">Sign In</span>
                            <span class="btn-icon"><i class="fas fa-arrow-right" style="font-size: 2.4rem;"></i></span>
                        </button>
                    </form>
                </div>

                <div class="form-footer" style="margin-top: 30px;">
                    <p class="copyright">
                        &copy; 2026 NRSC, ISRO
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password Toggle
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#passwordInput');

        togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye slash icon
            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });
        // Check for error in URL
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        if (error) {
            // Create and show error toast
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed;
                top: 50px;
                left: 50%;
                transform: translateX(-50%);
                background: #EF4444; /* Bright Red */
                color: white;
                padding: 30px 50px;
                border-radius: 16px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.5);
                z-index: 2000;
                font-family: 'Rajdhani', sans-serif;
                font-size: 1.8rem;
                font-weight: 700;
                display: flex;
                align-items: center;
                gap: 20px;
                animation: slideDown 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55);
                border: 3px solid rgba(255,255,255,0.3);
                text-transform: uppercase;
                letter-spacing: 1px;
            `;
            // Add custom animation
            const style = document.createElement('style');
            style.innerHTML = `
                @keyframes slideDown {
                    from { top: -100px; opacity: 0; transform: translateX(-50%); }
                    to { top: 50px; opacity: 1; transform: translateX(-50%); }
                }
            `;
            document.head.appendChild(style);

            toast.innerHTML = `<i class="fas fa-exclamation-triangle" style="font-size: 2.5rem;"></i> <span>${decodeURIComponent(error)}</span>`;
            document.body.appendChild(toast);

            // Hide after 4 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-20px)';
                setTimeout(() => toast.remove(), 500);
            }, 4000);
        }

        // Function to create floating particles
        function createParticles() {
            const container = document.querySelector('.floating-shapes');
            if (!container) return;

            for (let i = 0; i < 10; i++) { // Create 10 particles
                const particle = document.createElement('div');
                particle.classList.add('shape');
                particle.style.width = `${Math.random() * 20 + 10}px`; // 10-30px
                particle.style.height = particle.style.width;
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.top = `${Math.random() * 100}%`;
                particle.style.animationDelay = `${Math.random() * 10}s`;
                particle.style.animationDuration = `${Math.random() * 20 + 10}s`; // 10-30s
                particle.style.opacity = `${Math.random() * 0.5 + 0.2}`; // 0.2-0.7
                container.appendChild(particle);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
        });

        function openLogin(role) {
            // Hide Role Selection
            const cards = document.querySelectorAll('.role-card');
            cards.forEach(card => card.style.display = 'none');
            
            // Update Form
            const formContainer = document.getElementById('loginFormContainer');
            const title = document.getElementById('formTitle');
            const roleInput = document.getElementById('loginRole');
            const userLabel = document.getElementById('usernameLabel');

            formContainer.style.display = 'block';
            roleInput.value = role;

            if(role === 'employee') {
                title.textContent = 'Employee Login';
                userLabel.innerHTML = '<i class="fas fa-id-card"></i> Employee ID';
            } else if (role === 'officer') {
                title.textContent = 'Approving Officer Login';
                userLabel.innerHTML = '<i class="fas fa-user-tie"></i> Officer ID';
            } else {
                title.textContent = 'Canteen Staff Login';
                userLabel.innerHTML = '<i class="fas fa-utensils"></i> Staff Username';
            }
        }

        function closeLogin() {
             // Show Role Selection
             const cards = document.querySelectorAll('.role-card');
            cards.forEach(card => card.style.display = 'flex');

            // Hide Form
            document.getElementById('loginFormContainer').style.display = 'none';
        }
    </script>
</body>
</html>
