/**
 * Backstreet Boys Website - Redesigned JavaScript
 * Enhanced animations, effects, and interactivity
 * Theme: 90s Nostalgia × Y2K Chrome × Millennium Luxury
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // LOADING SCREEN WITH ANIMATION
    // ==========================================
    const loadingScreen = document.createElement('div');
    loadingScreen.className = 'loading';
    loadingScreen.innerHTML = `
        <div class="loader"></div>
    `;
    document.body.appendChild(loadingScreen);
    
    window.addEventListener('load', () => {
        setTimeout(() => {
            loadingScreen.classList.add('hidden');
            setTimeout(() => {
                loadingScreen.remove();
            }, 500);
        }, 1000);
    });
    
    // ==========================================
    // CREATE BACKGROUND EFFECTS
    // ==========================================
    
    // Star Field Background
    const starField = document.createElement('div');
    starField.className = 'star-field';
    document.body.insertBefore(starField, document.body.firstChild);
    
    // Digital Grid Overlay
    const digitalGrid = document.createElement('div');
    digitalGrid.className = 'digital-grid';
    document.body.insertBefore(digitalGrid, document.body.firstChild);
    
    // Floating Particles
    const particlesContainer = document.createElement('div');
    particlesContainer.className = 'particles-container';
    for (let i = 0; i < 9; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particlesContainer.appendChild(particle);
    }
    document.body.insertBefore(particlesContainer, document.body.firstChild);
    
    // Glow Orbs
    const glowOrb1 = document.createElement('div');
    glowOrb1.className = 'glow-orb glow-orb-1';
    document.body.insertBefore(glowOrb1, document.body.firstChild);
    
    const glowOrb2 = document.createElement('div');
    glowOrb2.className = 'glow-orb glow-orb-2';
    document.body.insertBefore(glowOrb2, document.body.firstChild);
    
    // ==========================================
    // MOBILE MENU TOGGLE
    // ==========================================
    const menuToggle = document.querySelector('.menu-toggle') || document.createElement('div');
    
    if (!document.querySelector('.menu-toggle')) {
        menuToggle.className = 'menu-toggle';
        menuToggle.innerHTML = '<span></span><span></span><span></span>';
        
        const header = document.querySelector('.header');
        if (header) {
            const navContainer = header.querySelector('.navigators');
            if (navContainer) {
                navContainer.insertBefore(menuToggle, navContainer.firstChild);
            }
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
                    entry.target.classList.contains('preview-card') ||
                    entry.target.classList.contains('timeline-item')) {
                    const index = Array.from(entry.target.parentNode.children).indexOf(entry.target);
                    entry.target.style.setProperty('--card-index', index);
                }
            }
        });
    }, observerOptions);
    
    // Observe elements for scroll animations
    const animateElements = document.querySelectorAll(
        '.preview-card, .band_card, .album_card, .timeline-item, ' +
        '.group_card h1, .group_info, .topSongs_text h1, ' +
        '.welcome-container, .members-title, .history-header'
    );
    
    animateElements.forEach(el => {
        el.classList.add('animate-on-scroll');
        observer.observe(el);
    });
    
    // ==========================================
    // BACK TO TOP BUTTON
    // ==========================================
    const backToTop = document.querySelector('.back-to-top') || document.createElement('div');
    
    if (!document.querySelector('.back-to-top')) {
        backToTop.className = 'back-to-top';
        document.body.appendChild(backToTop);
    }
    
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
    const heroBanner = document.querySelector('.hero-retro-frame img');
    
    if (heroBanner) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            if (scrolled < 650) {
                heroBanner.style.transform = `translateY(${scrolled * 0.3}px)`;
            }
        });
    }
    
    // ==========================================
    // CARD HOVER ENHANCEMENTS
    // ==========================================
    const cards = document.querySelectorAll('.band_card, .album_card, .preview-card, .timeline-content');
    
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
    const timelineDots = document.querySelectorAll('.timeline-dot');
    
    timelineDots.forEach((dot, index) => {
        dot.style.animationDelay = `${index * 0.2}s`;
    });
    
    // ==========================================
    // ALBUM CARD TRACK COUNTER
    // ==========================================
    document.querySelectorAll('.album_card').forEach(album => {
        const tracks = album.querySelectorAll('.tracklist p');
        if (tracks.length > 0 && !album.querySelector('.track-count')) {
            const count = document.createElement('span');
            count.className = 'track-count';
            count.style.cssText = `
                display: block;
                padding: 0 1.2rem 1.2rem;
                font-size: 0.75rem;
                color: var(--gold);
                opacity: 0.8;
                font-family: var(--font-tech);
            `;
            count.textContent = `${tracks.length} tracks shown`;
            album.querySelector('.tracklist')?.appendChild(count);
        }
    });
    
    // ==========================================
    // CONSOLE EASTER EGG
    // ==========================================
    console.log('%c🎵 Welcome to the Backstreet Boys Fan Page! 🎵', 
        'font-size: 20px; color: #00b4ff; font-weight: bold; text-shadow: 0 0 10px rgba(0, 180, 255, 0.5);');
    console.log('%c"Tell me why!" - Ain\'t nothin\' but a mistake...', 
        'font-size: 14px; color: #9333ea; font-style: italic;');
    console.log('%cExperience the 90s nostalgia meets Y2K chrome aesthetic!', 
        'font-size: 12px; color: #d4af37;');
    
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
            document.body.classList.add('page-transitioning');
            
            setTimeout(() => {
                window.location.href = target;
            }, 300);
        });
    });
    
    // Fade in on page load
    setTimeout(() => {
        document.body.style.opacity = '1';
    }, 100);
    
    // ==========================================
    // RESIZE HANDLER
    // ==========================================
    let resizeTimer;
    
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            document.body.classList.remove('resizing');
        }, 250);
        
        document.body.classList.add('resizing');
    });
    
    // ==========================================
    // SPOTLIGHT EFFECT ON CARDS
    // ==========================================
    cards.forEach(card => {
        card.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            this.style.setProperty('--spotlight-x', `${x}px`);
            this.style.setProperty('--spotlight-y', `${y}px`);
        });
    });
    
    // ==========================================
    // MEMBER INITIAL CLICK EFFECT
    // ==========================================
    document.querySelectorAll('.member-initial').forEach(initial => {
        initial.addEventListener('click', function() {
            this.style.animation = 'pulse 0.5s ease';
            setTimeout(() => {
                this.style.animation = '';
            }, 500);
        });
    });
    
    // ==========================================
    // DYNAMIC YEAR UPDATE
    // ==========================================
    const updateCopyrightYear = () => {
        const copyrightElements = document.querySelectorAll('.copyright');
        const currentYear = new Date().getFullYear();
        
        copyrightElements.forEach(el => {
            if (el.textContent.includes('©')) {
                el.textContent = el.textContent.replace(/© \d{4}/, `© ${currentYear}`);
            }
        });
    };
    
    updateCopyrightYear();
    
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

// Create floating music notes animation
function createMusicNotes() {
    const musicSection = document.querySelector('.music_Home');
    if (!musicSection) return;
    
    for (let i = 0; i < 5; i++) {
        const note = document.createElement('div');
        note.innerHTML = '♪';
        note.style.cssText = `
            position: absolute;
            font-size: ${Math.random() * 20 + 20}px;
            color: var(--electric-blue);
            opacity: 0.1;
            pointer-events: none;
            animation: music-float ${Math.random() * 3 + 3}s ease-in-out infinite;
            animation-delay: ${i * 0.5}s;
            left: ${Math.random() * 100}%;
            top: ${Math.random() * 100 + 50}%;
        `;
        musicSection.appendChild(note);
    }
}

// Initialize music notes after DOM is ready
setTimeout(createMusicNotes, 1500);
