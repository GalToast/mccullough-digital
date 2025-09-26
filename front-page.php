<?php
/**
 * The template for displaying the front page.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package McCullough_Digital
 */

get_header();
?>

<!-- Hero Section -->
<section class="hero">
    <canvas id="particle-canvas"></canvas> <!-- This was the missing element -->
    <div class="hero-content">
        <h1 id="interactive-headline">Bringing Your Digital Vision to Life.</h1>
        <p>We build beautiful, high-performance web experiences and creative marketing strategies that connect with your audience. Ready to create something amazing?</p>
        <a href="#contact" class="cta-button"><span class="btn-text">Start a Project</span></a>
    </div>
</section>

<!-- Services Section -->
<section id="services">
    <div class="container">
        <h2 class="section-title">What We Do</h2>
        <div class="services-grid">
            <div class="service-card">
               <div class="service-card-content">
                    <div>
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                        </div>
                        <h3>Complete Web Solutions</h3>
                        <p>We design, build, and host SEO-optimized websites. Every project includes analytics integration and ongoing monitoring to ensure your success.</p>
                    </div>
                    <a href="#" class="learn-more">Explore Our Process →</a>
                </div>
            </div>
            <div class="service-card">
                <div class="service-card-content">
                    <div>
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                        </div>
                        <h3>Targeted Digital Advertising</h3>
                        <p>We manage your advertising campaigns across platforms like Google, Meta, and Nextdoor to reach your ideal customers and maximize your ROI.</p>
                    </div>
                    <a href="#" class="learn-more">Discover Ad Management →</a>
                </div>
            </div>
            <div class="service-card">
                <div class="service-card-content">
                    <div>
                        <div class="icon">
                             <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.42 4.58a5.4 5.4 0 0 0-7.65 0l-.71.71a5.4 5.4 0 0 0 0 7.65l.71.71a5.4 5.4 0 0 0 7.65 0l4.24-4.24a5.4 5.4 0 0 0 0-7.65l-4.24-4.24z"></path><path d="M10.1 19.24a5.4 5.4 0 0 0 7.65 0l.71-.71a5.4 5.4 0 0 0 0-7.65l-.71-.71a5.4 5.4 0 0 0-7.65 0l-1.42 1.42"></path></svg>
                        </div>
                        <h3>Integrated Social Media</h3>
                        <p>From profile setup to full-service management, we handle your social media presence, integrating it all so you can focus on your business.</p>
                    </div>
                    <a href="#" class="learn-more">See Management Plans →</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Us Section -->
<section id="about">
    <div class="container">
        <h2 class="section-title">About Us</h2>
        <p>McCullough Digital is a small team of developers, designers, and strategists passionate about building beautiful things for the web. We thrive on collaboration and exist to create digital tools that help our clients succeed. We believe in clean code, thoughtful design, and a relentless pursuit of quality.</p>
    </div>
</section>

<!-- Final CTA Section -->
<section id="contact" class="cta-section">
     <div class="container">
        <h2 class="section-title">Ready to Create?</h2>
        <a href="mailto:contact@mccullough.digital" class="cta-button"><span class="btn-text">Let's Talk</span></a>
    </div>
</section>

<?php
get_footer();