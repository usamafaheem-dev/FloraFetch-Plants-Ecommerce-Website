<?php
include("../includes/db_connect.php");
include("includes/header.php");

$message = "";

// Handle Status Update
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    
    if ($conn) {
        // Get user_id for notification
        $user_id = 0;
        $u_q = mysqli_query($conn, "SELECT user_id FROM orders WHERE id = $order_id");
        if ($u_q && mysqli_num_rows($u_q) > 0) {
            $u_row = mysqli_fetch_assoc($u_q);
            $user_id = $u_row['user_id'];
        }

        $update_query = "UPDATE orders SET status = '$new_status' WHERE id = $order_id";
        if (mysqli_query($conn, $update_query)) {
            $message = "<div class='alert alert-success border-0 bg-success text-white'><i class='fa-solid fa-circle-check me-2'></i>Order #{$order_id} status updated to {$new_status}!</div>";
            
            // Insert Notification
            if ($user_id > 0 && in_array($new_status, ['Quality Check', 'In Transit', 'Delivered'])) {
                $notif_title = "Order " . $new_status;
                $notif_msg = "Your order #{$order_id} has been marked as {$new_status}.";
                $n_q = "INSERT INTO notifications (user_id, title, message) VALUES ($user_id, '$notif_title', '$notif_msg')";
                mysqli_query($conn, $n_q);
            }
        } else {
            $message = "<div class='alert alert-danger border-0 bg-danger text-white'><i class='fa-solid fa-triangle-exclamation me-2'></i>Error updating status.</div>";
        }
    }
}

// Handle Order Deletion
if (isset($_POST['delete_order'])) {
    $order_id = (int)$_POST['order_id'];
    
    if ($conn) {
        // Delete items first
        mysqli_query($conn, "DELETE FROM order_items WHERE order_id = $order_id");
        // Delete order
        if (mysqli_query($conn, "DELETE FROM orders WHERE id = $order_id")) {
            $message = "<div class='alert alert-success border-0 bg-success text-white'><i class='fa-solid fa-trash me-2'></i>Order #{$order_id} has been deleted.</div>";
        } else {
            $message = "<div class='alert alert-danger border-0 bg-danger text-white'><i class='fa-solid fa-triangle-exclamation me-2'></i>Error deleting order.</div>";
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="admin-page-title mb-0">Manage Orders</h2>
</div>

<?php echo $message; ?>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Details</th>
                    <th>Order Items</th>
                    <th>Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($conn) {
                    $query = "SELECT o.*, u.name as customer_name, u.email as customer_email 
                              FROM orders o 
                              JOIN users u ON o.user_id = u.id 
                              ORDER BY o.order_date DESC";
                    $result = mysqli_query($conn, $query);
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Fetch items for this order
                            $order_id = $row['id'];
                            $items_html = "<ul class='mb-0' style='padding-left: 15px; font-size: 0.85rem;'>";
                            $items_query = "SELECT oi.*, p.name FROM order_items oi JOIN plants p ON oi.plant_id = p.id WHERE oi.order_id = $order_id";
                            $items_result = mysqli_query($conn, $items_query);
                            if ($items_result && mysqli_num_rows($items_result) > 0) {
                                while($item = mysqli_fetch_assoc($items_result)) {
                                    $items_html .= "<li>{$item['name']} (x{$item['quantity']})</li>";
                                }
                            } else {
                                $items_html .= "<li>No items found</li>";
                            }
                            $items_html .= "</ul>";

                            // Status badge
                            $status = $row['status'];
                            $badge_class = 'bg-secondary';
                            if($status == 'Pending') $badge_class = 'bg-warning text-dark';
                            elseif($status == 'Quality Check') $badge_class = 'bg-info text-dark';
                            elseif($status == 'In Transit') $badge_class = 'bg-primary';
                            elseif($status == 'Delivered') $badge_class = 'bg-success';
                            elseif($status == 'Cancelled') $badge_class = 'bg-danger';

                            echo "<tr>";
                            echo "<td style='font-weight: 600;'>#{$row['id']}</td>";
                             $del_date = !empty($row['delivery_date']) ? date('d M Y', strtotime($row['delivery_date'])) : 'Not Selected';
                             echo "<td>
                                     <div style='font-weight: 600; color: var(--text-primary);'>{$row['customer_name']}</div>
                                     <div style='font-size: 0.85rem; color: #64748b;'>{$row['customer_email']}</div>
                                     <div style='font-size: 0.82rem; color: #166534; font-weight: 500; margin-top: 4px;'><i class='fa-solid fa-truck me-1'></i>Deliv Date: {$del_date}</div>
                                     <div style='font-size: 0.8rem; color: #475569; margin-top: 2px; max-width: 250px; white-space: normal;'><i class='fa-solid fa-map-pin me-1'></i>{$row['delivery_address']}</div>
                                   </td>";
                             echo "<td>{$items_html}</td>";
                            echo "<td>" . date('d M Y, h:i A', strtotime($row['order_date'])) . "</td>";
                            echo "<td style='font-weight: 600;'>Rs " . number_format($row['total_amount']) . "</td>";
                            echo "<td><span class='badge {$badge_class} px-2 py-1'>{$status}</span></td>";
                            echo "<td>
                                    <div class='d-flex gap-2 align-items-center'>";
                                    
                            if ($status == 'Cancelled') {
                                echo "<div class='text-danger fw-bold' style='width: 185px;'><i class='fa-solid fa-ban me-1'></i>Cancelled</div>";
                            } else {
                                echo "<form method='POST' action='' class='d-flex gap-2 align-items-center mb-0'>
                                        <input type='hidden' name='order_id' value='{$row['id']}'>
                                        <select name='status' class='form-select form-select-sm' style='width: 120px; border-color: var(--border);'>
                                            <option value='Pending' ".($status=='Pending'?'selected':'').">Pending</option>
                                            <option value='Quality Check' ".($status=='Quality Check'?'selected':'').">Quality Check</option>
                                            <option value='In Transit' ".($status=='In Transit'?'selected':'').">In Transit</option>
                                            <option value='Delivered' ".($status=='Delivered'?'selected':'').">Delivered</option>
                                            <option value='Cancelled' ".($status=='Cancelled'?'selected':'').">Cancelled</option>
                                        </select>
                                        <button type='submit' name='update_status' class='btn btn-sm btn-light border'>Update</button>
                                      </form>";
                            }
                                echo "<form method='POST' action='' class='mb-0' onsubmit='return confirm(\"Are you sure you want to permanently delete this order?\");'>
                                        <input type='hidden' name='order_id' value='{$row['id']}'>
                                        <button type='submit' name='delete_order' class='btn btn-sm btn-outline-danger' title='Delete Order'>
                                            <i class='fa-solid fa-trash'></i>
                                        </button>
                                      </form>
                                    </div>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center py-4 text-secondary'>No orders found.</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center py-4 text-warning'>Database not connected.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/footer.php"); ?>
