<?php
/**
 * FIX #9: Registration now has full backend integration.
 * Accepts POST JSON from the JS fetch, inserts into admins table,
 * and returns a JSON response.
 */

// Handle API registration request (JSON POST from JS fetch)
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {

    header('Content-Type: application/json');
    require_once 'config/database.php';

    $data = json_decode(file_get_contents('php://input'), true);

    $username       = isset($data['username'])       ? trim($data['username'])       : '';
    $password       = isset($data['password'])       ? $data['password']             : '';

    // Basic validation
    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled in.']);
        exit();
    }
    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
        exit();
    }

    $database = new Database();
    $db = $database->getConnection();

    // Check for duplicate username
    $check = $db->prepare('SELECT admin_id FROM admins WHERE username = ? LIMIT 1');
    $check->execute([$username]);
    if ($check->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Username already in use.']);
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare(
        'INSERT INTO admins (username, password)
         VALUES (?, ?)'
    );
    if ($stmt->execute([$username, $hashedPassword])) {
        http_response_code(201);
        echo json_encode(['success' => true, 'message' => 'Registration successful! You can now log in.']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Backstreet Boys Fan Club</title>
    <link rel="stylesheet" href="website.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            color: #e4e4e4;
            padding: 20px 0;
        }
        .bg-gradient {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-image:
                radial-gradient(circle at 30% 20%, rgba(76,29,149,0.15) 0%, transparent 50%),
                radial-gradient(circle at 70% 80%, rgba(59,130,246,0.1) 0%, transparent 50%);
            pointer-events: none; z-index: 0;
        }
        .register-container { position: relative; z-index: 10; width: 100%; max-width: 480px; padding: 20px; }
        .register-card {
            background: rgba(30,30,50,0.7);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px; padding: 40px;
            backdrop-filter: blur(20px);
            box-shadow: 0 4px 24px rgba(0,0,0,0.3), 0 0 40px rgba(76,29,149,0.1);
        }
        .logo-section { text-align: center; margin-bottom: 35px; }
        .logo { font-family: 'Poppins', sans-serif; font-size: 2rem; font-weight: 700; color: #fff; letter-spacing: -0.5px; margin-bottom: 8px; }
        .logo span { background: linear-gradient(135deg, #a78bfa 0%, #60a5fa 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .subtitle { color: #9ca3af; font-size: 0.95rem; }
        .form-group { margin-bottom: 20px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-group label { display: block; margin-bottom: 8px; color: #d1d5db; font-size: 0.9rem; font-weight: 500; }
        .input-wrapper { position: relative; }
        .form-group input, .form-group select {
            width: 100%; padding: 14px 16px 14px 44px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 10px; color: #fff; font-size: 0.95rem;
            transition: all 0.2s ease; outline: none;
        }
        .form-group input:focus, .form-group select:focus {
            border-color: rgba(139,92,246,0.5);
            background: rgba(255,255,255,0.08);
            box-shadow: 0 0 0 3px rgba(139,92,246,0.1);
        }
        .form-group input::placeholder { color: #6b7280; }
        .form-group select { appearance: none; cursor: pointer; }
        .form-group select option { background: #1e1e32; color: #fff; }
        .input-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 1rem; }
        .register-btn {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, #7c3aed 0%, #6366f1 100%);
            border: none; border-radius: 10px; color: #fff;
            font-family: 'Poppins', sans-serif; font-size: 1rem; font-weight: 600;
            cursor: pointer; transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(124,58,237,0.3); margin-top: 8px;
        }
        .register-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(124,58,237,0.4); }
        .register-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .extra-links { margin-top: 28px; text-align: center; }
        .extra-links a { color: #a78bfa; text-decoration: none; font-size: 0.9rem; font-weight: 500; }
        .extra-links a:hover { color: #c4b5fd; text-decoration: underline; }
        .back-home {
            position: absolute; top: 24px; left: 24px; color: #9ca3af;
            text-decoration: none; font-size: 0.9rem; font-weight: 500;
            transition: all 0.2s ease; z-index: 20; display: flex; align-items: center; gap: 8px;
        }
        .back-home:hover { color: #fff; }
        .terms { font-size: 0.85rem; color: #9ca3af; margin-top: 8px; display: flex; align-items: flex-start; }
        .terms input[type="checkbox"] { width: 16px; height: 16px; margin-right: 10px; margin-top: 2px; accent-color: #7c3aed; cursor: pointer; }
        .terms a { color: #a78bfa; text-decoration: none; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 0.9rem; display: none; }
        .alert-success { background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.4); color: #6ee7b7; }
        .alert-error   { background: rgba(239,68,68,0.15);  border: 1px solid rgba(239,68,68,0.4);  color: #fca5a5; }
        @media (max-width: 480px) {
            .register-card { padding: 32px 24px; }
            .logo { font-size: 1.75rem; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="bg-gradient"></div>
    <a href="home.php" class="back-home"><i class="fas fa-arrow-left"></i> Back to Home</a>

    <div class="register-container">
        <div class="register-card">
            <div class="logo-section">
                <h1 class="logo"><span>Backstreet Boys</span></h1>
                <p class="subtitle">Join the fan club today!</p>
            </div>

            <div id="alertBox" class="alert"></div>

            <form id="registerForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-wrapper">
                        <input type="text" id="username" name="username" placeholder="Choose a username" required>
                        <i class="fas fa-at input-icon"></i>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" placeholder="Create password" required>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm password" required>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="terms">
                        <input type="checkbox" id="termsCheck" required>
                        <span>I agree to the <a href="#">Terms &amp; Conditions</a> and <a href="#">Privacy Policy</a></span>
                    </label>
                </div>

                <button type="submit" class="register-btn" id="submitBtn">
                    Create Account
                </button>
            </form>

            <div class="extra-links">
                <p>Already have an account? <a href="login.php">Login Here</a></p>
            </div>
        </div>
    </div>

    <script>
        // FIX #9: Full backend integration — submits to register.php via JSON fetch
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const alertBox  = document.getElementById('alertBox');
            const submitBtn = document.getElementById('submitBtn');

            const password        = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            alertBox.style.display = 'none';
            alertBox.className     = 'alert';

            if (password !== confirmPassword) {
                alertBox.textContent    = 'Passwords do not match!';
                alertBox.className      = 'alert alert-error';
                alertBox.style.display  = 'block';
                return;
            }

            const payload = {
                username: document.getElementById('username').value,
                password: password
            };

            submitBtn.disabled   = true;
            submitBtn.textContent = 'Creating Account…';

            fetch('register.php', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alertBox.textContent   = data.message + ' Redirecting to login…';
                    alertBox.className     = 'alert alert-success';
                    alertBox.style.display = 'block';
                    document.getElementById('registerForm').reset();
                    setTimeout(() => { window.location.href = 'login.php'; }, 2000);
                } else {
                    alertBox.textContent   = data.message || 'Registration failed.';
                    alertBox.className     = 'alert alert-error';
                    alertBox.style.display = 'block';
                    submitBtn.disabled     = false;
                    submitBtn.textContent  = 'Create Account';
                }
            })
            .catch(err => {
                console.error('Registration error:', err);
                alertBox.textContent   = 'A network error occurred. Please try again.';
                alertBox.className     = 'alert alert-error';
                alertBox.style.display = 'block';
                submitBtn.disabled     = false;
                submitBtn.textContent  = 'Create Account';
            });
        });
    </script>
</body>
</html>
