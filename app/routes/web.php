<?php

declare(strict_types=1);

/**
 * Web Routes — Tamboli Samaj Portal.
 *
 * @var \App\Core\Router $router
 */

// ─── Public Routes ────────────────────────────────────────
$router->get('/', 'HomeController@index');
$router->get('/home', 'HomeController@index');

// ─── Auth Routes ──────────────────────────────────────────
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/forgot-password', 'AuthController@showForgotPassword');
$router->post('/forgot-password', 'AuthController@forgotPassword');
$router->get('/reset-password', 'AuthController@showResetPassword');
$router->post('/reset-password', 'AuthController@resetPassword');
$router->post('/logout', 'AuthController@logout');

// ─── Student Dashboard ────────────────────────────────────
$router->get('/dashboard', 'DashboardController@student');

// ─── Profile Routes ───────────────────────────────────────
$router->get('/profile', 'ProfileController@show');
$router->get('/profile/edit', 'ProfileController@edit');
$router->post('/profile', 'ProfileController@update');
$router->post('/profile/photo', 'ProfileController@uploadPhoto');

// ─── Student Application Routes ───────────────────────────
$router->get('/applications', 'ApplicationController@index');
$router->get('/applications/create', 'ApplicationController@create');
$router->get('/applications/scholarship', 'ApplicationController@scholarship');
$router->post('/applications/scholarship', 'ApplicationController@storeScholarship');
$router->get('/applications/pratibha', 'ApplicationController@pratibha');
$router->post('/applications/pratibha', 'ApplicationController@storePratibha');
$router->get('/applications/{id}', 'ApplicationController@show');

// ─── Admin Application Routes ─────────────────────────────
$router->get('/admin/applications', 'AdminApplicationController@index');
$router->get('/admin/applications/{id}', 'AdminApplicationController@show');
$router->post('/admin/applications/{id}/approve', 'AdminApplicationController@approve');
$router->post('/admin/applications/{id}/reject', 'AdminApplicationController@reject');
$router->post('/admin/applications/{id}/dispute', 'AdminApplicationController@dispute');

// ─── Admin Dashboard ──────────────────────────────────────
$router->get('/admin', 'DashboardController@admin');

// ─── Representative Dashboard ─────────────────────────────
$router->get('/representative', 'DashboardController@representative');

// ─── Application Resubmission ─────────────────────────────
$router->post('/applications/{id}/resubmit', 'ApplicationController@resubmit');
