# Plan: Add Main Website with Host-Based Routing & Shared Auth

## Goal
Extend the existing Tamboli Samaj Portal into a multi-site architecture with:
- **Main website** (`tambolisamaj.online`) — Events, Blog, public-facing
- **Portal** (`portal.tambolisamaj.online`) — Existing scholarship/Pratibha system
- **Unified admin** (`admin.tambolisamaj.online`) — Manages both portal + main site
- **Shared authentication** across all subdomains via database sessions
- Same database, same codebase, host-based routing

---

## Current Hosting Structure (Hostinger)

```
public_html/                              ← tambolisamaj.online document root
└── portal/                               ← Portal project root
    ├── app/
    ├── vendor/
    ├── database/
    ├── storage/
    ├── uploads/
    ├── .env
    ├── composer.json
    └── public/                           ← portal.tambolisamaj.online document root
        ├── index.php
        ├── .htaccess
        ├── assets/
        └── uploads/
```

## Target Structure

```
public_html/                              ← tambolisamaj.online document root
├── index.php                             ← Main site entry point (NEW)
├── .htaccess                             ← Main site rewrite rules (NEW)
├── assets/                               ← Main site assets (symlink or copy)
└── portal/                               ← Portal project root (EXISTING)
    ├── app/                              ← Shared app code (controllers, models, views)
    ├── vendor/                           ← Shared dependencies
    ├── database/                         ← Shared DB scripts
    ├── storage/
    ├── uploads/
    ├── .env
    └── public/                           ← portal.tambolisamaj.online document root
        ├── index.php                     ← Portal entry point (MODIFIED)
        ├── .htaccess
        ├── assets/
        └── uploads/
```

**Key:** The main site `public_html/index.php` bootstraps the app from `public_html/portal/`. Both entry points share the same `app/`, `vendor/`, and database.

---

## Phase 1: Database Changes

### 1.1 Update `database/schema/001_create_tables.sql`
Add these tables (append to existing file):

```sql
-- Sessions (for cross-subdomain auth)
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(128) NOT NULL PRIMARY KEY,
    data TEXT NOT NULL,
    last_access INT(10) UNSIGNED NOT NULL,
    INDEX idx_last_access (last_access)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Events
CREATE TABLE IF NOT EXISTS events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT,
    description LONGTEXT,
    event_date DATETIME NOT NULL,
    location VARCHAR(255),
    image VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    registration_required TINYINT(1) DEFAULT 0,
    max_participants INT DEFAULT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_event_date (event_date),
    INDEX idx_is_active (is_active),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Event Registrations
CREATE TABLE IF NOT EXISTS event_registrations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    mobile VARCHAR(20),
    status ENUM('registered','attended','cancelled') DEFAULT 'registered',
    registered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_event_user (event_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog Posts
CREATE TABLE IF NOT EXISTS blog_posts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT,
    excerpt TEXT,
    featured_image VARCHAR(255),
    author_id BIGINT UNSIGNED NOT NULL,
    status ENUM('draft','published','archived') DEFAULT 'draft',
    published_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_status_published (status, published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 1.2 Update `database/update_schema.php`
Add migration code for the 4 new tables above + add `scope` column to `announcements`:
```sql
ALTER TABLE announcements ADD COLUMN scope VARCHAR(20) DEFAULT 'portal';
-- Values: 'portal', 'site', 'all'
```

---

## Phase 2: Database Session Handler

### 2.1 Create `app/Core/SessionHandler.php`
- Implements `SessionHandlerInterface`
- Uses `Database::getInstance()` for PDO
- CRUD on `sessions` table
- Garbage collection: delete rows older than `session.gc_maxlifetime`

### 2.2 Modify `app/Core/Session.php`
- Register `SessionHandler` before `session_start()`
- Set cookie domain:
  - Production: `.tambolisamaj.online` (leading dot = all subdomains)
  - Local: `''` (empty = current host only)
- Detection: check if `APP_URL` contains `tambolisamaj.online`

### 2.3 Update `app/Config/constants.php`
Add:
```php
define('APP_HOST', $_ENV['APP_HOST'] ?? 'site');
```

### 2.4 Update `.env.example`
Add:
```
APP_HOST=site
```

---

## Phase 3: Host-Based Routing

### 3.1 Restructure `app/Routes/web.php`
Wrap all existing routes in `if ($host === 'portal')` block. Add routes for main site and admin.

```php
$host = APP_HOST;

