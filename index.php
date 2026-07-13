<?php
include("includes/head.php");
include("includes/header.php");
include("includes/db_connect.php");

$featured_result = false;
if ($conn) {
    $featured_query = "SELECT * FROM plants ORDER BY created_at DESC LIMIT 8";
    $featured_result = mysqli_query($conn, $featured_query);
}
?>

<!-- ======= HERO SECTION ======= -->
<section class="hero-section" style="position: relative; background: radial-gradient(circle at 75% 50%, #0a3d21 0%, #031409 100%);">
    <!-- Blurred Dark Overlay (Mobile Only) -->
    <div class="d-block d-lg-none" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(rgba(10, 61, 33, 0.75), rgba(3, 20, 9, 0.95)); backdrop-filter: blur(10px); z-index: 0;"></div>
    
    <div class="container" style="position: relative; z-index: 1;">
        <div class="row align-items-center">
            <div class="col-lg-6 pt-2 pt-lg-5" style="position: relative; z-index: 2;">
                <div class="hero-tag" style="display: inline-flex; align-items: center; gap: 10px; background: rgba(255, 255, 255, 0.05); color: #86efac; border: 1px solid rgba(134, 239, 172, 0.2); padding: 10px 24px; border-radius: 50px; font-size: 0.85rem; font-weight: 500; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 30px; backdrop-filter: blur(10px);">
                    <i class="fa-solid fa-leaf"></i>
                    FloraFetch Premium
                </div>
                <h1 class="hero-title" style="color: #f8fafc; font-family: 'Sora', sans-serif; font-size: clamp(2rem, 8vw, 4.2rem); font-weight: 700; line-height: 1.1; letter-spacing: -1px; animation: fadeInUp 0.6s ease forwards; text-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                    Top Selling<br>
                    <span style="color: #e2e8f0;">Plants <span style="color: #4ade80;">Collection.</span></span>
                </h1>
                <p class="hero-subtitle" style="color: #cbd5e1; font-family: 'DM Sans', sans-serif; animation: fadeInUp 0.6s ease 0.1s forwards; opacity:0; font-size: 1.15rem; line-height: 1.7; font-weight: 300; max-width: 440px; margin-bottom: 30px; margin-top: 20px;">
                    Curating nature through premium indoor and outdoor plants, easy care guides, and secure delivery right to your door.
                </p>
                <div class="hero-buttons" style="animation: fadeInUp 0.6s ease 0.2s forwards; opacity:0; margin-bottom: 40px;">
                    <a href="shop.php" class="btn-explore-hero">
                        Explore Shop <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </div>
                <div class="hero-stats" style="display: flex; gap: 3rem; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 30px; animation: fadeInUp 0.6s ease 0.3s forwards; opacity:0;">
                    <div class="hero-stat">
                        <h3 style="color: #f8fafc; font-size: 2rem; font-weight: 700; margin-bottom: 4px; font-family: 'Sora', sans-serif;">500<span style="color: #4ade80;">+</span></h3>
                        <p style="color: #94a3b8; font-size: 0.9rem; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; margin: 0;">Plant Species</p>
                    </div>
                    <div class="hero-stat">
                        <h3 style="color: #f8fafc; font-size: 2rem; font-weight: 700; margin-bottom: 4px; font-family: 'Sora', sans-serif;">10K<span style="color: #4ade80;">+</span></h3>
                        <p style="color: #94a3b8; font-size: 0.9rem; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; margin: 0;">Happy Customers</p>
                    </div>
                    <div class="hero-stat">
                        <h3 style="color: #f8fafc; font-size: 2rem; font-weight: 700; margin-bottom: 4px; font-family: 'Sora', sans-serif;">4.9<span style="color: #4ade80;">★</span></h3>
                        <p style="color: #94a3b8; font-size: 0.9rem; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; margin: 0;">Average Rating</p>
                    </div>
                </div>
            </div>


            <div class="col-lg-6 d-none d-lg-block text-end">
                <div class="hero-image-wrapper" style="animation: fadeInRight 0.8s ease 0.2s forwards; opacity:0; background: transparent; transform: scale(1.1) translate(0px, 20px); position: relative; z-index: 1;">
                    <!-- Giant transparent custom plant composition from generated assets -->
                    <img src="assets/images/plants/hero_image_transparent_2.png?v=<?php echo time(); ?>" alt="Premium Garden Composition" style="border-radius: 0; box-shadow: none; max-height: 650px; max-width: 100%; object-fit: contain; transform: scale(1.15); filter: drop-shadow(0 20px 40px rgba(0,0,0,0.45));">
                    
                    <!-- Animated 3D Butterfly -->
                    <div class="butterfly-container" style="top: 35%; right: 40%;">
                        <div class="butterfly">
                            <div class="wing left"></div>
                            <div class="wing right"></div>
                            <div class="butterfly-body"></div>
                        </div>
                    </div>

                    <!-- Crawling Ladybug on the plant leaf -->
                    <div class="ladybug" style="top: 55%; right: 28%; z-index: 5;"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======= CATEGORIES ======= -->
