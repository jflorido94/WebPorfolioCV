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

// ── Post editor (Markdown + subida de imagenes) ──────────────────────────────
Alpine.data('postEditor', (initialContent = '', uploadUrl = '/admin/posts/upload-image') => ({
    content: initialContent,
    preview: '',
    showPreview: false,
    loading: false,
    uploadingImage: false,

    renderPreview() {
        this.preview = window.marked ? marked.parse(this.content) : this.content;
    },

    openImagePicker() {
        this.$refs.imageInput.click();
    },

    onImageInputChange(event) {
        const file = event.target.files[0];
        event.target.value = '';
        this.uploadImage(file);
    },

    onDrop(event) {
        this.uploadImage(event.dataTransfer.files[0]);
    },

    onPaste(event) {
        const item = Array.from(event.clipboardData?.items || []).find((i) => i.type.startsWith('image/'));
        if (!item) return;
        event.preventDefault();
        this.uploadImage(item.getAsFile());
    },

    async uploadImage(file) {
        if (!file || !file.type.startsWith('image/')) return;

        this.uploadingImage = true;

        const formData = new FormData();
        formData.append('image', file);

        try {
            const response = await fetch(uploadUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    Accept: 'application/json',
                },
                body: formData,
            });

            if (!response.ok) {
                throw new Error('upload failed');
            }

            const data = await response.json();
            this.insertAtCursor(`![${file.name}](${data.url})\n`);
        } catch (error) {
            alert('No se pudo subir la imagen. Intentalo de nuevo.');
        } finally {
            this.uploadingImage = false;
        }
    },

    insertAtCursor(text) {
        const el = this.$refs.contentInput;
        const start = el.selectionStart;
        const end = el.selectionEnd;

        this.content = this.content.slice(0, start) + text + this.content.slice(end);

        this.$nextTick(() => {
            el.focus();
            el.selectionStart = el.selectionEnd = start + text.length;
        });
    },
}));

Alpine.start();
