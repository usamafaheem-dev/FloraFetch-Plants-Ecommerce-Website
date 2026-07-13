<?php
include("includes/head.php");
include("includes/header.php");
include("includes/db_connect.php");

// Get active filter
$active_category = isset($_GET['category']) ? $_GET['category'] : '';
$active_price = isset($_GET['price']) ? $_GET['price'] : '';
$active_sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Helper function to generate query string with updated sort parameter
function getSortUrl($sortVal) {
    global $active_category, $active_price;
    $params = [];
    if ($active_category) $params['category'] = $active_category;
    if ($active_price) $params['price'] = $active_price;
    $params['sort'] = $sortVal;
    return 'shop.php?' . http_build_query($params);
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
        <h1>Shop Plants</h1>
        <p>Explore our curated collection of healthy, beautiful plants.</p>
    </div>
</div>

<main style="padding: 40px 0 80px; position: relative; overflow: hidden;">
    
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row g-4">
            
            <!-- LEFT SIDEBAR — Filters -->
            <div class="col-lg-3 pe-lg-4">
                <form id="filterForm" action="shop.php" method="GET" class="shop-sidebar">
                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($active_sort); ?>">
                    <!-- Categories Filter -->
                    <div class="filter-group">
                        <h6><i class="fa-solid fa-layer-group me-2"></i>Categories</h6>
                        <div>
                            <input class="custom-filter-radio" type="radio" name="category" id="cat-all" value="" <?php echo $active_category == '' ? 'checked' : ''; ?>>
                            <label class="custom-filter-label" for="cat-all"><i class="fa-solid fa-seedling"></i>All Plants</label>
                        </div>
                        <div>
                            <input class="custom-filter-radio" type="radio" name="category" id="cat-indoor" value="Indoor" <?php echo $active_category == 'Indoor' ? 'checked' : ''; ?>>
                            <label class="custom-filter-label" for="cat-indoor"><i class="fa-solid fa-house"></i>Indoor</label>
                        </div>
                        <div>
                            <input class="custom-filter-radio" type="radio" name="category" id="cat-outdoor" value="Outdoor" <?php echo $active_category == 'Outdoor' ? 'checked' : ''; ?>>
                            <label class="custom-filter-label" for="cat-outdoor"><i class="fa-solid fa-sun"></i>Outdoor</label>
                        </div>
                        <div>
                            <input class="custom-filter-radio" type="radio" name="category" id="cat-succulents" value="Succulents" <?php echo $active_category == 'Succulents' ? 'checked' : ''; ?>>
                            <label class="custom-filter-label" for="cat-succulents"><i class="fa-solid fa-leaf"></i>Succulents</label>
                        </div>
                        <div>
                            <input class="custom-filter-radio" type="radio" name="category" id="cat-flowering" value="Flowering" <?php echo $active_category == 'Flowering' ? 'checked' : ''; ?>>
                            <label class="custom-filter-label" for="cat-flowering"><i class="fa-solid fa-fan"></i>Flowering</label>
                        </div>
                        <div>
                            <input class="custom-filter-radio" type="radio" name="category" id="cat-herbs" value="Herbs" <?php echo $active_category == 'Herbs' ? 'checked' : ''; ?>>
                            <label class="custom-filter-label" for="cat-herbs"><i class="fa-solid fa-kitchen-set"></i>Herbs</label>
                        </div>
                    </div>

                    <!-- Price Range Filter -->
                    <div class="filter-group">
                        <h6><i class="fa-solid fa-tag me-2"></i>Price Range</h6>
                        <div>
                            <input class="custom-filter-radio" type="radio" name="price" id="price-all" value="" <?php echo $active_price == '' ? 'checked' : ''; ?>>
                            <label class="custom-filter-label" for="price-all"><i class="fa-solid fa-circle-check"></i>All Prices</label>
                        </div>
                        <div>
                            <input class="custom-filter-radio" type="radio" name="price" id="price-1" value="under_1000" <?php echo $active_price == 'under_1000' ? 'checked' : ''; ?>>
                            <label class="custom-filter-label" for="price-1"><i class="fa-solid fa-circle-check"></i>Under Rs 1,000</label>
                        </div>
                        <div>
                            <input class="custom-filter-radio" type="radio" name="price" id="price-2" value="1000_3000" <?php echo $active_price == '1000_3000' ? 'checked' : ''; ?>>
                            <label class="custom-filter-label" for="price-2"><i class="fa-solid fa-circle-check"></i>Rs 1,000 - Rs 3,000</label>
                        </div>
                        <div>
                            <input class="custom-filter-radio" type="radio" name="price" id="price-3" value="over_3000" <?php echo $active_price == 'over_3000' ? 'checked' : ''; ?>>
                            <label class="custom-filter-label" for="price-3"><i class="fa-solid fa-circle-check"></i>Rs 3,000+</label>
                        </div>
                    </div>

                    <!-- Help Box -->
                    <div class="filter-group d-none d-lg-block" style="background: var(--primary); border: none; color: #fff; text-align: center; padding: 28px 20px;">
                        <i class="fa-solid fa-headset" style="font-size: 2rem; margin-bottom: 12px; display: block;"></i>
                        <h6 style="color: #fff; margin-bottom: 6px;">Need Help?</h6>
                        <p style="font-size: 0.82rem; opacity: 0.85; margin-bottom: 12px;">Can't find the right plant? Our experts can help.</p>
                        <a href="contactus.php" class="btn btn-sm" style="background: #fff; color: var(--primary); font-weight: 600; border-radius: 8px; padding: 8px 20px;">Contact Us</a>
                    </div>
                </form>
            </div>
            
            <!-- RIGHT — Product Grid -->
            <div class="col-lg-9 pt-lg-2" id="shop-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <p class="text-secondary mb-0" style="font-size: 0.88rem;">
                        Showing <strong class="text-dark"><?php echo $active_category ? $active_category : 'All'; ?></strong> plants
                    </p>
                    <div class="dropdown sort-dropdown d-none d-lg-block">
                        <button class="btn btn-sort dropdown-toggle" type="button" id="sortMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-arrow-down-short-wide me-1"></i> 
                            <?php 
                                if($active_sort == 'price_low') echo 'Price: Low to High';
                                elseif($active_sort == 'price_high') echo 'Price: High to Low';
                                else echo 'Newest First';
                            ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="sortMenuButton">
                            <li><a class="dropdown-item <?php echo $active_sort == 'newest' ? 'active' : ''; ?>" href="<?php echo getSortUrl('newest'); ?>">Newest First</a></li>
                            <li><a class="dropdown-item <?php echo $active_sort == 'price_low' ? 'active' : ''; ?>" href="<?php echo getSortUrl('price_low'); ?>">Price: Low to High</a></li>
                            <li><a class="dropdown-item <?php echo $active_sort == 'price_high' ? 'active' : ''; ?>" href="<?php echo getSortUrl('price_high'); ?>">Price: High to Low</a></li>
                        </ul>
                    </div>
                </div>

                <div class="row g-3">
                    <?php
                    $has_plants = false;
                    if ($conn) {
                        $query = "SELECT * FROM plants WHERE 1=1";
                        if ($active_category) {
                            $query .= " AND category LIKE '%" . mysqli_real_escape_string($conn, $active_category) . "%'";
                        }
                        
                        if ($active_price == 'under_1000') {
                            $query .= " AND CAST(REPLACE(price, ',', '') AS DECIMAL) < 1000";
                        } elseif ($active_price == '1000_3000') {
                            $query .= " AND CAST(REPLACE(price, ',', '') AS DECIMAL) BETWEEN 1000 AND 3000";
                        } elseif ($active_price == 'over_3000') {
                            $query .= " AND CAST(REPLACE(price, ',', '') AS DECIMAL) >= 3000";
                        }
                        
                        if ($active_sort == 'price_low') {
                            $query .= " ORDER BY CAST(REPLACE(price, ',', '') AS DECIMAL) ASC";
                        } elseif ($active_sort == 'price_high') {
                            $query .= " ORDER BY CAST(REPLACE(price, ',', '') AS DECIMAL) DESC";
                        } else {
                            $query .= " ORDER BY created_at DESC";
                        }
                        $result = mysqli_query($conn, $query);
                        
                        if ($result && mysqli_num_rows($result) > 0) {
                            $has_plants = true;
                            while ($row = mysqli_fetch_assoc($result)) {
                                $img = !empty($row['image']) ? $row['image'] : 'assets/images/plants/default.jpg';
                                ?>
                                <div class="col-lg-4 col-md-6">
                                    <div class="plant-card h-100 d-flex flex-column">
                                        <div class="plant-card-img">
                                        <a href="product.php?id=<?php echo $row['id']; ?>">
                                            <img src="<?php echo $img; ?>" alt="<?php echo $row['name']; ?>">
                                        </a>
                                        <span class="plant-badge"><?php echo $row['category']; ?></span>
                                        <div class="plant-wishlist" onclick="toggleWishlist(<?php echo $row['id']; ?>, this)" style="cursor: pointer;">
                                            <i class="<?php echo (isset($_SESSION['wishlist']) && in_array($row['id'], $_SESSION['wishlist'])) ? 'fa-solid text-danger' : 'fa-regular'; ?> fa-heart"></i>
                                        </div>
                                    </div>
                                    <div class="plant-card-body d-flex flex-column flex-grow-1">
                                        <h5><a href="product.php?id=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a></h5>
                                        <div class="plant-meta">
                                            <?php
                                            $care_levels = ['Easy care', 'Moderate care', 'Low maintenance', 'Needs attention', 'Water regularly'];
                                            $care_text = $care_levels[$row['id'] % count($care_levels)];
                                            $rating = 4.0 + (($row['id'] % 10) / 10);
                                            ?>
                                            <span><i class="fa-solid fa-star" style="color: #f59e0b;"></i> <?php echo number_format($rating, 1); ?></span>
                                            <span><i class="fa-solid fa-droplet"></i> <?php echo $care_text; ?></span>
                                        </div>
                                        <div class="mt-2 text-muted" style="font-size: 0.8rem; font-weight: 500;">
                                            <i class="fa-solid fa-box-open text-secondary me-1"></i> <?php echo $row['stock_quantity'] > 0 ? "{$row['stock_quantity']} in stock" : "<span class='text-danger'>Out of stock</span>"; ?>
                                        </div>
                                        <div class="plant-card-footer mt-auto">
                                                <div class="plant-price">Rs <?php echo number_format($row['price']); ?></div>
                                                <?php if(isset($is_admin) && $is_admin): ?>
                                                <button type="button" class="btn-add-cart" onclick="showAdminToast()" title="View Only (Admin)" style="background: var(--text-secondary);"><i class="fa-solid fa-eye"></i></button>
                                            <?php else: ?>
                                                <form method="POST" action="cart_action.php" style="margin:0;">
                                                    <input type="hidden" name="plant_id" value="<?php echo $row['id']; ?>">
                                                    <input type="hidden" name="action" value="add">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="btn-add-cart"><i class="fa-solid fa-bag-shopping"></i></button>
                                                </form>
                                            <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                    }

                    if (!$has_plants) {
                        ?>
                        <div class="col-12 text-center py-5" style="margin-top: 6rem; margin-bottom: 6rem;">
                            <i class="fa-solid fa-leaf text-muted mb-3" style="font-size: 3.5rem; opacity: 0.3;"></i>
                            <h4 class="text-secondary mt-2">No plants found</h4>
                            <p class="text-muted mb-4">We couldn't find any plants matching your current filters.</p>
                            <button type="button" class="btn btn-flora px-4 py-2" onclick="document.getElementById('cat-all').click(); document.getElementById('price-all').click();" style="border-radius: 50px;">
                                <i class="fa-solid fa-rotate-right me-2"></i>Clear Filters
                            </button>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Shop CTA -->
<section class="cta-section" style="padding-top: 0;">
    <div class="container">
        <div class="cta-box">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="cta-title-mobile">Can't find what you're looking for?</h2>
                    <p class="mb-3 cta-desc-mobile">Tell us your requirements and we'll source the perfect plant for you.</p>
                    <a href="contactus.php" class="btn" style="background: #fff; color: var(--primary); font-weight: 600; padding: 10px 24px; border-radius: 8px;">
                        <i class="fa-solid fa-envelope me-2"></i>Get in Touch
                    </a>
                </div>
                <div class="col-lg-4 text-end d-none d-lg-block">
                    <i class="fa-solid fa-seedling" style="font-size: 5rem; color: rgba(255,255,255,0.15);"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<script>

document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    
    function fetchShopContent(url) {
        const shopContent = document.getElementById('shop-content');
        shopContent.style.opacity = '0.5';
        shopContent.style.transition = 'opacity 0.3s ease';
        
        fetch(url)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('shop-content').innerHTML;
                
                shopContent.innerHTML = newContent;
                shopContent.style.opacity = '1';
                
                // Update URL to allow sharing and history
                window.history.pushState({path: url}, '', url);
                
                attachSortEvents();
            })
            .catch(error => {
                console.error('Error fetching shop content:', error);
                shopContent.style.opacity = '1';
            });
    }

    // Attach event listeners to filter radio buttons
    const radios = filterForm.querySelectorAll('input[type="radio"]');
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            fetchShopContent('shop.php?' + params.toString());
        });
    });

    // Attach event listeners to sort dropdown
    function attachSortEvents() {
        const sortLinks = document.querySelectorAll('.sort-dropdown .dropdown-item');
        sortLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                
                // Update hidden sort input
                const urlObj = new URL(url, window.location.origin);
                const sortParam = urlObj.searchParams.get('sort');
                if (sortParam) {
                    const sortInput = filterForm.querySelector('input[name="sort"]');
                    if(sortInput) sortInput.value = sortParam;
                }
                
                fetchShopContent(url);
            });
        });
    }
    attachSortEvents();
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        window.location.reload();
    });
});
</script>

<?php include("includes/footer.php"); ?>
