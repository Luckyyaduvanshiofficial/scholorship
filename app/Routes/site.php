<?php

declare(strict_types=1);

/**
 * Main website routes — tambolisamaj.online
 * Public site: events, blog, about. Students may also access dashboard here.
 *
 * @var \App\Core\Router $router
 */

// ─── Homepage ─────────────────────────────────────────────
$router->get('/', 'Site\HomeController@index');
$router->get('/about', 'Site\HomeController@about');

// ─── Events ───────────────────────────────────────────────
$router->get('/events', 'Site\EventController@index');
$router->get('/events/{slug}', 'Site\EventController@show');
$router->post('/events/{slug}/register', 'Site\EventController@register');

// ─── Blog ─────────────────────────────────────────────────
$router->get('/blog', 'Site\BlogController@index');
$router->get('/blog/{slug}', 'Site\BlogController@show');

// ─── Auth (shared DB session) ─────────────────────────────
$router->get('/login', 'Auth\AuthController@showLogin');
$router->post('/login', 'Auth\AuthController@login');
$router->get('/register', 'Auth\AuthController@showRegister');
$router->post('/register', 'Auth\AuthController@register');
$router->get('/forgot-password', 'Auth\AuthController@showForgotPassword');
$router->post('/forgot-password', 'Auth\AuthController@forgotPassword');
$router->get('/reset-password', 'Auth\AuthController@showResetPassword');
$router->post('/reset-password', 'Auth\AuthController@resetPassword');
$router->post('/logout', 'Auth\AuthController@logout');

// ─── Student dashboard (optional access from main site) ───
$router->get('/dashboard', 'Student\DashboardController@index');
$router->get('/dashboard/profile', 'Student\ProfileController@show');
$router->get('/dashboard/profile/edit', 'Student\ProfileController@edit');
$router->post('/dashboard/profile', 'Student\ProfileController@update');
$router->post('/dashboard/profile/photo', 'Student\ProfileController@uploadPhoto');
$router->get('/dashboard/applications', 'Student\ApplicationController@index');
$router->get('/dashboard/applications/create', 'Student\ApplicationController@create');
$router->get('/dashboard/applications/scholarship', 'Student\ApplicationController@scholarship');
$router->get('/dashboard/applications/pratibha', 'Student\ApplicationController@pratibha');
$router->get('/dashboard/applications/{id}', 'Student\ApplicationController@show');
$router->get('/dashboard/applications/{id}/edit', 'Student\ApplicationController@edit');
$router->post('/dashboard/applications/{id}/edit', 'Student\ApplicationController@update');
$router->post('/dashboard/applications/{id}/upload-document', 'Student\ApplicationController@uploadDocumentAjax');
$router->post('/dashboard/applications/{id}/delete-document', 'Student\ApplicationController@deleteDocumentAjax');
$router->post('/dashboard/applications/step/{step}', 'Student\ApplicationController@storeStep');
$router->get('/dashboard/applications/{id}/acknowledgment', 'Student\ApplicationController@acknowledgment');
$router->post('/dashboard/applications/{id}/resubmit', 'Student\ApplicationController@resubmit');