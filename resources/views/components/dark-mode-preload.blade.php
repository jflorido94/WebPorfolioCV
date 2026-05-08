<script>
    (function () {
        var stored = localStorage.getItem('theme');
        if (stored === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    })();
</script>
