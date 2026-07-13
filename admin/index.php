<?php
include("../includes/db_connect.php");
include("includes/header.php");

// Initialize counts
$total_plants = 0;
$total_orders = 0;
$total_users = 0;
$total_revenue = 0;

if ($conn) {
    // Get counts
    $plants_query = mysqli_query($conn, "SELECT COUNT(id) as count FROM plants");
    if($plants_query) $total_plants = mysqli_fetch_assoc($plants_query)['count'];

    $orders_query = mysqli_query($conn, "SELECT COUNT(id) as count, SUM(total_amount) as total FROM orders");
    if($orders_query) {
        $order_data = mysqli_fetch_assoc($orders_query);
        $total_orders = $order_data['count'];
        $total_revenue = $order_data['total'] ? $order_data['total'] : 0;
    }

    $users_query = mysqli_query($conn, "SELECT COUNT(id) as count FROM users WHERE role='customer'");
    if($users_query) $total_users = mysqli_fetch_assoc($users_query)['count'];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="admin-page-title mb-0">Dashboard Overview</h2>
    <span class="text-secondary" style="font-weight: 500;"><i class="fa-solid fa-calendar me-2"></i> <?php echo date('d M, Y'); ?></span>
</div>

<?php if (!$conn): ?>
    <div class="alert alert-warning border-0" style="background: #fff3cd; color: #856404; font-weight: 500;">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> Database connection failed. Stats are showing 0. Please import the database to see real data.
    </div>
<?php endif; ?>

<div class="row g-4 mb-5">
    <!-- Stat 1 -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fa-solid fa-seedling"></i>
            </div>
            <div>
                <p class="text-secondary mb-1" style="font-size: 0.9rem; font-weight: 500;">Total Plants</p>
                <h3 style="font-family: var(--font-heading); font-weight: 700; color: var(--text-primary); margin: 0;"><?php echo $total_plants; ?></h3>
            </div>
        </div>
    </div>
    
    <!-- Stat 2 -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <div>
                <p class="text-secondary mb-1" style="font-size: 0.9rem; font-weight: 500;">Total Orders</p>
                <h3 style="font-family: var(--font-heading); font-weight: 700; color: var(--text-primary); margin: 0;"><?php echo $total_orders; ?></h3>
            </div>
        </div>
    </div>

    <!-- Stat 3 -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <p class="text-secondary mb-1" style="font-size: 0.9rem; font-weight: 500;">Customers</p>
                <h3 style="font-family: var(--font-heading); font-weight: 700; color: var(--text-primary); margin: 0;"><?php echo $total_users; ?></h3>
            </div>
        </div>
    </div>

    <!-- Stat 4 -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div>
                <p class="text-secondary mb-1" style="font-size: 0.9rem; font-weight: 500;">Total Revenue</p>
                <h3 style="font-family: var(--font-heading); font-weight: 700; color: var(--text-primary); margin: 0;">Rs <?php echo number_format($total_revenue); ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0" style="font-family: var(--font-heading); font-weight: 700; color: var(--text-primary);">Recent Orders</h5>
                <a href="manage_orders.php" class="btn btn-sm btn-light border" style="font-weight: 500; color: #4b5563;">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($conn) {
                            $recent_query = "SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC LIMIT 5";
                            $recent_result = mysqli_query($conn, $recent_query);
                            
                            if ($recent_result && mysqli_num_rows($recent_result) > 0) {
                                while ($row = mysqli_fetch_assoc($recent_result)) {
                                    $status = $row['status'];
                                    $badge_class = 'bg-secondary';
                                    if($status == 'Pending') $badge_class = 'bg-warning text-dark';
                                    elseif($status == 'Quality Check') $badge_class = 'bg-info text-dark';
                                    elseif($status == 'In Transit') $badge_class = 'bg-primary';
                                    elseif($status == 'Delivered') $badge_class = 'bg-success';
                                    elseif($status == 'Cancelled') $badge_class = 'bg-danger';

                                    echo "<tr>";
                                    echo "<td style='font-weight: 600;'>#{$row['id']}</td>";
                                    echo "<td>{$row['customer_name']}</td>";
                                    echo "<td>" . date('d M Y, h:i A', strtotime($row['order_date'])) . "</td>";
                                    echo "<td style='font-weight: 600;'>Rs " . number_format($row['total_amount']) . "</td>";
                                    echo "<td><span class='badge {$badge_class} px-2 py-1'>{$status}</span></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-4 text-secondary'>No recent orders found.</td></tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
