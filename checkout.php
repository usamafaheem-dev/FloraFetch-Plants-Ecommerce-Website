<?php
// Start session for checkout processing
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("includes/db_connect.php");

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// If cart is empty, redirect to shop
if (empty($cart_items) && $_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: shop.php");
    exit;
}

// Ensure user is logged in before checkout
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// Fetch saved addresses
$saved_addresses = [];
if ($conn) {
    $addr_q = mysqli_query($conn, "SELECT * FROM user_addresses WHERE user_id = $user_id ORDER BY created_at DESC");
    if ($addr_q) {
        while($r = mysqli_fetch_assoc($addr_q)) {
            $saved_addresses[] = $r;
        }
    }
}

$total_price = 0;

// Calculate total before handling insertion
if ($conn) {
    foreach ($cart_items as $id => $quantity) {
        $q = mysqli_query($conn, "SELECT price FROM plants WHERE id = $id");
        if ($q && mysqli_num_rows($q) > 0) {
            $row = mysqli_fetch_assoc($q);
            $total_price += $row['price'] * $quantity;
        }
    }
}

// Handle form submission BEFORE any HTML output
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    
    if (empty($cart_items)) {
        header("Location: shop.php");
        exit;
    }

    if ($conn) {
        // Handle saved addresses
        $address_id = isset($_POST['saved_address_id']) ? (int)$_POST['saved_address_id'] : 0;
        
        $fname = isset($_POST['first_name']) ? mysqli_real_escape_string($conn, trim($_POST['first_name'])) : '';
        $lname = isset($_POST['last_name']) ? mysqli_real_escape_string($conn, trim($_POST['last_name'])) : '';
        $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, trim($_POST['email'])) : '';
        $phone = isset($_POST['phone']) ? mysqli_real_escape_string($conn, trim($_POST['phone'])) : '';
        $addr = isset($_POST['address']) ? mysqli_real_escape_string($conn, trim($_POST['address'])) : '';
        $city = isset($_POST['city']) ? mysqli_real_escape_string($conn, trim($_POST['city'])) : '';
        $zip = isset($_POST['zip']) ? mysqli_real_escape_string($conn, trim($_POST['zip'])) : '';

        if ($address_id > 0) {
            // Update existing
            mysqli_query($conn, "UPDATE user_addresses SET first_name='$fname', last_name='$lname', email='$email', phone='$phone', address='$addr', city='$city', zip='$zip' WHERE id=$address_id AND user_id=$user_id");
        } else {
            // Insert new only if checkbox is checked
            if (isset($_POST['save_to_address_book'])) {
                mysqli_query($conn, "INSERT INTO user_addresses (user_id, first_name, last_name, email, phone, address, city, zip) VALUES ($user_id, '$fname', '$lname', '$email', '$phone', '$addr', '$city', '$zip')");
            }
        }

        // Insert into orders table
        $instructions = isset($_POST['delivery_instructions']) ? mysqli_real_escape_string($conn, trim($_POST['delivery_instructions'])) : '';
        $address_str = $addr . ', ' . $city . ', ' . $zip;
        if (!empty($instructions)) {
            $address_str .= ' [Instructions: ' . $instructions . ']';
        }
        $total = $total_price;
        
        $delivery_date = isset($_POST['delivery_date']) ? mysqli_real_escape_string($conn, $_POST['delivery_date']) : '';
        $delivery_date_val = !empty($delivery_date) ? "'$delivery_date'" : "NULL";
        
        $order_query = "INSERT INTO orders (user_id, total_amount, delivery_address, delivery_date, status) VALUES ($user_id, $total, '$address_str', $delivery_date_val, 'Pending')";
        
        if (mysqli_query($conn, $order_query)) {
            $order_id = mysqli_insert_id($conn);
            
            // Insert into order_items
            foreach ($cart_items as $id => $quantity) {
                // Fetch current price
                $q = mysqli_query($conn, "SELECT price FROM plants WHERE id = $id");
                if ($q && mysqli_num_rows($q) > 0) {
                    $row = mysqli_fetch_assoc($q);
                    $price = $row['price'];
                    
                    $item_query = "INSERT INTO order_items (order_id, plant_id, quantity, price) VALUES ($order_id, $id, $quantity, $price)";
                    mysqli_query($conn, $item_query);
                    
                    // Deduct stock
                    $update_stock = "UPDATE plants SET stock_quantity = GREATEST(0, stock_quantity - $quantity) WHERE id = $id";
                    mysqli_query($conn, $update_stock);
                }
            }
        }
    }
    
    unset($_SESSION['cart']);
    
    // Redirect to Order Success page
    header("Location: order_success.php?id=" . $order_id);
    exit;
}

