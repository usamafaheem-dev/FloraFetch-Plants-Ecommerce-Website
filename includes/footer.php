<!-- ======= FOOTER ======= -->
<footer class="flora-footer" style="background: linear-gradient(rgba(2, 44, 34, 0.9), rgba(4, 47, 31, 0.95)), url('https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?q=80&w=2070&auto=format&fit=crop') center/cover no-repeat; padding-top: 100px; color: #f8fafc; border-top: 1px solid rgba(255,255,255,0.05);">
    <div class="container">
        <div class="row g-4 justify-content-between">
            <!-- Brand Column -->
            <div class="col-lg-4 col-md-6 text-center text-md-start mb-4 mb-md-0">
                <div class="footer-brand">
                    <i class="fa-solid fa-leaf"></i>
                    Flora<span>Fetch</span>
                </div>
                <p>Your destination for healthy, hand-picked plants delivered safely to your doorstep across Pakistan.</p>
                <div class="footer-social">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#"><i class="fa-brands fa-pinterest-p"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-auto col-md-6 text-center text-md-start mb-4 mb-md-0">
                <h6 class="footer-heading" style="color: #86efac; font-family: 'Sora', sans-serif; margin-bottom: 25px; text-transform: uppercase; letter-spacing: 1px; font-size: 0.9rem;">Quick Links</h6>
                <ul class="footer-links">
                    <li><a href="index.php"><i class="fa-solid fa-chevron-right"></i> Home</a></li>
                    <li><a href="shop.php"><i class="fa-solid fa-chevron-right"></i> Shop</a></li>
                    <li><a href="aboutus.php"><i class="fa-solid fa-chevron-right"></i> About Us</a></li>
                    <li><a href="contactus.php"><i class="fa-solid fa-chevron-right"></i> Contact</a></li>
                </ul>
            </div>

            <!-- Categories -->
            <div class="col-lg-auto col-md-6 text-center text-md-start mb-4 mb-md-0">
                <h6 class="footer-heading" style="color: #86efac; font-family: 'Sora', sans-serif; margin-bottom: 25px; text-transform: uppercase; letter-spacing: 1px; font-size: 0.9rem;">Categories</h6>
                <ul class="footer-links">
                    <li><a href="shop.php?category=Indoor"><i class="fa-solid fa-chevron-right"></i> Indoor Plants</a></li>
                    <li><a href="shop.php?category=Outdoor"><i class="fa-solid fa-chevron-right"></i> Outdoor Plants</a></li>
                    <li><a href="shop.php?category=Succulents"><i class="fa-solid fa-chevron-right"></i> Succulents</a></li>
                    <li><a href="shop.php?category=Flowering"><i class="fa-solid fa-chevron-right"></i> Flowering</a></li>
                    <li><a href="shop.php?category=Herbs"><i class="fa-solid fa-chevron-right"></i> Herbs</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-auto col-md-6 text-center text-md-start mb-4 mb-md-0">
                <h6 class="footer-heading" style="color: #86efac; font-family: 'Sora', sans-serif; margin-bottom: 25px; text-transform: uppercase; letter-spacing: 1px; font-size: 0.9rem;">Get in Touch</h6>
                <ul class="footer-links">
                    <li><a href="#"><i class="fa-solid fa-location-dot me-2" style="opacity:1; color:#86efac;"></i> Lahore, Pakistan</a></li>
                    <li><a href="tel:+923001234567"><i class="fa-solid fa-phone me-2" style="opacity:1; color:#86efac;"></i> +92 300 1234567</a></li>
                    <li><a href="mailto:info@florafetch.com"><i class="fa-solid fa-envelope me-2" style="opacity:1; color:#86efac;"></i> info@florafetch.com</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="footer-bottom">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> FloraFetch. All Rights Reserved. | CS-519 Final Project</p>
        </div>
    </div>
</footer>

