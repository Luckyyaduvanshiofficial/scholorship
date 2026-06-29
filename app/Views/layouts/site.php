<?php
/**
 * Main Website Layout — Clean public layout without sidebar.
 *
 * Available variables: $title, $isLoggedIn, $userName
 * Content is rendered via Response::view() which includes the template.
 */
declare(strict_types=1);

use App\Core\Helpers;

$pageTitle = $title ?? 'Tamboli Samaj';
?>
<!DOCTYPE html>
<html lang="hi" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Helpers::esc($pageTitle) ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --tsp-primary: #d4a017;
            --tsp-primary-dark: #b8860b;
            --tsp-bg: #f8f9fa;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--tsp-bg);
            color: #333;
        }

        /* Navbar */
        .navbar-site {
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 0.75rem 0;
        }
        .navbar-brand {
            font-weight: 700;
            color: var(--tsp-primary-dark) !important;
            font-size: 1.35rem;
        }
        .nav-link {
            color: #555 !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            transition: color 0.2s;
        }
        .nav-link:hover,
        .nav-link.active {
            color: var(--tsp-primary-dark) !important;
        }

        /* Hero */
        .hero-section {
            background: linear-gradient(135deg, var(--tsp-primary) 0%, var(--tsp-primary-dark) 100%);
            color: #fff;
            padding: 4rem 0;
            text-align: center;
        }
        .hero-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .hero-section p {
            font-size: 1.15rem;
            opacity: 0.9;
        }

        /* Cards */
        .card-event, .card-blog {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }
        .card-event:hover, .card-blog:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        .card-badge {
            background: var(--tsp-primary);
            color: #fff;
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
        }

        /* Section titles */
        .section-title {
            font-weight: 700;
            color: #222;
            margin-bottom: 1.5rem;
        }
        .section-title::after {
            content: '';
            display: block;
            width: 50px;
            height: 3px;
            background: var(--tsp-primary);
            margin-top: 0.5rem;
            border-radius: 2px;
        }

        /* Footer */
        .footer-site {
            background: #222;
            color: #aaa;
            padding: 2.5rem 0 1.5rem;
            margin-top: 3rem;
        }
        .footer-site h6 {
            color: #fff;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .footer-site a {
            color: #ccc;
            text-decoration: none;
        }
        .footer-site a:hover {
            color: var(--tsp-primary);
        }
        .footer-bottom {
            border-top: 1px solid #333;
            padding-top: 1rem;
            margin-top: 2rem;
            text-align: center;
            font-size: 0.85rem;
        }

        /* Bilingual */
        .lang-hi {
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-site sticky-top">
    <div class="container">
        <a class="navbar-brand" href="/">
            <i class="bi bi-people-fill me-2"></i>तम्बोली समाज
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#siteNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="siteNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/events">Events</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/blog">Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/about">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= \App\Core\Url::portal() ?>">Student Portal</a>
                </li>
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?= Helpers::esc($userName) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/dashboard">Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="/logout">
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-sm ms-2" style="background:var(--tsp-primary);color:#fff;border-radius:20px;padding:0.4rem 1.2rem;" href="/register">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Flash Messages -->
<?php if (function_exists('flash')): ?>
    <?php $flash = \App\Core\Flash::all(); ?>
    <?php if (!empty($flash)): ?>
        <div class="container mt-3">
            <?php foreach ($flash as $type => $message): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show">
                    <?= Helpers::esc($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Main Content -->
<main>
    <?= $content ?? '' ?>
</main>

<!-- Footer -->
<footer class="footer-site">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h6><i class="bi bi-people-fill me-2"></i>तम्बोली समाज</h6>
                <p class="small mb-0">Serving the Tamboli community with education, events, and cultural programs.</p>
            </div>
            <div class="col-md-4 mb-3">
                <h6>Quick Links</h6>
                <ul class="list-unstyled small">
                    <li class="mb-1"><a href="/">Home</a></li>
                    <li class="mb-1"><a href="/events">Events</a></li>
                    <li class="mb-1"><a href="/blog">Blog</a></li>
                    <li class="mb-1"><a href="/about">About</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-3">
                <h6>Contact</h6>
                <p class="small mb-1"><i class="bi bi-envelope me-2"></i>info@tambolisamaj.online</p>
                <p class="small mb-0"><i class="bi bi-globe me-2"></i>tambolisamaj.online</p>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?= date('Y') ?> Tamboli Samaj. All rights reserved.
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
