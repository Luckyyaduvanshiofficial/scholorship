<?php

declare(strict_types=1);

/**
 * Admin routes — admin.tambolisamaj.online
 * Unified admin for portal applications + main website content.
 *
 * @var \App\Core\Router $router
 */

// ─── Admin Dashboard ──────────────────────────────────────
$router->get('/', 'Admin\DashboardController@index');

// ─── Application Management ───────────────────────────────
$router->get('/applications', 'Admin\ApplicationController@index');
$router->get('/applications/{id}', 'Admin\ApplicationController@show');
$router->post('/applications/{id}/approve', 'Admin\ApplicationController@approve');
$router->post('/applications/{id}/reject', 'Admin\ApplicationController@reject');
$router->post('/applications/{id}/dispute', 'Admin\ApplicationController@dispute');

// ─── User Management ──────────────────────────────────────
$router->get('/students', 'Admin\UserController@students');
$router->post('/students/{id}/toggle-status', 'Admin\UserController@toggleStudentStatus');
$router->post('/students/{id}/delete', 'Admin\UserController@deleteStudent');
$router->get('/reps', 'Admin\UserController@reps');
$router->post('/reps/create', 'Admin\UserController@createRep');
$router->post('/reps/{id}/toggle-status', 'Admin\UserController@toggleRepStatus');
$router->post('/reps/{id}/delete', 'Admin\UserController@deleteRep');

// ─── Event Management ─────────────────────────────────────
$router->get('/events', 'Admin\EventController@index');
$router->get('/events/create', 'Admin\EventController@create');
$router->post('/events/create', 'Admin\EventController@store');
$router->get('/events/{id}/edit', 'Admin\EventController@edit');
$router->post('/events/{id}/edit', 'Admin\EventController@update');
$router->post('/events/{id}/delete', 'Admin\EventController@delete');

// ─── Blog Management ──────────────────────────────────────
$router->get('/blog', 'Admin\BlogController@index');
$router->get('/blog/create', 'Admin\BlogController@create');
$router->post('/blog/create', 'Admin\BlogController@store');
$router->get('/blog/{id}/edit', 'Admin\BlogController@edit');
$router->post('/blog/{id}/edit', 'Admin\BlogController@update');
$router->post('/blog/{id}/delete', 'Admin\BlogController@delete');

// ─── Announcements ────────────────────────────────────────
$router->get('/announcements', 'Admin\AnnouncementController@index');
$router->get('/announcements/create', 'Admin\AnnouncementController@create');
$router->post('/announcements/create', 'Admin\AnnouncementController@store');
$router->get('/announcements/{id}/edit', 'Admin\AnnouncementController@edit');
$router->post('/announcements/{id}/edit', 'Admin\AnnouncementController@update');
$router->post('/announcements/{id}/delete', 'Admin\AnnouncementController@delete');

// ─── Settings ─────────────────────────────────────────────
$router->get('/settings', 'Admin\SettingsController@index');
$router->post('/settings/update', 'Admin\SettingsController@update');
$router->post('/settings/session/create', 'Admin\SettingsController@createSession');
$router->post('/settings/session/{id}/activate', 'Admin\SettingsController@activateSession');

// ─── Auth ─────────────────────────────────────────────────
$router->get('/login', 'Auth\AuthController@showLogin');
$router->post('/login', 'Auth\AuthController@login');
$router->post('/logout', 'Auth\AuthController@logout');