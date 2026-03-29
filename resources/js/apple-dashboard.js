/**
 * Apple-Inspired Dashboard Animations
 * Minimal, smooth, premium interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // 1. SMOOTH NUMBER COUNTER ANIMATION
    // ============================================
    function animateValue(element, start, end, duration) {
        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                current = end;
                clearInterval(timer);
            }
            
            // Format based on content
            if (element.textContent.includes('₱')) {
                element.textContent = '₱' + current.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            } else {
                element.textContent = Math.floor(current).toLocaleString();
            }
        }, 16);
    }
    
    // ============================================
    // 2. INTERSECTION OBSERVER FOR FADE-IN
    // ============================================
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const fadeInObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('animate-fade-in-up');
                    entry.target.style.opacity = '1';
                    
                    // Animate numbers
                    const valueElement = entry.target.querySelector('.apple-stat-value, .fi-wi-stats-overview-stat-value');
                    if (valueElement) {
                        const text = valueElement.textContent.replace(/[₱,]/g, '');
                        const value = parseFloat(text);
                        if (!isNaN(value)) {
                            valueElement.textContent = valueElement.textContent.includes('₱') ? '₱0.00' : '0';
                            animateValue(valueElement, 0, value, 1000);
                        }
                    }
                }, index * 100);
                
                fadeInObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe all stat cards
    document.querySelectorAll('.apple-stat-card, .fi-wi-stats-overview-stat').forEach(card => {
        card.style.opacity = '0';
        fadeInObserver.observe(card);
    });
    
    // ============================================
    // 3. PARALLAX SCROLL EFFECT
    // ============================================
    let ticking = false;
    
    function updateParallax() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.apple-hero-header');
        
        parallaxElements.forEach(element => {
            const speed = 0.5;
            element.style.transform = `translateY(${scrolled * speed}px)`;
        });
        
        ticking = false;
    }
    
    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(updateParallax);
            ticking = true;
        }
    });
    
    // ============================================
    // 4. CARD HOVER GLOW EFFECT
    // ============================================
    document.querySelectorAll('.apple-stat-card, .fi-wi-stats-overview-stat').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            card.style.setProperty('--mouse-x', `${x}px`);
            card.style.setProperty('--mouse-y', `${y}px`);
        });
    });
    
    // ============================================
    // 5. SMOOTH SCROLL TO SECTIONS
    // ============================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // ============================================
    // 6. LOADING SHIMMER EFFECT
    // ============================================
    function showLoadingShimmer() {
        document.querySelectorAll('.apple-stat-card, .fi-wi-stats-overview-stat').forEach(card => {
            card.classList.add('shimmer-loading');
        });
    }
    
    function hideLoadingShimmer() {
        document.querySelectorAll('.apple-stat-card, .fi-wi-stats-overview-stat').forEach(card => {
            card.classList.remove('shimmer-loading');
        });
    }
    
    // Listen for Livewire updates
    if (typeof Livewire !== 'undefined') {
        Livewire.hook('message.sent', () => {
            showLoadingShimmer();
        });
        
        Livewire.hook('message.processed', () => {
            hideLoadingShimmer();
        });
    }
    
    // ============================================
    // 7. CARD TILT EFFECT (SUBTLE)
    // ============================================
    document.querySelectorAll('.apple-stat-card, .fi-wi-stats-overview-stat').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;
            
            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-4px) scale(1.02)`;
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });
    
    // ============================================
    // 8. REFRESH BUTTON ANIMATION
    // ============================================
    document.querySelectorAll('[data-action="refresh"]').forEach(button => {
        button.addEventListener('click', function() {
            const icon = this.querySelector('svg');
            if (icon) {
                icon.style.animation = 'spin 0.6s ease-in-out';
                setTimeout(() => {
                    icon.style.animation = '';
                }, 600);
            }
        });
    });
    
    // ============================================
    // 9. GRADIENT TEXT ANIMATION
    // ============================================
    const gradientTexts = document.querySelectorAll('.apple-hero-title');
    gradientTexts.forEach(text => {
        let hue = 0;
        setInterval(() => {
            hue = (hue + 1) % 360;
            text.style.filter = `hue-rotate(${hue}deg)`;
        }, 50);
    });
    
    // ============================================
    // 10. PERFORMANCE OPTIMIZATION
    // ============================================
    
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
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
    
    // ============================================
    // 11. ACCESSIBILITY ENHANCEMENTS
    // ============================================
    
    // Respect prefers-reduced-motion
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    
    if (prefersReducedMotion.matches) {
        document.documentElement.style.setProperty('--transition-fast', '0s');
        document.documentElement.style.setProperty('--transition-base', '0s');
        document.documentElement.style.setProperty('--transition-slow', '0s');
    }
    
    // ============================================
    // 12. CONSOLE SIGNATURE
    // ============================================
    console.log('%c🍎 Apple-Inspired Dashboard', 'font-size: 20px; font-weight: bold; color: #0071e3;');
    console.log('%cDesigned with ❤️ for premium experience', 'font-size: 12px; color: #6e6e73;');
});

// ============================================
// UTILITY FUNCTIONS
// ============================================

// Format currency
function formatCurrency(value) {
    return '₱' + value.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Format number
function formatNumber(value) {
    return value.toLocaleString('en-US');
}

// Add spin animation to CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
