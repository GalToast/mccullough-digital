document.addEventListener('DOMContentLoaded', function() {
    // --- Mobile Menu Toggle ---
    const menuToggle = document.querySelector('.wp-block-navigation__responsive-container-open');
    const mainNav = document.querySelector('.wp-block-navigation__responsive-container');
    const navBlock = document.querySelector('.wp-block-navigation');

    if (menuToggle && mainNav && navBlock) {
        menuToggle.addEventListener('click', function() {
            // The core block adds 'is-menu-open' to the main nav block
            // We just need to toggle our animation class on the button
            menuToggle.classList.toggle('is-active');
        });
    }

    // --- Hide header on scroll down, reveal on scroll up ---
    const header = document.querySelector('#masthead.site-header');
    let lastY = window.scrollY;

    if (header) {
        window.addEventListener('scroll', function() {
            const y = window.scrollY;
            if (y > 120 && y > lastY) {
                header.classList.add('hide');
            } else {
                header.classList.remove('hide');
            }
            lastY = y;
        }, { passive: true });
    }

    // --- Close mobile menu when a link is clicked ---
    const menuLinks = document.querySelectorAll('.wp-block-navigation-item a');
    menuLinks.forEach(link => {
        link.addEventListener('click', () => {
            // Check if the mobile menu is open
            if (navBlock.classList.contains('is-menu-open')) {
                // We need to simulate a click on the close button
                const menuClose = document.querySelector('.wp-block-navigation__responsive-container-close');
                if(menuClose) {
                    menuClose.click();
                }
                // Also remove our animation class from the toggle
                if(menuToggle) {
                    menuToggle.classList.remove('is-active');
                }
            }
        });
    });

    // --- 3D Logo Tilt Effect ---
    const logoContainer = document.querySelector('.site-branding');
    if (logoContainer) {
        const logoLink = logoContainer.querySelector('.custom-logo-link');
        const maxRotate = 15; // Max rotation in degrees

        logoContainer.addEventListener('mousemove', (e) => {
            const rect = logoContainer.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const { width, height } = rect;

            const rotateY = maxRotate * ((x - width / 2) / (width / 2));
            const rotateX = -maxRotate * ((y - height / 2) / (height / 2));

            logoLink.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
        });

        logoContainer.addEventListener('mouseleave', () => {
            logoLink.style.transform = 'rotateX(0deg) rotateY(0deg)';
        });
    }

    // --- Interactive Hero Headline ---
    const headline = document.getElementById('interactive-headline');
    if(headline) {
        const text = headline.textContent;
        const wrappedText = text.split('').map(char => `<span data-char="${char === ' ' ? '&nbsp;' : char}">${char === ' ' ? '&nbsp;' : char}</span>`).join('');
        headline.innerHTML = wrappedText;
    }

    // --- Twinkling Stars Canvas Animation ---
    const canvas = document.getElementById('particle-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let stars = [];
        let shootingStars = []; // Array for shooting stars
        let frame = 0;

        const setCanvasSize = () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        };

        window.addEventListener('resize', () => {
            setCanvasSize();
            init();
        });

        class Star {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 2 + 0.5; // Stars can be a bit bigger
                this.vy = Math.random() * 0.1 + 0.05; // Slow downward drift
                this.twinkleSpeed = Math.random() * 0.015 + 0.005; // Twinkle faster and more erratically
                this.twinkleOffset = Math.random() * 100;
            }

            update() {
                this.y += this.vy;
                // Reset star if it goes off screen
                if (this.y > canvas.height + this.size) {
                    this.y = -this.size;
                    this.x = Math.random() * canvas.width;
                }
            }

            draw() {
                // Using Math.pow makes the twinkle sharper and more 'dramatic'
                const opacity = Math.pow(Math.abs(Math.sin(this.twinkleOffset + frame * this.twinkleSpeed)), 10);
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2, false);
                const color = Math.random() > 0.1 ? `rgba(0, 229, 255, ${opacity})` : `rgba(255, 0, 224, ${opacity})`;
                ctx.fillStyle = color;
                ctx.fill();
            }
        }

        class ShootingStar {
            constructor() {
                this.reset();
            }

            reset() {
                this.x = Math.random() * canvas.width + 100;
                this.y = - (Math.random() * canvas.height * 0.5);
                this.len = Math.random() * 60 + 20;
                this.speed = Math.random() * 8 + 6;
                this.size = Math.random() * 1.5 + 0.5;
            }

            update() {
                this.x -= this.speed;
                this.y += this.speed * 0.4;
                if (this.x < -this.len || this.y > canvas.height + this.len) {
                    this.reset();
                }
            }

            draw() {
                const grad = ctx.createLinearGradient(this.x, this.y, this.x - this.len, this.y + (this.len * 0.4));
                grad.addColorStop(0, "rgba(255, 255, 255, 0.8)");
                grad.addColorStop(0.5, "rgba(0, 229, 255, 0.6)");
                grad.addColorStop(1, "rgba(0, 229, 255, 0)");

                ctx.strokeStyle = grad;
                ctx.lineWidth = this.size;
                ctx.lineCap = 'round';
                ctx.beginPath();
                ctx.moveTo(this.x, this.y);
                ctx.lineTo(this.x - this.len, this.y + (this.len * 0.4));
                ctx.stroke();
            }
        }


        function init() {
            stars = [];
            shootingStars = [];
            // More stars for a denser field
            let numberOfStars = (canvas.width * canvas.height) / 2500;
            for (let i = 0; i < numberOfStars; i++) {
                stars.push(new Star());
            }
            // Create a few shooting stars
            for (let i = 0; i < 3; i++) {
                shootingStars.push(new ShootingStar());
            }
        }

        function animate() {
            requestAnimationFrame(animate);
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            frame++;

            // Update and draw regular stars
            for (let i = 0; i < stars.length; i++) {
                stars[i].update();
                stars[i].draw();
            }

            // Update and draw shooting stars
            for (let i = 0; i < shootingStars.length; i++) {
                shootingStars[i].update();
                shootingStars[i].draw();
            }
        }

        setCanvasSize();
        init();
        animate();
    }
});