if ($host === 'portal') {
    // ALL existing routes (unchanged)
    $router->get('/', 'Public\HomeController@index');
    // ... all current routes ...
    
} elseif ($host === 'admin') {
    // Unified admin panel (manages both portal + main site)
    $router->get('/', 'Admin\DashboardController@index');
    // Existing admin routes
    $router->get('/applications', 'Admin\ApplicationController@index');
    // ... existing admin routes ...
    // NEW: Event management
    $router->get('/events', 'Admin\EventController@index');
    $router->get('/events/create', 'Admin\EventController@create');
    $router->post('/events/create', 'Admin\EventController@store');
    $router->get('/events/{id}/edit', 'Admin\EventController@edit');
    $router->post('/events/{id}/edit', 'Admin\EventController@update');
    $router->post('/events/{id}/delete', 'Admin\EventController@delete');
    // NEW: Blog management
    $router->get('/blog', 'Admin\BlogController@index');
    $router->get('/blog/create', 'Admin\BlogController@create');
    $router->post('/blog/create', 'Admin\BlogController@store');
    $router->get('/blog/{id}/edit', 'Admin\BlogController@edit');
    $router->post('/blog/{id}/edit', 'Admin\BlogController@update');
    $router->post('/blog/{id}/delete', 'Admin\BlogController@delete');
    
} else {
    // Main website (default: tambolisamaj.online)
    $router->get('/', 'Site\HomeController@index');
    $router->get('/about', 'Site\HomeController@about');
    $router->get('/events', 'Site\EventController@index');
    $router->get('/events/{slug}', 'Site\EventController@show');
    $router->post('/events/{slug}/register', 'Site\EventController@register');
    $router->get('/blog', 'Site\BlogController@index');
    $router->get('/blog/{slug}', 'Site\BlogController@show');
    
    // Auth routes (shared across all hosts)
    $router->get('/login', 'Auth\AuthController@showLogin');
    $router->post('/login', 'Auth\AuthController@login');
    $router->get('/register', 'Auth\AuthController@showRegister');
    $router->post('/register', 'Auth\AuthController@register');
    $router->get('/forgot-password', 'Auth\AuthController@showForgotPassword');
    $router->post('/forgot-password', 'Auth\AuthController@forgotPassword');
    $router->get('/reset-password', 'Auth\AuthController@showResetPassword');
    $router->post('/reset-password', 'Auth\AuthController@resetPassword');
    $router->post('/logout', 'Auth\AuthController@logout');
}
```

---

## Phase 4: New Models

### 4.1 `app/Models/Site/Event.php`
```php
namespace App\Models\Site;

class Event {
    // getAll(int $perPage, int $page): array — Paginated active events
    // getBySlug(string $slug): ?array — Single event
    // getUpcoming(int $limit): array — For homepage
    // create(array $data): int
    // update(int $id, array $data): bool
    // delete(int $id): bool
}
```

### 4.2 `app/Models/Site/BlogPost.php`
```php
namespace App\Models\Site;

class BlogPost {
    // getAll(int $perPage, int $page): array — Paginated published posts
    // getBySlug(string $slug): ?array — Single post
    // getLatest(int $limit): array — For homepage
    // create(array $data): int
    // update(int $id, array $data): bool
    // delete(int $id): bool
}
```

### 4.3 `app/Models/Site/EventRegistration.php`
```php
namespace App\Models\Site;