include("includes/head.php");
include("includes/header.php");
?>

<main style="padding-top: 120px; padding-bottom: 80px; min-height: 100vh;">
    <div class="container">
        <h1 class="section-title mb-5 text-dark">Checkout</h1>
        
        <form method="POST" action="">
            <div class="row g-5">
                <!-- Left: Billing Details -->
                <div class="col-lg-7">
                    <div class="admin-card" style="background: #fff; border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                        <h4 class="mb-4 text-dark" style="font-family: var(--font-heading);">Billing Details</h4>
                        
                        <?php if (!empty($saved_addresses)): ?>
                        <div class="mb-4">
                            <label class="text-secondary mb-1">Select a Saved Address</label>
                            <select id="savedAddressSelect" name="saved_address_id" class="form-select" style="border: 1px solid var(--border);">
                                <option value="">-- Use a New Address --</option>
                                <?php foreach($saved_addresses as $sa): ?>
                                    <option value="<?php echo $sa['id']; ?>"
                                        data-fname="<?php echo htmlspecialchars($sa['first_name']); ?>"
                                        data-lname="<?php echo htmlspecialchars($sa['last_name']); ?>"
                                        data-email="<?php echo htmlspecialchars($sa['email']); ?>"
                                        data-phone="<?php echo htmlspecialchars($sa['phone']); ?>"
                                        data-address="<?php echo htmlspecialchars($sa['address']); ?>"
                                        data-city="<?php echo htmlspecialchars($sa['city']); ?>"
                                        data-zip="<?php echo htmlspecialchars($sa['zip']); ?>"
                                    >
                                        <?php echo htmlspecialchars($sa['first_name'] . ' ' . $sa['last_name'] . ' - ' . $sa['address'] . ', ' . $sa['city']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-secondary mb-1">First Name</label>
                                <input type="text" name="first_name" class="form-control" style="border: 1px solid var(--border);" required>
                            </div>
                            <div class="col-md-6">
                                <label class="text-secondary mb-1">Last Name</label>
                                <input type="text" name="last_name" class="form-control" style="border: 1px solid var(--border);" required>
                            </div>
                            <div class="col-md-12">
                                <label class="text-secondary mb-1">Email Address</label>
                                <input type="email" name="email" class="form-control" style="border: 1px solid var(--border);" required>
                            </div>
                            <div class="col-md-12">
                                <label class="text-secondary mb-1">Phone Number</label>
                                <input type="text" name="phone" class="form-control" style="border: 1px solid var(--border);" required>
                            </div>
                            <div class="col-md-12">
                                <label class="text-secondary mb-1">Shipping Address</label>
                                <input type="text" name="address" class="form-control" style="border: 1px solid var(--border);" placeholder="House number and street name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="text-secondary mb-1">City</label>
                                <input type="text" name="city" class="form-control" style="border: 1px solid var(--border);" required>
                            </div>
                            <div class="col-md-6">
                                <label class="text-secondary mb-1">Postal Code</label>
                                <input type="text" name="zip" class="form-control" style="border: 1px solid var(--border);" required>
                            </div>
                            <div class="col-md-12 mt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="save_to_address_book" id="save_to_address_book" value="1" checked>
                                    <label class="form-check-label text-dark fw-semibold" for="save_to_address_book">
                                        Save this address to my profile address book for future orders
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <h4 class="mb-4 mt-5 text-dark" style="font-family: var(--font-heading);">Delivery Preferences</h4>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="text-secondary mb-1">Preferred Delivery Date</label>
                                <input type="date" name="delivery_date" class="form-control" style="border: 1px solid var(--border);" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                                <small class="text-muted" style="font-size: 0.8rem;">Plants need careful handling. Choose at least 24 hours from today.</small>
                            </div>
                            <div class="col-md-12">
                                <label class="text-secondary mb-1">Handling / Delivery Instructions</label>
                                <textarea name="delivery_instructions" class="form-control" rows="2" style="border: 1px solid var(--border);" placeholder="e.g. Leave with guard, keep pots upright, handle with care..."></textarea>
                            </div>
                        </div>

                        <h4 class="mb-4 mt-5 text-dark" style="font-family: var(--font-heading);">Payment Method</h4>
                        <div class="d-flex flex-column gap-3">
                            <div class="form-check" style="background: #f8fafc; padding: 15px 15px 15px 40px; border-radius: 8px; border: 1px solid var(--primary);">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                <label class="form-check-label text-dark fw-bold ms-2" for="cod">
                                    Cash on Delivery (COD)
                                </label>
                            </div>
                            <div class="form-check" style="background: #f8fafc; padding: 15px 15px 15px 40px; border-radius: 8px; border: 1px solid var(--border-subtle); opacity: 0.6;">
                                <input class="form-check-input" type="radio" name="payment_method" id="card" value="card" disabled>
                                <label class="form-check-label text-secondary ms-2" for="card">
                                    Credit/Debit Card (Coming Soon)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right: Order Summary -->
                <div class="col-lg-5">
                    <div class="admin-card position-sticky" style="top: 100px; background: #fff; border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                        <h4 class="mb-4 text-dark" style="font-family: var(--font-heading);">Your Order</h4>
                        
                        <div class="d-flex flex-column gap-3 mb-4">
                            <?php
                            $display_total = 0;
                            foreach ($cart_items as $id => $quantity) {
                                $plant_name = "Unknown Plant";
                                $plant_price = 0;
                                
                                // Fetch from DB or fallback
                                if ($conn) {
                                    $q = mysqli_query($conn, "SELECT * FROM plants WHERE id = $id");
                                    if ($q && mysqli_num_rows($q) > 0) {
                                        $row = mysqli_fetch_assoc($q);
                                        $plant_name = $row['name'];
                                        $plant_price = $row['price'];
                                    }
                                }
                                
                                $item_total = $plant_price * $quantity;
                                $display_total += $item_total;
                                ?>
                                <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                                    <span class="text-secondary"><?php echo $plant_name; ?> <strong class="text-dark">x<?php echo $quantity; ?></strong></span>
                                    <span class="text-dark fw-bold">Rs <?php echo number_format($item_total); ?></span>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3 text-secondary">
                            <span>Subtotal</span>
                            <span>Rs <?php echo number_format($display_total); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-secondary">
                            <span>Shipping</span>
                            <span class="text-success fw-bold">Free</span>
                        </div>
                        
                        <hr style="border-color: var(--border-subtle);">
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-dark" style="font-size: 1.1rem; font-weight: 600;">Total</span>
                            <span class="text-primary" style="font-size: 1.3rem; font-weight: 700;">Rs <?php echo number_format($display_total); ?></span>
                        </div>
                        
                        <?php if(isset($is_admin) && $is_admin): ?>
                            <button type="button" class="btn btn-secondary w-100 py-3 text-white" style="font-size: 1.1rem;" onclick="showAdminToast()">
                                <i class="fa-solid fa-eye me-2"></i> View Only Mode
                            </button>
                        <?php else: ?>
                            <button type="submit" name="place_order" id="placeOrderBtn" class="btn btn-flora w-100 py-3 text-white" style="font-size: 1.1rem;" onclick="this.innerHTML='<i class=\'fa-solid fa-spinner fa-spin me-2\'></i> Processing...'; setTimeout(() => { this.disabled=true; }, 50);">
                                <i class="fa-solid fa-lock me-2"></i> Place Order
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<?php include("includes/footer.php"); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('savedAddressSelect');
    if(select) {
        select.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if(this.value !== "") {
                document.querySelector('input[name="first_name"]').value = opt.getAttribute('data-fname');
                document.querySelector('input[name="last_name"]').value = opt.getAttribute('data-lname');
                document.querySelector('input[name="email"]').value = opt.getAttribute('data-email');
                document.querySelector('input[name="phone"]').value = opt.getAttribute('data-phone');
                document.querySelector('input[name="address"]').value = opt.getAttribute('data-address');
                document.querySelector('input[name="city"]').value = opt.getAttribute('data-city');
                document.querySelector('input[name="zip"]').value = opt.getAttribute('data-zip');
            } else {
                document.querySelector('input[name="first_name"]').value = '';
                document.querySelector('input[name="last_name"]').value = '';
                document.querySelector('input[name="email"]').value = '';
                document.querySelector('input[name="phone"]').value = '';
                document.querySelector('input[name="address"]').value = '';
                document.querySelector('input[name="city"]').value = '';
                document.querySelector('input[name="zip"]').value = '';
            }
        });
    }
});
</script>
