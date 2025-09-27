/**
 * Hero Block-specific JavaScript.
 */
document.addEventListener('DOMContentLoaded', function() {

    // --- Interactive Hero Headline ---
    const escapeTextContent = (char) => {
        switch (char) {
            case '<':
                return '&lt;';
            case '>':
                return '&gt;';
            case '&':
                return '&amp;';
            case '"':
                return '&quot;';
            case '\'':
                return '&#39;';
            case ' ':
                return '&nbsp;';
            default:
                return char;
        }
    };

    const escapeAttributeValue = (char) => {
        switch (char) {
            case '<':
                return '&lt;';
            case '>':
                return '&gt;';
            case '&':
                return '&amp;';
            case '"':
                return '&quot;';
            case '\'':
                return '&#39;';
            case ' ':
                return '&nbsp;';
            default:
                return char;
        }
    };

    const headline = document.getElementById('interactive-headline');
    if (headline) {
        const processNode = (node) => {
            const children = Array.from(node.childNodes);
            for (const child of children) {
                if (child.nodeType === Node.TEXT_NODE) {
                    const text = child.textContent;
                    const fragment = document.createDocumentFragment();
                    for (const char of text) {
                        const span = document.createElement('span');
                        const escapedText = escapeTextContent(char);
                        const escapedAttr = escapeAttributeValue(char);
                        span.dataset.char = escapedAttr;
                        span.innerHTML = escapedText;
                        fragment.appendChild(span);
                    }
                    child.parentNode.replaceChild(fragment, child);
                } else if (child.nodeType === Node.ELEMENT_NODE) {
                    processNode(child);
                }
            }
        };
        processNode(headline);
    }

    // --- Twinkling Stars Canvas Animation ---
    const canvas = document.getElementById('particle-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let stars = [];
        let shootingStars = []; // Array for shooting stars
        let frame = 0;

        const setCanvasSize = () => {
            // Set canvas size based on its parent .hero element
            const heroSection = canvas.closest('.hero');
            if (heroSection) {
                canvas.width = heroSection.offsetWidth;
                canvas.height = heroSection.offsetHeight;
            } else {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            }
        };

        window.addEventListener('resize', () => {
            setCanvasSize();
            init();
        });

        class Star {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 2 + 0.5;
                this.vy = Math.random() * 0.1 + 0.05;
                this.twinkleSpeed = Math.random() * 0.015 + 0.005;
                this.twinkleOffset = Math.random() * 100;
            }

            update() {
                this.y += this.vy;
                if (this.y > canvas.height + this.size) {
                    this.y = -this.size;
                    this.x = Math.random() * canvas.width;
                }
            }

            draw() {
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
            let numberOfStars = (canvas.width * canvas.height) / 2500;
            for (let i = 0; i < numberOfStars; i++) {
                stars.push(new Star());
            }
            for (let i = 0; i < 3; i++) {
                shootingStars.push(new ShootingStar());
            }
        }

        function animate() {
            requestAnimationFrame(animate);
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            frame++;

            for (let i = 0; i < stars.length; i++) {
                stars[i].update();
                stars[i].draw();
            }

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