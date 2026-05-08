<button id="theme-toggle-button" type="button" {{ $attributes->merge(['class' => 'inline-flex items-center gap-3', 'aria-label' => 'Alternar modo oscuro']) }}>
    <svg id="theme-toggle-moon" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
    </svg>

    <svg id="theme-toggle-sun" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="5"/>
        <line x1="12" y1="1" x2="12" y2="3"/>
        <line x1="12" y1="21" x2="12" y2="23"/>
        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
        <line x1="1" y1="12" x2="3" y2="12"/>
        <line x1="21" y1="12" x2="23" y2="12"/>
        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
    </svg>

    <span id="theme-toggle-text">{{ $slot ?? 'Modo oscuro' }}</span>
</button>

<script>
    (function () {
        const btn = document.getElementById('theme-toggle-button');
        const sunIcon = document.getElementById('theme-toggle-sun');
        const moonIcon = document.getElementById('theme-toggle-moon');
        const text = document.getElementById('theme-toggle-text');

        if (!btn) {
            return;
        }

        function updateUI() {
            const isDark = document.documentElement.classList.contains('dark');

            if (isDark) {
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
                text.textContent = 'Modo claro';
                btn.setAttribute('aria-label', 'Activar modo claro');
            } else {
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
                text.textContent = 'Modo oscuro';
                btn.setAttribute('aria-label', 'Activar modo oscuro');
            }
        }

        btn.addEventListener('click', function () {
            const isDark = document.documentElement.classList.contains('dark');

            if (isDark) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }

            updateUI();
        });

        updateUI();
    })();
</script>
