<?php
/**
 * Standalone Application Form Layout — no sidebar, no navbar.
 * Used via Response::view('applications/...', $data, 'layouts/form').
 */
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=5.0">
    <title><?= \App\Core\Helpers::esc($title ?? 'Application Form — Tamboli Samaj Portal') ?></title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="/assets/fonts/fonts.css" rel="stylesheet">
    <link href="/assets/css/style.css?v=2.1.0" rel="stylesheet">
    <link href="/assets/css/print.css?v=1.1.0" rel="stylesheet" media="print">
    <style>
        body {
            background-color: #f5f6f8;
            color: #0f172a;
            font-family: 'Inter', 'Noto Sans Devanagari', sans-serif;
            min-height: 100vh;
            padding: 0;
            margin: 0;
        }

        .tsp-form-page-container {
            max-width: 1000px;
            margin: 2.5rem auto 6rem;
            background: #ffffff;
            padding: 3.5rem 4rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            border: 1px solid #cbd5e1;
        }

        .tsp-form-header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .tsp-form-brand {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            text-decoration: none;
        }

        .tsp-form-logo {
            width: 56px;
            height: 56px;
            object-fit: contain;
        }

        .tsp-form-brand-text {
            display: flex;
            flex-direction: column;
        }

        .tsp-form-brand-title {
            font-family: 'Manrope', 'Noto Sans Devanagari', sans-serif;
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--accent, #8B0000);
            line-height: 1.2;
        }

        .tsp-form-brand-sub {
            font-size: 1.2rem;
            color: #64748b;
            font-weight: 500;
        }

        .tsp-form-back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #475569;
            font-weight: 600;
            text-decoration: none;
            font-size: 1.35rem;
            padding: 0.6rem 1.4rem;
            border: 1px solid #e2e8f0;
            border-radius: 99px;
            transition: all 0.2s ease-in-out;
            background: #ffffff;
        }

        .tsp-form-back-link:hover {
            background: #f1f5f9;
            color: var(--accent, #8B0000);
            border-color: #cbd5e1;
            transform: translateX(-2px);
        }

        .tsp-form-step {
            transition: opacity 0.15s ease-in-out;
        }

        .sticky-bar,
        #wizardActions.sticky-bar {
            position: sticky;
            bottom: 0;
            background: #ffffff;
            z-index: 100;
            margin-left: -4rem;
            margin-right: -4rem;
            margin-bottom: -3.5rem;
            padding: 1rem 4rem;
            border-top: 1px solid #e2e8f0;
            box-shadow: 0 -4px 12px rgba(15, 23, 42, 0.06);
        }

        .tsp-form-footer {
            text-align: center;
            padding: 1.5rem;
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .bg-orange { background-color: #fd7e14 !important; color: #fff !important; }
        .bg-purple { background-color: #6f42c1 !important; color: #fff !important; }
    </style>
</head>
<body>

<main class="py-1">
    <div class="tsp-form-page-container form-card">
        <header class="tsp-form-header-bar">
            <a href="/" class="tsp-form-brand">
                <img src="/assets/images/logo/logo-placeholder.svg" alt="Logo" class="tsp-form-logo">
                <div class="tsp-form-brand-text">
                    <span class="tsp-form-brand-title">तम्बोली समाज विकास संस्था, राजस्थान</span>
                    <span class="tsp-form-brand-sub"><?= \App\Core\Helpers::esc($formSubtitle ?? 'प्रतिभा सम्मान एवं छात्रवृत्ति आवेदन पोर्टल') ?></span>
                </div>
            </a>
            <a href="/dashboard" class="tsp-form-back-link back-link no-print">
                <i class="bi bi-arrow-left"></i>
                <span>डैशबोर्ड / Back to Dashboard</span>
            </a>
        </header>

        <?php require VIEW_PATH . '/layouts/flash-message.php'; ?>

        <?= $content ?? '' ?>

    </div>
</main>

<footer class="tsp-form-footer no-print">
    &copy; <?= date('Y') ?> तम्बोली समाज विकास संस्था, राजस्थान
</footer>

<script src="/assets/js/bootstrap.bundle.min.js" defer></script>
</body>
</html>