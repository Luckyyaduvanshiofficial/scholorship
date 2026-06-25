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
$router->get('/applications/{id}/edit', 'ApplicationController@edit');
$router->post('/applications/{id}/edit', 'ApplicationController@update');
$router->post('/applications/{id}/upload-document', 'ApplicationController@uploadDocumentAjax');
$router->post('/applications/{id}/delete-document', 'ApplicationController@deleteDocumentAjax');
$router->get('/uploads/applications/{id}/{filename}', 'ApplicationController@viewUpload');

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

// ─── Super Admin / Admin User Management Routes ───────────
$router->get('/admin/students', 'AdminUserController@students');
$router->post('/admin/students/{id}/toggle-status', 'AdminUserController@toggleStudentStatus');
$router->post('/admin/students/{id}/delete', 'AdminUserController@deleteStudent');

// ─── Super Admin Representative Management Routes ─────────
$router->get('/admin/reps', 'AdminUserController@reps');
$router->post('/admin/reps/create', 'AdminUserController@createRep');
$router->post('/admin/reps/{id}/toggle-status', 'AdminUserController@toggleRepStatus');
$router->post('/admin/reps/{id}/delete', 'AdminUserController@deleteRep');

// ─── Announcements Management Routes ──────────────────────
$router->get('/admin/announcements', 'AdminAnnouncementController@index');
$router->get('/admin/announcements/create', 'AdminAnnouncementController@create');
$router->post('/admin/announcements/create', 'AdminAnnouncementController@store');
$router->get('/admin/announcements/{id}/edit', 'AdminAnnouncementController@edit');
$router->post('/admin/announcements/{id}/edit', 'AdminAnnouncementController@update');
$router->post('/admin/announcements/{id}/delete', 'AdminAnnouncementController@delete');

// ─── Settings / Session Management Routes ─────────────────
$router->get('/admin/settings', 'AdminSettingsController@index');
$router->post('/admin/settings/update', 'AdminSettingsController@update');
$router->post('/admin/settings/session/create', 'AdminSettingsController@createSession');
$router->post('/admin/settings/session/{id}/activate', 'AdminSettingsController@activateSession');

// ─── Application Resubmission ─────────────────────────────
$router->post('/applications/{id}/resubmit', 'ApplicationController@resubmit');
