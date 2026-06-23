<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>PrintOS</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'><path fill='%23ff4f1a' d='M3 5h18v3H3V5zm2 4h14v2H5V9zm-2 3h18v8H3v-8zm2 2v4h14v-4H5z'/></svg>" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead

    <script>
        (function () {
            try {
            var theme = localStorage.getItem('printos_theme');
            if (theme) document.documentElement.setAttribute('data-theme', theme);

            var accent = localStorage.getItem('printos_accent');
            if (accent) document.documentElement.style.setProperty('--accent', accent);

            var accentD = localStorage.getItem('printos_accent_d');
            if (accentD) document.documentElement.style.setProperty('--accent-d', accentD);

            var accentLight = localStorage.getItem('printos_accent_light');
            if (accentLight) document.documentElement.style.setProperty('--accent-light', accentLight);
            } catch (e) {
            }
        })();
        </script>
</head>
<body class="antialiased">
    @inertia
</body>
</html>
