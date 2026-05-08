import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Dark mode is now handled by the reusable dark mode toggle component.

// ── Mobile nav ────────────────────────────────────────────────────────────────
Alpine.data('mobileNav', () => ({
    open: false,
    toggle() { this.open = !this.open; },
    close() { this.open = false; },
}));

// ── Admin sidebar (mobile) ────────────────────────────────────────────────────
Alpine.data('adminSidebar', () => ({
    open: false,
    toggle() { this.open = !this.open; },
    close() { this.open = false; },
}));

// ── Scroll reveal ─────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.12 }
    );

    document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));
});

// ── Admin tabs ────────────────────────────────────────────────────────────────
Alpine.data('adminTabs', (defaultTab = 'profile') => ({
    active: defaultTab,
    isActive(tab) { return this.active === tab; },
    setActive(tab) { this.active = tab; },
}));

Alpine.start();