class EventRegistration {
    // register(int $eventId, int $userId, string $name, string $mobile): int
    // isRegistered(int $eventId, int $userId): bool
    // getForEvent(int $eventId): array
    // cancel(int $eventId, int $userId): bool
}
```

---

## Phase 5: New Controllers

### 5.1 `app/Controllers/Site/HomeController.php`
```php
namespace App\Controllers\Site;

class HomeController {
    // index() — Homepage with upcoming events + latest blog posts
    // about() — About the Samaj page
}
```

### 5.2 `app/Controllers/Site/EventController.php`
```php
namespace App\Controllers\Site;

class EventController {
    // index() — List active events (paginated)
    // show(string $slug) — Event detail page
    // register(string $slug) — Register for event (POST, requires auth)
}
```

### 5.3 `app/Controllers/Site/BlogController.php`
```php
namespace App\Controllers\Site;

class BlogController {
    // index() — List published posts (paginated)
    // show(string $slug) — Single blog post
}
```

### 5.4 `app/Controllers/Admin/EventController.php`
```php
namespace App\Controllers\Admin;

class EventController {
    // index() — List all events
    // create() — Show create form
    // store() — Save new event (POST)
    // edit(int $id) — Show edit form
    // update(int $id) — Save changes (POST)
    // delete(int $id) — Delete event (POST)
}
```

### 5.5 `app/Controllers/Admin/BlogController.php`
```php
namespace App\Controllers\Admin;

class BlogController {
    // index() — List all blog posts
    // create() — Show create form
    // store() — Save new post (POST)
    // edit(int $id) — Show edit form
    // update(int $id) — Save changes (POST)
    // delete(int $id) — Delete post (POST)
}
```

---

## Phase 6: New Views

### 6.1 `app/Views/layouts/site.php`
- Clean public layout (no sidebar)
- Bootstrap 5 navbar: Home, Events, Blog, About, Login/Register
- Footer with Samaj info
- Bilingual (Hindi + English)

### 6.2 Main Site Views
| View | Purpose |
|------|---------|
| `app/Views/site/home.php` | Homepage: hero, upcoming events, latest posts |
| `app/Views/site/events/index.php` | Events grid/list |
| `app/Views/site/events/show.php` | Event detail + registration |
| `app/Views/site/blog/index.php` | Blog post cards |
| `app/Views/site/blog/show.php` | Full blog post |
| `app/Views/site/about.php` | About the Samaj |

### 6.3 Admin Views (Event/Blog Management)
| View | Purpose |
|------|---------|
| `app/Views/admin/events/index.php` | Event list table |
| `app/Views/admin/events/form.php` | Create/edit event form |
| `app/Views/admin/blog/index.php` | Blog post list table |
| `app/Views/admin/blog/form.php` | Create/edit blog post form |

---

## Phase 7: Entry Point Refactoring

### 7.1 Create `app/Core/Bootstrap.php`
Extract the bootstrap logic from `public/index.php` into a reusable class:
- Load `.env`
- Load config files
- Set error reporting
- Set timezone
- Start session (with DB handler)
- Register exception handler

### 7.2 Modify `public/index.php` (Portal entry)
```php
<?php
declare(strict_types=1);
define('APP_HOST', 'portal');
require dirname(__DIR__) . '/app/Core/Bootstrap.php';
$app = new \App\Core\App();
$app->run();
```

### 7.3 Create `public_html/index.php` (Main site entry)
```php
<?php
declare(strict_types=1);
define('ROOT_PATH', __DIR__ . '/portal');
define('APP_HOST', 'site');
require ROOT_PATH . '/vendor/autoload.php';
// Load .env from portal
// ... bootstrap ...
$app = new \App\Core\App();
$app->run();
```

### 7.4 Create `public_html/.htaccess`
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [L]
```

---

## Phase 8: Local Development (Laragon)

### 8.1 Add to `.env`
```
APP_HOST=site
```

