<?php
include("includes/head.php");
include("includes/header.php");
include("includes/db_connect.php");

$wishlist_items = [];
if (isset($_SESSION['wishlist']) && count($_SESSION['wishlist']) > 0) {
    $ids = implode(',', array_map('intval', $_SESSION['wishlist']));
    if ($conn && !empty($ids)) {
        $q = mysqli_query($conn, "SELECT * FROM plants WHERE id IN ($ids)");
        if ($q && mysqli_num_rows($q) > 0) {
            while ($row = mysqli_fetch_assoc($q)) {
                $wishlist_items[] = $row;
            }
        }
    }
}
?>

<!-- Page Header -->
<div class="page-header">
    <img src="assets/images/plants/hero_image_transparent_2.png" alt="Decoration" class="hero-plant-left">
    <img src="assets/images/plants/hero_image_transparent_2.png" alt="Decoration" class="hero-plant-right" style="transform: translateY(-50%) scaleX(-1);">
    
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
    
    <div class="container" style="position: relative; z-index: 2;">
        <h1><i class="fa-solid fa-heart me-2" style="color: #f43f5e;"></i> My Wishlist</h1>
        <p>Your favorite plants, ready to be added to your cart.</p>
    </div>
</div>

<main style="padding: 40px 0 80px; position: relative; overflow: hidden; min-height: 60vh;">
    <div class="container">

        <?php if (count($wishlist_items) > 0): ?>
            <div class="row g-4 mb-5">
                <?php foreach ($wishlist_items as $item): 
                    $img = !empty($item['image']) ? $item['image'] : 'assets/images/plants/default.jpg';
                ?>
                    <div class="col-12 col-sm-6 col-lg-3" id="wishlist-item-<?php echo $item['id']; ?>">
                        <div class="plant-card h-100 d-flex flex-column">
                            <div class="plant-card-img position-relative">
                                <a href="product.php?id=<?php echo $item['id']; ?>">
                                    <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                </a>
                                <span class="plant-badge"><?php echo htmlspecialchars($item['category']); ?></span>
                                <button type="button" class="btn position-absolute top-0 end-0 m-2" style="background: white; border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1);" onclick="removeFromWishlist(<?php echo $item['id']; ?>)">
                                    <i class="fa-solid fa-xmark text-danger"></i>
                                </button>
                            </div>
                            <div class="plant-card-body d-flex flex-column flex-grow-1">
                                <h5><a href="product.php?id=<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['name']); ?></a></h5>
                                <div class="plant-meta">
                                    <?php
                                    $care_levels = ['Easy care', 'Moderate care', 'Low maintenance', 'Needs attention', 'Water regularly'];
                                    $care_text = $care_levels[$item['id'] % count($care_levels)];
                                    $rating = 4.0 + (($item['id'] % 10) / 10);
                                    ?>
                                    <span><i class="fa-solid fa-star" style="color: #f59e0b;"></i> <?php echo number_format($rating, 1); ?></span>
                                    <span><i class="fa-solid fa-droplet" style="color: #64748b;"></i> <?php echo $care_text; ?></span>
                                </div>
                                <div class="plant-card-footer mt-auto">
                                    <div class="plant-price">Rs <?php echo number_format($item['price']); ?></div>
                                    <form method="POST" action="cart_action.php" style="margin:0;">
                                        <input type="hidden" name="plant_id" value="<?php echo $item['id']; ?>">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn-add-cart" style="background:var(--primary); color:#fff; border-radius:50%; width:40px; height:40px; display:flex; align-items:center; justify-content:center; border:none;"><i class="fa-solid fa-cart-plus"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5" style="background: #f8fafc; border-radius: 12px; border: 1px dashed var(--border);">
                <i class="fa-regular fa-heart mb-3" style="font-size: 4rem; color: #cbd5e1;"></i>
                <h3 class="text-secondary">Your wishlist is empty</h3>
                <p class="text-muted mb-4">Looks like you haven't added any favorite plants yet.</p>
                <a href="shop.php" class="btn btn-flora px-4 py-2">Explore Shop</a>
            </div>
        <?php endif; ?>

        <?php
        // Fetch related plants for carousel
        $related_plants = [];
        if ($conn) {
            $q_rel = mysqli_query($conn, "SELECT * FROM plants ORDER BY RAND() LIMIT 8");
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
                                        <img src="<?php echo $rp_img; ?>" alt="<?php echo htmlspecialchars($rp['name']); ?>">
                                    </a>
                                    <span class="plant-badge"><?php echo htmlspecialchars($rp['category']); ?></span>
                                </div>
                                <div class="plant-card-body d-flex flex-column flex-grow-1">
                                    <h5><a href="product.php?id=<?php echo $rp['id']; ?>"><?php echo htmlspecialchars($rp['name']); ?></a></h5>
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

            function removeFromWishlist(id) {
                fetch('wishlist_action.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=toggle&plant_id=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'success') {
                        location.reload(); // Reload to update UI
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        </script>
        <style>
        .related-carousel::-webkit-scrollbar { display: none; }
        </style>
        <?php endif; ?>
    </div>
</main>

<?php include("includes/footer.php"); ?>
