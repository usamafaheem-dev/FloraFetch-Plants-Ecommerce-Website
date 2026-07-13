<?php
require_once __DIR__ . '/db_connect.php';

// Start session if not already started (for cart & login)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Calculate total items in cart for the badge
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $quantity) {
        $cart_count += (int)$quantity;
    }
}

// Calculate total items in wishlist for the badge
$wishlist_count = 0;
if (isset($_SESSION['wishlist']) && is_array($_SESSION['wishlist'])) {
    $wishlist_count = count($_SESSION['wishlist']);
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['user_name'] : '';
$is_admin = ($is_logged_in && isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

// Fetch unread notifications count
$notif_count = 0;
$notifications = [];
if ($is_logged_in && isset($conn)) {
    $uid = (int)$_SESSION['user_id'];
    // Count unread
    $nc_q = mysqli_query($conn, "SELECT COUNT(*) as c FROM notifications WHERE user_id = $uid AND is_read = 0");
    if ($nc_q) {
        $notif_count = mysqli_fetch_assoc($nc_q)['c'];
    }
    // Fetch top 5
    $n_q = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id = $uid ORDER BY created_at DESC LIMIT 5");
    if ($n_q) {
        while($r = mysqli_fetch_assoc($n_q)) {
            $notifications[] = $r;
        }
    }
}
?>
<?php
// Determine if navbar should be transparent
$transparent_pages = ['index.php', 'shop.php', 'aboutus.php', 'contactus.php', 'cart.php', 'wishlist.php', 'my_orders.php', 'profile.php'];
$is_transparent = in_array(basename($_SERVER['PHP_SELF']), $transparent_pages);
$is_always_scrolled = in_array(basename($_SERVER['PHP_SELF']), ['product.php', 'checkout.php']);

$navbar_class = $is_transparent ? 'flora-navbar navbar-transparent' : 'flora-navbar';
if ($is_always_scrolled) {
    $navbar_class .= ' scrolled always-scrolled';
}

$toggler_color = ($is_transparent || $is_always_scrolled) ? '#ffffff' : '#111827';
?>

<!-- ======= NAVBAR ======= -->
<nav class="navbar navbar-expand-lg <?php echo $navbar_class; ?>" id="mainNavbar">
    <div class="container">
        <!-- Brand / Logo -->
        <a class="navbar-brand flora-brand" href="index.php">
            <i class="fa-solid fa-leaf"></i>
            Flora<span>Fetch</span>
        </a>

        <!-- Mobile Icons & Toggle -->
        <div class="d-flex align-items-center ms-auto gap-2">
            <!-- Mobile Cart -->
            <a class="nav-link cart-glass position-relative d-flex d-lg-none" href="cart.php" style="align-items: center; justify-content: center; width: 36px; height: 36px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 50%; backdrop-filter: blur(10px); color: var(--text-primary); padding: 0 !important; transition: all 0.3s ease;">
                <i class="fa-solid fa-cart-shopping" style="font-size: 0.9rem;"></i>
                <?php if ($cart_count > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill mt-1" style="background: #ef4444; font-size: 0.65rem; padding: 2px 4px; box-shadow: 0 2px 5px rgba(239,68,68,0.4); border: 2px solid var(--bg-card);"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation" style="padding: 4px 8px;">
                <i class="fa-solid fa-bars" style="color: <?php echo $toggler_color; ?>; font-size: 1.4rem;"></i>
            </button>
        </div>

        <!-- Nav Links -->
        <div class="collapse navbar-collapse order-lg-2" id="navbarContent">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 mt-3 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                        <i class="fa-solid fa-house me-1" style="color: #3b82f6;"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'shop.php' ? 'active' : ''; ?>" href="shop.php">
                        <i class="fa-solid fa-store me-1" style="color: #10b981;"></i> Shop
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'aboutus.php' ? 'active' : ''; ?>" href="aboutus.php">
                        <i class="fa-solid fa-circle-info me-1" style="color: #f59e0b;"></i> About
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contactus.php' ? 'active' : ''; ?>" href="contactus.php">
                        <i class="fa-solid fa-envelope me-1" style="color: #8b5cf6;"></i> Contact
                    </a>
                </li>
            </ul>

            <!-- Right Side: Account, Cart & Wishlist -->
            <ul class="navbar-nav align-items-center justify-content-center flex-row gap-3 mt-4 mt-lg-0 pb-2 pb-lg-0">
                
                <!-- Notification Bell -->
                <li class="nav-item dropdown">
                    <a class="nav-link position-relative" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; color: var(--text-primary); padding: 0 !important; transition: all 0.3s ease;">
                        <i class="fa-solid fa-bell" style="font-size: 1.25rem; <?php echo ($notif_count > 0) ? 'color: #f59e0b !important;' : ''; ?>"></i>
                        <?php if ($notif_count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background: #ef4444; font-size: 0.65rem; padding: 2px 4px; box-shadow: 0 2px 5px rgba(239,68,68,0.4); border: 2px solid var(--bg-card);"><?php echo $notif_count; ?></span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="notifDropdown" style="width: 320px; max-height: 400px; overflow-y: auto; border: 1px solid var(--border); border-radius: 12px; box-shadow: var(--shadow-md); margin-top: 10px;">
                        <li class="p-3 border-bottom d-flex justify-content-between align-items-center" style="background: #f8fafc; border-radius: 12px 12px 0 0;">
                            <h6 class="mb-0" style="font-weight: 600;">Notifications</h6>
                            <?php if ($notif_count > 0): ?>
                                <button class="btn btn-sm text-primary p-0 mark-read-btn" style="font-size: 0.8rem; font-weight: 500;">Mark all as read</button>
                            <?php endif; ?>
                        </li>
                        <?php if (!$is_logged_in): ?>
                            <li class="p-4 text-center text-muted">
                                <i class="fa-solid fa-lock mb-2" style="font-size: 1.5rem; opacity: 0.5;"></i>
                                <p class="mb-0" style="font-size: 0.9rem;">Please <a href="login.php" class="text-primary text-decoration-none">login</a> to view notifications.</p>
                            </li>
                        <?php elseif (empty($notifications)): ?>
                            <li class="p-4 text-center text-muted">
                                <i class="fa-regular fa-bell-slash mb-2" style="font-size: 1.5rem; opacity: 0.5;"></i>
                                <p class="mb-0" style="font-size: 0.9rem;">No new notifications.</p>
                            </li>
                        <?php else: ?>
                            <?php foreach($notifications as $n): ?>
                                <li>

                                    <a class="dropdown-item p-3 border-bottom text-wrap <?php echo $n['is_read'] ? 'bg-transparent' : 'bg-light'; ?>" href="#" style="white-space: normal;">
                                        <div class="d-flex gap-3">
                                            <div class="mt-1">
                                                <i class="fa-solid fa-circle-info text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1" style="font-size: 0.9rem; font-weight: 600;"><?php echo htmlspecialchars($n['title']); ?></h6>
                                                <p class="mb-1 text-muted" style="font-size: 0.85rem;"><?php echo htmlspecialchars($n['message']); ?></p>
                                                <small class="text-secondary" style="font-size: 0.75rem;"><?php echo date('M d, h:i A', strtotime($n['created_at'])); ?></small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </li>
                <!-- Wishlist -->
                <li class="nav-item">
                    <a class="nav-link cart-glass position-relative" href="wishlist.php" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 50%; backdrop-filter: blur(10px); color: var(--text-primary); padding: 0 !important; transition: all 0.3s ease;">
                        <i class="fa-regular fa-heart" style="font-size: 1rem;"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill wishlist-badge-count" style="background: #f43f5e; font-size: 0.7rem; padding: 3px 5px; box-shadow: 0 2px 5px rgba(244,63,94,0.4); border: 2px solid var(--bg-card); display: <?php echo $wishlist_count > 0 ? 'inline-block' : 'none'; ?>;"><?php echo $wishlist_count; ?></span>
                    </a>
                </li>

                <!-- Desktop Cart -->
                <li class="nav-item d-none d-lg-block">
                    <a class="nav-link cart-glass position-relative" href="cart.php" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 50%; backdrop-filter: blur(10px); color: var(--text-primary); padding: 0 !important; transition: all 0.3s ease;">
                        <i class="fa-solid fa-cart-shopping" style="font-size: 1rem;"></i>
                        <?php if ($cart_count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background: #ef4444; font-size: 0.7rem; padding: 3px 5px; box-shadow: 0 2px 5px rgba(239,68,68,0.4); border: 2px solid var(--bg-card);"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>

                <?php if ($is_logged_in): ?>
                    <!-- Logged In User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle user-glass" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="display: flex; align-items: center; gap: 8px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 50px; padding: 4px 12px 4px 4px !important; backdrop-filter: blur(10px); color: var(--text-primary);">
                            <?php 
                            $avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($user_name) . "&background=042f1f&color=fff&rounded=true&size=32";
                            if (!empty($_SESSION['profile_image'])) {
                                $avatar_url = $_SESSION['profile_image'];
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($avatar_url); ?>" alt="Avatar" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                            <span class="d-none d-md-inline" style="font-weight: 600; font-size: 0.85rem; letter-spacing: 0.5px;"><?php echo htmlspecialchars(strtoupper($user_name)); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end position-absolute" style="background: #fff; border: 1px solid var(--border); box-shadow: var(--shadow-md); border-radius: 12px; padding: 8px 0; margin-top: 10px;">
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <li><a class="dropdown-item" href="admin/index.php" style="color: var(--primary); font-weight: 500; padding: 8px 20px;"><i class="fa-solid fa-gauge-high me-2"></i>Admin Dashboard</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="profile.php" style="color: var(--text-secondary); font-weight: 500; padding: 8px 20px;"><i class="fa-solid fa-user me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item" href="my_orders.php" style="color: var(--text-secondary); font-weight: 500; padding: 8px 20px;"><i class="fa-solid fa-box me-2"></i>My Orders</a></li>
                            <li><hr class="dropdown-divider" style="border-color: var(--border); margin: 8px 0;"></li>
                            <li><a class="dropdown-item" href="logout.php" style="color: #dc2626; font-weight: 500; padding: 8px 20px;"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Login Button -->
                    <li class="nav-item">
                        <style>
                            .btn-login-hero {
                                background: linear-gradient(135deg, #22c55e, #15803d) !important;
                                color: #fff !important;
                                border: none !important;
                                box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4) !important;
                                transition: all 0.3s ease !important;
                            }
                            .btn-login-hero:hover {
                                transform: translateY(-2px);
                                box-shadow: 0 8px 25px rgba(34, 197, 94, 0.6) !important;
                                color: #fff !important;
                            }
                        </style>
                        <a class="btn btn-login-hero btn-sm" href="auth.php" style="padding: 10px 24px; font-size: 0.9rem; border-radius: 50px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; width: fit-content;">
                            <i class="fa-solid fa-right-to-bracket"></i> Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
        <div id="adminToast" class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-circle-info me-2"></i> Only users can perform this action.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        
        <?php if (isset($is_logged_in) && $is_logged_in && isset($notif_count) && $notif_count > 0 && !empty($notifications)): ?>
        <div id="userNotifToast" class="toast align-items-center bg-white border-0" role="alert" aria-live="assertive" aria-atomic="true" style="box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-radius: 12px; border-left: 4px solid var(--primary) !important;">
            <div class="toast-header border-0 bg-white" style="border-radius: 12px 12px 0 0;">
                <i class="fa-solid fa-bell text-primary me-2"></i>
                <strong class="me-auto text-dark" style="font-family: var(--font-heading);">New Notification!</strong>
                <button type="button" class="btn-close ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body text-secondary" style="font-size: 0.95rem;">
                <strong class="text-dark d-block mb-1"><?php echo htmlspecialchars($notifications[0]['title']); ?></strong>
                <?php echo htmlspecialchars($notifications[0]['message']); ?>
            </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var userToastEl = document.getElementById('userNotifToast');
                if(userToastEl) {
                    var userToast = new bootstrap.Toast(userToastEl, { delay: 6000 });
                    userToast.show();
                }
            });
        </script>
        <?php endif; ?>
    </div>

<style>
@media (max-width: 991.98px) {
    .navbar-collapse {
        background: radial-gradient(circle at 50% 0%, rgba(10, 61, 33, 0.95) 0%, rgba(3, 20, 9, 0.98) 100%);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        padding: 20px;
        border-radius: 15px;
        margin-top: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        border: 1px solid rgba(255,255,255,0.05);
    }
    .navbar-collapse .nav-link {
        color: #f8fafc !important;
        padding: 12px 15px !important;
        border-radius: 8px;
    }
    .navbar-collapse .nav-link:hover, .navbar-collapse .nav-link.active {
        background: rgba(255,255,255,0.1);
    }
    .flora-brand {
        font-size: 1.3rem !important;
    }
    .flora-brand i {
        font-size: 1.1rem !important;
    }
}
</style>
