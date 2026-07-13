<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check: Only allow 'admin' role
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//     header("Location: ../auth.php");
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FloraFetch - Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Main Style -->
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <style>
        /* Admin specific overrides for Light Theme */
        html, body {
            max-width: 100%;
            overflow-x: hidden;
        }
        body {
            background: #f4f7f6; /* slightly different from frontend to distinguish admin */
        }
        .admin-layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }
        
        /* Premium Light Sidebar */
        .sidebar {
            width: 280px;
            background: #ffffff;
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            box-shadow: 2px 0 12px rgba(0,0,0,0.03);
        }
        .sidebar-brand {
            padding: 28px 24px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 15px;
        }
        .sidebar-nav {
            list-style: none;
            padding: 0 15px;
            margin: 0;
            flex-grow: 1;
        }
        .sidebar-nav li {
            margin-bottom: 6px;
        }
        .sidebar-nav .nav-link {
            color: #4b5563 !important;
            padding: 12px 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s;
            text-decoration: none;
        }
        .sidebar-nav .nav-link:hover {
            background: #f3f4f6;
            color: #111827 !important;
        }
        .sidebar-nav .nav-link.active {
            background: var(--primary-glow);
            color: var(--primary-dark) !important;
            font-weight: 600;
        }
        .sidebar-nav .nav-link i {
            font-size: 1.15rem;
            width: 24px;
            text-align: center;
            color: #9ca3af;
            transition: color 0.2s;
        }
        .sidebar-nav .nav-link:hover i {
            color: #4b5563;
        }
        .sidebar-nav .nav-link.active i {
            color: var(--primary);
        }
        
        .sidebar-footer {
            padding: 24px;
            border-top: 1px solid var(--border);
            background: #fafafa;
        }
        
        /* Main Content Area */
        .main-content {
            flex-grow: 1;
            margin-left: 280px;
            padding: 40px;
            min-width: 0;
        }
        
        /* Admin Card styling */
        .admin-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 28px;
            box-shadow: var(--shadow-sm);
            height: 100%;
        }
        .admin-page-title {
            font-family: var(--font-heading);
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 24px;
            font-size: 1.75rem;
        }
        
        /* Enhanced Tables */
        .table {
            color: var(--text-primary);
            vertical-align: middle;
            margin-bottom: 0;
            white-space: nowrap;
        }
        .table thead th {
            background: #f8fafc;
            color: #64748b;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border);
            padding: 16px;
            font-weight: 600;
        }
        .table tbody td {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            color: #334155;
            font-size: 0.95rem;
        }
        .table tbody tr:hover td {
            background-color: #f8fafc;
        }
        
        /* Custom scrollbar for tables */
        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Form Overrides */
        .admin-form-control {
            background: #ffffff;
            border: 1px solid var(--border);
            color: var(--text-primary);
            border-radius: 8px;
            padding: 10px 15px;
        }
        .admin-form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }
        
        /* Stat Cards */
        .stat-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
        }
        .stat-icon.green { background: #dcfce7; color: #16a34a; }
        .stat-icon.blue { background: #dbeafe; color: #2563eb; }
        .stat-icon.purple { background: #f3e8ff; color: #9333ea; }
        .stat-icon.orange { background: #ffedd5; color: #ea580c; }
        
        /* Responsive */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                width: 260px;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .sidebar-brand {
                padding-right: 45px !important;
            }
            .main-content {
                margin-left: 0;
                padding: 15px;
                width: 100%;
                overflow-x: hidden;
                min-width: 0;
            }
            .admin-card {
                padding: 15px;
                overflow: hidden;
            }
            .flora-brand {
                font-size: 1.3rem !important;
            }
            .admin-page-title {
                font-size: 1.4rem;
                margin-bottom: 15px;
            }
            .stat-card {
                padding: 15px;
                gap: 15px;
            }
            .stat-icon {
                width: 45px;
                height: 45px;
                font-size: 1.3rem;
            }
            .stat-card h3 {
                font-size: 1.4rem !important;
            }
        }
    </style>
</head>
<body>

<?php $current_page = basename($_SERVER['PHP_SELF']); ?>

<div class="admin-layout">
    <!-- ======= ADMIN SIDEBAR ======= -->
    <aside class="sidebar" id="adminSidebar">
        <div class="sidebar-brand text-center position-relative">
            <a class="flora-brand" href="index.php" style="font-size: 1.8rem; display: inline-block;">
                <i class="fa-solid fa-leaf"></i> Flora<span>Admin</span>
            </a>
            <button type="button" class="btn-close d-lg-none position-absolute top-50 end-0 translate-middle-y me-3" onclick="document.getElementById('adminSidebar').classList.remove('show');"></button>
        </div>
        
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Auto wrap all tables for responsiveness
            document.querySelectorAll('.table').forEach(function(table) {
                if (!table.parentElement.classList.contains('table-responsive')) {
                    var wrapper = document.createElement('div');
                    wrapper.className = 'table-responsive';
                    wrapper.style.border = 'none';
                    table.parentNode.insertBefore(wrapper, table);
                    wrapper.appendChild(table);
                }
            });
        });
        </script>
        
        <ul class="sidebar-nav mt-3">
            <li>
                <a href="index.php" class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-chart-pie"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="manage_plants.php" class="nav-link <?php echo ($current_page == 'manage_plants.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-seedling"></i> Manage Plants
                </a>
            </li>
            <li>
                <a href="manage_orders.php" class="nav-link <?php echo ($current_page == 'manage_orders.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-cart-flatbed"></i> Orders
                </a>
            </li>
            <li>
                <a href="manage_users.php" class="nav-link <?php echo ($current_page == 'manage_users.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-users"></i> Customers
                </a>
            </li>
            <li>
                <a href="manage_reviews.php" class="nav-link <?php echo ($current_page == 'manage_reviews.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-star"></i> Manage Reviews
                </a>
            </li>
        </ul>
        
        <div class="sidebar-footer">
            <a href="../index.php" class="btn btn-light w-100 mb-2" style="border: 1px solid var(--border); font-weight: 500; color: #4b5563;">
                <i class="fa-solid fa-arrow-up-right-from-square me-2"></i>View Live Store
            </a>
            <a href="../logout.php" class="btn w-100" style="background: #fee2e2; color: #dc2626; font-weight: 500; border: none;">
                <i class="fa-solid fa-power-off me-2"></i>Logout
            </a>
        </div>
    </aside>

    <!-- ======= MAIN CONTENT ======= -->
    <main class="main-content">
        <!-- Mobile Toggle Button -->
        <button class="btn btn-light d-lg-none mb-4 border" type="button" onclick="document.getElementById('adminSidebar').classList.toggle('show');">
            <i class="fa-solid fa-bars text-dark"></i> Menu
        </button>
        
        <div class="container-fluid">
