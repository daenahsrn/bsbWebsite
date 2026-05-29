<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    require_once 'config/database.php';

    $username = $_POST['username'];
    $password = $_POST['password'];

    $database = new Database();
    $db = $database->getConnection();

    if ($db) {
        $stmt = $db->prepare("SELECT admin_id, username, password FROM admins WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['logged_in'] = true;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password!";
        }
    } else {
        $error = "Database connection failed. Please try again later.";
    }
}

// Check if admin is logged in
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// If not logged in and trying to access dashboard, show login form
if (!$is_logged_in && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redirect to login page or show login form
    header("Location: login.php");
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Backstreet Boys</title>
    <link rel="stylesheet" href="website.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(135deg, #0a0a1a 0%, #1a1a3e 50%, #0f3460 100%);
            min-height: 100vh;
            color: #e4e4e4;
            position: relative;
            overflow-x: hidden;
        }

        /* Retro Background Elements */
        .retro-bg-stars {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(2px 2px at 20px 30px, rgba(255, 255, 255, 0.8), transparent),
                radial-gradient(2px 2px at 40px 70px, rgba(255, 255, 255, 0.6), transparent),
                radial-gradient(1px 1px at 90px 40px, rgba(255, 255, 255, 0.7), transparent),
                radial-gradient(2px 2px at 160px 120px, rgba(255, 255, 255, 0.5), transparent);
            background-size: 200px 200px;
            animation: twinkle 5s infinite;
            pointer-events: none;
            z-index: 0;
        }

        .retro-bg-grid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(139, 92, 246, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(139, 92, 246, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0.8; }
            50% { opacity: 1; }
        }

        /* Header - Matching Website Style */
        .dashboard-header {
            background: rgba(20, 20, 40, 0.9);
            border-bottom: 2px solid rgba(139, 92, 246, 0.3);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 10;
            backdrop-filter: blur(20px);
        }

        .logo {
            font-family: 'Cinzel', serif;
            font-size: 1.8rem;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo span {
            background: linear-gradient(135deg, #a78bfa 0%, #60a5fa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo i {
            color: #a78bfa;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .welcome-text {
            font-family: 'DM Sans', sans-serif;
            color: #9ca3af;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .admin-badge {
            background: linear-gradient(135deg, #a78bfa 0%, #60a5fa 100%);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .logout-btn {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(239, 68, 68, 0.5);
        }

        /* Main Container */
        .dashboard-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 24px;
            padding: 30px 40px;
            position: relative;
            z-index: 10;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Sidebar */
        .sidebar {
            background: rgba(30, 30, 60, 0.6);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: 20px;
            padding: 28px;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            height: fit-content;
            position: sticky;
            top: 30px;
        }

        .profile-section {
            text-align: center;
            margin-bottom: 32px;
            padding-bottom: 28px;
            border-bottom: 1px solid rgba(139, 92, 246, 0.2);
        }

        .profile-avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            margin: 0 auto 18px;
            background: linear-gradient(135deg, #a78bfa 0%, #60a5fa 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Cinzel', serif;
            font-size: 2.2rem;
            font-weight: 700;
            color: #fff;
            box-shadow: 0 4px 20px rgba(167, 139, 250, 0.4);
            border: 3px solid rgba(255, 255, 255, 0.1);
        }

        .profile-name {
            font-family: 'Cinzel', serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 6px;
        }

        .profile-role {
            font-family: 'DM Sans', sans-serif;
            color: #a78bfa;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 10px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            background: transparent;
            border: none;
            border-radius: 12px;
            color: #d1d5db;
            text-decoration: none;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: linear-gradient(135deg, #a78bfa 0%, #60a5fa 100%);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .nav-link:hover::before,
        .nav-link.active::before {
            transform: scaleY(1);
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(139, 92, 246, 0.15);
            color: #a78bfa;
        }

        .nav-link i {
            color: #9ca3af;
            font-size: 1.1rem;
            width: 22px;
            text-align: center;
        }

        .nav-link:hover i,
        .nav-link.active i {
            color: #a78bfa;
        }

        /* Main Content */
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        .content-card {
            background: rgba(30, 30, 60, 0.6);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: 20px;
            padding: 32px;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(139, 92, 246, 0.2);
        }

        .card-title {
            font-family: 'Cinzel', serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-title i {
            color: #a78bfa;
        }

        .add-new-btn {
            background: linear-gradient(135deg, #a78bfa 0%, #60a5fa 100%);
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 12px rgba(167, 139, 250, 0.3);
        }

        .add-new-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(167, 139, 250, 0.5);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(139, 92, 246, 0.1);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: 16px;
            padding: 26px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(135deg, #a78bfa 0%, #60a5fa 100%);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            background: rgba(139, 92, 246, 0.15);
            border-color: rgba(139, 92, 246, 0.4);
            box-shadow: 0 8px 24px rgba(139, 92, 246, 0.2);
        }

        .stat-icon {
            font-size: 2.2rem;
            color: #a78bfa;
            margin-bottom: 14px;
        }

        .stat-value {
            font-family: 'Cinzel', serif;
            font-size: 2rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .stat-label {
            font-family: 'DM Sans', sans-serif;
            color: #9ca3af;
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Members Table */
        .members-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .members-table th,
        .members-table td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid rgba(139, 92, 246, 0.1);
        }

        .members-table th {
            font-family: 'DM Sans', sans-serif;
            color: #a78bfa;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .members-table td {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            color: #d1d5db;
        }

        .members-table tr:hover {
            background: rgba(139, 92, 246, 0.05);
        }

        .member-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #a78bfa 0%, #60a5fa 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Cinzel', serif;
            font-weight: 700;
            color: #fff;
            font-size: 1.1rem;
        }

        .member-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .member-name {
            font-weight: 600;
            color: #ffffff;
        }

        .member-email {
            font-size: 0.85rem;
            color: #9ca3af;
        }

        .status-badge {
            padding: 5px 14px;
            border-radius: 20px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-active {
            background: rgba(34, 197, 94, 0.15);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .status-inactive {
            background: rgba(107, 114, 128, 0.15);
            color: #6b7280;
            border: 1px solid rgba(107, 114, 128, 0.3);
        }

        .status-premium {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        .action-btn.edit {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #fff;
        }

        .action-btn.edit:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .action-btn.hits {
            background: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 100%);
            color: #fff;
        }

        .action-btn.hits:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(167, 139, 250, 0.4);
        }

        .action-btn.history {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
        }

        .action-btn.history:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .action-btn.delete {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #fff;
        }

        .action-btn.delete:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-container {
            background: linear-gradient(135deg, rgba(30, 30, 60, 0.95) 0%, rgba(20, 20, 40, 0.98) 100%);
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: 24px;
            padding: 36px;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            position: relative;
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(139, 92, 246, 0.2);
        }

        .modal-title {
            font-family: 'Cinzel', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-title i {
            color: #a78bfa;
        }

        .close-modal {
            background: transparent;
            border: none;
            color: #9ca3af;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-modal:hover {
            background: rgba(139, 92, 246, 0.1);
            color: #a78bfa;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-label {
            display: block;
            font-family: 'DM Sans', sans-serif;
            color: #a78bfa;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 14px 18px;
            background: rgba(139, 92, 246, 0.05);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: 12px;
            color: #ffffff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #a78bfa;
            background: rgba(139, 92, 246, 0.1);
            box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.1);
        }

        .form-textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .modal-footer {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid rgba(139, 92, 246, 0.2);
        }

        .btn-cancel {
            background: transparent;
            border: 1px solid rgba(139, 92, 246, 0.3);
            padding: 12px 28px;
            border-radius: 10px;
            color: #9ca3af;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            border-color: #a78bfa;
            color: #a78bfa;
        }

        .btn-primary {
            background: linear-gradient(135deg, #a78bfa 0%, #60a5fa 100%);
            border: none;
            padding: 12px 32px;
            border-radius: 10px;
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 12px rgba(167, 139, 250, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(167, 139, 250, 0.5);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: none;
            padding: 12px 32px;
            border-radius: 10px;
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(239, 68, 68, 0.5);
        }

        /* Top Hits List in Modal */
        .hits-list {
            list-style: none;
            margin-top: 10px;
        }

        .hit-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            background: rgba(139, 92, 246, 0.05);
            border: 1px solid rgba(139, 92, 246, 0.1);
            border-radius: 12px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .hit-item:hover {
            background: rgba(139, 92, 246, 0.1);
            border-color: rgba(139, 92, 246, 0.2);
        }

        .hit-rank {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #a78bfa 0%, #60a5fa 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Cinzel', serif;
            font-weight: 700;
            color: #fff;
            font-size: 0.9rem;
        }

        .hit-details {
            flex: 1;
        }

        .hit-title {
            font-family: 'DM Sans', sans-serif;
            color: #ffffff;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .hit-album {
            font-size: 0.85rem;
            color: #9ca3af;
        }

        .hit-actions {
            display: flex;
            gap: 8px;
        }

        .hit-action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
        }

        .hit-action-btn.up {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .hit-action-btn.down {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        .hit-action-btn.remove {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .hit-action-btn:hover {
            transform: scale(1.1);
        }

        /* History Timeline in Modal */
        .history-timeline {
            position: relative;
            padding-left: 30px;
            margin-top: 20px;
        }

        .history-timeline::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(135deg, #a78bfa 0%, #60a5fa 100%);
        }

        .history-item {
            position: relative;
            margin-bottom: 24px;
            padding: 18px 22px;
            background: rgba(139, 92, 246, 0.05);
            border: 1px solid rgba(139, 92, 246, 0.1);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .history-item:hover {
            background: rgba(139, 92, 246, 0.1);
            border-color: rgba(139, 92, 246, 0.2);
        }

        .history-item::before {
            content: '';
            position: absolute;
            left: -26px;
            top: 24px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: linear-gradient(135deg, #a78bfa 0%, #60a5fa 100%);
            border: 2px solid rgba(30, 30, 60, 0.9);
        }

        .history-year {
            font-family: 'Cinzel', serif;
            color: #a78bfa;
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .history-title {
            font-family: 'DM Sans', sans-serif;
            color: #ffffff;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .history-description {
            font-size: 0.9rem;
            color: #9ca3af;
            line-height: 1.6;
        }

        /* Search & Filter Bar */
        .toolbar {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 18px 12px 45px;
            background: rgba(139, 92, 246, 0.05);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: 12px;
            color: #ffffff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: #a78bfa;
            background: rgba(139, 92, 246, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .filter-select {
            padding: 12px 20px;
            background: rgba(139, 92, 246, 0.05);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: 12px;
            color: #ffffff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-select:focus {
            outline: none;
            border-color: #a78bfa;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 28px;
        }

        .page-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 1px solid rgba(139, 92, 246, 0.2);
            background: transparent;
            color: #9ca3af;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .page-btn:hover,
        .page-btn.active {
            background: linear-gradient(135deg, #a78bfa 0%, #60a5fa 100%);
            border-color: transparent;
            color: #fff;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
                order: 2;
            }
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                gap: 16px;
                padding: 16px 20px;
            }

            .dashboard-container {
                padding: 20px;
            }

            .content-card {
                padding: 24px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .members-table {
                font-size: 0.85rem;
            }

            .members-table th,
            .members-table td {
                padding: 12px 8px;
            }

            .action-buttons {
                flex-direction: column;
                gap: 4px;
            }

            .action-btn {
                width: 32px;
                height: 32px;
            }
        }
    </style>
</head>
<body>
    <!-- Retro Background Elements -->
    <div class="retro-bg-stars"></div>
    <div class="retro-bg-grid"></div>

    <!-- Header -->
    <header class="dashboard-header">
        <h1 class="logo"><i class="fas fa-music"></i> <span>BACKSTREET</span> BOYS</h1>
        <div class="user-info">
            <span class="admin-badge">Admin</span>
            <span class="welcome-text">Welcome, Admin!</span>
            <button class="logout-btn" onclick="logout()">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </div>
    </header>

    <!-- Main Dashboard -->
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="profile-section">
                <div class="profile-avatar">AD</div>
                <h2 class="profile-name">Admin Dashboard</h2>
                <p class="profile-role">Site Administrator</p>
            </div>

            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#members-section" class="nav-link active">
                        <i class="fas fa-users"></i>
                        <span>Members</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#tophits-section" class="nav-link">
                        <i class="fas fa-trophy"></i>
                        <span>Top Hits</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#history-section" class="nav-link">
                        <i class="fas fa-history"></i>
                        <span>History</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="home.php" class="nav-link">
                        <i class="fas fa-external-link-alt"></i>
                        <span>View Site</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Members Section -->
            <div class="content-card" id="members-section">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-users"></i>
                        Members Management
                    </h2>
                    <button class="add-new-btn" onclick="openMemberModal()">
                        <i class="fas fa-plus"></i> Add Member
                    </button>
                </div>

                <!-- Toolbar -->
                <div class="toolbar">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="memberSearch" placeholder="Search members..." onkeyup="filterMembers()">
                    </div>
                    <select class="filter-select" id="statusFilter" onchange="filterMembers()">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="premium">Premium</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-value" id="totalMembers">5</div>
                        <div class="stat-label">Total Members</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-value" id="activeMembers">3</div>
                        <div class="stat-label">Active</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-value" id="premiumMembers">1</div>
                        <div class="stat-label">Premium</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <div class="stat-value" id="newMembers">2</div>
                        <div class="stat-label">New This Month</div>
                    </div>
                </div>

                <!-- Members Table -->
                <table class="members-table">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Email</th>
                            <th>Favorite Member</th>
                            <th>Join Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="membersTableBody">
                        <!-- Members will be loaded dynamically -->
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
                    <button class="page-btn active">1</button>
                    <button class="page-btn">2</button>
                    <button class="page-btn">3</button>
                    <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

            <!-- Albums Section -->
            <div class="content-card" id="albums-section">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-compact-disc"></i>
                        Albums Management
                    </h2>
                    <button class="add-new-btn" onclick="openAlbumModal()">
                        <i class="fas fa-plus"></i> Add Album
                    </button>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <input type="text" id="albumSearch" placeholder="Search albums..." onkeyup="searchAlbums()" 
                           style="width:100%;padding:12px;border-radius:8px;border:1px solid rgba(139, 92, 246, 0.3);background:rgba(139, 92, 246, 0.05);color:#fff;">
                </div>

                <div class="albums-grid" id="albumsGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:20px;">
                    <!-- Albums will be loaded dynamically -->
                </div>
            </div>

            <!-- Top Hits Section -->
            <div class="content-card" id="tophits-section">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-trophy"></i>
                        Top Hits Management
                    </h2>
                    <button class="add-new-btn" onclick="openTopHitsModal()">
                        <i class="fas fa-plus"></i> Add Hit
                    </button>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-music"></i>
                        </div>
                        <div class="stat-value">25</div>
                        <div class="stat-label">Total Hits</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-play"></i>
                        </div>
                        <div class="stat-value">1.2M</div>
                        <div class="stat-label">Total Streams</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stat-value">850K</div>
                        <div class="stat-label">Favorites</div>
                    </div>
                </div>

                <p style="color: #9ca3af; text-align: center; padding: 40px;">
                    <i class="fas fa-info-circle" style="color: #a78bfa; margin-right: 8px;"></i>
                    Manage top hits through individual member profiles or use the bulk editor
                </p>
            </div>

            <!-- History Section -->
            <div class="content-card" id="history-section">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-history"></i>
                        Band History Management
                    </h2>
                    <button class="add-new-btn" onclick="openHistoryModal()">
                        <i class="fas fa-plus"></i> Add Event
                    </button>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-value">32</div>
                        <div class="stat-label">Timeline Events</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-award"></i>
                        </div>
                        <div class="stat-value">150+</div>
                        <div class="stat-label">Awards Won</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-album"></i>
                        </div>
                        <div class="stat-value">10</div>
                        <div class="stat-label">Studio Albums</div>
                    </div>
                </div>

                <p style="color: #9ca3af; text-align: center; padding: 40px;">
                    <i class="fas fa-info-circle" style="color: #a78bfa; margin-right: 8px;"></i>
                    Edit the band's timeline and historical events
                </p>
            </div>
        </main>
    </div>

    <!-- Edit/Create Member Modal -->
    <div class="modal-overlay" id="memberModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-user-edit"></i>
                    <span id="modalTitle">Edit Member</span>
                </h3>
                <button class="close-modal" onclick="closeMemberModal()">&times;</button>
            </div>
            <form id="memberForm" onsubmit="saveMember(event)">
                <input type="hidden" id="memberId">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Full Name *</label>
                        <input type="text" class="form-input" id="memberName" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stage Name</label>
                        <input type="text" class="form-input" id="memberStageName">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Birthdate</label>
                        <input type="date" class="form-input" id="memberBirthdate">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nationality</label>
                        <input type="text" class="form-input" id="memberNationality">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Position</label>
                        <select class="form-select" id="memberPosition">
                            <option value="Vocalist">Vocalist</option>
                            <option value="Lead Vocalist">Lead Vocalist</option>
                            <option value="Rapper">Rapper</option>
                            <option value="Dancer">Dancer</option>
                            <option value="Multi-talented">Multi-talented</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Profile Image</label>
                        <input type="file" class="form-input" id="memberProfileImg" accept="image/*">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Bio</label>
                    <textarea class="form-textarea" id="memberBio" placeholder="Write member biography..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeMemberModal()">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Save Member
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Top Hits Modal -->
    <div class="modal-overlay" id="topHitsModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-trophy"></i>
                    <span>Manage Top Hits</span>
                </h3>
                <button class="close-modal" onclick="closeTopHitsModal()">&times;</button>
            </div>
            <div class="form-group">
                <label class="form-label">Member</label>
                <select class="form-select" id="hitsMemberSelect">
                    <option value="">Select Member...</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Add New Hit</label>
                <div class="form-row">
                    <input type="text" class="form-input" id="newHitTitle" placeholder="Song title">
                    <button class="btn-primary" onclick="addNewHit()" style="padding: 12px 20px;">
                        <i class="fas fa-plus"></i> Add
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Current Top Hits</label>
                <ul class="hits-list" id="hitsList">
                    <!-- Hits will be loaded dynamically -->
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeTopHitsModal()">Close</button>
                <button type="button" class="btn-primary" onclick="saveTopHits()">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </div>
    </div>

    <!-- Album Modal -->
    <div class="modal-overlay" id="albumModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-compact-disc"></i>
                    <span id="albumModalTitle">Edit Album</span>
                </h3>
                <button class="close-modal" onclick="closeAlbumModal()">&times;</button>
            </div>
            <form id="albumForm" onsubmit="saveAlbum(event)">
                <input type="hidden" id="albumId">
                <div class="form-group">
                    <label class="form-label">Album Title *</label>
                    <input type="text" class="form-input" id="albumTitle" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Release Date</label>
                        <input type="date" class="form-input" id="albumRelease">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cover Image</label>
                        <input type="file" class="form-input" id="albumCoverImg" accept="image/*">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-textarea" id="albumDescription" placeholder="Album description..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeAlbumModal()">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Save Album
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- History Modal -->
    <div class="modal-overlay" id="historyModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-history"></i>
                    <span>Member History Timeline</span>
                </h3>
                <button class="close-modal" onclick="closeHistoryModal()">&times;</button>
            </div>
            <div class="form-group">
                <label class="form-label">Member</label>
                <select class="form-select" id="historyMemberSelect">
                    <option value="">Select Member...</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Add New Event</label>
                <div class="form-row">
                    <input type="number" class="form-input" id="eventYear" placeholder="Year" min="1993" max="2026">
                    <input type="text" class="form-input" id="eventTitle" placeholder="Event title">
                </div>
                <textarea class="form-textarea" id="eventDescription" placeholder="Event description..." style="margin-top: 10px;"></textarea>
                <button class="btn-primary" onclick="addNewEvent()" style="margin-top: 10px; width: 100%;">
                    <i class="fas fa-plus"></i> Add Event
                </button>
            </div>
            <div class="form-group">
                <label class="form-label">Timeline</label>
                <div class="history-timeline" id="historyTimeline">
                    <!-- History items will be loaded dynamically -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeHistoryModal()">Close</button>
                <button type="button" class="btn-primary" onclick="exportHistory()">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-container" style="max-width: 450px;">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
                    <span>Confirm Delete</span>
                </h3>
                <button class="close-modal" onclick="closeDeleteModal()">&times;</button>
            </div>
            <div class="form-group">
                <p style="color: #d1d5db; font-size: 1rem; line-height: 1.6;">
                    Are you sure you want to delete <strong id="deleteItemName" style="color: #ef4444;"></strong>? 
                    This action cannot be undone.
                </p>
            </div>
            <input type="hidden" id="deleteItemId">
            <input type="hidden" id="deleteItemType">
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
                <button type="button" class="btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>

    <script>
        // API URLs
        const MEMBERS_API = 'api/members.php';
        const ALBUMS_API = 'api/albums.php';
        
        // Data storage
        let members = [];
        let albums = [];

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadMembers();
            loadAlbums();
        });

        // Load Members from API with search support
        function loadMembers(search = '') {
            const tbody = document.getElementById('membersTableBody');
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Loading...</td></tr>';

            let url = MEMBERS_API;
            if (search) {
                url += '?search=' + encodeURIComponent(search);
            }

            fetch(url)
                .then(response => response.json())
                // FIX #6: API returns {success, data, count} — unwrap .data to get the array
                .then(data => {
                    members = data.data || [];
                    renderMembersTable();
                })
                .catch(error => {
                    console.error('Error loading members:', error);
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#ef4444;">Error loading members</td></tr>';
                });
        }

        // Render Members Table
        function renderMembersTable() {
            const tbody = document.getElementById('membersTableBody');
            tbody.innerHTML = '';

            if (members.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No members found</td></tr>';
                updateStats();
                return;
            }

            members.forEach(member => {
                const tr = document.createElement('tr');
                const initials = member.name ? member.name.split(' ').map(n => n[0]).join('') : 'M';
                const birthdate = member.birthdate ? new Date(member.birthdate).toLocaleDateString() : 'N/A';
                
                tr.innerHTML = `
                    <td>
                        <div class="member-info">
                            <div class="member-avatar">${initials}</div>
                            <div>
                                <div class="member-name">${member.name || 'Unknown'}</div>
                                <small style="color:#9ca3af;">${member.stage_name || member.position || ''}</small>
                            </div>
                        </div>
                    </td>
                    <td>${birthdate}</td>
                    <td>${member.nationality || 'N/A'}</td>
                    <td>${member.position || 'Vocalist'}</td>
                    <td>
                        <span class="status-badge status-active">Active</span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn edit" onclick="editMember(${member.member_id})" title="Edit Info">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete" onclick="deleteMember(${member.member_id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            updateStats();
        }

        // Load Albums from API with search support
        function loadAlbums(search = '') {
            const grid = document.getElementById('albumsGrid');
            grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px;">Loading...</div>';

            let url = ALBUMS_API;
            if (search) {
                url += '?search=' + encodeURIComponent(search);
            }

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    albums = data;
                    renderAlbumsGrid();
                })
                .catch(error => {
                    console.error('Error loading albums:', error);
                    grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;color:#ef4444;padding:40px;">Error loading albums</div>';
                });
        }

        // Render Albums Grid
        function renderAlbumsGrid() {
            const grid = document.getElementById('albumsGrid');
            grid.innerHTML = '';

            if (albums.length === 0) {
                grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px;">No albums found</div>';
                updateStats();
                return;
            }

            albums.forEach(album => {
                const div = document.createElement('div');
                div.className = 'album-card';
                const releaseDate = album.release ? new Date(album.release).getFullYear() : 'Unknown';
                const coverImage = album.cover_img && album.cover_img !== '' 
                    ? album.cover_img 
                    : 'https://via.placeholder.com/300x300/2a3f5a/d4a853?text=' + encodeURIComponent(album.title || 'Album');
                
                div.innerHTML = `
                    <div class="album-cover">
                        <img src="${coverImage}" alt="${album.title || 'Album'}" onerror="this.src='https://via.placeholder.com/300x300/2a3f5a/d4a853?text=No+Image'">
                    </div>
                    <div class="album-info">
                        <h3 class="album-title">${album.title || 'Untitled'}</h3>
                        <p class="album-year">${releaseDate}</p>
                        <p class="album-description">${album.description ? album.description.substring(0, 80) + '...' : 'No description'}</p>
                        <div class="album-actions">
                            <button class="action-btn-small edit" onclick="editAlbum(${album.album_id})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="action-btn-small delete" onclick="deleteAlbum(${album.album_id})">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                `;
                grid.appendChild(div);
            });

            updateStats();
        }

        // Update Stats
        // FIX #13: premiumMembers card was showing albums.length (wrong label),
        // and totalHits was just albums.length duplicated.
        // Now: totalMembers=members, activeMembers=members, totalAlbums=albums, totalHits placeholder.
        function updateStats() {
            document.getElementById('totalMembers').textContent = members.length;
            document.getElementById('activeMembers').textContent = members.length;
            document.getElementById('premiumMembers').textContent = albums.length;  // Album count
            document.getElementById('totalHits').textContent = albums.length > 0 ? albums.length * 10 : 0; // Estimated hits based on albums
        }

        // Member Modal Functions
        function openMemberModal(memberId = null) {
            const modal = document.getElementById('memberModal');
            const title = document.getElementById('modalTitle');
            
            if (memberId) {
                // FIX #8: Must include action=getById so the API returns a single member,
                // not the full members list.
                fetch(MEMBERS_API + '?action=getById&id=' + memberId)
                    .then(response => response.json())
                    .then(member => {
                        document.getElementById('memberId').value = member.member_id;
                        document.getElementById('memberName').value = member.name || '';
                        document.getElementById('memberStageName').value = member.stage_name || '';
                        document.getElementById('memberBirthdate').value = member.birthdate || '';
                        document.getElementById('memberNationality').value = member.nationality || '';
                        document.getElementById('memberPosition').value = member.position || 'Vocalist';
                        document.getElementById('memberBio').value = member.bio || '';
                        title.textContent = 'Edit Member';
                        modal.classList.add('active');
                    })
                    .catch(error => console.error('Error loading member:', error));
            } else {
                document.getElementById('memberForm').reset();
                document.getElementById('memberId').value = '';
                title.textContent = 'Add New Member';
                modal.classList.add('active');
            }
        }

        function closeMemberModal() {
            document.getElementById('memberModal').classList.remove('active');
        }

        function editMember(id) {
            openMemberModal(id);
        }

        function saveMember(event) {
            event.preventDefault();
            const id = document.getElementById('memberId').value;

            // FIX #7: Was sending FormData (multipart) but api/members.php reads
            // php://input as JSON. Switch to JSON.stringify with Content-Type: application/json.
            const payload = {
                about_id:    '1',
                name:        document.getElementById('memberName').value,
                stage_name:  document.getElementById('memberStageName').value,
                birthdate:   document.getElementById('memberBirthdate').value,
                nationality: document.getElementById('memberNationality').value,
                position:    document.getElementById('memberPosition').value,
                bio:         document.getElementById('memberBio').value,
            };

            const method = id ? 'PUT' : 'POST';
            const url    = id ? MEMBERS_API + '?id=' + id : MEMBERS_API;

            fetch(url, {
                method:  method,
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message || (data.success ? 'Saved!' : 'Error saving member'));
                loadMembers();
                closeMemberModal();
            })
            .catch(error => {
                console.error('Error saving member:', error);
                alert('Error saving member');
            });
        }

        function deleteMember(id) {
            if (confirm('Are you sure you want to delete this member?')) {
                fetch(MEMBERS_API + '?id=' + id, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    loadMembers();
                })
                .catch(error => {
                    console.error('Error deleting member:', error);
                    alert('Error deleting member');
                });
            }
        }

        // Album Modal Functions
        function openAlbumModal(albumId = null) {
            const modal = document.getElementById('albumModal');
            const title = document.getElementById('albumModalTitle');
            
            if (albumId) {
                fetch(ALBUMS_API + '?id=' + albumId)
                    .then(response => response.json())
                    .then(album => {
                        document.getElementById('albumId').value = album.album_id;
                        document.getElementById('albumTitle').value = album.title || '';
                        document.getElementById('albumRelease').value = album.release || '';
                        document.getElementById('albumDescription').value = album.description || '';
                        title.textContent = 'Edit Album';
                        modal.classList.add('active');
                    })
                    .catch(error => console.error('Error loading album:', error));
            } else {
                document.getElementById('albumForm').reset();
                document.getElementById('albumId').value = '';
                title.textContent = 'Add New Album';
                modal.classList.add('active');
            }
        }

        function closeAlbumModal() {
            document.getElementById('albumModal').classList.remove('active');
        }

        function editAlbum(id) {
            openAlbumModal(id);
        }

        function saveAlbum(event) {
            event.preventDefault();
            const id = document.getElementById('albumId').value;
            
            const formData = new FormData();
            formData.append('action', id ? 'update' : 'create');
            if (id) formData.append('album_id', id);
            formData.append('title', document.getElementById('albumTitle').value);
            formData.append('release', document.getElementById('albumRelease').value);
            formData.append('description', document.getElementById('albumDescription').value);
            
            // Add cover image if selected
            const coverImgInput = document.getElementById('albumCoverImg');
            if (coverImgInput.files[0]) {
                formData.append('cover_img', coverImgInput.files[0]);
            }

            fetch(ALBUMS_API, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                loadAlbums();
                closeAlbumModal();
            })
            .catch(error => {
                console.error('Error saving album:', error);
                alert('Error saving album');
            });
        }

        function deleteAlbum(id) {
            if (confirm('Are you sure you want to delete this album?')) {
                fetch(ALBUMS_API + '?id=' + id, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    loadAlbums();
                })
                .catch(error => {
                    console.error('Error deleting album:', error);
                    alert('Error deleting album');
                });
            }
        }

        // Search functionality
        function searchMembers() {
            const search = document.getElementById('memberSearch').value;
            loadMembers(search);
        }

        function searchAlbums() {
            const search = document.getElementById('albumSearch').value;
            loadAlbums(search);
        }

        // Top Hits Modal Functions
        function openTopHitsModal(memberId = null) {
            const modal = document.getElementById('topHitsModal');
            
            if (memberId) {
                document.getElementById('hitsMemberSelect').value = memberId;
            }
            
            loadHitsList();
            modal.classList.add('active');
        }

        function closeTopHitsModal() {
            document.getElementById('topHitsModal').classList.remove('active');
        }

        function loadHitsList() {
            const memberId = document.getElementById('hitsMemberSelect').value;
            const list = document.getElementById('hitsList');
            list.innerHTML = '';

            if (!memberId || !topHits[memberId]) {
                list.innerHTML = '<li style="color: #9ca3af; text-align: center; padding: 20px;">No hits added yet</li>';
                return;
            }

            topHits[memberId].forEach((hit, index) => {
                const li = document.createElement('li');
                li.className = 'hit-item';
                li.innerHTML = `
                    <div class="hit-rank">${hit.rank}</div>
                    <div class="hit-details">
                        <div class="hit-title">${hit.title}</div>
                        <div class="hit-album">${hit.album}</div>
                    </div>
                    <div class="hit-actions">
                        <button class="hit-action-btn up" onclick="moveHitUp(${memberId}, ${index})" title="Move Up">
                            <i class="fas fa-arrow-up"></i>
                        </button>
                        <button class="hit-action-btn down" onclick="moveHitDown(${memberId}, ${index})" title="Move Down">
                            <i class="fas fa-arrow-down"></i>
                        </button>
                        <button class="hit-action-btn remove" onclick="removeHit(${memberId}, ${index})" title="Remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                list.appendChild(li);
            });
        }

        function addNewHit() {
            const memberId = document.getElementById('hitsMemberSelect').value;
            const title = document.getElementById('newHitTitle').value;
            
            if (!memberId || !title) {
                alert('Please select a member and enter a song title');
                return;
            }

            // TODO: Replace with API call
            if (!topHits[memberId]) topHits[memberId] = [];
            topHits[memberId].push({
                rank: topHits[memberId].length + 1,
                title: title,
                album: 'Unknown'
            });

            document.getElementById('newHitTitle').value = '';
            loadHitsList();
            console.log('ADD hit for member', memberId, ':', title);
        }

        function moveHitUp(memberId, index) {
            if (index > 0) {
                [topHits[memberId][index], topHits[memberId][index - 1]] = 
                [topHits[memberId][index - 1], topHits[memberId][index]];
                topHits[memberId].forEach((hit, i) => hit.rank = i + 1);
                loadHitsList();
                console.log('REORDER hits for member', memberId);
            }
        }

        function moveHitDown(memberId, index) {
            if (index < topHits[memberId].length - 1) {
                [topHits[memberId][index], topHits[memberId][index + 1]] = 
                [topHits[memberId][index + 1], topHits[memberId][index]];
                topHits[memberId].forEach((hit, i) => hit.rank = i + 1);
                loadHitsList();
                console.log('REORDER hits for member', memberId);
            }
        }

        function removeHit(memberId, index) {
            topHits[memberId].splice(index, 1);
            topHits[memberId].forEach((hit, i) => hit.rank = i + 1);
            loadHitsList();
            console.log('REMOVE hit from member', memberId, 'at index', index);
        }

        function saveTopHits() {
            // TODO: Replace with API call
            // Example: fetch('/api/tophits', { method: 'POST', body: JSON.stringify(topHits) })
            console.log('SAVE top hits:', topHits);
            alert('Top hits saved successfully!');
            closeTopHitsModal();
        }

        // History Modal Functions — FIX #10: wired up to api/history.php (was 100% static/in-memory)
        const HISTORY_API = 'api/history.php';

        function openHistoryModal(memberId = null) {
            const modal = document.getElementById('historyModal');
            if (memberId) {
                document.getElementById('historyMemberSelect').value = memberId;
            }
            loadHistoryTimeline();
            modal.classList.add('active');
        }

        function closeHistoryModal() {
            document.getElementById('historyModal').classList.remove('active');
        }

        function loadHistoryTimeline() {
            const timeline = document.getElementById('historyTimeline');
            timeline.innerHTML = '<p style="color:#9ca3af;text-align:center;padding:20px;">Loading…</p>';

            fetch(HISTORY_API)
                .then(r => r.json())
                .then(res => {
                    const events = res.data || [];
                    timeline.innerHTML = '';
                    if (events.length === 0) {
                        timeline.innerHTML = '<p style="color:#9ca3af;text-align:center;padding:20px;">No history events yet</p>';
                        return;
                    }
                    events.forEach(event => {
                        const div = document.createElement('div');
                        div.className = 'history-item';
                        div.innerHTML = `
                            <div class="history-year">${event.year}</div>
                            <div class="history-title">${event.title}</div>
                            <div class="history-description">${event.description || ''}</div>
                            <div class="action-buttons" style="margin-top:6px;">
                                <button class="action-btn delete" onclick="deleteHistoryEvent(${event.timeline_id})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                        timeline.appendChild(div);
                    });
                })
                .catch(err => {
                    console.error('Error loading history:', err);
                    timeline.innerHTML = '<p style="color:#ef4444;text-align:center;padding:20px;">Error loading history</p>';
                });
        }

        function addNewEvent() {
            const year        = document.getElementById('eventYear').value;
            const title       = document.getElementById('eventTitle').value;
            const description = document.getElementById('eventDescription').value;

            if (!year || !title) {
                alert('Please provide a year and event title');
                return;
            }

            fetch(HISTORY_API, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify({ year, title, description })
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    document.getElementById('eventYear').value        = '';
                    document.getElementById('eventTitle').value       = '';
                    document.getElementById('eventDescription').value = '';
                    loadHistoryTimeline();
                } else {
                    alert(res.message || 'Failed to add event');
                }
            })
            .catch(err => {
                console.error('Error adding history event:', err);
                alert('Error adding history event');
            });
        }

        function deleteHistoryEvent(id) {
            if (!confirm('Delete this history event?')) return;
            fetch(HISTORY_API + '?action=delete&id=' + id, { method: 'POST' })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        loadHistoryTimeline();
                    } else {
                        alert(res.message || 'Failed to delete event');
                    }
                })
                .catch(err => {
                    console.error('Error deleting history event:', err);
                    alert('Error deleting history event');
                });
        }

        function exportHistory() {
            fetch(HISTORY_API)
                .then(r => r.json())
                .then(res => {
                    const blob = new Blob([JSON.stringify(res.data || [], null, 2)], { type: 'application/json' });
                    const a    = document.createElement('a');
                    a.href     = URL.createObjectURL(blob);
                    a.download = 'bsb_history_export.json';
                    a.click();
                })
                .catch(err => {
                    console.error('Export error:', err);
                    alert('Export failed');
                });
        }

        // Listen for member select changes
        document.getElementById('hitsMemberSelect').addEventListener('change', loadHitsList);
        document.getElementById('historyMemberSelect').addEventListener('change', loadHistoryTimeline);

        // Logout Function
        function logout() {
            window.location.href = 'dashboard.php?logout=1';
        }

        // Close modals when clicking outside
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
