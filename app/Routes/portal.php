<?php

declare(strict_types=1);

/**
 * Portal routes — portal.tambolisamaj.online
 * Student scholarship & Pratibha application portal.
 *
 * @var \App\Core\Router $router
 */

// ─── Secure uploads (application documents) ───────────────
$router->get('/uploads/applications/{id}/{file}', 'UploadController@applicationDocument');

// ─── Public Routes ────────────────────────────────────────
$router->get('/', 'Public\HomeController@index');
$router->get('/home', 'Public\HomeController@index');

// ─── Auth Routes ──────────────────────────────────────────
$router->get('/login', 'Auth\AuthController@showLogin');
$router->post('/login', 'Auth\AuthController@login');
$router->get('/register', 'Auth\AuthController@showRegister');
$router->post('/register', 'Auth\AuthController@register');
$router->get('/forgot-password', 'Auth\AuthController@showForgotPassword');
$router->post('/forgot-password', 'Auth\AuthController@forgotPassword');
$router->get('/reset-password', 'Auth\AuthController@showResetPassword');
$router->post('/reset-password', 'Auth\AuthController@resetPassword');
$router->post('/logout', 'Auth\AuthController@logout');

// ─── Student Dashboard ────────────────────────────────────
$router->get('/dashboard', 'Student\DashboardController@index');

// ─── Profile Routes ───────────────────────────────────────
$router->get('/dashboard/profile', 'Student\ProfileController@show');
$router->get('/dashboard/profile/edit', 'Student\ProfileController@edit');
$router->post('/dashboard/profile', 'Student\ProfileController@update');
$router->post('/dashboard/profile/photo', 'Student\ProfileController@uploadPhoto');

// ─── Student Application Routes ───────────────────────────
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

// ─── Representative Dashboard ─────────────────────────────
$router->get('/representative', 'Representative\DashboardController@index');

// Note: /admin/* on portal is redirected to admin.tambolisamaj.online in App::redirectLegacyPortalAdmin()