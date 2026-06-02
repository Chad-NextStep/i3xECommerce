<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'i3X Store') ?></title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            safelist: [
                'border-brand-green', 'bg-green-50', 'text-brand-green',
                'border-gray-300', 'text-gray-700', 'hover:border-brand-green',
            ],
            theme: {
                extend: {
                    colors: {
                        brand: {
                            green: '#00a009',
                            'green-dark': '#016b06',
                            navy: '#003678',
                            dark: '#171721',
                            red: '#e14d43',
                        }
                    },
                    fontFamily: {
                        heading: ['Lato', 'sans-serif'],
                        body: ['Work Sans', 'sans-serif'],
                    },
                    borderRadius: {
                        'pill': '40px',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Work Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Lato', sans-serif; }
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }
        .aspect-w-1 { position: relative; padding-bottom: 100%; }
        .aspect-w-1 > * { position: absolute; inset: 0; width: 100%; height: 100%; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen">
    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <main class="flex-1">
        <?= $content ?>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="/assets/js/cart.js"></script>
    <?php if (!empty($page_scripts)): ?>
        <?php foreach ($page_scripts as $script): ?>
            <script src="<?= htmlspecialchars($script) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