<section class="categories-section" id="categories">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-tag"><i class="fa-solid fa-grip"></i> Browse by Type</div>
            <h2 class="section-title">Plant Categories</h2>
            <p class="section-subtitle mx-auto">Find the perfect plant for every corner of your space.</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-4 col-12 fade-in-up">
                <a href="shop.php?category=Indoor">
                    <div class="category-card">
                        <div class="category-icon"><i class="fa-solid fa-house-chimney"></i></div>
                        <h5>Indoor</h5>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-12 fade-in-up">
                <a href="shop.php?category=Outdoor">
                    <div class="category-card">
                        <div class="category-icon"><i class="fa-solid fa-sun"></i></div>
                        <h5>Outdoor</h5>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-12 fade-in-up">
                <a href="shop.php?category=Succulents">
                    <div class="category-card">
                        <div class="category-icon"><i class="fa-solid fa-droplet"></i></div>
                        <h5>Succulents</h5>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-12 fade-in-up">
                <a href="shop.php?category=Flowering">
                    <div class="category-card">
                        <div class="category-icon"><i class="fa-solid fa-spa"></i></div>
                        <h5>Flowering</h5>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ======= FEATURED PLANTS ======= -->
<section class="featured-section" id="featured">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-tag mx-auto"><i class="fa-solid fa-fire"></i> Popular Picks</div>
            <h2 class="section-title mb-0">Featured Plants</h2>
        </div>
        <div class="row g-4">
            <?php
            if ($featured_result && mysqli_num_rows($featured_result) > 0) {
                $counter = 0;
                while ($plant = mysqli_fetch_assoc($featured_result)) {
                    $counter++;
                    $display_class = ($counter > 5) ? 'd-none d-lg-block' : '';
                    $img_src = !empty($plant['image']) ? $plant['image'] : 'https://images.unsplash.com/photo-1459411552884-841db9b3cc2a?w=400&h=300&fit=crop';
            ?>
                <div class="col-lg-3 col-md-4 col-12 fade-in-up <?php echo $display_class; ?>">
                    <div class="plant-card">
                        <div class="plant-card-img">
                            <a href="product.php?id=<?php echo $plant['id']; ?>"><img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($plant['name']); ?>"></a>
                            <span class="plant-badge"><?php echo htmlspecialchars($plant['category']); ?></span>
                        </div>
                        <div class="plant-card-body">
                            <div class="plant-category-tag"><?php echo htmlspecialchars($plant['category']); ?></div>
                            <h5><a href="product.php?id=<?php echo $plant['id']; ?>" class="text-decoration-none text-dark"><?php echo htmlspecialchars($plant['name']); ?></a></h5>
                            <div class="plant-meta">
                                <span><i class="fa-solid fa-leaf"></i> <?php echo htmlspecialchars(substr($plant['care_guide'], 0, 15)); ?>...</span>
                            </div>
                            <div class="plant-card-footer">
                                <div class="plant-price">
                                    Rs <?php echo number_format($plant['price']); ?>
                                </div>
                                <?php if(isset($is_admin) && $is_admin): ?>
                                    <button type="button" class="btn-add-cart" onclick="showAdminToast()" title="View Only (Admin)" style="background: var(--text-secondary); color:#fff; border:none;"><i class="fa-solid fa-eye"></i></button>
                                <?php else: ?>
                                    <form method="POST" action="cart_action.php" style="margin:0;">
                                        <input type="hidden" name="plant_id" value="<?php echo $plant['id']; ?>">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn-add-cart" style="border:none;"><i class="fa-solid fa-bag-shopping"></i></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                }
            } else {
                echo "<div class='col-12 text-center text-secondary py-5'>No featured plants found in the database.</div>";
            }
            ?>
        </div>
        <div class="text-center mt-5">
            <a href="shop.php" class="btn btn-premium-green" style="padding: 12px 30px; font-size: 1.1rem; border-radius: 50px;">
                Explore More <i class="fa-solid fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>


<!-- ======= WHY CHOOSE US ======= -->
<section class="features-section">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-tag"><i class="fa-solid fa-star"></i> Why FloraFetch</div>
            <h2 class="section-title">Why Plant Parents Love Us</h2>
            <p class="section-subtitle mx-auto">We don't just sell plants — we deliver happiness in a pot.</p>
        </div>
        <div class="row g-4 step-container">
            <div class="step-line d-none d-lg-block"></div>
            
            <div class="col-lg-3 col-md-6 fade-in-up">
                <div class="feature-card step-card">
                    <div class="step-number">01</div>
                    <div class="feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
                    <h5>Quality Guaranteed</h5>
                    <p>Every plant undergoes a health check before dispatch from our nursery.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 fade-in-up" style="animation-delay: 0.1s;">
                <div class="feature-card step-card">
                    <div class="step-number">02</div>
                    <div class="feature-icon"><i class="fa-solid fa-box-open"></i></div>
                    <h5>Safe Packaging</h5>
                    <p>Specialized packaging designed for delicate leaves ensures healthy arrival.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 fade-in-up" style="animation-delay: 0.2s;">
                <div class="feature-card step-card">
                    <div class="step-number">03</div>
                    <div class="feature-icon"><i class="fa-solid fa-book-open"></i></div>
                    <h5>Care Guides</h5>
                    <p>Each plant comes with a detailed care guide — sunlight, water & soil.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 fade-in-up" style="animation-delay: 0.3s;">
                <div class="feature-card step-card">
                    <div class="step-number">04</div>
                    <div class="feature-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
                    <h5>Cash on Delivery</h5>
                    <p>Pay only when you receive and inspect your plant. Trust first.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======= OUR TEAM ======= -->
<section class="team-section" style="padding: 80px 0; background: radial-gradient(circle at 25% 50%, #0a3d21 0%, #031409 100%);">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <!-- Using a highly reliable local team image -->
                <img src="assets/images/plants/team_nursery_2.jpg" alt="Our Plant Experts" style="width: 100%; border-radius: var(--radius-lg); box-shadow: 0 15px 35px rgba(0,0,0,0.4); object-fit: cover; max-height: 500px;">
            </div>
            <div class="col-lg-6 text-center text-lg-start">
                <div class="section-tag mx-auto mx-lg-0" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(134, 239, 172, 0.2); color: #86efac; display: inline-flex; padding: 6px 16px; border-radius: 50px; font-size: 0.85rem; font-weight: 600; margin-bottom: 20px;"><i class="fa-solid fa-users-gear me-2"></i> Expert Care</div>
                <h2 class="section-title" style="color: #f8fafc; font-weight: 700; margin-bottom: 20px;">Nurtured by Professionals</h2>
                <p class="section-subtitle mb-4 mx-auto mx-lg-0" style="max-width: 100%; color: #cbd5e1; font-size: 1.1rem; line-height: 1.6;">Behind every healthy plant delivered to your door is a team of dedicated horticulturists and nursery specialists. We don't just sell plants; we nurture them with passion, expertise, and a deep understanding of botanical care.</p>
                
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 15px;">
                    <li style="display: flex; align-items: flex-start; gap: 12px;">
                        <i class="fa-solid fa-check-circle" style="color: #4ade80; font-size: 1.2rem; margin-top: 3px;"></i>
                        <span style="color: #e2e8f0; font-size: 1.05rem;">Daily health inspections and organic pest control.</span>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: 12px;">
                        <i class="fa-solid fa-check-circle" style="color: #4ade80; font-size: 1.2rem; margin-top: 3px;"></i>
                        <span style="color: #e2e8f0; font-size: 1.05rem;">Custom soil mixing for optimal root growth.</span>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: 12px;">
                        <i class="fa-solid fa-check-circle" style="color: #4ade80; font-size: 1.2rem; margin-top: 3px;"></i>
                        <span style="color: #e2e8f0; font-size: 1.05rem;">Professional pruning and acclimatization before shipping.</span>
                    </li>
                </ul>
                <div class="text-center text-lg-start">
                    <a href="aboutus.php" class="btn mt-4" style="background: linear-gradient(135deg, #22c55e, #15803d); color: #fff; padding: 12px 28px; border-radius: 50px; font-weight: 600; border: none; box-shadow: 0 8px 25px rgba(34, 197, 94, 0.4); display: inline-flex; align-items: center; gap: 10px;">Meet The Team <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======= FAQ SECTION ======= -->
<section class="faq-section" style="padding: 80px 0;">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-tag"><i class="fa-solid fa-circle-question"></i> Help Center</div>
            <h2 class="section-title">Frequently Asked Questions</h2>
            <p class="section-subtitle mx-auto">Everything you need to know about ordering and plant care.</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <!-- FAQ 1 -->
                    <div class="accordion-item faq-custom-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                How are plants packaged for safe delivery?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We use custom-designed corrugated boxes with internal structural supports that hold the pot firmly in place. The soil is covered to prevent spillage, and delicate leaves are gently wrapped in breathable material to prevent bruising during transit.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ 2 -->
                    <div class="accordion-item faq-custom-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                What if my plant arrives damaged?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We have a 7-day live delivery guarantee! If your plant arrives severely damaged or dead, simply send us a photo on WhatsApp or Email within 24 hours of receiving it, and we will send a free replacement right away.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ 3 -->
                    <div class="accordion-item faq-custom-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Do you provide care instructions?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes! Every plant comes with a physical care card containing instructions for sunlight, watering schedules, and humidity preferences. You can also contact our team anytime for expert advice.
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======= CTA / NEWSLETTER ======= -->
<section class="cta-section">
    <div class="container">
        <div class="cta-box">
            <div class="row align-items-center">
                <div class="col-lg-7 text-center text-lg-start">
                    <h2>Stay in the Green Loop</h2>
                    <p class="mx-auto mx-lg-0">Curating nature through premium indoor and outdoor plants, easy care guides, and secure delivery right to your door.</p>
                    <div style="margin-top: 25px;">
                        <a href="shop.php" class="btn-premium-green">Shop Now <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-5 text-center d-none d-lg-block">
                    <img src="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400&h=280&fit=crop" alt="Plants" style="border-radius: var(--radius-lg); max-height: 240px; opacity: 0.8;">
                </div>
            </div>
        </div>
    </div>
</section>

<?php include("includes/footer.php"); ?>
