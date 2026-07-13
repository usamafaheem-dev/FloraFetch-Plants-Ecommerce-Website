<?php
include("includes/head.php");
include("includes/header.php");

// Get Order ID from URL or generate placeholder
$order_id = isset($_GET['id']) ? "#" . (int)$_GET['id'] : "FLORA-" . strtoupper(substr(md5(uniqid()), 0, 8));
?>

<main style="padding-top: 150px; padding-bottom: 100px; min-height: 100vh; display: flex; align-items: center;">
    <div class="container text-center">
        <div style="width: 100px; height: 100px; background: rgba(46, 204, 113, 0.1); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin: 0 auto 30px;">
            <i class="fa-solid fa-check"></i>
        </div>
        
        <h1 class="section-title mb-3">Order Confirmed!</h1>
        <p class="text-secondary mb-4" style="font-size: 1.1rem; max-width: 500px; margin: 0 auto;">
            Thank you for shopping with FloraFetch. Your order has been placed successfully and is now being processed.
        </p>
        
        <div class="admin-card d-inline-block text-start mb-5" style="min-width: 350px; background: #fff; border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">Order Number:</span>
                <strong class="text-dark"><?php echo $order_id; ?></strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">Date:</span>
                <strong class="text-dark"><?php echo date('d M, Y'); ?></strong>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-secondary">Payment Method:</span>
                <strong class="text-dark">Cash on Delivery</strong>
            </div>
        </div>
        
        <div>
            <a href="shop.php" class="btn btn-flora px-4 me-3 text-white">Continue Shopping</a>
            <a href="index.php" class="btn btn-outline-dark px-4">Back to Home</a>
        </div>
    </div>
</main>

<?php include("includes/footer.php"); ?>
