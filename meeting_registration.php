<?php
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting Registration - NRSC Catering</title>
    <link rel="stylesheet" href="./assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Professional One-Page Layout */
        body {
            background: 
                linear-gradient(rgba(5, 10, 24, 0.92), rgba(5, 10, 24, 0.95)),
                url('./assets/nrsc_campus.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            overflow: hidden; /* Force one page feel */
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }

        .reg-container {
            width: 98%;
            height: 96vh;
            background: rgba(10, 20, 35, 0.85); /* Darker, more professional */
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            backdrop-filter: blur(25px);
            box-shadow: 0 0 60px rgba(0,0,0,0.6);
            display: flex;
            flex-direction: column;
            padding: 20px;
            box-sizing: border-box;
        }

        /* Compact Header */
        .reg-header {
            justify-content: space-between;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 15px;
            margin-bottom: 15px;
            flex-shrink: 0;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-logo {
            height: 90px;
            width: auto;
            /* Removed color inversion so original orange/blue colors show. Added drop-shadow for contrast. */
            filter: drop-shadow(0 0 5px rgba(255,255,255,0.2));
        }

        /* Compact Header */
        .reg-header {
            position: relative; /* Needed for absolute positioning of service bar */
            display: flex;
            align-items: center;
            justify-content: center; /* Center the main branding unit */
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 15px;
            margin-bottom: 15px;
            flex-shrink: 0;
            min-height: 100px;
        }

        .header-branding {
            display: flex;
            align-items: center;
            gap: 25px; /* Space between Logo and Text */
        }

        .header-logo {
            height: 95px;
            width: auto;
            filter: drop-shadow(0 0 5px rgba(0,0,0,0.5));
        }

        .header-titles {
            display: flex;
            flex-direction: column;
            align-items: center; /* Center text stack */
            justify-content: center;
            text-align: center;
        }

        /* Styles for text remain centered by previous applied class styles, 
           but we just ensure the container aligns them centrally */

        .service-bar {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%); /* Vertically center */
            display: flex;
            gap: 15px;
            align-items: center;
            background: rgba(255, 255, 255, 0.05);
            padding: 8px 15px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .header-govt {
            color: #d2691e;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            text-transform: none;
        }

        .header-main-title {
            color: #33b5e5;
            font-family: 'Arial', sans-serif;
            font-size: 2.2rem;
            font-weight: 800;
            margin: 0;
            line-height: 1.1;
            text-transform: none;
        }

        .header-sub-title {
            color: #d2691e;
            font-size: 1.3rem;
            font-weight: 700;
            margin-top: 2px;
            text-transform: none;
        }

        .service-bar {
            /* Keep on right, removed margin-left auto since flex:1 pushes it */
            display: flex;
            gap: 15px;
            align-items: center;
            background: rgba(255, 255, 255, 0.05);
            padding: 8px 15px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .service-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: 0.2s;
        }

        .service-link:hover {
            color: var(--saffron);
        }

        /* Scrollable Content Area */
        .content-area {
            flex: 1;
            overflow-y: auto;
            padding-right: 10px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Custom Scrollbar */
        .content-area::-webkit-scrollbar {
            width: 8px;
        }
        .content-area::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.02);
        }
        .content-area::-webkit-scrollbar-thumb {
            background: var(--glass-border);
            border-radius: 4px;
        }

        /* 4-Column Grid for efficiency */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            background: rgba(0,0,0,0.2);
            padding: 20px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-secondary);
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .form-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #FFF;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            transition: 0.3s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--saffron);
            background: rgba(255, 153, 51, 0.1);
        }

        /* Split Section: Details & Items */
        .split-section {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 20px;
        }

        .panel-box {
            background: rgba(0,0,0,0.2);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 10px;
            padding: 15px;
        }

        .panel-header {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--saffron);
            text-transform: uppercase;
            margin-bottom: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .items-grid {
            display: grid;
            grid-template-columns: 2fr 1fr auto;
            gap: 10px;
            align-items: end;
        }

        .btn-add {
            background: var(--green);
            color: white;
            border: none;
            height: 35px;
            padding: 0 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            text-transform: uppercase;
            font-size: 0.85rem;
            transition: 0.3s;
        }
        
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(19, 136, 8, 0.3);
        }

        /* Table */
        .table-container {
            flex: 1;
            /* overflow: hidden; */
            min-height: 150px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background: rgba(255,255,255,0.08);
            color: var(--saffron);
            font-size: 0.8rem;
            text-transform: uppercase;
            padding: 12px;
            text-align: left;
            position: sticky;
            top: 0;
        }

        .data-table td {
            padding: 10px 12px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            color: #ddd;
            font-size: 0.9rem;
        }

        .btn-delete {
            color: #ff4444;
            background: none;
            border: 1px solid rgba(255,68,68,0.3);
            padding: 4px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.75rem;
            transition: 0.2s;
        }

        .btn-delete:hover {
            background: #ff4444;
            color: white;
        }

        /* Footer Action Bar */
        .footer-actions {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255,255,255,0.1);
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .action-btn {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: #FFF;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            cursor: pointer;
            transition: 0.3s;
        }

        .action-btn:hover {
            background: var(--saffron);
            border-color: var(--saffron);
            transform: translateY(-2px);
        }

        .action-btn.primary {
            background: var(--gradient-tricolour);
            border: none;
            color: var(--bg-primary);
            font-weight: 800;
        }

        /* Visual Polish to match Reference Image */
        .reg-container {
            background: #050a12; /* Deep dark navy/black */
            border: 1px solid #1a2639;
            box-shadow: 0 0 80px rgba(0,0,0,0.8);
            width: 98%;
            height: 96vh;
            border-radius: 12px;
            backdrop-filter: blur(25px);
            display: flex;
            flex-direction: column;
            padding: 20px;
            box-sizing: border-box;
        }

        /* Section Headings */
        .panel-header {
            color: #ff9933; /* Saffron Orange */
            font-family: 'Rajdhani', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 1rem;
            border-bottom: none;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .panel-header i {
            background: #ff9933;
            color: #000;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 0.7rem;
        }

        /* Inputs */
        .form-label {
            color: #8fa1b3; /* Muted blue-grey */
            font-size: 0.7rem;
            margin-bottom: 4px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .form-input {
            background: #0d1421; /* Very dark input bg */
            border: 1px solid #1f2d40;
            border-radius: 4px; /* Sharper corners */
            color: #e0e6ed;
            font-size: 0.85rem;
            height: 38px;
            padding: 8px 12px;
            font-family: 'Inter', sans-serif;
            transition: 0.3s;
        }

        .form-input:focus {
            border-color: #33b5e5; /* ISRO Blue focus */
            background: #131c2e;
            box-shadow: 0 0 0 2px rgba(51, 181, 229, 0.2);
            outline: none;
        }

        /* Table Header */
        .data-table th {
            background: #0d1421;
            color: #ff9933;
            font-size: 0.75rem;
            border-bottom: 1px solid #1f2d40;
            font-family: 'Rajdhani', sans-serif;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 12px;
            text-align: left;
            position: sticky;
            top: 0;
        }

        .data-table td {
            border-bottom: 1px solid #1f2d40;
            font-size: 0.85rem;
            color: #b0bec5;
            padding: 10px 12px;
        }

        /* Specific Button Styles from Image */
        .footer-actions {
            border-top: 1px solid #1f2d40;
            gap: 15px;
            background: #050a12;
            margin-top: 15px;
            padding-top: 15px;
            display: flex;
            justify-content: center;
            flex-shrink: 0;
        }

        .action-btn {
            background: rgba(255,255,255,0.05);
            border: 1px solid #3d4b60;
            color: #FFF;
            font-family: 'Rajdhani', sans-serif;
            font-weight: 700;
            letter-spacing: 1px;
            border-radius: 4px;
            padding: 8px 25px;
            font-size: 0.85rem;
            text-transform: uppercase;
            cursor: pointer;
            transition: 0.3s;
        }

        /* The prominent Register button */
        .action-btn.primary {
            background: linear-gradient(90deg, #ff9933, #a2b70d 50%, #4CAF50); /* Saffron to Green gradient */
            border: none;
            color: #000;
            font-weight: 800;
            font-size: 1rem;
            padding: 10px 40px;
            box-shadow: 0 0 20px rgba(255, 153, 51, 0.3);
            text-transform: uppercase;
        }
        
        .action-btn.primary:hover {
            filter: brightness(1.1);
            transform: scale(1.02);
        }

        /* Add Button */
        .btn-add {
            background: #4CAF50; /* Distinct Green */
            border-radius: 4px;
            height: 38px;
            color: white;
            border: none;
            padding: 0 20px;
            font-weight: 600;
            cursor: pointer;
            text-transform: uppercase;
            font-size: 0.85rem;
            transition: 0.3s;
        }

    </style>
</head>
<body>
    <div class="reg-container">
        <!-- Header -->
        <div class="reg-header">
            <div class="header-branding">
                <img src="./assets/custom_logo.png" class="header-logo" alt="logo">
                <div class="header-titles">
                    <div class="header-govt">Government of India</div>
                    <div class="header-main-title">National Remote Sensing Centre</div>
                    <div class="header-sub-title">Indian Space Research Organisation</div>
                </div>
            </div>
            
            <div class="service-bar">
                <a href="#" class="service-link"><i class="fas fa-home"></i> Home</a>
                <span style="color:#555">|</span>
                <a href="#" class="service-link"><i class="fas fa-cog"></i> Service</a>
                <span style="color:#555">|</span>
                <a href="#" class="service-link"><i class="fas fa-key"></i> Change Password</a>
                <span style="color:#555">|</span>
                <a href="index.php" class="service-link" style="color:#ff6b6b"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <!-- Scrollable Content -->
        <div class="content-area">
            
            <!-- Main Form Info (4 Columns) -->
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Meeting Ref Id</label>
                    <input type="text" class="form-input" placeholder="Auto-generated">
                </div>
                <div class="form-group">
                    <label class="form-label">Date of Request</label>
                    <input type="date" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Meeting Area</label>
                    <input type="text" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Meeting Date</label>
                    <input type="date" class="form-input">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Requesting Person</label>
                    <input type="text" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Designation</label>
                    <input type="text" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Department</label>
                    <input type="text" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone/Ext</label>
                    <input type="text" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Approving Officer</label>
                    <input type="text" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Approver Desig.</label>
                    <input type="text" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Approver Dept</label>
                    <input type="text" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">LIC Code</label>
                    <input type="text" class="form-input">
                </div>
                
                <div class="form-group" style="grid-column: span 2">
                    <label class="form-label">Meeting Name/Title</label>
                    <input type="text" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Meeting Time</label>
                    <input type="time" class="form-input">
                </div>
                <!-- Spacer -->
                <div></div>
            </div>

            <div class="split-section">
                <!-- Service Details Panel -->
                <div class="panel-box">
                    <div class="panel-header">
                        <span><i class="fas fa-info-circle"></i> Service Details</span>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div class="form-group">
                            <label class="form-label">Service Date</label>
                            <input type="date" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Service Time</label>
                            <input type="time" class="form-input">
                        </div>
                        <div class="form-group" style="grid-column: span 2">
                            <label class="form-label">Location/Hall</label>
                            <input type="text" class="form-input">
                        </div>
                    </div>
                </div>

                <!-- Add Items Panel -->
                <div class="panel-box">
                    <div class="panel-header">
                        <span><i class="fas fa-utensils"></i> Add Refreshments</span>
                    </div>
                    <div class="items-grid">
                        <div class="form-group">
                            <label class="form-label">Select Item</label>
                            <select class="form-input">
                                <option>-- Select --</option>
                                <option>Coffee</option>
                                <option>Tea</option>
                                <option>Biscuits</option>
                                <option>High Tea</option>
                                <option>Lunch (Veg)</option>
                                <option>Lunch (Non-Veg)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Qty</label>
                            <input type="number" class="form-input" min="1">
                        </div>
                        <div style="padding-bottom: 2px;">
                            <button class="btn-add"><i class="fas fa-plus"></i> Add</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Service Date</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Item Name</th>
                            <th>Qty</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Empty State -->
                    </tbody>
                </table>
            </div>

        </div>

        <!-- Sticky Footer -->
        <div class="footer-actions">
            <button class="action-btn"><i class="fas fa-file"></i> New</button>
            <button class="action-btn primary"><i class="fas fa-save"></i> Register Meeting</button>
            <button class="action-btn"><i class="fas fa-edit"></i> Update</button>
            <button class="action-btn"><i class="fas fa-trash"></i> Delete</button>
            <button class="action-btn"><i class="fas fa-print"></i> Print</button>
            <button class="action-btn"><i class="fas fa-eraser"></i> Clear</button>
        </div>
    </div>
</body>
</html>