<!-- Global Toast Container -->
<div class="toast-container position-fixed bottom-0 start-0 p-3" style="z-index: 1060;" id="globalToastContainer"></div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Navbar Scroll Effect & Scroll Animations -->
<script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('mainNavbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            if (!navbar.classList.contains('always-scrolled')) {
                navbar.classList.remove('scrolled');
            }
        }
    });

    // Scroll reveal animation
    const fadeElements = document.querySelectorAll('.fade-in-up');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('visible');
                }, index * 100);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    fadeElements.forEach(el => observer.observe(el));
    
    // Admin View Only Toast
    function showAdminToast() {
        const toastContainer = document.getElementById('adminToastContainer');
        if(!toastContainer) {
            const html = `
            <div id="adminToastContainer" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
                <div id="adminToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body" style="font-weight: 500;">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> Admins cannot purchase items (View Only Mode).
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>`;
            document.body.insertAdjacentHTML('beforeend', html);
        }
        const toastEl = document.getElementById('adminToast');
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
    }

    // Utility function to show toast
    window.showToast = function(message, type = 'success') {
        const toastContainer = document.getElementById('globalToastContainer');
        const icon = type === 'success' ? 'fa-circle-check text-success' : 'fa-triangle-exclamation text-danger';
        const toastId = 'toast-' + Date.now();
        
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center bg-white border-0" role="alert" aria-live="assertive" aria-atomic="true" style="box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 8px;">
                <div class="d-flex">
                    <div class="toast-body" style="font-weight: 500; font-size: 0.95rem;">
                        <i class="fa-solid ${icon} me-2"></i> ${message}
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>`;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        const toastEl = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
        
        // Remove from DOM after hide
        toastEl.addEventListener('hidden.bs.toast', function () {
            toastEl.remove();
        });
    };

    // Notification mark as read
    const markReadBtn = document.querySelector('.mark-read-btn');
    if (markReadBtn) {
        markReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            fetch('mark_notifications_read.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=mark_read'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Remove unread styling
                    document.querySelectorAll('#notifDropdown + .dropdown-menu .bg-light').forEach(el => {
                        el.classList.remove('bg-light');
                        el.classList.add('bg-transparent');
                    });
                    // Hide badge
                    const badge = document.querySelector('#notifDropdown .badge');
                    if (badge) badge.style.display = 'none';
                    // Reset icon color
                    const icon = document.querySelector('#notifDropdown i');
                    if (icon) icon.style.color = 'var(--text-primary)';
                    // Hide mark as read button
                    markReadBtn.style.display = 'none';
                }
            });
        });
    }

    // AJAX Add to Cart
    document.addEventListener('submit', function(e) {
        if(e.target && e.target.tagName === 'FORM' && e.target.getAttribute('action') === 'cart_action.php') {
            const actionInput = e.target.querySelector('input[name="action"]');
            if(actionInput && actionInput.value === 'add') {
                e.preventDefault();
                
                // Show loading state on button
                const btn = e.target.querySelector('button[type="submit"]');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
                btn.disabled = true;

                const formData = new FormData(e.target);
                formData.append('ajax', '1');

                fetch('cart_action.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // Restore button
                        btn.innerHTML = '<i class="fa-solid fa-check"></i>';
                        setTimeout(() => {
                            btn.innerHTML = originalHTML;
                            btn.disabled = false;
                        }, 1500);

                        // Update cart badges
                        let cartLinks = document.querySelectorAll('a[href="cart.php"].cart-glass');
                        cartLinks.forEach(cartLink => {
                            let badge = cartLink.querySelector('.badge');
                            if(!badge) {
                                cartLink.insertAdjacentHTML('beforeend', `<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background: #ef4444; font-size: 0.75rem; padding: 4px 6px; box-shadow: 0 2px 5px rgba(239,68,68,0.4); border: 2px solid var(--bg-card);">${data.cart_count}</span>`);
                            } else {
                                badge.innerText = data.cart_count;
                            }
                        });

                        // Show success toast
                        showToast('Item added to cart!');
                    }
                })
                .catch(err => {
                    console.error('Error adding to cart:', err);
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                    showToast('Error adding item to cart', 'error');
                });
            }
        }
    });

    // Wishlist Toggle
    window.toggleWishlist = function(plantId, element) {
        const icon = element.querySelector('i');
        
        // Toggle UI optimistically
        if (icon.classList.contains('fa-regular')) {
            icon.classList.remove('fa-regular');
            icon.classList.add('fa-solid', 'text-danger');
        } else {
            icon.classList.remove('fa-solid', 'text-danger');
            icon.classList.add('fa-regular');
        }

        // Send AJAX request
        fetch('wishlist_action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=toggle&plant_id=' + plantId
        })
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'success') {
                // Revert if failed
                if (icon.classList.contains('fa-regular')) {
                    icon.classList.remove('fa-regular');
                    icon.classList.add('fa-solid', 'text-danger');
                } else {
                    icon.classList.remove('fa-solid', 'text-danger');
                    icon.classList.add('fa-regular');
                }
                showToast(data.message || 'Error updating wishlist', 'error');
            } else {
                // Update badge
                const badge = document.querySelector('.wishlist-badge-count');
                if (badge) {
                    badge.innerText = data.count;
                    badge.style.display = data.count > 0 ? 'inline-block' : 'none';
                }
                // Show success toast
                const isAdded = icon.classList.contains('fa-solid');
                showToast(isAdded ? 'Added to wishlist!' : 'Removed from wishlist!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Something went wrong', 'error');
        });
    };
</script>

<!-- Floating WhatsApp Button -->
<div class="whatsapp-float-container" style="position: fixed; bottom: 30px; right: 30px; z-index: 1050; display: flex; align-items: flex-end; flex-direction: column; gap: 10px;">
    <!-- Tooltip Message -->
    <div class="whatsapp-tooltip d-none d-md-block" style="background: white; padding: 12px 18px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.15); font-size: 0.95rem; font-weight: 500; color: #111827; position: relative; border-bottom-right-radius: 4px; animation: slideUpFade 0.5s ease forwards; transform-origin: bottom right;">
        Hum premium plants offer karte hain.<br>Aaj hum aapki kya madad kar sakte hain? 😊
        <div style="position: absolute; right: 15px; bottom: -8px; width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-top: 8px solid white;"></div>
    </div>
    <!-- Button -->
    <a href="https://wa.me/923456701973?text=Hi!%20I%20would%20like%20to%20know%20more%20about%20your%20plants." target="_blank" class="whatsapp-btn" style="width: 60px; height: 60px; background: #25d366; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4); transition: transform 0.3s ease, box-shadow 0.3s ease; text-decoration: none;">
        <i class="fa-brands fa-whatsapp"></i>
    </a>
</div>

<style>
    .whatsapp-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(37, 211, 102, 0.6) !important;
        color: white !important;
    }
    @keyframes slideUpFade {
        0% { opacity: 0; transform: translateY(20px) scale(0.9); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }
    @media (max-width: 576px) {
        .whatsapp-float-container {
            bottom: 15px !important;
            right: 15px !important;
        }
        .whatsapp-btn {
            width: 40px !important;
            height: 40px !important;
            font-size: 1.4rem !important;
        }
    }
</style>

</body>
</html>
