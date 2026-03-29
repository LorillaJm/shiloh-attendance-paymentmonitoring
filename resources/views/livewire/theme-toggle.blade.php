<div>
    <div x-data="{ 
        theme: @entangle('theme'),
        isAnimating: false 
    }" 
    x-init="
        $watch('theme', value => {
            isAnimating = true;
            setTimeout(() => isAnimating = false, 600);
        })
    ">
        <button 
            @click="$wire.toggle()" 
            type="button"
            class="modern-theme-toggle"
            :class="{ 'animating': isAnimating }"
            aria-label="Toggle theme"
            :title="theme === 'light' ? 'Switch to dark mode' : 'Switch to light mode'"
        >
            <div class="toggle-track">
                <div class="toggle-thumb" :class="theme === 'dark' ? 'translate-x-5' : 'translate-x-0'">
                    <div class="icon-container">
                        <!-- Sun icon -->
                        <svg 
                            class="icon sun-icon" 
                            :class="theme === 'light' ? 'active' : ''"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        
                        <!-- Moon icon -->
                        <svg 
                            class="icon moon-icon" 
                            :class="theme === 'dark' ? 'active' : ''"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </div>
                </div>
                
                <!-- Background decorations -->
                <div class="toggle-bg">
                    <div class="stars" :class="theme === 'dark' ? 'visible' : ''">
                        <span class="star" style="left: 15%; top: 20%;"></span>
                        <span class="star" style="left: 70%; top: 30%;"></span>
                        <span class="star" style="left: 40%; top: 60%;"></span>
                    </div>
                    <div class="clouds" :class="theme === 'light' ? 'visible' : ''">
                        <span class="cloud" style="left: 20%; top: 25%;"></span>
                        <span class="cloud" style="left: 60%; top: 50%;"></span>
                    </div>
                </div>
            </div>
        </button>
    </div>

    <style>
    .modern-theme-toggle {
        position: relative;
        padding: 0.25rem;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .modern-theme-toggle:hover {
        transform: scale(1.05);
    }

    .modern-theme-toggle:active {
        transform: scale(0.95);
    }

    .toggle-track {
        position: relative;
        width: 3.5rem;
        height: 1.75rem;
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        border-radius: 9999px;
        overflow: hidden;
        box-shadow: 
            inset 0 2px 4px rgba(0, 0, 0, 0.1),
            0 2px 8px rgba(0, 0, 0, 0.05);
        transition: background 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .dark .toggle-track {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        box-shadow: 
            inset 0 2px 4px rgba(0, 0, 0, 0.3),
            0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .toggle-thumb {
        position: absolute;
        top: 0.125rem;
        left: 0.125rem;
        width: 1.5rem;
        height: 1.5rem;
        background: white;
        border-radius: 9999px;
        box-shadow: 
            0 2px 8px rgba(0, 0, 0, 0.15),
            0 1px 3px rgba(0, 0, 0, 0.1);
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 10;
    }

    .dark .toggle-thumb {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    }

    .icon-container {
        position: relative;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon {
        position: absolute;
        width: 1rem;
        height: 1rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 0;
        transform: scale(0) rotate(-180deg);
    }

    .icon.active {
        opacity: 1;
        transform: scale(1) rotate(0deg);
    }

    .sun-icon {
        color: #f59e0b;
    }

    .moon-icon {
        color: #6366f1;
    }

    .toggle-bg {
        position: absolute;
        inset: 0;
        pointer-events: none;
    }

    .stars,
    .clouds {
        position: absolute;
        inset: 0;
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .stars.visible,
    .clouds.visible {
        opacity: 1;
    }

    .star {
        position: absolute;
        width: 2px;
        height: 2px;
        background: white;
        border-radius: 50%;
        animation: twinkle 2s ease-in-out infinite;
    }

    .star:nth-child(2) {
        animation-delay: 0.5s;
    }

    .star:nth-child(3) {
        animation-delay: 1s;
    }

    @keyframes twinkle {
        0%, 100% { opacity: 0.3; transform: scale(1); }
        50% { opacity: 1; transform: scale(1.5); }
    }

    .cloud {
        position: absolute;
        width: 8px;
        height: 3px;
        background: rgba(255, 255, 255, 0.6);
        border-radius: 9999px;
        animation: float 3s ease-in-out infinite;
    }

    .cloud:nth-child(2) {
        animation-delay: 1.5s;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-2px); }
    }

    /* Ripple effect on click */
    .modern-theme-toggle.animating::after {
        content: '';
        position: absolute;
        inset: -0.5rem;
        border-radius: 9999px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, transparent 70%);
        animation: ripple 0.6s ease-out;
    }

    @keyframes ripple {
        0% {
            transform: scale(0.8);
            opacity: 1;
        }
        100% {
            transform: scale(1.5);
            opacity: 0;
        }
    }

    /* Responsive */
    @media (max-width: 640px) {
        .toggle-track {
            width: 3rem;
            height: 1.5rem;
        }
        
        .toggle-thumb {
            width: 1.25rem;
            height: 1.25rem;
        }
        
        .icon {
            width: 0.875rem;
            height: 0.875rem;
        }
    }
    </style>
</div>
