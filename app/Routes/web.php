<?php

declare(strict_types=1);

/**
 * Web Routes — Tamboli Samaj Portal.
 *
 * @var \App\Core\Router $router
 */

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

// ─── Profile Routes (under /dashboard) ────────────────────
$router->get('/dashboard/profile', 'Student\ProfileController@show');
$router->get('/dashboard/profile/edit', 'Student\ProfileController@edit');
$router->post('/dashboard/profile', 'Student\ProfileController@update');
$router->post('/dashboard/profile/photo', 'Student\ProfileController@uploadPhoto');

// ─── Student Application Routes (under /dashboard) ────────
$router->get('/dashboard/applications', 'Student\ApplicationController@index');
$router->get('/dashboard/applications/create', 'Student\ApplicationController@create');
$router->get('/dashboard/applications/scholarship', 'Student\ApplicationController@scholarship');
$router->post('/dashboard/applications/scholarship', 'Student\ApplicationController@storeScholarship');
$router->get('/dashboard/applications/pratibha', 'Student\ApplicationController@pratibha');
$router->post('/dashboard/applications/pratibha', 'Student\ApplicationController@storePratibha');
$router->get('/dashboard/applications/{id}', 'Student\ApplicationController@show');
$router->get('/dashboard/applications/{id}/edit', 'Student\ApplicationController@edit');
$router->post('/dashboard/applications/{id}/edit', 'Student\ApplicationController@update');
$router->post('/dashboard/applications/{id}/upload-document', 'Student\ApplicationController@uploadDocumentAjax');
$router->post('/dashboard/applications/{id}/delete-document', 'Student\ApplicationController@deleteDocumentAjax');
$router->post('/dashboard/applications/step/{step}', 'Student\ApplicationController@storeStep');
$router->get('/dashboard/applications/{id}/acknowledgment', 'Student\ApplicationController@acknowledgment');
$router->post('/dashboard/applications/{id}/resubmit', 'Student\ApplicationController@resubmit');

// ─── Admin Application Routes ─────────────────────────────
$router->get('/admin/applications', 'Admin\ApplicationController@index');
$router->get('/admin/applications/{id}', 'Admin\ApplicationController@show');
$router->post('/admin/applications/{id}/approve', 'Admin\ApplicationController@approve');
$router->post('/admin/applications/{id}/reject', 'Admin\ApplicationController@reject');
$router->post('/admin/applications/{id}/dispute', 'Admin\ApplicationController@dispute');

// ─── Admin Dashboard ──────────────────────────────────────
$router->get('/admin', 'Admin\DashboardController@index');

// ─── Representative Dashboard ─────────────────────────────
$router->get('/representative', 'Representative\DashboardController@index');

// ─── Super Admin / Admin User Management Routes ───────────
$router->get('/admin/students', 'Admin\UserController@students');
$router->post('/admin/students/{id}/toggle-status', 'Admin\UserController@toggleStudentStatus');
$router->post('/admin/students/{id}/delete', 'Admin\UserController@deleteStudent');

// ─── Super Admin Representative Management Routes ─────────
$router->get('/admin/reps', 'Admin\UserController@reps');
$router->post('/admin/reps/create', 'Admin\UserController@createRep');
$router->post('/admin/reps/{id}/toggle-status', 'Admin\UserController@toggleRepStatus');
$router->post('/admin/reps/{id}/delete', 'Admin\UserController@deleteRep');

// ─── Announcements Management Routes ──────────────────────
$router->get('/admin/announcements', 'Admin\AnnouncementController@index');
$router->get('/admin/announcements/create', 'Admin\AnnouncementController@create');
$router->post('/admin/announcements/create', 'Admin\AnnouncementController@store');
$router->get('/admin/announcements/{id}/edit', 'Admin\AnnouncementController@edit');
$router->post('/admin/announcements/{id}/edit', 'Admin\AnnouncementController@update');
$router->post('/admin/announcements/{id}/delete', 'Admin\AnnouncementController@delete');

// ─── Settings / Session Management Routes ─────────────────
$router->get('/admin/settings', 'Admin\SettingsController@index');
$router->post('/admin/settings/update', 'Admin\SettingsController@update');
$router->post('/admin/settings/session/create', 'Admin\SettingsController@createSession');
$router->post('/admin/settings/session/{id}/activate', 'Admin\SettingsController@activateSession');
