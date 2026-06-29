<?php
/**
 * About Page
 *
 * Available: $isLoggedIn, $userName
 */
declare(strict_types=1);

use App\Core\Helpers;
?>

<!-- About Hero -->
<section class="hero-section" style="padding:3rem 0;">
    <div class="container">
        <h1>About Us</h1>
        <p class="mb-0">हमारे बारे में — तम्बोली समाज</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="mb-3">तम्बोली समाज</h3>
                        <p>
                            The Tamboli Samaj is a community organization dedicated to the welfare and
                            development of the Tamboli community. Our mission is to promote education,
                            preserve cultural heritage, and support community members in their academic
                            and professional pursuits.
                        </p>

                        <h4 class="mt-4 mb-3">Our Mission</h4>
                        <ul>
                            <li>Promote education through scholarships and awards (Pratibha Samman)</li>
                            <li>Organize cultural and community events</li>
                            <li>Support students in their academic journey</li>
                            <li>Preserve and promote Tamboli community heritage</li>
                            <li>Foster unity and cooperation among community members</li>
                        </ul>

                        <h4 class="mt-4 mb-3">What We Do</h4>
                        <p>
                            <strong>Scholarship Program:</strong> We provide financial assistance to meritorious
                            students from the community to support their education.
                        </p>
                        <p>
                            <strong>Pratibha Samman:</strong> We recognize and reward academic excellence and
                            outstanding achievements of community students.
                        </p>
                        <p>
                            <strong>Community Events:</strong> We organize regular events, gatherings, and
                            programs to strengthen community bonds.
                        </p>

                        <h4 class="mt-4 mb-3">Get Involved</h4>
                        <p>
                            Join our community portal to stay updated with events, apply for scholarships,
                            and connect with other community members.
                        </p>
                        <div class="mt-3">
                            <?php if (!$isLoggedIn): ?>
                                <a href="/register" class="btn btn-warning me-2">Join Our Community</a>
                                <a href="/login" class="btn btn-outline-dark">Login</a>
                            <?php else: ?>
                                <a href="/dashboard" class="btn btn-warning">Go to Dashboard</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
