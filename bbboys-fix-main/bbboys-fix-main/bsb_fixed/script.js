/**
 * Backstreet Boys Website - Main JavaScript
 * Enhanced interactivity and animations
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // LOADING SCREEN
    // ==========================================
    const loadingScreen = document.createElement('div');
    loadingScreen.className = 'loading';
    loadingScreen.innerHTML = '<div class="loader"></div>';
    document.body.appendChild(loadingScreen);
    
    window.addEventListener('load', () => {
        setTimeout(() => {
            loadingScreen.classList.add('hidden');
            setTimeout(() => {
                loadingScreen.remove();
            }, 500);
        }, 800);
    });
    
    // ==========================================
    // MOBILE MENU TOGGLE
    // ==========================================
    const menuToggle = document.createElement('div');
    menuToggle.className = 'menu-toggle';
    menuToggle.innerHTML = '<span></span><span></span><span></span>';
    
    const header = document.querySelector('.header');
    if (header) {
        const navContainer = header.querySelector('.navigators');
        if (navContainer) {
            navContainer.insertBefore(menuToggle, navContainer.firstChild);
        }
    }
    
    menuToggle.addEventListener('click', () => {
        menuToggle.classList.toggle('active');
        const nav = document.querySelector('.navigators nav');
        if (nav) {
            nav.classList.toggle('active');
        }
    });
    
    // Close mobile menu when clicking on a link
    document.querySelectorAll('.navigators nav a').forEach(link => {
        link.addEventListener('click', () => {
            menuToggle.classList.remove('active');
            const nav = document.querySelector('.navigators nav');
            if (nav) {
                nav.classList.remove('active');
            }
        });
    });
    
    // ==========================================
    // HEADER SCROLL EFFECT
    // ==========================================
    const headerEl = document.querySelector('.header');
    let lastScroll = 0;
    
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 100) {
            headerEl.classList.add('scrolled');
        } else {
            headerEl.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    });
    
    // ==========================================
    // ACTIVE NAVIGATION HIGHLIGHTING
    // ==========================================
    const currentPage = window.location.pathname.split('/').pop() || 'home.html';
    
    document.querySelectorAll('.navigators nav a').forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPage) {
            link.classList.add('active');
        }
    });
    
    // ==========================================
    // SCROLL ANIMATIONS (INTERSECTION OBSERVER)
    // ==========================================
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                
                // Stagger animation for cards
                if (entry.target.classList.contains('band_card') || 
                    entry.target.classList.contains('album_card') ||
                    entry.target.classList.contains('preview-card')) {
                    const index = Array.from(entry.target.parentNode.children).indexOf(entry.target);
                    entry.target.style.transitionDelay = `${index * 0.1}s`;
                }
            }
        });
    }, observerOptions);
    
    // Observe elements for scroll animations
    const animateElements = document.querySelectorAll(
        '.preview-card, .band_card, .album_card, [class$="_card"], ' +
        '.group_card h1, .group_info, .topSongs_text h1'
    );
    
    animateElements.forEach(el => {
        el.classList.add('animate-on-scroll');
        observer.observe(el);
    });
    
    // ==========================================
    // BACK TO TOP BUTTON
    // ==========================================
    const backToTop = document.createElement('div');
    backToTop.className = 'back-to-top';
    document.body.appendChild(backToTop);
    
    backToTop.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            backToTop.classList.add('visible');
        } else {
            backToTop.classList.remove('visible');
        }
    });
    
    // ==========================================
    // SMOOTH SCROLL FOR INTERNAL LINKS
    // ==========================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                const offsetTop = targetElement.offsetTop - 80;
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // ==========================================
    // PARALLAX EFFECT FOR HERO BANNER
    // ==========================================
    const heroBanner = document.querySelector('.hero_banner img');
    
    if (heroBanner) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            if (scrolled < 620) {
                heroBanner.style.transform = `translateY(${scrolled * 0.3}px)`;
            }
        });
    }
    
    // ==========================================
    // CARD HOVER ENHANCEMENTS
    // ==========================================
    const cards = document.querySelectorAll('.band_card, .album_card, .preview-card, [class$="_card"]');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.zIndex = '10';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.zIndex = '1';
        });
    });
    
    // ==========================================
    // MUSIC LINKS SHINE EFFECT
    // ==========================================
    const musicLinks = document.querySelectorAll('.music_container a');
    
    musicLinks.forEach(link => {
        link.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            this.style.setProperty('--mouse-x', `${x}px`);
            this.style.setProperty('--mouse-y', `${y}px`);
        });
    });
    
    // ==========================================
    // SOCIAL ICONS ANIMATION
    // ==========================================
    const socialIcons = document.querySelectorAll('.CallToAction a');
    
    socialIcons.forEach((icon, index) => {
        icon.addEventListener('mouseenter', function() {
            this.style.animation = `float 0.6s ease-in-out ${index * 0.1}s`;
        });
        
        icon.addEventListener('mouseleave', function() {
            this.style.animation = '';
        });
    });
    
    // ==========================================
    // TIMELINE DOT PULSE ANIMATION
    // ==========================================
    const timelineCards = document.querySelectorAll('[class$="_card"]');
    
    timelineCards.forEach((card, index) => {
        const dot = card.querySelector('::before');
        if (index % 2 === 0) {
            card.style.animationDelay = `${index * 0.2}s`;
        }
    });
    
    // ==========================================
    // TEXT REVEAL ANIMATION
    // ==========================================
    const revealText = (element) => {
        const text = element.textContent;
        element.textContent = '';
        let i = 0;
        
        const typeWriter = () => {
            if (i < text.length) {
                element.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 30);
            }
        };
        
        typeWriter();
    };
    
    // Apply to band member names on hover
    document.querySelectorAll('.band_name').forEach(name => {
        name.addEventListener('mouseenter', function() {
            const originalText = this.textContent;
            this.style.cursor = 'pointer';
        });
    });
    
    // ==========================================
    // ALBUM CARD TRACK COUNTER
    // ==========================================
    document.querySelectorAll('.album_card').forEach(album => {
        const tracks = album.querySelectorAll('p');
        if (tracks.length > 0) {
            const count = document.createElement('span');
            count.style.cssText = `
                display: block;
                padding: 0 1rem 1rem;
                font-size: 0.75rem;
                color: var(--gold);
                opacity: 0.8;
            `;
            count.textContent = `${tracks.length} tracks shown`;
            album.appendChild(count);
        }
    });
    
    // ==========================================
    // PERFORMANCE OPTIMIZATION
    // ==========================================
    let ticking = false;
    
    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                ticking = false;
            });
            ticking = true;
        }
    });
    
    // ==========================================
    // KEYBOARD NAVIGATION
    // ==========================================
    document.addEventListener('keydown', (e) => {
        // Press 'Home' to go to top
        if (e.key === 'Home') {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
        
        // Press 'End' to go to bottom
        if (e.key === 'End') {
            e.preventDefault();
            window.scrollTo({
                top: document.body.scrollHeight,
                behavior: 'smooth'
            });
        }
    });
    
    // ==========================================
    // IMAGE LAZY LOADING
    // ==========================================
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.add('loaded');
                        imageObserver.unobserve(img);
                    }
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // ==========================================
    // CONSOLE EASTER EGG
    // ==========================================
    console.log('%c🎵 Welcome to the Backstreet Boys Fan Page! 🎵', 
        'font-size: 20px; color: #c9a84c; font-weight: bold;');
    console.log('%c"Tell me why!" - Ain\'t nothin\' but a mistake...', 
        'font-size: 14px; color: #e8c96e;');
    
    // ==========================================
    // PAGE TRANSITION EFFECT
    // ==========================================
    const links = document.querySelectorAll('a[href$=".html"]');
    
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            const target = this.getAttribute('href');
            
            // Don't prevent default for external links
            if (target.startsWith('http')) return;
            
            // Add fade out effect
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.3s ease';
            
            setTimeout(() => {
                window.location.href = target;
            }, 300);
        });
    });
    
    // Fade in on page load
    document.body.style.opacity = '1';
    document.body.style.transition = 'opacity 0.5s ease';
    
    // ==========================================
    // RESIZE HANDLER
    // ==========================================
    let resizeTimer;
    
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            // Recalculate any layout-dependent features
            document.body.classList.remove('resizing');
        }, 250);
        
        document.body.classList.add('resizing');
    });
    
});

// ==========================================
// UTILITY FUNCTIONS
// ==========================================

// Debounce function for performance
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle function for scroll events
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Check if element is in viewport
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

// Add dynamic year to copyright
const updateCopyrightYear = () => {
    const copyrightElements = document.querySelectorAll('footer p');
    const currentYear = new Date().getFullYear();
    
    copyrightElements.forEach(el => {
        if (el.textContent.includes('©')) {
            el.textContent = el.textContent.replace(/© \d{4}/, `© ${currentYear}`);
        }
    });
};

// Run on initialization
updateCopyrightYear();
