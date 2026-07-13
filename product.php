<?php
include("includes/head.php");
include("includes/header.php");
include("includes/db_connect.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$plant_name = "Monstera Deliciosa";
$plant_price = 2500;
$plant_cat = "Indoor";
$plant_img = "assets/images/plants/p1.jpg";
$plant_desc = "The Monstera Deliciosa is a striking tropical plant known for its large, glossy, fenestrated leaves. It is relatively easy to care for and can grow quite large indoors, making it a perfect statement piece for your living room.";
$plant_care = "Bright indirect light. Water when the top 2 inches of soil are dry.";

// DB Fetch if available
if ($conn) {
    $q = mysqli_query($conn, "SELECT * FROM plants WHERE id = $id");
    if ($q && mysqli_num_rows($q) > 0) {
        $row = mysqli_fetch_assoc($q);
        $plant_name = $row['name'];
        $plant_price = $row['price'];
        $plant_cat = $row['category'];
        $plant_desc = $row['care_guide'];
        $plant_care = "Sunlight: " . ($row['sunlight'] ? $row['sunlight'] : "Varies") . " | Watering: " . ($row['watering'] ? $row['watering'] : "Varies");
        $plant_img = !empty($row['image']) ? $row['image'] : $plant_img;
        $plant_stock = (int)$row['stock_quantity'];
    }
}
// Handle Review Submission
$review_msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $rating = (int)$_POST['rating'];
        $comment = mysqli_real_escape_string($conn, $_POST['comment']);
        
        $review_image = "";
        if (isset($_FILES['review_image']) && $_FILES['review_image']['error'] == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['review_image']['tmp_name'];
            $name_file = time() . '_' . basename($_FILES['review_image']['name']);
            $upload_dir = 'assets/images/reviews/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            if (move_uploaded_file($tmp_name, $upload_dir . $name_file)) {
                $review_image = 'assets/images/reviews/' . $name_file;
            }
        }
        $img_val = !empty($review_image) ? "'$review_image'" : "NULL";
        
        $insert_query = "INSERT INTO reviews (plant_id, user_id, rating, comment, review_image, is_approved) VALUES ($id, $user_id, $rating, '$comment', $img_val, 0)";
        if (mysqli_query($conn, $insert_query)) {
            $review_msg = "<div class='alert alert-success mt-3'><i class='fa-solid fa-circle-check me-2'></i>Your review has been submitted and is awaiting approval.</div>";
        } else {
            $review_msg = "<div class='alert alert-danger mt-3'><i class='fa-solid fa-triangle-exclamation me-2'></i>Error submitting review.</div>";
        }
    } else {
        $review_msg = "<div class='alert alert-warning mt-3'><i class='fa-solid fa-lock me-2'></i>You must be <a href='login.php' class='alert-link'>logged in</a> to write a review.</div>";
    }
}
?>

