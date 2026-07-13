<?php
include("includes/head.php");
include("includes/header.php");
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
        <h1>Contact Us</h1>
        <p>Have a question or need help? We'd love to hear from you.</p>
    </div>
</div>

<main style="padding: 60px 0 80px;">
    <div class="container">
        
        <!-- Contact Info Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="contact-info-card">
                    <div class="icon-wrap"><i class="fa-solid fa-location-dot"></i></div>
                    <h6>Visit Us</h6>
                    <p>Johar Town, Lahore<br>Punjab, Pakistan</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-info-card">
                    <div class="icon-wrap"><i class="fa-solid fa-phone"></i></div>
                    <h6>Call Us</h6>
                    <p>+92 300 1234567<br>Mon - Sat, 9am - 6pm</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-info-card">
                    <div class="icon-wrap"><i class="fa-solid fa-envelope"></i></div>
                    <h6>Email Us</h6>
                    <p>info@florafetch.com<br>support@florafetch.com</p>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="flora-form-card">
                    <h4 style="font-family: var(--font-heading); font-weight: 700; margin-bottom: 8px;">Send us a Message</h4>
                    <p class="text-secondary mb-4" style="font-size: 0.9rem;">Fill out the form below and we'll get back to you within 24 hours.</p>
                    
                    <form id="contactForm" onsubmit="sendToWhatsApp(event)">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.84rem; color: var(--text-secondary); font-weight: 500;">Full Name</label>
                                <input type="text" class="form-control" name="name" placeholder="Your name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.84rem; color: var(--text-secondary); font-weight: 500;">Email Address</label>
                                <input type="email" class="form-control" name="email" placeholder="you@example.com" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" style="font-size: 0.84rem; color: var(--text-secondary); font-weight: 500;">Subject</label>
                                <input type="text" class="form-control" name="subject" placeholder="How can we help?" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" style="font-size: 0.84rem; color: var(--text-secondary); font-weight: 500;">Message</label>
                                <textarea class="form-control" name="message" rows="5" placeholder="Write your message here..." required></textarea>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-flora px-4">
                                    <i class="fa-solid fa-paper-plane me-2"></i>Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Map Section -->
<section style="padding: 0;">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d108888.29891876748!2d74.23287399999999!3d31.4503777!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3919040e39c8e0c7%3A0x3b0f3e6c6f4bbc56!2sLahore%2C%20Punjab%2C%20Pakistan!5e0!3m2!1sen!2s!4v1689765000000!5m2!1sen!2s" width="100%" height="350" style="border:0; display: block;" allowfullscreen="" loading="lazy"></iframe>
</section>

<script>
function sendToWhatsApp(e) {
    e.preventDefault();
    const form = document.getElementById('contactForm');
    const name = form.name.value;
    const email = form.email.value;
    const subject = form.subject.value;
    const message = form.message.value;
    
    let waText = `*New Contact Inquiry*\n\n`;
    waText += `*Name:* ${name}\n`;
    waText += `*Email:* ${email}\n`;
    waText += `*Subject:* ${subject}\n\n`;
    waText += `*Message:* \n${message}`;
    
    const waUrl = `https://wa.me/923456701973?text=${encodeURIComponent(waText)}`;
    window.open(waUrl, '_blank');
    
    // Optional: reset form after sending
    form.reset();
}
</script>

<?php include("includes/footer.php"); ?>
