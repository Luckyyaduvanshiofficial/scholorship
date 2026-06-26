<?php
/**
 * Standalone Application Form Layout wrapper.
 * This layout replaces the dashboard panel, providing a clean, print-optimized page structure.
 * 
 * Usage:
 * At the start of view:
 *   ob_start();
 * At the end of view:
 *   $content = ob_get_clean();
 *   require VIEW_PATH . '/layouts/FormLayout.php';
 */
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=5.0">
    <title><?= \App\Core\Helpers::esc($title ?? 'Application Form — Tamboli Samaj Portal') ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/favicon.png">
    <!-- Bootstrap CSS -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Self-hosted Google Fonts -->
    <link href="/assets/fonts/fonts.css" rel="stylesheet">
    <!-- Portal Custom CSS -->
    <link href="/assets/css/style.css?v=2.1.0" rel="stylesheet">
    <link href="/assets/css/print.css?v=1.0.0" rel="stylesheet" media="print">
    
    <style>
        /* Standalone Form Page Styles */
        body {
            background-color: #f8fafc;
            color: #0f172a;
            font-family: 'Inter', 'Noto Sans Devanagari', sans-serif;
            min-height: 100vh;
            padding: 0;
            margin: 0;
        }

        .tsp-form-page-container {
            max-width: 1000px;
            margin: 2.5rem auto;
            background: #ffffff;
            padding: 3.5rem 4rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            border: 1px solid #cbd5e1;
        }

        /* Minimal Header */
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

        /* Print Style Adjustments for Screen rendering */
        .tsp-form-step {
            transition: opacity 0.15s ease-in-out;
        }

        /* Print-Optimized Stylesheet for physical forms */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 11pt !important;
            }

            .tsp-form-page-container {
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                max-width: 100% !important;
                width: 100% !important;
                background: #ffffff !important;
            }

            .no-print, 
            .tsp-form-back-link, 
            .tsp-stepper, 
            #wizardActions, 
            #declarationBox, 
            .btn, 
            button, 
            .alert,
            .file-upload-actions,
            .btn-upload-doc,
            .btn-delete-doc,
            .input-group button,
            .form-text,
            .small.text-danger {
                display: none !important;
            }

            /* Make all stepper wizard steps visible simultaneously for print output */
            .tsp-form-step {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
                margin-bottom: 2.5rem !important;
                page-break-inside: avoid;
            }

            /* Force standard inputs and textareas to look like a physical paper document */
            .form-control, 
            .form-select, 
            input, 
            select, 
            textarea {
                border: 1px solid #000000 !important;
                background-color: transparent !important;
                color: #000000 !important;
                border-radius: 4px !important;
                box-shadow: none !important;
                padding: 0.4rem 0.6rem !important;
                font-size: 11pt !important;
            }

            /* Display selected files visually instead of inputs */
            .doc-status-container {
                font-weight: 600 !important;
                color: #000000 !important;
            }

            /* Headings styled classically like physical layouts */
            h4.border-bottom {
                border-bottom: 1.5px solid #000000 !important;
                color: #000000 !important;
                font-size: 13pt !important;
                margin-top: 2rem !important;
            }

            .tsp-form-header-bar {
                border-bottom: 2.5px solid #000000 !important;
            }

            .tsp-form-brand-title {
                color: #000000 !important;
            }

            @page {
                size: A4;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body>

<main class="py-1">
    <div class="tsp-form-page-container">
        <!-- Minimal organization header bar -->
        <header class="tsp-form-header-bar">
            <a href="/" class="tsp-form-brand">
                <img src="/assets/images/logo/logo-placeholder.svg" alt="Logo" class="tsp-form-logo">
                <div class="tsp-form-brand-text">
                    <span class="tsp-form-brand-title">तम्बोली समाज विकास संस्था, राजस्थान</span>
                    <span class="tsp-form-brand-sub">प्रतिभा सम्मान एवं छात्रवृत्ति आवेदन पोर्टल</span>
                </div>
            </a>
            <a href="/dashboard" class="tsp-form-back-link no-print">
                <i class="bi bi-arrow-left"></i>
                <span>डैशबोर्ड / Back to Dashboard</span>
            </a>
        </header>

        <!-- Flash messages inside wrapper -->
        <?php require VIEW_PATH . '/layouts/flash-message.php'; ?>

        <!-- Main form content output -->
        <?= $content ?>

    </div>
</main>

<script src="/assets/js/bootstrap.bundle.min.js" defer></script>
</body>
</html>
