import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

// Make Chart available globally
window.Chart = Chart;

// Initialize Alpine.js
window.Alpine = Alpine;

// Dark mode functionality
Alpine.store('darkMode', {
    on: localStorage.getItem('darkMode') === 'true' || 
        (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches),
    
    toggle() {
        this.on = !this.on;
        localStorage.setItem('darkMode', this.on);
        this.updateClass();
    },
    
    updateClass() {
        if (this.on) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },
    
    init() {
        this.updateClass();
    }
});

// Sidebar state
Alpine.store('sidebar', {
    open: localStorage.getItem('sidebarOpen') !== 'false',
    
    toggle() {
        this.open = !this.open;
        localStorage.setItem('sidebarOpen', this.open);
    }
});

Alpine.start();

// Initialize dark mode on page load
document.addEventListener('DOMContentLoaded', () => {
    Alpine.store('darkMode').init();
});