<?php
include("includes/head.php");
include("includes/header.php");
include("includes/db_connect.php");

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total_price = 0;
?>

<!-- Page Header -->
<div class="page-header" style="min-height: 25vh;">
    <img src="assets/images/plants/hero_monstera.png" alt="Decoration" class="hero-plant-left">
    <img src="assets/images/plants/hero_pothos.png" alt="Decoration" class="hero-plant-right">
    
    <!-- Animated 3D Butterfly -->
    <div class="butterfly-container" style="top: 25%; right: 15%;">
        <div class="butterfly">
            <div class="wing left"></div>
            <div class="wing right"></div>
            <div class="butterfly-body"></div>
        </div>
    </div>
    
    <!-- Crawling Ladybug -->
    <div class="ladybug" style="top: 75%; left: 10%; z-index: 5;"></div>
    
    <div class="container">
        <h1>Your Cart</h1>
        <p>Review the items in your cart before checkout.</p>
    </div>
</div>

<main style="padding-top: 40px; padding-bottom: 80px; min-height: 50vh;">
    <div class="container">
        
        <?php if (empty($cart_items)): ?>
            <div class="text-center py-5">
                <div class="mb-4" style="font-size: 4rem; color: var(--text-secondary); opacity: 0.5;">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <h3 class="text-white">Your cart is empty</h3>
                <p class="text-muted">Looks like you haven't added any plants yet.</p>
                <a href="shop.php" class="btn btn-flora mt-3">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="admin-card" style="padding: 0;">
                        <div class="table-responsive">
                            <table class="table table-borderless table-hover align-middle mb-0" style="color: var(--white);">
                                <thead style="background: rgba(255,255,255,0.05);">
                                    <tr>
                                        <th class="py-3 px-4">Product</th>
                                        <th class="py-3">Price</th>
                                        <th class="py-3">Quantity</th>
                                        <th class="py-3">Total</th>
                                        <th class="py-3 text-end px-4"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($cart_items as $id => $quantity) {
                                        $plant_name = "Unknown Plant";
                                        $plant_price = 0;
                                        $plant_img = "assets/images/plants/default.jpg";
                                        
                                        // Fetch from DB or fallback
                                        if ($conn) {
                                            $q = mysqli_query($conn, "SELECT * FROM plants WHERE id = $id");
                                            if ($q && mysqli_num_rows($q) > 0) {
                                                $row = mysqli_fetch_assoc($q);
                                                $plant_name = $row['name'];
                                                $plant_price = $row['price'];
                                                $plant_img = !empty($row['image']) ? $row['image'] : $plant_img;
                                            }
                                        } else {
                                            // Fallback logic for demo
                                            $demo_prices = [1=>2500, 2=>1200, 3=>3500, 4=>800, 5=>1500, 6=>900, 7=>2100, 8=>1100];
                                            $demo_names = [1=>'Monstera Deliciosa', 2=>'Snake Plant', 3=>'Fiddle Leaf Fig', 4=>'Aloe Vera', 5=>'Peace Lily', 6=>'Spider Plant', 7=>'Rubber Plant', 8=>'Jade Plant'];
                                            if (isset($demo_prices[$id])) {
                                                $plant_price = $demo_prices[$id];
                                                $plant_name = $demo_names[$id];
                                                $plant_img = 'assets/images/plants/p'.(($id%4)+1).'.jpg';
                                            }
                                        }
                                        
                                        $item_total = $plant_price * $quantity;
                                        $total_price += $item_total;
                                        ?>
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="<?php echo $plant_img; ?>" alt="plant" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                                    <h6 class="mb-0" style="font-weight: 600;"><?php echo $plant_name; ?></h6>
                                                </div>
                                            </td>
                                            <td class="py-3">Rs <?php echo number_format($plant_price); ?></td>
                                            <td class="py-3">
                                                <form method="POST" action="cart_action.php" class="d-flex align-items-center gap-2">
                                                    <input type="hidden" name="action" value="update">
                                                    <input type="hidden" name="plant_id" value="<?php echo $id; ?>">
                                                    <input type="number" name="quantity" value="<?php echo $quantity; ?>" min="1" class="form-control admin-form-control form-control-sm text-center" style="width: 60px;">
                                                    <button type="submit" class="btn btn-sm btn-outline-light border-0"><i class="fa-solid fa-rotate"></i></button>
                                                </form>
                                            </td>
                                            <td class="py-3" style="font-weight: 600;">Rs <?php echo number_format($item_total); ?></td>
                                            <td class="py-3 text-end px-4">
                                                <form method="POST" action="cart_action.php">
                                                    <input type="hidden" name="action" value="remove">
                                                    <input type="hidden" name="plant_id" value="<?php echo $id; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="admin-card">
                        <h4 class="mb-4 text-white" style="font-family: var(--font-heading);">Order Summary</h4>
                        
                        <div class="d-flex justify-content-between mb-3 text-secondary">
                            <span>Subtotal</span>
                            <span>Rs <?php echo number_format($total_price); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-secondary">
                            <span>Shipping</span>
                            <span>Calculated at checkout</span>
                        </div>
                        
                        <hr style="border-color: var(--border-subtle);">
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-white" style="font-size: 1.1rem; font-weight: 600;">Total</span>
                            <span class="text-white" style="font-size: 1.2rem; font-weight: 700; color: var(--primary-light) !important;">Rs <?php echo number_format($total_price); ?></span>
                        </div>
                        <?php if(isset($is_admin) && $is_admin): ?>
                            <button type="button" class="btn btn-secondary w-100" onclick="showAdminToast()">View Only Mode</button>
                        <?php else: ?>
                            <a href="checkout.php" class="btn btn-flora w-100">Proceed to Checkout</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include("includes/footer.php"); ?>
