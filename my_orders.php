<?php
session_start();
include("includes/db_connect.php");

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = '';

if (isset($_SESSION['order_msg'])) {
    $msg = $_SESSION['order_msg'];
    unset($_SESSION['order_msg']);
}

// Handle Order Cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order_id'])) {
    $cancel_id = (int)$_POST['cancel_order_id'];
    
    // Check if the order is Pending and belongs to the user
    $check_q = "SELECT status FROM orders WHERE id = $cancel_id AND user_id = $user_id AND status = 'Pending'";
    $check_r = mysqli_query($conn, $check_q);
    
    if ($check_r && mysqli_num_rows($check_r) > 0) {
        $upd_q = "UPDATE orders SET status = 'Cancelled' WHERE id = $cancel_id";
        if(mysqli_query($conn, $upd_q)) {
            // Restock items
            $items_q = "SELECT plant_id, quantity FROM order_items WHERE order_id = $cancel_id";
            $items_r = mysqli_query($conn, $items_q);
            if ($items_r) {
                while ($it = mysqli_fetch_assoc($items_r)) {
                    $pid = $it['plant_id'];
                    $qty = $it['quantity'];
                    mysqli_query($conn, "UPDATE plants SET stock_quantity = stock_quantity + $qty WHERE id = $pid");
                }
            }
            $_SESSION['order_msg'] = "<div class='alert alert-success mt-3'><i class='fa-solid fa-circle-check me-2'></i>Order #$cancel_id has been cancelled successfully.</div>";
        } else {
            $_SESSION['order_msg'] = "<div class='alert alert-danger mt-3'><i class='fa-solid fa-triangle-exclamation me-2'></i>Error cancelling order.</div>";
        }
    } else {
        // Find out current status
        $curr_q = mysqli_query($conn, "SELECT status FROM orders WHERE id = $cancel_id AND user_id = $user_id");
        if ($curr_q && mysqli_num_rows($curr_q) > 0) {
            $curr_stat = mysqli_fetch_assoc($curr_q)['status'];
            if ($curr_stat != 'Cancelled') {
                $_SESSION['order_msg'] = "<div class='alert alert-danger mt-3'><i class='fa-solid fa-triangle-exclamation me-2'></i>Order #$cancel_id cannot be cancelled as it is currently '$curr_stat'.</div>";
            }
            // If it's already cancelled, we do nothing (silent redirect) to prevent annoyance on form resubmission
        } else {
            $_SESSION['order_msg'] = "<div class='alert alert-danger mt-3'><i class='fa-solid fa-triangle-exclamation me-2'></i>Order not found.</div>";
        }
    }
    header("Location: my_orders.php");
    exit;
}

include("includes/head.php");
include("includes/header.php");
?>

<div class="page-header">
    <div class="container">
        <h1>My Orders</h1>
        <p>View and track your plant orders.</p>
    </div>
</div>

<main style="padding: 60px 0 80px; min-height: 50vh;">
    <div class="container">
        <?php echo $msg; ?>
        <?php
        if ($conn) {
            $query = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY order_date DESC";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                $orders = [];
                $has_pending = false;
                while ($row = mysqli_fetch_assoc($result)) {
                    $orders[] = $row;
                    if ($row['status'] == 'Pending') {
                        $has_pending = true;
                    }
                }
                ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" style="background: #fff; border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-sm);">
                        <thead style="background: var(--bg-elevated);">
                            <tr>
                                <th class="py-3 px-4" style="font-family: var(--font-heading); color: var(--text-primary);">Order ID</th>
                                <th class="py-3 px-4" style="font-family: var(--font-heading); color: var(--text-primary);">Date</th>
                                <th class="py-3 px-4" style="font-family: var(--font-heading); color: var(--text-primary);">Total Amount</th>
                                <th class="py-3 px-4" style="font-family: var(--font-heading); color: var(--text-primary);">Status</th>
                                <th class="py-3 px-4" style="font-family: var(--font-heading); color: var(--text-primary);">Items</th>
                                <?php if($has_pending): ?>
                                <th class="py-3 px-4 text-center" style="font-family: var(--font-heading); color: var(--text-primary);">Action</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td class="px-4 text-dark font-weight-bold">#<?php echo $order['id']; ?></td>
                                    <td class="px-4 text-secondary"><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></td>
                                    <td class="px-4 text-dark"><strong>Rs <?php echo number_format($order['total_amount']); ?></strong></td>
                                    <td class="px-4">
                                        <?php 
                                            $status = $order['status'];
                                            $badge_class = 'bg-secondary';
                                            if($status == 'Pending') $badge_class = 'bg-warning text-dark';
                                            elseif($status == 'Quality Check') $badge_class = 'bg-info text-dark';
                                            elseif($status == 'In Transit') $badge_class = 'bg-primary';
                                            elseif($status == 'Delivered') $badge_class = 'bg-success';
                                            elseif(strpos($status, 'Cancelled') !== false) $badge_class = 'bg-danger';
                                        ?>
                                        <span class="badge <?php echo $badge_class; ?>" style="padding: 6px 10px; font-weight: 500; font-family: var(--font-body);">
                                            <?php echo htmlspecialchars($status); ?>
                                        </span>
                                    </td>
                                    <td class="px-4">
                                        <?php
                                        // Fetch items for this order
                                        $order_id = $order['id'];
                                        $items_query = "SELECT oi.*, p.name FROM order_items oi JOIN plants p ON oi.plant_id = p.id WHERE oi.order_id = '$order_id'";
                                        $items_result = mysqli_query($conn, $items_query);
                                        if ($items_result && mysqli_num_rows($items_result) > 0) {
                                            echo "<ul class='mb-0' style='padding-left: 15px; font-size: 0.88rem; color: var(--text-secondary);'>";
                                            while($item = mysqli_fetch_assoc($items_result)) {
                                                echo "<li>" . $item['name'] . " (x" . $item['quantity'] . ")</li>";
                                            }
                                            echo "</ul>";
                                        } else {
                                            echo "<span class='text-muted small'>Details not found</span>";
                                        }
                                        ?>
                                    </td>
                                    <?php if($has_pending): ?>
                                    <td class="px-4 text-center">
                                        <?php if($status == 'Pending'): ?>
                                            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                                <input type="hidden" name="cancel_order_id" value="<?php echo $order_id; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;">
                                                    <i class="fa-solid fa-xmark me-1"></i>Cancel
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted" style="font-size: 0.85rem;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php
            } else {
                // No orders
                ?>
                <div class="text-center py-5">
                    <i class="fa-solid fa-box-open mb-3" style="font-size: 3rem; color: var(--border-hover);"></i>
                    <h3 style="font-family: var(--font-heading);">No Orders Yet!</h3>
                    <p class="text-secondary mb-4">Looks like you haven't bought any plants from us yet.</p>
                    <a href="shop.php" class="btn btn-flora px-4 py-2">Go to Shop</a>
                </div>
                <?php
            }
        } else {
            echo "<div class='alert alert-danger'>Database connection error.</div>";
        }
        ?>
    </div>
</main>

<?php include("includes/footer.php"); ?>