### 8.2 Testing different hosts locally
Option A: Change `APP_HOST` in `.env` to switch between `site` and `portal`
Option B: Add to `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 tambolisamaj.local
127.0.0.1 portal.tambolisamaj.local
```
Then configure Laragon virtual hosts.

---

## Files to Create (14 files)

| # | File | Purpose |
|---|------|---------|
| 1 | `app/Core/SessionHandler.php` | Database session handler |
| 2 | `app/Core/Bootstrap.php` | Reusable bootstrap logic |
| 3 | `app/Controllers/Site/HomeController.php` | Main site homepage |
| 4 | `app/Controllers/Site/EventController.php` | Events listing/detail |
| 5 | `app/Controllers/Site/BlogController.php` | Blog listing/detail |
| 6 | `app/Controllers/Admin/EventController.php` | Admin event management |
| 7 | `app/Controllers/Admin/BlogController.php` | Admin blog management |
| 8 | `app/Models/Site/Event.php` | Event model |
| 9 | `app/Models/Site/BlogPost.php` | Blog post model |
| 10 | `app/Models/Site/EventRegistration.php` | Registration model |
| 11 | `app/Views/layouts/site.php` | Main site layout |
| 12 | `app/Views/site/home.php` | Homepage view |
| 13 | `app/Views/site/events/index.php` | Events list view |
| 14 | `app/Views/site/events/show.php` | Event detail view |
| 15 | `app/Views/site/blog/index.php` | Blog list view |
| 16 | `app/Views/site/blog/show.php` | Blog post view |
| 17 | `app/Views/site/about.php` | About page view |
| 18 | `app/Views/admin/events/index.php` | Admin events list |
| 19 | `app/Views/admin/events/form.php` | Admin event form |
| 20 | `app/Views/admin/blog/index.php` | Admin blog list |
| 21 | `app/Views/admin/blog/form.php` | Admin blog form |
| 22 | `public_html/index.php` | Main site entry point |
| 23 | `public_html/.htaccess` | Main site rewrite rules |

## Files to Modify (6 files)

| # | File | Change |
|---|------|--------|
| 1 | `app/Routes/web.php` | Host-based routing split |
| 2 | `app/Core/Session.php` | DB handler + cross-subdomain cookie |
| 3 | `app/Config/constants.php` | Add `APP_HOST` constant |
| 4 | `database/update_schema.php` | Add 4 new tables + announcements.scope |
| 5 | `public/index.php` | Refactor to use Bootstrap.php |
| 6 | `.env.example` | Add `APP_HOST` variable |

## Files Unchanged
- All existing controllers (Student/*, Auth/*, Public/*, Representative/*)
- All existing models
- All existing views
- `app/Core/Auth.php`
- `app/Core/Database.php`
- `composer.json`

---

## Verification Plan

1. **Database:** Run `php database/update_schema.php` — verify 4 new tables created
2. **Session:** Login on main site, navigate to portal, verify still logged in
3. **Main site:** Visit `/` — see homepage with events and blog
4. **Events:** Visit `/events` — see event list; click event — see detail
5. **Blog:** Visit `/blog` — see blog posts; click post — see full content
6. **Portal:** Visit portal subdomain — existing functionality works unchanged
7. **Auth:** Register on main site, login on portal — same account works
8. **Admin:** Create events/blog posts via admin panel

---

## Implementation Order

1. Database schema (update_schema.php)
2. SessionHandler class
3. Session.php modifications
4. Bootstrap.php (extract from index.php)
5. constants.php + .env.example updates
6. New models (Event, BlogPost, EventRegistration)
7. New Site controllers (HomeController, EventController, BlogController)
8. New Admin controllers (EventController, BlogController)
9. Site layout (layouts/site.php)
10. Main site views (home, events, blog, about)
11. Admin views (events, blog)
12. Route restructuring (web.php)
13. Entry point files (public/index.php, public_html/index.php, public_html/.htaccess)