<main style="padding-top: 120px; padding-bottom: 80px; min-height: 100vh;">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-secondary" style="text-decoration:none;">Home</a></li>
                <li class="breadcrumb-item"><a href="shop.php" class="text-secondary" style="text-decoration:none;">Shop</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page"><?php echo $plant_name; ?></li>
            </ol>
        </nav>

        <div class="row g-5 position-relative">
            <div class="col-lg-5">
                <div class="position-relative overflow-hidden" style="background: #f8fafc; padding: 20px; border-radius: 24px; border: 1px solid var(--border);" onmousemove="zoomImage(event, this)" onmouseleave="resetZoom(this)">
                    <img src="<?php echo $plant_img; ?>" class="w-100 rounded-3 product-zoom-img" style="object-fit: cover; cursor: zoom-in; transition: transform 0.1s ease; aspect-ratio: 4/5; transform-origin: center center;" alt="<?php echo $plant_name; ?>">
                    <span class="badge" style="position: absolute; top: 30px; left: 30px; background: rgba(255,255,255,0.9); color: var(--primary); padding: 8px 16px; font-size: 0.85rem; font-weight: 700; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); backdrop-filter: blur(10px); z-index: 5; pointer-events: none;"><?php echo $plant_cat; ?></span>
                </div>
            </div>
            
            <div class="col-lg-7">
                
                <h1 style="font-family: var(--font-heading); font-size: 2.75rem; font-weight: 800; color: var(--text-primary); margin-bottom: 10px; letter-spacing: -0.5px;"><?php echo $plant_name; ?></h1>
                
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="color: #f59e0b; font-size: 1rem;">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star-half-stroke"></i>
                    </div>
                    <span class="text-secondary" style="font-size: 0.95rem;">(24 Premium Reviews)</span>
                </div>
                
                <h2 style="font-size: 2rem; color: var(--primary); font-weight: 800; margin-bottom: 10px;">
                    Rs <?php echo number_format($plant_price); ?>
                </h2>
                <div class="mb-4">
                    <?php if (isset($plant_stock)): ?>
                        <?php if ($plant_stock > 0): ?>
                            <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill" style="font-size: 0.9rem;"><i class="fa-solid fa-check-circle me-1"></i> <?php echo $plant_stock; ?> in stock</span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2 rounded-pill" style="font-size: 0.9rem;"><i class="fa-solid fa-xmark-circle me-1"></i> Out of stock</span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                
                <p style="color: var(--text-secondary); font-size: 1.05rem; line-height: 1.8; margin-bottom: 35px; max-width: 90%;">
                    <?php echo $plant_desc; ?>
                </p>
                
                <div class="care-card mb-4" style="background: linear-gradient(145deg, #ffffff, #f8fafc); border: 1px solid var(--border); border-radius: 16px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.02);">
                    <h5 class="text-dark mb-2" style="font-weight: 700; font-size: 1.1rem;"><i class="fa-solid fa-leaf me-2" style="color: #10b981;"></i> Expert Care Guide</h5>
                    <p class="text-secondary mb-0" style="line-height: 1.6; font-size: 0.95rem;"><?php echo $plant_care; ?></p>
                </div>
                
                <?php if(isset($is_admin) && $is_admin): ?>
                <div class="d-flex gap-3 align-items-center mt-4">
                    <button type="button" class="btn py-3 px-5 flex-grow-1" style="background: var(--text-secondary); color: white; font-size: 1.1rem; border-radius: 12px; font-weight: 600;" onclick="showAdminToast()">
                        <i class="fa-solid fa-eye me-2"></i> View Only (Admin)
                    </button>
                </div>
                <?php else: ?>
                <form method="POST" action="cart_action.php" class="d-flex gap-3 align-items-center mt-4">
                    <input type="hidden" name="plant_id" value="<?php echo $id; ?>">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="quantity-selector" style="display: flex; align-items: center; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; background: #fff; height: 54px;">
                        <input type="number" name="quantity" class="form-control text-center border-0 h-100" value="1" min="1" <?php echo isset($plant_stock) && $plant_stock > 0 ? "max='$plant_stock'" : ""; ?> style="width: 80px; font-weight: 600; font-size: 1.1rem; box-shadow: none;">
                    </div>
                    
                    <button type="submit" class="btn btn-flora py-3 px-4 flex-grow-1" <?php echo (isset($plant_stock) && $plant_stock <= 0) ? 'disabled' : ''; ?> style="font-size: 1.1rem; border-radius: 12px; font-weight: 600; box-shadow: 0 8px 20px rgba(4, 47, 31, 0.15);">
                        <i class="fa-solid fa-cart-plus me-2"></i> Add to Cart
                    </button>
                    
                    <button type="button" class="btn" style="border: 1px solid var(--border); color: var(--text-secondary); background: #fff; height: 54px; width: 54px; border-radius: 12px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onclick="toggleWishlist(<?php echo $id; ?>, this)" onmouseover="this.style.borderColor='var(--primary)'; this.style.color='var(--primary)';" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--text-secondary)';">
                        <i class="<?php echo (isset($_SESSION['wishlist']) && in_array($id, $_SESSION['wishlist'])) ? 'fa-solid text-danger' : 'fa-regular'; ?> fa-heart" style="font-size: 1.2rem;"></i>
                    </button>
                </form>
                <?php endif; ?>
                
                <div class="mt-4 pt-4 border-top d-flex flex-wrap gap-4">
                    <div class="d-flex align-items-center text-muted" style="font-size: 0.9rem;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fa-solid fa-truck-fast text-primary"></i>
                        </div>
                        Free shipping over Rs 5k
                    </div>
                    <div class="d-flex align-items-center text-muted" style="font-size: 0.9rem;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fa-solid fa-shield-halved text-success"></i>
                        </div>
                        Safe arrival guarantee
                    </div>
                </div>
            </div>
        </div>
        
        <?php echo $review_msg; ?>
        
        <!-- Product Reviews Section -->
        <div class="mt-5 pt-5 border-top">
            <h3 class="mb-4" style="font-family: var(--font-heading); font-weight: 800; color: var(--text-primary); font-size: 2rem;">Customer Reviews</h3>
            
            <div class="row g-5">
                <div class="col-lg-7">
                    <?php
                    if ($conn) {
                        $rev_query = "SELECT r.*, u.name as user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.plant_id = $id AND r.is_approved = 1 ORDER BY r.created_at DESC";
                        $rev_result = mysqli_query($conn, $rev_query);
                        
                        if ($rev_result && mysqli_num_rows($rev_result) > 0) {
                            echo "<div class='reviews-container'>";
                             while ($rev = mysqli_fetch_assoc($rev_result)) {
                                $r_stars = str_repeat("<i class='fa-solid fa-star text-warning'></i>", $rev['rating']) . str_repeat("<i class='fa-regular fa-star text-warning'></i>", 5 - $rev['rating']);
                                $r_date = date("M d, Y", strtotime($rev['created_at']));
                                $img_html = "";
                                if (!empty($rev['review_image'])) {
                                    $img_html = "<div class='mt-2'><img src='" . htmlspecialchars($rev['review_image']) . "' alt='user review' style='max-width: 150px; border-radius: 8px; border: 1px solid var(--border);'></div>";
                                }
                                echo "
                                <div class='review-item mb-4 pb-4 border-bottom'>
                                    <div class='d-flex justify-content-between align-items-center mb-2'>
                                        <div class='fw-bold text-dark'>" . htmlspecialchars($rev['user_name']) . "</div>
                                        <div class='text-muted small'>$r_date</div>
                                    </div>
                                    <div class='mb-2'>$r_stars</div>
                                    <p class='text-secondary mb-0' style='line-height: 1.6;'>" . htmlspecialchars($rev['comment']) . "</p>
                                    $img_html
                                </div>";
                            }
                            echo "</div>";
                        } else {
                            echo "<div class='alert alert-light border text-center py-5'><i class='fa-regular fa-comment-dots fa-3x text-muted mb-3'></i><p class='text-secondary mb-0'>No reviews yet. Be the first to review this plant!</p></div>";
                        }
                    }
                    ?>
                </div>
                <div class="col-lg-5">
                    <div class="write-review-card p-4" style="background: #f8fafc; border-radius: 16px; border: 1px solid var(--border);">
                        <h4 class="mb-3 fw-bold">Write a Review</h4>
                        <?php
                        $can_review = false;
                        if (isset($_SESSION['user_id'])) {
                            $uid = $_SESSION['user_id'];
                            $check_purchase = mysqli_query($conn, "SELECT o.id FROM orders o JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = $uid AND o.status = 'Delivered' AND oi.plant_id = $id LIMIT 1");
                            if ($check_purchase && mysqli_num_rows($check_purchase) > 0) {
                                $can_review = true;
                            }
                        }
                        ?>
                        
                        <?php if ($can_review): ?>
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Rating</label>
                                <select name="rating" class="form-select" required>
                                    <option value="5">5 - Excellent</option>
                                    <option value="4">4 - Very Good</option>
                                    <option value="3">3 - Average</option>
                                    <option value="2">2 - Poor</option>
                                    <option value="1">1 - Terrible</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Your Review</label>
                                <textarea name="comment" class="form-control" rows="4" placeholder="Share your experience with this plant..." required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Upload Photo <small class="text-muted">(Optional)</small></label>
                                <input type="file" name="review_image" class="form-control" accept="image/*" style="border-radius: 8px;">
                            </div>
                            <button type="submit" name="submit_review" class="btn btn-flora w-100 py-2">Submit Review</button>
                        </form>
                        <?php else: ?>
                            <div class="alert alert-info mt-2">
                                <i class="fa-solid fa-circle-info me-2"></i> You can only write a review after you have purchased and received this plant.
                            </div>
                            <?php if(!isset($_SESSION['user_id'])): ?>
                                <a href="auth.php" class="btn btn-outline-primary w-100 mt-2">Login to Review</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // Fetch related plants
        $related_plants = [];
        if ($conn) {
            $q_rel = mysqli_query($conn, "SELECT * FROM plants WHERE id != $id ORDER BY RAND() LIMIT 8");
            if ($q_rel && mysqli_num_rows($q_rel) > 0) {
                while($r = mysqli_fetch_assoc($q_rel)) {
                    $related_plants[] = $r;
                }
            }
        }
        ?>
        <?php if(count($related_plants) > 0): ?>
        <!-- Related Products Section -->
        <div class="mt-5 pt-5 border-top">
            <h3 class="mb-4" style="font-family: var(--font-heading); font-weight: 800; color: var(--text-primary); font-size: 2rem;">You May Also Like</h3>
            <div class="related-carousel-wrapper" style="position: relative; overflow: hidden; padding-bottom: 20px;">
                <div class="related-carousel" style="display: flex; gap: 24px; overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none;">
                    <?php foreach($related_plants as $rp): 
                        $rp_img = !empty($rp['image']) ? $rp['image'] : 'assets/images/plants/default.jpg';
                    ?>
                        <div style="flex: 0 0 280px;">
                            <div class="plant-card h-100 d-flex flex-column">
                                <div class="plant-card-img">
                                    <a href="product.php?id=<?php echo $rp['id']; ?>">
                                        <img src="<?php echo $rp_img; ?>" alt="<?php echo $rp['name']; ?>">
                                    </a>
                                    <span class="plant-badge"><?php echo $rp['category']; ?></span>
                                </div>
                                <div class="plant-card-body d-flex flex-column flex-grow-1">
                                    <h5><a href="product.php?id=<?php echo $rp['id']; ?>"><?php echo $rp['name']; ?></a></h5>
                                    <div class="plant-meta">
                                        <?php
                                        $care_levels = ['Easy care', 'Moderate care', 'Low maintenance', 'Needs attention', 'Water regularly'];
                                        $care_text = $care_levels[$rp['id'] % count($care_levels)];
                                        $rating = 4.0 + (($rp['id'] % 10) / 10);
                                        ?>
                                        <span><i class="fa-solid fa-star" style="color: #f59e0b;"></i> <?php echo number_format($rating, 1); ?></span>
                                        <span><i class="fa-solid fa-droplet" style="color: #64748b;"></i> <?php echo $care_text; ?></span>
                                    </div>
                                    <div class="plant-card-footer mt-auto">
                                        <div class="plant-price">Rs <?php echo number_format($rp['price']); ?></div>
                                        <?php if(isset($is_admin) && $is_admin): ?>
                                            <button type="button" class="btn-add-cart" onclick="showAdminToast()" title="View Only (Admin)" style="background: var(--text-secondary); color:#fff; border-radius:50%; width:40px; height:40px; display:flex; align-items:center; justify-content:center; border:none;"><i class="fa-solid fa-eye"></i></button>
                                        <?php else: ?>
                                            <form method="POST" action="cart_action.php" style="margin:0;">
                                                <input type="hidden" name="plant_id" value="<?php echo $rp['id']; ?>">
                                                <input type="hidden" name="action" value="add">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn-add-cart" style="background:var(--primary); color:#fff; border-radius:50%; width:40px; height:40px; display:flex; align-items:center; justify-content:center; border:none;"><i class="fa-solid fa-arrow-right"></i></button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const carousel = document.querySelector('.related-carousel');
                
                if(carousel) {
                    // Duplicate content for infinite scrolling effect
                    const clone = carousel.innerHTML;
                    carousel.innerHTML += clone;
                    
                    let isDown = false;
                    let startX;
                    let scrollLeft;
                    let autoScrollInterval;
                    
                    const startAutoScroll = () => {
                        autoScrollInterval = setInterval(() => {
                            if(!isDown) {
                                carousel.scrollLeft += 1;
                                // If we've scrolled past the first set of items, seamlessly jump back to start
                                if(carousel.scrollLeft >= carousel.scrollWidth / 2) {
                                    carousel.scrollLeft = 0;
                                }
                            }
                        }, 20); // Adjust speed here
                    };
                    
                    startAutoScroll();
                    
                    carousel.addEventListener('mousedown', (e) => {
                        isDown = true;
                        startX = e.pageX - carousel.offsetLeft;
                        scrollLeft = carousel.scrollLeft;
                        clearInterval(autoScrollInterval);
                    });
                    carousel.addEventListener('mouseleave', () => { 
                        if(isDown) startAutoScroll();
                        isDown = false; 
                    });
                    carousel.addEventListener('mouseup', () => { 
                        isDown = false; 
                        startAutoScroll();
                    });
                    carousel.addEventListener('mousemove', (e) => {
                        if(!isDown) return;
                        e.preventDefault();
                        const x = e.pageX - carousel.offsetLeft;
                        const walk = (x - startX) * 2;
                        carousel.scrollLeft = scrollLeft - walk;
                    });
                }
            });
        </script>
        <style>
        .related-carousel::-webkit-scrollbar { display: none; }
        </style>
        <?php endif; ?>
    </div>
</main>

<script>
function zoomImage(e, container) {
    const img = container.querySelector('.product-zoom-img');
    const rect = container.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    
    // Calculate percentage position
    const xPercent = (x / rect.width) * 100;
    const yPercent = (y / rect.height) * 100;
    
    img.style.transformOrigin = `${xPercent}% ${yPercent}%`;
    img.style.transform = 'scale(1.8)';
}

function resetZoom(container) {
    const img = container.querySelector('.product-zoom-img');
    img.style.transformOrigin = 'center center';
    img.style.transform = 'scale(1)';
}
</script>

<?php include("includes/footer.php"); ?>
