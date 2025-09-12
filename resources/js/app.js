import './bootstrap';
import Alpine from 'alpinejs';

// Make Alpine available globally
window.Alpine = Alpine;

// Alpine.js plugins and magic helpers can be registered here
// Example: Alpine.plugin(pluginName);

// Start Alpine.js
Alpine.start();

// Add dark mode toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    // Ensure dark mode is always active for this theme
    if (!document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.add('dark');
    }

    // Initialize any additional JavaScript functionality here
    initializeAnimations();
    initializeTooltips();
    initializeModals();
});

// Initialize scroll animations
function initializeAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
            }
        });
    }, observerOptions);

    // Observe elements with data-animate attribute
    document.querySelectorAll('[data-animate]').forEach(el => {
        observer.observe(el);
    });
}

// Initialize tooltips
function initializeTooltips() {
    // Tooltip initialization code
}

// Initialize modals
function initializeModals() {
    // Modal initialization code
}

// Cart update notification
window.showNotification = function(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    } text-white`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
};
