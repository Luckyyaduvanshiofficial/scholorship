<?php

declare(strict_types=1);

namespace App\Models\Site;

use App\Core\Database;
use App\Core\Helpers;
use PDO;

class BlogPost
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get paginated published posts.
     */
    public function getAll(int $perPage = 12, int $page = 1): array
    {
        $offset = ($page - 1) * $perPage;

        $stmt = $this->db->prepare("
            SELECT bp.*, u.username AS author_name
            FROM blog_posts bp
            LEFT JOIN users u ON bp.author_id = u.id
            WHERE bp.status = 'published'
            ORDER BY bp.published_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$perPage, $offset]);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countStmt = $this->db->query("SELECT COUNT(*) FROM blog_posts WHERE status = 'published'");
        $total = (int) $countStmt->fetchColumn();

        return [
            'data'       => $posts,
            'total'      => $total,
            'per_page'   => $perPage,
            'current_page' => $page,
            'last_page'  => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Get latest published posts for homepage.
     */
    public function getLatest(int $limit = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT bp.*, u.username AS author_name
            FROM blog_posts bp
            LEFT JOIN users u ON bp.author_id = u.id
            WHERE bp.status = 'published'
            ORDER BY bp.published_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get post by slug.
     */
    public function getBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("
            SELECT bp.*, u.username AS author_name
            FROM blog_posts bp
            LEFT JOIN users u ON bp.author_id = u.id
            WHERE bp.slug = ? AND bp.status = 'published'
        ");
        $stmt->execute([$slug]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        return $post ?: null;
    }

    /**
     * Get post by ID (admin).
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT bp.*, u.username AS author_name
            FROM blog_posts bp
            LEFT JOIN users u ON bp.author_id = u.id
            WHERE bp.id = ?
        ");
        $stmt->execute([$id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        return $post ?: null;
    }

    /**
     * Get all posts (admin — includes drafts).
     */
    public function getAllAdmin(int $perPage = 20, int $page = 1): array
    {
        $offset = ($page - 1) * $perPage;

        $stmt = $this->db->prepare("
            SELECT bp.*, u.username AS author_name
            FROM blog_posts bp
            LEFT JOIN users u ON bp.author_id = u.id
            ORDER BY bp.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$perPage, $offset]);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countStmt = $this->db->query("SELECT COUNT(*) FROM blog_posts");
        $total = (int) $countStmt->fetchColumn();

        return [
            'data'       => $posts,
            'total'      => $total,
            'per_page'   => $perPage,
            'current_page' => $page,
            'last_page'  => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Create a new blog post.
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO blog_posts (title, slug, content, excerpt, featured_image, author_id, status, published_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['title'],
            $data['slug'],
            $data['content'] ?? null,
            $data['excerpt'] ?? null,
            $data['featured_image'] ?? null,
            $data['author_id'],
            $data['status'] ?? 'draft',
            $data['published_at'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update a blog post.
     */
    public function update(int $id, array $data): bool
    {
        $allowed = ['title', 'slug', 'content', 'excerpt', 'featured_image', 'status', 'published_at'];
        $fields = [];
        $values = [];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "`{$field}` = ?";
                $values[] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $values[] = $id;
        $sql = "UPDATE blog_posts SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($values);
    }

    /**
     * Delete a blog post.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM blog_posts WHERE id = ?");

        return $stmt->execute([$id]);
    }

    /**
     * Generate a unique slug from title.
     */
    public function generateSlug(string $title): string
    {
        $slug = Helpers::slug($title);
        $original = $slug;
        $counter = 1;

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM blog_posts WHERE slug = ?");
        while (true) {
            $stmt->execute([$slug]);
            if ((int) $stmt->fetchColumn() === 0) {
                return $slug;
            }
            $slug = $original . '-' . $counter;
            $counter++;
        }
    }
}
